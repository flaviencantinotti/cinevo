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
