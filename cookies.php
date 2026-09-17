<?php
$page = 'cookies';
require_once 'includes/auth.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="La politique cookies de Cinévo : quels cookies sont utilisés, pourquoi et comment les gérer ou les supprimer.">
    <meta name="robots" content="noindex, follow">
    <link rel="stylesheet" type="text/css" href="css/style.css?v=88">
    <title>Cinévo · Politique cookies</title>
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="contenu">
  <div class="page-editoriale">

    <h1>Politique cookies.</h1>
    <p class="intro">
        Comment Cinévo utilise les cookies, et surtout ce qu'on n'utilise pas.
    </p>

    <div class="meta-article">
        <div><strong>Dernière mise à jour</strong>13 mai 2026</div>
        <div><strong>Conformité</strong>RGPD · CNIL</div>
    </div>

    <div class="corps-texte">

        <section>
            <h2>Qu'est-ce qu'un cookie ?</h2>
            <p>
                Un cookie est un petit fichier déposé par un site sur le terminal que vous
                utilisez (ordinateur, tablette, smartphone). Cinévo n'en dépose qu'un seul,
                celui qui vous garde connecté à votre compte d'une page à l'autre.
            </p>
            <p>
                Un cookie possède une durée de validité variable. Celui de Cinévo est un
                cookie de session : il expire dès que vous fermez votre navigateur. En tout
                état de cause, aucun cookie déposé ici ne dépasse 13 mois.
            </p>
        </section>

        <section>
            <h2>Paramétrage des cookies</h2>
            <ul>
                <li>
                    <strong>Cookies strictement nécessaires.</strong>
                    Le seul que Cinévo dépose : le cookie de session, qui vous garde connecté
                    d'une page à l'autre. Il ne nécessite pas votre consentement, ne peut pas
                    être désactivé, et disparaît à la fermeture du navigateur.
                </li>
                <li>
                    <strong>Cookies de mesure d'audience.</strong>
                    Cinévo n'en dépose aucun : aucune statistique de navigation n'est collectée,
                    ni par nous ni par un service tiers.
                </li>
                <li>
                    <strong>Cookies marketing ou publicitaires.</strong>
                    Cinévo n'en dépose aucun non plus : pas de publicité personnalisée,
                    pas de traceur commercial, rien à refuser.
                </li>
            </ul>
        </section>

        <section>
            <h2>Gestion et suppression</h2>
            <p>
                Vous pouvez paramétrer votre navigateur de manière à refuser les cookies sur votre
                appareil ou à demander votre consentement à chaque dépôt. Vous pouvez également
                supprimer les cookies déjà enregistrés depuis les paramètres de votre navigateur.
            </p>
            <p>
                La désactivation des cookies strictement nécessaires peut conduire à des
                restrictions fonctionnelles (déconnexion automatique, perte de préférences).
            </p>
            <p>
                Pour toute question : <a href="contact.php">formulaire de contact</a> ou
                <a href="mailto:hello@cinevo.fr">hello@cinevo.fr</a>.
                Voir aussi les <a href="mentions-legales.php">mentions légales</a>.
            </p>
        </section>

    </div>

  </div>
</main>

<?php include 'includes/footer.php'; ?>

<script src="js/scripts.js"></script>
</body>
</html>
