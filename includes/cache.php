<?php

/**
 * Cache fichier des réponses de l'API TMDB.
 *
 * Chaque réponse est stockée dans un fichier JSON accompagné de sa date.
 * On peut ensuite la relire soit si elle est encore fraîche (get), soit
 * même si elle est périmée (getPerime), ce qui sert de secours quand
 * l'API ne répond plus.
 */
class Cache {

    private string $dossier;

    public function __construct() {
        $this->dossier = __DIR__ . '/../cache';

        if (!is_dir($this->dossier)) {
            @mkdir($this->dossier, 0775, true);
        }
    }

    // Le nom du fichier est un condensé de la clé, pour éviter les
    // caractères interdits dans un nom de fichier.
    private function chemin(string $cle): string {
        return $this->dossier . '/' . sha1($cle) . '.json';
    }

    private function lire(string $cle): ?array {
        $fichier = $this->chemin($cle);

        if (!is_readable($fichier)) return null;

        $contenu = @file_get_contents($fichier);
        if ($contenu === false) return null;

        $entree = json_decode($contenu, true);

        if (!isset($entree['date']) || !isset($entree['donnees'])) return null;

        return $entree;
    }

    /** Renvoie la donnée si elle a moins de $ttl secondes, sinon null. */
    public function get(string $cle, int $ttl): ?array {
        $entree = $this->lire($cle);

        if ($entree === null) return null;

        if (time() - $entree['date'] > $ttl) return null;

        return $entree['donnees'];
    }

    /** Renvoie la donnée même périmée : dernier recours si l'API est muette. */
    public function getPerime(string $cle): ?array {
        $entree = $this->lire($cle);

        return $entree === null ? null : $entree['donnees'];
    }

    public function set(string $cle, array $donnees): void {
        $entree = [
            'date'    => time(),
            'donnees' => $donnees,
        ];

        @file_put_contents($this->chemin($cle), json_encode($entree), LOCK_EX);
    }

    public function estActif(): bool {
        return is_writable($this->dossier);
    }

    public function dossier(): string {
        return $this->dossier;
    }
}
