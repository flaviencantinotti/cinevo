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
    <link rel="stylesheet" type="text/css" href="css/style.css?v=34">
    <title>Découvrir Cinévo · La plateforme d'avis cinéma sans notes</title>
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="contenu">

    <div class="ouverture-decouvrir">
        <h1>Le cinéma,<br><em>simplement.</em></h1>
        <p class="intro-decouvrir">
            Marre de la guerre des notes sur 5 ou sur 10, fatigué(e) de ne pas avoir de réels échanges
            autour d'un film ? Vous voulez juste partager votre avis et découvrir celui des autres ?
            <strong>Cinévo</strong> est fait pour vous.
        </p>
        <p class="intro-decouvrir">
            Concrètement, c'est un site où l'on écrit ce qu'on a pensé d'un film, avec ses propres mots,
            sans jamais le réduire à un chiffre. Un film récent, un classique que tout le monde connaît
            sauf vous, une pépite oubliée : tout a sa place, du moment que c'est sincère. Pas d'algorithme
            qui décide ce que vous devez voir, pas de classement qui pousse les mêmes films en avant :
            juste des avis triés par date, et la liberté de vous perdre dedans comme bon vous semble.
        </p>
    </div>

    <hr class="separateur">

    <section>
        <span class="label-section surligne">Le site, en quatre gestes</span>
        <h2>Comment ça marche ?</h2>

        <div class="grille-etapes">
            <div class="etape-decouvrir">
                <span class="etape-numero">01</span>
                <h3>Cherchez un film</h3>
                <p>
                    Tapez un titre dans la barre de recherche : Cinévo va chercher parmi des milliers de
                    films, des dernières sorties aux classiques. Vous tombez sur sa fiche, avec les avis
                    déjà publiés dessus si quelqu'un en a écrit un.
                </p>
            </div>
            <div class="etape-decouvrir">
                <span class="etape-numero">02</span>
                <h3>Écrivez votre avis</h3>
                <p>
                    Deux phrases ou dix paragraphes, peu importe : il n'y a pas de longueur minimale
                    imposée, ni de note à cocher. Juste ce que le film vous a fait, raconté avec vos mots.
                </p>
            </div>
            <div class="etape-decouvrir">
                <span class="etape-numero">03</span>
                <h3>Suivez les discussions</h3>
                <p>
                    Chaque film ayant reçu au moins un avis devient une discussion sur la page
                    <a href="avis.php">Discussions</a>. Les plus récentes remontent en tête, pour suivre
                    la conversation en train de se faire, sans jamais de classement par popularité.
                </p>
            </div>
            <div class="etape-decouvrir">
                <span class="etape-numero">04</span>
                <h3>Laissez-vous surprendre</h3>
                <p>
                    Pas d'idée précise ? La page <a href="hasard.php">Au hasard</a> tire cinq films au
                    sort à chaque visite. Aucun algorithme de recommandation derrière : juste le hasard,
                    et peut-être une bonne surprise au bout.
                </p>
            </div>
        </div>

        <p class="source" style="margin-top:28px;">
            Une fois inscrit(e), <strong>Mon fil</strong> rassemble les avis de toute la communauté au
            même endroit, et <strong>Mes avis</strong> garde une trace de tout ce que vous avez écrit.
        </p>
    </section>

    <hr class="separateur">

    <section>
        <span class="label-section surligne">Vos questions, nos réponses</span>

        <div class="grille-questions">
            <details class="question-decouvrir">
                <summary><span class="signe"></span>Je peux lire les avis sans créer de compte ?</summary>
                <p>Oui, tout est en accès libre. Un compte ne sert qu'à écrire les vôtres.</p>
            </details>
            <details class="question-decouvrir">
                <summary><span class="signe"></span>Il faut écrire un roman pour publier un avis ?</summary>
                <p>Pas du tout. Deux phrases suffisent largement, personne ne compte les mots.</p>
            </details>
            <details class="question-decouvrir">
                <summary><span class="signe"></span>Vous n'allez pas me ressortir les mêmes films que partout ailleurs ?</summary>
                <p>Non : pas d'algorithme ici. Les avis s'affichent par date, jamais par popularité.</p>
            </details>
            <details class="question-decouvrir">
                <summary><span class="signe"></span>Il faut s'y connaître en cinéma pour venir ?</summary>
                <p>Pas du tout. Ni jargon, ni ton donneur de leçons : juste des gens qui aiment le cinéma.</p>
            </details>
        </div>
    </section>

    <hr class="separateur">

    <section class="cta-decouvrir">
        <h2>Prêt(e) à rejoindre la communauté Cinévo ?</h2>
        <p>Créer un compte pour publier vos critiques de films est gratuit, sans algorithme, et ça prend 30 secondes.</p>
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
