<?php
$page = 'home';
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/tmdb.php';
require_once 'includes/format.php';

if (!estConnecte()) {
    header('Location: connexion.php');
    exit;
}

$tmdb = new TMDB();

$avisRecents = [];
$result = baseDisponible() ? $conn->query("
    SELECT avis.film_id, avis.titre, avis.contenu, avis.publie_le, utilisateurs.nom_utilisateur, utilisateurs.photo_profil
    FROM avis
    JOIN utilisateurs ON avis.utilisateur_id = utilisateurs.id
    ORDER BY avis.publie_le DESC
    LIMIT 10
") : null;

while ($result && $row = $result->fetch_assoc()) {
    $film = $tmdb->getMovie((int) $row['film_id']);
    $row['film_titre'] = $film['title'] ?? 'Film';
    $row['affiche']    = $tmdb->getPosterUrl($film['poster_path'] ?? null);
    $avisRecents[]     = $row;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" type="text/css" href="css/style.css?v=35">
    <title>Cinévo · Mon fil</title>
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="contenu">

    <div class="message-accueil">
        <h1>Bonjour, <?= htmlspecialchars($_SESSION['nom_utilisateur']) ?>.</h1>
        <p>Les derniers avis publiés par la communauté Cinévo.</p>
    </div>

    <div class="page-home">
        <div class="colonne-principale">
            <div class="liste-avis">

                <?php if (!baseDisponible()): ?>
                    <?= messageBaseIndisponible('L\'affichage de votre fil') ?>
                <?php elseif (empty($avisRecents)): ?>
                    <p class="intro">Aucun avis publié pour l'instant. <a href="ecrire.php">Soyez le premier à en écrire un</a> !</p>
                <?php endif; ?>

                <?php foreach ($avisRecents as $avis): ?>
                    <article class="carte-avis carte-avis-avec-affiche">
                        <a href="fiche.php?id=<?= (int) $avis['film_id'] ?>" class="lien-carte">
                            <div class="affiche-fil">
                                <img src="<?= htmlspecialchars($avis['affiche']) ?>" alt="Affiche de <?= htmlspecialchars($avis['film_titre']) ?>" loading="lazy">
                            </div>
                            <div class="corps-fil">
                                <h3 class="avis-titre"><?= htmlspecialchars($avis['titre'] ?: $avis['film_titre']) ?></h3>
                                <p class="avis-texte"><?= htmlspecialchars(extrait($avis['contenu'], 160)) ?></p>
                            </div>
                        </a>
                        <div class="avis-bas">
                            <?= avatarHtml($avis['nom_utilisateur'], $avis['photo_profil']) ?>
                            <span class="avis-auteur"><?= htmlspecialchars($avis['nom_utilisateur']) ?></span>
                            <span style="color: #8A8378;">sur</span>
                            <a href="fiche.php?id=<?= (int) $avis['film_id'] ?>" class="lien-film"><?= htmlspecialchars($avis['film_titre']) ?></a>
                            <span style="margin-left: auto;"><?= formaterDateFr($avis['publie_le']) ?></span>
                        </div>
                    </article>
                <?php endforeach; ?>

            </div>
            <a href="avis.php">
                <button class="btn-blanc" style="margin-top: 20px;">Voir plus d'avis.</button>
            </a>
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

</main>

<?php include 'includes/footer.php'; ?>

<script src="js/scripts.js"></script>
</body>
</html>
