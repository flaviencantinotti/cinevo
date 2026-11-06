<?php
require_once __DIR__ . '/auth.php';
$pageCourante = isset($page) ? $page : '';
?>
<header>
    <div class="entete">
        <a href="index.php" class="logo">CINÉVO</a>

        <?php include __DIR__ . '/nav.php'; ?>

        <div class="entete-nav">
            <form method="GET" action="recherche.php" class="barre-recherche">
                <input type="search" name="q" placeholder="Chercher un film" aria-label="Chercher un film">
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
