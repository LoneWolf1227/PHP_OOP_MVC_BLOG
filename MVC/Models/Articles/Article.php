<?php

namespace MVC\Models\Articles;


use Exception\InvalidArgumentException;
use MVC\Models\ActiveRecordEntity;
use MVC\Models\Users\User;


class Article extends ActiveRecordEntity
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $text;

    /** @var int */
    protected $authorId;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return User::getById($this->authorId);
    }

    /**
     * @return int
     */
    public function getAuthorId(): int
    {
        return $this->authorId;
    }

    protected static function getTableName(): string
    {
        return 'articles';
    }

    /**
     * @param string $text
     */
    public function setText(string $text)
    {
        $this->text = $text;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @param User $author
     */
    public function setAuthor(User $author)
    {
        $this->authorId = $author->getId();
    }

    public static function createFromArray(array $fields, User $author): Article
    {
        if (empty($fields['name']))
        {
            throw new InvalidArgumentException('Не передано названия статьи');
        }
        if (empty($fields['text']))
        {
            throw new InvalidArgumentException('Не передан текст статьи');
        }

        $article = new Article();

        $article->setAuthor($author);
        $article->setName(htmlspecialchars($fields['name']));
        $article->setText($fields['text']);

        $article->save();

        return $article;
    }

    public function updateFromArray(array $fields): Article
    {
        if (empty($fields['name']))
        {
            throw new InvalidArgumentException('Не передано названия статьи');
        }
        if (empty($fields['text']))
        {
            throw new InvalidArgumentException('Не передан текст статьи');
        }

        $this->setName(htmlspecialchars($fields['name']));
        $this->setText($fields['text']);

        $this->save();

        return $this;
    }
}