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

$id     = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$erreur = '';
$avis   = null;

// Même vérification que pour la modification : on ne récupère l'avis que
// s'il appartient à l'utilisateur connecté.
if (baseDisponible() && $id > 0) {
    $requete = $conn->prepare("
        SELECT id, film_id, titre, contenu, created_at
        FROM avis
        WHERE id = ? AND utilisateur_id = ?
    ");
    $requete->bind_param('ii', $id, $_SESSION['utilisateur_id']);
    $requete->execute();
    $avis = $requete->get_result()->fetch_assoc();
}

// La suppression se fait uniquement en POST : un simple lien visité par
// erreur (ou par un robot) ne doit jamais effacer quoi que ce soit.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verifie($_POST['csrf_token'] ?? null)) {
        $erreur = 'Requête invalide, merci de réessayer.';
    } elseif (!baseDisponible()) {
        $erreur = 'Suppression impossible : la base de données ne répond pas.';
    } elseif (!$avis) {
        $erreur = 'Cet avis n\'existe pas, ou ne vous appartient pas.';
    } else {
        $requete = $conn->prepare("DELETE FROM avis WHERE id = ? AND utilisateur_id = ?");
        $requete->bind_param('ii', $id, $_SESSION['utilisateur_id']);
        $requete->execute();

        header('Location: mes-avis.php?supprime=1');
        exit;
    }
}

$filmTitre = '';
if ($avis) {
    $film = $tmdb->getMovie((int) $avis['film_id']);
    $filmTitre = $film['title'] ?? 'ce film';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>Cinévo — Supprimer mon avis</title>
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="contenu">

    <div class="entete-page">
        <span class="label-section">Votre espace</span>
        <h1>Supprimer mon avis</h1>
    </div>

    <hr class="separateur">

    <?php if ($erreur): ?>
        <p class="message-erreur"><?= htmlspecialchars($erreur) ?></p>
    <?php endif; ?>

    <?php if (!$avis): ?>

        <p class="intro">Cet avis est introuvable, ou il ne vous appartient pas.</p>
        <div class="hero-boutons" style="margin-top:20px;">
            <a href="mes-avis.php"><button class="btn-rouge">Retour à mes avis</button></a>
        </div>

    <?php else: ?>

        <p class="intro">
            Cette suppression est définitive. Votre avis sur
            <?= htmlspecialchars($filmTitre) ?> ne pourra pas être récupéré.
        </p>

        <article class="carte-avis" style="margin-top:20px;">
            <?php if ($avis['titre']): ?>
                <h2 class="avis-titre"><?= htmlspecialchars($avis['titre']) ?></h2>
            <?php endif; ?>
            <p class="avis-texte"><?= htmlspecialchars(extrait($avis['contenu'], 300)) ?></p>
            <div class="avis-bas">
                <span style="margin-left:auto;"><?= formaterDateFr($avis['created_at']) ?></span>
            </div>
        </article>

        <form action="supprimer-avis.php?id=<?= (int) $avis['id'] ?>" method="POST">
            <?= csrf_champ() ?>
            <div class="boutons-form">
                <a href="mes-avis.php">
                    <button type="button" class="btn-blanc">Annuler</button>
                </a>
                <button type="submit" class="btn-rouge">Supprimer définitivement</button>
            </div>
        </form>

    <?php endif; ?>

</main>

<?php include 'includes/footer.php'; ?>

<script src="js/scripts.js"></script>
</body>
</html>
