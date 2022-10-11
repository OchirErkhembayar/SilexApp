<?php
declare(strict_types=1);

namespace App\Controllers\Cart;

use App\Classes\Cart\Cart;
use App\Classes\Cart\CartRepository;

class CartController
{
    public function getCart(): Cart
    {
        $cartRepository = new CartRepository();
        return $cartRepository->getCart();
    }

    public function getCartItems($cart_id): array
    {
        $cartRepository = new CartRepository();
        return $cartRepository->getCartItems($cart_id);
    }

    public function addToCart($car_id, $cart_id): void
    {
        $cartRepository = new CartRepository();
        $cartRepository->addToCart($car_id, $cart_id);
    }

    public function removeFromCart($cart_item_id): void
    {
        $cartRepository = new CartRepository();
        $cartRepository->removeFromCart($cart_item_id);
    }
}