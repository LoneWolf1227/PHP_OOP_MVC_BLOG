<?php


namespace MVC\Controllers;


use Core\EmailSender;
use Core\UsersAuth;
use Exception\InvalidArgumentException;
use MVC\Models\Users\User;
use MVC\Models\Users\UserActivationService;

class UsersController extends AbstractController
{

    public function signUp()
    {
        if (!empty($_POST))
        {
            try {
                $user = User::signUp($_POST);
            }
            catch (\Exception\InvalidArgumentException $e)
            {
                $this->view->renderHtml('users/signUp.php', ['error' => $e->getMessage()]);
                return;
            }
        }

        if ($user instanceof User)
        {
            $code = UserActivationService::createActivationCode($user);

            EmailSender::send($user, 'Активация аккаунта', 'userActivation.php' ,
                array(
                    'userId' => $user->getId(),
                    'code' => $code
                )
            );

            $this->view->renderHTML('users/success.php');
            return;
        }
        $this->view->renderHTML('users/signUp.php');
    }

    public function activate(int $userId, string $activationCode)
    {
        $user = User::getById($userId);
        $isCodeValid = UserActivationService::checkActivationCode($user, $activationCode);
        if ($isCodeValid)
        {
            $user->activate();
            $this->view->renderHTML('users/activated.php');
        }
        else
        {
            echo 'OOPS Problem with activation';
        }
    }

    public function login()
    {
        if (!empty($_POST))
        {
            try {
                $user = User::login($_POST);
                UsersAuth::createToken($user);
                header('Location: /');
                exit();
            } catch (InvalidArgumentException $e)
            {
                $this->view->renderHTML('users/login.php', ['error' => $e->getMessage()]);
                return;
            }
        }
        $this->view->renderHTML('users/login.php');
    }

    public function logout()
    {
        UsersAuth::deleteToken();
        header('Location: /');
        exit();
    }
}
