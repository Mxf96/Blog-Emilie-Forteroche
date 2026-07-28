<?php

/**
 * Page de monitoring de l'administration.
 * Affichage des statistiques récupérées depuis AdminController.
 * 
 * @var int $nbArticles
 * @var int $nbComments
 * @var int $totalViews
 * @var Article|null $mostViewedArticle
 * @var Article[] $articles
 * @var string $sort
 * @var string $order
 */

// Permet d'inverser le sens du tri au prochain clic.
$newOrder = ($order === "ASC") ? "DESC" : "ASC";
?>

<section class="monitoring">
    <h2>Monitoring</h2>
    <div class="stats">
        <article>
            <h3>Nombre d'articles</h3>
            <p>
                <?= $nbArticles ?>
            </p>
        </article>

        <article>
            <h3>Nombre de commentaires</h3>
            <p>
                <?= $nbComments ?>
            </p>
        </article>

        <article>
            <h3>Nombre total de vues</h3>
            <p>
                <?= $totalViews ?>
            </p>
        </article>

        <article>
            <h3>Article le plus consulté</h3>

            <?php if ($mostViewedArticle) { ?>

                <p>
                    <?= Utils::format($mostViewedArticle->getTitle()) ?>
                    -
                    <?= $mostViewedArticle->getNbViews() ?> vues
                </p>

            <?php } else { ?>

                <p>
                    Aucun article disponible.
                </p>

            <?php } ?>

        </article>
    </div>

    <!-- 
        Ajout d'un tableau permettant de trier les articles.
     -->
    <h2>Liste des articles</h2>

    <table class="monitoringTable">
        <thead>
            <tr>
                <th>
                    <!-- 
                        Lien permettant de trier par titre.
                    -->
                    <a href="index.php?action=monitoring&sort=title&order=<?= $newOrder ?>">
                        Titre
                        <?php if ($sort === "title") { ?>
                            <?= $order === "ASC" ? "▲" : "▼" ?>
                        <?php } ?>
                    </a>
                </th>
                <th>
                    <!-- 
                        Lien permettant de trier par date.
                     -->
                    <a href="index.php?action=monitoring&sort=date&order=<?= $newOrder ?>">
                        Date création

                        <?php if ($sort === "date") { ?>
                            <?= $order === "ASC" ? "▲" : "▼" ?>
                        <?php } ?>

                    </a>
                </th>

                <th>
                    <!-- 
                        Lien permettant de trier par nombre de vues.
                     -->
                    <a href="index.php?action=monitoring&sort=views&order=<?= $newOrder ?>">
                        Nombre de vues

                        <?php if ($sort === "views") { ?>
                            <?= $order === "ASC" ? "▲" : "▼" ?>
                        <?php } ?>

                    </a>
                </th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($articles as $article) { ?>
                <tr>
                    <td>
                        <?= Utils::format($article->getTitle()) ?>
                    </td>

                    <td>
                        <?= Utils::convertDateToFrenchFormat($article->getDateCreation()) ?>
                    </td>

                    <td>
                        <?= $article->getNbViews() ?>
                    </td>

                </tr>
            <?php } ?>
        </tbody>
    </table>

    <!-- 
        Permet de revenir facilement à l'espace administration. 
    -->
    <a class="submit" href="index.php?action=admin">
        Retour administration
    </a>

</section>