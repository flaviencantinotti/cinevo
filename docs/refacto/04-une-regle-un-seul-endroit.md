# Étape 4 : une règle, un seul endroit

## Le problème

Certaines règles du site étaient écrites plusieurs fois, parfois avec de petites
différences :

| Règle | Combien de copies | Où |
|---|---|---|
| Vérifier le jeton CSRF et afficher « Requête invalide… » | 10 | toutes les pages avec un formulaire |
| « Il faut être connecté, sinon retour à la connexion » | 6 | profil, mes avis, modifier, supprimer, home, écrire |
| Un mot de passe fait au moins 6 caractères | 3 (et 3 formulations) | inscription, profil, réinitialisation, plus `minlength="6"` dans le HTML |
| Une adresse e-mail est valide | 4 | inscription, profil, contact, mot de passe oublié |

Le plus grave n'est pas le nombre de copies. C'est qu'**une copie manquait**. La page
« Écrire un avis » vérifiait que le titre n'est pas vide et fait 120 caractères au
plus. La page « Modifier mon avis », elle, ne vérifiait rien.

Voici ce que ça donne sur le site d'origine, essayé pour de vrai :

| Action | Avant | Après |
|---|---|---|
| Modifier un avis avec un titre vide | « Avis modifié », et le titre est effacé | refusé : « Donnez un titre à votre avis. » |
| Modifier un avis avec un titre de 300 caractères | « Avis modifié », mais la base refuse en silence | refusé : « Le titre ne doit pas dépasser 120 caractères. » |
| Contact avec un e-mail de plus de 100 caractères | aucune erreur affichée, message non enregistré | refusé : « Cette adresse e-mail est trop longue. » |

Quand une règle est copiée à plusieurs endroits, un jour quelqu'un la corrige à un
endroit et oublie les autres. Ici, c'est arrivé avant même d'avoir à la corriger.

## La règle

> **Une règle du métier s'écrit une seule fois.**

Attention à ne pas en faire un dogme : deux morceaux de code qui se ressemblent par
hasard ne doivent pas forcément être fusionnés. Ce qu'on fusionne, c'est **une même
décision** (« un mot de passe fait 6 caractères au moins »), pas deux boucles qui se
ressemblent. Preuve : on a laissé séparés les messages « la base de données ne répond
pas ». Ils ont 7 formulations parce que chaque page a un contexte différent
(publication, modification, suppression…), et ils évolueront chacun de leur côté.

## Ce qui a changé

**`includes/validation.php`** (nouveau). Une fonction par règle, qui renvoie **le
message d'erreur à afficher, ou `null` si tout va bien** :

```php
erreurEmail($email)                          // format et longueur
erreurMotDePasse($motDePasse, $confirmation) // identiques, puis assez long
erreurTitreAvis($titre)                      // non vide, 120 caractères au plus
erreurContenuAvis($contenu)                  // 20 caractères au moins
```

Les nombres (6, 100, 120, 20) sont des constantes nommées, écrites une fois. Le
`minlength` des formulaires HTML les utilise aussi, donc le navigateur et le serveur ne
peuvent plus se contredire.

Pourquoi renvoyer un message plutôt que `true`/`false` ? Parce que la page n'a ensuite
qu'à l'afficher, sans réécrire le texte. Et l'ordre des contrôles reste lisible dans la
page :

```php
// avant (inscription.php)
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erreur = 'Cette adresse e-mail n\'est pas valide.';
} elseif (strlen($email) > 100) {
    $erreur = 'Cette adresse e-mail est trop longue.';
} elseif ($password !== $confirm) { ... } elseif (strlen($password) < 6) { ... }

// après
} elseif ($emailInvalide) {
    $erreur = $emailInvalide;
} elseif ($mdpInvalide) {
    $erreur = $mdpInvalide;
}
```

**`includes/auth.php`** gagne trois éléments :
- `exigerConnexion()` remplace les 4 lignes recopiées dans 6 pages ;
- `csrf_verifie_post()` lit le jeton dans `$_POST` (avant, chaque page savait comment) ;
- `ERREUR_CSRF`, le texte du message, écrit une fois.

## Les changements de comportement, dits franchement

Ce n'est plus du pur remaniement sur trois points, parce qu'unifier une règle oblige à
choisir laquelle garder :

1. Le titre est maintenant contrôlé aussi à la modification (tableau plus haut).
2. L'e-mail du formulaire de contact et celui de « mot de passe oublié » sont limités à
   100 caractères, comme à l'inscription. Il le faut : la colonne en base fait 100.
3. Dans le profil, le message devient « Le mot de passe doit faire au moins 6
   caractères » (avant : « Le nouveau mot de passe… »). Un seul libellé.

## Les tests

`tests/validation-test.php` (13 vérifications) teste les règles directement, sans page
ni base : `php tests/lancer.php`. Elles montrent par exemple qu'un titre de 121 `é` est
refusé (on compte des **caractères**, pas des octets, avec `mb_strlen`).

Essai à l'envers : en passant la constante de 6 à 4, un test devient rouge. Remise à 6,
il repasse au vert.

## Comment on a vérifié que rien d'autre n'a bougé

Le parcours complet sur vraie base, rejoué avant et après : **seules les 4 lignes du
tableau plus haut changent**, les 37 autres sont identiques. Le HTML des pages est
inchangé.

## Pour t'entraîner

Le compteur de caractères en JavaScript (`js/scripts.js`) contient encore `var MIN = 20`
écrit en dur. La règle existe donc toujours à un endroit de trop. Comment ferais-tu pour
que le JavaScript lise la valeur de `AVIS_LONGUEUR_MIN` ? (Piste : un attribut `data-min`
sur le `<textarea>`.)

## À retenir pour l'oral

- Quelle différence entre « deux codes qui se ressemblent » et « une même règle écrite
  deux fois » ? Lequel fusionne-t-on ?
- Pourquoi `erreurEmail()` renvoie-t-elle un message ou `null` plutôt qu'un booléen ?
- Où vérifie-t-on les données : dans le navigateur, sur le serveur, ou les deux ? Pourquoi ?
