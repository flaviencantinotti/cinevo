<?php
require_once __DIR__ . '/config.php';

/**
 * Connexion à la base de données.
 *
 * L'absence de base n'interrompt plus le chargement : les pages qui ont besoin
 * de MySQL affichent un message clair, celles qui n'en dépendent pas (recherche,
 * fiche film, tirage au hasard) restent consultables.
 *
 * Après inclusion de ce fichier :
 *   $conn             mysqli connecté, ou null
 *   baseDisponible()  true si les requêtes peuvent être exécutées
 */

$host   = 'localhost';
$dbname = 'cinevo';
$user   = 'root';
$pass   = '';

// On récupère les erreurs mysqli à la main plutôt que sous forme d'exception,
// pour pouvoir afficher un message au lieu d'interrompre la page.
mysqli_report(MYSQLI_REPORT_OFF);

$conn     = null;
$dbErreur = '';

$lien = mysqli_init();

// Sans ce délai, une base éteinte peut bloquer la page très longtemps.
$lien->options(MYSQLI_OPT_CONNECT_TIMEOUT, 3);

if (@$lien->real_connect($host, $user, $pass)) {
    $conn = $lien;
} else {
    $dbErreur = $lien->connect_error ? $lien->connect_error : 'Connexion refusée';
}

if ($conn !== null) {
    $conn->set_charset('utf8mb4');

    $conn->query("CREATE DATABASE IF NOT EXISTS `cinevo` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    if (!$conn->select_db($dbname)) {
        $dbErreur = 'Base « ' . $dbname . ' » inaccessible';
        $conn     = null;
    }
}

if ($conn !== null) {
    $conn->query("
        CREATE TABLE IF NOT EXISTS utilisateurs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nom_utilisateur VARCHAR(50) UNIQUE NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            mot_de_passe VARCHAR(255) NOT NULL,
            inscrit_le DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");

    $conn->query("
        CREATE TABLE IF NOT EXISTS avis (
            id INT AUTO_INCREMENT PRIMARY KEY,
            utilisateur_id INT NOT NULL,
            film_id INT NOT NULL,
            titre VARCHAR(255),
            contenu TEXT NOT NULL,
            publie_le DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
        )
    ");

    // Messages envoyés depuis le formulaire de contact (page contact.php).
    $conn->query("
        CREATE TABLE IF NOT EXISTS messages_contact (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nom VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            message TEXT NOT NULL,
            envoye_le DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // Jetons de réinitialisation de mot de passe : on stocke un hachage du
    // jeton, jamais le jeton lui-même, comme pour un mot de passe.
    $conn->query("
        CREATE TABLE IF NOT EXISTS reinitialisations_mdp (
            id INT AUTO_INCREMENT PRIMARY KEY,
            utilisateur_id INT NOT NULL,
            jeton_hache VARCHAR(255) NOT NULL,
            expire_le DATETIME NOT NULL,
            utilise TINYINT(1) NOT NULL DEFAULT 0,
            cree_le DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
        )
    ");

    // Renomme les colonnes d'une base créée avec une ancienne version du
    // projet (en anglais), pour ne pas casser un environnement déjà en place.
    renommerColonneSiExiste($conn, 'utilisateurs', 'username', 'nom_utilisateur VARCHAR(50) UNIQUE NOT NULL');
    renommerColonneSiExiste($conn, 'utilisateurs', 'password', 'mot_de_passe VARCHAR(255) NOT NULL');
    renommerColonneSiExiste($conn, 'utilisateurs', 'created_at', 'inscrit_le DATETIME DEFAULT CURRENT_TIMESTAMP');
    renommerColonneSiExiste($conn, 'avis', 'created_at', 'publie_le DATETIME DEFAULT CURRENT_TIMESTAMP');
}

// Renomme une colonne uniquement si elle existe encore sous son ancien nom.
function renommerColonneSiExiste($conn, $table, $ancienNom, $nouvelleDefinition) {
    $resultat = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$ancienNom'");

    if ($resultat && $resultat->num_rows > 0) {
        $conn->query("ALTER TABLE `$table` CHANGE `$ancienNom` $nouvelleDefinition");
    }
}

// La base est-elle utilisable ?
function baseDisponible() {
    global $conn;
    return $conn !== null;
}

// Détail de la panne, affiché par la page de diagnostic.
function baseErreur() {
    global $dbErreur;
    return $dbErreur;
}

// Encart affiché à la place d'un contenu qui vient de la base.
// $action décrit ce qui est momentanément impossible.
function messageBaseIndisponible($action = 'Cette partie du site') {
    return '<p class="message-erreur">'
        . htmlspecialchars($action)
        . ' est momentanément indisponible : la base de données ne répond pas. '
        . 'Le catalogue de films, lui, reste consultable.</p>';
}
