<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/cache.php';

/**
 * Accès à l'API TMDB.
 *
 * Les réponses passent par un cache fichier, et plusieurs secours sont
 * prévus pour que le site reste consultable si l'API ne répond pas :
 * cache périmé, puis catalogue de films livré avec le projet.
 */
class TMDB {

    // Durées de cache, en secondes.
    const CACHE_FILM      = 86400; // une fiche film ne change quasiment jamais
    const CACHE_RECHERCHE = 3600;
    const CACHE_TIRAGE    = 1800;

    const TIMEOUT = 4; // secondes accordées à chaque appel

    private string $apiKey;
    private string $baseUrl;
    public  string $imageUrl;
    private Cache  $cache;

    // Dernière erreur rencontrée, affichée par la page de diagnostic.
    private string $erreur = '';

    public function __construct() {
        $this->apiKey   = TMDB_API_KEY;
        $this->baseUrl  = TMDB_BASE_URL;
        $this->imageUrl = TMDB_IMAGE_URL;
        $this->cache    = new Cache();
    }

    /**
     * Appelle l'API, en passant d'abord par le cache.
     * Si l'appel échoue, on ressert la version en cache même périmée.
     */
    private function fetch(string $endpoint, array $params = [], int $ttl = self::CACHE_FILM): ?array {
        $params['language'] = 'fr-FR';

        $cle = $endpoint . '?' . http_build_query($params);

        $enCache = $this->cache->get($cle, $ttl);
        if ($enCache !== null) return $enCache;

        if ($this->apiKey === '') {
            $this->erreur = 'Clé API absente : vérifiez le fichier .env';
            return $this->cache->getPerime($cle);
        }

        $params['api_key'] = $this->apiKey;
        $url = $this->baseUrl . $endpoint . '?' . http_build_query($params);

        // Deux essais : une coupure réseau très brève ne doit pas suffire
        // à faire échouer la page.
        for ($essai = 1; $essai <= 2; $essai++) {
            $reponse = $this->appelHttp($url);

            if ($reponse !== null) {
                $donnees = json_decode($reponse, true);

                // TMDB renvoie "success" à false quand la clé est mauvaise.
                if (is_array($donnees) && !isset($donnees['success'])) {
                    $this->cache->set($cle, $donnees);
                    $this->erreur = '';
                    return $donnees;
                }

                $this->erreur = 'Réponse refusée par TMDB';
                break;
            }
        }

        return $this->cache->getPerime($cle);
    }

    /** Requête HTTP avec cURL, ou file_get_contents si cURL n'est pas installé. */
    private function appelHttp(string $url): ?string {
        if (function_exists('curl_init')) {
            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, self::TIMEOUT);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::TIMEOUT);

            // Certaines installations WAMP/XAMPP n'ont pas de certificats :
            // on peut alors indiquer un cacert.pem dans le .env.
            if (TMDB_CA_BUNDLE !== '') {
                curl_setopt($ch, CURLOPT_CAINFO, TMDB_CA_BUNDLE);
            }

            $reponse = curl_exec($ch);
            $erreur  = curl_error($ch);
            curl_close($ch);

            if ($reponse !== false) return $reponse;

            $this->erreur = $erreur;
            return null;
        }

        $contexte = stream_context_create([
            'http' => ['timeout' => self::TIMEOUT]
        ]);

        $reponse = @file_get_contents($url, false, $contexte);

        if ($reponse === false) {
            $this->erreur = 'Impossible de joindre l\'API';
            return null;
        }

        return $reponse;
    }

    public function searchMovie(string $query, int $page = 1): ?array {
        return $this->fetch('/search/movie', [
            'query' => $query,
            'page'  => $page,
        ], self::CACHE_RECHERCHE);
    }

    public function getMovie(int $id): ?array {
        return $this->fetch("/movie/{$id}", [
            'append_to_response' => 'credits,watch/providers'
        ]);
    }

    public function getPopular(int $page = 1): ?array {
        return $this->fetch('/movie/popular', ['page' => $page], self::CACHE_TIRAGE);
    }

    /**
     * Tire des films au hasard.
     *
     * On pioche une page au hasard dans le catalogue, ce qui donne un
     * tirage différent à chaque appel. Les films obtenus sont mis de côté
     * dans une réserve, utilisée si l'API devient injoignable.
     */
    public function getRandomMovies(int $count = 5): array {
        $data = $this->fetch('/discover/movie', [
            'page'           => random_int(1, 20),
            'sort_by'        => 'popularity.desc',
            'include_adult'  => 'false',
            'vote_count.gte' => 100,
        ], self::CACHE_TIRAGE);

        $films = $this->filtrerFilms($data['results'] ?? []);

        if (!empty($films)) {
            $this->remplirReserve($films);
        } else {
            $films = $this->lireReserve();
        }

        shuffle($films);

        return array_slice($films, 0, $count);
    }

    /** On ne garde que les films affichables : avec un titre et une affiche. */
    private function filtrerFilms(array $films): array {
        $garde = [];

        foreach ($films as $film) {
            if (!empty($film['id']) && !empty($film['title']) && !empty($film['poster_path'])) {
                $garde[] = $film;
            }
        }

        return $garde;
    }

    private function fichierReserve(): string {
        return $this->cache->dossier() . '/reserve-films.json';
    }

    private function fichierCatalogue(): string {
        return __DIR__ . '/../data/catalogue.json';
    }

    /** Ajoute les films à la réserve, sans doublon, en gardant les 100 derniers. */
    private function remplirReserve(array $films): void {
        if (!$this->cache->estActif()) return;

        $reserve = [];

        // On indexe par identifiant : un film déjà présent est simplement remplacé.
        foreach (array_merge($this->lireReserve(), $films) as $film) {
            $reserve[$film['id']] = [
                'id'           => $film['id'],
                'title'        => $film['title'],
                'release_date' => $film['release_date'] ?? '',
                'poster_path'  => $film['poster_path'],
            ];
        }

        $reserve = array_slice(array_values($reserve), -100);

        @file_put_contents($this->fichierReserve(), json_encode($reserve), LOCK_EX);
    }

    /** Réserve locale, ou à défaut le catalogue livré avec le projet. */
    private function lireReserve(): array {
        $films = $this->lireFichierFilms($this->fichierReserve());

        if (empty($films)) {
            $films = $this->lireFichierFilms($this->fichierCatalogue());
        }

        return $films;
    }

    private function lireFichierFilms(string $fichier): array {
        if (!is_readable($fichier)) return [];

        $films = json_decode(@file_get_contents($fichier), true);

        return is_array($films) ? $this->filtrerFilms($films) : [];
    }

    /**
     * Copie la réserve dans data/catalogue.json, qui lui est versionné.
     * À lancer une fois depuis une machine où l'API répond.
     */
    public function exporterCatalogue(): int {
        $films = $this->lireFichierFilms($this->fichierReserve());

        if (empty($films)) return 0;

        $dossier = dirname($this->fichierCatalogue());

        if (!is_dir($dossier)) {
            @mkdir($dossier, 0775, true);
        }

        $json = json_encode($films, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        if (@file_put_contents($this->fichierCatalogue(), $json) === false) return 0;

        return count($films);
    }

    public function tailleReserve(): int {
        return count($this->lireFichierFilms($this->fichierReserve()));
    }

    public function tailleCatalogue(): int {
        return count($this->lireFichierFilms($this->fichierCatalogue()));
    }

    public function getPosterUrl(?string $posterPath, string $size = 'w342'): string {
        if (!$posterPath) return 'images/affiche-indisponible.svg';

        return $this->imageUrl . '/' . $size . $posterPath;
    }

    public function derniereErreur(): string {
        return $this->erreur;
    }

    public function cleConfiguree(): bool {
        return $this->apiKey !== '';
    }

    public function cacheActif(): bool {
        return $this->cache->estActif();
    }
}
