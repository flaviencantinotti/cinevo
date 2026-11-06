<?php
$page = 'inscription';
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/validation.php';

$erreur   = '';
$username = '';
$email    = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');

    if (!csrf_verifie_post()) {
        $erreur = ERREUR_CSRF;
    } elseif (!baseDisponible()) {
        $erreur = 'Inscription impossible : la base de données ne répond pas.';
    } else {
        $password = trim($_POST['password'] ?? '');
        $confirm  = trim($_POST['confirm'] ?? '');

        $emailInvalide = erreurEmail($email);
        $mdpInvalide   = erreurMotDePasse($password, $confirm);

        if ($username === '' || $email === '') {
            $erreur = 'Merci de remplir tous les champs.';
        } elseif (mb_strlen($username) > 50) {
            $erreur = 'Le nom d\'utilisateur ne doit pas dépasser 50 caractères.';
        } elseif ($emailInvalide) {
            $erreur = $emailInvalide;
        } elseif ($mdpInvalide) {
            $erreur = $mdpInvalide;
        } else {
            if (inscrire($conn, $username, $email, $password)) {
                connecter($conn, $email, $password);
                header('Location: home.php');
                exit;
            } else {
                $erreur = 'Ce nom d\'utilisateur ou cet email est déjà utilisé.';
            }
        }
    }
}

$meta = [
    'titre'       => 'Inscription gratuite · Cinévo',
    'description' => 'Créez votre compte Cinévo gratuitement et rejoignez une communauté de cinéphiles qui écrit ses avis sur les films, sans notes ni algorithme.',
];
include 'includes/debut-page.php';
?>

<main class="contenu">
    <div class="boite-inscription">
        <h1>Inscrivez-vous</h1>

        <?php if ($erreur): ?>
            <p class="message-erreur"><?= htmlspecialchars($erreur) ?></p>
        <?php endif; ?>

        <form class="formulaire" action="inscription.php" method="POST">
            <?= csrf_champ() ?>
            <div class="champ">
                <label for="username">Nom d'utilisateur</label>
                <input type="text" id="username" name="username" maxlength="50" value="<?= htmlspecialchars($username) ?>" required>
            </div>
            <div class="champ">
                <label for="email">Adresse e-mail</label>
                <input type="email" id="email" name="email" maxlength="100" value="<?= htmlspecialchars($email) ?>" required autocomplete="email">
            </div>
            <div class="champ">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" minlength="<?= MOT_DE_PASSE_LONGUEUR_MIN ?>" required autocomplete="new-password">
            </div>
            <div class="champ">
                <label for="confirm">Confirmer le mot de passe</label>
                <input type="password" id="confirm" name="confirm" minlength="<?= MOT_DE_PASSE_LONGUEUR_MIN ?>" required autocomplete="new-password">
            </div>

            <button type="submit" class="btn-rouge">Créer mon compte</button>
        </form>

        <p class="source">
            En vous inscrivant, vous acceptez les <a href="conditions-generales.php">conditions générales d'utilisation</a>.
        </p>

        <p class="lien-alternatif">Déjà un compte ? <a href="connexion.php">Se connecter</a></p>
    </div>
</main>

<?php include 'includes/fin-page.php'; ?>
