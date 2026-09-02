<?php
$page = 'hasard';

require_once 'includes/auth.php';
require_once 'includes/tmdb.php';
$tmdb = new TMDB();

$films = $tmdb->getRandomMovies(5);

function formaterFilm(TMDB $tmdb, array $film): array {
    return [
        'id'     => $film['id'],
        'titre'  => $film['title'],
        'annee'  => substr($film['release_date'] ?? '', 0, 4),
        'affiche' => $tmdb->getPosterUrl($film['poster_path'], 'w342'),
    ];
}

if (isset($_GET['ajax'])) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array_map(function ($f) use ($tmdb) {
        return formaterFilm($tmdb, $f);
    }, $films));
    exit;
}

$filmsAffiches = array_map(function ($f) use ($tmdb) {
    return formaterFilm($tmdb, $f);
}, $films);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Cinq films tirés au hasard, sans algorithme de recommandation. Sortez de votre bulle et découvrez un film à voir ce soir sur Cinévo.">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>Films au hasard à découvrir — Cinévo</title>
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="contenu">

    <div class="entete-page">
        <span class="label-section">Cinq films, tirés au sort</span>
        <h1>Au hasard</h1>
        <p class="intro">Pas d'algorithme de recommandation. Juste le hasard, pour sortir de sa bulle.</p>
    </div>

    <hr class="separateur">

    <div class="grille-hasard" id="grilleHasard" aria-live="polite">
        <?php if (empty($filmsAffiches)): ?>
            <p class="message-erreur">
                Le tirage est momentanément indisponible : le catalogue de films n'a pas pu être joint.
                Réessayez dans un instant.
            </p>
        <?php endif; ?>

        <?php foreach ($filmsAffiches as $film): ?>
            <a href="fiche.php?id=<?= $film['id'] ?>" class="carte-hasard">
                <div class="affiche-hasard">
                    <img src="<?= htmlspecialchars($film['affiche']) ?>" alt="Affiche de <?= htmlspecialchars($film['titre']) ?>" loading="lazy">
                </div>
                <div class="titre-hasard"><?= htmlspecialchars($film['titre']) ?></div>
                <div class="annee-hasard"><?= htmlspecialchars($film['annee']) ?></div>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="hero-boutons" style="margin-top:32px;">
        <button class="btn-rouge" id="btnHasard" type="button">
            Encore 5 films au hasard
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 2v6h-6M3 22v-6h6M3 8a9 9 0 0 1 15-4l3 3M21 16a9 9 0 0 1-15 4l-3-3"></path>
            </svg>
        </button>
    </div>

    <hr class="separateur">

    <section class="section-communaute">
        <span class="label-section">Comment ça marche</span>
        <h2>Un tirage aléatoire de films, sans algorithme de recommandation</h2>
        <p>
            Cette page pioche cinq films au hasard dans un large catalogue, sans tenir compte de votre historique
            ni d'un quelconque algorithme de recommandation. Pas de tri par popularité, pas de suggestion
            personnalisée : chaque tirage aléatoire de films est indépendant du précédent, pour une découverte de
            films vraiment ouverte au hasard.
        </p>
        <p>
            L'objectif est simple : sortir de sa bulle de filtres et retomber sur un film que vous n'auriez
            jamais cherché vous-même — qu'il s'agisse d'une dernière sortie, d'un classique du cinéma ou d'une
            pépite méconnue. Si un tirage ne vous inspire pas, relancez-en un autre : la sélection aléatoire est
            illimitée.
        </p>
        <p>
            Vous avez trouvé votre prochain film à voir ? Une fois vu, <a href="ecrire.php">écrivez votre avis</a>
            sur Cinévo, ou allez <a href="decouvrir.php">découvrir le concept du site</a>.
        </p>
    </section>

    <hr class="separateur">

</main>

<?php include 'includes/footer.php'; ?>

<script src="js/scripts.js"></script>
</body>
</html>
