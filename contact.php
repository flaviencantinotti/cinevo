<?php $page = 'contact'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>Cinévo — Contact</title>
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="contenu">

    <div class="chapeau">Nous écrire</div>
    <h1>Contact</h1>
    <p class="intro">Une question, une suggestion, un problème à signaler ? On vous répond.</p>

    <hr class="separateur">

    <form class="formulaire-contact" action="contact.php" method="post">
        <div class="champ">
            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" placeholder="Votre nom" required>
        </div>
        <div class="champ">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" placeholder="votre@email.fr" required>
        </div>
        <div class="champ">
            <label for="message">Message</label>
            <textarea id="message" name="message" placeholder="Votre message..." rows="6" required></textarea>
        </div>
        <div class="boutons-form">
            <input type="submit" value="Envoyer">
        </div>
    </form>

    <p class="lien-alternatif" style="margin-top:24px;">
        Vous pouvez aussi nous écrire directement à <a href="mailto:hello@cinevo.fr">hello@cinevo.fr</a>.
    </p>

</main>

<?php include 'includes/footer.php'; ?>

<script src="js/scripts.js"></script>
</body>
</html>
