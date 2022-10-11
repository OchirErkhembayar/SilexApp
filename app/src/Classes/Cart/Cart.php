<?php
declare(strict_types=1);

namespace App\Classes\Cart;

class Cart
{
    public function __construct(public readonly int $cart_id = 0) {}

    public static function oneFromDatabaseFields($fields): Cart
    {
        return new Cart($fields["cart_id"]);
    }
}