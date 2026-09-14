<?php
$page = 'contact';
require_once 'includes/auth.php';
require_once 'includes/db.php';

$erreur  = '';
$succes  = false;
$nom     = '';
$email   = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verifie($_POST['csrf_token'] ?? null)) {
        $erreur = 'Requête invalide, merci de réessayer.';
    } else {
        $nom     = trim($_POST['nom'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if ($nom === '' || $email === '' || $message === '') {
            $erreur = 'Merci de remplir tous les champs.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erreur = 'Cette adresse e-mail n\'est pas valide.';
        } elseif (!baseDisponible()) {
            $erreur = 'Envoi impossible : la base de données ne répond pas. '
                . 'Écrivez-nous directement à hello@cinevo.fr en attendant.';
        } else {
            $stmt = $conn->prepare("INSERT INTO messages_contact (nom, email, message) VALUES (?, ?, ?)");
            $stmt->bind_param('sss', $nom, $email, $message);
            $stmt->execute();

            $succes  = true;
            $nom = $email = $message = '';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Une question, une suggestion ou une remarque sur Cinévo ? Contactez l'équipe directement via ce formulaire.">
    <link rel="stylesheet" type="text/css" href="css/style.css?v=29">
    <title>Contact · Cinévo</title>
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="contenu">

    <span class="label-section surligne">Nous écrire</span>
    <h1>Contact</h1>
    <p class="intro">Une question, une suggestion, un problème à signaler ? On vous répond.</p>

    <hr class="separateur">

    <?php if ($succes): ?>
        <p class="message-succes">Votre message a bien été envoyé, merci ! On vous répond au plus vite.</p>
    <?php elseif ($erreur): ?>
        <p class="message-erreur"><?= htmlspecialchars($erreur) ?></p>
    <?php endif; ?>

    <form class="formulaire-contact" action="contact.php" method="post">
        <?= csrf_champ() ?>
        <div class="champ">
            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" placeholder="Votre nom" value="<?= htmlspecialchars($nom) ?>" required>
        </div>
        <div class="champ">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" placeholder="votre@email.fr" value="<?= htmlspecialchars($email) ?>" required>
        </div>
        <div class="champ">
            <label for="message">Message</label>
            <textarea id="message" name="message" placeholder="Votre message..." rows="6" required><?= htmlspecialchars($message) ?></textarea>
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
