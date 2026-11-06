<?php
require_once __DIR__ . '/../includes/validation.php';

verifie('e-mail correct accepté', erreurEmail('ana@exemple.fr') === null);
verifie('e-mail sans @ refusé', erreurEmail('ana.exemple.fr') !== null);
verifie('e-mail de 101 caractères refusé', erreurEmail(str_repeat('a', 60) . '@' . str_repeat('b', 37) . '.fr') === 'Cette adresse e-mail est trop longue.');

verifie('mots de passe identiques et assez longs acceptés', erreurMotDePasse('secret1', 'secret1') === null);
verifie('mots de passe différents refusés', erreurMotDePasse('secret1', 'secret2') === 'Les mots de passe ne correspondent pas.');
verifie('mot de passe trop court refusé', str_contains(erreurMotDePasse('abc', 'abc'), 'au moins 6'));
verifie('la différence est signalée avant la longueur', erreurMotDePasse('abc', 'abd') === 'Les mots de passe ne correspondent pas.');
verifie('6 caractères pile acceptés', erreurMotDePasse('abcdef', 'abcdef') === null);

verifie('titre vide refusé', erreurTitreAvis('') === 'Donnez un titre à votre avis.');
verifie('titre de 120 caractères accepté', erreurTitreAvis(str_repeat('é', 120)) === null);
verifie('titre de 121 caractères refusé (compté en caractères, pas en octets)', erreurTitreAvis(str_repeat('é', 121)) !== null);

verifie('avis de 19 caractères refusé', erreurContenuAvis(str_repeat('é', 19)) !== null);
verifie('avis de 20 caractères accepté', erreurContenuAvis(str_repeat('é', 20)) === null);

verifie('avis de 10000 caractères accepté', erreurContenuAvis(str_repeat('é', AVIS_LONGUEUR_MAX)) === null);
verifie('avis de 10001 caractères refusé', str_contains(erreurContenuAvis(str_repeat('é', AVIS_LONGUEUR_MAX + 1)), 'ne doit pas dépasser'));

verifie('nom de contact de 100 caractères accepté', erreurNomContact(str_repeat('é', 100)) === null);
verifie('nom de contact de 101 caractères refusé', erreurNomContact(str_repeat('é', 101)) !== null);
verifie('message de contact de 5000 caractères accepté', erreurMessageContact(str_repeat('é', 5000)) === null);
verifie('message de contact de 5001 caractères refusé', erreurMessageContact(str_repeat('é', 5001)) !== null);

$types   = ['image/png' => 'png'];
$tmpPng  = tempnam(sys_get_temp_dir(), 'ph');
file_put_contents($tmpPng, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg=='));
$tmpTxt  = tempnam(sys_get_temp_dir(), 'ph');
file_put_contents($tmpTxt, 'pas une image');
$envoi = fn($erreur, $taille, $tmp) => ['error' => $erreur, 'size' => $taille, 'tmp_name' => $tmp];

verifie('photo : aucun fichier choisi refusé', erreurPhotoTeleversee($envoi(UPLOAD_ERR_NO_FILE, 0, ''), 1000, $types) === 'Choisissez une image avant de valider.');
verifie('photo : limite du serveur dépassée refusée', str_contains(erreurPhotoTeleversee($envoi(UPLOAD_ERR_INI_SIZE, 0, ''), 3145728, $types), 'serveur (3 Mo)'));
verifie('photo : fichier trop gros refusé', str_contains(erreurPhotoTeleversee($envoi(UPLOAD_ERR_OK, 2000, $tmpPng), 1000, $types), 'ne doit pas dépasser'));
verifie('photo : un texte n\'est pas une image', str_contains(erreurPhotoTeleversee($envoi(UPLOAD_ERR_OK, 13, $tmpTxt), 1000, $types), 'Format non supporté'));
verifie('photo : un vrai PNG accepté', erreurPhotoTeleversee($envoi(UPLOAD_ERR_OK, 70, $tmpPng), 1000, $types) === null);

unlink($tmpPng);
unlink($tmpTxt);
