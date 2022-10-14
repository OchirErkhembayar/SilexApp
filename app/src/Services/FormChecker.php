<?php
declare(strict_types=1);

namespace App\Services;

class FormChecker
{
    /**
     * @param array<string,string|int|float> $params
     * @return bool
     */
    public static function checkAddCarInputs(array $params): bool
    {
        foreach ($params as $value)
        {
            if (empty(trim(strval($value)))) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return array<string,string>
     */
    public static function checkSignupCredentials(string $username, string $email, string $password): array
    {
        $errors = [
            "emailError" => null,
            "usernameError" => null,
            "passwordError" => null,
            "hasErrors" => false
        ];
        if (strlen(trim($username)) === 0) {
            $errors["usernameError"] = "Username cannot be empty";
            $errors["hasErrors"] = true;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors["emailError"] = "Invalid email";
            $errors["hasErrors"] = true;
        }
        if (strlen(trim($password)) < 6) {
            $errors["passwordError"] = "Password must be at least 6 characters long";
            $errors["hasErrors"] = true;
        }
        return $errors;
    }
}