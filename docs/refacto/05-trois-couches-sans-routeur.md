# Étape 5 : mettre un nom sur les trois couches

## Les trois couches, sans framework

Après les étapes 1 à 4, le projet est découpé en **trois couches**. C'est ce qu'on
appelle MVC, et il n'a fallu aucun **framework** (une grosse boîte à outils toute faite,
comme Laravel) pour y arriver :

| Couche | Rôle | Où elle vit chez toi |
|---|---|---|
| **M**odèle | parle à la base de données, rend des tableaux PHP | `includes/modeles/` (étape 3) |
| **C**ontrôleur | reçoit la demande, vérifie, décide quoi faire | le haut de chaque page, plus `includes/auth.php` et `includes/validation.php` (étape 4) |
| **V**ue | fabrique le HTML | le bas de chaque page, plus `includes/debut-page.php`, `header.php`, `nav.php`, `fin-page.php` (étape 1) |

Dans une page, on lit toujours dans le même ordre : d'abord le contrôleur (jusqu'à
`include 'includes/debut-page.php'`), ensuite la vue. Sur `profil.php`, un commentaire
marque la frontière : « Fin du contrôleur : en dessous, que de l'affichage ». Les autres
pages n'ont pas ce commentaire, mais la frontière est toujours le même `include`.

C'est la forme la plus simple de MVC, souvent appelée **« une page = un contrôleur »**.
Pour un site de cette taille, c'est le bon choix.

**Où ranger `validation.php` ?** On peut le défendre côté contrôleur (on vérifie ce que le
visiteur a tapé) ou côté modèle (ce sont des règles du métier, valables même sans
formulaire). Ici, on l'a mis avec le contrôleur parce que ses fonctions renvoient des
messages faits pour l'écran. Le plus important est de savoir expliquer ton choix.

## Le dernier nettoyage : l'envoi de la photo

`profil.php` était la page la plus lourde. Dans le traitement de la photo, il y avait
**5 niveaux de `if` imbriqués** : pour comprendre le dernier message d'erreur, il fallait
garder en tête les quatre conditions au-dessus.

On a séparé deux questions :

1. **Ce fichier est-il acceptable ?** (choisi, pas trop gros, vraiment une image).
   Réponse dans `erreurPhotoTeleversee()` (`includes/validation.php`), qui renvoie un
   message ou `null`, comme les autres règles de l'étape 4.
2. **Que fait-on d'un fichier acceptable ?** (le ranger, supprimer l'ancien, mettre à
   jour la base). Cela reste dans la page, à plat : 3 niveaux au lieu de 5.

Tu contrôlais déjà le type d'après le **contenu** du fichier (`mime_content_type`), pas
d'après son nom : c'est le bon réflexe, on l'a gardé tel quel. Un fichier `virus.php`
renommé en `photo.png` est refusé. Une vraie image est acceptée quel que soit son nom,
et rangée avec la bonne extension.

Cinq tests couvrent la règle, dont un vrai PNG de 1 pixel et un faux PNG qui contient du
texte. En retirant le contrôle du type, le test du faux PNG devient rouge.

## Vérification

Le même parcours (aucun fichier, faux PNG, vrai PNG, remplacement, suppression) a été
rejoué sur le code d'origine et sur le code refait, avec une vraie base : **mêmes codes
HTTP, mêmes messages, même nombre de fichiers sur le disque**.

## Et le routeur ?

On ne l'a **pas** introduit, volontairement. Un routeur (un fichier `index.php` qui reçoit
toutes les URL et appelle le bon contrôleur) apporte :

- des URL propres (`/film/424` au lieu de `fiche.php?id=424`) ;
- un point d'entrée unique, pratique pour la sécurité et les erreurs 500.

Mais il coûte un fichier de réécriture d'URL (`.htaccess`), un changement de tous les
liens du site et un risque de casser le référencement existant. Pour 22 pages, le gain
est faible. Ça pourrait faire une **deuxième proposition de modification (PR)**, à part, quand les URL propres
deviendront utiles.

## Ce qui reste à faire (hors de cette PR)

Ces points ne relèvent pas d'une refonte de structure, donc on les a laissés :

- une vraie page d'erreur 500, dessinée comme le reste du site (le filet de la leçon 06
  affiche seulement un message neutre) ;
- `cache/schema-ok-<base>` est écrit même si la création d'une table échoue ;
- le lien de réinitialisation du mot de passe, l'envoi des e-mails, la limitation des
  tentatives de connexion, les polices Google (RGPD) et la gestion d'un rôle admin :
  c'est de la sécurité et du produit, à traiter à part.

## Pour t'entraîner

Dans `profil.php`, le contrôleur est déjà bien séparé de la vue. Choisis une autre page
de ton choix et vérifie : y a-t-il encore du SQL, ou un calcul, après `debut-page.php` ?
Si oui, remonte-le au-dessus. (C'était le cas de `fiche.php`, corrigé dans la leçon 06.)

## À retenir pour l'oral

- Que veut dire MVC ? Montre un fichier de ton projet pour chaque lettre.
- Pourquoi n'avons-nous pas de routeur, et dans quel cas en voudrait-on un ?
- Pourquoi lit-on le contenu du fichier envoyé plutôt que son extension ?
