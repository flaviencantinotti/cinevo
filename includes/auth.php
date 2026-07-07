<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function estConnecte() {
    return isset($_SESSION['utilisateur_id']);
}

function utilisateurConnecte() {
    return isset($_SESSION['username']) ? $_SESSION['username'] : null;
}

function inscrire($conn, $username, $email, $password) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO utilisateurs (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $username, $email, $hash);
    if ($stmt->execute()) {
        return true;
    }
    return false;
}

function connecter($conn, $email, $password) {
    $stmt = $conn->prepare("SELECT id, username, password FROM utilisateurs WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['utilisateur_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        return true;
    }
    return false;
}

function deconnecter() {
    session_destroy();
    header('Location: index.php');
    exit;
}