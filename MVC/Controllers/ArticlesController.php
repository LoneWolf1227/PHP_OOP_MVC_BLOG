<?php


namespace MVC\Controllers;


use Exception\InvalidArgumentException;
use Exception\NotFoundException;
use Exception\UnauthorizedException;
use MVC\Models\Articles\Article;
use MVC\Models\Users\User;

class ArticlesController extends AbstractController
{

    public function view(int $articleId)
    {
        $article = Article::getById($articleId);

        if ($article === [] || $article === null)
        {
            throw new \Exception\NotFoundException('');
        }
        if ($_GET['delete'] === 'yes')
        {
            $article->delete();
            echo 'Post deleted';exit;
        }
        $this->view->renderHTML('articles/view.php', ['article' => $article]);
    }

    public function edit(int $articleId)
    {
        $article = Article::getById($articleId);

        if ($article === null)
        {
            throw new NotFoundException();
        }
        if ($this->user === null)
        {
            throw new UnauthorizedException();
        }

        if (!empty($_POST))
        {
            try {
                $article->updateFromArray($_POST);
            }
            catch (InvalidArgumentException $e)
            {
                $this->view->renderHTML('articles/edit.php', ['error' => $e->getMessage(), 'article' => $article]);
                return;
            }
            header('Location: /articles/' . $article->getId(), true, 302);
            exit();
        }

        $this->view->renderHtml('articles/edit.php', ['article' => $article]);
    }

    public function add()
    {
        if ($this->user === null)
        {
            throw new UnauthorizedException();
        }

        if (!empty($_POST))
        {
            try {
                $article = Article::createFromArray($_POST, $this->user);
            }
            catch (InvalidArgumentException $e)
            {
                $this->view->renderHTML('articles/add.php', ['error' => $e->getMessage()]);
            }
            header('Location: /articles/' . $article->getId(), true, 302);
            exit();
        }

        $this->view->renderHTML('articles/add.php');
    }

}