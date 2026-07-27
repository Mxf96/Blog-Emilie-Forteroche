<?php

/**
 * Page de monitoring de l'administration.
 * Affichage des statistiques récupérées depuis AdminController.
 * 
 * @var int $nbArticles
 * @var int $nbComments
 * @var int $totalViews
 * @var Article|null $mostViewedArticle
 */
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
</section>