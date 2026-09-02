<?php
$page = 'mes-avis';

require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/tmdb.php';
require_once 'includes/format.php';

if (!estConnecte()) {
    header('Location: connexion.php');
    exit;
}

$tmdb = new TMDB();

// Message transmis après une modification ou une suppression réussie.
$confirmation = '';
if (isset($_GET['modifie']))  $confirmation = 'Votre avis a été modifié.';
if (isset($_GET['supprime'])) $confirmation = 'Votre avis a été supprimé.';

$mesAvis = [];

if (baseDisponible()) {
    $stmt = $conn->prepare("
        SELECT id, film_id, titre, contenu, created_at
        FROM avis
        WHERE utilisateur_id = ?
        ORDER BY created_at DESC
    ");
    $stmt->bind_param('i', $_SESSION['utilisateur_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $film = $tmdb->getMovie((int) $row['film_id']);
        $row['film_titre'] = $film['title'] ?? 'Film';
        $mesAvis[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>Cinévo — Mes avis</title>
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="contenu">

    <div class="entete-page">
        <span class="label-section">Votre espace</span>
        <h1>Mes avis</h1>
        <p class="intro">
            <?= count($mesAvis) > 0
                ? 'Retrouvez ici tout ce que vous avez publié, pour le modifier ou le supprimer.'
                : 'Vous n\'avez encore rien publié.' ?>
        </p>
    </div>

    <?php if ($confirmation): ?>
        <p class="message-succes"><?= htmlspecialchars($confirmation) ?></p>
    <?php endif; ?>

    <hr class="separateur">

    <div class="grille-avis">

        <?php if (!baseDisponible()): ?>
            <?= messageBaseIndisponible('La liste de vos avis') ?>
        <?php elseif (empty($mesAvis)): ?>
            <p class="intro"><a href="ecrire.php">Écrivez votre premier avis</a> sur un film que vous avez vu.</p>
        <?php endif; ?>

        <?php foreach ($mesAvis as $avis): ?>
            <article class="carte-avis">
                <a href="fiche.php?id=<?= (int) $avis['film_id'] ?>" class="lien-carte">
                    <h2 class="avis-titre"><?= htmlspecialchars($avis['titre'] ?: $avis['film_titre']) ?></h2>
                    <p class="avis-texte"><?= htmlspecialchars(extrait($avis['contenu'])) ?></p>
                </a>

                <div class="avis-bas">
                    <span style="color:#8A8378;">sur</span>
                    <a href="fiche.php?id=<?= (int) $avis['film_id'] ?>" class="lien-film"><?= htmlspecialchars($avis['film_titre']) ?></a>
                    <span style="margin-left:auto;"><?= formaterDateFr($avis['created_at']) ?></span>
                </div>

                <div class="actions-avis">
                    <a href="modifier-avis.php?id=<?= (int) $avis['id'] ?>">
                        <button class="btn-blanc">Modifier</button>
                    </a>
                    <a href="supprimer-avis.php?id=<?= (int) $avis['id'] ?>">
                        <button class="btn-transparent btn-danger">Supprimer</button>
                    </a>
                </div>
            </article>
        <?php endforeach; ?>

    </div>

    <hr class="separateur">

</main>

<?php include 'includes/footer.php'; ?>

<script src="js/scripts.js"></script>
</body>
</html>
