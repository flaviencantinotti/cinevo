<?php
$page = 'decouvrir';
require_once 'includes/auth.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Cinévo est la plateforme d'avis et critiques de films sans notes ni classement. Découvrez pourquoi des cinéphiles partagent leur vrai ressenti sur le cinéma, loin des algorithmes.">
    <link rel="stylesheet" type="text/css" href="css/style.css?v=2">
    <title>Découvrir Cinévo — La plateforme d'avis cinéma sans notes</title>
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="contenu">
    <div class="banniere">
        <h1>Cinévo, la plateforme d'avis et critiques de films sans notes</h1>
    </div>

    <div class="titre-decouvrir">
        <h2 class="hero-titre">Le cinéma, <em>simplement</em>.</h2>
        <p class="texte-intro">
            <em>Marre de la guerre des notes sur 5 ou sur 10, fatigué(e) de ne pas avoir de réels échanges avec quelqu'un autour d'un film ? Vous voulez simplement partager votre avis sur le cinéma et découvrir celui des autres ?
            </em><span>Cinévo</span><em> est fait pour vous !</em>
        </p>
    </div>

    <hr class="separateur">

    <section>
        <div class="label-section">Pour qui</div>
        <h3>Une communauté de cinéphiles ouverte à tous, point.</h3>
        <ul class="liste-public">
            <li>
                Si vous cherchez une critique de film ou un avis de spectateur avant de choisir votre prochaine sortie cinéma
                <br><em>Consultez librement les critiques et les avis publiés par toute la communauté, même sans créer de compte ni vous inscrire.</em>
            </li>
            <li>
                Si vous voulez partager votre ressenti et raconter ce qu'un film vous a fait vivre, en salle ou chez vous
                <br><em>Aucune longueur minimale imposée pour rédiger votre critique : quelques phrases suffisent, ou même 20 caractères si vous êtes pressé(e) !</em>
            </li>
            <li>
                Si vous voulez sortir de votre bulle algorithmique et explorer un catalogue de films plus large et plus varié
                <br><em>Des avis de spectateurs aux goûts différents, présentés côte à côte, jamais triés ni hiérarchisés par une note ou un classement.</em>
            </li>
            <li>
                Si vous n'êtes pas un critique de cinéma professionnel mais simplement un amateur de films comme tant d'autres
                <br><em>Aucun jargon technique, aucun ton snob toléré ici : chaque avis de la communauté compte autant que les autres.</em>
            </li>
        </ul>
    </section>

    <hr class="separateur">

    <section class="appel-action">
        <h3>Prêt(e) à rejoindre la communauté Cinévo ?</h3>
        <p class="texte-intro" style="margin-top:12px;"><em>Créer un compte pour publier vos critiques de films est gratuit, sans algorithme, et ça prend 30 secondes.</em></p>
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
