<?php

class Cache {

    private $dossier;

    public function __construct() {
        $this->dossier = __DIR__ . '/../cache';

        if (!is_dir($this->dossier)) {
            @mkdir($this->dossier, 0775, true);
        }
    }

    private function chemin($cle) {
        $nom = preg_replace('/[^a-z0-9]+/i', '-', $cle);
        $nom = trim($nom, '-');

        $nom = substr($nom, 0, 150);

        return $this->dossier . '/' . $nom . '.json';
    }

    public function get($cle, $ttl) {
        $fichier = $this->chemin($cle);

        if (!is_readable($fichier)) return null;

        $contenu = @file_get_contents($fichier);
        if ($contenu === false) return null;

        $entree = json_decode($contenu, true);

        if (!isset($entree['date']) || !isset($entree['donnees'])) return null;

        if (time() - $entree['date'] > $ttl) return null;

        return $entree['donnees'];
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
