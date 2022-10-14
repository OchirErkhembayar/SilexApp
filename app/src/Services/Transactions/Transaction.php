<?php

namespace Services\Transactions;

use App\Classes\Car\CarRepository;
use App\Classes\Cart\CartRepository;
use App\Classes\Order\OrderRepository;

class Transaction
{
    private CartRepository $cartRepository;
    private OrderRepository $orderRepository;
    private CarRepository $carRepository;

    public function __construct(CartRepository   $cartRepository,
                                OrderRepository $orderRepository, CarRepository $carRepository)
    {
            $this->cartRepository = $cartRepository;
            $this->carRepository = $carRepository;
            $this->orderRepository = $orderRepository;
    }

    // Create a new order CHECK save()
    // Get the cart CHECK getCart()
    // Get the cart items CHECK getCartItems
    // Create order items for each cart item create objects then loop through saveOrderItem()
    // Delete cart items Loop through delete cart items;
    // Check user balance CHECKBALANCE
    // Deduct from buyer
    // Pay the seller
}