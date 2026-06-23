<?php
$page = 'recherche';

require_once 'includes/tmdb.php';
$tmdb = new TMDB();

$resultats    = [];
$erreur       = '';
$query        = trim($_GET['q'] ?? '');

if ($query !== '') {
    $data = $tmdb->searchMovie($query);

    if ($data && !empty($data['results'])) {
        $films = $data['results'];
        $films = array_filter($films, function($f) {
            return !empty($f['title']) && !empty($f['poster_path']);
        });
        usort($films, function($a, $b) {
            return $b['popularity'] > $a['popularity'] ? 1 : -1;
        });
        $resultats = array_slice($films, 0, 20);
    } else {
        $erreur = 'Aucun résultat pour "' . htmlspecialchars($query) . '".';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>Cinévo — Recherche<?= $query ? ' · ' . htmlspecialchars($query) : '' ?></title>
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="contenu">

    <div class="ecrire-header">
        <span class="bibliotheque">Résultats de recherche</span>
        <h1><?= $query ? 'Résultats pour <em>' . htmlspecialchars($query) . '</em>' : 'Chercher un film' ?></h1>
    </div>

    <hr class="diviseur">

    <?php if ($erreur): ?>
        <p class="search-erreur"><?= $erreur ?></p>

    <?php elseif (empty($query)): ?>
        <p class="search-erreur">Entrez un titre dans la barre de recherche.</p>

    <?php else: ?>
        <div class="search-resultats">
            <p class="bibliotheque" style="margin-bottom:16px;">
                <?= count($resultats) ?> résultats
            </p>

            <?php foreach ($resultats as $film):
                $annee   = substr($film['release_date'] ?? '', 0, 4);
                $affiche = $tmdb->getPosterUrl($film['poster_path'], 'w92');
            ?>
                <a href="fiche.php?id=<?= $film['id'] ?>" class="search-item">
                    <img
                        src="<?= $affiche ?>"
                        alt="Affiche de <?= htmlspecialchars($film['title']) ?>"
                        class="search-poster"
                        loading="lazy"
                    >
                    <div class="search-info">
                        <div class="search-titre"><?= htmlspecialchars($film['title']) ?></div>
                        <div class="search-meta">
                            <?= $annee ?>
                            <?php if (!empty($film['original_title']) && $film['original_title'] !== $film['title']): ?>
                                · <em><?= htmlspecialchars($film['original_title']) ?></em>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($film['overview'])): ?>
                            <div class="search-apercu">
                                <?= htmlspecialchars(mb_substr($film['overview'], 0, 120)) ?>…
                            </div>
                        <?php endif; ?>
                    </div>
                    <svg class="search-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14M13 6l6 6-6 6"></path>
                    </svg>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</main>

<?php include 'includes/footer.php'; ?>

<script src="js/scripts.js"></script>
</body>
</html>