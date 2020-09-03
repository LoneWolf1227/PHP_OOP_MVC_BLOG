<?php

namespace MVC\Models\Users;

use \Exception\InvalidArgumentException;
use MVC\Models\ActiveRecordEntity;

class User extends ActiveRecordEntity
{
    /** @var string */
    protected $nickname;

    /** @var string */
    protected $email;

    /** @var int */
    protected $isConfirmed;

    /** @var string */
    protected $role;

    /** @var string */
    protected $passwordHash;

    /** @var string */
    protected $authToken;

    /** @var string */
    protected $createdAt;

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getNickname(): string
    {
        return $this->nickname;
    }

    /** @return string */
    protected static function getTableName(): string
    {
        return 'users';
    }

    public static function signUp(array $userData)
    {
        if (empty($userData['nickname']))
        {
            throw new InvalidArgumentException('Не передан nickname');
        }
        if (!preg_match('/^[a-zA-Z0-9]+$/', $userData['nickname']))
        {
            throw new InvalidArgumentException('Nickname может состоять только из символов латинского алфавита 
            и цифр');
        }

        if (empty($userData['email']))
        {
            throw new InvalidArgumentException('Не передан email');
        }
        if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL))
        {
            throw new InvalidArgumentException('Email не корректен');
        }

        if (empty($userData['password']))
        {
            throw new InvalidArgumentException('Не передан password');
        }
        if (mb_strlen($userData['password']) < 8)
        {
            throw new InvalidArgumentException('Пароль должен быть не менее 8 символов');
        }

        if (static::findOneByColumn('nickname', htmlspecialchars($userData['nickname'])) !== null)
        {
            throw new InvalidArgumentException('Nickname уже используется');
        }
        if (static::findOneByColumn('email', htmlspecialchars($userData['email'])) !== null)
        {
            throw new InvalidArgumentException('Email уже используется');
        }

        $user = new self();
        $user->nickname = htmlspecialchars($userData['nickname']);
        $user->email = htmlspecialchars($userData['email']);
        $user->passwordHash = password_hash($userData['password'], PASSWORD_DEFAULT);
        $user->isConfirmed = false;
        $user->role = 'user';
        $user->authToken = sha1(random_bytes(100)).hash('sha256', $userData['nickname'].$userData['email'])
                            .sha1(random_bytes(200));
        $user->save();

        return $user;
    }

    public function activate()
    {
        $this->isConfirmed = true;
        $this->save();
    }

    public static function login(array $loginData): User
    {
        if (empty($loginData['email']))
        {
            throw new InvalidArgumentException('Заполните поле для email');
        }
        if (empty($loginData['password']))
        {
            throw new InvalidArgumentException('Заполните поле для пароля');
        }

        $user = User::findOneByColumn('email', htmlspecialchars($loginData['email']));
        if ($user === null)
        {
            throw new InvalidArgumentException('Нет пользователя с таким email');
        }
        if (!password_verify($loginData['password'], $user->getPasswordHash()))
        {
            throw new InvalidArgumentException('Неправильный пароль');
        }
        if (!$user->isConfirmed)
        {
            throw new InvalidArgumentException('Ваш Email не авторизован');
        }

        $user->refreshAuthToken($loginData);
        $user->save();

        return $user;
    }

    /**
     * @return string
     */
    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    /**
     * @param array $userData
     * @return string
     * @throws \Exception
     */
    public function refreshAuthToken(array $userData): string
    {
        return $this->authToken = sha1(random_bytes(100)).hash('sha256', $userData['email'].$userData['password'])
            .sha1(random_bytes(200));
    }

    /**
     * @return string
     */
    public function getAuthToken(): string
    {
        return $this->authToken;
    }
}