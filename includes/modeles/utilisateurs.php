<?php

function trouverUtilisateur($conn, $utilisateurId) {
    $stmt = $conn->prepare("SELECT nom_utilisateur, email, mot_de_passe, photo_profil FROM utilisateurs WHERE id = ?");
    $stmt->bind_param('i', $utilisateurId);
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc();
}

function trouverIdParEmail($conn, $email) {
    $stmt = $conn->prepare("SELECT id FROM utilisateurs WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $ligne = $stmt->get_result()->fetch_assoc();

    return $ligne ? (int) $ligne['id'] : null;
}

function changerEmail($conn, $utilisateurId, $email) {
    $stmt = $conn->prepare("UPDATE utilisateurs SET email = ? WHERE id = ?");
    $stmt->bind_param('si', $email, $utilisateurId);
    $stmt->execute();
}

function changerMotDePasse($conn, $utilisateurId, $motDePasseHache) {
    $stmt = $conn->prepare("UPDATE utilisateurs SET mot_de_passe = ? WHERE id = ?");
    $stmt->bind_param('si', $motDePasseHache, $utilisateurId);
    $stmt->execute();
}

function changerPhoto($conn, $utilisateurId, $nomFichier) {
    $stmt = $conn->prepare("UPDATE utilisateurs SET photo_profil = ? WHERE id = ?");
    $stmt->bind_param('si', $nomFichier, $utilisateurId);
    $stmt->execute();
}
