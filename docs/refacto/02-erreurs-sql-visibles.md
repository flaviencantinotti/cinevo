# Étape 2 : une erreur SQL doit faire du bruit

## Mots à connaître

- **Exception** : un signal d'erreur que PHP « lance » (`throw`). Il remonte jusqu'à un
  `try ... catch` qui l'attrape. Si personne ne l'attrape, la page s'arrête.
- **Code `302` et `500`** : codes de réponse HTTP. `302` veut dire « va voir ailleurs »
  (une redirection), `500` veut dire « le serveur a rencontré un problème ».
- **Mode strict** : le réglage de mysqli où une requête qui échoue lève une exception.

## Le problème

Dans `includes/db.php`, une ligne dit à PHP : « ne me signale aucune erreur MySQL ».

```php
mysqli_report(MYSQLI_REPORT_OFF);
```

Depuis PHP 8.1, c'est justement l'inverse qui est le réglage par défaut : une requête
qui échoue lève une exception. Ici, on a désactivé ce filet de sécurité. Conséquence :
quand une requête rate, **le code continue comme si de rien n'était**.

Preuve, sur le site d'origine. On envoie un avis pour un film dont l'identifiant est
trop grand pour la colonne `INT` de la base :

| | Réponse du site | Avis réellement enregistré |
|---|---|---|
| Avant | `302` vers la fiche du film, comme si tout allait bien | non (0 ligne en base) |
| Après | `500` : l'erreur remonte | non (0 ligne en base) |

(Depuis, `ecrire.php` vérifie aussi que le `film_id` tient dans la colonne : voir la leçon 06.)

Avant, le visiteur croit avoir publié son avis, et il n'existe nulle part. C'est le
pire cas : un bug qui ne laisse aucune trace, et que personne ne verra avant qu'un
utilisateur se plaigne.

## La règle

> **Une erreur inattendue doit être bruyante. Une erreur attendue doit être traitée.**

Il y a deux sortes d'erreurs, et il faut savoir de quel côté on est.

- **Attendue** : l'e-mail est déjà pris (deux comptes ne peuvent pas avoir le même),
  ou le serveur MySQL est éteint. On sait que ça peut arriver, on a prévu quoi
  afficher à l'utilisateur.
- **Inattendue** : une colonne trop petite, une table manquante, une faute de frappe
  dans le SQL. C'est un bug. Le cacher ne le fait pas disparaître, ça empêche seulement
  de le voir.

## Ce qui a changé

**`includes/db.php`.** La connexion garde son réglage « silencieux » : un serveur MySQL
injoignable est un cas prévu (le site passe en mode dégradé, c'est une bonne idée du
projet). Une fois connecté, on active le réglage strict. À partir de là, une requête
qui échoue lève une `mysqli_sql_exception`.

**`includes/erreurs-sql.php`** (nouveau). Une seule fonction, `estDoublon()`, qui
reconnaît le code d'erreur MySQL `1062` (« Duplicate entry », valeur déjà utilisée dans
une colonne `UNIQUE`). Le nombre `1062` est écrit une seule fois, avec un nom.

**`inscrire()` et le changement d'e-mail de `profil.php`.** Ces deux endroits
comptaient sur l'échec silencieux : « si `execute()` renvoie `false`, c'est que
l'e-mail existe déjà » (pour `inscrire()`, le nom d'utilisateur aussi est `UNIQUE`). Avec le mode strict, l'échec devient une exception, donc on
l'attrape, et **on ne traite que le doublon** :

```php
try {
    $stmt->execute();
    return true;
} catch (mysqli_sql_exception $erreur) {
    if (estDoublon($erreur)) {
        return false;      // cas prévu : on le gère
    }
    throw $erreur;         // tout le reste : un bug, on le laisse remonter
}
```

Le `throw $erreur` final est l'idée centrale : on attrape large, mais on ne garde que
ce qu'on sait traiter, et on relance le reste.

## Les premiers tests

Le dossier `tests/` est nouveau. Pour les lancer :

```bash
php tests/lancer.php
```

`tests/erreurs-sql-test.php` vérifie les comportements de `inscrire()` : succès,
doublon (renvoie `false`), autre erreur (relancée), et le rôle de `estDoublon()`.

Ces tests utilisent de **faux objets** à la place de la vraie base. Un faux objet a les
mêmes méthodes que le vrai (`prepare`, `execute`...), mais au lieu d'interroger MySQL il
note le SQL reçu et renvoie les lignes qu'on a choisies. Le test peut alors vérifier ce
qui a été demandé, sans base et sans risque d'abîmer des données.

Un test qui ne peut jamais échouer ne prouve rien. On l'a donc essayé à l'envers :
en cassant volontairement `estDoublon()` pour qu'elle réponde toujours « oui », deux
tests passent au rouge. Puis on l'a remise. Fais cette expérience toi-même, c'est la
meilleure façon de comprendre ce qu'un test protège.

## Comment on a vérifié que rien n'a cassé

On a rejoué sur une vraie base MariaDB un parcours complet : inscription (avec doublon),
connexion, écriture, modification et suppression d'un avis, tentative de modifier
l'avis d'un autre membre, changement d'e-mail (avec doublon) et de mot de passe, photo
de profil, formulaire de contact, réinitialisation de mot de passe. Les 37 lignes de
résultat sont **identiques** avant et après la modification.

## Ce qu'il reste à faire (et qui n'est pas dans cette étape)

- Une exception non attrapée dépendait du réglage `display_errors` du serveur : page
  blanche, ou message brut avec chemins et SQL. La leçon 06 ajoute un filet : le détail
  va dans le journal, le visiteur voit un message neutre. Une vraie page d'erreur
  dessinée reste à faire.
- Dans `db.php`, le témoin `cache/schema-ok-<base>` est écrit même si la création des
  tables a échoué. Le site ne retenterait alors jamais de les créer. À corriger
  séparément.

## À retenir pour l'oral

- Quelle différence entre une erreur attendue et inattendue ? Donne un exemple de
  chaque dans Cinévo.
- Pourquoi relancer l'exception avec `throw` au lieu de l'avaler dans le `catch` ?
- À quoi sert un test qu'on a vu échouer au moins une fois ?
