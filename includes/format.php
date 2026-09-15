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

// Avatar d'un membre : sa photo de profil si elle existe encore sur le
// disque, sinon le cercle coloré avec son initiale (identique partout,
// pour que la couleur d'un même pseudo ne change jamais d'une page à l'autre).
function avatarHtml($nomUtilisateur, $photoProfil = null, $classesSupp = '') {
    $classes = trim('avatar ' . $classesSupp);

    if (!empty($photoProfil) && is_file(__DIR__ . '/../uploads/avatars/' . $photoProfil)) {
        return '<img src="uploads/avatars/' . htmlspecialchars($photoProfil) . '"'
            . ' alt="Photo de ' . htmlspecialchars($nomUtilisateur) . '"'
            . ' class="' . htmlspecialchars($classes) . ' avatar-photo">';
    }

    $teinte   = crc32($nomUtilisateur) % 360;
    $initiale = htmlspecialchars(mb_strtoupper(mb_substr($nomUtilisateur, 0, 1)));

    return '<span class="' . htmlspecialchars($classes) . '" style="background: oklch(0.55 0.12 ' . $teinte . ');">'
        . $initiale . '</span>';
}
