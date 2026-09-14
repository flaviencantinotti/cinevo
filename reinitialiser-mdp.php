<?php
$page = 'reinitialiser-mdp';
require_once 'includes/db.php';
require_once 'includes/auth.php';

$jeton  = $_GET['token'] ?? ($_POST['token'] ?? '');
$erreur = '';
$succes = false;

$utilisateurId = (baseDisponible() && $jeton !== '') ? utilisateurDepuisJeton($conn, $jeton) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verifie($_POST['csrf_token'] ?? null)) {
        $erreur = 'Requête invalide, merci de réessayer.';
    } elseif (!baseDisponible()) {
        $erreur = 'Impossible pour le moment : la base de données ne répond pas.';
    } elseif (!$utilisateurId) {
        $erreur = 'Ce lien est invalide ou a expiré. Merci de refaire une demande.';
    } else {
        $password = trim($_POST['password'] ?? '');
        $confirm  = trim($_POST['confirm'] ?? '');

        if ($password !== $confirm) {
            $erreur = 'Les mots de passe ne correspondent pas.';
        } elseif (strlen($password) < 6) {
            $erreur = 'Le mot de passe doit faire au moins 6 caractères.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE utilisateurs SET mot_de_passe = ? WHERE id = ?");
            $stmt->bind_param('si', $hash, $utilisateurId);
            $stmt->execute();

            invaliderJeton($conn, $jeton);
            $succes = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" type="text/css" href="css/style.css?v=32">
    <title>Cinévo · Nouveau mot de passe</title>
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="contenu">
    <div class="boite-inscription">
        <h1>Nouveau mot de passe</h1>

        <?php if ($succes): ?>

            <p class="intro" style="text-align:center; margin-top:16px;">
                Votre mot de passe a été changé. Vous pouvez maintenant vous connecter.
            </p>
            <div class="hero-boutons" style="margin-top:20px; justify-content:center;">
                <a href="connexion.php"><button class="btn-rouge">Se connecter</button></a>
            </div>

        <?php elseif (!$utilisateurId): ?>

            <p class="intro" style="text-align:center; margin-top:16px;">
                Ce lien est invalide ou a expiré.
            </p>
            <?php if ($erreur): ?>
                <p class="message-erreur" style="text-align:center;"><?= htmlspecialchars($erreur) ?></p>
            <?php endif; ?>
            <div class="hero-boutons" style="margin-top:20px; justify-content:center;">
                <a href="mot-de-passe-oublie.php"><button class="btn-rouge">Refaire une demande</button></a>
            </div>

        <?php else: ?>

            <?php if ($erreur): ?>
                <p class="message-erreur" style="text-align:center;"><?= htmlspecialchars($erreur) ?></p>
            <?php endif; ?>

            <form class="formulaire" action="reinitialiser-mdp.php" method="POST">
                <?= csrf_champ() ?>
                <input type="hidden" name="token" value="<?= htmlspecialchars($jeton) ?>">

                <label for="password">Nouveau mot de passe</label>
                <input type="password" id="password" name="password" required autocomplete="new-password">

                <label for="confirm">Confirmer le mot de passe</label>
                <input type="password" id="confirm" name="confirm" required autocomplete="new-password">

                <button type="submit">Changer le mot de passe</button>
            </form>

        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

<script src="js/scripts.js"></script>
</body>
</html>
