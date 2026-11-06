<?php

function derniersAvis($conn, $limite) {
    $stmt = $conn->prepare("
        SELECT avis.film_id, avis.titre, avis.contenu, avis.publie_le, utilisateurs.nom_utilisateur, utilisateurs.photo_profil
        FROM avis
        JOIN utilisateurs ON avis.utilisateur_id = utilisateurs.id
        ORDER BY avis.publie_le DESC
        LIMIT ?
    ");
    $stmt->bind_param('i', $limite);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function avisDuFilm($conn, $filmId) {
    $stmt = $conn->prepare("
        SELECT a.id, a.utilisateur_id, a.titre, a.contenu, a.publie_le, u.nom_utilisateur, u.photo_profil
        FROM avis a
        JOIN utilisateurs u ON a.utilisateur_id = u.id
        WHERE a.film_id = ?
        ORDER BY a.publie_le DESC
    ");
    $stmt->bind_param('i', $filmId);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function avisDeLUtilisateur($conn, $utilisateurId) {
    $stmt = $conn->prepare("
        SELECT id, film_id, titre, contenu, publie_le
        FROM avis
        WHERE utilisateur_id = ?
        ORDER BY publie_le DESC
    ");
    $stmt->bind_param('i', $utilisateurId);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function trouverAvisDeLUtilisateur($conn, $avisId, $utilisateurId) {
    $stmt = $conn->prepare("
        SELECT id, film_id, titre, contenu, publie_le
        FROM avis
        WHERE id = ? AND utilisateur_id = ?
    ");
    $stmt->bind_param('ii', $avisId, $utilisateurId);
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc();
}

function ajouterAvis($conn, $utilisateurId, $filmId, $titre, $contenu) {
    $stmt = $conn->prepare("INSERT INTO avis (utilisateur_id, film_id, titre, contenu) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('iiss', $utilisateurId, $filmId, $titre, $contenu);
    $stmt->execute();
}

function modifierAvis($conn, $avisId, $utilisateurId, $titre, $contenu) {
    $stmt = $conn->prepare("
        UPDATE avis
        SET titre = ?, contenu = ?
        WHERE id = ? AND utilisateur_id = ?
    ");
    $stmt->bind_param('ssii', $titre, $contenu, $avisId, $utilisateurId);
    $stmt->execute();
}

function supprimerAvis($conn, $avisId, $utilisateurId) {
    $stmt = $conn->prepare("DELETE FROM avis WHERE id = ? AND utilisateur_id = ?");
    $stmt->bind_param('ii', $avisId, $utilisateurId);
    $stmt->execute();
}

function compterFilmsAvecAvis($conn) {
    $ligne = $conn->query("SELECT COUNT(DISTINCT film_id) AS total FROM avis")->fetch_assoc();

    return (int) $ligne['total'];
}

function filmsLesPlusRecemmentCommentes($conn, $limite, $decalage) {
    $stmt = $conn->prepare("
        SELECT film_id, COUNT(*) AS nb_avis, MAX(publie_le) AS dernier_le
        FROM avis
        GROUP BY film_id
        ORDER BY dernier_le DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->bind_param('ii', $limite, $decalage);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function avisDesFilms($conn, array $filmIds) {
    if (empty($filmIds)) {
        return [];
    }

    $trous = implode(',', array_fill(0, count($filmIds), '?'));
    $stmt  = $conn->prepare("
        SELECT avis.film_id, avis.contenu, utilisateurs.nom_utilisateur, utilisateurs.photo_profil
        FROM avis
        JOIN utilisateurs ON avis.utilisateur_id = utilisateurs.id
        WHERE avis.film_id IN ($trous)
        ORDER BY avis.publie_le DESC
    ");
    $stmt->bind_param(str_repeat('i', count($filmIds)), ...$filmIds);
    $stmt->execute();

    $avisParFilm = [];

    foreach ($stmt->get_result()->fetch_all(MYSQLI_ASSOC) as $avis) {
        $avisParFilm[(int) $avis['film_id']][] = $avis;
    }

    return $avisParFilm;
}

function filmsAvecDerniereMaj($conn) {
    return $conn->query("
        SELECT film_id, MAX(publie_le) AS derniere_maj
        FROM avis
        GROUP BY film_id
    ")->fetch_all(MYSQLI_ASSOC);
}
