document.addEventListener('DOMContentLoaded', function () {

    /* --- Menu burger --- */
    var btn = document.getElementById('burgerBtn');
    var nav = document.getElementById('mainNav');

    if (btn && nav) {
        btn.addEventListener('click', function () {
            var ouvert = nav.classList.toggle('open');
            btn.classList.toggle('open', ouvert);
            btn.setAttribute('aria-expanded', ouvert ? 'true' : 'false');
        });

        document.addEventListener('click', function (e) {
            if (!btn.contains(e.target) && !nav.contains(e.target)) {
                nav.classList.remove('open');
                btn.classList.remove('open');
                btn.setAttribute('aria-expanded', 'false');
            }
        });

        window.addEventListener('resize', function () {
            if (window.innerWidth > 768) {
                nav.classList.remove('open');
                btn.classList.remove('open');
                btn.setAttribute('aria-expanded', 'false');
            }
        });
    }

    /* --- Compteur de caractères (page écrire) --- */
    var zoneAvis  = document.getElementById('avis');
    var compteur  = document.getElementById('compteur');
    var btnPublier = document.getElementById('btnPublier');

    if (zoneAvis && compteur) {
        var MIN = 20;

        function mettreAJourCompteur() {
            var nb = zoneAvis.value.length;
            compteur.textContent = nb;

            if (btnPublier) {
                btnPublier.disabled = nb < MIN;
                btnPublier.style.opacity = nb < MIN ? '0.5' : '1';
                btnPublier.style.cursor  = nb < MIN ? 'not-allowed' : 'pointer';
            }
        }

        zoneAvis.addEventListener('input', mettreAJourCompteur);
        mettreAJourCompteur();

        zoneAvis.addEventListener('input', function () {
            this.style.height = 'auto';
            this.style.height = Math.max(200, this.scrollHeight) + 'px';
        });
    }

    /* --- Films au hasard (page hasard.php) --- */
    var btnHasard   = document.getElementById('btnHasard');
    var grilleHasard = document.getElementById('grilleHasard');

    if (btnHasard && grilleHasard) {
        function echapperHtml(texte) {
            var div = document.createElement('div');
            div.textContent = texte;
            return div.innerHTML;
        }

        function construireCarte(film) {
            return '<a href="fiche.php?id=' + film.id + '" class="carte-hasard">' +
                '<div class="affiche-hasard"><img src="' + film.affiche + '" alt="Affiche de ' + echapperHtml(film.titre) + '" loading="lazy"></div>' +
                '<div class="titre-hasard">' + echapperHtml(film.titre) + '</div>' +
                '<div class="annee-hasard">' + echapperHtml(film.annee) + '</div>' +
                '</a>';
        }

        btnHasard.addEventListener('click', function () {
            btnHasard.disabled = true;
            var contenuOriginal = btnHasard.innerHTML;
            btnHasard.textContent = 'Tirage en cours...';

            fetch('hasard.php?ajax=1')
                .then(function (reponse) { return reponse.json(); })
                .then(function (films) {
                    grilleHasard.innerHTML = films.map(construireCarte).join('');
                })
                .catch(function () {
                    grilleHasard.innerHTML = '<p class="message-erreur">Impossible de tirer de nouveaux films pour le moment.</p>';
                })
                .finally(function () {
                    btnHasard.disabled = false;
                    btnHasard.innerHTML = contenuOriginal;
                });
        });
    }

});
