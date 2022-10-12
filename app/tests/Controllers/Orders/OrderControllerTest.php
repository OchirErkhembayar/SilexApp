<?php
declare(strict_types=1);

namespace Test\Controllers\Orders;

use App\Classes\Database\DatabaseConnection;
use App\Classes\Order\OrderRepository;
use App\Controllers\Orders\OrderController;
use PHPUnit\Framework\TestCase;

class OrderControllerTest extends TestCase
{
    private OrderController $orderController;

    public function setUp(): void
    {
        parent::setUp();
        $dbc = new DatabaseConnection("silexCarsTest");
        $orderRepository = new OrderRepository($dbc);
        $this->orderController = new OrderController($orderRepository);
    }

    /**
     * @test
     */
    public function it_can_get_orders(): void
    {
        $orders = $this->orderController->getOrders();
        $this->assertIsArray($orders);
    }

    /**
     * @test
     */
    public function it_can_create_order(): void
    {
        $result = $this->orderController->save();
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function it_can_get_order_by_id(): void
    {
        $order = $this->orderController->getOrders()[0];
        \assert($order->order_id !== null);
        $order = $this->orderController->getOrder($order->order_id);
        $this->assertIsArray($order);
    }
}