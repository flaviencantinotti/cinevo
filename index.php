<?php
$page = 'index';
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/tmdb.php';
$tmdb = new TMDB();

function formaterDateFr(string $datetime): string {
    static $mois = [1 => 'janvier', 2 => 'février', 3 => 'mars', 4 => 'avril', 5 => 'mai', 6 => 'juin',
                    7 => 'juillet', 8 => 'août', 9 => 'septembre', 10 => 'octobre', 11 => 'novembre', 12 => 'décembre'];
    $d = new DateTime($datetime);
    return (int) $d->format('j') . ' ' . $mois[(int) $d->format('n')] . ' ' . $d->format('Y');
}

$avisRecents = [];
$result = $conn->query("
    SELECT avis.film_id, avis.titre, avis.contenu, avis.created_at, utilisateurs.username
    FROM avis
    JOIN utilisateurs ON avis.utilisateur_id = utilisateurs.id
    ORDER BY avis.created_at DESC
    LIMIT 3
");

while ($row = $result->fetch_assoc()) {
    $film = $tmdb->getMovie((int) $row['film_id']);
    $row['film_titre'] = $film['title'] ?? 'Film';
    $row['teinte']     = crc32($row['username']) % 360;
    $avisRecents[]     = $row;
}

$heroFilms = [];
foreach ($tmdb->getRandomMovies(3) as $filmBrut) {
    $details     = $tmdb->getMovie($filmBrut['id']);
    $realisateur = '';
    foreach (($details['credits']['crew'] ?? []) as $membre) {
        if ($membre['job'] === 'Director') {
            $realisateur = $membre['name'];
            break;
        }
    }
    $heroFilms[] = [
        'id'          => $filmBrut['id'],
        'titre'       => $filmBrut['title'],
        'annee'       => substr($filmBrut['release_date'] ?? '', 0, 4),
        'affiche'     => $tmdb->getPosterUrl($filmBrut['poster_path'], 'w342'),
        'realisateur' => $realisateur,
    ];
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Cinévo, le site d'avis et critiques de films sans notes ni étoiles. Une communauté de cinéphiles qui écrit ce qu'elle a vu et ressenti, sans algorithme.">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>Cinévo — Le cinéma se lit aussi</title>
</head>

<body>

        <?php include 'includes/header.php'; ?>

    <main class="contenu">

        <section class="hero">
            <div>
                <span class="label-section">Une bibliothèque cinéma — pas un concours</span>
                <h1 class="hero-titre">Le cinéma <em>se lit</em> aussi.</h1>
                <p class="hero-texte">Avis libres. Pas de notes, pas d'étoiles, pas de pouces. On écrit ce qu'on a vu,
                    ce qu'on a ressenti.</p>
                <div class="hero-boutons">
                    <a href="decouvrir.php">
                        <button class="btn-rouge">Découvrir
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14M13 6l6 6-6 6"></path>
                            </svg>
                        </button>
                    </a>
                    <a href="inscription.php">
                        <button class="btn-blanc">Rejoindre Cinévo</button>
                    </a>
                </div>
                <p class="source">Lecture gratuite. Écriture libre. Pas d'algorithme.</p>
            </div>

            <div class="hero-visuels">
                <?php foreach ($heroFilms as $i => $film): ?>
                    <a href="fiche.php?id=<?= $film['id'] ?>" class="image-deco p<?= $i + 1 ?>">
                        <img src="<?= htmlspecialchars($film['affiche']) ?>"
                             alt="Affiche de <?= htmlspecialchars($film['titre']) ?>" loading="lazy">
                        <div class="legende-deco">
                            <?php if ($film['realisateur']): ?>
                                <div><?= htmlspecialchars($film['realisateur']) ?></div>
                            <?php endif; ?>
                            <div><?= htmlspecialchars($film['titre']) ?></div>
                            <div><?= htmlspecialchars($film['annee']) ?></div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>

        <hr class="separateur">

        <section class="section-communaute">
            <span class="label-section">Notre concept</span>
            <h2>Un site communautaire d'avis et de critiques de films</h2>
            <p>
                Cinévo est un site communautaire dédié aux avis et critiques de films, pensé pour les cinéphiles
                qui préfèrent écrire plutôt que noter. Ici, pas d'étoiles, pas de classement : chaque avis de
                spectateur raconte avec ses propres mots ce qu'un film a été vu et ressenti.
            </p>
            <p>
                Vous avez un espace clair pour discuter entre passionné(e)s de cinéma : les dernières sorties en
                salle, les films de patrimoine et le cinéma classique, vos listes de films et vos tops personnels —
                tout est sujet à discussion et à débat cinéphile, dans un endroit sain, sans jugement ni ton snob.
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

                <?php if (empty($avisRecents)): ?>
                    <p class="intro">Aucun avis publié pour l'instant. <a href="ecrire.php">Soyez le premier à en écrire un</a> !</p>
                <?php endif; ?>

                <?php foreach ($avisRecents as $avis): ?>
                    <article class="carte-avis">
                        <a href="fiche.php?id=<?= (int) $avis['film_id'] ?>" class="lien-carte">
                            <h3 class="avis-titre"><?= htmlspecialchars($avis['titre'] ?: $avis['film_titre']) ?></h3>
                            <p class="avis-texte"><?= htmlspecialchars(mb_substr($avis['contenu'], 0, 160)) ?><?= mb_strlen($avis['contenu']) > 160 ? '…' : '' ?></p>
                        </a>
                        <div class="avis-bas">
                            <span class="avatar" style="background: oklch(0.55 0.12 <?= $avis['teinte'] ?>);"><?= htmlspecialchars(mb_strtoupper(mb_substr($avis['username'], 0, 1))) ?></span>
                            <span class="avis-auteur"><?= htmlspecialchars($avis['username']) ?></span>
                            <span style="color: #8A8378;">sur</span>
                            <a href="fiche.php?id=<?= (int) $avis['film_id'] ?>" class="lien-film"><?= htmlspecialchars($avis['film_titre']) ?></a>
                            <span style="margin-left: auto;"><?= formaterDateFr($avis['created_at']) ?></span>
                        </div>
                    </article>
                <?php endforeach; ?>

            </div>

            <hr class="separateur">
        </section>

        <section class="appel-action">
            <span class="label-section">Pas d'idée ce soir ?</span>
            <h2>Découvrez un film au hasard, sans algorithme</h2>
            <p style="max-width:640px; margin-top:12px; font-family:'Playfair Display', serif; font-size:18px; line-height:1.65; color:#1A1A1A;">
                Marre de scroller sans trouver de film à voir ? Cinévo tire pour vous une sélection aléatoire de
                cinq films, sans recommandation algorithmique ni classement de popularité. Une vraie découverte de
                films, pensée pour sortir de votre bulle et retrouver le plaisir de choisir un film au hasard.
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