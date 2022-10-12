<?php
declare(strict_types=1);

namespace App\Controllers\Cart;

use App\Classes\Cart\Cart;
use App\Classes\Cart\CartItem;
use App\Classes\Cart\CartRepository;

class CartController
{
    private CartRepository $cartRepository;

    public function __construct(CartRepository $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

    public function getCart(): Cart
    {
        return $this->cartRepository->getCart();
    }

    /**
     * @return array<CartItem>
     * */
    public function getCartItems(int $cart_id): array
    {
        return $this->cartRepository->getCartItems($cart_id);
    }

    public function addToCart(int $car_id, int $cart_id): void
    {
        $this->cartRepository->addToCart($car_id, $cart_id);
    }

    public function removeFromCart(int $cart_item_id): void
    {
        $this->cartRepository->removeFromCart($cart_item_id);
    }
}