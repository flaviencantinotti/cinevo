<?php
$page = '404';
require_once __DIR__ . '/includes/auth.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" type="text/css" href="css/style.css?v=94">
    <title>Page introuvable · Cinévo</title>
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<main class="contenu">

    <div class="bloc-centre">
        <h1>Cette page n'existe pas.</h1>
        <p class="intro">
            Le lien est peut-être cassé, ou la page a été déplacée. Direction l'accueil,
            ou cinq films au hasard en attendant.
        </p>
        <div class="hero-boutons hero-boutons-centre">
            <a href="index.php" class="btn-rouge">Retour à l'accueil</a>
            <a href="hasard.php" class="btn-blanc">Cinq films au hasard</a>
        </div>
    </div>

</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script src="js/scripts.js"></script>
</body>
</html>
