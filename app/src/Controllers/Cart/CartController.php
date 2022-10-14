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

    public function findByUserId(int $user_id): Cart
    {
        return $this->cartRepository->findByUserId($user_id);
    }

    public function getCartQuantity(int $user_id): int
    {
        $cart = $this->getCartQuantity($user_id);
        return $this->cartRepository->getCartQuantity($cart);
    }

    /**
     * @return array<CartItem>
     * */
    public function getCartItems(int $cart_id): array
    {
        return $this->cartRepository->getCartItems($cart_id);
    }

    public function addToCart(int $car_id, int $cart_id): bool
    {
        return $this->cartRepository->addToCart($car_id, $cart_id);
    }

    public function editCartQuantity(int $cart_item_id, int $quantity): bool
    {
        if ($quantity < 1) {
            return $this->cartRepository->removeFromCart($cart_item_id);
        }
        return $this->cartRepository->editCartQuantity($cart_item_id, $quantity);
    }

    public function removeFromCart(int $cart_item_id): bool
    {
        return $this->cartRepository->removeFromCart($cart_item_id);
    }
}