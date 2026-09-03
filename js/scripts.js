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

    /* --- Affiche de secours si une image ne se charge pas --- */
    var AFFICHE_SECOURS = 'images/affiche-indisponible.svg';

    document.addEventListener('error', function (e) {
        var cible = e.target;
        if (!cible || cible.tagName !== 'IMG') return;

        // Le photogramme du hero est large (16/9) : l'affiche de secours,
        // pensée pour un format portrait, y serait déformée. On masque
        // simplement l'image, le dégradé posé derrière suffit.
        if (cible.closest('.hero-fond')) {
            cible.style.display = 'none';
            return;
        }

        if (cible.getAttribute('src') !== AFFICHE_SECOURS) {
            cible.src = AFFICHE_SECOURS;
        }
    }, true); // en phase de capture : l'évènement « error » ne remonte pas

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
                .then(function (reponse) {
                    if (!reponse.ok) throw new Error('Réponse ' + reponse.status);
                    return reponse.json();
                })
                .then(function (films) {
                    if (!Array.isArray(films) || films.length === 0) {
                        grilleHasard.innerHTML = '<p class="message-erreur">Aucun film à proposer pour le moment. Réessayez dans un instant.</p>';
                        return;
                    }
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
