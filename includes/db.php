<?php
require_once __DIR__ . '/config.php';

$host   = config_valeur('DB_HOST', 'localhost');
$dbname = config_valeur('DB_NAME', 'cinevo');
$user   = config_valeur('DB_USER', 'root');
$pass   = config_valeur('DB_PASS', '');

mysqli_report(MYSQLI_REPORT_OFF);

$conn     = null;
$dbErreur = '';

$lien = mysqli_init();

$lien->options(MYSQLI_OPT_CONNECT_TIMEOUT, 3);

if (@$lien->real_connect($host, $user, $pass)) {
    $conn = $lien;
} else {
    $dbErreur = $lien->connect_error ? $lien->connect_error : 'Connexion refusée';
}

if ($conn !== null) {
    $conn->set_charset('utf8mb4');

    $conn->query("CREATE DATABASE IF NOT EXISTS `" . $conn->real_escape_string($dbname) . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    if (!$conn->select_db($dbname)) {
        $dbErreur = 'Base « ' . $dbname . ' » inaccessible';
        $conn     = null;
    }
}

$temoinSchema = __DIR__ . '/../cache/schema-ok';

if ($conn !== null && !is_file($temoinSchema)) {
    $conn->query("
        CREATE TABLE IF NOT EXISTS utilisateurs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nom_utilisateur VARCHAR(50) UNIQUE NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            mot_de_passe VARCHAR(255) NOT NULL,
            photo_profil VARCHAR(255) NULL DEFAULT NULL,
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

    $conn->query("
        CREATE TABLE IF NOT EXISTS messages_contact (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nom VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            message TEXT NOT NULL,
            envoye_le DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");

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

    if (!is_dir(dirname($temoinSchema))) {
        @mkdir(dirname($temoinSchema), 0775, true);
    }
    @file_put_contents($temoinSchema, date('c'));
}

function baseDisponible() {
    global $conn;
    return $conn !== null;
}

function baseErreur() {
    global $dbErreur;
    return $dbErreur;
}

function messageBaseIndisponible($action = 'Cette partie du site') {
    return '<p class="message-erreur">'
        . htmlspecialchars($action)
        . ' est momentanément indisponible : la base de données ne répond pas. '
        . 'Le catalogue de films, lui, reste consultable.</p>';
}
