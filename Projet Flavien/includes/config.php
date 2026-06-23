<?php
function loadEnv(string $path): void {
    if (!file_exists($path)) return;

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        if (!str_contains($line, '=')) continue;
        [$key, $value] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

loadEnv(__DIR__ . '/../.env');

define('TMDB_API_KEY',   $_ENV['TMDB_API_KEY']   ?? '');
define('TMDB_BASE_URL',  $_ENV['TMDB_BASE_URL']  ?? 'https://api.themoviedb.org/3');
define('TMDB_IMAGE_URL', $_ENV['TMDB_IMAGE_URL'] ?? 'https://image.tmdb.org/t/p');
