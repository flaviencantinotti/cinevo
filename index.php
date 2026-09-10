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
        $row['teinte']     = crc32($row['nom_utilisateur']) % 360;
        $avisRecents[]     = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Cinévo, le site d'avis et critiques de films sans notes ni étoiles. Une communauté de cinéphiles qui écrit ce qu'elle a vu et ressenti, sans algorithme.">
    <link rel="stylesheet" type="text/css" href="css/style.css?v=11">
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

        <section class="section-communaute">
            <span class="label-section">Notre concept</span>
            <h2>Un site communautaire d'avis et de critiques de films</h2>
            <p>
                On n'a jamais mis de note à un film ici, et on ne va pas commencer. Vous avez aimé, détesté,
                pleuré, ou dormi pendant la moitié ? Dites-le avec vos mots, personne ne compte les étoiles.
            </p>
            <p>
                Sorties du moment, vieux classiques poussiéreux, tops perso improbables : tout passe, tant que
                ça vient vraiment de vous.
            </p>
            <p>
                Envie d'en savoir plus sur cette <a href="decouvrir.php">communauté de cinéphiles</a> ou de
                <a href="a-propos.php">découvrir l'histoire de Cinévo</a> ?
            </p>
        </section>

        <hr class="separateur">

        <section>
            <div class="avis-entete">
                <div>
                    <span class="label-section">À lire en ce moment</span>
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

            <div class="grille-avis">

                <?php if (!baseDisponible()): ?>
                    <?= messageBaseIndisponible('L\'affichage des avis') ?>
                <?php elseif (empty($avisRecents)): ?>
                    <p class="intro">Aucun avis publié pour l'instant. <a href="ecrire.php">Soyez le premier à en écrire un</a> !</p>
                <?php endif; ?>

                <?php foreach ($avisRecents as $avis): ?>
                    <article class="carte-avis">
                        <a href="fiche.php?id=<?= (int) $avis['film_id'] ?>" class="lien-carte">
                            <h3 class="avis-titre"><?= htmlspecialchars($avis['titre'] ?: $avis['film_titre']) ?></h3>
                            <p class="avis-texte"><?= htmlspecialchars(extrait($avis['contenu'], 160)) ?></p>
                        </a>
                        <div class="avis-bas">
                            <span class="avatar" style="background: oklch(0.55 0.12 <?= $avis['teinte'] ?>);"><?= htmlspecialchars(mb_strtoupper(mb_substr($avis['nom_utilisateur'], 0, 1))) ?></span>
                            <span class="avis-auteur"><?= htmlspecialchars($avis['nom_utilisateur']) ?></span>
                            <span style="color: #8A8378;">sur</span>
                            <a href="fiche.php?id=<?= (int) $avis['film_id'] ?>" class="lien-film"><?= htmlspecialchars($avis['film_titre']) ?></a>
                            <span style="margin-left: auto;"><?= formaterDateFr($avis['publie_le']) ?></span>
                        </div>
                    </article>
                <?php endforeach; ?>

            </div>

            <hr class="separateur">
        </section>

        <section class="appel-action">
            <span class="label-section">Pas d'idée ce soir ?</span>
            <h2>Découvrez un film au hasard, sans algorithme</h2>
            <p style="max-width:640px; margin-top:12px; font-family:'Spectral', serif; font-size:18px; line-height:1.65; color:#1A1A1A;">
                Vous scrollez depuis 20 minutes sans rien trouver ? On connaît. On vous pioche cinq films au
                pifomètre. Pas d'algo qui vous ressert le même film sous un autre titre : juste du hasard, et
                peut-être un coup de cœur inattendu.
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
        </section>

    </main>

        <?php include 'includes/footer.php'; ?>

    <script src="js/scripts.js"></script>
</body>

</html>