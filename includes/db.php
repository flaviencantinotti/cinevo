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
            username VARCHAR(50) UNIQUE NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");

    $conn->query("
        CREATE TABLE IF NOT EXISTS avis (
            id INT AUTO_INCREMENT PRIMARY KEY,
            utilisateur_id INT NOT NULL,
            film_id INT NOT NULL,
            titre VARCHAR(255),
            contenu TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
        )
    ");
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
