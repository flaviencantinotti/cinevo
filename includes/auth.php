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

// Crée un jeton de réinitialisation de mot de passe, valable une heure.
// Retourne le jeton en clair (à mettre dans le lien envoyé à l'utilisateur) ;
// seul son hachage est stocké en base, comme pour un mot de passe.
function genererJetonReinitialisation($conn, $utilisateurId) {
    $jeton = bin2hex(random_bytes(32));
    $hache = hash('sha256', $jeton);
    $expire = date('Y-m-d H:i:s', time() + 3600);

    $stmt = $conn->prepare("INSERT INTO reinitialisations_mdp (utilisateur_id, jeton_hache, expire_le) VALUES (?, ?, ?)");
    $stmt->bind_param('iss', $utilisateurId, $hache, $expire);
    $stmt->execute();

    return $jeton;
}

// Retourne l'utilisateur_id associé à un jeton valide (non expiré, non
// utilisé), ou null si le jeton est invalide.
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

// Marque un jeton comme utilisé, pour qu'il ne serve pas deux fois.
function invaliderJeton($conn, $jeton) {
    $hache = hash('sha256', $jeton);
    $stmt = $conn->prepare("UPDATE reinitialisations_mdp SET utilise = 1 WHERE jeton_hache = ?");
    $stmt->bind_param('s', $hache);
    $stmt->execute();
}