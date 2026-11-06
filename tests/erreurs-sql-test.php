<?php
require_once __DIR__ . '/faux-mysqli.php';
require_once __DIR__ . '/../includes/auth.php';

verifie('inscrire réussit quand la requête passe',
    inscrire(new FausseConnexion(), 'ana', 'ana@exemple.fr', 'secret1') === true);

verifie('inscrire renvoie false sur un doublon (erreur 1062)',
    inscrire(new FausseConnexion([], new mysqli_sql_exception('Duplicate entry', 1062)), 'ana', 'ana@exemple.fr', 'secret1') === false);

$autreErreurRelancee = false;
try {
    inscrire(new FausseConnexion([], new mysqli_sql_exception('Table inconnue', 1146)), 'ana', 'ana@exemple.fr', 'secret1');
} catch (mysqli_sql_exception $e) {
    $autreErreurRelancee = true;
}
verifie('inscrire relance les erreurs qui ne sont pas des doublons', $autreErreurRelancee);

verifie('estDoublon reconnaît 1062', estDoublon(new mysqli_sql_exception('x', 1062)));
verifie('estDoublon refuse les autres codes', !estDoublon(new mysqli_sql_exception('x', 1146)));

$journalTemporaire = tempnam(sys_get_temp_dir(), 'log');
ini_set('error_log', $journalTemporaire);
ob_start();
repondreErreurInattendue(new mysqli_sql_exception("Data too long for column 'nom' /var/www/secret.php"));
$pageVisible = ob_get_clean();

verifie('erreur inattendue : le visiteur ne voit aucun détail', !str_contains($pageVisible, 'Data too long') && !str_contains($pageVisible, 'secret.php'));
verifie('erreur inattendue : le détail va dans le journal', str_contains(file_get_contents($journalTemporaire), 'Data too long'));
unlink($journalTemporaire);
