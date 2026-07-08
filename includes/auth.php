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
    return isset($_SESSION['username']) ? $_SESSION['username'] : null;
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