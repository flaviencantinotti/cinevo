<?php
$page = 'index';
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/tmdb.php';
require_once 'includes/format.php';
$tmdb = new TMDB();

$avisRecents = [];

if (baseDisponible()) {
    $result = $conn->query("
        SELECT avis.film_id, avis.titre, avis.contenu, avis.publie_le, utilisateurs.nom_utilisateur
        FROM avis
        JOIN utilisateurs ON avis.utilisateur_id = utilisateurs.id
        ORDER BY avis.publie_le DESC
        LIMIT 3
    ");

    while ($result && $row = $result->fetch_assoc()) {
        $film = $tmdb->getMovie((int) $row['film_id']);
        $row['film_titre'] = $film['title'] ?? 'Film';
        $row['affiche']    = $tmdb->getPosterUrl($film['poster_path'] ?? null);
        $avisRecents[]     = $row;
    }
}

// L'affiche du hero est celle du dernier avis publié : le seul élément en
// couleur de la page, et un point d'entrée vers la discussion la plus récente.
$filmHero = $avisRecents[0] ?? null;

// Petite pile d'affiches, purement décorative, pour illustrer l'appel au
// tirage aléatoire sans dépendre des avis déjà publiés.
$filmsPioche = $tmdb->getRandomMovies(3);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Cinévo, le site d'avis et critiques de films sans notes ni étoiles. Une communauté de cinéphiles qui écrit ce qu'elle a vu et ressenti, sans algorithme.">
    <link rel="stylesheet" type="text/css" href="css/style.css?v=89">
    <title>Cinévo · Le cinéma se lit aussi</title>
</head>

<body>

    <?php include 'includes/header.php'; ?>

    <main class="contenu">

        <section class="hero hero-accueil">
            <div class="hero-grille">

                <div class="hero-contenu">
                    <h1>Parlons ciné.</h1>
                    <div class="hero-boutons">
                        <a href="decouvrir.php" class="bouton-principal">Découvrir</a>
                    </div>
                </div>

                <?php if ($filmHero): ?>
                    <a href="fiche.php?id=<?= (int) $filmHero['film_id'] ?>" class="hero-affiche">
                        <img src="<?= htmlspecialchars($filmHero['affiche']) ?>" alt="Affiche de <?= htmlspecialchars($filmHero['film_titre']) ?>">
                        <span class="legende">
                            <span><?= htmlspecialchars($filmHero['film_titre']) ?></span>
                            <span>Dernier avis</span>
                        </span>
                    </a>
                <?php endif; ?>

            </div>
        </section>

        <section>
            <div class="concept-layout">
                <div class="concept-texte">
                    <h2>Pas de<br>note.</h2>
                    <p>
                        Ici, un film ne se résume jamais à trois étoiles et demie. On écrit ce
                        qu'il nous a fait, en deux phrases ou en dix paragraphes, et chaque film
                        commenté ouvre une discussion que tout le monde peut lire.
                    </p>
                </div>
                <?php // La colonne de droite est une accroche en capitales (.concept-cta p) :
                      // elle doit rester courte, l'explication tient dans la colonne de gauche. ?>
                <div class="concept-cta">
                    <p>Le site en quatre étapes.</p>
                    <a href="decouvrir.php" class="lien-fleche">Comment ça marche</a>
                </div>
            </div>
        </section>

        <section>
            <div class="avis-entete">
                <h2>À lire.</h2>
                <a href="avis.php" class="lien-fleche">Tous les avis</a>
            </div>

            <div class="grille-avis grille-avis-accueil">

                <?php if (!baseDisponible()): ?>
                    <?= messageBaseIndisponible('L\'affichage des avis') ?>
                <?php elseif (empty($avisRecents)): ?>
                    <p class="intro">Aucun avis publié pour l'instant. <a href="ecrire.php">Soyez le premier à en écrire un</a> !</p>
                <?php endif; ?>

                <?php foreach ($avisRecents as $avis): ?>
                    <a href="fiche.php?id=<?= (int) $avis['film_id'] ?>" class="carte-avis-accueil">
                        <div class="affiche">
                            <img src="<?= htmlspecialchars($avis['affiche']) ?>" alt="Affiche de <?= htmlspecialchars($avis['film_titre']) ?>" loading="lazy">
                        </div>
                        <div class="corps">
                            <?php // Petite ligne : le film. Grande ligne : le titre de l'avis,
                                  // obligatoire depuis ecrire.php. ?>
                            <div class="film"><?= htmlspecialchars($avis['film_titre']) ?></div>
                            <h3><?= htmlspecialchars($avis['titre']) ?></h3>
                            <p class="avis-texte"><?= htmlspecialchars(extrait($avis['contenu'], 140)) ?></p>
                            <div class="qui">
                                <?= htmlspecialchars($avis['nom_utilisateur']) ?> — <?= formaterDateFr($avis['publie_le']) ?>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>

            </div>
        </section>

        <section class="appel-action">
            <div class="hasard-encart">
                <div class="hasard-texte">
                    <h2>Une<br>séance.</h2>
                    <p>
                        Vous ne savez pas quoi regarder ce soir ? On pioche cinq films au hasard
                        dans tout le catalogue, sans tenir compte de ce que vous avez déjà vu ni
                        de ce qui marche en ce moment. Relancez autant de fois qu'il faudra.
                    </p>
                    <div class="hero-boutons">
                        <a href="hasard.php" class="bouton-principal">Tirer cinq films au hasard</a>
                    </div>
                </div>
                <?php if (!empty($filmsPioche)): ?>
                    <div class="hasard-pile">
                        <?php foreach ($filmsPioche as $film): ?>
                            <div class="affiche">
                                <img src="<?= htmlspecialchars($tmdb->getPosterUrl($film['poster_path'] ?? null)) ?>" alt="Affiche de <?= htmlspecialchars($film['title'] ?? '') ?>" loading="lazy">
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="js/scripts.js"></script>
</body>

</html>
