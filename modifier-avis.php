<?php
$page = 'mes-avis';

require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/tmdb.php';

if (!estConnecte()) {
    header('Location: connexion.php');
    exit;
}

$tmdb = new TMDB();

$id      = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$erreur  = '';
$avis    = null;

// On récupère l'avis en vérifiant qu'il appartient bien à l'utilisateur
// connecté : sans cette condition, n'importe qui pourrait modifier
// l'avis d'un autre en changeant l'identifiant dans l'URL.
if (baseDisponible() && $id > 0) {
    $requete = $conn->prepare("
        SELECT id, film_id, titre, contenu
        FROM avis
        WHERE id = ? AND utilisateur_id = ?
    ");
    $requete->bind_param('ii', $id, $_SESSION['utilisateur_id']);
    $requete->execute();
    $avis = $requete->get_result()->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verifie($_POST['csrf_token'] ?? null)) {
        $erreur = 'Requête invalide, merci de réessayer.';
    } elseif (!baseDisponible()) {
        $erreur = 'Modification impossible : la base de données ne répond pas.';
    } elseif (!$avis) {
        $erreur = 'Cet avis n\'existe pas, ou ne vous appartient pas.';
    } else {
        $titre   = trim($_POST['titre'] ?? '');
        $contenu = trim($_POST['avis'] ?? '');

        if (strlen($contenu) < 20) {
            $erreur = 'Votre avis doit faire au moins 20 caractères.';
        } else {
            $requete = $conn->prepare("
                UPDATE avis
                SET titre = ?, contenu = ?
                WHERE id = ? AND utilisateur_id = ?
            ");
            $requete->bind_param('ssii', $titre, $contenu, $id, $_SESSION['utilisateur_id']);
            $requete->execute();

            header('Location: mes-avis.php?modifie=1');
            exit;
        }
    }
}

// Titre du film, pour rappeler de quoi parle l'avis.
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
    <link rel="stylesheet" type="text/css" href="css/style.css?v=2">
    <title>Cinévo — Modifier mon avis</title>
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="contenu">

    <div class="entete-page">
        <span class="label-section">Votre espace</span>
        <h1>Modifier mon avis</h1>
        <?php if ($avis): ?>
            <p class="intro">Votre avis sur <?= htmlspecialchars($filmTitre) ?>.</p>
        <?php endif; ?>
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

        <form class="formulaire-avis" action="modifier-avis.php?id=<?= (int) $avis['id'] ?>" method="POST">

            <?= csrf_champ() ?>

            <div class="champ">
                <label for="titre">Titre de votre avis <span class="facultatif">(facultatif)</span></label>
                <input type="text" id="titre" name="titre" placeholder="Une phrase qui résume votre ressenti..."
                       value="<?= htmlspecialchars($_POST['titre'] ?? $avis['titre']) ?>">
            </div>

            <div class="champ">
                <label for="avis">Votre avis <span class="minimum">20 caractères minimum</span></label>
                <textarea id="avis" name="avis" rows="8" required minlength="20"><?= htmlspecialchars($_POST['avis'] ?? $avis['contenu']) ?></textarea>
                <div class="compteur"><span id="compteur">0</span> caractères</div>
            </div>

            <div class="boutons-form">
                <a href="mes-avis.php">
                    <button type="button" class="btn-blanc">Annuler</button>
                </a>
                <button type="submit" class="btn-rouge" id="btnPublier">Enregistrer</button>
            </div>

        </form>

    <?php endif; ?>

</main>

<?php include 'includes/footer.php'; ?>

<script src="js/scripts.js"></script>
</body>
</html>
