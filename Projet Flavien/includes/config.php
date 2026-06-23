<?php
function loadEnv(string $path): void {
    if (!file_exists($path)) return;

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        if (strpos($line, '=') === false) continue;
        $parts = explode('=', $line, 2);
        $_ENV[trim($parts[0])] = trim($parts[1]);
    }
}

loadEnv(__DIR__ . '/../.env');

define('TMDB_API_KEY',   isset($_ENV['TMDB_API_KEY'])   ? $_ENV['TMDB_API_KEY']   : '');
define('TMDB_BASE_URL',  isset($_ENV['TMDB_BASE_URL'])  ? $_ENV['TMDB_BASE_URL']  : 'https://api.themoviedb.org/3');
define('TMDB_IMAGE_URL', isset($_ENV['TMDB_IMAGE_URL']) ? $_ENV['TMDB_IMAGE_URL'] : 'https://image.tmdb.org/t/p');
