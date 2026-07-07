<?php
require_once __DIR__ . '/config.php';

class TMDB {

    private string $apiKey;
    private string $baseUrl;
    public  string $imageUrl;

    public function __construct() {
        $this->apiKey   = TMDB_API_KEY;
        $this->baseUrl  = TMDB_BASE_URL;
        $this->imageUrl = TMDB_IMAGE_URL;
    }

    private function fetch(string $endpoint, array $params = []): ?array {
        $params['api_key']  = $this->apiKey;
        $params['language'] = 'fr-FR';

        $url = $this->baseUrl . $endpoint . '?' . http_build_query($params);

        $context = stream_context_create([
            'http' => ['timeout' => 5]
        ]);

        $response = @file_get_contents($url, false, $context);

        if ($response === false) return null;

        return json_decode($response, true);
    }

    public function searchMovie(string $query, int $page = 1): ?array {
        return $this->fetch('/search/movie', [
            'query' => $query,
            'page'  => $page,
        ]);
    }

    public function getMovie(int $id): ?array {
        return $this->fetch("/movie/{$id}", [
            'append_to_response' => 'credits,watch/providers'
        ]);
    }

    public function getPopular(int $page = 1): ?array {
        return $this->fetch('/movie/popular', ['page' => $page]);
    }

    public function getRandomMovies(int $count = 5): array {
        $data = $this->fetch('/discover/movie', [
            'page'             => random_int(1, 20),
            'sort_by'          => 'popularity.desc',
            'include_adult'    => 'false',
            'vote_count.gte'   => 100,
        ]);

        $films = $data['results'] ?? [];
        $films = array_values(array_filter($films, function ($f) {
            return !empty($f['title']) && !empty($f['poster_path']);
        }));

        shuffle($films);

        return array_slice($films, 0, $count);
    }

    public function getPosterUrl(?string $posterPath, string $size = 'w342'): string {
        if (!$posterPath) return '';
        return $this->imageUrl . '/' . $size . $posterPath;
    }
}
