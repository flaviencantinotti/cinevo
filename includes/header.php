<?php
require_once __DIR__ . '/auth.php';
$pageCourante = isset($page) ? $page : '';
?>
<header>
    <div class="entete">
        <a href="index.php" class="logo">
            CINÉVO
            <span class="logo-point"></span>
        </a>

        <div class="entete-nav">
            <form method="GET" action="recherche.php" class="barre-recherche">
                <input type="search" name="q" placeholder="Chercher un film...">
            </form>
            <button class="menu-burger" id="burgerBtn" aria-label="Menu" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>
            <nav id="mainNav">
                <a href="decouvrir.php" <?= $pageCourante === 'decouvrir' ? 'class="actif"' : '' ?>>Découvrir</a>
                <a href="hasard.php" <?= $pageCourante === 'hasard' ? 'class="actif"' : '' ?>>Au hasard</a>
                <?php if (estConnecte()): ?>
                    <a href="home.php" <?= $pageCourante === 'home' ? 'class="actif"' : '' ?>>Mon fil</a>
                    <span class="entete-utilisateur">Bonjour, <?= htmlspecialchars(utilisateurConnecte()) ?></span>
                    <a href="deconnexion.php">
                        <button class="btn-blanc">Déconnexion</button>
                    </a>
                <?php else: ?>
                    <a href="connexion.php" <?= $pageCourante === 'connexion' ? 'class="actif"' : '' ?>>Connexion</a>
                    <a href="inscription.php">
                        <button class="btn-rouge">Rejoindre</button>
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    </div>
</header>
