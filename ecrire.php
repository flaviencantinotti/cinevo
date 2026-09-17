<?php
$page = 'ecrire';
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/tmdb.php';
require_once 'includes/format.php';
$tmdb = new TMDB();

$resultats       = [];
$erreur          = '';
$aucunResultat   = false;
$filmSelectionne = null;

$idSelectionne = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($idSelectionne > 0) {
    $filmSelectionne = $tmdb->getMovie($idSelectionne);
}

if (!empty($_GET['q'])) {
    $data = $tmdb->searchMovie(trim($_GET['q']));

    if ($data && !empty($data['results'])) {
        $films = $data['results'];
        $films = array_filter($films, function ($f) {
            return !empty($f['title']) && !empty($f['poster_path']);
        });
        usort($films, fn($a, $b) => ($b['popularity'] ?? 0) <=> ($a['popularity'] ?? 0));
        $resultats = array_slice($films, 0, 5);
    } else {
        // Ce n'est pas une erreur du formulaire : le message va avec les
        // résultats, pas dans l'encadré rouge des erreurs de publication.
        $aucunResultat = true;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!estConnecte()) {
        header('Location: connexion.php');
        exit;
    }

    if (!csrf_verifie($_POST['csrf_token'] ?? null)) {
        $erreur = 'Requête invalide, merci de réessayer.';
    } else {
        $film_id = (int) ($_POST['film_id'] ?? 0);
        $titre   = trim($_POST['titre'] ?? '');
        $contenu = trim($_POST['avis'] ?? '');

        if (!baseDisponible()) {
            $erreur = 'Publication impossible : la base de données ne répond pas. '
                . 'Votre texte est conservé ci-dessous, réessayez dans un instant.';
        } elseif ($titre === '') {
            $erreur = 'Donnez un titre à votre avis.';
        } elseif (mb_strlen($titre) > 120) {
            $erreur = 'Le titre ne doit pas dépasser 120 caractères.';
        } elseif ($film_id > 0 && mb_strlen($contenu) >= 20) {
            $stmt = $conn->prepare("INSERT INTO avis (utilisateur_id, film_id, titre, contenu) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('iiss', $_SESSION['utilisateur_id'], $film_id, $titre, $contenu);
            $stmt->execute();
            header('Location: fiche.php?id=' . $film_id);
            exit;
        } else {
            $erreur = 'Avis trop court ou film manquant.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" type="text/css" href="css/style.css?v=88">
    <title>Cinévo · Écrire un avis</title>
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="contenu">

    <div class="entete-page">
        <h1>Écrire un avis</h1>
        <p class="intro">Pas de note. Pas de format imposé. Juste ce que le film vous a fait.</p>
    </div>

    <hr class="separateur">

    <?php if ($erreur): ?>
        <p class="message-erreur"><?= htmlspecialchars($erreur) ?></p>
    <?php endif; ?>

    <form method="GET" action="ecrire.php" class="formulaire-recherche">
        <input type="text" name="q" placeholder="Chercher un film par titre..."
               value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
        <button type="submit" class="btn-rouge">Chercher</button>
    </form>

    <?php if ($filmSelectionne): ?>
        <div class="resultat-film film-selectionne">
            <?php if (!empty($filmSelectionne['poster_path'])): ?>
                <img src="<?= htmlspecialchars($tmdb->getPosterUrl($filmSelectionne['poster_path'], 'w92')) ?>"
                     alt="Affiche de <?= htmlspecialchars($filmSelectionne['title']) ?>"
                     class="affiche-mini">
            <?php endif; ?>
            <div class="info-film">
                <div class="titre-resultat"><?= htmlspecialchars($filmSelectionne['title']) ?></div>
                <div class="meta-resultat"><?= htmlspecialchars(substr($filmSelectionne['release_date'] ?? '', 0, 4)) ?></div>
            </div>
            <a href="ecrire.php" class="btn-transparent">Changer de film</a>
        </div>

        <hr class="separateur">

    <?php elseif ($idSelectionne > 0): ?>
        <?php // Un identifiant est bien passé dans l'URL, mais le catalogue n'a
              // pas répondu : sans ce message, le formulaire s'ouvrait sur un
              // film invisible et l'avis partait dans le vide. ?>
        <p class="intro">
            Ce film n'a pas pu être chargé pour le moment.
            <a href="ecrire.php">Cherchez-le à nouveau</a> avant d'écrire votre avis.
        </p>

        <hr class="separateur">
    <?php endif; ?>

    <?php if ($aucunResultat): ?>
        <p class="intro">Aucun film ne correspond à « <?= htmlspecialchars($_GET['q']) ?> ». Vérifiez l'orthographe.</p>

        <hr class="separateur">
    <?php endif; ?>

    <?php if (!empty($resultats)): ?>

        <?php $lienFilm = 'ecrire.php'; include 'includes/resultats-films.php'; ?>

        <hr class="separateur">
    <?php endif; ?>

    <?php if (!estConnecte()): ?>

        <section class="cta-decouvrir">
            <h2>Connectez-vous pour publier votre avis</h2>
            <p>
                La rédaction d'un avis est réservée aux membres, pour que chaque avis reste rattaché
                à une vraie personne. Créer un compte est gratuit et prend trente secondes.
            </p>
            <div class="hero-boutons" style="margin-top:20px;">
                <a href="inscription.php" class="btn-rouge">Rejoindre Cinévo</a>
                <a href="connexion.php" class="btn-blanc">Déjà inscrit ? Connexion</a>
            </div>
        </section>

    <?php else: ?>

    <form class="formulaire-avis" action="ecrire.php" method="POST">

        <?= csrf_champ() ?>
        <input type="hidden" name="film_id" value="<?= $idSelectionne ?: '' ?>">

        <div class="champ">
            <label for="titre">Titre de votre avis</label>
            <input type="text" id="titre" name="titre" placeholder="Une phrase qui résume votre ressenti..."
                   maxlength="120" required
                   value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>">
        </div>

        <div class="champ">
            <label for="avis">Votre avis <span class="minimum">20 caractères minimum</span></label>
            <textarea id="avis" name="avis" placeholder="Écrivez librement ce que ce film vous a fait..."
                      rows="8" required minlength="20"><?= htmlspecialchars($_POST['avis'] ?? '') ?></textarea>
            <div class="compteur"><span id="compteur">0</span> caractères</div>
        </div>

        <div class="boutons-form">
            <a href="<?= $idSelectionne ? 'fiche.php?id=' . $idSelectionne : 'index.php' ?>" class="btn-blanc">Annuler</a>
            <button type="submit" class="btn-rouge" id="btnPublier">Publier l'avis</button>
        </div>

    </form>

    <?php endif; ?>

</main>

<?php include 'includes/footer.php'; ?>

<script src="js/scripts.js"></script>
</body>
</html>
