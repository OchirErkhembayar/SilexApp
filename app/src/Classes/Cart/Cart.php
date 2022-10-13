<?php
declare(strict_types=1);

namespace App\Classes\Cart;

class Cart
{
    public function __construct(public readonly int $cart_id = 0, public readonly int $user_id = 1) {}

    /**
     * @param array<string,int> $fields
     * */
    public static function oneFromDatabaseFields(array $fields): Cart
    {
        return new Cart($fields["cart_id"]);
    }
}