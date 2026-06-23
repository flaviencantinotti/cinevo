<?php
$envFile = __DIR__ . '/../.env';

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        $pos = strpos($line, '=');
        if ($pos === false) continue;
        $key   = trim(substr($line, 0, $pos));
        $value = trim(substr($line, $pos + 1));
        $_ENV[$key] = $value;
    }
}

define('TMDB_API_KEY',   isset($_ENV['TMDB_API_KEY'])   ? $_ENV['TMDB_API_KEY']   : '');
define('TMDB_BASE_URL',  isset($_ENV['TMDB_BASE_URL'])  ? $_ENV['TMDB_BASE_URL']  : 'https://api.themoviedb.org/3');
define('TMDB_IMAGE_URL', isset($_ENV['TMDB_IMAGE_URL']) ? $_ENV['TMDB_IMAGE_URL'] : 'https://image.tmdb.org/t/p');
