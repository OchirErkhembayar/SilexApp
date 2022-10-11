<?php
declare(strict_types=1);

namespace App\Controllers\Cart;

use App\Classes\Cart\Cart;
use App\Classes\Cart\CartItem;
use App\Classes\Cart\CartRepository;

class CartController
{
    public function getCart(): Cart
    {
        $cartRepository = new CartRepository();
        return $cartRepository->getCart();
    }

    /**
     * @return array<CartItem>
     * */
    public function getCartItems(int $cart_id): array
    {
        $cartRepository = new CartRepository();
        return $cartRepository->getCartItems($cart_id);
    }

    public function addToCart(int $car_id, int $cart_id): void
    {
        $cartRepository = new CartRepository();
        $cartRepository->addToCart($car_id, $cart_id);
    }

    public function removeFromCart(int $cart_item_id): void
    {
        $cartRepository = new CartRepository();
        $cartRepository->removeFromCart($cart_item_id);
    }
}