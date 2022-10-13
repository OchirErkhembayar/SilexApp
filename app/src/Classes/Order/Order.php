<?php
declare(strict_types=1);

namespace App\Classes\Order;

class Order
{
    public function __construct(public readonly ?int $order_id = null, public readonly ?float $total = null, public
    readonly ?int $quantity = null, public readonly int $user_id = 1) {}

    /**
     * @param array<string|int,array<string,int|float>> $fieldsArray
     * */
    public static function oneFromDatabaseFields(array $fieldsArray): Order
    {
        $id = $fieldsArray[0]["order_id"];
        $total = array_reduce($fieldsArray, function($sum, $orderItem) {
           return $sum += $orderItem["price"];
        }, 0);
        $quantity = count($fieldsArray);
        return new Order((int)$id, $total, $quantity);
    }

    /**
     * @return array<Order>
     * @param array<array<string,array<string,int|float|string>>> $fieldsArray
     * */
    public static function manyFromDatabaseFields(array $fieldsArray): array
    {
        $orders = [];
        foreach ($fieldsArray as $order_id => $fields) {
            $price = \array_reduce($fields, function($sum, $orderItem) {
                return $sum += (float)$orderItem["price"] * (int)$orderItem["quantity"];
            }, 0);
            $quantity = \array_reduce($fields, function($sum, $order_item) {
                return $sum += $order_item["quantity"];
            }, 0);
            $order = new Order($order_id, $price, $quantity);
            $orders[] = $order;
        }
        return $orders;
    }
}