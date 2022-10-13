<?php

namespace App\Controllers\Users;

use App\Classes\User\User;
use App\Classes\User\UserRepository;

class UserController
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUserDetails(int $id): User
    {
        return $this->userRepository->fetchUserInfo($id);
    }

    public function addBalance(int $id, float $amount): void
    {
        $this->userRepository->addBalance($id, $amount);
    }
}