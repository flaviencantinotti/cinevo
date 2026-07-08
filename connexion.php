<?php
$page = 'connexion';
require_once 'includes/db.php';
require_once 'includes/auth.php';

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verifie($_POST['csrf_token'] ?? null)) {
        $erreur = 'Requête invalide, merci de réessayer.';
    } else {
        $email    = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (connecter($conn, $email, $password)) {
            header('Location: home.php');
            exit;
        } else {
            $erreur = 'Email ou mot de passe incorrect.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, follow">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>Cinévo — Connexion</title>
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="contenu">
    <div class="boite-connexion">
        <h1>Connexion</h1>

        <?php if ($erreur): ?>
            <p class="message-erreur"><?= htmlspecialchars($erreur) ?></p>
        <?php endif; ?>

        <form action="connexion.php" method="POST">
            <?= csrf_champ() ?>
            <div class="groupe-champ">
                <label for="email">Adresse e-mail</label>
                <input type="email" id="email" name="email" required autocomplete="email">
            </div>
            <div class="groupe-champ">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>
            <input type="submit" value="Se connecter">
        </form>

        <p class="lien-alternatif">Pas encore de compte ? <a href="inscription.php">Rejoindre Cinévo</a></p>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

<script src="js/scripts.js"></script>
</body>
</html>
