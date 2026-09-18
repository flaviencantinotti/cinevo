<?php
$page = 'a-propos';
require_once 'includes/auth.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Pourquoi Cinévo existe, qui l'a écrit et la seule règle qui y tient : aucun ton snob toléré. Un site d'avis cinéma sans notes ni algorithme.">
    <title>À propos de Cinévo · Pourquoi ce site existe</title>
    <link rel="stylesheet" type="text/css" href="css/style.css?v=92">
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="contenu">

    <div class="entete-page">
        <h1>L'histoire derrière Cinévo</h1>
        <p class="intro">
            Ici, personne ne met de note à un film. On l'écrit, on en discute, et c'est tout.
            Pas d'algorithme, pas de publicité, rien à payer.
        </p>
    </div>

    <hr class="separateur">

    <section>
        <h2>Pourquoi ce site existe</h2>

        <div class="concept-layout" style="margin-top:20px;">
            <div class="concept-texte">
                <p>
                    Sur la plupart des sites de cinéma, un film finit en note sur cinq. Les avis les
                    plus populaires remontent, les autres disparaissent, et au bout d'un moment on vous
                    propose toujours les mêmes titres.
                </p>
                <p style="margin-top:14px;">
                    Cinévo prend le problème à l'envers : on lit ce que quelqu'un a écrit, pas ce
                    qu'une moyenne a calculé. Chaque film commenté ouvre un fil dans
                    <a href="avis.php">Discussions</a>, du plus récent au plus ancien.
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

    <div class="corps-texte" style="margin-top:40px;">

        <section>
            <h2>Pour qui Cinévo est conçu ?</h2>
            <p>
                Étudiant en école de cinéma, retraité curieux, parent qui cherche un film pour ce
                soir : il n'y a pas de profil type. Vous n'avez rien à prouver pour écrire ici.
            </p>
            <p>
                Une seule règle tient tout le reste : aucun ton snob toléré. Si vous avez envie de
                mépriser le goût de quelqu'un, vous êtes au mauvais endroit.
            </p>
        </section>

        <section>
            <h2>Comment le site vit ?</h2>
            <p>
                Le site est <strong>gratuit</strong> et le restera. Aucun abonnement, aucune version
                premium, aucune fonctionnalité réservée à ceux qui paient. L'hébergement est à ma
                charge.
            </p>
            <p>
                Si le site vous sert et que vous voulez participer aux frais, un don est possible.
                Il ne vous donne droit à rien de plus : vous restez exactement le même membre.
            </p>
        </section>

        <section>
            <h2>Qui est derrière ?</h2>
            <p>
                Je m'appelle <strong>Flavien</strong>, je développe depuis Besançon, et Cinévo est né
                en mai 2026 pendant ma formation au titre RNCP 37674 (Développeur Web et Web Mobile,
                DWWM).
            </p>
            <p>
                Une question, une idée, un bug repéré ? Écrivez à
                <a href="mailto:hello@cinevo.fr">hello@cinevo.fr</a> ou passez par la
                <a href="contact.php">page contact</a>. Vous trouverez le cadre légal dans les
                <a href="mentions-legales.php">mentions légales</a>.
            </p>
        </section>

    </div>

    <hr class="separateur">

    <section class="cta-decouvrir">
        <h2>Envie de rejoindre la conversation ?</h2>
        <p>Créer un compte prend trente secondes. C'est gratuit, et ça le restera.</p>
        <div class="hero-boutons" style="margin-top:20px;">
            <a href="inscription.php" class="btn-rouge">Rejoindre Cinévo</a>
            <a href="decouvrir.php" class="btn-blanc">En savoir plus</a>
        </div>
    </section>

</main>

<?php include 'includes/footer.php'; ?>

<script src="js/scripts.js"></script>
</body>
</html>
