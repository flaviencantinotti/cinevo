# Catalogue de secours

`catalogue.json` contient une centaine de films (identifiant, titre, année,
affiche) issus de l'API TMDB. Il sert de réserve : tant qu'il est présent, la
page d'accueil et la page « Au hasard » affichent des films **même sans clé API
et même sans connexion**, sur n'importe quelle machine.

Contrairement au dossier `cache/`, ce fichier est **versionné** : il part avec
le dépôt, et c'est ce qui rend le projet fonctionnel dès le clone.

## Le générer ou le mettre à jour

Depuis une machine où la clé TMDB est configurée et le réseau disponible, la
classe `TMDB` expose `exporterCatalogue()`, qui écrit dans ce fichier les films
déjà rencontrés. Une fois le fichier produit, il suffit de le commiter :

```bash
git add data/catalogue.json
git commit -m "Met à jour le catalogue de secours"
```

Le fichier ne contient que des données publiques de TMDB : aucune clé, aucune
donnée personnelle.
