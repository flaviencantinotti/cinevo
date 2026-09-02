<?php
// Domaine du site — à adapter une fois le nom de domaine définitif connu.
define('SITE_URL', 'https://cinevo.fr');

require_once 'includes/db.php';

header('Content-Type: application/xml; charset=utf-8');

$pagesStatiques = [
    ['loc' => '/index.php',            'priority' => '1.0'],
    ['loc' => '/decouvrir.php',        'priority' => '0.8'],
    ['loc' => '/avis.php',             'priority' => '0.8'],
    ['loc' => '/a-propos.php',         'priority' => '0.5'],
    ['loc' => '/contact.php',          'priority' => '0.3'],
    ['loc' => '/mentions-legales.php', 'priority' => '0.1'],
    ['loc' => '/cookies.php',          'priority' => '0.1'],
];

$films = [];

// Sans base, le sitemap se limite aux pages statiques plutôt que d'échouer.
$result = baseDisponible() ? $conn->query("
    SELECT film_id, MAX(created_at) AS derniere_maj
    FROM avis
    GROUP BY film_id
") : null;

while ($result && $row = $result->fetch_assoc()) {
    $films[] = $row;
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($pagesStatiques as $page): ?>
    <url>
        <loc><?= SITE_URL . $page['loc'] ?></loc>
        <priority><?= $page['priority'] ?></priority>
    </url>
<?php endforeach; ?>
<?php foreach ($films as $film): ?>
    <url>
        <loc><?= SITE_URL . '/fiche.php?id=' . (int) $film['film_id'] ?></loc>
        <lastmod><?= date('Y-m-d', strtotime($film['derniere_maj'])) ?></lastmod>
        <priority>0.6</priority>
    </url>
<?php endforeach; ?>
</urlset>
