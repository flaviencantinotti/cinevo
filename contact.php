<?php
$page = 'contact';
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/validation.php';

$erreur  = '';
$succes  = false;
$nom     = '';
$email   = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verifie_post()) {
        $erreur = ERREUR_CSRF;
    } else {
        $nom     = trim($_POST['nom'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $message = trim($_POST['message'] ?? '');

        $emailInvalide = erreurEmail($email);

        if ($nom === '' || $email === '' || $message === '') {
            $erreur = 'Merci de remplir tous les champs.';
        } elseif ($emailInvalide) {
            $erreur = $emailInvalide;
        } elseif (!baseDisponible()) {
            $erreur = 'Envoi impossible : la base de données ne répond pas. '
                . 'Écrivez-nous directement à hello@cinevo.fr en attendant.';
        } else {
            enregistrerMessageContact($conn, $nom, $email, $message);

            $succes  = true;
            $nom = $email = $message = '';
        }
    }
}

$meta = [
    'titre'       => 'Contact · Cinévo',
    'description' => 'Une question, une suggestion ou une remarque sur Cinévo ? Contactez l\'équipe directement via ce formulaire.',
];
include 'includes/debut-page.php';
?>

<main class="contenu">

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
            <button type="submit" class="btn-rouge">Envoyer</button>
        </div>
    </form>

    <p class="lien-alternatif" style="margin-top:24px;">
        Vous pouvez aussi nous écrire directement à <a href="mailto:hello@cinevo.fr">hello@cinevo.fr</a>.
    </p>

</main>

<?php include 'includes/fin-page.php'; ?>
