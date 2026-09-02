<?php
$page = 'mentions-legales';
require_once 'includes/auth.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Mentions légales de Cinévo : éditeur du site, hébergement, propriété intellectuelle et données personnelles.">
    <meta name="robots" content="noindex, follow">
    <title>Cinévo — Mentions légales</title>
    <link rel="stylesheet" type="text/css" href="css/style.css?v=2">
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="contenu">
  <div class="page-editoriale">

    <div class="chapeau">Informations légales</div>
    <h1>Mentions <em>légales.</em></h1>
    <p class="intro">
        Tout ce que la loi nous oblige à vous dire — et que nous vous disons aussi clairement que possible.
    </p>

    <div class="meta-article">
        <div><strong>Dernière mise à jour</strong>13 mai 2026</div>
        <div><strong>Conformité</strong>LCEN · RGPD · CNIL</div>
    </div>

    <div class="corps-texte">

        <section>
            <h2><span class="num">01</span> Éditeur du site</h2>
            <dl class="liste-definitions">
                <dt>Nom</dt><dd>Flavien CANTINOTTI. — entrepreneur individuel</dd>
                <dt>Statut</dt><dd>Micro-entreprise</dd>
                <dt>SIRET</dt><dd>012 345 678 90123</dd>
                <dt>Adresse</dt><dd>Besançon, France</dd>
                <dt>Email</dt><dd><a href="mailto:hello@cinevo.fr">hello@cinevo.fr</a></dd>
                <dt>Directeur de la publication</dt><dd>Flavien CANTINOTTI.</dd>
            </dl>
        </section>

        <section>
            <h2><span class="num">02</span> Hébergement</h2>
            <dl class="liste-definitions">
                <dt>Hébergeur</dt><dd>OVH SAS</dd>
                <dt>Adresse</dt><dd>2 rue Kellermann, 59100 Roubaix, France</dd>
                <dt>Téléphone</dt><dd>+33 9 72 10 10 07</dd>
                <dt>Site</dt><dd><a href="https://www.ovh.com" target="_blank" rel="noopener">www.ovh.com</a></dd>
            </dl>
        </section>

        <section>
            <h2><span class="num">03</span> Propriété intellectuelle</h2>
            <p>
                L'ensemble du site Cinévo — interface, code source, identité visuelle, textes
                éditoriaux — est la propriété de son éditeur, sauf mention contraire. Toute
                reproduction, même partielle, est soumise à autorisation préalable.
            </p>
            <p>
                Les <em>avis</em> publiés par les membres restent la propriété intellectuelle de
                leurs auteurs. En publiant sur Cinévo, vous accordez au site une licence
                non-exclusive d'affichage, sans transfert de propriété. Vous pouvez supprimer
                vos avis à tout moment depuis votre profil.
            </p>
            <p>
                Les <em>affiches, titres, et données techniques</em> des films cités sont la
                propriété de leurs ayants droit respectifs et sont reproduits dans un cadre
                d'information éditoriale (article L.122-5 du Code de la propriété intellectuelle).
            </p>
        </section>

        <section>
            <h2><span class="num">04</span> Responsabilité éditoriale</h2>
            <p>
                Cinévo héberge des avis rédigés par ses membres. Conformément à la loi pour la
                confiance dans l'économie numérique (LCEN), l'éditeur n'a pas d'obligation
                générale de surveillance des contenus, mais s'engage à retirer promptement tout
                contenu manifestement illicite signalé.
            </p>
            <p>
                Pour signaler un contenu : <a href="mailto:hello@cinevo.fr">hello@cinevo.fr</a>.
                Décrivez la nature précise du problème — diffamation, atteinte au droit d'auteur,
                propos haineux. Nous traitons sous 72 heures.
            </p>
        </section>

        <section>
            <h2><span class="num">05</span> Données personnelles</h2>
            <p>
                Cinévo collecte le minimum nécessaire au fonctionnement du service : un pseudo,
                une adresse e-mail, un mot de passe haché. Aucune donnée n'est revendue ni
                partagée avec un tiers commercial.
            </p>
            <p>
                Vous disposez d'un droit d'accès, de rectification, de portabilité, d'opposition
                et de suppression de vos données. Depuis votre page profil, vous
                pouvez à tout moment exporter vos avis ou supprimer votre compte (suppression
                définitive sous 30 jours).
            </p>
            <p>
                Pour toute question liée au RGPD : <a href="mailto:hello@cinevo.fr">hello@cinevo.fr</a>.
                Voir aussi notre <a href="cookies.php">politique cookies</a>.
            </p>
        </section>

        <section>
            <h2><span class="num">06</span> Droit applicable</h2>
            <p>
                Les présentes mentions sont régies par le droit français. En cas de litige, et
                après tentative de résolution amiable, les tribunaux français seront seuls
                compétents.
            </p>
        </section>

    </div>

  </div>
</main>

<?php include 'includes/footer.php'; ?>

<script src="js/scripts.js"></script>
</body>
</html>
