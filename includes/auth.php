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

        session_regenerate_id(true);

        $_SESSION['utilisateur_id'] = $user['id'];
        $_SESSION['nom_utilisateur'] = $user['nom_utilisateur'];
        return true;
    }
    return false;
}

function deconnecter() {

    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }

    session_destroy();
    header('Location: index.php');
    exit;
}

function genererJetonReinitialisation($conn, $utilisateurId) {
    $jeton = bin2hex(random_bytes(32));
    $hache = hash('sha256', $jeton);
    $expire = date('Y-m-d H:i:s', time() + 3600);

    $stmt = $conn->prepare("INSERT INTO reinitialisations_mdp (utilisateur_id, jeton_hache, expire_le) VALUES (?, ?, ?)");
    $stmt->bind_param('iss', $utilisateurId, $hache, $expire);
    $stmt->execute();

    return $jeton;
}

function utilisateurDepuisJeton($conn, $jeton) {
    $hache = hash('sha256', $jeton);

    $stmt = $conn->prepare("
        SELECT utilisateur_id FROM reinitialisations_mdp
        WHERE jeton_hache = ? AND utilise = 0 AND expire_le > NOW()
    ");
    $stmt->bind_param('s', $hache);
    $stmt->execute();
    $ligne = $stmt->get_result()->fetch_assoc();

    return $ligne ? (int) $ligne['utilisateur_id'] : null;
}

function invaliderJeton($conn, $jeton) {
    $hache = hash('sha256', $jeton);
    $stmt = $conn->prepare("UPDATE reinitialisations_mdp SET utilise = 1 WHERE jeton_hache = ?");
    $stmt->bind_param('s', $hache);
    $stmt->execute();
}