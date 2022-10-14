<?php

namespace Services\Transactions;

use App\Classes\User\User;
use App\Classes\User\UserRepository;

class BalanceChecker
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function checkBalance(User $user, float $amount): bool
    {
        $user = $this->userRepository->findById($user->user_id);
        return (float)$user->balance >= $amount;
    }
}