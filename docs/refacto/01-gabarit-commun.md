# Étape 1 : un seul gabarit pour toutes les pages

## Le problème

Ouvre `contact.php`, puis `profil.php`, puis `a-propos.php`. Au début de chacun, tu
trouvais le même bloc d'une quinzaine de lignes : `<!DOCTYPE html>`, `<head>`, les
`<meta>`, la feuille de style, `<body>`, puis l'inclusion du header. À la fin de
chacun, le même bloc encore : footer, script, `</body>`, `</html>`.

Ça faisait **22 copies du même squelette**. Trois choses en découlent :

- **Changer quelque chose coûte 22 modifications.** Ajouter une icône d'onglet,
  c'était 22 fichiers à ouvrir, et il suffit d'en oublier un.
- **Les copies dérivent.** Trois pages (`a-propos`, `conditions-generales`,
  `mentions-legales`) écrivaient `charset="UTF-8"` et `initial-scale=1.0`,
  les autres `charset="utf-8"` et `initial-scale=1`. Personne ne l'a voulu : c'est
  ce qui arrive quand on copie-colle.
- **Le vrai contenu de la page est noyé.** Dans `contact.php`, ce qui compte (le
  formulaire) commençait après une douzaine de lignes de décor.

## La règle

> **Une connaissance, un seul endroit.**

Le squelette HTML est une connaissance : « une page de Cinévo commence comme ça ».
On l'écrit une fois, dans un fichier, et chaque page vient le chercher avec
`include`. PHP colle alors le contenu du fichier à l'endroit de l'`include`, comme si
tu l'avais tapé là.

## Ce qui a changé

Trois fichiers neufs dans `includes/` :

| Fichier | Rôle |
|---|---|
| `debut-page.php` | `<head>`, ouverture du `<body>`, puis le header |
| `fin-page.php` | footer, script, fermeture de la page |
| `nav.php` | les liens du menu (avant, ils étaient au milieu de `header.php`) |

Chaque fichier a **un seul rôle**. `header.php` s'occupe du bandeau (logo, recherche,
compte), et il délègue le menu à `nav.php`.

Ce qui varie d'une page à l'autre (le titre, la description, la consigne aux moteurs
de recherche) passe par un petit tableau `$meta`, que la page remplit avant
d'appeler le gabarit :

```php
// avant : une douzaine de lignes de HTML en tête de contact.php
// après :
$meta = [
    'titre'       => 'Contact · Cinévo',
    'description' => 'Une question, une suggestion ou une remarque sur Cinévo ? ...',
];
include 'includes/debut-page.php';
```

À la fin de la page, une seule ligne remplace les 5 d'avant :

```php
<?php include 'includes/fin-page.php'; ?>
```

`titre` est obligatoire. `description`, `robots` et `classe_body` sont facultatifs :
le gabarit n'écrit la balise que si tu as donné la valeur.

## Pourquoi un tableau et pas des variables séparées ?

On aurait pu écrire `$titre = ...; $description = ...;`. Mais ces variables vivent
alors toutes dans le même espace que le reste de la page : un nom comme `$titre`
est vite réutilisé pour autre chose (c'est le titre d'un avis dans `ecrire.php`).
Un seul tableau nommé `$meta` réduit le risque de collision à un seul nom.

## Comment on a vérifié que rien n'a cassé

On a lancé le site avant et après, et comparé le HTML produit par chaque page.
Parmi les 18 pages visibles sans être connecté, 16 ont un contenu stable d'une visite
à l'autre : leur HTML est identique, aux détails près suivants :

- les polices Google sont désormais dans le `<head>` (leur place normale) et plus au
  milieu du `<body>` ;
- `charset` et `viewport` sont écrits de la même façon partout ;
- une apostrophe dans une `<meta>` s'écrit `'` au lieu de `&#039;`, ce qui revient au
  même pour le navigateur.

L'accueil et « Au hasard » changent à chaque visite (le tirage est aléatoire), on les
a donc seulement vérifiés à l'œil. Les pages qui demandent une connexion sont
couvertes par le parcours décrit à l'étape 2.

Retiens la méthode : **avant de remanier du code qui marche, on se donne un moyen de
prouver qu'il marche toujours après.** Ici, c'est comparer le HTML. Ailleurs, ce
seront des tests.

## À retenir pour l'oral

- Qu'est-ce que `include` fait exactement ? (Il insère le fichier à cet endroit, dans
  la même portée de variables.)
- Pourquoi ne pas simplement copier-coller le squelette ? (Une règle changée, 22 pages
  à retoucher, et elles finissent par se contredire.)
- Un gabarit commun, c'est le début de la séparation entre **ce qu'on affiche** et
  **ce qu'on calcule**. On y revient aux étapes suivantes.

## Pour t'entraîner

Ajoute une balise `<meta name="theme-color" content="#000">` à toutes les pages.
Combien de fichiers modifies-tu maintenant ? (Un seul : `debut-page.php`. Avant :
22.)
