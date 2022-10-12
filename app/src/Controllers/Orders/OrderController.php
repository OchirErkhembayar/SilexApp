<?php
declare(strict_types=1);

namespace App\Controllers\Orders;

use App\Classes\Order\Order;
use App\Classes\Order\OrderItem;
use App\Classes\Order\OrderRepository;

class OrderController
{
    private OrderRepository $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function save(): bool
    {
        return $this->orderRepository->createOrder();
    }

    /**
     * @return array<Order>
     * */
    public function getOrders(): array
    {
        return $this->orderRepository->getOrders();
    }

    /**
     * @return array{order:Order,order_items:array<OrderItem>}
     *
     */
    public function getOrder(int $id): array
    {
        return $this->orderRepository->getOrder($id);
    }
}