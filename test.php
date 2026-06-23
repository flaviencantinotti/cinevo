<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/config.php';
require_once 'includes/tmdb.php';

$tmdb   = new TMDB();
$result = $tmdb->searchMovie('parasite');

if ($result && !empty($result['results'])) {
    echo "<p style='color:green'>✅ API fonctionne — " . count($result['results']) . " films trouvés</p>";
    echo "<p>Premier résultat : <strong>" . $result['results'][0]['title'] . "</strong></p>";
} else {
    echo "<p style='color:red'>❌ API ne répond pas</p>";
}
?>