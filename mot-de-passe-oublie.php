<?php
$page = 'mot-de-passe-oublie';
require_once 'includes/db.php';
require_once 'includes/auth.php';

$erreur    = '';
$email     = '';
$lienReset = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (!csrf_verifie($_POST['csrf_token'] ?? null)) {
        $erreur = 'Requête invalide, merci de réessayer.';
    } elseif (!baseDisponible()) {
        $erreur = 'Impossible pour le moment : la base de données ne répond pas.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = 'Cette adresse e-mail n\'est pas valide.';
    } else {
        $stmt = $conn->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $utilisateur = $stmt->get_result()->fetch_assoc();

        if (!$utilisateur) {
            $erreur = 'Aucun compte n\'est associé à cette adresse e-mail.';
        } else {
            $jeton     = genererJetonReinitialisation($conn, (int) $utilisateur['id']);
            $lienReset = 'reinitialiser-mdp.php?token=' . $jeton;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, follow">
    <link rel="stylesheet" type="text/css" href="css/style.css?v=33">
    <title>Cinévo · Mot de passe oublié</title>
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="contenu">
    <div class="boite-connexion">
        <h1>Mot de passe oublié</h1>

        <?php if ($lienReset): ?>

            <p class="intro" style="text-align:center; margin-top:16px;">
                Un lien de réinitialisation a été généré pour ce compte.
            </p>
            <p class="source" style="margin-top:16px; text-align:center;">
                En production, ce lien vous serait envoyé par e-mail. Sur cette installation
                locale, sans serveur d'envoi configuré, le voici directement :
            </p>
            <p style="text-align:center; margin-top:12px; word-break:break-all;">
                <a href="<?= htmlspecialchars($lienReset) ?>"><?= htmlspecialchars($lienReset) ?></a>
            </p>
            <p class="source" style="margin-top:16px; text-align:center;">Valable une heure.</p>

        <?php else: ?>

            <p class="intro" style="text-align:center; margin-top:16px;">
                Indiquez votre adresse e-mail, on vous envoie un lien pour en choisir un nouveau.
            </p>

            <?php if ($erreur): ?>
                <p class="message-erreur" style="text-align:center;"><?= htmlspecialchars($erreur) ?></p>
            <?php endif; ?>

            <form action="mot-de-passe-oublie.php" method="POST">
                <?= csrf_champ() ?>
                <div class="groupe-champ">
                    <label for="email">Adresse e-mail</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required autocomplete="email">
                </div>
                <input type="submit" value="Envoyer le lien">
            </form>

        <?php endif; ?>

        <p class="lien-alternatif"><a href="connexion.php">Retour à la connexion</a></p>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

<script src="js/scripts.js"></script>
</body>
</html>
