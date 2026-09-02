<?php
/**
 * Assistant de configuration à usage local.
 *
 * Permet de renseigner la clé TMDB depuis le navigateur, sans passer par un
 * éditeur de texte, lorsqu'on installe le projet sur une nouvelle machine.
 *
 * Deux garde-fous : la page n'est accessible que depuis la machine elle-même,
 * et elle refuse d'agir si une clé est déjà configurée.
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/tmdb.php';

$adresse    = $_SERVER['REMOTE_ADDR'] ?? '';
$enLocal    = in_array($adresse, ['127.0.0.1', '::1', 'localhost'], true);
$dejaConfig = TMDB_API_KEY !== '';

$erreur  = '';
$succes  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $enLocal && !$dejaConfig) {
    if (!csrf_verifie($_POST['csrf_token'] ?? null)) {
        $erreur = 'Requête invalide, merci de réessayer.';
    } else {
        $cle = trim($_POST['cle'] ?? '');

        if (!preg_match('/^[A-Za-z0-9._-]{16,256}$/', $cle)) {
            $erreur = 'Cette clé n\'a pas un format valide.';
        } else {
            // On vérifie la clé auprès de TMDB avant de l'enregistrer, pour ne
            // pas écrire un .env qui ne fonctionnera pas.
            $test = @file_get_contents(
                TMDB_BASE_URL . '/movie/550?api_key=' . urlencode($cle),
                false,
                stream_context_create(['http' => ['timeout' => 6, 'ignore_errors' => true]])
            );
            $reponse = $test === false ? null : json_decode($test, true);

            if (!is_array($reponse) || empty($reponse['title'])) {
                $erreur = 'TMDB refuse cette clé, ou l\'API est injoignable depuis cette machine.';
            } else {
                $contenu = "TMDB_API_KEY=" . $cle . "\n"
                    . "TMDB_BASE_URL=https://api.themoviedb.org/3\n"
                    . "TMDB_IMAGE_URL=https://image.tmdb.org/t/p\n";

                if (@file_put_contents(__DIR__ . '/.env', $contenu) === false) {
                    $erreur = 'Impossible d\'écrire le fichier .env : vérifiez les droits du dossier.';
                } else {
                    $succes = 'Clé enregistrée et vérifiée. Le site est prêt.';
                }
            }
        }
    }
}

$tmdb = new TMDB();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" type="text/css" href="css/style.css?v=2">
    <title>Installation — Cinévo</title>
</head>
<body>

<main class="contenu">

    <div class="entete-page">
        <span class="label-section">Mise en route sur une nouvelle machine</span>
        <h1>Installation</h1>
    </div>

    <hr class="separateur">

    <?php if (!$enLocal): ?>

        <p class="message-erreur">
            Cet assistant n'est utilisable que depuis la machine sur laquelle le site
            est installé. Ouvrez-le via <code>localhost</code>.
        </p>

    <?php elseif ($succes): ?>

        <p class="intro"><?= htmlspecialchars($succes) ?></p>
        <div class="hero-boutons" style="margin-top:20px;">
            <a href="diagnostic.php?prechauffer=1"><button class="btn-rouge">Préchauffer le cache</button></a>
            <a href="index.php"><button class="btn-blanc">Aller sur le site</button></a>
        </div>

    <?php elseif ($dejaConfig): ?>

        <p class="intro">
            Une clé TMDB est déjà configurée sur cette machine : il n'y a rien à faire.
            Pour la remplacer, modifiez le fichier <code>.env</code> à la main.
        </p>
        <div class="hero-boutons" style="margin-top:20px;">
            <a href="diagnostic.php"><button class="btn-rouge">Vérifier l'installation</button></a>
        </div>

    <?php else: ?>

        <p class="intro">
            Le fichier <code>.env</code> est absent : c'est normal sur une machine
            fraîchement clonée, la clé n'est jamais versionnée. Collez-la ci-dessous,
            elle sera vérifiée auprès de TMDB puis enregistrée.
        </p>

        <?php if ($erreur): ?>
            <p class="message-erreur"><?= htmlspecialchars($erreur) ?></p>
        <?php endif; ?>

        <form method="post" style="margin-top:20px; max-width:520px;">
            <?= csrf_champ() ?>
            <label for="cle">Clé API TMDB</label>
            <input type="text" id="cle" name="cle" required autocomplete="off"
                   placeholder="Collez ici votre clé (API Key v3)">
            <input type="submit" value="Vérifier et enregistrer">
        </form>

        <p class="source" style="margin-top:14px;">
            La clé se récupère sur
            <a href="https://www.themoviedb.org/settings/api" target="_blank" rel="noopener">themoviedb.org</a>.
        </p>

    <?php endif; ?>

    <hr class="separateur">

    <section class="section-communaute">
        <h2>Et sans clé ?</h2>
        <p>
            Le site reste consultable : un catalogue de
            <?= $tmdb->tailleCatalogue() ?> films est livré avec le projet, ce qui
            permet à l'accueil et à la page « Au hasard » de fonctionner
            immédiatement. La recherche et les fiches détaillées, elles,
            nécessitent une clé.
        </p>
    </section>

</main>

</body>
</html>
