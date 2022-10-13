<?php

namespace App\Services;

use App\Classes\User\User;
use App\Classes\User\UserRepository;

class Authorization
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login(string $username, string $password): User|bool
    {
        return $this->userRepository->getAuthenticateUser($username, $password);
    }

    public function signup(string $username, string $password, string $email): User|bool
    {
        return $this->userRepository->createUser($username, $email, $password);
    }
}