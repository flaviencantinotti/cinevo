<?php
$page = 'ecrire';
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/tmdb.php';
$tmdb = new TMDB();

$resultats       = [];
$erreur          = '';
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
        usort($films, function ($a, $b) {
            return $b['popularity'] > $a['popularity'] ? 1 : -1;
        });
        $resultats = array_slice($films, 0, 5);
    } else {
        $erreur = 'Aucun film trouvé pour "' . htmlspecialchars($_GET['q']) . '".';
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

        if ($film_id > 0 && strlen($contenu) >= 20) {
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
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>Cinévo — Écrire un avis</title>
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="contenu">

    <div class="entete-page">
        <span class="label-section">Partager votre ressenti</span>
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
                <img src="<?= $tmdb->getPosterUrl($filmSelectionne['poster_path'], 'w92') ?>"
                     alt="Affiche de <?= htmlspecialchars($filmSelectionne['title']) ?>"
                     class="affiche-mini">
            <?php endif; ?>
            <div class="info-film">
                <div class="label-section">Film sélectionné</div>
                <div class="titre-resultat"><?= htmlspecialchars($filmSelectionne['title']) ?></div>
                <div class="meta-resultat"><?= substr($filmSelectionne['release_date'] ?? '', 0, 4) ?></div>
            </div>
            <a href="ecrire.php" class="btn-transparent">Changer de film</a>
        </div>

        <hr class="separateur">
    <?php endif; ?>

    <?php if (!empty($resultats)): ?>
        <div class="liste-resultats">
            <p class="label-section" style="margin-bottom:8px;">
                <?= count($resultats) ?> résultats pour "<?= htmlspecialchars($_GET['q']) ?>"
            </p>

            <?php foreach ($resultats as $film):
                $annee   = substr($film['release_date'] ?? '', 0, 4);
                $affiche = $tmdb->getPosterUrl($film['poster_path'], 'w92');
            ?>
                <a href="ecrire.php?id=<?= $film['id'] ?>" class="resultat-film">
                    <img src="<?= $affiche ?>" alt="Affiche de <?= htmlspecialchars($film['title']) ?>"
                         class="affiche-mini" loading="lazy">
                    <div class="info-film">
                        <div class="titre-resultat"><?= htmlspecialchars($film['title']) ?></div>
                        <div class="meta-resultat">
                            <?= $annee ?>
                            <?php if (!empty($film['original_title']) && $film['original_title'] !== $film['title']): ?>
                                · <em><?= htmlspecialchars($film['original_title']) ?></em>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($film['overview'])): ?>
                            <div class="apercu">
                                <?= htmlspecialchars(mb_substr($film['overview'], 0, 100)) ?>…
                            </div>
                        <?php endif; ?>
                    </div>
                    <svg class="fleche" width="16" height="16" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14M13 6l6 6-6 6"></path>
                    </svg>
                </a>
            <?php endforeach; ?>
        </div>

        <hr class="separateur">
    <?php endif; ?>

    <form class="formulaire-avis" action="ecrire.php" method="POST">

        <?= csrf_champ() ?>
        <input type="hidden" name="film_id" value="<?= $idSelectionne ?: '' ?>">

        <div class="champ">
            <label for="titre">Titre de votre avis <span class="facultatif">(facultatif)</span></label>
            <input type="text" id="titre" name="titre" placeholder="Une phrase qui résume votre ressenti...">
        </div>

        <div class="champ">
            <label for="avis">Votre avis <span class="minimum">20 caractères minimum</span></label>
            <textarea id="avis" name="avis" placeholder="Écrivez librement ce que ce film vous a fait..."
                      rows="8" required minlength="20"></textarea>
            <div class="compteur"><span id="compteur">0</span> caractères</div>
        </div>

        <div class="boutons-form">
            <a href="fiche.php?id=<?= $idSelectionne ?: '' ?>">
                <button type="button" class="btn-blanc">Annuler</button>
            </a>
            <button type="submit" class="btn-rouge" id="btnPublier">Publier l'avis</button>
        </div>

    </form>

</main>

<?php include 'includes/footer.php'; ?>

<script src="js/scripts.js"></script>
</body>
</html>
