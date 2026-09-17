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
    <link rel="stylesheet" type="text/css" href="css/style.css?v=85">
    <title>Page introuvable · Cinévo</title>
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<main class="contenu">

    <div style="text-align:center; padding: 100px 0;">
        <h1 style="margin-top:16px;">Cette page n'existe pas.</h1>
        <p class="intro" style="margin:16px auto 0; max-width:480px;">
            Le lien est peut-être cassé, ou la page a été déplacée. Direction l'accueil,
            ou cinq films au hasard en attendant.
        </p>
        <div class="hero-boutons" style="margin-top:28px; justify-content:center;">
            <a href="index.php" class="btn-rouge">Retour à l'accueil</a>
            <a href="hasard.php" class="btn-blanc">Cinq films au hasard</a>
        </div>
    </div>

</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script src="js/scripts.js"></script>
</body>
</html>
