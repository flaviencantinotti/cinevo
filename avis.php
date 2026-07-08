<?php
$page = 'avis';
require_once 'includes/db.php';
require_once 'includes/tmdb.php';
$tmdb = new TMDB();

function formaterDateFr(string $datetime): string {
    static $mois = [1 => 'janvier', 2 => 'février', 3 => 'mars', 4 => 'avril', 5 => 'mai', 6 => 'juin',
                    7 => 'juillet', 8 => 'août', 9 => 'septembre', 10 => 'octobre', 11 => 'novembre', 12 => 'décembre'];
    $d = new DateTime($datetime);
    return (int) $d->format('j') . ' ' . $mois[(int) $d->format('n')] . ' ' . $d->format('Y');
}

$parPage      = 12;
$pageCourante = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$offset       = ($pageCourante - 1) * $parPage;

$total       = (int) $conn->query("SELECT COUNT(*) AS total FROM avis")->fetch_assoc()['total'];
$totalPages  = max(1, (int) ceil($total / $parPage));

$stmt = $conn->prepare("
    SELECT avis.film_id, avis.titre, avis.contenu, avis.created_at, utilisateurs.username
    FROM avis
    JOIN utilisateurs ON avis.utilisateur_id = utilisateurs.id
    ORDER BY avis.created_at DESC
    LIMIT ? OFFSET ?
");
$stmt->bind_param('ii', $parPage, $offset);
$stmt->execute();
$result = $stmt->get_result();

$avisListe = [];
while ($row = $result->fetch_assoc()) {
    $film = $tmdb->getMovie((int) $row['film_id']);
    $row['film_titre'] = $film['title'] ?? 'Film';
    $row['teinte']     = crc32($row['username']) % 360;
    $avisListe[]       = $row;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Tous les avis et critiques de films publiés par la communauté Cinévo, sans notes ni classement, triés du plus récent au plus ancien.">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>Tous les avis et critiques de films — Cinévo</title>
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="contenu">

    <div class="entete-page">
        <span class="label-section">La communauté écrit</span>
        <h1>Tous les avis</h1>
        <p class="intro">Les critiques de films publiées par la communauté Cinévo, du plus récent au plus ancien.</p>
    </div>

    <hr class="separateur">

    <div class="grille-avis">

        <?php if (empty($avisListe)): ?>
            <p class="intro">Aucun avis publié pour l'instant. <a href="ecrire.php">Soyez le premier à en écrire un</a> !</p>
        <?php endif; ?>

        <?php foreach ($avisListe as $avis): ?>
            <article class="carte-avis">
                <a href="fiche.php?id=<?= (int) $avis['film_id'] ?>" class="lien-carte">
                    <h3 class="avis-titre"><?= htmlspecialchars($avis['titre'] ?: $avis['film_titre']) ?></h3>
                    <p class="avis-texte"><?= htmlspecialchars(mb_substr($avis['contenu'], 0, 160)) ?><?= mb_strlen($avis['contenu']) > 160 ? '…' : '' ?></p>
                </a>
                <div class="avis-bas">
                    <span class="avatar" style="background: oklch(0.55 0.12 <?= $avis['teinte'] ?>);"><?= htmlspecialchars(mb_strtoupper(mb_substr($avis['username'], 0, 1))) ?></span>
                    <span class="avis-auteur"><?= htmlspecialchars($avis['username']) ?></span>
                    <span style="color: #8A8378;">sur</span>
                    <a href="fiche.php?id=<?= (int) $avis['film_id'] ?>" class="lien-film"><?= htmlspecialchars($avis['film_titre']) ?></a>
                    <span style="margin-left: auto;"><?= formaterDateFr($avis['created_at']) ?></span>
                </div>
            </article>
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
