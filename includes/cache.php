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

    private $dossier;

    public function __construct() {
        $this->dossier = __DIR__ . '/../cache';

        if (!is_dir($this->dossier)) {
            @mkdir($this->dossier, 0775, true);
        }
    }

    // La clé est une URL : on remplace les caractères interdits dans un nom
    // de fichier pour obtenir un nom lisible.
    private function chemin($cle) {
        $nom = preg_replace('/[^a-z0-9]+/i', '-', $cle);
        $nom = trim($nom, '-');

        // Un nom de fichier ne peut pas dépasser 255 caractères : une longue
        // recherche produirait un nom trop long et l'écriture échouerait.
        $nom = substr($nom, 0, 150);

        return $this->dossier . '/' . $nom . '.json';
    }

    private function lire($cle) {
        $fichier = $this->chemin($cle);

        if (!is_readable($fichier)) return null;

        $contenu = @file_get_contents($fichier);
        if ($contenu === false) return null;

        $entree = json_decode($contenu, true);

        if (!isset($entree['date']) || !isset($entree['donnees'])) return null;

        return $entree;
    }

    /** Renvoie la donnée si elle a moins de $ttl secondes, sinon null. */
    public function get($cle, $ttl) {
        $entree = $this->lire($cle);

        if ($entree === null) return null;

        if (time() - $entree['date'] > $ttl) return null;

        return $entree['donnees'];
    }

    /** Renvoie la donnée même périmée : dernier recours si l'API est muette. */
    public function getPerime($cle) {
        $entree = $this->lire($cle);

        return $entree === null ? null : $entree['donnees'];
    }

    public function set($cle, $donnees) {
        $entree = [
            'date'    => time(),
            'donnees' => $donnees,
        ];

        @file_put_contents($this->chemin($cle), json_encode($entree));
    }

    public function estActif() {
        return is_writable($this->dossier);
    }

    public function dossier() {
        return $this->dossier;
    }
}
