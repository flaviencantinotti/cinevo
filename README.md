# Cinévo

Un site de critiques de films : on cherche un film, on consulte sa fiche, on écrit son avis.

Projet personnel réalisé pendant ma formation Développement web et web mobile
(Formagraph Design, Besançon — 2026). Le catalogue vient de l'API TMDB, les
comptes et les avis sont stockés en base.

---

## Le choix technique

PHP et MySQL, sans framework, sans Composer, sans npm. C'était volontaire.

Je voulais comprendre ce que fait chaque partie de l'application avant de laisser
un framework le faire à ma place : les sessions, l'échappement des sorties, les
requêtes préparées. Un framework aurait été plus rapide à écrire, mais je serais
passé à côté de l'essentiel.

Ce choix a une contrepartie que j'assume : aucune dépendance à mettre à jour,
donc aucune faille héritée d'une bibliothèque tierce, mais toute la sécurité
repose sur mon propre code. C'est ce qui m'a poussé à tenir un journal de veille
(`veille.md`), où je consigne les vulnérabilités que je trouve dans le projet
et la façon dont je les corrige.

---

## Fonctionnalités

**Films**
- Recherche par titre
- Fiche détaillée : synopsis, distribution, plateformes de streaming disponibles
- Tirage au hasard, pour ceux qui ne savent pas quoi regarder

**Comptes et avis**
- Inscription, connexion, déconnexion
- Mot de passe oublié : réinitialisation par jeton à usage unique
- Rédaction d'un avis sur un film
- Consultation des avis publiés
- Espace personnel : retrouver ses propres avis, les modifier, les supprimer
- Profil : changer son adresse e-mail ou son mot de passe, envoyer une photo
  de profil

**Le reste**
- Page mode d'emploi (`decouvrir.php`) : comment le site fonctionne, et une foire
  aux questions
- Pages légales : mentions, conditions générales, cookies, à propos, contact
- Page 404 personnalisée
- `sitemap.php` et `robots.txt`

---

## Stack

| | |
|---|---|
| Back-end | PHP 8.2 (vanilla), MySQL via mysqli |
| Front-end | HTML, CSS, JavaScript |
| Données films | API TMDB (`/search/movie`, `/movie/{id}`, `/discover/movie`) |
| Dépendances | aucune |

---

## Installation

**Prérequis** : PHP 8.2 ou plus récent, MySQL, et une clé API TMDB (gratuite sur
[themoviedb.org](https://www.themoviedb.org/settings/api)).

**1. Récupérer le projet**

```bash
git clone https://github.com/flaviencantinotti/cinevo.git
cd cinevo
```

**2. Renseigner la clé TMDB**

La clé n'est jamais versionnée : sur une machine fraîchement clonée, il faut la
renseigner une fois.

```bash
cp .env.example .env
```

puis y renseigner la clé :

```
TMDB_API_KEY=votre_cle_ici
```

Sans clé, le site reste consultable grâce au catalogue de secours décrit plus
bas : seules la recherche et les fiches détaillées sont indisponibles.

**3. Lancer**

Placer le dossier dans le répertoire web de votre serveur local (XAMPP, WAMP,
MAMP, Laragon…), puis ouvrir `index.php`.

La base `cinevo` et ses tables sont créées automatiquement au premier
chargement — il n'y a rien à importer. Les identifiants MySQL par défaut sont
ceux d'une installation locale (`localhost`, `root`, sans mot de passe) ; pour
un autre environnement, renseignez `DB_HOST`, `DB_NAME`, `DB_USER` et
`DB_PASS` dans le `.env` (voir `.env.example`), sans toucher au code.

**4. (Optionnel) Avis de démonstration**

Pour ne pas partir d'un site vide, `data/avis-demo.sql` contient vingt-cinq
avis prêts à l'emploi. Ils sont répartis à tour de rôle sur les comptes déjà
créés, quels que soient leurs pseudos et leurs identifiants. Sans danger à
relancer plusieurs fois : les doublons sont ignorés.

L'option `--default-character-set=utf8mb4` n'est pas facultative : sans elle,
les accents sont enregistrés de travers et les doublons ne sont plus reconnus.

```bash
mysql -u root cinevo --default-character-set=utf8mb4 < data/avis-demo.sql
```

---

## Robustesse en cas de panne

Une démonstration se joue souvent sur un réseau qu'on ne maîtrise pas, et sur une
machine où un service peut ne pas avoir démarré. Le site est conçu pour rester
présentable dans ces deux cas.

### Si l'API TMDB est injoignable

- **Délai court** : 4 secondes maximum par appel. Une API lente ne fige jamais la
  page plus de quelques secondes.
- **Cache disque** (`cache/`) : les fiches films sont gardées 24 h, les
  recherches 1 h, les tirages 30 min. Cela allège aussi le nombre d'appels.
- **Réserve de films hors ligne** : chaque tirage réussi alimente un stock local
  de 100 films. Coupez le réseau, la page « Au hasard » continue de tirer
  cinq films différents à chaque clic.
- **Catalogue livré avec le dépôt** (`data/catalogue.json`) : versionné, il prend
  le relais quand il n'y a ni cache ni clé. Un clone neuf affiche donc des films
  dès le premier chargement, sur n'importe quelle machine. Les affiches passent
  par le CDN de TMDB, qui ne demande aucune clé. Voir `data/README.md` pour le
  régénérer.
- **Affiche de remplacement** : un film dont TMDB ne fournit pas d'affiche reçoit
  un visuel neutre, la mise en page ne se casse pas.
- **Repli sur les variables d'environnement** du serveur si le `.env` est absent.

### Si MySQL ne répond pas

Oublier de démarrer MySQL est plus fréquent qu'une panne d'API, et cela suffisait
à rendre le site totalement blanc. Ce n'est plus le cas :

- `includes/db.php` n'interrompt plus le chargement ; il expose `baseDisponible()`
  et un délai de connexion de 3 secondes pour ne pas faire attendre la page.
- Les pages qui ne dépendent pas de la base — **recherche, fiche film, tirage au
  hasard** — restent pleinement consultables.
- Celles qui en dépendent affichent un encart explicite au lieu d'une erreur
  brute : accueil, liste des avis, fil personnel.
- Connexion, inscription et publication d'un avis annoncent clairement
  l'indisponibilité, et le formulaire de rédaction **conserve le texte saisi**.
- `sitemap.php` se limite alors aux pages statiques.

**Sans réseau du tout**

Le catalogue de 100 films livré dans `data/catalogue.json` permet à l'accueil, aux
discussions et au tirage de fonctionner immédiatement, même sur un clone neuf et
sans connexion.

---

## Structure

```
cinevo/
├── includes/
│   ├── config.php          constantes et lecture du .env
│   ├── db.php              connexion MySQL tolérante à la panne, création des tables
│   ├── auth.php            inscription, connexion, session, jetons CSRF
│   ├── tmdb.php            appels à l'API TMDB, cache et repli hors ligne
│   ├── cache.php           cache fichier des réponses de l'API
│   ├── format.php          dates, extraits, avatars
│   ├── resultats-films.php gabarit de liste de films, partagé par deux pages
│   ├── header.php
│   └── footer.php
├── cache/                  réponses TMDB mises en cache (ignoré par Git)
├── data/
│   ├── catalogue.json      catalogue de secours, versionné
│   ├── avis-demo.sql       vingt-cinq avis de démonstration
│   ├── titres-avis.sql     complète les avis importés avant les titres
│   └── README.md           comment régénérer le catalogue
├── uploads/avatars/        photos de profil (contenu ignoré par Git)
├── css/
├── js/
├── images/
│
├── index.php               accueil
├── home.php                fil personnel du membre connecté
├── recherche.php           recherche de films
├── fiche.php               fiche d'un film
├── hasard.php              tirage aléatoire
├── decouvrir.php           mode d'emploi et foire aux questions
├── avis.php                discussions en cours
├── ecrire.php              rédaction d'un avis
├── mes-avis.php · modifier-avis.php · supprimer-avis.php
├── inscription.php · connexion.php · deconnexion.php
├── mot-de-passe-oublie.php · reinitialiser-mdp.php
├── profil.php              e-mail, mot de passe, photo de profil
├── contact.php · a-propos.php · mentions-legales.php
├── conditions-generales.php · cookies.php
├── 404.php · sitemap.php · robots.txt
│
├── .htaccess               refuse l'accès au .env, aux .sql, à includes/ et cache/
├── .env.example            modèle de configuration
└── veille.md               journal de veille et de sécurité
```

---

## Sécurité

Le projet a fait l'objet d'un passage au crible du Top 10 OWASP, documenté dans
`veille.md`. Ce qui est en place :

- **Requêtes préparées** partout où une donnée utilisateur entre dans une
  requête ; les seules requêtes directes sont du SQL statique, sans variable
- **Mots de passe hachés** avec `password_hash()` / `password_verify()`,
  jamais stockés en clair
- **Jetons CSRF** sur la totalité des formulaires POST : les neuf pages du site
  qui traitent un POST vérifient le jeton, sans exception
- **Échappement systématique des sorties** avec `htmlspecialchars()`, contre les
  injections de script
- **Cookies de session** en `HttpOnly` et `SameSite`, avec `Secure` activé dès que
  le site est servi en HTTPS
- **Clé API hors du dépôt**, dans un `.env` ignoré par Git
- **Fichiers sensibles fermés** par `.htaccess` : le `.env`, les `.sql`, `includes/`
  et `cache/` ne sont pas servis par Apache

Les jetons CSRF et les attributs de cookies ont d'abord manqué. Je les ai
identifiés en relisant le projet à la lumière des recommandations OWASP, puis
corrigés — c'est justement ce que retrace `veille.md`.

---

## Limites connues

- Les messages du formulaire de contact sont enregistrés en base (table
  `messages_contact`) mais ne partent pas par mail : il reste à brancher un
  service d'envoi.
- Pas de pagination au-delà de la première page de résultats renvoyée par TMDB.
- Le site n'est pas encore déployé en ligne. Il tourne en local.

---

## À venir

- Mise en ligne sur un hébergement mutualisé
- Notes chiffrées en plus des avis rédigés
- Envoi par mail des messages de contact et des liens de réinitialisation

---

## Auteur

**Flavien Cantinotti** — développeur web
[GitHub](https://github.com/flaviencantinotti) ·
[LinkedIn](https://www.linkedin.com/in/flavien-cantinotti/)

Les données de films proviennent de l'API [TMDB](https://www.themoviedb.org/).
Ce produit utilise l'API TMDB mais n'est ni approuvé ni certifié par TMDB.
