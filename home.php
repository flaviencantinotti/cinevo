<?php
$page = 'home';
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (!estConnecte()) {
    header('Location: connexion.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>Cinévo — Mon fil</title>
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="contenu">

    <div class="message-accueil">
        <h1>Bonsoir, <?= htmlspecialchars($_SESSION['username']) ?>.</h1>
        <p>Bienvenue sur votre fil d'avis.</p>
    </div>

    <div class="page-home">
        <div class="colonne-principale">
            <div class="liste-avis">

                <article class="carte-avis">
                    <a href="fiche.php?id=496243" class="lien-carte">
                        <h3 class="avis-titre">Une cave, et tout l'édifice s'écroule</h3>
                        <p class="avis-texte">Bong Joon-ho ne filme pas la lutte des classes : il en filme l'architecture. Chaque escalier, chaque seuil, chaque odeur trace une frontière qu'on croyait abolie.</p>
                    </a>
                    <div class="avis-bas">
                        <span class="avatar" style="background: oklch(0.55 0.12 149);">P</span>
                        <span class="avis-auteur">philmage</span>
                        <span style="color: #8A8378;">sur</span>
                        <a href="fiche.php?id=496243" class="lien-film">Parasite</a>
                        <span style="margin-left: auto;">2 mai 2026</span>
                    </div>
                </article>

                <article class="carte-avis">
                    <a href="fiche.php?id=758866" class="lien-carte">
                        <h3 class="avis-titre">Conduire pour se taire</h3>
                        <p class="avis-texte">Trois heures qui ne pèsent rien. Hamaguchi installe un théâtre dans une voiture, et la voiture devient un lieu de soin.</p>
                    </a>
                    <div class="avis-bas">
                        <span class="avatar" style="background: oklch(0.55 0.12 30);">M</span>
                        <span class="avis-auteur">melfilmophile</span>
                        <span style="color: #8A8378;">sur</span>
                        <a href="fiche.php?id=758866" class="lien-film">Drive My Car</a>
                        <span style="margin-left: auto;">29 avril 2026</span>
                    </div>
                </article>

                <article class="carte-avis">
                    <a href="fiche.php?id=976893" class="lien-carte">
                        <h3 class="avis-titre">Monsieur Propre</h3>
                        <p class="avis-texte">Vu sans y croire, ressorti changé. Wenders nettoie sa caméra avec autant de soin que Hirayama nettoie ses toilettes.</p>
                    </a>
                    <div class="avis-bas">
                        <span class="avatar" style="background: oklch(0.55 0.12 220);">R</span>
                        <span class="avis-auteur">rachidkanopy</span>
                        <span style="color: #8A8378;">sur</span>
                        <a href="fiche.php?id=976893" class="lien-film">Perfect Days</a>
                        <span style="margin-left: auto;">27 avril 2026</span>
                    </div>
                </article>

            </div>
            <a href="fiche.php">
                <button class="btn-blanc" style="margin-top: 20px;">Voir plus d'avis.</button>
            </a>
        </div>

        <aside class="colonne-lateral">
            <div class="encart">
                <h4>Le parti pris</h4>
                <p>Pas de note, pas de classement, pas d'algorithme. Nous valorisons l'humain et ces émotions.</p>
            </div>
            <div class="encart">
                <h4>Écrire un avis</h4>
                <p>Un film vous a marqué ? Partagez ce qu'il vous a fait.</p>
                <a href="ecrire.php" style="display:inline-block; margin-top:12px;">
                    <button class="btn-rouge" style="width:100%;">Commencer à écrire</button>
                </a>
            </div>
        </aside>
    </div>

    <hr class="separateur">

</main>

<?php include 'includes/footer.php'; ?>

<script src="js/scripts.js"></script>
</body>
</html>
