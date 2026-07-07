<?php
$page = 'home';
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/tmdb.php';

if (!estConnecte()) {
    header('Location: connexion.php');
    exit;
}

$tmdb = new TMDB();

function formaterDateFr(string $datetime): string {
    static $mois = [1 => 'janvier', 2 => 'février', 3 => 'mars', 4 => 'avril', 5 => 'mai', 6 => 'juin',
                    7 => 'juillet', 8 => 'août', 9 => 'septembre', 10 => 'octobre', 11 => 'novembre', 12 => 'décembre'];
    $d = new DateTime($datetime);
    return (int) $d->format('j') . ' ' . $mois[(int) $d->format('n')] . ' ' . $d->format('Y');
}

$avisRecents = [];
$result = $conn->query("
    SELECT avis.film_id, avis.titre, avis.contenu, avis.created_at, utilisateurs.username
    FROM avis
    JOIN utilisateurs ON avis.utilisateur_id = utilisateurs.id
    ORDER BY avis.created_at DESC
    LIMIT 10
");

while ($row = $result->fetch_assoc()) {
    $film = $tmdb->getMovie((int) $row['film_id']);
    $row['film_titre'] = $film['title'] ?? 'Film';
    $row['teinte']     = crc32($row['username']) % 360;
    $avisRecents[]     = $row;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>Cinévo — Mon fil</title>
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="contenu">

    <div class="message-accueil">
        <h1>Bonsoir, <?= htmlspecialchars($_SESSION['username']) ?>.</h1>
        <p>Bienvenue sur votre fil d'avis.</p>
    </div>

    <div class="page-home">
        <div class="colonne-principale">
            <div class="liste-avis">

                <?php if (empty($avisRecents)): ?>
                    <p class="intro">Aucun avis publié pour l'instant. <a href="ecrire.php">Soyez le premier à en écrire un</a> !</p>
                <?php endif; ?>

                <?php foreach ($avisRecents as $avis): ?>
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
        </div>

        <aside class="colonne-lateral">
            <div class="encart">
                <h4>Le parti pris</h4>
                <p>Pas de note, pas de classement, pas d'algorithme. Nous valorisons l'humain et ces émotions.</p>
            </div>
            <div class="encart">
                <h4>Écrire un avis</h4>
                <p>Un film vous a marqué ? Partagez ce qu'il vous a fait.</p>
                <a href="ecrire.php" style="display:inline-block; margin-top:12px;">
                    <button class="btn-rouge" style="width:100%;">Commencer à écrire</button>
                </a>
            </div>
        </aside>
    </div>

    <hr class="separateur">

</main>

<?php include 'includes/footer.php'; ?>

<script src="js/scripts.js"></script>
</body>
</html>
