<?php
$page = 'recherche';

require_once 'includes/auth.php';
require_once 'includes/tmdb.php';
require_once 'includes/format.php';
$tmdb = new TMDB();

$resultats     = [];
$aucunResultat = false;
$query         = trim($_GET['q'] ?? '');

if ($query !== '') {
    $data = $tmdb->searchMovie($query);

    if ($data && !empty($data['results'])) {
        $films = $data['results'];
        $films = array_filter($films, function($f) {
            return !empty($f['title']) && !empty($f['poster_path']);
        });
        usort($films, fn($a, $b) => ($b['popularity'] ?? 0) <=> ($a['popularity'] ?? 0));
        $resultats = array_slice($films, 0, 20);
    } else {
        $aucunResultat = true;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Recherchez un film et retrouvez les avis et critiques écrits par la communauté Cinévo, sans notes ni classement.">
    <meta name="robots" content="noindex, follow">
    <link rel="stylesheet" type="text/css" href="css/style.css?v=89">
    <title>Cinévo · Recherche<?= $query ? ' · ' . htmlspecialchars($query) : '' ?></title>
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="contenu">

    <div class="entete-page">
        <h1><?= $query ? 'Résultats pour ' . htmlspecialchars($query) : 'Chercher un film' ?></h1>
    </div>

    <hr class="separateur">

    <?php if (empty($query)): ?>
        <p class="intro">Entrez un titre dans la barre de recherche pour lancer une recherche.</p>

    <?php elseif ($aucunResultat): ?>
        <p class="intro">
            Aucun film ne correspond à « <?= htmlspecialchars($query) ?> ».
            Vérifiez l'orthographe, ou tentez le <a href="hasard.php">tirage au hasard</a>.
        </p>

    <?php else: ?>
        <?php $lienFilm = 'fiche.php'; include 'includes/resultats-films.php'; ?>
    <?php endif; ?>

</main>

<?php include 'includes/footer.php'; ?>

<script src="js/scripts.js"></script>
</body>
</html>
