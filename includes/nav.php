<nav id="mainNav">
    <a href="decouvrir.php" <?= $pageCourante === 'decouvrir' ? 'class="actif"' : '' ?>>Découvrir</a>
    <a href="avis.php" <?= $pageCourante === 'avis' ? 'class="actif"' : '' ?>>Discussions</a>
    <a href="hasard.php" <?= $pageCourante === 'hasard' ? 'class="actif"' : '' ?>>Au hasard</a>
    <?php if (estConnecte()): ?>
        <a href="home.php" <?= $pageCourante === 'home' ? 'class="actif"' : '' ?>>Mon fil</a>
        <a href="mes-avis.php" <?= $pageCourante === 'mes-avis' ? 'class="actif"' : '' ?>>Mes avis</a>
    <?php endif; ?>
</nav>
