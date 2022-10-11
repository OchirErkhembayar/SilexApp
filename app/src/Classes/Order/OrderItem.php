<?php
declare(strict_types=1);

namespace App\Classes\Order;

use App\Classes\Car\Car;

class OrderItem
{
    public function __construct(public readonly int $order_id,
                                public readonly Car $car,
                                public readonly int $quantity,
                                public readonly float $price,
                                public readonly ?int $order_item_id = null) {}

    public static function oneFromDatabaseFields($fields): OrderItem
    {
        $car = Car::oneFromDatabaseFields($fields);
        $price = $fields["quantity"] * $car->price;
        return new OrderItem($fields["order_id"], $car, $fields["quantity"], $price,
            $fields["order_item_id"]);
    }

    public static function manyFromDatabaseFields($fields): array
    {
        return \array_map('self::oneFromDatabaseFields', $fields);
    }
}