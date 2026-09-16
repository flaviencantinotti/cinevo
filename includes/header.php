<?php
require_once __DIR__ . '/auth.php';
$pageCourante = isset($page) ? $page : '';
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<header>
    <div class="entete">
        <a href="index.php" class="logo">CINÉVO</a>

        <nav id="mainNav">
            <a href="decouvrir.php" <?= $pageCourante === 'decouvrir' ? 'class="actif"' : '' ?>>Découvrir</a>
            <a href="avis.php" <?= $pageCourante === 'avis' ? 'class="actif"' : '' ?>>Discussions</a>
            <a href="hasard.php" <?= $pageCourante === 'hasard' ? 'class="actif"' : '' ?>>Au hasard</a>
            <?php if (estConnecte()): ?>
                <a href="home.php" <?= $pageCourante === 'home' ? 'class="actif"' : '' ?>>Mon fil</a>
                <a href="mes-avis.php" <?= $pageCourante === 'mes-avis' ? 'class="actif"' : '' ?>>Mes avis</a>
            <?php endif; ?>
        </nav>

        <div class="entete-nav">
            <form method="GET" action="recherche.php" class="barre-recherche">
                <input type="search" name="q" placeholder="Chercher un film">
            </form>
            <button class="menu-burger" id="burgerBtn" aria-label="Menu" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>
            <?php if (estConnecte()): ?>
                <a href="profil.php" class="entete-utilisateur"><?= htmlspecialchars(utilisateurConnecte()) ?></a>
                <a href="deconnexion.php" class="lien-fleche">Déconnexion</a>
            <?php else: ?>
                <a href="connexion.php" <?= $pageCourante === 'connexion' ? 'class="actif"' : '' ?>>Connexion</a>
                <a href="inscription.php" class="lien-fleche">Rejoindre</a>
            <?php endif; ?>
        </div>
    </div>
</header>
