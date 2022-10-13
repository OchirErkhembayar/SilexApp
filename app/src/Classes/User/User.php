<?php

namespace App\Classes\User;

class User
{
    public function __construct (public readonly string $email, public readonly string $username, public readonly
    float $balance, public readonly ?int $user_id = null, private ?string $password = null) {}

    public static function oneFromDatabaseFields($fields): User
    {
        return new User($fields["email"], $fields["username"], $fields["balance"], $fields["user_id"]);
    }
}