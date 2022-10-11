<?php
declare(strict_types=1);

namespace App\Controllers\Orders;

use App\Classes\Order\Order;
use App\Classes\Order\OrderItem;
use App\Classes\Order\OrderRepository;

class OrderController
{
    public function save(): void
    {
        $orderRepository = new OrderRepository();
        $orderRepository->createOrder();
    }

    /**
     * @return array<Order>
     * */
    public function getOrders(): array
    {
        $orderRepository = new OrderRepository();
        return $orderRepository->getOrders();
    }

    /**
     * @return array{order:Order,order_items:array<OrderItem>}
     * */
    public function getOrder(int $id): array
    {
        $orderRepository = new OrderRepository();
        return $orderRepository->getOrder($id);
    }
}