<?php
$page = 'mes-avis';

require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/tmdb.php';
require_once 'includes/validation.php';

exigerConnexion();

$tmdb = new TMDB();

$id      = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$erreur  = '';
$avis    = null;

if (baseDisponible() && $id > 0) {
    $avis = trouverAvisDeLUtilisateur($conn, $id, $_SESSION['utilisateur_id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verifie_post()) {
        $erreur = ERREUR_CSRF;
    } elseif (!baseDisponible()) {
        $erreur = 'Modification impossible : la base de données ne répond pas.';
    } elseif (!$avis) {
        $erreur = 'Cet avis n\'existe pas, ou ne vous appartient pas.';
    } else {
        $titre   = trim($_POST['titre'] ?? '');
        $contenu = trim($_POST['avis'] ?? '');

        $titreInvalide   = erreurTitreAvis($titre);
        $contenuInvalide = erreurContenuAvis($contenu);

        if ($titreInvalide) {
            $erreur = $titreInvalide;
        } elseif ($contenuInvalide) {
            $erreur = $contenuInvalide;
        } else {
            modifierAvis($conn, $id, $_SESSION['utilisateur_id'], $titre, $contenu);

            header('Location: mes-avis.php?modifie=1');
            exit;
        }
    }
}

$filmTitre = '';
if ($avis) {
    $film = $tmdb->getMovie((int) $avis['film_id']);
    $filmTitre = $film['title'] ?? 'ce film';
}

$meta = [
    'titre'       => 'Cinévo · Modifier mon avis',
    'robots'      => 'noindex, nofollow',
];
include 'includes/debut-page.php';
?>

<main class="contenu">

    <div class="entete-page">
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

        <p class="intro">Cet avis n'existe pas, ou ne vous appartient pas.</p>
        <div class="hero-boutons">
            <a href="mes-avis.php" class="btn-rouge">Retour à mes avis</a>
        </div>

    <?php else: ?>

        <form class="formulaire-avis" action="modifier-avis.php?id=<?= (int) $avis['id'] ?>" method="POST">

            <?= csrf_champ() ?>

            <div class="champ">
                <label for="titre">Titre de votre avis</label>
                <input type="text" id="titre" name="titre" placeholder="Une phrase qui résume votre ressenti..."
                       maxlength="120" required
                       value="<?= htmlspecialchars($_POST['titre'] ?? $avis['titre']) ?>">
            </div>

            <div class="champ">
                <label for="avis">Votre avis <span class="minimum">20 caractères minimum</span></label>
                <textarea id="avis" name="avis" rows="8" required minlength="<?= AVIS_LONGUEUR_MIN ?>"><?= htmlspecialchars($_POST['avis'] ?? $avis['contenu']) ?></textarea>
                <div class="compteur"><span id="compteur">0</span> caractères</div>
            </div>

            <div class="boutons-form">
                <a href="mes-avis.php" class="btn-blanc">Annuler</a>
                <button type="submit" class="btn-rouge" id="btnPublier">Enregistrer</button>
            </div>

        </form>

    <?php endif; ?>

</main>

<?php include 'includes/fin-page.php'; ?>
