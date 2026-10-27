<?php
$page = 'avis';
require_once 'includes/db.php';
require_once 'includes/tmdb.php';
require_once 'includes/format.php';
$tmdb = new TMDB();

// La page regroupe les avis par film : chaque film devient une discussion,
// avec son dernier message et le nombre d'avis publiés (jamais une note).
$parPage      = 10;
// Ne pas nommer cette variable $pageCourante : includes/header.php utilise
// déjà ce nom pour l'onglet actif et écraserait ce numéro de page.
$pageListe    = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$offset       = ($pageListe - 1) * $parPage;

$total          = 0;
$discussions    = [];

if (baseDisponible()) {
    $compte = $conn->query("SELECT COUNT(DISTINCT film_id) AS total FROM avis");
    $total  = $compte ? (int) $compte->fetch_assoc()['total'] : 0;
}

$totalPages = max(1, (int) ceil($total / $parPage));

if (baseDisponible()) {
    $stmt = $conn->prepare("
        SELECT film_id, COUNT(*) AS nb_avis, MAX(publie_le) AS dernier_le
        FROM avis
        GROUP BY film_id
        ORDER BY dernier_le DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->bind_param('ii', $parPage, $offset);
    $stmt->execute();
    $filmsRow = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Une seule requête pour les avis des dix films affichés, au lieu de deux
    // requêtes par film : la page en réclamait une cinquantaine. Le volume est
    // borné par la pagination, le tri se termine donc en PHP.
    $idsFilms = [];

    foreach ($filmsRow as $ligne) {
        $idsFilms[] = (int) $ligne['film_id'];
    }

    $avisParFilm = [];

    if (!empty($idsFilms)) {
        $trous  = implode(',', array_fill(0, count($idsFilms), '?'));
        $stmtAvis = $conn->prepare("
            SELECT avis.film_id, avis.contenu, utilisateurs.nom_utilisateur, utilisateurs.photo_profil
            FROM avis
            JOIN utilisateurs ON avis.utilisateur_id = utilisateurs.id
            WHERE avis.film_id IN ($trous)
            ORDER BY avis.publie_le DESC
        ");
        $stmtAvis->bind_param(str_repeat('i', count($idsFilms)), ...$idsFilms);
        $stmtAvis->execute();

        foreach ($stmtAvis->get_result()->fetch_all(MYSQLI_ASSOC) as $ligneAvis) {
            $avisParFilm[(int) $ligneAvis['film_id']][] = $ligneAvis;
        }
    }

    foreach ($filmsRow as $ligne) {
        $filmId   = (int) $ligne['film_id'];
        $avisFilm = $avisParFilm[$filmId] ?? [];

        // Les avis arrivent du plus récent au plus ancien : le premier est le
        // dernier message, et les trois premiers auteurs distincts illustrent
        // la discussion.
        $auteurs = [];
        foreach ($avisFilm as $unAvis) {
            if (count($auteurs) >= 3) break;
            if (!isset($auteurs[$unAvis['nom_utilisateur']])) {
                $auteurs[$unAvis['nom_utilisateur']] = [
                    'nom_utilisateur' => $unAvis['nom_utilisateur'],
                    'photo_profil'    => $unAvis['photo_profil'],
                ];
            }
        }

        $film = $tmdb->getMovie($filmId);

        $discussions[] = [
            'film_id'     => $filmId,
            'film_titre'  => $film['title'] ?? 'Film',
            'nb_avis'     => (int) $ligne['nb_avis'],
            'dernier'     => $avisFilm[0] ?? null,
            'auteurs'     => array_values($auteurs),
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Tous les avis et critiques de films publiés par la communauté Cinévo, sans notes ni classement, triés du plus récent au plus ancien.">
    <link rel="stylesheet" type="text/css" href="css/style.css?v=94">
    <title>Tous les avis et critiques de films · Cinévo</title>
</head>
<body>

<?php include 'includes/header.php'; ?>

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
                <div class="avatars-empiles">
                    <?php foreach ($discussion['auteurs'] as $auteur): ?>
                        <?= avatarHtml($auteur['nom_utilisateur'], $auteur['photo_profil']) ?>
                    <?php endforeach; ?>
                </div>
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

<?php include 'includes/footer.php'; ?>

<script src="js/scripts.js"></script>
</body>
</html>
