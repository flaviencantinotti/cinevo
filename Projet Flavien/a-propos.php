<?php $page = 'a-propos'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinévo — À propos</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="contenu">

    <div class="text-kicker">À propos de Cinévo</div>
    <h1>Le cinéma, <em>comme on en parle.</em></h1>
    <p class="text-lede">
        Cinévo est un site écrit à la main, par des passionnés, pour des passionnés.
        Pas de note. Pas d'algorithme. Gratuit.
    </p>

    <div class="text-meta">
        <div><strong>Lancé</strong>Besançon · mai 2026</div>
        <div><strong>Auteur</strong>Flavien · projet personnel</div>
        <div><strong>Statut</strong>100 % gratuit · à but non lucratif</div>
    </div>

    <div class="text-body">

        <section>
            <h2><span class="num">01</span> Pourquoi ce site existe ?</h2>
            <p>
                Les sites cinéphiles existants — Letterboxd, SensCritique — réduisent le film à un
                chiffre, hiérarchisent les avis selon la popularité, et installent une bulle
                algorithmique qui appauvrit la découverte.
            </p>
            <p>
                Cinévo répond à un besoin simple : <em>une conversation cinéphile écrite</em> qui
                valorise chaque avis pour ce qu'il dit, pas pour la note qui l'accompagne.
                Trois lignes valent autant qu'une page, si elles sont sincères.
            </p>
            <blockquote class="pull">
                « J'écris pour me souvenir de ce que j'ai vu.<br>
                Si ça aide quelqu'un d'autre, tant mieux. »
            </blockquote>
        </section>

        <section>
            <h2><span class="num">02</span> Ce qu'on ne trouvera jamais ici !</h2>
            <p>Cinévo est défini par ce qu'on a choisi de ne pas y mettre :</p>
            <ul>
                <li>
                    Pas de note sur cinq, pas d'étoiles, pas de pouce
                    <em>Un film ne se résume pas à un chiffre.</em>
                </li>
                <li>
                    Pas de classement « tendance » ni de « top de la semaine »
                    <em>Tri chronologique strict. Du plus récent au plus ancien.</em>
                </li>
                <li>
                    Pas de recommandation algorithmique
                    <em>Vous lisez parce que quelqu'un a écrit — pas parce qu'une machine a calculé.</em>
                </li>
                <li>
                    Pas de compteur de films vus, pas de badges, pas de niveaux
                    <em>Vous n'êtes pas une statistique.</em>
                </li>
                <li>
                    Pas de publicité, pas de tracking commercial
                    <em>Voir notre <a href="cookies.php">politique cookies</a>.</em>
                </li>
            </ul>
        </section>

        <section>
            <h2><span class="num">03</span> Pour qui Cinévo est conçu ?</h2>
            <p>
                Pour ceux qui aiment le cinéma — point. Que vous soyez étudiant en école de cinéma,
                retraité curieux, parent fatigué qui cherche un film pour ce soir ou cinéphile
                à l'imposteur — Cinévo est conçu pour vous, et pour les autres en même temps.
            </p>
            <p>
                Une règle, une seule, qui découle de tout le reste : <em>aucun ton snob toléré</em>.
                Si vous ressentez le besoin de mépriser le goût d'un autre membre, vous êtes
                probablement au mauvais endroit.
            </p>
        </section>

        <section>
            <h2><span class="num">04</span> Comment le site vit ?</h2>
            <p>
                Cinévo est et restera <strong>100 % gratuit</strong>. Pas de version premium, pas
                d'abonnement, pas de fonctionnalité payante. L'hébergement est à la charge de
                l'auteur du site.
            </p>
            <p>
                Si Cinévo vous est utile et que vous souhaitez contribuer aux frais, un
                don est possible — sans contrepartie, sans
                statut particulier sur le site. Vous restez exactement le même membre.
            </p>
        </section>

        <section>
            <h2><span class="num">05</span> Qui est derrière ?</h2>
            <p>
                Cinévo est conçu et développé par <strong>Flavien</strong>, basé à Besançon, dans
                le cadre d'un projet de validation du titre RNCP 37674 — Développeur Web et
                Web Mobile (DWWM).
            </p>
            <p>
                Pour toute question, suggestion, ou remarque : <a href="mailto:hello@cinevo.fr">hello@cinevo.fr</a>.
                Pour les détails légaux, voir les <a href="mentions-legales.php">mentions légales</a>.
                Pour nous contacter via le formulaire : <a href="contact.php">page contact</a>.
            </p>
        </section>

    </div>

</main>

<?php include 'includes/footer.php'; ?>

<script src="js/scripts.js"></script>
</body>
</html>
