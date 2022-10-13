<?php

namespace App\Classes\User;

class User
{
    public function __construct (public readonly string $email, public readonly string $username, public readonly
    float $balance, public readonly ?int $user_id = null) {}

    /**
     * @param array<string,string|int|float> $fields
     * @return User
     */
    public static function oneFromDatabaseFields(array $fields): User
    {
        return new User((string)$fields["email"], (string)$fields["username"], (float)$fields["balance"],
            (int)$fields["user_id"]);
    }
}