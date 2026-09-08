<?php
if (session_status() === PHP_SESSION_NONE) {
    $enHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => $enHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();
}

function estConnecte() {
    return isset($_SESSION['utilisateur_id']);
}

function utilisateurConnecte() {
    return isset($_SESSION['nom_utilisateur']) ? $_SESSION['nom_utilisateur'] : null;
}

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_champ() {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}

function csrf_verifie($token) {
    return !empty($_SESSION['csrf_token']) && !empty($token) && hash_equals($_SESSION['csrf_token'], $token);
}

function inscrire($conn, $username, $email, $password) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO utilisateurs (nom_utilisateur, email, mot_de_passe) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $username, $email, $hash);
    if ($stmt->execute()) {
        return true;
    }
    return false;
}

function connecter($conn, $email, $password) {
    $stmt = $conn->prepare("SELECT id, nom_utilisateur, mot_de_passe FROM utilisateurs WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['mot_de_passe'])) {
        $_SESSION['utilisateur_id'] = $user['id'];
        $_SESSION['nom_utilisateur'] = $user['nom_utilisateur'];
        return true;
    }
    return false;
}

function deconnecter() {
    session_destroy();
    header('Location: index.php');
    exit;
}