<?php
$page = 'profil';
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/format.php';
require_once 'includes/validation.php';

exigerConnexion();

$erreurEmail = '';
$succesEmail = '';
$erreurMdp   = '';
$succesMdp   = '';
$erreurPhoto = '';
$succesPhoto = '';

$utilisateurId = (int) $_SESSION['utilisateur_id'];

$typesAutorises  = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
$limiteServeur   = tailleIniEnOctets(ini_get('upload_max_filesize'));
$tailleMaxOctets = min(3 * 1024 * 1024, $limiteServeur);
$tailleMaxMo     = round($tailleMaxOctets / 1024 / 1024, 1);

if (!baseDisponible()) {
    $erreurEmail = 'Cette page est momentanément indisponible : la base de données ne répond pas.';
} else {
    $utilisateur = trouverUtilisateur($conn, $utilisateurId);

    if (!$utilisateur) {
        deconnecter();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_verifie_post()) {
        $erreurEmail = $erreurMdp = $erreurPhoto = ERREUR_CSRF;
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'email') {

        $nouvelEmail       = trim($_POST['email'] ?? '');
        $motDePasseActuel  = trim($_POST['mot_de_passe_actuel'] ?? '');

        $emailInvalide = erreurEmail($nouvelEmail);

        if (!password_verify($motDePasseActuel, $utilisateur['mot_de_passe'])) {
            $erreurEmail = 'Mot de passe incorrect.';
        } elseif ($emailInvalide) {
            $erreurEmail = $emailInvalide;
        } else {
            try {
                changerEmail($conn, $utilisateurId, $nouvelEmail);
                $utilisateur['email'] = $nouvelEmail;
                $succesEmail = 'Votre adresse e-mail a été mise à jour.';
            } catch (mysqli_sql_exception $erreur) {
                if (!estDoublon($erreur)) {
                    throw $erreur;
                }
                $erreurEmail = 'Cette adresse e-mail est déjà utilisée par un autre compte.';
            }
        }

    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'mot_de_passe') {

        $motDePasseActuel = trim($_POST['mot_de_passe_actuel'] ?? '');
        $nouveauMdp       = trim($_POST['nouveau_mot_de_passe'] ?? '');
        $confirmation     = trim($_POST['confirmation'] ?? '');

        $mdpInvalide = erreurMotDePasse($nouveauMdp, $confirmation);

        if (!password_verify($motDePasseActuel, $utilisateur['mot_de_passe'])) {
            $erreurMdp = 'Mot de passe actuel incorrect.';
        } elseif ($mdpInvalide) {
            $erreurMdp = $mdpInvalide;
        } else {
            $hash = password_hash($nouveauMdp, PASSWORD_DEFAULT);
            changerMotDePasse($conn, $utilisateurId, $hash);
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

                        if (!empty($utilisateur['photo_profil']) && is_file($dossier . $utilisateur['photo_profil'])) {
                            @unlink($dossier . $utilisateur['photo_profil']);
                        }

                        changerPhoto($conn, $utilisateurId, $nomFichier);

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

            changerPhoto($conn, $utilisateurId, null);

            $utilisateur['photo_profil'] = null;
            $succesPhoto = 'Votre photo a été supprimée, vous revenez à l\'avatar par défaut.';
        }
    }
}

$meta = [
    'titre'       => 'Cinévo · Mon profil',
    'robots'      => 'noindex, nofollow',
    'classe_body' => 'page-profil',
];
include 'includes/debut-page.php';
?>

<main class="contenu">

    <div class="entete-page">
        <h1>Mon profil</h1>
        <p class="intro">Gérez vos identifiants et votre photo de profil.</p>
    </div>

    <hr class="separateur">

    <?php if (!baseDisponible()): ?>
        <?= messageBaseIndisponible('Votre profil') ?>
    <?php else: ?>

        <div class="encart">
            <h2>Photo de profil</h2>
            <div class="ligne-avatar">
                <?= avatarHtml($utilisateur['nom_utilisateur'], $utilisateur['photo_profil'], 'avatar-grand') ?>
                <form action="profil.php" method="POST" enctype="multipart/form-data">
                    <?= csrf_champ() ?>
                    <input type="hidden" name="action" value="photo">
                    <div class="champ">
                        <label for="photo">Choisir une image</label>
                        <input type="file" id="photo" name="photo" class="champ-fichier"
                               accept="image/jpeg,image/png,image/webp" required>
                    </div>
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
            <p class="source">JPG, PNG ou WebP, <?= $tailleMaxMo ?> Mo maximum.</p>
            <?php if ($erreurPhoto): ?>
                <p class="message-erreur"><?= htmlspecialchars($erreurPhoto) ?></p>
            <?php endif; ?>
            <?php if ($succesPhoto): ?>
                <p class="message-succes"><?= htmlspecialchars($succesPhoto) ?></p>
            <?php endif; ?>
        </div>

        <div class="encart">
            <h2>Adresse e-mail</h2>
            <p class="source">Actuelle : <?= htmlspecialchars($utilisateur['email']) ?></p>
            <form action="profil.php" method="POST">
                <?= csrf_champ() ?>
                <input type="hidden" name="action" value="email">
                <div class="champ">
                    <label for="email">Nouvelle adresse e-mail</label>
                    <input type="email" id="email" name="email" required autocomplete="email">
                </div>
                <div class="champ">
                    <label for="mdp_email">Mot de passe actuel</label>
                    <input type="password" id="mdp_email" name="mot_de_passe_actuel" required autocomplete="current-password">
                </div>
                <button type="submit" class="btn-rouge">Changer l'e-mail</button>
            </form>
            <?php if ($erreurEmail): ?>
                <p class="message-erreur"><?= htmlspecialchars($erreurEmail) ?></p>
            <?php endif; ?>
            <?php if ($succesEmail): ?>
                <p class="message-succes"><?= htmlspecialchars($succesEmail) ?></p>
            <?php endif; ?>
        </div>

        <div class="encart">
            <h2>Mot de passe</h2>
            <form action="profil.php" method="POST">
                <?= csrf_champ() ?>
                <input type="hidden" name="action" value="mot_de_passe">
                <div class="champ">
                    <label for="mdp_actuel">Mot de passe actuel</label>
                    <input type="password" id="mdp_actuel" name="mot_de_passe_actuel" required autocomplete="current-password">
                </div>
                <div class="champ">
                    <label for="nouveau_mdp">Nouveau mot de passe</label>
                    <input type="password" id="nouveau_mdp" name="nouveau_mot_de_passe" required autocomplete="new-password">
                </div>
                <div class="champ">
                    <label for="confirmation">Confirmer le nouveau mot de passe</label>
                    <input type="password" id="confirmation" name="confirmation" required autocomplete="new-password">
                </div>
                <button type="submit" class="btn-rouge">Changer le mot de passe</button>
            </form>
            <?php if ($erreurMdp): ?>
                <p class="message-erreur"><?= htmlspecialchars($erreurMdp) ?></p>
            <?php endif; ?>
            <?php if ($succesMdp): ?>
                <p class="message-succes"><?= htmlspecialchars($succesMdp) ?></p>
            <?php endif; ?>
        </div>

    <?php endif; ?>

</main>

<?php include 'includes/fin-page.php'; ?>
