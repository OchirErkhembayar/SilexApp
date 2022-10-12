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

    /**
     * @param array<string,int|float|string> $fields
     * */
    public static function oneFromDatabaseFields(array $fields): OrderItem
    {
        $car = Car::oneFromDatabaseFields($fields);
        $price = (int)$fields["quantity"] * $car->price;
        return new OrderItem((int)$fields["order_id"], $car, (int)$fields["quantity"], $price,
            (int)$fields["order_item_id"]);
    }

    /**
     * @return array<OrderItem>
     * @param array<array<string,int>> $fields
     * */
    public static function manyFromDatabaseFields(array $fields): array
    {
        return \array_map([OrderItem::class, 'oneFromDatabaseFields'], $fields);
    }
}