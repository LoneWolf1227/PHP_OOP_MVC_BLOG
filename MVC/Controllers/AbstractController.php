<?php


namespace MVC\Controllers;


use Core\UsersAuth;
use MVC\Views\Views;
use MVC\Models\Users\User;

abstract class AbstractController
{
    /** @var Views  */
    protected $view;

    /** @var User|NULL */
    protected $user;

    public function __construct()
    {
        $this->user = UsersAuth::getUserByToken();
        $this->view = new Views(__DIR__ . '/../Views/Temp');
        $this->view->setVars('user', $this->user);
    }

}