<?php
require_once __DIR__ . '/config.php';

$host = 'localhost';
$dbname = 'cinevo';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die('Erreur de connexion : ' . $conn->connect_error);
}


$conn->query("CREATE DATABASE IF NOT EXISTS `cinevo` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$conn->select_db('cinevo');



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