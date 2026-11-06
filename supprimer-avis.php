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

if (baseDisponible() && $id > 0) {
    $avis = trouverAvisDeLUtilisateur($conn, $id, $_SESSION['utilisateur_id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verifie($_POST['csrf_token'] ?? null)) {
        $erreur = 'Requête invalide, merci de réessayer.';
    } elseif (!baseDisponible()) {
        $erreur = 'Suppression impossible : la base de données ne répond pas.';
    } elseif (!$avis) {
        $erreur = 'Cet avis n\'existe pas, ou ne vous appartient pas.';
    } else {
        supprimerAvis($conn, $id, $_SESSION['utilisateur_id']);

        header('Location: mes-avis.php?supprime=1');
        exit;
    }
}

$filmTitre = '';
if ($avis) {
    $film = $tmdb->getMovie((int) $avis['film_id']);
    $filmTitre = $film['title'] ?? 'ce film';
}

$meta = [
    'titre'       => 'Cinévo · Supprimer mon avis',
    'robots'      => 'noindex, nofollow',
];
include 'includes/debut-page.php';
?>

<main class="contenu">

    <div class="entete-page">
        <h1>Supprimer mon avis</h1>
    </div>

    <hr class="separateur">

    <?php if ($erreur): ?>
        <p class="message-erreur"><?= htmlspecialchars($erreur) ?></p>
    <?php endif; ?>

    <?php if (!$avis): ?>

        <p class="intro">Cet avis n'existe pas, ou ne vous appartient pas.</p>
        <div class="hero-boutons">
            <a href="mes-avis.php" class="btn-rouge">Retour à mes avis</a>
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
                <span class="avis-date"><?= formaterDateFr($avis['publie_le']) ?></span>
            </div>
        </article>

        <form action="supprimer-avis.php?id=<?= (int) $avis['id'] ?>" method="POST">
            <?= csrf_champ() ?>
            <div class="boutons-form">
                <a href="mes-avis.php" class="btn-blanc">Annuler</a>
                <button type="submit" class="btn-rouge">Supprimer définitivement</button>
            </div>
        </form>

    <?php endif; ?>

</main>

<?php include 'includes/fin-page.php'; ?>
