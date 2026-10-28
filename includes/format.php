<?php

function formaterDateFr($datetime) {
    $mois = [1 => 'janvier', 2 => 'février', 3 => 'mars', 4 => 'avril', 5 => 'mai', 6 => 'juin',
             7 => 'juillet', 8 => 'août', 9 => 'septembre', 10 => 'octobre', 11 => 'novembre', 12 => 'décembre'];

    $date = new DateTime($datetime);

    return (int) $date->format('j') . ' ' . $mois[(int) $date->format('n')] . ' ' . $date->format('Y');
}

function extrait($texte, $longueur = 160) {
    if (mb_strlen($texte) > $longueur) {
        return mb_substr($texte, 0, $longueur) . '…';
    }

    return $texte;
}

function tailleIniEnOctets($valeur) {
    $valeur = trim((string) $valeur);
    if ($valeur === '' || $valeur === '-1') {
        return PHP_INT_MAX;
    }

    $unite  = strtoupper(substr($valeur, -1));
    $nombre = (int) $valeur;

    switch ($unite) {
        case 'G': return $nombre * 1024 * 1024 * 1024;
        case 'M': return $nombre * 1024 * 1024;
        case 'K': return $nombre * 1024;
        default:  return (int) $valeur;
    }
}

function ajouterFilms(TMDB $tmdb, array $lignes) {
    foreach ($lignes as &$ligne) {
        $film = $tmdb->getMovie((int) $ligne['film_id']);

        $ligne['film_titre'] = $film['title'] ?? 'Film';
        $ligne['affiche']    = $tmdb->getPosterUrl($film['poster_path'] ?? null);
    }

    return $lignes;
}

function avatarHtml($nomUtilisateur, $photoProfil = null, $classesSupp = '') {
    $classes = trim('avatar ' . $classesSupp);

    if (!empty($photoProfil) && is_file(__DIR__ . '/../uploads/avatars/' . $photoProfil)) {
        return '<img src="uploads/avatars/' . htmlspecialchars($photoProfil) . '"'
            . ' alt="Photo de ' . htmlspecialchars($nomUtilisateur) . '"'
            . ' class="' . htmlspecialchars($classes) . ' avatar-photo">';
    }

    $initiale = htmlspecialchars(mb_strtoupper(mb_substr($nomUtilisateur, 0, 1)));

    return '<span class="' . htmlspecialchars($classes) . '">' . $initiale . '</span>';
}
