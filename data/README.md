# Catalogue de secours

`catalogue.json` contient une centaine de films (identifiant, titre, année,
affiche) issus de l'API TMDB. Il sert de réserve : tant qu'il est présent, la
page d'accueil et la page « Au hasard » affichent des films **même sans clé API
et même sans connexion**, sur n'importe quelle machine.

Contrairement au dossier `cache/`, ce fichier est **versionné** : il part avec
le dépôt, et c'est ce qui rend le projet fonctionnel dès le clone.

## Le générer ou le mettre à jour

Depuis une machine où la clé TMDB est configurée et le réseau disponible :

1. ouvrir `diagnostic.php` ;
2. cliquer sur **Préchauffer le cache** ;
3. cliquer sur **Exporter le catalogue** ;
4. commiter le fichier produit :

```bash
git add data/catalogue.json
git commit -m "Met à jour le catalogue de secours"
```

Le fichier ne contient que des données publiques de TMDB : aucune clé, aucune
donnée personnelle.
