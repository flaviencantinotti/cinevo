<?php
$page = 'fiche';

require_once 'includes/tmdb.php';
require_once 'includes/db.php';
$tmdb = new TMDB();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$film        = null;
$annee       = '';
$duree       = '';
$realisateur = '';
$genres      = [];

if ($id > 0) {
    $film = $tmdb->getMovie($id);

    if ($film) {
        $annee  = substr($film['release_date'] ?? '', 0, 4);
        $heures = floor(($film['runtime'] ?? 0) / 60);
        $mins   = ($film['runtime'] ?? 0) % 60;
        $duree  = $heures . ' h ' . $mins . ' min';

        foreach (($film['credits']['crew'] ?? []) as $membre) {
            if ($membre['job'] === 'Director') {
                $realisateur = $membre['name'];
                break;
            }
        }

        $genres = $film['genres'] ?? [];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>Cinévo — <?= $film ? htmlspecialchars($film['title']) : 'Film introuvable' ?></title>
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="contenu">

    <?php if (!$film): ?>

        <div style="text-align:center; padding: 80px 0;">
            <h1>Film introuvable.</h1>
            <p class="intro">L'identifiant ne correspond à aucun film connu.</p>
            <a href="index.php" style="margin-top:24px; display:inline-block;">
                <button class="btn-rouge">Retour à l'accueil</button>
            </a>
        </div>

    <?php else: ?>

        <article class="detail-film">

            <div>
                <div class="grande-affiche">
                    <?php if ($film['poster_path']): ?>
                        <img src="<?= $tmdb->getPosterUrl($film['poster_path'], 'w342') ?>"
                             alt="Affiche de <?= htmlspecialchars($film['title']) ?>"
                             style="width:100%; height:100%; object-fit:cover;">
                    <?php else: ?>
                        <div class="p1" style="position:absolute; inset:0; padding:18px;
                             display:flex; flex-direction:column; justify-content:space-between;">
                            <div><?= htmlspecialchars($realisateur) ?></div>
                            <div style="font-size:24px; font-weight:700;">
                                <?= htmlspecialchars($film['title']) ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <span class="label-section">Long métrage · <?= $annee ?></span>
                <h1 class="titre-film"><?= htmlspecialchars($film['title']) ?></h1>

                <div class="infos-film">
                    <?= htmlspecialchars($realisateur) ?>
                    <span class="sep">·</span>
                    <?= $annee ?>
                    <span class="sep">·</span>
                    <?= $duree ?>
                </div>

                <?php if (!empty($genres)): ?>
                    <div class="liste-genres">
                        <?php foreach ($genres as $genre): ?>
                            <span class="tag"><?= htmlspecialchars($genre['name']) ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($film['overview'])): ?>
                    <p class="synopsis"><?= htmlspecialchars($film['overview']) ?></p>
                <?php endif; ?>

                <div class="boutons-actions">
                    <a href="ecrire.php?id=<?= $id ?>">
                        <button class="btn-rouge">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 4l6 6-11 11H3v-6z"></path>
                                <path d="M14 4l3-3 6 6-3 3"></path>
                            </svg>
                            Écrire un avis
                        </button>
                    </a>
                    <button class="btn-blanc">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 3h12v18l-6-4-6 4z"></path>
                        </svg>
                        Ajouter à ma liste
                    </button>
                </div>

                <div class="ou-voir">
                    <div class="titre-bloc">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="9"></circle>
                            <path d="m15 9-2 6-6 2 2-6z"></path>
                        </svg>
                        Où voir ce film
                    </div>

                    <?php
                    $providers = $film['watch/providers']['results']['FR'] ?? null;
                    $abonnement = $providers['flatrate'] ?? [];
                    $location   = $providers['rent'] ?? [];
                    ?>

                    <?php foreach ($abonnement as $p): ?>
                        <div class="ligne-plateforme">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="5" width="18" height="13" rx="2"></rect>
                                <path d="M8 21h8M12 18v3"></path>
                            </svg>
                            <div class="nom-plateforme"><?= htmlspecialchars($p['provider_name']) ?></div>
                            <div class="acces">Inclus dans l'abonnement</div>
                        </div>
                    <?php endforeach; ?>

                    <?php foreach ($location as $p): ?>
                        <div class="ligne-plateforme">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="9"></circle>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <div class="nom-plateforme"><?= htmlspecialchars($p['provider_name']) ?></div>
                            <div class="acces">Location disponible</div>
                        </div>
                    <?php endforeach; ?>

                    <?php if (empty($abonnement) && empty($location)): ?>
                        <p style="font-family:'Playfair Display'; font-style:italic; color:#8A8378; font-size:14px; margin-top:12px;">
                            Aucune plateforme disponible en France pour le moment.
                        </p>
                    <?php endif; ?>

                    <div class="source">Données fournies par TMDB et JustWatch</div>
                </div>
            </div>
        </article>

        <hr class="separateur">

        <section>
            <div class="entete-section">
                <div>
                    <span class="label-section">Tri chronologique · plus récent en premier</span>
                    <h2 style="margin-top:8px;">Avis sur le film</h2>
                </div>
                <a href="ecrire.php?id=<?= $id ?>" style="margin-left:auto;">
                    <button class="btn-rouge">Écrire un avis</button>
                </a>
            </div>

            <?php
            $stmt = $conn->prepare("
                SELECT a.titre, a.contenu, a.created_at, u.username
                FROM avis a
                JOIN utilisateurs u ON a.utilisateur_id = u.id
                WHERE a.film_id = ?
                ORDER BY a.created_at DESC
            ");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $liste_avis = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            ?>

            <div class="liste-avis" style="margin-top:24px;">

                <?php if (empty($liste_avis)): ?>
                    <p style="font-family:'Playfair Display'; font-style:italic; color:#8A8378;">
                        Aucun avis pour ce film. Soyez le premier à en écrire un.
                    </p>
                <?php else: ?>
                    <?php foreach ($liste_avis as $avis):
                        $initiale = strtoupper(mb_substr($avis['username'], 0, 1));
                        $date = date('j F Y', strtotime($avis['created_at']));
                    ?>
                        <article class="carte-avis">
                            <?php if (!empty($avis['titre'])): ?>
                                <h3 class="avis-titre"><?= htmlspecialchars($avis['titre']) ?></h3>
                            <?php endif; ?>
                            <p class="avis-texte"><?= htmlspecialchars($avis['contenu']) ?></p>
                            <div class="avis-bas">
                                <span class="avatar"><?= $initiale ?></span>
                                <span class="avis-auteur"><?= htmlspecialchars($avis['username']) ?></span>
                                <span style="color:#8A8378;">sur</span>
                                <span class="lien-film"><?= htmlspecialchars($film['title']) ?></span>
                                <span style="margin-left: auto;"><?= $date ?></span>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>

            </div>
        </section>

    <?php endif; ?>

</main>

<?php include 'includes/footer.php'; ?>

<script src="js/scripts.js"></script>
</body>
</html>
