<?php
require_once __DIR__ . '/../includes/auth.php';

// Faux objets : inscrire() ne touche ainsi aucune base.
class FausseRequete {
    public function __construct(private ?Throwable $aLever) {}
    public function bind_param(...$args) {}
    public function execute() {
        if ($this->aLever) throw $this->aLever;
        return true;
    }
}

class FausseConnexion {
    public function __construct(private ?Throwable $aLever) {}
    public function prepare($sql) { return new FausseRequete($this->aLever); }
}

verifie('inscrire réussit quand la requête passe',
    inscrire(new FausseConnexion(null), 'ana', 'ana@exemple.fr', 'secret1') === true);

verifie('inscrire renvoie false sur un doublon (erreur 1062)',
    inscrire(new FausseConnexion(new mysqli_sql_exception('Duplicate entry', 1062)), 'ana', 'ana@exemple.fr', 'secret1') === false);

$autreErreurRelancee = false;
try {
    inscrire(new FausseConnexion(new mysqli_sql_exception('Table inconnue', 1146)), 'ana', 'ana@exemple.fr', 'secret1');
} catch (mysqli_sql_exception $e) {
    $autreErreurRelancee = true;
}
verifie('inscrire relance les erreurs qui ne sont pas des doublons', $autreErreurRelancee);

verifie('estDoublon reconnaît 1062', estDoublon(new mysqli_sql_exception('x', 1062)));
verifie('estDoublon refuse les autres codes', !estDoublon(new mysqli_sql_exception('x', 1146)));
