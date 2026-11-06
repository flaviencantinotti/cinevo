<?php
$page = '404';
require_once __DIR__ . '/includes/auth.php';

$meta = [
    'titre'       => 'Page introuvable · Cinévo',
    'robots'      => 'noindex, nofollow',
];
include __DIR__ . '/includes/debut-page.php';
?>

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

<?php include __DIR__ . '/includes/fin-page.php'; ?>
