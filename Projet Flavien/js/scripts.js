/* ============================================================
   Cinévo — scripts.js
   Burger menu + interactions générales
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {

    /* --- Burger menu --- */
    var btn = document.getElementById('burgerBtn');
    var nav = document.getElementById('mainNav');

    if (btn && nav) {
        btn.addEventListener('click', function () {
            var isOpen = nav.classList.toggle('open');
            btn.classList.toggle('open', isOpen);
            btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        /* Ferme le menu si on clique en dehors */
        document.addEventListener('click', function (e) {
            if (!btn.contains(e.target) && !nav.contains(e.target)) {
                nav.classList.remove('open');
                btn.classList.remove('open');
                btn.setAttribute('aria-expanded', 'false');
            }
        });

        /* Ferme le menu sur redimensionnement (retour desktop) */
        window.addEventListener('resize', function () {
            if (window.innerWidth > 768) {
                nav.classList.remove('open');
                btn.classList.remove('open');
                btn.setAttribute('aria-expanded', 'false');
            }
        });
    }

    /* --- Compteur de caractères page écrire --- */
    var avisArea = document.getElementById('avis');
    var compteur = document.getElementById('compteur');
    var btnPublier = document.getElementById('btnPublier');

    if (avisArea && compteur) {
        var MIN = 20;

        function majCompteur() {
            var len = avisArea.value.length;
            compteur.textContent = len;

            if (btnPublier) {
                btnPublier.disabled = len < MIN;
                btnPublier.style.opacity = len < MIN ? '0.5' : '1';
                btnPublier.style.cursor = len < MIN ? 'not-allowed' : 'pointer';
            }
        }

        avisArea.addEventListener('input', majCompteur);
        majCompteur();

        /* Auto-resize textarea */
        avisArea.addEventListener('input', function () {
            this.style.height = 'auto';
            this.style.height = Math.max(200, this.scrollHeight) + 'px';
        });
    }

});
