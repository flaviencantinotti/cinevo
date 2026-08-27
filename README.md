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
git clone https://github.com/flaviencantinotti-ship-it/cinevo.git
cd cinevo
```

**2. Renseigner la clé TMDB**

À la racine du projet, créer un fichier `.env` :

```
TMDB_API_KEY=votre_cle_ici
```

Ce fichier est ignoré par Git : la clé ne part jamais sur le dépôt.

**3. Lancer**

Placer le dossier dans le répertoire web de votre serveur local (WAMP, MAMP,
Laragon…), puis ouvrir `index.php`.

La base `cinevo` et ses deux tables sont créées automatiquement au premier
chargement — il n'y a rien à importer. Les identifiants MySQL par défaut sont
ceux d'une installation locale (`localhost`, `root`, sans mot de passe) ; à
adapter dans `includes/db.php` selon votre environnement.

---

## Structure

```
cinevo/
├── includes/
│   ├── config.php     constantes et lecture du .env
│   ├── db.php         connexion MySQL, création de la base et des tables
│   ├── auth.php       inscription, connexion, session
│   ├── tmdb.php       appels à l'API TMDB
│   ├── header.php
│   └── footer.php
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
[GitHub](https://github.com/flaviencantinotti-ship-it) ·
[LinkedIn](https://www.linkedin.com/in/flavien-cantinotti/)

Les données de films proviennent de l'API [TMDB](https://www.themoviedb.org/).
Ce produit utilise l'API TMDB mais n'est ni approuvé ni certifié par TMDB.
