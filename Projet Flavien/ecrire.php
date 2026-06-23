<?php $page = 'ecrire'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>Cinévo — Écrire un avis</title>
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="contenu">

    <div class="ecrire-header">
        <span class="bibliotheque">Partager votre ressenti</span>
        <h1>Écrire un avis</h1>
        <p class="text-lede">Pas de note. Pas de format imposé. Juste ce que le film vous a fait.</p>
    </div>

    <hr class="diviseur">

    <form class="ecrire-form" action="home.php" method="post">

        <div class="ecrire-champ">
            <label for="film">Film ou série</label>
            <input type="text" id="film" name="film" placeholder="Chercher un titre..." autocomplete="off" required>
        </div>

        <div class="ecrire-champ">
            <label for="titre">Titre de votre avis <span class="champ-opt">(facultatif)</span></label>
            <input type="text" id="titre" name="titre" placeholder="Une phrase qui résume votre ressenti...">
        </div>

        <div class="ecrire-champ">
            <label for="avis">Votre avis <span class="champ-min">20 caractères minimum</span></label>
            <textarea id="avis" name="avis" placeholder="Écrivez librement ce que ce film vous a fait..." rows="8" required minlength="20"></textarea>
            <div class="ecrire-compteur"><span id="compteur">0</span> caractères</div>
        </div>

        <div class="ecrire-actions">
            <a href="fiche.php">
                <button type="button" class="rejoindre">Annuler</button>
            </a>
            <button type="submit" class="terra" id="btnPublier">Publier l'avis</button>
        </div>

    </form>

</main>

<?php include 'includes/footer.php'; ?>

<script src="js/scripts.js"></script>
</body>
</html>
