# Veille technologique & sécurité — Cinévo

Registre daté de la veille effectuée sur le projet, exigé par le référentiel DWWM (compétences 1, 3, 4, 6, 7, 8) et par le dossier projet ("description de la veille effectuée sur les vulnérabilités de sécurité, description des vulnérabilités éventuellement trouvées et des failles potentiellement corrigées").

> Stack réelle du projet : PHP 8.4 vanilla + MySQL (mysqli), sans framework, sans gestionnaire de dépendances (pas de Composer, pas de npm). Les recommandations ci-dessous sont adaptées à cette stack, pas à un projet Symfony/Doctrine.

## Sources suivies

- **OWASP Top 10** — https://owasp.org/www-project-top-ten/ (référentiel des vulnérabilités applicatives)
- **OWASP Cheat Sheet Series** — https://cheatsheetseries.owasp.org/ (CSRF, XSS, Session Management)
- **CERT-FR** — https://www.cert.ssi.gouv.fr/ (alertes officielles ANSSI)
- **PHP.net Releases / Changelog** — https://www.php.net/releases/ (failles corrigées par version)
- **GitHub Advisory Database** — https://github.com/advisories (CVE par écosystème, y compris PHP)
- **The Hacker News** — https://thehackernews.com/

Pas de dépendance tierce dans le projet (pas de `composer.json` / `package.json`) : `composer audit` / `npm audit` / Dependabot ne s'appliquent pas actuellement. La surface d'attaque liée aux CVE de dépendances est donc réduite, mais il n'y a pas non plus d'outillage automatisé — la veille manuelle sur PHP/MySQL directement reste nécessaire.

## Cadence

- **Hebdomadaire (30 min)** : lecture OWASP / CERT-FR / Hacker News, mise à jour du registre.
- **Avant chaque mise en ligne** : relecture rapide de la checklist ci-dessous sur les pages modifiées.
- **Mensuel** : revue du registre, vérification de la version PHP utilisée face aux dernières releases.

## Checklist avant de clore une fonctionnalité

1. Entrées utilisateur validées côté serveur (pas seulement côté client) ?
2. Accès protégé par `estConnecte()` là où l'action modifie une donnée ?
3. Requête SQL paramétrée (`prepare()`/`bind_param`), jamais de concaténation de `$_GET`/`$_POST` ?
4. Sortie HTML échappée (`htmlspecialchars`) partout où une donnée utilisateur est affichée ?
5. Formulaire POST protégé par un token CSRF ?
6. Rien de sensible (`.env`, mots de passe) commité dans Git ?

## Registre

## 2026-07-08 — Absence de protection CSRF sur les formulaires POST

**Source :** OWASP Cheat Sheet Series — CSRF Prevention Cheat Sheet
**Catégorie :** Vulnérabilité
**Concerné Cinévo ?** Oui

**Résumé en 2-3 lignes :**
Revue des formulaires POST du site (`connexion.php`, `inscription.php`, `ecrire.php`, `contact.php`) : aucun ne portait de token CSRF. Un attaquant pouvait forger une page tierce soumettant automatiquement le formulaire de connexion, d'inscription ou de publication d'avis à l'insu de la victime connectée.

**Action prise / à prendre :**
- [x] Ajout de `csrf_token()` / `csrf_champ()` / `csrf_verifie()` dans `includes/auth.php` (token aléatoire 32 octets, stocké en session, comparaison via `hash_equals`)
- [x] Vérification ajoutée sur `connexion.php`, `inscription.php`, `ecrire.php` (formulaire de publication d'avis)
- [x] Champ caché `csrf_token` ajouté dans les 3 formulaires concernés
- [x] Testé en local : requête POST sans token → rejetée ; avec token valide → traitement normal
- [ ] `contact.php` : pas de traitement serveur actuellement (le formulaire ne fait rien) → rien à protéger pour l'instant, à couvrir le jour où l'envoi d'e-mail sera implémenté

**Statut :** Corrigé le 2026-07-08.

## 2026-07-08 — Audit Top 10 OWASP sur l'existant (injection, XSS, authentification)

**Source :** OWASP Top 10
**Catégorie :** Bonne pratique
**Concerné Cinévo ?** À vérifier

**Résumé en 2-3 lignes :**
Revue des points classiques du Top 10 sur le code existant : les requêtes touchant des paramètres utilisateur (`avis.php`, `ecrire.php`, `fiche.php`) utilisent des requêtes préparées `mysqli` (`prepare`/`bind_param`), aucune concaténation de `$_GET`/`$_POST` trouvée dans une requête SQL. L'échappement `htmlspecialchars` est présent sur l'ensemble des pages publiques. Les mots de passe sont stockés avec `password_hash()` (Bcrypt via `PASSWORD_DEFAULT`) et vérifiés avec `password_verify()`.

**Action prise / à prendre :**
- [x] Injection SQL : vérifiée, non concerné (requêtes paramétrées)
- [x] XSS stocké/réfléchi : vérifié, non concerné (échappement systématique)
- [x] Authentification : vérifiée, conforme (hash + vérification, pas de mot de passe en clair)
- [x] Attributs de cookie de session (`SameSite`, `HttpOnly`, `Secure`) — corrigés le 2026-07-08 (voir entrée dédiée ci-dessous)

**Statut :** Corrigé le 2026-07-08 (les 4 points sont désormais conformes).

## 2026-07-08 — Cookie de session sans attributs de sécurité explicites

**Source :** OWASP Cheat Sheet Series — Session Management Cheat Sheet
**Catégorie :** Vulnérabilité
**Concerné Cinévo ?** Oui

**Résumé en 2-3 lignes :**
Le cookie `PHPSESSID` était posé avec les valeurs par défaut de PHP, sans `HttpOnly`, `SameSite` ni `Secure`. Un cookie non-`HttpOnly` peut être lu par un script injecté (XSS) et exfiltré ; sans `SameSite`, il peut être renvoyé sur des requêtes forgées depuis un site tiers.

**Action prise / à prendre :**
- [x] `session_set_cookie_params()` ajouté dans `includes/auth.php`, appelé avant `session_start()`
- [x] `httponly => true` (cookie inaccessible en JS)
- [x] `samesite => 'Lax'` (bloque l'envoi cross-site)
- [x] `secure` activé dynamiquement si `$_SERVER['HTTPS']` est actif (compatible avec le dev local en HTTP, s'active seul en prod)
- [x] Testé en local : `document.cookie` ne montre plus le cookie de session après une requête fraîche ; connexion/inscription fonctionnent toujours normalement

**Statut :** Corrigé le 2026-07-08.

## 2026-07-08 — Exposition de la clé API TMDB

**Source :** revue interne du dépôt Git (`git ls-files`, `git log -- .env`)
**Catégorie :** Bonne pratique
**Concerné Cinévo ?** Non

**Résumé en 2-3 lignes :**
Vérification que la clé `TMDB_API_KEY` (stockée dans `.env`) n'a jamais été commitée dans l'historique Git. `.env` figure dans `.gitignore` depuis le début du projet et n'apparaît dans aucun commit.

**Action prise / à prendre :**
- [x] Recherche dans tout l'historique Git : aucune trace de `.env`
- [x] Confirmation que `.gitignore` couvre bien le fichier

**Statut :** Non concerné — déjà conforme, à surveiller à chaque nouveau commit.
