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
- Page découverte, avec filtres sur la popularité et les votes
- Tirage au hasard, pour ceux qui ne savent pas quoi regarder

**Comptes et avis**
- Inscription, connexion, déconnexion
- Rédaction d'un avis sur un film
- Consultation des avis publiés

**Le reste**
- Pages légales : mentions, cookies, à propos, contact
- `sitemap.php` et `robots.txt`

---

## Stack

| | |
|---|---|
| Back-end | PHP 8.4 (vanilla), MySQL via mysqli |
| Front-end | HTML, CSS, JavaScript |
| Données films | API TMDB (`/search/movie`, `/movie/{id}`, `/movie/popular`, `/discover/movie`) |
| Dépendances | aucune |

---

## Installation

**Prérequis** : PHP 8.4, MySQL, et une clé API TMDB (gratuite sur
[themoviedb.org](https://www.themoviedb.org/settings/api)).

**1. Récupérer le projet**

```bash
git clone https://github.com/flaviencantinotti/cinevo.git
cd cinevo
```

**2. Renseigner la clé TMDB**

La clé n'est jamais versionnée : sur une machine fraîchement clonée, il faut la
renseigner une fois. Deux façons de faire, au choix.

*Depuis le navigateur* — ouvrir `installation.php`, coller la clé, valider. Elle
est vérifiée auprès de TMDB puis enregistrée. L'assistant n'est accessible que
depuis la machine elle-même.

*À la main* :

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

Placer le dossier dans le répertoire web de votre serveur local (WAMP, MAMP,
Laragon…), puis ouvrir `index.php`.

La base `cinevo` et ses deux tables sont créées automatiquement au premier
chargement — il n'y a rien à importer. Les identifiants MySQL par défaut sont
ceux d'une installation locale (`localhost`, `root`, sans mot de passe) ; à
adapter dans `includes/db.php` selon votre environnement.

**Après avoir modifié le CSS**

Les pages appellent la feuille de style avec un numéro de version :

```html
<link rel="stylesheet" type="text/css" href="css/style.css?v=2">
```

Incrémentez ce numéro après chaque modification de `css/style.css`. Sans cela,
les navigateurs qui ont déjà visité le site continuent d'afficher l'ancienne
version gardée en cache, et les changements semblent ne pas s'appliquer.

---

## Robustesse en cas de panne

Une démonstration se joue souvent sur un réseau qu'on ne maîtrise pas, et sur une
machine où un service peut ne pas avoir démarré. Le site est conçu pour rester
présentable dans ces deux cas.

### Si l'API TMDB est injoignable

- **Deux moyens d'appel** : cURL en premier, `file_get_contents` en secours si
  l'extension cURL n'est pas activée sur le serveur.
- **Délai court et nouvelle tentative** : 4 secondes par essai, deux essais au
  maximum. Une API lente ne fige jamais la page plus de quelques secondes.
- **Cache disque** (`cache/`) : les fiches films sont gardées 24 h, les
  recherches 1 h, les tirages 30 min. Cela allège aussi le nombre d'appels.
- **Cache périmé servi en dernier recours** : si l'API ne répond plus, la
  dernière version connue est réutilisée plutôt que d'afficher une page vide.
- **Réserve de films hors ligne** : chaque tirage réussi alimente un stock local
  de 100 films. Coupez le réseau, la page « Au hasard » continue de tirer
  cinq films différents à chaque clic.
- **Catalogue livré avec le dépôt** (`data/catalogue.json`) : versionné, il prend
  le relais quand il n'y a ni cache ni clé. Un clone neuf affiche donc des films
  dès le premier chargement, sur n'importe quelle machine. Les affiches passent
  par le CDN de TMDB, qui ne demande aucune clé. Voir `data/README.md` pour le
  régénérer.
- **Affiche de remplacement** : une image qui ne se charge pas est remplacée par
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

**Avant une démonstration**

Ouvrir `diagnostic.php` dans le navigateur (ou lancer `php diagnostic.php` en
console). La page contrôle PHP, la clé API, le cache, la base de données et
effectue un vrai appel à TMDB. Le bouton **Préchauffer le cache** remplit la
réserve hors ligne pendant que la connexion fonctionne.

```bash
php diagnostic.php   # code de sortie 0 si tout est prêt
```

Cette page est un outil de vérification : elle est à retirer avant une mise en
production.

---

## Structure

```
cinevo/
├── includes/
│   ├── config.php     constantes et lecture du .env
│   ├── db.php         connexion MySQL tolérante à la panne, création des tables
│   ├── auth.php       inscription, connexion, session
│   ├── tmdb.php       appels à l'API TMDB, cache et repli hors ligne
│   ├── cache.php      cache fichier des réponses de l'API
│   ├── header.php
│   └── footer.php
├── cache/             réponses TMDB mises en cache (ignoré par Git)
├── data/
│   └── catalogue.json catalogue de secours, versionné
├── css/
├── js/
├── images/
├── index.php          accueil
├── recherche.php      recherche de films
├── fiche.php          fiche d'un film
├── decouvrir.php      exploration par filtres
├── hasard.php         tirage aléatoire
├── ecrire.php         rédaction d'un avis
├── avis.php           avis publiés
├── inscription.php · connexion.php · deconnexion.php
├── contact.php · a-propos.php · mentions-legales.php · cookies.php
├── diagnostic.php     vérification technique avant démonstration
├── installation.php   assistant de configuration de la clé API
├── .env.example       modèle de configuration
└── veille.md          journal de veille et de sécurité
```

---

## Sécurité

Le projet a fait l'objet d'un passage au crible du Top 10 OWASP, documenté dans
`veille.md`. Ce qui est en place :

- **Requêtes préparées** partout où une donnée utilisateur entre dans une
  requête ; les seules requêtes directes sont du SQL statique, sans variable
- **Mots de passe hachés** avec `password_hash()` / `password_verify()`,
  jamais stockés en clair
- **Jetons CSRF** sur les formulaires POST (connexion, inscription, publication
  d'un avis)
- **Échappement systématique des sorties** avec `htmlspecialchars()`, contre les
  injections de script
- **Cookies de session** en `HttpOnly`, `SameSite` et `Secure`
- **Clé API hors du dépôt**, dans un `.env` ignoré par Git

Les jetons CSRF et les attributs de cookies ont d'abord manqué. Je les ai
identifiés en relisant le projet à la lumière des recommandations OWASP, puis
corrigés — c'est justement ce que retrace `veille.md`.

---

## Limites connues

- Le formulaire de contact affiche un message de confirmation mais n'envoie
  rien : il reste à brancher un service d'envoi de mail.
- Pas de pagination au-delà de la première page de résultats renvoyée par TMDB.
- Le site n'est pas encore déployé en ligne. Il tourne en local.

---

## À venir

- Mise en ligne sur un hébergement mutualisé
- Notes chiffrées en plus des avis rédigés
- Espace personnel : retrouver ses propres avis, les modifier, les supprimer

---

## Auteur

**Flavien Cantinotti** — développeur web
[GitHub](https://github.com/flaviencantinotti) ·
[LinkedIn](https://www.linkedin.com/in/flavien-cantinotti/)

Les données de films proviennent de l'API [TMDB](https://www.themoviedb.org/).
Ce produit utilise l'API TMDB mais n'est ni approuvé ni certifié par TMDB.
