# Étape 5 : mettre un nom sur les trois couches

## Ce que tu as déjà, sans le savoir

Après les étapes 1 à 4, ton projet est découpé en **trois couches**. C'est ce qu'on
appelle MVC, et tu ne t'en es pas rendu compte parce qu'on n'a installé aucun framework :

| Couche | Rôle | Où elle vit chez toi |
|---|---|---|
| **M**odèle | parle à la base de données, rend des tableaux PHP | `includes/modeles/` (étape 3) |
| **C**ontrôleur | reçoit la demande, vérifie, décide quoi faire | le haut de chaque page, plus `includes/auth.php` et `includes/validation.php` (étape 4) |
| **V**ue | fabrique le HTML | le bas de chaque page, plus `includes/debut-page.php`, `header.php`, `nav.php`, `fin-page.php` (étape 1) |

Dans une page, on lit toujours dans le même ordre : d'abord le contrôleur (jusqu'à
`include 'includes/debut-page.php'`), ensuite la vue. Sur `profil.php`, un commentaire
marque la frontière : « Fin du contrôleur : en dessous, que de l'affichage ».

C'est la forme la plus simple de MVC, souvent appelée **« une page = un contrôleur »**.
Pour un site de cette taille, c'est le bon choix.

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

Le contrôle du type lit le **contenu** du fichier (`mime_content_type`), pas son nom.
Un fichier `virus.php` renommé en `photo.png` reste refusé, et inversement.

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
est faible. Ça pourrait faire une **deuxième PR**, à part, quand les URL propres
deviendront utiles.

## Ce qui reste à faire (hors de cette PR)

Ces points ne relèvent pas d'une refonte de structure, donc on les a laissés :

- une vraie page d'erreur 500 (aujourd'hui, PHP affiche son message brut) ;
- `cache/schema-ok` est écrit même si la création d'une table échoue ;
- le lien de réinitialisation du mot de passe, l'envoi des e-mails, la limitation des
  tentatives de connexion, les polices Google (RGPD) et la gestion d'un rôle admin :
  c'est de la sécurité et du produit, à traiter à part.

## À retenir pour l'oral

- Que veut dire MVC ? Montre un fichier de ton projet pour chaque lettre.
- Pourquoi n'avons-nous pas de routeur, et dans quel cas en voudrait-on un ?
- Pourquoi lit-on le contenu du fichier envoyé plutôt que son extension ?
