<?php
// Usage : php tests/lancer.php
// Chaque fichier tests/*-test.php appelle verifie() ; aucun test ne touche à la vraie base.

$echecs = 0;
$total  = 0;

function verifie($libelle, $condition) {
    global $echecs, $total;
    $total++;
    if (!$condition) {
        $echecs++;
        echo "ECHEC  $libelle\n";
    }
}

foreach (glob(__DIR__ . '/*-test.php') as $fichier) {
    require $fichier;
}

echo $total - $echecs, "/$total tests passent\n";
exit($echecs === 0 ? 0 : 1);
