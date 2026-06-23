<?php $page = 'fiche'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>Cinévo — Drive My Car</title>
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="contenu">

    <article class="fiche">
        <div>
            <div class="poster-lg">
                <div class="poster-art p1" style="inset:0; position:absolute; padding:18px; display:flex; flex-direction:column; justify-content:space-between; font-family:var(--serif);">
                    <div style="font-size:11px; letter-spacing:0.24em; text-transform:uppercase; opacity:0.75; font-family:var(--sans); font-weight:600;">Ryūsuke Hamaguchi</div>
                    <div style="position:absolute; inset:0; background:repeating-linear-gradient(rgba(255,255,255,0.04) 0px, rgba(255,255,255,0.04) 1px, transparent 1px, transparent 6px);"></div>
                    <div style="position:relative;">
                        <div style="font-size:28px; font-weight:700; line-height:1.05; letter-spacing:-0.01em;">Drive My Car</div>
                        <div style="font-size:12px; margin-top:4px; opacity:0.8; font-family:var(--sans); letter-spacing:0.1em;">2021</div>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <span class="bibliotheque">Long métrage · 2021</span>
            <h1 class="film-title">Drive My Car</h1>

            <div class="film-meta">
                Ryūsuke Hamaguchi
                <span class="sep">·</span>
                2021
                <span class="sep">·</span>
                2 h 12 min
            </div>

            <div class="genres">
                <span class="chip">Drame</span>
            </div>

            <p class="synopsis">
                Yusuke Kafuku, acteur et metteur en scène, est encore meurtri par un drame survenu deux ans plus tôt. Il accepte de monter Oncle Vania à Hiroshima. Il y rencontre Misaki, une jeune femme réservée qu'on lui a assignée comme chauffeure. Au fil des trajets, leurs confessions vont les amener à faire face à leur passé.
            </p>

            <div class="actions">
                <a href="ecrire.php">
                    <button class="terra">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 4l6 6-11 11H3v-6z"></path>
                            <path d="M14 4l3-3 6 6-3 3"></path>
                        </svg>
                        Écrire un avis
                    </button>
                </a>
                <button class="rejoindre">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 3h12v18l-6-4-6 4z"></path>
                    </svg>
                    Ajouter à ma liste
                </button>
            </div>

            <div class="where-watch">
                <div class="subhead" style="display:flex; align-items:center; gap:8px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="m15 9-2 6-6 2 2-6z"></path>
                    </svg>
                    Où voir ce film
                </div>

                <div class="watch-row">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 8a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v3a2 2 0 0 0 0 4v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-3a2 2 0 0 0 0-4z"></path>
                        <path d="M9 6v12" stroke-dasharray="2 2"></path>
                    </svg>
                    <div class="label">Cinéma Utopia, Bordeaux</div>
                    <div class="sublabel">Mer. 19h45 · Jeu. 21h00</div>
                </div>

                <div class="watch-row">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="5" width="18" height="13" rx="2"></rect>
                        <path d="M8 21h8M12 18v3"></path>
                    </svg>
                    <div class="label">Disponible sur MUBI</div>
                    <div class="sublabel">Inclus, sous-titres FR</div>
                </div>

                <div class="watch-row">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="9"></circle>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    <div class="label">Édition Blu-ray</div>
                    <div class="sublabel">Sortie : 12 mars 2026</div>
                </div>

                <div class="algo">Données fournies par TMDB et JustWatch</div>
            </div>
        </div>
    </article>

    <hr class="diviseur">

    <section>
        <div class="section-head">
            <div>
                <span class="bibliotheque">Tri chronologique · plus récent en premier</span>
                <h2 style="margin-top:8px;">3 Avis sur le film</h2>
            </div>
            <a href="ecrire.php" style="margin-left:auto;">
                <button class="terra">Écrire un avis</button>
            </a>
        </div>

        <div class="avis-reviews" style="margin-top:24px;">

            <article class="review-carte">
                <h3 class="r-titre">Une cave, et tout l'édifice s'écroule</h3>
                <p class="r-excerpt">Bong Joon-ho ne filme pas la lutte des classes : il en filme l'architecture. Chaque escalier, chaque seuil, chaque odeur trace une frontière qu'on croyait abolie. La famille Kim s'infiltre par le bas, et c'est le bas qui finit par parler. On rit, on s'effraie, on rit encore — puis on comprend, trop tard, qu'on riait avec les mauvais.</p>
                <div class="r-foot">
                    <span class="avatar" aria-hidden="true" style="background: oklch(0.55 0.12 149);">P</span>
                    <span class="r-auteur">philmage</span>
                    <span style="color: var(--ink-3);">sur</span>
                    <span class="r-film">Parasite</span>
                    <span style="margin-left: auto;">2 mai 2026</span>
                </div>
            </article>

            <article class="review-carte">
                <h3 class="r-titre">Conduire pour se taire</h3>
                <p class="r-excerpt">Trois heures qui ne pèsent rien. Hamaguchi installe un théâtre dans une voiture, et la voiture devient un lieu de soin. La cassette tourne, Tchekhov répond, Misaki conduit comme on respire. Je suis sortie de la salle plus lente que j'y suis entrée.</p>
                <div class="r-foot">
                    <span class="avatar" aria-hidden="true" style="background: oklch(0.55 0.12 30);">M</span>
                    <span class="r-auteur">melfilmophile</span>
                    <span style="color: var(--ink-3);">sur</span>
                    <span class="r-film">Drive My Car</span>
                    <span style="margin-left: auto;">29 avril 2026</span>
                </div>
            </article>

            <article class="review-carte">
                <h3 class="r-titre">Monsieur Propre</h3>
                <p class="r-excerpt">Vu sans y croire, ressorti changé. Wenders nettoie sa caméra avec autant de soin que Hirayama nettoie ses toilettes. Le film ne raconte presque rien, et pourtant il dit beaucoup sur ce que c'est, vivre avec très peu — et trouver ça largement suffisant.</p>
                <div class="r-foot">
                    <span class="avatar" aria-hidden="true" style="background: oklch(0.55 0.12 220);">R</span>
                    <span class="r-auteur">rachidkanopy</span>
                    <span style="color: var(--ink-3);">sur</span>
                    <span class="r-film">Perfect Days</span>
                    <span style="margin-left: auto;">27 avril 2026</span>
                </div>
            </article>

        </div>
    </section>

</main>

<?php include 'includes/footer.php'; ?>

<script src="js/scripts.js"></script>
</body>
</html>
