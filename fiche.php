<?php
$page = 'fiche';

require_once 'includes/tmdb.php';
$tmdb = new TMDB();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$film = null;
$annee = '';
$duree = '';
$realisateur = '';
$genres = [];

if ($id > 0) {
    $film = $tmdb->getMovie($id);

    if ($film) {
        $annee = substr($film['release_date'] ?? '', 0, 4);
        $heures = floor(($film['runtime'] ?? 0) / 60);
        $mins = ($film['runtime'] ?? 0) % 60;
        $duree = $heures . ' h ' . $mins . ' min';

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
                <p class="text-lede">L'identifiant ne correspond à aucun film connu.</p>
                <a href="index.php" style="margin-top:24px; display:inline-block;">
                    <button class="terra">Retour à l'accueil</button>
                </a>
            </div>

        <?php else: ?>

            <article class="fiche">

                <div>
                    <div class="poster-lg">
                        <?php if ($film['poster_path']): ?>
                            <img src="<?= $tmdb->getPosterUrl($film['poster_path'], 'w342') ?>"
                                alt="Affiche de <?= htmlspecialchars($film['title']) ?>"
                                style="width:100%; height:100%; object-fit:cover;">
                        <?php else: ?>
                            <div class="poster-art p1" style="position:absolute; inset:0; padding:18px;
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
                    <span class="bibliotheque">Long métrage · <?= $annee ?></span>
                    <h1 class="film-title"><?= htmlspecialchars($film['title']) ?></h1>

                    <div class="film-meta">
                        <?= htmlspecialchars($realisateur) ?>
                        <span class="sep">·</span>
                        <?= $annee ?>
                        <span class="sep">·</span>
                        <?= $duree ?>
                    </div>

                    <?php if (!empty($genres)): ?>
                        <div class="genres">
                            <?php foreach ($genres as $genre): ?>
                                <span class="chip"><?= htmlspecialchars($genre['name']) ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($film['overview'])): ?>
                        <p class="synopsis"><?= htmlspecialchars($film['overview']) ?></p>
                    <?php endif; ?>

                    <div class="actions">
                        <a href="ecrire.php?id=<?= $id ?>">
                            <button class="terra">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 4l6 6-11 11H3v-6z"></path>
                                    <path d="M14 4l3-3 6 6-3 3"></path>
                                </svg>
                                Écrire un avis
                            </button>
                        </a>
                        <button class="rejoindre">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 3h12v18l-6-4-6 4z"></path>
                            </svg>
                            Ajouter à ma liste
                        </button>
                    </div>

                    <div class="where-watch">
                        <div class="subhead" style="display:flex; align-items:center; gap:8px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="9"></circle>
                                <path d="m15 9-2 6-6 2 2-6z"></path>
                            </svg>
                            Où voir ce film
                        </div>

                        <?php
                        $providers = $film['watch/providers']['results']['FR'] ?? null;
                        $flatrate = $providers['flatrate'] ?? [];
                        $rent = $providers['rent'] ?? [];
                        ?>

                        <?php foreach ($flatrate as $p): ?>
                            <div class="watch-row">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="5" width="18" height="13" rx="2"></rect>
                                    <path d="M8 21h8M12 18v3"></path>
                                </svg>
                                <div class="label"><?= htmlspecialchars($p['provider_name']) ?></div>
                                <div class="sublabel">Inclus dans l'abonnement</div>
                            </div>
                        <?php endforeach; ?>

                        <?php foreach ($rent as $p): ?>
                            <div class="watch-row">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="9"></circle>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <div class="label"><?= htmlspecialchars($p['provider_name']) ?></div>
                                <div class="sublabel">Location disponible</div>
                            </div>
                        <?php endforeach; ?>

                        <?php if (empty($flatrate) && empty($rent)): ?>
                            <p style="font-family:'Playfair Display'; font-style:italic;
                              color:var(--ink-3); font-size:14px; margin-top:12px;">
                                Aucune plateforme disponible en France pour le moment.
                            </p>
                        <?php endif; ?>

                        <div class="algo">Données fournies par TMDB et JustWatch</div>
                    </div>
                </div>
            </article>

            <hr class="diviseur">

            <section>
                <div class="section-head">
                    <div>
                        <span class="bibliotheque">Tri chronologique · plus récent en premier</span>
                        <h2 style="margin-top:8px;">Avis sur le film</h2>
                    </div>
                    <a href="ecrire.php?id=<?= $id ?>" style="margin-left:auto;">
                        <button class="terra">Écrire un avis</button>
                    </a>
                </div>

                <div class="avis-reviews" style="margin-top:24px;">

                    <article class="review-carte">
                        <h3 class="r-titre">Une cave, et tout l'édifice s'écroule</h3>
                        <p class="r-excerpt">Bong Joon-ho ne filme pas la lutte des classes : il en filme l'architecture.
                            Chaque escalier, chaque seuil, chaque odeur trace une frontière qu'on croyait abolie.</p>
                        <div class="r-foot">
                            <span class="avatar" aria-hidden="true" style="background: oklch(0.55 0.12 149);">P</span>
                            <span class="r-auteur">philmage</span>
                            <span style="color: var(--ink-3);">sur</span>
                            <span class="r-film"><?= htmlspecialchars($film['title']) ?></span>
                            <span style="margin-left: auto;">2 mai 2026</span>
                        </div>
                    </article>

                    <article class="review-carte">
                        <h3 class="r-titre">Conduire pour se taire</h3>
                        <p class="r-excerpt">Trois heures qui ne pèsent rien. Hamaguchi installe un théâtre dans une
                            voiture, et la voiture devient un lieu de soin.</p>
                        <div class="r-foot">
                            <span class="avatar" aria-hidden="true" style="background: oklch(0.55 0.12 30);">M</span>
                            <span class="r-auteur">melfilmophile</span>
                            <span style="color: var(--ink-3);">sur</span>
                            <span class="r-film"><?= htmlspecialchars($film['title']) ?></span>
                            <span style="margin-left: auto;">29 avril 2026</span>
                        </div>
                    </article>

                    <article class="review-carte">
                        <h3 class="r-titre">Monsieur Propre</h3>
                        <p class="r-excerpt">Vu sans y croire, ressorti changé. Wenders nettoie sa caméra avec autant de
                            soin que Hirayama nettoie ses toilettes.</p>
                        <div class="r-foot">
                            <span class="avatar" aria-hidden="true" style="background: oklch(0.55 0.12 220);">R</span>
                            <span class="r-auteur">rachidkanopy</span>
                            <span style="color: var(--ink-3);">sur</span>
                            <span class="r-film"><?= htmlspecialchars($film['title']) ?></span>
                            <span style="margin-left: auto;">27 avril 2026</span>
                        </div>
                    </article>

                </div>
            </section>

        <?php endif; ?>

    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="js/scripts.js"></script>
</body>

</html>