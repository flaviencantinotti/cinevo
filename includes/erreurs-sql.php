<?php

const SQL_ERREUR_DOUBLON = 1062;

function estDoublon(mysqli_sql_exception $erreur) {
    return $erreur->getCode() === SQL_ERREUR_DOUBLON;
}
