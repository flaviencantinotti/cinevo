<?php

const MOT_DE_PASSE_LONGUEUR_MIN = 6;
const EMAIL_LONGUEUR_MAX        = 100;
const TITRE_AVIS_LONGUEUR_MAX   = 120;
const AVIS_LONGUEUR_MIN         = 20;
// Bien en dessous de la colonne TEXT (65535 octets, soit environ 16000 caractères accentués).
const AVIS_LONGUEUR_MAX         = 10000;
// Plus grand INT de la base : au-delà, l'INSERT d'un film_id échoue.
const ENTIER_SQL_MAX            = 2147483647;
const NOM_CONTACT_LONGUEUR_MAX  = 100;
const MESSAGE_CONTACT_LONGUEUR_MAX = 5000;

// Chaque fonction renvoie le message à afficher, ou null quand la valeur est correcte.

function erreurEmail($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'Cette adresse e-mail n\'est pas valide.';
    }

    if (strlen($email) > EMAIL_LONGUEUR_MAX) {
        return 'Cette adresse e-mail est trop longue.';
    }

    return null;
}

function erreurMotDePasse($motDePasse, $confirmation) {
    if ($motDePasse !== $confirmation) {
        return 'Les mots de passe ne correspondent pas.';
    }

    if (strlen($motDePasse) < MOT_DE_PASSE_LONGUEUR_MIN) {
        return 'Le mot de passe doit faire au moins ' . MOT_DE_PASSE_LONGUEUR_MIN . ' caractères.';
    }

    return null;
}

function erreurTitreAvis($titre) {
    if ($titre === '') {
        return 'Donnez un titre à votre avis.';
    }

    if (mb_strlen($titre) > TITRE_AVIS_LONGUEUR_MAX) {
        return 'Le titre ne doit pas dépasser ' . TITRE_AVIS_LONGUEUR_MAX . ' caractères.';
    }

    return null;
}

function erreurContenuAvis($contenu) {
    if (mb_strlen($contenu) < AVIS_LONGUEUR_MIN) {
        return 'Votre avis doit faire au moins ' . AVIS_LONGUEUR_MIN . ' caractères.';
    }

    if (mb_strlen($contenu) > AVIS_LONGUEUR_MAX) {
        return 'Votre avis ne doit pas dépasser ' . AVIS_LONGUEUR_MAX . ' caractères.';
    }

    return null;
}

function erreurNomContact($nom) {
    if (mb_strlen($nom) > NOM_CONTACT_LONGUEUR_MAX) {
        return 'Le nom ne doit pas dépasser ' . NOM_CONTACT_LONGUEUR_MAX . ' caractères.';
    }

    return null;
}

function erreurMessageContact($message) {
    if (mb_strlen($message) > MESSAGE_CONTACT_LONGUEUR_MAX) {
        return 'Le message ne doit pas dépasser ' . MESSAGE_CONTACT_LONGUEUR_MAX . ' caractères.';
    }

    return null;
}

function erreurPhotoTeleversee($fichier, $tailleMaxOctets, array $typesAutorises) {
    $tailleMaxMo = round($tailleMaxOctets / 1024 / 1024, 1);

    if (!$fichier || $fichier['error'] === UPLOAD_ERR_NO_FILE) {
        return 'Choisissez une image avant de valider.';
    }

    if ($fichier['error'] === UPLOAD_ERR_INI_SIZE || $fichier['error'] === UPLOAD_ERR_FORM_SIZE) {
        return 'L\'image dépasse la taille maximale autorisée par le serveur (' . $tailleMaxMo . ' Mo).';
    }

    if ($fichier['error'] !== UPLOAD_ERR_OK) {
        return 'L\'envoi a échoué, réessayez.';
    }

    if ($fichier['size'] > $tailleMaxOctets) {
        return 'L\'image ne doit pas dépasser ' . $tailleMaxMo . ' Mo.';
    }

    // Le type vient du contenu du fichier, jamais du nom ni du type annoncé par le navigateur.
    if (!isset($typesAutorises[mime_content_type($fichier['tmp_name'])])) {
        return 'Format non supporté : utilisez une image JPG, PNG ou WebP.';
    }

    return null;
}
