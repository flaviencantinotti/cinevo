<?php
$page = 'avis';
require_once 'includes/db.php';
require_once 'includes/tmdb.php';
require_once 'includes/format.php';
$tmdb = new TMDB();

// La page regroupe les avis par film : chaque film devient une discussion,
// avec son dernier message et le nombre d'avis publiés (jamais une note).
$parPage      = 10;
$pageCourante = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$offset       = ($pageCourante - 1) * $parPage;

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

    foreach ($filmsRow as $ligne) {
        $filmId = (int) $ligne['film_id'];

        $stmtDernier = $conn->prepare("
            SELECT avis.titre, avis.contenu, utilisateurs.nom_utilisateur
            FROM avis
            JOIN utilisateurs ON avis.utilisateur_id = utilisateurs.id
            WHERE avis.film_id = ?
            ORDER BY avis.publie_le DESC
            LIMIT 1
        ");
        $stmtDernier->bind_param('i', $filmId);
        $stmtDernier->execute();
        $dernierAvis = $stmtDernier->get_result()->fetch_assoc();

        $stmtAuteurs = $conn->prepare("
            SELECT DISTINCT utilisateurs.nom_utilisateur
            FROM avis
            JOIN utilisateurs ON avis.utilisateur_id = utilisateurs.id
            WHERE avis.film_id = ?
            ORDER BY avis.publie_le DESC
            LIMIT 3
        ");
        $stmtAuteurs->bind_param('i', $filmId);
        $stmtAuteurs->execute();
        $auteurs = array_column($stmtAuteurs->get_result()->fetch_all(MYSQLI_ASSOC), 'nom_utilisateur');

        $film = $tmdb->getMovie($filmId);

        $discussions[] = [
            'film_id'     => $filmId,
            'film_titre'  => $film['title'] ?? 'Film',
            'nb_avis'     => (int) $ligne['nb_avis'],
            'dernier'     => $dernierAvis,
            'auteurs'     => $auteurs,
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
    <link rel="stylesheet" type="text/css" href="css/style.css?v=7">
    <title>Tous les avis et critiques de films — Cinévo</title>
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="contenu">

    <div class="entete-page">
        <span class="label-section">Ce qui nous distingue</span>
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
                    <?php foreach ($discussion['auteurs'] as $nomAuteur):
                        $teinte = crc32($nomAuteur) % 360;
                    ?>
                        <span class="avatar" style="background: oklch(0.55 0.12 <?= $teinte ?>);"><?= htmlspecialchars(mb_strtoupper(mb_substr($nomAuteur, 0, 1))) ?></span>
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
        <div class="hero-boutons" style="margin-top:32px; justify-content:center;">
            <?php if ($pageCourante > 1): ?>
                <a href="avis.php?page=<?= $pageCourante - 1 ?>">
                    <button class="btn-blanc">Précédent</button>
                </a>
            <?php endif; ?>
            <span class="label-section" style="align-self:center;">Page <?= $pageCourante ?> / <?= $totalPages ?></span>
            <?php if ($pageCourante < $totalPages): ?>
                <a href="avis.php?page=<?= $pageCourante + 1 ?>">
                    <button class="btn-rouge">Suivant</button>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <hr class="separateur">

    <section class="section-communaute">
        <span class="label-section">Pourquoi sans notes</span>
        <h2>Des critiques, pas des étoiles</h2>
        <p>
            Ici, aucun film n'est réduit à une note sur 5 ou sur 10. Chaque critique que vous lisez sur cette page
            est écrite par un membre de la communauté Cinévo, avec ses propres mots, sans barème ni classement.
        </p>
        <p>
            Envie de partager votre propre ressenti sur un film ? <a href="ecrire.php">Écrivez votre avis</a> en
            quelques lignes, ou allez voir un <a href="hasard.php">film au hasard</a> si vous cherchez l'inspiration.
        </p>
    </section>

    <hr class="separateur">

</main>

<?php include 'includes/footer.php'; ?>

<script src="js/scripts.js"></script>
</body>
</html>
