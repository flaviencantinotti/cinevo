<?php $page = 'inscription'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>Cinévo — Inscription</title>
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="contenu">
    <div class="signup-container">
        <h2>Inscrivez-vous</h2>
        <form class="signup-form" action="home.php" method="post">
            <label for="name">Nom d'utilisateur</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Adresse e-mail</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>

            <label for="dob">Date de naissance</label>
            <input type="date" id="dob" name="dob" required>

            <button type="submit">S'inscrire</button>
        </form>
        <p class="login-alt" style="text-align:center; margin-top:16px;">Déjà un compte ? <a href="connexion.php">Se connecter</a></p>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

<script src="js/scripts.js"></script>
</body>
</html>
