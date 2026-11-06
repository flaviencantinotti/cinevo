<?php
// Faux objets mysqli : on lit le SQL et les paramètres envoyés, sans base réelle.

class FauxResultat {
    public function __construct(private array $lignes) {}
    public function fetch_all($mode = null) { return $this->lignes; }
    public function fetch_assoc() { return $this->lignes[0] ?? null; }
}

class FausseRequete {
    public $types;
    public $valeurs = [];

    public function __construct(private array $lignes, private ?Throwable $aLever) {}

    public function bind_param($types, ...$valeurs) {
        $this->types   = $types;
        $this->valeurs = $valeurs;
    }

    public function execute() {
        if ($this->aLever) throw $this->aLever;
        return true;
    }

    public function get_result() { return new FauxResultat($this->lignes); }
}

class FausseConnexion {
    public $requetes = [];

    public function __construct(private array $lignes = [], private ?Throwable $aLever = null) {}

    public function prepare($sql) {
        $requete = new FausseRequete($this->lignes, $this->aLever);
        $this->requetes[] = [$sql, $requete];
        return $requete;
    }

    public function derniereRequete() { return end($this->requetes); }
}
