<?php $page = 'decouvrir'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>Cinévo — Découvrir</title>
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="contenu">
    <div class="banniere">
        <h1>Qu'est-ce que Cinévo ?</h1>
    </div>

    <div class="titre-decouvrir">
        <h2 class="hero-titre">Le cinéma, <em>simplement</em>.</h2>
        <p class="texte-intro">
            <em>Marre de la guerre des notes, fatigué(e) de ne pas avoir de réels échanges avec quelqu'un ? Vous voulez simplement donner votre avis et en lire d'autres ?
            </em><span>Cinévo</span><em> est fait pour vous !</em>
        </p>
    </div>

    <hr class="separateur">

    <section>
        <div class="label-section">Pour qui</div>
        <h3>Pour ceux qui aiment le cinéma, point.</h3>
        <ul class="liste-public">
            <li>
                Si vous cherchez un avis avant d'aller en salle
                <br><em>Vous pouvez lire sans être inscrit.</em>
            </li>
            <li>
                Si vous voulez écrire ce qu'un film vous a fait
                <br><em>Pas de longueur minimale réelle. (seulement 20 caractères, ça va vite !)</em>
            </li>
            <li>
                Si vous voulez sortir de votre bulle algorithmique
                <br><em>Des voix variées, jamais hiérarchisées.</em>
            </li>
            <li>
                Si vous n'êtes pas un cinéphile « pro »
                <br><em>Aucun ton snob toléré. C'est une règle.</em>
            </li>
        </ul>
    </section>

    <hr class="separateur">

    <section class="appel-action">
        <h3>Prêt(e) à rejoindre ?</h3>
        <p class="texte-intro" style="margin-top:12px;"><em>C'est gratuit, sans algorithme, et ça prend 30 secondes.</em></p>
        <div class="hero-boutons" style="margin-top:20px;">
            <a href="inscription.php">
                <button class="btn-rouge">Rejoindre Cinévo
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14M13 6l6 6-6 6"></path>
                    </svg>
                </button>
            </a>
            <a href="connexion.php">
                <button class="btn-blanc">Déjà inscrit ? Connexion</button>
            </a>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>

<script src="js/scripts.js"></script>
</body>
</html>
