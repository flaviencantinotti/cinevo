<?php
$page = 'avis';
require_once 'includes/db.php';
require_once 'includes/tmdb.php';
require_once 'includes/format.php';
$tmdb = new TMDB();

$parPage      = 10;

$pageListe    = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$offset       = ($pageListe - 1) * $parPage;

$total          = 0;
$discussions    = [];

if (baseDisponible()) {
    $total = compterFilmsAvecAvis($conn);
}

$totalPages = max(1, (int) ceil($total / $parPage));

if (baseDisponible()) {
    $filmsRow = filmsLesPlusRecemmentCommentes($conn, $parPage, $offset);

    $idsFilms = [];

    foreach ($filmsRow as $ligne) {
        $idsFilms[] = (int) $ligne['film_id'];
    }

    $avisParFilm = avisDesFilms($conn, $idsFilms);

    foreach ($filmsRow as $ligne) {
        $filmId   = (int) $ligne['film_id'];
        $avisFilm = $avisParFilm[$filmId] ?? [];

        $film = $tmdb->getMovie($filmId);

        $discussions[] = [
            'film_id'     => $filmId,
            'film_titre'  => $film['title'] ?? 'Film',
            'nb_avis'     => (int) $ligne['nb_avis'],
            'dernier'     => $avisFilm[0] ?? null,
        ];
    }
}

$meta = [
    'titre'       => 'Tous les avis et critiques de films · Cinévo',
    'description' => 'Tous les avis et critiques de films publiés par la communauté Cinévo, sans notes ni classement, triés du plus récent au plus ancien.',
];
include 'includes/debut-page.php';
?>

<main class="contenu">

    <div class="entete-page">
        <h1>Discussions en cours</h1>
        <p class="intro">Chaque avis ouvre une conversation autour d'un film. Pas de note ni de classement : juste des gens qui débattent, du plus récent au plus ancien.</p>
    </div>

    <hr class="separateur">

    <div class="liste-discussions">

        <?php if (!baseDisponible()): ?>
            <?= messageBaseIndisponible('La liste des discussions') ?>
        <?php elseif (empty($discussions)): ?>
            <p class="intro">Aucun avis publié pour l'instant. <a href="ecrire.php">Soyez le premier à en écrire un</a> !</p>
        <?php endif; ?>

        <?php foreach ($discussions as $discussion): ?>
            <a href="fiche.php?id=<?= $discussion['film_id'] ?>" class="apercu-discussion">
                <div class="corps">
                    <div class="film"><?= htmlspecialchars($discussion['film_titre']) ?></div>
                    <?php if ($discussion['dernier']): ?>
                        <div class="dernier-message"><?= htmlspecialchars($discussion['dernier']['nom_utilisateur']) ?> : « <?= htmlspecialchars(extrait($discussion['dernier']['contenu'], 90)) ?> »</div>
                    <?php endif; ?>
                </div>
                <div class="compte"><?= $discussion['nb_avis'] ?> avis</div>
            </a>
        <?php endforeach; ?>

    </div>

    <?php if ($totalPages > 1): ?>
        <nav class="pagination" aria-label="Pages de discussions">
            <?php if ($pageListe > 1): ?>
                <a href="avis.php?page=<?= $pageListe - 1 ?>" class="btn-blanc">Précédent</a>
            <?php endif; ?>
            <span class="label-section">Page <?= $pageListe ?> / <?= $totalPages ?></span>
            <?php if ($pageListe < $totalPages): ?>
                <a href="avis.php?page=<?= $pageListe + 1 ?>" class="btn-rouge">Suivant</a>
            <?php endif; ?>
        </nav>
    <?php endif; ?>

    <hr class="separateur">

    <section class="section-communaute">
        <h2>Des critiques, pas des étoiles</h2>
        <p>
            Chaque film ci-dessus devient une discussion, pas une fiche produit : les avis les plus récents
            remontent en tête dès qu'un membre publie une nouvelle critique, pour suivre la conversation en
            train de se faire.
        </p>
        <p>
            Envie de partager votre propre ressenti sur un film ? <a href="ecrire.php">Écrivez votre avis</a> en
            quelques lignes, ou allez voir un <a href="hasard.php">film au hasard</a> si vous cherchez l'inspiration.
        </p>
    </section>

</main>

<?php include 'includes/fin-page.php'; ?>
