<?php $page = 'connexion'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>Cinévo — Connexion</title>
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="contenu">
    <div class="login-container">
        <h2 class="connexionh2">Connexion</h2>
        <form action="home.php" method="POST">
            <div class="form-group">
                <label for="login">Nom d'utilisateur</label>
                <input type="text" id="login" name="login" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>
            <input type="submit" value="Se connecter">
            <div class="error" id="error-message"></div>
        </form>
        <p class="login-alt">Pas encore de compte ? <a href="inscription.php">Rejoindre Cinévo</a></p>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

<script src="js/scripts.js"></script>
</body>
</html>
