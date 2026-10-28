<?php
$page = 'hasard';

require_once 'includes/auth.php';
require_once 'includes/tmdb.php';
$tmdb = new TMDB();

// Le tirage sert aussi bien à la page complète qu'à la réponse JSON du
// bouton « Encore 5 films » : on le met en forme une seule fois.
$filmsAffiches = [];

foreach ($tmdb->getRandomMovies(5) as $film) {
    $dateSortie = isset($film['release_date']) ? $film['release_date'] : '';

    $filmsAffiches[] = [
        'id'      => $film['id'],
        'titre'   => $film['title'],
        'annee'   => substr($dateSortie, 0, 4),
        'affiche' => $tmdb->getPosterUrl($film['poster_path'], 'w342'),
    ];
}

if (isset($_GET['ajax'])) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($filmsAffiches);
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Cinq films tirés au hasard, sans algorithme de recommandation. Sortez de votre bulle et découvrez un film à voir ce soir sur Cinévo.">
    <link rel="stylesheet" type="text/css" href="css/style.css?v=94">
    <title>Films au hasard à découvrir · Cinévo</title>
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="contenu">

    <div class="entete-page">
        <h1>Au hasard</h1>
        <p class="intro">
            On regarde quoi ce soir ? L'éternelle question qui reste sans réponse pendant de
            longues minutes. Et si on vous donnait un coup de main ? Les filtres de recherche
            c'est bien, mais à chaque fois, c'est les mêmes films qui ressortent. Ici, nous
            essayons de varier les plaisirs. 5 films, le hasard total et peut-être une bonne
            surprise au bout. Laissez-vous tenter par l'inconnu.
        </p>
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

    <?php // Un formulaire plutôt qu'un bouton seul : sans JavaScript, il recharge
          // la page, et comme le tirage est refait à chaque chargement, cela suffit
          // à obtenir cinq nouveaux films. Avec JavaScript, l'envoi est bloqué et
          // seule la grille est remplacée. ?>
    <form method="GET" action="hasard.php" class="hero-boutons" style="margin-top:32px;">
        <button class="btn-rouge" id="btnHasard" type="submit">
            Encore 5 films au hasard
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 2v6h-6M3 22v-6h6M3 8a9 9 0 0 1 15-4l3 3M21 16a9 9 0 0 1-15 4l-3-3"></path>
            </svg>
        </button>
    </form>

    <hr class="separateur">

    <section class="section-communaute">
        <h2>Un vrai tirage au sort, pas une suggestion déguisée</h2>
        <p>
            Cette page pioche cinq films dans un large catalogue, sans tenir compte de votre historique de
            navigation : chaque tirage est indépendant du précédent, aucune mémoire d'une fois sur l'autre.
        </p>
        <p>
            Si un tirage ne vous inspire pas, relancez-en un autre : la sélection est illimitée, aussi
            longtemps qu'il vous faudra pour tomber sur celui qui vous fera dire « oui, ce soir c'est lui ».
        </p>
        <p>
            Vous avez trouvé votre prochain film à voir ? Une fois vu, <a href="ecrire.php">écrivez votre avis</a>
            sur Cinévo, ou allez <a href="decouvrir.php">découvrir le concept du site</a>.
        </p>
    </section>

</main>

<?php include 'includes/footer.php'; ?>

<script src="js/scripts.js"></script>
</body>
</html>
