<?php

namespace Services\Transactions;

use App\Classes\Cart\CartRepository;

class OrderCostCalculator
{
    private CartRepository $cartRepository;

    public function __construct(CartRepository $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }


}