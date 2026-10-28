<?php
$page = 'fiche';

require_once 'includes/auth.php';
require_once 'includes/tmdb.php';
require_once 'includes/db.php';
require_once 'includes/format.php';
$tmdb = new TMDB();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$film        = null;
$annee       = '';
$duree       = '';
$realisateur = '';
$genres      = [];

if ($id > 0) {
    $film = $tmdb->getMovie($id);

    if ($film) {
        $annee = substr($film['release_date'] ?? '', 0, 4);

        if (!empty($film['runtime'])) {
            $heures = intdiv($film['runtime'], 60);
            $mins   = $film['runtime'] % 60;

            $duree  = $heures > 0 ? $heures . ' h ' . $mins . ' min' : $mins . ' min';
        }

        foreach (($film['credits']['crew'] ?? []) as $membre) {
            if ($membre['job'] === 'Director') {
                $realisateur = $membre['name'];
                break;
            }
        }

        $genres = $film['genres'] ?? [];
    }
}

if (!$film) {
    http_response_code(404);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php if ($film): ?>
        <?php
            $descriptionMeta = trim(($realisateur ? $realisateur . ' · ' : '') . ($film['overview'] ?? ''));
            $descriptionMeta = $descriptionMeta !== '' ? $descriptionMeta : 'Lisez les avis et critiques de la communauté Cinévo sur ' . $film['title'] . '.';
        ?>
        <meta name="description" content="<?= htmlspecialchars(mb_substr($descriptionMeta, 0, 155)) ?>">
        <title><?= htmlspecialchars($film['title']) ?> (<?= $annee ?>) · Critique et avis | Cinévo</title>
    <?php else: ?>
        <meta name="robots" content="noindex, follow">
        <title>Film introuvable · Cinévo</title>
    <?php endif; ?>
    <link rel="stylesheet" type="text/css" href="css/style.css?v=94">
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="contenu">

    <?php if (!$film): ?>

        <div class="bloc-centre">
            <h1>Film introuvable.</h1>
            <p class="intro">L'identifiant ne correspond à aucun film connu.</p>
            <a href="index.php" class="btn-rouge bouton-espace">Retour à l'accueil</a>
        </div>

    <?php else: ?>

        <article class="detail-film">

            <div>
                <div class="grande-affiche">
                    <img src="<?= htmlspecialchars($tmdb->getPosterUrl($film['poster_path'], 'w342')) ?>"
                         alt="Affiche de <?= htmlspecialchars($film['title']) ?>"
                         style="width:100%; height:100%; object-fit:cover;">
                </div>
            </div>

            <div>
                <h1 class="titre-film"><?= htmlspecialchars($film['title']) ?></h1>

                <?php
                $infosPresentes = [];

                foreach ([$realisateur, $annee, $duree] as $info) {
                    if ($info !== '') {
                        $infosPresentes[] = htmlspecialchars($info);
                    }
                }
                ?>
                <div class="infos-film">
                    <?= implode(' <span class="sep">·</span> ', $infosPresentes) ?>
                </div>

                <?php if (!empty($genres)): ?>
                    <div class="liste-genres">
                        <?php foreach ($genres as $genre): ?>
                            <span class="tag"><?= htmlspecialchars($genre['name']) ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($film['overview'])): ?>
                    <p class="synopsis"><?= htmlspecialchars($film['overview']) ?></p>
                <?php endif; ?>

                <div class="boutons-actions">
                    <a href="ecrire.php?id=<?= $id ?>" class="btn-rouge"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 4l6 6-11 11H3v-6z"></path>
                                <path d="M14 4l3-3 6 6-3 3"></path>
                            </svg>
                            Écrire un avis</a>
                </div>

                <div class="ou-voir">
                    <div class="titre-bloc">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="9"></circle>
                            <path d="m15 9-2 6-6 2 2-6z"></path>
                        </svg>
                        Où voir ce film
                    </div>

                    <?php
                    $providers = $film['watch/providers']['results']['FR'] ?? null;
                    $abonnement = $providers['flatrate'] ?? [];
                    $location   = $providers['rent'] ?? [];
                    ?>

                    <?php foreach ($abonnement as $p): ?>
                        <div class="ligne-plateforme">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="5" width="18" height="13" rx="2"></rect>
                                <path d="M8 21h8M12 18v3"></path>
                            </svg>
                            <div class="nom-plateforme"><?= htmlspecialchars($p['provider_name']) ?></div>
                            <div class="acces">Inclus dans l'abonnement</div>
                        </div>
                    <?php endforeach; ?>

                    <?php foreach ($location as $p): ?>
                        <div class="ligne-plateforme">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="9"></circle>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <div class="nom-plateforme"><?= htmlspecialchars($p['provider_name']) ?></div>
                            <div class="acces">Location disponible</div>
                        </div>
                    <?php endforeach; ?>

                    <?php if (empty($abonnement) && empty($location)): ?>
                        <p class="note-discrete">
                            Aucune plateforme disponible en France pour le moment.
                        </p>
                    <?php endif; ?>

                    <div class="source">Données fournies par TMDB et JustWatch</div>
                </div>
            </div>
        </article>

        <hr class="separateur">

        <section>
            <div class="entete-section">
                <h2>Avis sur le film</h2>
                <a href="ecrire.php?id=<?= $id ?>" class="btn-rouge" style="margin-left:auto;">Écrire un avis</a>
            </div>

            <p class="intro" style="margin-top:12px;">
                Aucune note ni classement ici : seulement l'avis de la communauté Cinévo sur
                <?= htmlspecialchars($film['title']) ?>, dans ses propres mots.
            </p>

            <?php
            $avisFilm = [];

            if (baseDisponible()) {
                $stmt = $conn->prepare("
                    SELECT a.id, a.utilisateur_id, a.titre, a.contenu, a.publie_le, u.nom_utilisateur, u.photo_profil
                    FROM avis a
                    JOIN utilisateurs u ON a.utilisateur_id = u.id
                    WHERE a.film_id = ?
                    ORDER BY a.publie_le DESC
                ");
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $avisFilm = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            }
            ?>

            <?php if (!baseDisponible()): ?>
                <?= messageBaseIndisponible('L\'affichage des avis') ?>
            <?php elseif (empty($avisFilm)): ?>
                <p class="note-discrete note-espacee">
                    Aucun avis pour ce film. Soyez le premier à en écrire un.
                </p>
            <?php else: ?>
                <div class="salon">
                    <?php foreach ($avisFilm as $avis):
                        $date = formaterDateFr($avis['publie_le']);
                    ?>
                        <div class="message">
                            <?= avatarHtml($avis['nom_utilisateur'], $avis['photo_profil']) ?>
                            <div class="message-corps">
                                <span class="message-auteur"><?= htmlspecialchars($avis['nom_utilisateur']) ?> · <?= $date ?></span>
                                <div class="message-bulle">
                                    <?php if (!empty($avis['titre'])): ?>
                                        <span class="message-titre"><?= htmlspecialchars($avis['titre']) ?></span>
                                    <?php endif; ?>
                                    <?= nl2br(htmlspecialchars($avis['contenu'])) ?>
                                </div>

                                <?php if (estConnecte() && $avis['utilisateur_id'] == $_SESSION['utilisateur_id']): ?>
                                    <div class="actions-avis" style="margin-top:8px;">
                                        <a href="modifier-avis.php?id=<?= (int) $avis['id'] ?>" class="btn-blanc">Modifier</a>
                                        <a href="supprimer-avis.php?id=<?= (int) $avis['id'] ?>" class="btn-transparent btn-danger">Supprimer</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="salon-reponse">
                        <p>Pas d'accord, ou envie d'ajouter votre avis sur <?= htmlspecialchars($film['title']) ?> ?</p>
                        <a href="ecrire.php?id=<?= $id ?>" class="btn-rouge">Répondre</a>
                    </div>
                </div>
            <?php endif; ?>
        </section>

    <?php endif; ?>

</main>

<?php include 'includes/footer.php'; ?>

<script src="js/scripts.js"></script>
</body>
</html>
