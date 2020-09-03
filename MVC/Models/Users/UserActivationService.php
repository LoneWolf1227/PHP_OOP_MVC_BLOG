<?php


namespace MVC\Models\Users;


use Core\Db;


class UserActivationService
{
    const TABLE_NAME = 'usersActivationCodes';

    public static function createActivationCode(User $user): string
    {
        $string = "1abc2def3ghi4jklm5nop6qrs7tuv8wqy9zASDFGHJKLZXCVBNMQWERTYUIOP";
        $code = substr(str_shuffle($string), 0, 64);

        $db = Db::getInstance();
        $db->query(
            'INSERT INTO ' . self::TABLE_NAME . ' (userId, code) VALUES (:userId, :code)',
            [
                'userId' => $user->getId(),
                'code' => $code
            ]
        );

        return $code;
    }

    public static function checkActivationCode(User $user, string $code): bool
    {
        $db = Db::getInstance();

        $result = $db->query(
            'SELECT * FROM ' . self::TABLE_NAME . ' WHERE userId = :userId AND code = :code',
            [
                'userId' => $user->getId(),
                'code' => $code
            ]
        );

        return !empty($result);
    }

}