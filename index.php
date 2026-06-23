<?php $page = 'index'; 
?>
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
            <span class="label-section">Une bibliothèque cinéma — pas un concours</span>
            <h1 class="hero-titre">Le cinéma <em>se lit</em> aussi.</h1>
            <p class="hero-texte">Avis libres. Pas de notes, pas d'étoiles, pas de pouces. On écrit ce qu'on a vu, ce qu'on a ressenti.</p>
            <div class="hero-boutons">
                <a href="decouvrir.php">
                    <button class="btn-rouge">Découvrir
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
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
            <div class="image-deco p1">
                <div>Ryūsuke Hamaguchi</div>
                <div>Drive My Car</div>
                <div>2021</div>
            </div>
            <div class="image-deco p2">
                <div>Wim Wenders</div>
                <div>Perfect Days</div>
                <div>2023</div>
            </div>
            <div class="image-deco p3">
                <div>Charlotte Wells</div>
                <div>Aftersun</div>
                <div>2022</div>
            </div>
        </div>
    </section>

    <hr class="separateur">

    <section>
        <div class="avis-entete">
            <div>
                <span class="label-section">À lire en ce moment</span>
                <h2>Avis récents</h2>
            </div>
            <a href="fiche.php">
                <button class="btn-transparent">Voir plus d'avis
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14M13 6l6 6-6 6"></path>
                    </svg>
                </button>
            </a>
        </div>

        <div class="grille-avis">

            <article class="carte-avis">
                <a href="fiche.php" class="lien-carte">
                    <h3 class="avis-titre">Une cave, et tout l'édifice s'écroule</h3>
                    <p class="avis-texte">Bong Joon-ho ne filme pas la lutte des classes : il en filme l'architecture. Chaque escalier, chaque seuil, chaque odeur trace une frontière qu'on croyait abolie.</p>
                </a>
                <div class="avis-bas">
                    <span class="avatar" style="background: oklch(0.55 0.12 149);">P</span>
                    <span class="avis-auteur">philmage</span>
                    <span style="color: #8A8378;">sur</span>
                    <a href="fiche.php" class="lien-film">Parasite</a>
                    <span style="margin-left: auto;">2 mai 2026</span>
                </div>
            </article>

            <article class="carte-avis">
                <a href="fiche.php" class="lien-carte">
                    <h3 class="avis-titre">Conduire pour se taire</h3>
                    <p class="avis-texte">Trois heures qui ne pèsent rien. Hamaguchi installe un théâtre dans une voiture, et la voiture devient un lieu de soin.</p>
                </a>
                <div class="avis-bas">
                    <span class="avatar" style="background: oklch(0.55 0.12 30);">M</span>
                    <span class="avis-auteur">melfilmophile</span>
                    <span style="color: #8A8378;">sur</span>
                    <a href="fiche.php" class="lien-film">Drive My Car</a>
                    <span style="margin-left: auto;">29 avril 2026</span>
                </div>
            </article>

            <article class="carte-avis">
                <a href="fiche.php" class="lien-carte">
                    <h3 class="avis-titre">Monsieur Propre</h3>
                    <p class="avis-texte">Vu sans y croire, ressorti changé. Wenders nettoie sa caméra avec autant de soin que Hirayama nettoie ses toilettes.</p>
                </a>
                <div class="avis-bas">
                    <span class="avatar" style="background: oklch(0.55 0.12 220);">R</span>
                    <span class="avis-auteur">rachidkanopy</span>
                    <span style="color: #8A8378;">sur</span>
                    <a href="fiche.php" class="lien-film">Perfect Days</a>
                    <span style="margin-left: auto;">27 avril 2026</span>
                </div>
            </article>

        </div>

        <hr class="separateur">
    </section>

</main>

<?php include 'includes/footer.php'; ?>

<script src="js/scripts.js"></script>
</body>
</html>
