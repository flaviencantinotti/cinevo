<?php
$page = 'inscription';
require_once 'includes/db.php';
require_once 'includes/auth.php';

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm  = trim($_POST['confirm'] ?? '');

    if ($password !== $confirm) {
        $erreur = 'Les mots de passe ne correspondent pas.';
    } elseif (strlen($password) < 6) {
        $erreur = 'Le mot de passe doit faire au moins 6 caractères.';
    } else {
        if (inscrire($conn, $username, $email, $password)) {
            connecter($conn, $email, $password);
            header('Location: home.php');
            exit;
        } else {
            $erreur = 'Ce nom d\'utilisateur ou cet email est déjà utilisé.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Créez votre compte Cinévo gratuitement et rejoignez une communauté de cinéphiles qui écrit ses avis sur les films, sans notes ni algorithme.">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>Inscription gratuite — Cinévo</title>
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="contenu">
    <div class="boite-inscription">
        <h1>Inscrivez-vous</h1>

        <?php if ($erreur): ?>
            <p class="message-erreur" style="text-align:center;"><?= htmlspecialchars($erreur) ?></p>
        <?php endif; ?>

        <form class="formulaire" action="inscription.php" method="POST">
            <label for="username">Nom d'utilisateur</label>
            <input type="text" id="username" name="username" required>

            <label for="email">Adresse e-mail</label>
            <input type="email" id="email" name="email" required autocomplete="email">

            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required autocomplete="new-password">

            <label for="confirm">Confirmer le mot de passe</label>
            <input type="password" id="confirm" name="confirm" required autocomplete="new-password">

            <button type="submit">S'inscrire</button>
        </form>

        <p class="lien-alternatif">Déjà un compte ? <a href="connexion.php">Se connecter</a></p>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

<script src="js/scripts.js"></script>
</body>
</html>
