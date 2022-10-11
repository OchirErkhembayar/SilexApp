<?php
declare(strict_types=1);

namespace App\Classes\Order;

class Order
{
    public function __construct(public readonly ?int $order_id = null, public readonly ?float $total = null, public
    readonly ?int $quantity = null) {}

    public static function oneFromDatabaseFields($fieldsArray): Order
    {
        $id = $fieldsArray[0]["order_id"];
        $total = array_reduce($fieldsArray, function($sum, $orderItem) {
           return $sum += $orderItem["price"];
        }, 0);
        $quantity = count($fieldsArray);
        return new Order($id, $total, $quantity);
    }

    public static function manyFromDatabaseFields($fieldsArray): array
    {
        $orders = [];
        foreach ($fieldsArray as $order_id => $fields) {
            $price = \array_reduce($fields, function($sum, $orderItem) {
                return $sum += $orderItem["price"] * $orderItem["quantity"];
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