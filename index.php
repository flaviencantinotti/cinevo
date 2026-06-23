<?php $page = 'index'; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>Cinévo — Le cinéma se lit aussi</title>
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="contenu">

    <section class="hero">
        <div>
            <span class="bibliotheque">Une bibliothèque cinéma — pas un concours</span>
            <h1 class="hero-titre">Le cinéma <em>se lit</em> aussi.</h1>
            <p class="hero-p">Avis libres. Pas de notes, pas d'étoiles, pas de pouces. On écrit ce qu'on a vu, ce qu'on a ressenti.</p>
            <div class="hero-boutons">
                <a href="decouvrir.php">
                    <button class="decouvrir">Découvrir <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"></path></svg></button>
                </a>
                <a href="inscription.php">
                    <button class="rejoindre">Rejoindre Cinévo</button>
                </a>
            </div>
            <p class="algo">Lecture gratuite. Écriture libre. Pas d'algorithme.</p>
        </div>

        <div class="hero-art">
            <div class="art-poster p1">
                <div class="poster-art">
                    <div>Ryūsuke Hamaguchi</div>
                    <div>Drive My Car</div>
                    <div>2021</div>
                </div>
            </div>
            <div class="art-poster p2">
                <div class="poster-art">
                    <div>Wim Wenders</div>
                    <div>Perfect Days</div>
                    <div>2023</div>
                </div>
            </div>
            <div class="art-poster p3">
                <div class="poster-art">
                    <div>Charlotte Wells</div>
                    <div>Aftersun</div>
                    <div>2022</div>
                </div>
            </div>
        </div>
    </section>

    <hr class="diviseur">

    <section>
        <div class="avis-recents">
            <div>
                <span class="bibliotheque">À lire en ce moment</span>
                <h2 id="avis-titre">Avis récents</h2>
            </div>
            <a href="fiche.php" class="voir-plus-link">
                <button class="voir-plus">Voir plus d'avis <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"></path></svg></button>
            </a>
        </div>

        <div class="avis-reviews">

            <article class="review-carte">
                <a href="fiche.php" class="carte-link">
                    <h3 class="r-titre">Une cave, et tout l'édifice s'écroule</h3>
                    <p class="r-excerpt">Bong Joon-ho ne filme pas la lutte des classes : il en filme l'architecture. Chaque escalier, chaque seuil, chaque odeur trace une frontière qu'on croyait abolie. La famille Kim s'infiltre par le bas, et c'est le bas qui finit par parler. On rit, on s'effraie, on rit encore — puis on comprend, trop tard, qu'on riait avec les mauvais.</p>
                </a>
                <div class="r-foot">
                    <span class="avatar" aria-hidden="true" style="background: oklch(0.55 0.12 149);">P</span>
                    <span class="r-auteur">philmage</span>
                    <span style="color: var(--ink-3);">sur</span>
                    <a href="fiche.php" class="r-film">Parasite</a>
                    <span style="margin-left: auto;">2 mai 2026</span>
                </div>
            </article>

            <article class="review-carte">
                <a href="fiche.php" class="carte-link">
                    <h3 class="r-titre">Conduire pour se taire</h3>
                    <p class="r-excerpt">Trois heures qui ne pèsent rien. Hamaguchi installe un théâtre dans une voiture, et la voiture devient un lieu de soin. La cassette tourne, Tchekhov répond, Misaki conduit comme on respire. Je suis sortie de la salle plus lente que j'y suis entrée.</p>
                </a>
                <div class="r-foot">
                    <span class="avatar" aria-hidden="true" style="background: oklch(0.55 0.12 30);">M</span>
                    <span class="r-auteur">melfilmophile</span>
                    <span style="color: var(--ink-3);">sur</span>
                    <a href="fiche.php" class="r-film">Drive My Car</a>
                    <span style="margin-left: auto;">29 avril 2026</span>
                </div>
            </article>

            <article class="review-carte">
                <a href="fiche.php" class="carte-link">
                    <h3 class="r-titre">Monsieur Propre</h3>
                    <p class="r-excerpt">Vu sans y croire, ressorti changé. Wenders nettoie sa caméra avec autant de soin que Hirayama nettoie ses toilettes. Le film ne raconte presque rien, et pourtant il dit beaucoup sur ce que c'est, vivre avec très peu — et trouver ça largement suffisant.</p>
                </a>
                <div class="r-foot">
                    <span class="avatar" aria-hidden="true" style="background: oklch(0.55 0.12 220);">R</span>
                    <span class="r-auteur">rachidkanopy</span>
                    <span style="color: var(--ink-3);">sur</span>
                    <a href="fiche.php" class="r-film">Perfect Days</a>
                    <span style="margin-left: auto;">27 avril 2026</span>
                </div>
            </article>

        </div>

        <hr class="diviseur">
    </section>

</main>

<?php include 'includes/footer.php'; ?>

<script src="js/scripts.js"></script>
</body>
</html>
