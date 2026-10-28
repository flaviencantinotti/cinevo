-- Titres des avis de démonstration.
--
-- Le titre d'un avis est obligatoire depuis la mise à jour de la page
-- d'écriture ; les avis importés avant celle-ci n'en avaient pas, ce qui
-- laissait un trou dans les cartes de l'accueil. Ce fichier les complète.
-- Sans effet sur les avis déjà titrés, et sans danger à relancer.
--
-- En PowerShell (XAMPP) :
--   Get-Content data/titres-avis.sql -Raw -Encoding UTF8 | C:\xampp\mysql\bin\mysql.exe -u root cinevo --default-character-set=utf8mb4
-- En bash :
--   mysql -u root cinevo --default-character-set=utf8mb4 < data/titres-avis.sql

UPDATE avis SET titre = 'La métaphore prend toute la place'
 WHERE (titre IS NULL OR titre = '') AND contenu LIKE 'Pas totalement d\'accord%';
UPDATE avis SET titre = 'Le genre de film dont on reparle à table'
 WHERE (titre IS NULL OR titre = '') AND contenu LIKE 'Je l\'ai vu en famille%';
UPDATE avis SET titre = 'Sympathique, sans plus'
 WHERE (titre IS NULL OR titre = '') AND contenu LIKE 'Sympathique sans plus%';
UPDATE avis SET titre = 'Le rouge qui fait tout basculer'
 WHERE (titre IS NULL OR titre = '') AND contenu LIKE 'Le noir et blanc n\'est jamais%';
UPDATE avis SET titre = 'Du grand cinéma, et une sortie difficile'
 WHERE (titre IS NULL OR titre = '') AND contenu LIKE 'Phoenix est hallucinant%';
UPDATE avis SET titre = 'Excessif, et terriblement juste'
 WHERE (titre IS NULL OR titre = '') AND contenu LIKE 'Je n\'ai pas dormi la nuit%';
UPDATE avis SET titre = 'Dénonce-t-il vraiment ce monde-là ?'
 WHERE (titre IS NULL OR titre = '') AND contenu LIKE 'Trois heures qui passent vite%';
UPDATE avis SET titre = 'Le grain du bricolage'
 WHERE (titre IS NULL OR titre = '') AND contenu LIKE 'Revu en version originale%';
UPDATE avis SET titre = 'Mon fils a adoré, moi un peu moins'
 WHERE (titre IS NULL OR titre = '') AND contenu LIKE 'Emmené mon fils de 8 ans%';
UPDATE avis SET titre = 'Ombrage, plus effrayante que Voldemort'
 WHERE (titre IS NULL OR titre = '') AND contenu LIKE 'Revu la saga en entier%';
UPDATE avis SET titre = 'La scène des portes, toujours un modèle'
 WHERE (titre IS NULL OR titre = '') AND contenu LIKE 'La scène des portes%';
UPDATE avis SET titre = 'On est sorties toutes les deux en larmes'
 WHERE (titre IS NULL OR titre = '') AND contenu LIKE 'Emmenée ma fille de 6 ans%';
UPDATE avis SET titre = 'Une semaine à dormir la lumière allumée'
 WHERE (titre IS NULL OR titre = '') AND contenu LIKE 'Je n\'ai plus dormi la lumière%';
UPDATE avis SET titre = 'Fidèle sans être paresseux'
 WHERE (titre IS NULL OR titre = '') AND contenu LIKE 'Fidèle aux jeux sans être%';
