# Étape 6 : ce que la relecture a trouvé (et comment on le prouve)

## Pourquoi cette leçon existe

Une fois les 5 étapes écrites, le code a été **relu par des lecteurs indépendants**, puis
testé à la main sur le site, avec une vraie base de données. Ils ont trouvé de vrais
défauts, **dont certains venaient de nous**. C'est normal : tout code relu en trouve. Ce qui
compte, c'est la méthode : on ne corrige pas parce que quelqu'un « a l'impression » que
c'est faux, on **reproduit d'abord**, puis on corrige, puis on reproduit à nouveau.

## Mots à connaître

- **Journal d'erreurs** (`error_log`) : un fichier où PHP écrit les erreurs, au lieu de
  les montrer au visiteur. Avec Apache, c'est `error.log` ; avec `php -S`, c'est le
  terminal.
- **Reproduire** : refaire exactement l'action qui pose problème, pour voir le défaut
  de ses yeux avant de le corriger.

## Défaut 1 : des champs sans limite (le plus important)

À l'étape 2, on a activé le mode strict : une requête qui échoue lève une exception. À
l'étape 4, on a donné des limites au titre et à l'e-mail. **On en a oublié quatre.** Un
visiteur pouvait envoyer un texte plus long que la colonne de la base :

| Saisie | Site d'origine | Après l'étape 5 | Maintenant |
|---|---|---|---|
| Contact, nom de 101 caractères | « message envoyé », rien en base | erreur PHP brute | message d'erreur clair |
| Contact, message de 70 000 caractères | idem | idem | message d'erreur clair |
| Avis de 70 000 caractères | redirigé comme un succès, rien en base | erreur PHP brute | message d'erreur clair |
| Avis sur un film numéro 99999999999 | idem | idem | « Choisissez un film » |

Chaque ligne a été rejouée sur une vraie base MariaDB : plus rien n'est enregistré à tort,
et plus rien ne s'affiche en dehors du message prévu.

**La règle à retenir** : chaque champ qu'un visiteur peut remplir a une **limite côté
serveur**, qui vient de la taille de la colonne. Ici :

```php
const NOM_CONTACT_LONGUEUR_MAX     = 100;   // VARCHAR(100)
const MESSAGE_CONTACT_LONGUEUR_MAX = 5000;
const AVIS_LONGUEUR_MAX            = 10000; // la colonne TEXT accepte bien plus
const ENTIER_SQL_MAX               = 2147483647; // le plus grand INT
```

Les mêmes constantes servent à poser le `maxlength` des formulaires : le navigateur
empêche déjà de taper trop, le serveur reste le vrai garde-fou (n'importe qui peut
envoyer un formulaire sans passer par ton HTML).

Choix de produit, à connaître : un avis est maintenant limité à 10 000 caractères
(environ 1 700 mots), alors que la colonne pouvait en garder bien plus. C'est une
valeur raisonnable, pas une loi : tu peux la changer à un seul endroit.

Les tests (`tests/validation-test.php`) vérifient les deux côtés de chaque limite : le
dernier caractère accepté, le premier refusé.

## Défaut 2 : une erreur imprévue montrait trop de choses

Rien n'imposait `display_errors=0` dans le projet. Selon le serveur, une exception non
attrapée donnait une page blanche, ou **le message brut de PHP** avec le nom de la table,
les chemins des fichiers, parfois les arguments des fonctions (donc un mot de passe haché
ou le texte d'un message).

On a ajouté un **gestionnaire** dans `includes/erreurs-sql.php`, branché dans `db.php` :

```php
function repondreErreurInattendue(Throwable $erreur) {
    error_log((string) $erreur);                    // le détail va au journal
    http_response_code(500);
    echo '<p>Une erreur est survenue. Réessayez dans un instant.</p>';
}
```

Le visiteur ne voit qu'un message neutre. **Toi**, tu retrouves le détail dans le journal
d'erreurs. C'est la différence entre une erreur *bruyante* (on la voit, on la corrige) et
une erreur *bavarde* (on la montre à tout le monde). Les erreurs **attendues**, comme le
doublon d'e-mail, sont toujours rattrapées là où elles arrivent.

Preuve : on supprime à la main la table `avis`, puis on ouvre la page des avis. Avant le
gestionnaire, la page affichait l'erreur SQL. Maintenant : un code 500 et une ligne neutre.
Deux tests le vérifient, dont un qui s'assure que le détail n'est **pas** dans la page.

Une vraie page d'erreur, dessinée comme le reste du site, reste à faire : c'est un bon
exercice.

## Défaut 3 : `fiche.php` ne suivait pas sa propre leçon

La leçon 05 dit « d'abord le contrôleur, ensuite la vue ». Mais `fiche.php` lisait encore
les avis au milieu de l'affichage. On a remonté ce bloc au-dessus de `debut-page.php`. Le
HTML produit est resté identique, avec et sans avis (comparé avec un film et des avis).

## Défaut 4 : le témoin du schéma ne connaissait pas le nom de la base

`cache/schema-ok` disait « les tables existent » quelle que soit la base utilisée.
Changer de base pouvait donc donner un site sans tables. Le témoin porte maintenant le
nom de la base (`cache/schema-ok-cinevo`). Si tu **supprimes la base à la main**, supprime
aussi son témoin dans `cache/`.

## Défaut 5 : des chiffres faux dans les leçons

Les lecteurs ont recompté : « 22 requêtes dans 11 fichiers » étaient en réalité 21
requêtes dans 13 pages, « quatre pages en `UTF-8` » étaient trois, « 10 copies » de la
vérification du jeton étaient 9. Rien de grave, mais une leçon sur la **preuve** qui se
trompe sur ses chiffres perd de sa force. Tout est corrigé.

Le réflexe : un chiffre écrit dans une doc se recompte avec une commande, par exemple :

```
git grep -c 'csrf_verifie(' 3136338 -- '*.php'   # 3136338 = le code avant la PR
```

## À retenir pour l'oral

- Pourquoi une limite côté navigateur ne suffit-elle pas ?
- Quelle différence entre une erreur bruyante et une erreur bavarde ? Pourquoi veut-on la
  première et pas la seconde ?
- Comment prouve-t-on qu'un défaut est réel avant de le corriger ?

## Pour t'entraîner

Liste chaque champ de chaque formulaire de Cinévo avec la taille de sa colonne
(`includes/db.php`). Pour chacun, trouve où la limite est vérifiée côté serveur. En
manque-t-il encore un ?
