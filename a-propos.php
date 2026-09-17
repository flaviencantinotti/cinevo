<?php
$page = 'a-propos';
require_once 'includes/auth.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="L'histoire de Cinévo : pourquoi ce site d'avis cinéma sans notes existe, qui l'a créé et la règle qui le définit, aucun ton snob toléré.">
    <title>À propos de Cinévo · Notre histoire et notre mission</title>
    <link rel="stylesheet" type="text/css" href="css/style.css?v=64">
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="contenu">

    <div class="entete-page">
        <span class="label-section">À propos de Cinévo</span>
        <h1>L'histoire derrière <em>Cinévo.</em></h1>
        <p class="intro">
            Cinévo est un site écrit à la main, par des passionnés, pour des passionnés : on n'y
            met pas de note, pas d'algorithme et c'est entièrement gratuit.
        </p>
    </div>

    <hr class="separateur">

    <section>
        <span class="label-section">Pourquoi ce site existe</span>

        <div class="concept-layout" style="margin-top:20px;">
            <div class="concept-texte">
                <p>
                    Les sites cinéphiles existants (Letterboxd, SensCritique) réduisent le film à un
                    chiffre, hiérarchisent les avis selon la popularité et installent une bulle
                    algorithmique qui appauvrit la découverte.
                </p>
                <p style="margin-top:14px;">
                    Cinévo répond à un besoin simple : proposer une vraie conversation cinéphile
                    écrite, qui valorise chaque avis pour ce qu'il dit et non pour la note qui
                    l'accompagne. Cette conversation, c'est <a href="avis.php">Discussions</a>, où
                    chaque film ayant reçu un avis devient un fil, trié du plus récent au plus ancien.
                </p>
            </div>
            <div class="carte-citation">
                <p>
                    « J'écris pour me souvenir de ce que j'ai vu.<br>
                    Si ça aide quelqu'un d'autre, tant mieux. »
                </p>
            </div>
        </div>
    </section>

    <hr class="separateur">

    <section>
        <span class="label-section">Ce qu'on a choisi de ne pas faire</span>
        <h2 style="margin-top:8px;">Ce qu'on ne trouvera jamais ici</h2>

        <div class="grille-caracteristiques">
            <div class="carte-caracteristique">
                <div class="titre-carac">Pas de note sur cinq, pas d'étoiles, pas de pouce</div>
                <p>Un film ne se résume pas à un chiffre.</p>
            </div>
            <div class="carte-caracteristique">
                <div class="titre-carac">Pas de classement « tendance » ni de « top de la semaine »</div>
                <p>Tri chronologique strict. Du plus récent au plus ancien.</p>
            </div>
            <div class="carte-caracteristique">
                <div class="titre-carac">Pas de recommandation algorithmique</div>
                <p>Vous lisez parce que quelqu'un a écrit, pas parce qu'une machine a calculé.</p>
            </div>
            <div class="carte-caracteristique">
                <div class="titre-carac">Pas de compteur de films vus, pas de badges, pas de niveaux</div>
                <p>Vous n'êtes pas une statistique.</p>
            </div>
            <div class="carte-caracteristique">
                <div class="titre-carac">Pas de publicité, pas de tracking commercial</div>
                <p>Voir notre <a href="cookies.php">politique cookies</a>.</p>
            </div>
        </div>
    </section>

    <hr class="separateur">

    <div class="corps-texte" style="margin-top:40px;">

        <section>
            <h2>Pour qui Cinévo est conçu ?</h2>
            <p>
                Cinévo est fait pour ceux qui aiment le cinéma, sans distinction : étudiant en école
                de cinéma, retraité curieux, parent fatigué qui cherche un film pour ce soir, ou
                cinéphile qui doute encore de son propre avis, ce site est pensé pour vous comme
                pour les autres.
            </p>
            <p>
                Tout le reste découle d'une seule règle : aucun ton snob toléré. Si vous ressentez
                le besoin de mépriser le goût d'un autre membre, vous êtes probablement au mauvais
                endroit.
            </p>
        </section>

        <section>
            <h2>Comment le site vit ?</h2>
            <p>
                Cinévo est et restera <strong>100 % gratuit</strong>, sans version premium, sans
                abonnement et sans fonctionnalité payante : l'hébergement reste entièrement à la
                charge de l'auteur du site.
            </p>
            <p>
                Si Cinévo vous est utile et que vous voulez contribuer aux frais, un don reste
                possible, sans contrepartie ni statut particulier sur le site. Vous restez
                exactement le même membre qu'avant.
            </p>
        </section>

        <section>
            <h2>Qui est derrière ?</h2>
            <p>
                Cinévo est conçu et développé par <strong>Flavien</strong>, lancé depuis Besançon en
                mai 2026, dans le cadre d'un projet de validation du titre RNCP 37674 (Développeur Web
                et Web Mobile, DWWM).
            </p>
            <p>
                Pour toute question, suggestion ou remarque, écrivez à
                <a href="mailto:hello@cinevo.fr">hello@cinevo.fr</a> ou passez par la
                <a href="contact.php">page contact</a> ; les détails légaux, eux, sont dans les
                <a href="mentions-legales.php">mentions légales</a>.
            </p>
        </section>

    </div>

    <hr class="separateur">

    <section class="cta-decouvrir">
        <h2>Envie de rejoindre la conversation ?</h2>
        <p>Créer un compte pour publier vos critiques de films est gratuit, sans algorithme et ça prend 30 secondes.</p>
        <div class="hero-boutons" style="margin-top:20px;">
            <a href="inscription.php">
                <button class="btn-rouge">Rejoindre Cinévo</button>
            </a>
            <a href="decouvrir.php">
                <button class="btn-blanc">En savoir plus</button>
            </a>
        </div>
    </section>

</main>

<?php include 'includes/footer.php'; ?>

<script src="js/scripts.js"></script>
</body>
</html>
