<?php

const SQL_ERREUR_DOUBLON = 1062;

function estDoublon(mysqli_sql_exception $erreur) {
    return $erreur->getCode() === SQL_ERREUR_DOUBLON;
}

// Sans ça, selon la config du serveur, le visiteur voit le SQL, les chemins ou les arguments de l'erreur.
function repondreErreurInattendue(Throwable $erreur) {
    error_log((string) $erreur);

    if (!headers_sent()) {
        http_response_code(500);
    }

    echo '<p>Une erreur est survenue. Réessayez dans un instant.</p>';
}
