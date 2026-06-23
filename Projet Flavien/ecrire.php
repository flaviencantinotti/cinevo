<?php
$page = 'ecrire';

require_once 'includes/tmdb.php';
$tmdb = new TMDB();

$resultats = [];
$erreur    = '';

if (!empty($_GET['q'])) {
    $data = $tmdb->searchMovie(trim($_GET['q']));

    if ($data && !empty($data['results'])) {
        $films = $data['results'];

        // Garde seulement les films avec un titre ET une affiche
        $films = array_filter($films, fn($f) => !empty($f['title']) && !empty($f['poster_path']));

        // Tri par popularité décroissante (le plus connu en premier)
        usort($films, fn($a, $b) => $b['popularity'] <=> $a['popularity']);

        // On garde les 5 premiers
        $resultats = array_slice($films, 0, 5);

    } else {
        $erreur = 'Aucun film trouvé pour "' . htmlspecialchars($_GET['q']) . '".';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>Cinévo — Écrire un avis</title>
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="contenu">

    <div class="ecrire-header">
        <span class="bibliotheque">Partager votre ressenti</span>
        <h1>Écrire un avis</h1>
        <p class="text-lede">Pas de note. Pas de format imposé. Juste ce que le film vous a fait.</p>
    </div>

    <hr class="diviseur">

    <!-- Étape 1 : Chercher un film -->
    <form method="GET" action="ecrire.php" class="search-form">
        <input
            type="text"
            name="q"
            placeholder="Chercher un film par titre..."
            value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
            class="search-input"
        >
        <button type="submit" class="terra">Chercher</button>
    </form>

    <!-- Résultats de recherche -->
    <?php if ($erreur): ?>
        <p class="search-erreur"><?= $erreur ?></p>

    <?php elseif (!empty($resultats)): ?>
        <div class="search-resultats">
            <p class="bibliotheque" style="margin-bottom:8px;"><?= count($resultats) ?> résultats pour "<?= htmlspecialchars($_GET['q']) ?>"</p>

            <?php foreach ($resultats as $film):
                $annee   = substr($film['release_date'] ?? '', 0, 4);
                $affiche = $tmdb->getPosterUrl($film['poster_path'], 'w92');
            ?>
                <a href="fiche.php?id=<?= $film['id'] ?>" class="search-item">
                    <img
                        src="<?= $affiche ?>"
                        alt="Affiche de <?= htmlspecialchars($film['title']) ?>"
                        class="search-poster"
                        loading="lazy"
                    >
                    <div class="search-info">
                        <div class="search-titre"><?= htmlspecialchars($film['title']) ?></div>
                        <div class="search-meta">
                            <?= $annee ?>
                            <?php if (!empty($film['original_title']) && $film['original_title'] !== $film['title']): ?>
                                · <em><?= htmlspecialchars($film['original_title']) ?></em>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($film['overview'])): ?>
                            <div class="search-apercu"><?= htmlspecialchars(mb_substr($film['overview'], 0, 100)) ?>…</div>
                        <?php endif; ?>
                    </div>
                    <svg class="search-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14M13 6l6 6-6 6"></path>
                    </svg>
                </a>
            <?php endforeach; ?>
        </div>

        <hr class="diviseur">
    <?php endif; ?>

    <!-- Étape 2 : Écrire l'avis -->
    <form class="ecrire-form" action="home.php" method="post">

        <input type="hidden" name="film_id" value="<?= htmlspecialchars($_GET['id'] ?? '') ?>">

        <div class="ecrire-champ">
            <label for="titre">Titre de votre avis <span class="champ-opt">(facultatif)</span></label>
            <input type="text" id="titre" name="titre" placeholder="Une phrase qui résume votre ressenti...">
        </div>

        <div class="ecrire-champ">
            <label for="avis">Votre avis <span class="champ-min">20 caractères minimum</span></label>
            <textarea id="avis" name="avis" placeholder="Écrivez librement ce que ce film vous a fait..." rows="8" required minlength="20"></textarea>
            <div class="ecrire-compteur"><span id="compteur">0</span> caractères</div>
        </div>

        <div class="ecrire-actions">
            <a href="fiche.php">
                <button type="button" class="rejoindre">Annuler</button>
            </a>
            <button type="submit" class="terra" id="btnPublier">Publier l'avis</button>
        </div>

    </form>

</main>

<?php include 'includes/footer.php'; ?>

<script src="js/scripts.js"></script>
</body>
</html>
