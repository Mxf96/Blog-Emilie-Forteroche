<?php

/**
 * Page de gestion des commentaires.
 * @var Comment[] $comments
 */

?>

<h2>Gestion des commentaires</h2>
<div class="adminArticle">
    <?php foreach ($comments as $comment) { ?>
        <div class="articleLine">
            <div class="title">
                <?= Utils::format($comment->getPseudo()) ?>
            </div>

            <div class="content">
                <?= Utils::format($comment->getContent()) ?>
            </div>

            <div>
                <?= Utils::convertDateToFrenchFormat($comment->getDateCreation()) ?>
            </div>

            <div>
                <!-- 
                    Bouton permettant de supprimer un commentaire.
                -->
                <a class="submit"
                    href="index.php?action=deleteComment&id=<?= $comment->getId() ?>"
                    <?= Utils::askConfirmation("Êtes-vous sûr de vouloir supprimer ce commentaire ?") ?>>
                    Supprimer
                </a>
            </div>
        </div>
    <?php } ?>
</div>

<a class="submit" href="index.php?action=admin">
    Retour administration
</a>