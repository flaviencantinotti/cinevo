<?php
/**
 * Page de vérification, à ouvrir avant une démonstration.
 *
 * Elle contrôle que tout est en place et permet de remplir le cache pendant
 * que le réseau fonctionne. La clé API n'est jamais affichée.
 * Fonctionne aussi en console : php diagnostic.php
 */

require_once __DIR__ . '/includes/tmdb.php';
require_once __DIR__ . '/includes/db.php';

$enConsole = (PHP_SAPI === 'cli');
$tmdb      = new TMDB();
$message   = '';

// Remplit la réserve en enchaînant plusieurs tirages, chacun piochant
// une page différente du catalogue.
if ($enConsole || isset($_GET['prechauffer'])) {
    $avant = $tmdb->tailleReserve();

    for ($i = 0; $i < 6; $i++) {
        $tmdb->getRandomMovies(5);
    }

    $message = 'Réserve hors ligne : ' . $avant . ' → ' . $tmdb->tailleReserve() . ' films.';
}

if (isset($_GET['exporter'])) {
    $nombre = $tmdb->exporterCatalogue();

    $message = $nombre > 0
        ? $nombre . ' films écrits dans data/catalogue.json. Commitez ce fichier pour que le site fonctionne sur toute machine.'
        : 'Rien à exporter : préchauffez d\'abord le cache pendant que l\'API répond.';
}

// Chaque contrôle : intitulé, réussite, détail, et s'il est bloquant.
$controles = [];

$transport = function_exists('curl_init') ? 'cURL' : (ini_get('allow_url_fopen') ? 'file_get_contents' : '');

$controles[] = ['Version de PHP', version_compare(PHP_VERSION, '7.4', '>='), PHP_VERSION, true];

$controles[] = ['Moyen d\'appel réseau', $transport !== '',
    $transport !== '' ? $transport : 'activez cURL ou allow_url_fopen dans php.ini', true];

$controles[] = ['Extension OpenSSL', extension_loaded('openssl'),
    extension_loaded('openssl') ? 'chargée' : 'nécessaire pour appeler l\'API en HTTPS', true];

$controles[] = ['Fichier .env présent', is_readable(__DIR__ . '/.env'),
    is_readable(__DIR__ . '/.env') ? 'trouvé' : 'ouvrez installation.php pour le créer', true];

$controles[] = ['Clé API renseignée', $tmdb->cleConfiguree(),
    $tmdb->cleConfiguree() ? 'configurée' : 'TMDB_API_KEY est vide', true];

$controles[] = ['Dossier de cache inscriptible', $tmdb->cacheActif(),
    $tmdb->cacheActif() ? 'actif' : 'donnez les droits d\'écriture sur le dossier cache/', true];

// Appel réel à l'API, avec un film dont l'identifiant ne change jamais.
$film = $tmdb->getMovie(550);
$apiRepond = !empty($film['title']);

$controles[] = ['Réponse de l\'API TMDB', $apiRepond,
    $apiRepond ? 'OK — film test reçu : ' . $film['title'] : ($tmdb->derniereErreur() ?: 'aucune réponse'), true];

$reserve = $tmdb->tailleReserve();
$controles[] = ['Réserve de films hors ligne', $reserve > 0,
    $reserve > 0 ? $reserve . ' films disponibles sans réseau' : 'vide — lancez un préchauffage', false];

$catalogue = $tmdb->tailleCatalogue();
$controles[] = ['Catalogue livré avec le dépôt', $catalogue > 0,
    $catalogue > 0 ? $catalogue . ' films : un clone neuf affiche des films sans configuration'
                   : 'absent — préchauffez puis cliquez « Exporter le catalogue »', false];

$controles[] = ['Base de données MySQL', baseDisponible(),
    baseDisponible() ? 'connexion établie' : baseErreur() . ' — démarrez MySQL dans WAMP/XAMPP', true];

if (!baseDisponible()) {
    $controles[] = ['Pages consultables sans base', true,
        'recherche, fiche film et tirage au hasard restent accessibles', false];
}

// Tout va bien si aucun contrôle bloquant n'a échoué.
$toutVaBien = true;
foreach ($controles as $controle) {
    if ($controle[3] && !$controle[1]) $toutVaBien = false;
}

if ($enConsole) {
    echo "\nDiagnostic Cinévo\n=================\n\n";

    foreach ($controles as $controle) {
        $marque  = $controle[1] ? '[OK] ' : ($controle[3] ? '[KO] ' : '[!]  ');
        $espaces = str_repeat(' ', max(1, 34 - mb_strlen($controle[0])));

        echo $marque . $controle[0] . $espaces . $controle[2] . "\n";
    }

    if ($message) echo "\n" . $message . "\n";

    echo "\n" . ($toutVaBien ? "Tout est prêt.\n\n" : "Des points sont à corriger.\n\n");
    exit($toutVaBien ? 0 : 1);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" type="text/css" href="css/style.css?v=2">
    <title>Diagnostic technique — Cinévo</title>
</head>
<body>

<main class="contenu">

    <div class="entete-page">
        <span class="label-section">Vérification avant démonstration</span>
        <h1>Diagnostic</h1>
        <p class="intro">
            <?= $toutVaBien
                ? 'Tout est opérationnel : l\'API répond et le site peut être présenté.'
                : 'Un ou plusieurs points doivent être corrigés avant la démonstration.' ?>
        </p>
    </div>

    <hr class="separateur">

    <?php if ($message): ?>
        <p class="intro"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <table class="table-diagnostic">
        <?php foreach ($controles as $controle): ?>
            <tr>
                <td class="diag-etat">
                    <span class="pastille-diag <?= $controle[1] ? 'diag-ok' : ($controle[3] ? 'diag-ko' : 'diag-avert') ?>"></span>
                </td>
                <td class="diag-intitule"><?= htmlspecialchars($controle[0]) ?></td>
                <td class="diag-detail"><?= htmlspecialchars($controle[2]) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <div class="hero-boutons" style="margin-top:28px;">
        <a href="diagnostic.php?prechauffer=1"><button class="btn-rouge">Préchauffer le cache</button></a>
        <a href="diagnostic.php?exporter=1"><button class="btn-blanc">Exporter le catalogue</button></a>
        <a href="index.php"><button class="btn-blanc">Retour au site</button></a>
    </div>

    <hr class="separateur">

    <section class="section-communaute">
        <h2>À quoi sert cette page</h2>
        <p>
            Le préchauffage interroge l'API pendant que la connexion fonctionne et garde les
            films récupérés dans une réserve locale. Si le réseau vient à manquer pendant la
            démonstration, le site pioche dans cette réserve au lieu d'afficher des pages vides.
        </p>
        <p>
            « Exporter le catalogue » copie cette réserve dans <code>data/catalogue.json</code>,
            qui est versionné : une fois commité, n'importe quel clone du projet affiche des
            films dès le premier chargement.
        </p>
        <p>Cette page est un outil de vérification : supprimez-la avant une mise en production.</p>
    </section>

</main>

</body>
</html>
