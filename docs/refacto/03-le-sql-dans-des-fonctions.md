# Étape 3 : le SQL sort des pages (le « modèle »)

## Le problème

Avant cette étape, le SQL était écrit **directement dans les pages** : 21 requêtes
(17 préparées, 4 simples) réparties dans 13 pages, pour seulement 4 tables. Dans `fiche.php`, la
requête était même au milieu du HTML, entre deux balises.

Une **requête préparée** envoie le SQL avec des `?`, puis les valeurs à part : une valeur
tapée par un visiteur ne peut jamais être lue comme du SQL. Bonne nouvelle : toutes tes
requêtes qui reçoivent une valeur du visiteur étaient déjà préparées. Les 4 requêtes
simples n'en reçoivent aucune.

Cela pose trois problèmes concrets :

- **Une même idée est écrite plusieurs fois.** « Retrouver un avis qui m'appartient »
  existait dans `modifier-avis.php` et dans `supprimer-avis.php`, avec une colonne de
  différence. « Changer le mot de passe » était recopié dans `profil.php` et dans
  `reinitialiser-mdp.php`.
- **Pour savoir ce que fait le site avec ses données, il faut ouvrir 13 fichiers.**
- **Impossible de tester** une requête sans lancer une page entière.

## La règle

> **Une page décide quoi afficher. Elle ne sait pas comment on interroge la base.**

C'est la première lettre de MVC (Modèle, Vue, Contrôleur, détaillé à l'étape 5) : le
**M**, le **modèle**. C'est la couche qui connaît
les données (les tables, les requêtes) et qui les rend sous une forme simple : des
tableaux PHP. La page demande « les avis de ce film » ; comment on les trouve ne la
regarde pas.

## Ce qui a changé

Trois fichiers dans `includes/modeles/`, un par sujet :

| Fichier | Contient |
|---|---|
| `avis.php` | `derniersAvis`, `avisDuFilm`, `avisDeLUtilisateur`, `trouverAvisDeLUtilisateur`, `ajouterAvis`, `modifierAvis`, `supprimerAvis`, plus trois fonctions pour la page Discussions et une pour le sitemap |
| `utilisateurs.php` | `trouverUtilisateur`, `trouverIdParEmail`, `changerEmail`, `changerMotDePasse`, `changerPhoto` |
| `messages-contact.php` | `enregistrerMessageContact` |

`includes/db.php` les charge, donc toute page qui se connecte à la base y a accès.
Il n'y a plus un seul `prepare()` dans les pages.

Avant / après, dans `mes-avis.php` :

```php
// avant : 8 lignes de SQL au milieu de la page
$stmt = $conn->prepare("
    SELECT id, film_id, titre, contenu, publie_le
    FROM avis WHERE utilisateur_id = ? ORDER BY publie_le DESC
");
$stmt->bind_param('i', $_SESSION['utilisateur_id']);
$stmt->execute();
$mesAvis = ajouterFilms($tmdb, $stmt->get_result()->fetch_all(MYSQLI_ASSOC));

// après : la page raconte ce qu'elle veut, pas comment
$mesAvis = ajouterFilms($tmdb, avisDeLUtilisateur($conn, $_SESSION['utilisateur_id']));
```

La page se lit maintenant comme une phrase : « mes avis, avec leurs films ».

## Pourquoi des fonctions et pas des classes ?

On aurait pu écrire une classe qui regroupe les fonctions d'avis (un « dépôt »). On ne l'a pas fait : le projet utilise
déjà des fonctions (`inscrire()`, `connecter()` dans `auth.php`), elles suffisent, et
une classe n'apporterait ici que du vocabulaire en plus. **On choisit la solution la
plus simple qui marche**, et on ne la complique que quand un vrai besoin apparaît.

Chaque fonction reçoit `$conn` en premier paramètre au lieu de le prendre dans une
variable globale. C'est ce qui permet de la tester avec un faux `$conn`.

## Une règle de sécurité qui devient testable

Dans `modifierAvis`, `supprimerAvis` et `trouverAvisDeLUtilisateur`, la requête
contient `WHERE id = ? AND utilisateur_id = ?`. C'est ce qui empêche un membre de
toucher à l'avis d'un autre. Avant, cette règle était écrite en 4 requêtes dans 2 pages (`modifier-avis.php` et
`supprimer-avis.php`).
Maintenant elle est dans un seul fichier, et `tests/modeles-test.php` vérifie qu'elle y
est toujours :

```
php tests/lancer.php
```

On a vérifié que ce test sert : en retirant le filtre `utilisateur_id` de
`supprimerAvis`, le test devient rouge. On a remis le filtre ensuite.

## Comment on a vérifié que rien n'a cassé

Le parcours complet sur vraie base (inscription, avis, modification, suppression,
profil, contact, réinitialisation de mot de passe) donne exactement les **mêmes 37
lignes** qu'avant l'étape (le parcours en compte 42 à la fin de la PR, car l'étape 4 en a
ajouté 5). Les 15 tests passent. Le HTML des pages sans base est
inchangé.

## Ce qu'on a laissé de côté, volontairement

- `inscrire()`, `connecter()` et les fonctions de jeton restent dans `auth.php` : elles
  forment déjà un bloc cohérent.
- Le tri, la pagination et le regroupement des avis par film restent dans le modèle
  quand ils sont liés à la requête (`avisDesFilms` renvoie déjà les avis regroupés).

## À retenir pour l'oral

- Que veut dire « modèle » dans MVC ? Montre un exemple dans Cinévo.
- Pourquoi passer `$conn` en paramètre plutôt que d'utiliser `global $conn` ?
- Où est la règle « on ne modifie que ses propres avis » ? Comment sais-tu qu'elle
  n'a pas disparu ?
