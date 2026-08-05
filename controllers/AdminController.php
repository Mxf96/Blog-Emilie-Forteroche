<?php

/**
 * Contrôleur de la partie admin.
 */

class AdminController
{

    /**
     * Affiche la page d'administration.
     * @return void
     */
    public function showAdmin(): void
    {
        // On vérifie que l'utilisateur est connecté.
        $this->checkIfUserIsConnected();

        // On récupère les articles.
        $articleManager = new ArticleManager();
        $articles = $articleManager->getAllArticles();

        // On affiche la page d'administration.
        $view = new View("Administration");
        $view->render("admin", [
            'articles' => $articles
        ]);
    }


    /**
     * Affiche la page de monitoring.
     * @return void
     */
    public function showMonitoring(): void
    {
        // Vérifie que seule une personne connectée peut accéder au monitoring.
        $this->checkIfUserIsConnected();

        // Récupération des statistiques des articles.
        // Le contrôleur récupère les données nécessaires avant de les envoyer à la vue.
        $articleManager = new ArticleManager();

        $nbArticles = $articleManager->getNbArticles();

        $totalViews = $articleManager->getTotalViews();

        $mostViewedArticle = $articleManager->getMostViewedArticle();

        // Récupération de tous les articles afin de les afficher dans un tableau triable.
        $articles = $articleManager->getAllArticles();

        // Sécurisation des paramètres de tri.
        $sort = Utils::request("sort", "title");

        if (!in_array($sort, ["title", "date", "views"])) {
            $sort = "title";
        }


        $order = Utils::request("order", "ASC");

        if (!in_array($order, ["ASC", "DESC"])) {
            $order = "ASC";
        }

        // Tri réalisé directement en PHP avec usort().
        // Aucun langage externe ou librairie n'est utilisé.
        usort($articles, function ($a, $b) use ($sort, $order) {

            switch ($sort) {

                case "views":
                    // Tri selon le nombre de vues.
                    $result = $a->getNbViews() <=> $b->getNbViews();
                    break;


                case "date":
                    // Tri selon la date de création.
                    $result = $a->getDateCreation() <=> $b->getDateCreation();
                    break;


                case "title":
                default:
                    // Tri alphabétique selon le titre.
                    $result = strcmp($a->getTitle(), $b->getTitle());
                    break;
            }


            // Inversion du résultat si le tri demandé est décroissant.
            return $order === "ASC" ? $result : -$result;
        });

        // Récupération des statistiques des commentaires.
        // Permet d'afficher le nombre total de commentaires.
        $commentManager = new CommentManager();

        $nbComments = $commentManager->getNbComments();

        // Création de la vue monitoring.
        $view = new View("Monitoring");

        // Transmission des statistiques à la vue.
        // La vue reçoit maintenant les informations à afficher.
        $view->render("monitoring", [
            'nbArticles' => $nbArticles,
            'nbComments' => $nbComments,
            'totalViews' => $totalViews,
            'mostViewedArticle' => $mostViewedArticle,

            // Envoi des articles triés à la vue.
            'articles' => $articles,

            // Envoi des paramètres de tri pour afficher l'état actuel.
            'sort' => $sort,
            'order' => $order
        ]);
    }

    /**
     * Affiche la page de gestion des commentaires.
     * @return void
     */
    public function showComments(): void
    {

        // Vérifie que seul un utilisateur connecté peut accéder à cette page.
        $this->checkIfUserIsConnected();

        // Récupération de tous les commentaires depuis la base de données.
        $commentManager = new CommentManager();

        $comments = $commentManager->getAllComments();

        // Création de la vue de gestion des commentaires.
        $view = new View("Gestion des commentaires");

        // Transmission des commentaires à la vue.
        $view->render("comments", [
            'comments' => $comments
        ]);
    }

    /**
     * Supprime un commentaire.
     * @return void
     */
    public function deleteComment(): void
    {
        // Vérifie que seul un utilisateur connecté peut supprimer un commentaire.
        $this->checkIfUserIsConnected();

        // Récupération de l'identifiant du commentaire.
        $id = Utils::request("id", -1);

        // Recherche du commentaire correspondant.
        $commentManager = new CommentManager();

        $comment = $commentManager->getCommentById($id);


        if (!$comment) {
            throw new Exception("Le commentaire demandé n'existe pas.");
        }

        // Suppression du commentaire trouvé.
        $commentManager->deleteComment($comment);

        // Retour vers la liste des commentaires après suppression.
        Utils::redirect("comments");
    }


    /**
     * Vérifie que l'utilisateur est connecté.
     * @return void
     */
    private function checkIfUserIsConnected(): void
    {
        // On vérifie que l'utilisateur est connecté.
        if (!isset($_SESSION['user'])) {
            Utils::redirect("connectionForm");
        }
    }

    /**
     * Affichage du formulaire de connexion.
     * @return void
     */
    public function displayConnectionForm(): void
    {
        $view = new View("Connexion");
        $view->render("connectionForm");
    }

    /**
     * Connexion de l'utilisateur.
     * @return void
     */
    public function connectUser(): void
    {
        // On récupère les données du formulaire.
        $login = Utils::request("login");
        $password = Utils::request("password");

        // On vérifie que les données sont valides.
        if (empty($login) || empty($password)) {
            throw new Exception("Tous les champs sont obligatoires. 1");
        }

        // On vérifie que l'utilisateur existe.
        $userManager = new UserManager();
        $user = $userManager->getUserByLogin($login);
        if (!$user) {
            throw new Exception("L'utilisateur demandé n'existe pas.");
        }

        // On vérifie que le mot de passe est correct.
        if (!password_verify($password, $user->getPassword())) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            throw new Exception("Le mot de passe est incorrect : $hash");
        }

        // On connecte l'utilisateur.
        $_SESSION['user'] = $user;
        $_SESSION['idUser'] = $user->getId();

        // On redirige vers la page d'administration.
        Utils::redirect("admin");
    }

    /**
     * Déconnexion de l'utilisateur.
     * @return void
     */
    public function disconnectUser(): void
    {
        // On déconnecte l'utilisateur.
        unset($_SESSION['user']);

        // On redirige vers la page d'accueil.
        Utils::redirect("home");
    }

    /**
     * Affichage du formulaire d'ajout d'un article.
     * @return void
     */
    public function showUpdateArticleForm(): void
    {
        $this->checkIfUserIsConnected();

        // On récupère l'id de l'article s'il existe.
        $id = Utils::request("id", -1);

        // On récupère l'article associé.
        $articleManager = new ArticleManager();
        $article = $articleManager->getArticleById($id);

        // Si l'article n'existe pas, on en crée un vide. 
        if (!$article) {
            $article = new Article();
        }

        // On affiche la page de modification de l'article.
        $view = new View("Edition d'un article");
        $view->render("updateArticleForm", [
            'article' => $article
        ]);
    }

    /**
     * Ajout et modification d'un article. 
     * On sait si un article est ajouté car l'id vaut -1.
     * @return void
     */
    public function updateArticle(): void
    {
        $this->checkIfUserIsConnected();

        // On récupère les données du formulaire.
        $id = Utils::request("id", -1);
        $title = Utils::request("title");
        $content = Utils::request("content");

        // On vérifie que les données sont valides.
        if (empty($title) || empty($content)) {
            throw new Exception("Tous les champs sont obligatoires. 2");
        }

        // On crée l'objet Article.
        $article = new Article([
            'id' => $id, // Si l'id vaut -1, l'article sera ajouté. Sinon, il sera modifié.
            'title' => $title,
            'content' => $content,
            'id_user' => $_SESSION['idUser']
        ]);

        // On ajoute l'article.
        $articleManager = new ArticleManager();
        $articleManager->addOrUpdateArticle($article);

        // On redirige vers la page d'administration.
        Utils::redirect("admin");
    }


    /**
     * Suppression d'un article.
     * @return void
     */
    public function deleteArticle(): void
    {
        $this->checkIfUserIsConnected();

        $id = Utils::request("id", -1);

        // On supprime l'article.
        $articleManager = new ArticleManager();
        $articleManager->deleteArticle($id);

        // On redirige vers la page d'administration.
        Utils::redirect("admin");
    }
}