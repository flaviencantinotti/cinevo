<?php $page = 'home'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>Cinévo — Mon fil</title>
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="contenu">

    <div class="greeting">
        <h1>Bonsoir, Mélanie.</h1>
        <p>Quatre nouveaux avis depuis ta dernière visite. Aucune urgence.</p>
    </div>

    <div class="feed-home">
        <div class="feed-main">
            <div class="review-feed">

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
            <a href="fiche.php">
                <button class="rejoindre voir-avis">Voir plus d'avis.</button>
            </a>
        </div>

        <aside class="feed-side">
            <div class="side-card">
                <h4>Le parti pris</h4>
                <p>Pas de note, pas de classement, pas d'algorithme. Nous valorisons l'humain et ces émotions.</p>
            </div>
            <div class="side-card">
                <h4>Écrire un avis</h4>
                <p>Un film vous a marqué ? Partagez ce qu'il vous a fait.</p>
                <a href="ecrire.php" style="display:inline-block; margin-top:12px;">
                    <button class="terra" style="width:100%;">Commencer à écrire</button>
                </a>
            </div>
        </aside>
    </div>

    <hr class="diviseur">

</main>

<?php include 'includes/footer.php'; ?>

<script src="js/scripts.js"></script>
</body>
</html>
