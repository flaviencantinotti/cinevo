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

    <div class="text-kicker">Nous écrire</div>
    <h1>Contact</h1>
    <p class="text-lede">Une question, une suggestion, un problème à signaler ? On vous répond.</p>

    <hr class="diviseur">

    <form id="form" class="contact-form" action="contact.php" method="post">
        <div class="contact-champ">
            <label for="c-name">Nom</label>
            <input id="c-name" type="text" name="nom" placeholder="Votre nom" required>
        </div>
        <div class="contact-champ">
            <label for="c-email">E-mail</label>
            <input id="c-email" type="email" name="email" placeholder="votre@email.fr" required>
        </div>
        <div class="contact-champ">
            <label for="c-message">Message</label>
            <textarea id="c-message" name="message" placeholder="Votre message..." rows="6" required></textarea>
        </div>
        <div class="contact-actions">
            <input id="submit" type="submit" value="Envoyer">
        </div>
    </form>

    <p class="contact-alt">
        Vous pouvez aussi nous écrire directement à <a href="mailto:hello@cinevo.fr">hello@cinevo.fr</a>.
    </p>

</main>

<?php include 'includes/footer.php'; ?>

<script src="js/scripts.js"></script>
</body>
</html>
