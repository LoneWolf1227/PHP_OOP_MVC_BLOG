<?php


namespace MVC\Controllers;

use Core\UsersAuth;
use MVC\Models\Articles\Article;


class MainController extends AbstractController
{
    public function main()
    {
        $articles = Article::findAll();
        $this->view->renderHTML('main/main.php',
            [
                'articles' => $articles,
                'user' => UsersAuth::getUserByToken()
            ]);
    }

}