<?php
/**
 * Liste de résultats de recherche de films.
 *
 * Partagé par recherche.php (qui mène à la fiche du film) et ecrire.php (qui
 * mène au formulaire d'avis) : les deux pages affichaient le même bloc, écrit
 * deux fois, avec des longueurs d'aperçu différentes et un « … » ajouté même
 * quand le synopsis n'était pas coupé.
 *
 * Attend deux variables :
 *   $resultats   films renvoyés par l'API
 *   $lienFilm    page de destination : 'fiche.php' ou 'ecrire.php'
 */
?>
<div class="liste-resultats">
    <?php foreach ($resultats as $film):
        $annee   = substr($film['release_date'] ?? '', 0, 4);
        $affiche = $tmdb->getPosterUrl($film['poster_path'], 'w92');
    ?>
        <a href="<?= $lienFilm ?>?id=<?= (int) $film['id'] ?>" class="resultat-film">
            <img src="<?= htmlspecialchars($affiche) ?>"
                 alt="Affiche de <?= htmlspecialchars($film['title']) ?>"
                 class="affiche-mini"
                 loading="lazy">
            <div class="info-film">
                <div class="titre-resultat"><?= htmlspecialchars($film['title']) ?></div>
                <div class="meta-resultat">
                    <?= htmlspecialchars($annee) ?>
                    <?php if (!empty($film['original_title']) && $film['original_title'] !== $film['title']): ?>
                        · <?= htmlspecialchars($film['original_title']) ?>
                    <?php endif; ?>
                </div>
                <?php if (!empty($film['overview'])): ?>
                    <div class="apercu"><?= htmlspecialchars(extrait($film['overview'], 120)) ?></div>
                <?php endif; ?>
            </div>
        </a>
    <?php endforeach; ?>
</div>
