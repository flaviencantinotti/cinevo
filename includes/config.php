<?php

$envFile = __DIR__ . '/../.env';

if (is_readable($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {

        $line = trim($line, " \t\r\n\xEF\xBB\xBF");

        if ($line === '' || $line[0] === '#') continue;

        $pos = strpos($line, '=');
        if ($pos === false) continue;

        $key   = trim(substr($line, 0, $pos));
        $value = trim(substr($line, $pos + 1));

        if (strlen($value) >= 2) {
            $premier = $value[0];
            $dernier = $value[strlen($value) - 1];
            if (($premier === '"' && $dernier === '"') || ($premier === "'" && $dernier === "'")) {
                $value = substr($value, 1, -1);
            }
        }

        if ($key !== '') $_ENV[$key] = $value;
    }
}

function config_valeur($cle, $defaut = '') {
    if (!empty($_ENV[$cle])) return $_ENV[$cle];

    $systeme = getenv($cle);
    if (!empty($systeme)) return $systeme;

    return $defaut;
}

define('TMDB_API_KEY',   config_valeur('TMDB_API_KEY'));
define('TMDB_BASE_URL',  config_valeur('TMDB_BASE_URL',  'https://api.themoviedb.org/3'));
define('TMDB_IMAGE_URL', config_valeur('TMDB_IMAGE_URL', 'https://image.tmdb.org/t/p'));

define('TMDB_CA_BUNDLE', config_valeur('TMDB_CA_BUNDLE'));
