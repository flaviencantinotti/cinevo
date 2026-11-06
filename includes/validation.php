<?php

const MOT_DE_PASSE_LONGUEUR_MIN = 6;
const EMAIL_LONGUEUR_MAX        = 100;
const TITRE_AVIS_LONGUEUR_MAX   = 120;
const AVIS_LONGUEUR_MIN         = 20;

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

    return null;
}
