-- Avis de demonstration pour Cinevo
-- A importer une seule fois. En PowerShell (XAMPP) :
--   Get-Content data/avis-demo.sql -Raw -Encoding UTF8 | C:\xampp\mysql\bin\mysql.exe -u root cinevo --default-character-set=utf8mb4
-- En bash/mac/linux :
--   mysql -u root cinevo < data/avis-demo.sql
--
-- Les avis sont repartis sur les comptes existants, quel que soit leur pseudo (aucune dependance
-- sur un nom d'utilisateur precis). Necessite au moins un compte deja cree sur le site.

INSERT INTO avis (utilisateur_id, film_id, titre, contenu, publie_le)
SELECT u.id, 424, NULL, 'Le noir et blanc n\'est jamais un choix esthétique gratuit chez Spielberg : ici il installe une distance presque documentaire, avant que le rouge du manteau ne vienne tout faire basculer. Un film qu\'on ne regarde pas deux fois par plaisir, mais qu\'on n\'oublie jamais.', '2026-09-13 21:12:00'
FROM (SELECT id, ROW_NUMBER() OVER (ORDER BY id) AS rn, COUNT(*) OVER () AS total FROM utilisateurs) u
WHERE u.rn = (0 MOD u.total) + 1
AND NOT EXISTS (SELECT 1 FROM avis WHERE film_id = 424 AND contenu = 'Le noir et blanc n\'est jamais un choix esthétique gratuit chez Spielberg : ici il installe une distance presque documentaire, avant que le rouge du manteau ne vienne tout faire basculer. Un film qu\'on ne regarde pas deux fois par plaisir, mais qu\'on n\'oublie jamais.');

INSERT INTO avis (utilisateur_id, film_id, titre, contenu, publie_le)
SELECT u.id, 389, 'Un huis clos qui tient sur un regard', 'Toute l\'intrigue se joue dans une seule pièce et pourtant on ne s\'ennuie jamais une seconde : chaque acteur porte le doute différemment, et c\'est passionnant de regarder les visages changer plutôt que les décors.', '2026-09-12 19:40:00'
FROM (SELECT id, ROW_NUMBER() OVER (ORDER BY id) AS rn, COUNT(*) OVER () AS total FROM utilisateurs) u
WHERE u.rn = (1 MOD u.total) + 1
AND NOT EXISTS (SELECT 1 FROM avis WHERE film_id = 389 AND contenu = 'Toute l\'intrigue se joue dans une seule pièce et pourtant on ne s\'ennuie jamais une seconde : chaque acteur porte le doute différemment, et c\'est passionnant de regarder les visages changer plutôt que les décors.');

INSERT INTO avis (utilisateur_id, film_id, titre, contenu, publie_le)
SELECT u.id, 475557, NULL, 'Phoenix est hallucinant mais je suis sorti de la salle mal à l\'aise pendant deux jours. Pas sûr d\'avoir eu envie de ça, mais impossible de dire que ce n\'est pas du grand cinéma.', '2026-09-11 22:05:00'
FROM (SELECT id, ROW_NUMBER() OVER (ORDER BY id) AS rn, COUNT(*) OVER () AS total FROM utilisateurs) u
WHERE u.rn = (2 MOD u.total) + 1
AND NOT EXISTS (SELECT 1 FROM avis WHERE film_id = 475557 AND contenu = 'Phoenix est hallucinant mais je suis sorti de la salle mal à l\'aise pendant deux jours. Pas sûr d\'avoir eu envie de ça, mais impossible de dire que ce n\'est pas du grand cinéma.');

INSERT INTO avis (utilisateur_id, film_id, titre, contenu, publie_le)
SELECT u.id, 8587, 'Celui qui m\'a donné envie d\'aimer le cinéma', 'Revu pour la centième fois avec ma nièce la semaine dernière, et le Cercle de la vie me fait toujours autant d\'effet. Il y a des films qu\'on ne peut plus juger objectivement, celui-là en fait partie pour moi.', '2026-09-06 18:30:00'
FROM (SELECT id, ROW_NUMBER() OVER (ORDER BY id) AS rn, COUNT(*) OVER () AS total FROM utilisateurs) u
WHERE u.rn = (0 MOD u.total) + 1
AND NOT EXISTS (SELECT 1 FROM avis WHERE film_id = 8587 AND contenu = 'Revu pour la centième fois avec ma nièce la semaine dernière, et le Cercle de la vie me fait toujours autant d\'effet. Il y a des films qu\'on ne peut plus juger objectivement, celui-là en fait partie pour moi.');

INSERT INTO avis (utilisateur_id, film_id, titre, contenu, publie_le)
SELECT u.id, 569094, 'Une claque visuelle qui n\'oublie jamais l\'histoire', 'Chaque univers a sa propre grammaire graphique et pourtant tout reste lisible, c\'est un tour de force d\'animation. Miles reste attachant même noyé sous des idées visuelles toutes les trente secondes.', '2026-09-03 20:15:00'
FROM (SELECT id, ROW_NUMBER() OVER (ORDER BY id) AS rn, COUNT(*) OVER () AS total FROM utilisateurs) u
WHERE u.rn = (1 MOD u.total) + 1
AND NOT EXISTS (SELECT 1 FROM avis WHERE film_id = 569094 AND contenu = 'Chaque univers a sa propre grammaire graphique et pourtant tout reste lisible, c\'est un tour de force d\'animation. Miles reste attachant même noyé sous des idées visuelles toutes les trente secondes.');

INSERT INTO avis (utilisateur_id, film_id, titre, contenu, publie_le)
SELECT u.id, 933260, NULL, 'Je n\'ai pas dormi la nuit d\'après. C\'est gore, c\'est excessif, et pourtant ça dit quelque chose de très juste sur la pression qu\'on met aux femmes à vieillir bien. Pas pour tout le monde, mais je ne l\'oublierai pas.', '2026-08-28 23:02:00'
FROM (SELECT id, ROW_NUMBER() OVER (ORDER BY id) AS rn, COUNT(*) OVER () AS total FROM utilisateurs) u
WHERE u.rn = (2 MOD u.total) + 1
AND NOT EXISTS (SELECT 1 FROM avis WHERE film_id = 933260 AND contenu = 'Je n\'ai pas dormi la nuit d\'après. C\'est gore, c\'est excessif, et pourtant ça dit quelque chose de très juste sur la pression qu\'on met aux femmes à vieillir bien. Pas pour tout le monde, mais je ne l\'oublierai pas.');

INSERT INTO avis (utilisateur_id, film_id, titre, contenu, publie_le)
SELECT u.id, 106646, NULL, 'Trois heures qui passent vite mais qui laissent un arrière-goût bizarre : le film dénonce-t-il vraiment ce monde-là ou finit-il par le rendre désirable ? Je n\'ai toujours pas tranché après plusieurs visionnages.', '2026-08-22 17:45:00'
FROM (SELECT id, ROW_NUMBER() OVER (ORDER BY id) AS rn, COUNT(*) OVER () AS total FROM utilisateurs) u
WHERE u.rn = (0 MOD u.total) + 1
AND NOT EXISTS (SELECT 1 FROM avis WHERE film_id = 106646 AND contenu = 'Trois heures qui passent vite mais qui laissent un arrière-goût bizarre : le film dénonce-t-il vraiment ce monde-là ou finit-il par le rendre désirable ? Je n\'ai toujours pas tranché après plusieurs visionnages.');

INSERT INTO avis (utilisateur_id, film_id, titre, contenu, publie_le)
SELECT u.id, 11, NULL, 'Revu en version originale récemment : ce qui frappe, c\'est à quel point tout semble bricolé avec les moyens du bord, et c\'est précisément ce qui rend l\'univers vivant. Les effets numériques d\'aujourd\'hui ont perdu ce grain-là.', '2026-08-15 20:50:00'
FROM (SELECT id, ROW_NUMBER() OVER (ORDER BY id) AS rn, COUNT(*) OVER () AS total FROM utilisateurs) u
WHERE u.rn = (1 MOD u.total) + 1
AND NOT EXISTS (SELECT 1 FROM avis WHERE film_id = 11 AND contenu = 'Revu en version originale récemment : ce qui frappe, c\'est à quel point tout semble bricolé avec les moyens du bord, et c\'est précisément ce qui rend l\'univers vivant. Les effets numériques d\'aujourd\'hui ont perdu ce grain-là.');

INSERT INTO avis (utilisateur_id, film_id, titre, contenu, publie_le)
SELECT u.id, 950387, NULL, 'Emmené mon fils de 8 ans, il a adoré, moi un peu moins convaincu par le scénario. Mais l\'entendre rire pendant une heure et demie, ça n\'a pas de prix.', '2026-08-09 16:20:00'
FROM (SELECT id, ROW_NUMBER() OVER (ORDER BY id) AS rn, COUNT(*) OVER () AS total FROM utilisateurs) u
WHERE u.rn = (2 MOD u.total) + 1
AND NOT EXISTS (SELECT 1 FROM avis WHERE film_id = 950387 AND contenu = 'Emmené mon fils de 8 ans, il a adoré, moi un peu moins convaincu par le scénario. Mais l\'entendre rire pendant une heure et demie, ça n\'a pas de prix.');

INSERT INTO avis (utilisateur_id, film_id, titre, contenu, publie_le)
SELECT u.id, 675, NULL, 'Revu la saga en entier cet été et celui-là reste mon préféré : Ombrage est un des meilleurs méchants du cinéma jeunesse, plus effrayante que Voldemort parce que terriblement crédible.', '2026-09-02 20:10:00'
FROM (SELECT id, ROW_NUMBER() OVER (ORDER BY id) AS rn, COUNT(*) OVER () AS total FROM utilisateurs) u
WHERE u.rn = (0 MOD u.total) + 1
AND NOT EXISTS (SELECT 1 FROM avis WHERE film_id = 675 AND contenu = 'Revu la saga en entier cet été et celui-là reste mon préféré : Ombrage est un des meilleurs méchants du cinéma jeunesse, plus effrayante que Voldemort parce que terriblement crédible.');

INSERT INTO avis (utilisateur_id, film_id, titre, contenu, publie_le)
SELECT u.id, 808, 'Le seul dessin animé qui a autant marqué les parents que les enfants', 'Les blagues visées adultes passent toujours aussi bien vingt ans après. Rare de voir un film pour enfants qui ne prend jamais les enfants pour des idiots, ni les adultes pour acquis.', '2026-08-30 19:00:00'
FROM (SELECT id, ROW_NUMBER() OVER (ORDER BY id) AS rn, COUNT(*) OVER () AS total FROM utilisateurs) u
WHERE u.rn = (1 MOD u.total) + 1
AND NOT EXISTS (SELECT 1 FROM avis WHERE film_id = 808 AND contenu = 'Les blagues visées adultes passent toujours aussi bien vingt ans après. Rare de voir un film pour enfants qui ne prend jamais les enfants pour des idiots, ni les adultes pour acquis.');

INSERT INTO avis (utilisateur_id, film_id, titre, contenu, publie_le)
SELECT u.id, 585, NULL, 'La scène des portes est un modèle d\'action et de mise en scène, encore aujourd\'hui. Pixar au sommet de son inventivité visuelle, bien avant de se reposer sur ses suites.', '2026-08-25 21:20:00'
FROM (SELECT id, ROW_NUMBER() OVER (ORDER BY id) AS rn, COUNT(*) OVER () AS total FROM utilisateurs) u
WHERE u.rn = (2 MOD u.total) + 1
AND NOT EXISTS (SELECT 1 FROM avis WHERE film_id = 585 AND contenu = 'La scène des portes est un modèle d\'action et de mise en scène, encore aujourd\'hui. Pixar au sommet de son inventivité visuelle, bien avant de se reposer sur ses suites.');

INSERT INTO avis (utilisateur_id, film_id, titre, contenu, publie_le)
SELECT u.id, 1022789, NULL, 'Emmenée ma fille de 6 ans, on est sorties toutes les deux en larmes. Ça parle autant aux enfants qu\'aux parents qui les regardent grandir, ce qui est rare pour une suite.', '2026-08-19 18:05:00'
FROM (SELECT id, ROW_NUMBER() OVER (ORDER BY id) AS rn, COUNT(*) OVER () AS total FROM utilisateurs) u
WHERE u.rn = (0 MOD u.total) + 1
AND NOT EXISTS (SELECT 1 FROM avis WHERE film_id = 1022789 AND contenu = 'Emmenée ma fille de 6 ans, on est sorties toutes les deux en larmes. Ça parle autant aux enfants qu\'aux parents qui les regardent grandir, ce qui est rare pour une suite.');

INSERT INTO avis (utilisateur_id, film_id, titre, contenu, publie_le)
SELECT u.id, 911430, 'Du spectacle, mais pas grand-chose derrière', 'Techniquement impressionnant, les courses sont filmées avec un vrai sens du rythme. Le scénario en revanche ne surprend jamais une seconde, on connaît l\'arc du pilote vieillissant par cœur.', '2026-08-12 22:40:00'
FROM (SELECT id, ROW_NUMBER() OVER (ORDER BY id) AS rn, COUNT(*) OVER () AS total FROM utilisateurs) u
WHERE u.rn = (1 MOD u.total) + 1
AND NOT EXISTS (SELECT 1 FROM avis WHERE film_id = 911430 AND contenu = 'Techniquement impressionnant, les courses sont filmées avec un vrai sens du rythme. Le scénario en revanche ne surprend jamais une seconde, on connaît l\'arc du pilote vieillissant par cœur.');

INSERT INTO avis (utilisateur_id, film_id, titre, contenu, publie_le)
SELECT u.id, 1038392, NULL, 'Je n\'ai plus dormi la lumière éteinte pendant une semaine. Efficace du début à la fin, avec de vrais moments de mise en scène plutôt que des jumpscares gratuits.', '2026-08-05 23:15:00'
FROM (SELECT id, ROW_NUMBER() OVER (ORDER BY id) AS rn, COUNT(*) OVER () AS total FROM utilisateurs) u
WHERE u.rn = (2 MOD u.total) + 1
AND NOT EXISTS (SELECT 1 FROM avis WHERE film_id = 1038392 AND contenu = 'Je n\'ai plus dormi la lumière éteinte pendant une semaine. Efficace du début à la fin, avec de vrais moments de mise en scène plutôt que des jumpscares gratuits.');

INSERT INTO avis (utilisateur_id, film_id, titre, contenu, publie_le)
SELECT u.id, 502356, NULL, 'Fidèle aux jeux sans être paresseux, et visuellement très soigné. Pas le film le plus original de l\'année mais mon neveu de 7 ans l\'a regardé trois fois en une semaine.', '2026-07-29 17:30:00'
FROM (SELECT id, ROW_NUMBER() OVER (ORDER BY id) AS rn, COUNT(*) OVER () AS total FROM utilisateurs) u
WHERE u.rn = (0 MOD u.total) + 1
AND NOT EXISTS (SELECT 1 FROM avis WHERE film_id = 502356 AND contenu = 'Fidèle aux jeux sans être paresseux, et visuellement très soigné. Pas le film le plus original de l\'année mais mon neveu de 7 ans l\'a regardé trois fois en une semaine.');

INSERT INTO avis (utilisateur_id, film_id, titre, contenu, publie_le)
SELECT u.id, 198663, 'Une bonne idée qui s\'essouffle avant la fin', 'Le concept de départ est vraiment prenant, le mystère du labyrinthe fonctionne bien pendant une heure. Puis le film semble ne plus savoir où aller et enchaîne les révélations un peu forcées.', '2026-07-20 20:50:00'
FROM (SELECT id, ROW_NUMBER() OVER (ORDER BY id) AS rn, COUNT(*) OVER () AS total FROM utilisateurs) u
WHERE u.rn = (1 MOD u.total) + 1
AND NOT EXISTS (SELECT 1 FROM avis WHERE film_id = 198663 AND contenu = 'Le concept de départ est vraiment prenant, le mystère du labyrinthe fonctionne bien pendant une heure. Puis le film semble ne plus savoir où aller et enchaîne les révélations un peu forcées.');

