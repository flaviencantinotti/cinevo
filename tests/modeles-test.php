<?php
require_once __DIR__ . '/faux-mysqli.php';
require_once __DIR__ . '/../includes/modeles/avis.php';
require_once __DIR__ . '/../includes/modeles/utilisateurs.php';

$conn = new FausseConnexion([
    ['film_id' => '1', 'contenu' => 'a'],
    ['film_id' => '2', 'contenu' => 'b'],
    ['film_id' => '1', 'contenu' => 'c'],
]);
$parFilm = avisDesFilms($conn, [1, 2]);
[$sql, $requete] = $conn->derniereRequete();

verifie('avisDesFilms regroupe les avis par film', count($parFilm[1]) === 2 && count($parFilm[2]) === 1);
verifie('avisDesFilms met un ? par film, typé entier', str_contains($sql, 'IN (?,?)') && $requete->types === 'ii');

$connVide = new FausseConnexion();
verifie('avisDesFilms sans film ne lance aucune requête',
    avisDesFilms($connVide, []) === [] && $connVide->requetes === []);

verifie('trouverIdParEmail renvoie un entier',
    trouverIdParEmail(new FausseConnexion([['id' => '7']]), 'a@b.fr') === 7);
verifie('trouverIdParEmail renvoie null si personne', trouverIdParEmail(new FausseConnexion(), 'a@b.fr') === null);

$conn = new FausseConnexion();
changerPhoto($conn, 5, null);
[, $requete] = $conn->derniereRequete();
verifie('changerPhoto accepte null pour retirer la photo', $requete->valeurs === [null, 5] && $requete->types === 'si');

$conn = new FausseConnexion();
derniersAvis($conn, 3);
[, $requete] = $conn->derniereRequete();
verifie('derniersAvis envoie la limite en entier', $requete->valeurs === [3] && $requete->types === 'i');

// Invariant de sécurité : on ne lit, modifie ou supprime que SES avis.
foreach ([
    'trouverAvisDeLUtilisateur' => fn($c) => trouverAvisDeLUtilisateur($c, 1, 2),
    'modifierAvis'              => fn($c) => modifierAvis($c, 1, 2, 't', 'c'),
    'supprimerAvis'             => fn($c) => supprimerAvis($c, 1, 2),
] as $nom => $appel) {
    $conn = new FausseConnexion();
    $appel($conn);
    verifie("$nom filtre sur utilisateur_id", str_contains($conn->derniereRequete()[0], 'utilisateur_id = ?'));
}
