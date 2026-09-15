<?php
$page = 'profil';
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/format.php';

if (!estConnecte()) {
    header('Location: connexion.php');
    exit;
}

$erreurEmail = '';
$succesEmail = '';
$erreurMdp   = '';
$succesMdp   = '';
$erreurPhoto = '';
$succesPhoto = '';

$utilisateurId = (int) $_SESSION['utilisateur_id'];

// Formats et taille acceptés pour la photo de profil. La limite affichée et
// vérifiée tient compte de upload_max_filesize : au-delà, PHP rejette l'envoi
// avant même que notre propre contrôle de taille s'exécute.
$typesAutorises  = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
$limiteServeur   = tailleIniEnOctets(ini_get('upload_max_filesize'));
$tailleMaxOctets = min(3 * 1024 * 1024, $limiteServeur);
$tailleMaxMo     = round($tailleMaxOctets / 1024 / 1024, 1);

if (!baseDisponible()) {
    $erreurEmail = 'Cette page est momentanément indisponible : la base de données ne répond pas.';
} else {
    $stmt = $conn->prepare("SELECT nom_utilisateur, email, mot_de_passe, photo_profil FROM utilisateurs WHERE id = ?");
    $stmt->bind_param('i', $utilisateurId);
    $stmt->execute();
    $utilisateur = $stmt->get_result()->fetch_assoc();

    if (!$utilisateur) {
        deconnecter();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_verifie($_POST['csrf_token'] ?? null)) {
        $erreurEmail = $erreurMdp = $erreurPhoto = 'Requête invalide, merci de réessayer.';
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'email') {

        $nouvelEmail       = trim($_POST['email'] ?? '');
        $motDePasseActuel  = trim($_POST['mot_de_passe_actuel'] ?? '');

        if (!password_verify($motDePasseActuel, $utilisateur['mot_de_passe'])) {
            $erreurEmail = 'Mot de passe incorrect.';
        } elseif (!filter_var($nouvelEmail, FILTER_VALIDATE_EMAIL)) {
            $erreurEmail = 'Cette adresse e-mail n\'est pas valide.';
        } elseif (strlen($nouvelEmail) > 100) {
            $erreurEmail = 'Cette adresse e-mail est trop longue.';
        } else {
            $stmt = $conn->prepare("UPDATE utilisateurs SET email = ? WHERE id = ?");
            $stmt->bind_param('si', $nouvelEmail, $utilisateurId);

            if ($stmt->execute()) {
                $utilisateur['email'] = $nouvelEmail;
                $succesEmail = 'Votre adresse e-mail a été mise à jour.';
            } else {
                $erreurEmail = 'Cette adresse e-mail est déjà utilisée par un autre compte.';
            }
        }

    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'mot_de_passe') {

        $motDePasseActuel = trim($_POST['mot_de_passe_actuel'] ?? '');
        $nouveauMdp       = trim($_POST['nouveau_mot_de_passe'] ?? '');
        $confirmation     = trim($_POST['confirmation'] ?? '');

        if (!password_verify($motDePasseActuel, $utilisateur['mot_de_passe'])) {
            $erreurMdp = 'Mot de passe actuel incorrect.';
        } elseif ($nouveauMdp !== $confirmation) {
            $erreurMdp = 'Les mots de passe ne correspondent pas.';
        } elseif (strlen($nouveauMdp) < 6) {
            $erreurMdp = 'Le nouveau mot de passe doit faire au moins 6 caractères.';
        } else {
            $hash = password_hash($nouveauMdp, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE utilisateurs SET mot_de_passe = ? WHERE id = ?");
            $stmt->bind_param('si', $hash, $utilisateurId);
            $stmt->execute();
            $succesMdp = 'Votre mot de passe a été changé.';
        }

    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'photo') {

        $fichier = $_FILES['photo'] ?? null;

        if (!$fichier || $fichier['error'] === UPLOAD_ERR_NO_FILE) {
            $erreurPhoto = 'Choisissez une image avant de valider.';
        } elseif ($fichier['error'] === UPLOAD_ERR_INI_SIZE || $fichier['error'] === UPLOAD_ERR_FORM_SIZE) {
            $erreurPhoto = 'L\'image dépasse la taille maximale autorisée par le serveur (' . $tailleMaxMo . ' Mo).';
        } elseif ($fichier['error'] !== UPLOAD_ERR_OK) {
            $erreurPhoto = 'L\'envoi a échoué, réessayez.';
        } elseif ($fichier['size'] > $tailleMaxOctets) {
            $erreurPhoto = 'L\'image ne doit pas dépasser ' . $tailleMaxMo . ' Mo.';
        } else {
            // On vérifie le vrai type du fichier (pas seulement son extension),
            // pour ne pas se fier à ce que le navigateur prétend envoyer.
            $typeReel = mime_content_type($fichier['tmp_name']);

            if (!isset($typesAutorises[$typeReel])) {
                $erreurPhoto = 'Format non supporté : utilisez une image JPG, PNG ou WebP.';
            } else {
                $dossier = __DIR__ . '/uploads/avatars/';

                if (!is_dir($dossier) || !is_writable($dossier)) {
                    $erreurPhoto = 'Envoi impossible : le dossier de destination n\'est pas accessible en écriture.';
                } else {
                    $nomFichier = 'u' . $utilisateurId . '-' . bin2hex(random_bytes(8)) . '.' . $typesAutorises[$typeReel];

                    if (move_uploaded_file($fichier['tmp_name'], $dossier . $nomFichier)) {
                        // On supprime l'ancienne photo, sinon le dossier grossit indéfiniment
                        // à chaque changement.
                        if (!empty($utilisateur['photo_profil']) && is_file($dossier . $utilisateur['photo_profil'])) {
                            @unlink($dossier . $utilisateur['photo_profil']);
                        }

                        $stmt = $conn->prepare("UPDATE utilisateurs SET photo_profil = ? WHERE id = ?");
                        $stmt->bind_param('si', $nomFichier, $utilisateurId);
                        $stmt->execute();

                        $utilisateur['photo_profil'] = $nomFichier;
                        $succesPhoto = 'Votre photo de profil a été mise à jour.';
                    } else {
                        $erreurPhoto = 'Envoi impossible, réessayez.';
                    }
                }
            }
        }

    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'supprimer_photo') {

        if (!empty($utilisateur['photo_profil'])) {
            $chemin = __DIR__ . '/uploads/avatars/' . $utilisateur['photo_profil'];
            if (is_file($chemin)) {
                @unlink($chemin);
            }

            $stmt = $conn->prepare("UPDATE utilisateurs SET photo_profil = NULL WHERE id = ?");
            $stmt->bind_param('i', $utilisateurId);
            $stmt->execute();

            $utilisateur['photo_profil'] = null;
            $succesPhoto = 'Votre photo a été supprimée, vous revenez à l\'avatar par défaut.';
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
    <link rel="stylesheet" type="text/css" href="css/style.css?v=36">
    <title>Cinévo · Mon profil</title>
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="contenu">

    <div class="entete-page">
        <span class="label-section surligne">Votre espace</span>
        <h1>Mon profil</h1>
        <p class="intro">Gérez vos identifiants et votre photo de profil.</p>
    </div>

    <hr class="separateur">

    <?php if (!baseDisponible()): ?>
        <?= messageBaseIndisponible('Votre profil') ?>
    <?php else: ?>

        <div class="encart" style="margin-bottom:24px;">
            <h4>Photo de profil</h4>
            <div style="display:flex; align-items:center; gap:20px; margin-top:14px; flex-wrap:wrap;">
                <?= avatarHtml($utilisateur['nom_utilisateur'], $utilisateur['photo_profil'], 'avatar-grand') ?>
                <form action="profil.php" method="POST" enctype="multipart/form-data" style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                    <?= csrf_champ() ?>
                    <input type="hidden" name="action" value="photo">
                    <input type="file" name="photo" accept="image/jpeg,image/png,image/webp" required>
                    <button type="submit" class="btn-rouge">Enregistrer la photo</button>
                </form>
                <?php if (!empty($utilisateur['photo_profil'])): ?>
                    <form action="profil.php" method="POST">
                        <?= csrf_champ() ?>
                        <input type="hidden" name="action" value="supprimer_photo">
                        <button type="submit" class="btn-transparent">Revenir à l'avatar par défaut</button>
                    </form>
                <?php endif; ?>
            </div>
            <p class="source" style="margin-top:12px;">JPG, PNG ou WebP, <?= $tailleMaxMo ?> Mo maximum.</p>
            <?php if ($erreurPhoto): ?>
                <p class="message-erreur" style="margin-top:12px;"><?= htmlspecialchars($erreurPhoto) ?></p>
            <?php endif; ?>
            <?php if ($succesPhoto): ?>
                <p class="message-succes" style="margin-top:12px;"><?= htmlspecialchars($succesPhoto) ?></p>
            <?php endif; ?>
        </div>

        <div class="encart" style="margin-bottom:24px;">
            <h4>Adresse e-mail</h4>
            <p class="source" style="margin-top:4px;">Actuelle : <?= htmlspecialchars($utilisateur['email']) ?></p>
            <form action="profil.php" method="POST" style="margin-top:14px; max-width:420px;">
                <?= csrf_champ() ?>
                <input type="hidden" name="action" value="email">
                <div class="champ">
                    <label for="email">Nouvelle adresse e-mail</label>
                    <input type="email" id="email" name="email" required autocomplete="email">
                </div>
                <div class="champ" style="margin-top:12px;">
                    <label for="mdp_email">Mot de passe actuel</label>
                    <input type="password" id="mdp_email" name="mot_de_passe_actuel" required autocomplete="current-password">
                </div>
                <button type="submit" class="btn-rouge" style="margin-top:14px;">Changer l'e-mail</button>
            </form>
            <?php if ($erreurEmail): ?>
                <p class="message-erreur" style="margin-top:12px;"><?= htmlspecialchars($erreurEmail) ?></p>
            <?php endif; ?>
            <?php if ($succesEmail): ?>
                <p class="message-succes" style="margin-top:12px;"><?= htmlspecialchars($succesEmail) ?></p>
            <?php endif; ?>
        </div>

        <div class="encart">
            <h4>Mot de passe</h4>
            <form action="profil.php" method="POST" style="margin-top:14px; max-width:420px;">
                <?= csrf_champ() ?>
                <input type="hidden" name="action" value="mot_de_passe">
                <div class="champ">
                    <label for="mdp_actuel">Mot de passe actuel</label>
                    <input type="password" id="mdp_actuel" name="mot_de_passe_actuel" required autocomplete="current-password">
                </div>
                <div class="champ" style="margin-top:12px;">
                    <label for="nouveau_mdp">Nouveau mot de passe</label>
                    <input type="password" id="nouveau_mdp" name="nouveau_mot_de_passe" required autocomplete="new-password">
                </div>
                <div class="champ" style="margin-top:12px;">
                    <label for="confirmation">Confirmer le nouveau mot de passe</label>
                    <input type="password" id="confirmation" name="confirmation" required autocomplete="new-password">
                </div>
                <button type="submit" class="btn-rouge" style="margin-top:14px;">Changer le mot de passe</button>
            </form>
            <?php if ($erreurMdp): ?>
                <p class="message-erreur" style="margin-top:12px;"><?= htmlspecialchars($erreurMdp) ?></p>
            <?php endif; ?>
            <?php if ($succesMdp): ?>
                <p class="message-succes" style="margin-top:12px;"><?= htmlspecialchars($succesMdp) ?></p>
            <?php endif; ?>
        </div>

    <?php endif; ?>

</main>

<?php include 'includes/footer.php'; ?>

<script src="js/scripts.js"></script>
</body>
</html>
