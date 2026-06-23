<?php $currentPage = isset($page) ? $page : ''; ?>
<header>
    <div class="header1">
        <a href="index.php" class="logo" aria-label="Cinévo, accueil">
            CINÉVO
            <span class="dot" aria-hidden="true"></span>
        </a>

        <div class="header2">
            <span class="icon"></span>
            <input type="search" placeholder="Chercher un film, une série, une personne, une liste...">
            <button class="burger" id="burgerBtn" aria-label="Menu" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>
            <nav id="mainNav">
                <a href="decouvrir.php" <?= $currentPage === 'decouvrir' ? 'class="active"' : '' ?>>Découvrir</a>
                <a href="connexion.php" <?= $currentPage === 'connexion' ? 'class="active"' : '' ?>>Connexion</a>
                <a href="inscription.php">
                    <button class="terra">Rejoindre</button>
                </a>
            </nav>
        </div>
    </div>
</header>
