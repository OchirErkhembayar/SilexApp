<?php
declare(strict_types=1);

namespace App\Classes\Cart;

use App\Classes\Car\Car;

class CartItem
{
    public function __construct(public readonly int $cart_id,
                                public readonly Car $car,
                                public readonly int $quantity,
                                public readonly float $price,
                                public readonly int $cart_item_id = 0)
    {
    }

    public static function oneFromDatabaseFields($fields): CartItem
    {
        $car = Car::oneFromDatabaseFields($fields);
        $price = $fields["quantity"] * $car->price;
        return new CartItem($fields["cart_id"], $car, $fields["quantity"], $price,
            $fields["cart_item_id"]);
    }

    public static function manyFromDatabaseFields($fields): array
    {
        return \array_map('self::oneFromDatabaseFields', $fields);
    }
}