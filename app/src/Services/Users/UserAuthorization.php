<?php

namespace App\Services\Users;

use App\Classes\User\UserRepository;

class UserAuthorization
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @return array<string,bool|string>
     */
    public function checkIfUserExists(string $username, string $email): array
    {
        $errors = [
            "username" => false,
            "email" => false,
            "taken" => false
        ];
        $user = $this->userRepository->findByEmail($email);
        if ($user) {
            $errors["email"] = "Email is taken";
            $errors["taken"] = true;
        }
        $user = $this->userRepository->findByUsername($username);
        if ($user) {
            $errors["username"] = "Username is taken";
            $errors["taken"] = true;
        }
        return $errors;
    }
}