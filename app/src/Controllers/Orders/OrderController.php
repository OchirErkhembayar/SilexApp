<?php
declare(strict_types=1);

namespace App\Controllers\Orders;

use App\Classes\Order\OrderRepository;

class OrderController
{
    public function save(): void
    {
        $orderRepository = new OrderRepository();
        $orderRepository->createOrder();
    }

    public function getOrders(): array
    {
        $orderRepository = new OrderRepository();
        return $orderRepository->getOrders();
    }

    public function getOrder($id): array
    {
        $orderRepository = new OrderRepository();
        return $orderRepository->getOrder($id);
    }
}