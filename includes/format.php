<?php

// Petites fonctions d'affichage utilisées par plusieurs pages.

// Transforme une date SQL en date lisible : « 14 mars 2026 ».
function formaterDateFr($datetime) {
    $mois = [1 => 'janvier', 2 => 'février', 3 => 'mars', 4 => 'avril', 5 => 'mai', 6 => 'juin',
             7 => 'juillet', 8 => 'août', 9 => 'septembre', 10 => 'octobre', 11 => 'novembre', 12 => 'décembre'];

    $date = new DateTime($datetime);

    return (int) $date->format('j') . ' ' . $mois[(int) $date->format('n')] . ' ' . $date->format('Y');
}

// Coupe un texte trop long et ajoute des points de suspension.
function extrait($texte, $longueur = 160) {
    if (mb_strlen($texte) > $longueur) {
        return mb_substr($texte, 0, $longueur) . '…';
    }

    return $texte;
}
