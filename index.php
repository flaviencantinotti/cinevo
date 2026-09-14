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
        $row['teinte']     = crc32($row['nom_utilisateur']) % 360;
        $avisRecents[]     = $row;
    }
}

// Petite pile d'affiches, purement decorative, pour illustrer l'appel au
// tirage aleatoire sans dependre des avis deja publies.
$filmsPioche = array_slice($tmdb->getRandomMovies(3), 0, 3);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Cinévo, le site d'avis et critiques de films sans notes ni étoiles. Une communauté de cinéphiles qui écrit ce qu'elle a vu et ressenti, sans algorithme.">
    <link rel="stylesheet" type="text/css" href="css/style.css?v=23">
    <title>Cinévo · Le cinéma se lit aussi</title>
</head>

<body>

        <?php include 'includes/header.php'; ?>

    <main class="contenu">

        <section class="hero">
            <div class="hero-fond"></div>
            <div class="hero-voile"></div>

            <div class="hero-contenu">

                <span class="hero-badge">Sans note, sans classement</span>
                <h1>Le cinéma, ça se <em>discute</em></h1>

                <p class="hero-description">
                    Sur Cinévo, chaque avis ouvre une conversation. Pas d'accord avec une critique ?
                    Répondez, nuancez, débattez : c'est ce que les sites de notation ne permettent pas.
                </p>

                <div class="hero-boutons">
                    <a href="avis.php">
                        <button class="btn-rouge">Rejoindre une discussion
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14M13 6l6 6-6 6"></path>
                            </svg>
                        </button>
                    </a>
                </div>

            </div>
        </section>

        <hr class="separateur">

        <section class="section-concept">
            <span class="label-section surligne">Notre concept</span>
            <div class="concept-layout">
                <div class="concept-texte">
                    <p>
                        Ici, personne ne met de note à un film, encore moins des étoiles. Vous avez adoré, détesté,
                        pleuré ou piqué du nez au bout de vingt minutes ? Dites-le avec vos mots, comme vous le sentez.
                        Une sortie de la semaine, un classique que tout le monde a déjà vu sauf vous, une madeleine
                        de Proust totalement improbable : tout a sa place ici, tant que c'est vraiment votre avis.
                    </p>
                </div>
                <div class="concept-cta">
                    <p>Envie d'en savoir plus sur nous ?</p>
                    <a href="a-propos.php">
                        <button class="btn-blanc">Découvrir l'histoire de Cinévo
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14M13 6l6 6-6 6"></path>
                            </svg>
                        </button>
                    </a>
                </div>
            </div>
        </section>

        <hr class="separateur">

        <section>
            <div class="avis-entete">
                <div>
                    <span class="label-section surligne">À lire en ce moment</span>
                    <h2>Avis récents</h2>
                </div>
                <a href="avis.php">
                    <button class="btn-transparent">Voir plus d'avis
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14M13 6l6 6-6 6"></path>
                        </svg>
                    </button>
                </a>
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
                            <div class="film"><?= htmlspecialchars($avis['film_titre']) ?></div>
                            <h3><?= htmlspecialchars($avis['titre'] ?: $avis['film_titre']) ?></h3>
                            <p class="avis-texte"><?= htmlspecialchars(extrait($avis['contenu'], 140)) ?></p>
                            <div class="qui">
                                <span class="avatar" style="background: oklch(0.55 0.12 <?= $avis['teinte'] ?>);"><?= htmlspecialchars(mb_strtoupper(mb_substr($avis['nom_utilisateur'], 0, 1))) ?></span>
                                <span><strong><?= htmlspecialchars($avis['nom_utilisateur']) ?></strong> · <?= formaterDateFr($avis['publie_le']) ?></span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>

            </div>

            <hr class="separateur">
        </section>

        <section class="appel-action">
            <div class="hasard-encart">
                <div class="hasard-texte">
                    <span class="label-section surligne">On regarde quoi ce soir ?</span>
                    <h2>Découvrez un film au hasard, sans algorithme</h2>
                    <p>
                        Vous scrollez depuis 20 minutes sans rien trouver ? On connaît. On vous propose cinq
                        films au hasard : le but ? Se laisser guider, loin des recommandations, et découvrir
                        un film qui peut (ou pas) vous surprendre.
                    </p>
                    <div class="hero-boutons" style="margin-top:20px;">
                        <a href="hasard.php">
                            <button class="btn-rouge">Tirer 5 films au hasard
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 12h14M13 6l6 6-6 6"></path>
                                </svg>
                            </button>
                        </a>
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