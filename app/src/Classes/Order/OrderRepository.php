<?php
declare(strict_types=1);

namespace App\Classes\Order;

use App\Classes\Database\DatabaseConnection;
use PDO;

class OrderRepository
{
    public PDO $conn;

    public function __construct(DatabaseConnection $conn)
    {
        $this->conn = $conn->conn;
    }

    /**
     * @return array<Order>
     * */
    public function getOrders(): array
    {
        $sql = "SELECT * FROM orders
                JOIN order_items ON orders.order_id=order_items.order_id
                JOIN cars ON cars.car_id=order_items.car_id";
        $result = $statement = $this->conn->query($sql);
        if (gettype($result) === "boolean") {
            throw new \PDOException("Failed to fetch orders.");
        }
        $fieldsArray = $result->fetchAll(PDO::FETCH_GROUP);
        return Order::manyFromDatabaseFields($fieldsArray);
    }

    /**
     * @return array{order:Order,order_items:array<OrderItem>}
     * */
    public function getOrder(int $id): array
    {
        $sql = "SELECT * FROM orders
                INNER JOIN order_items ON order_items.order_id=orders.order_id
                INNER JOIN cars ON cars.car_id=order_items.car_id
                INNER JOIN engines ON cars.car_id=engines.car_id
                WHERE orders.order_id = :id";
        $statement = $this->conn->prepare($sql);
        $statement->execute([
            ':id' => $id
        ]);
        $fieldsArray = $statement->fetchAll();
        $order = Order::oneFromDatabaseFields($fieldsArray);
        $order_items = OrderItem::manyFromDatabaseFields($fieldsArray);
        return [
            "order" => $order,
            "order_items" => $order_items
        ];
    }

    public function createOrder(): void
    {
        $orderSql = "INSERT INTO orders () VALUES ()";
        $statement = $this->conn->prepare($orderSql);
        $statement->execute([]);
        $order_id = $this->conn->lastInsertId();
        $cartItemsSql = "SELECT * FROM cart_items";
        $statement = $this->conn->prepare($cartItemsSql);
        $statement->execute([]);
        $fieldsArray = $statement->fetchAll();
        foreach ($fieldsArray as $fields) {
            $sql = "INSERT INTO order_items (order_id, car_id, quantity) VALUES (:order_id, :car_id, :quantity)";
            $statement = $this->conn->prepare($sql);
            $statement->execute([
                ':order_id' => $order_id,
                ':car_id' => $fields["car_id"],
                ':quantity' => $fields["quantity"]
            ]);
        }
        $deleteCartItemsSql = "DELETE FROM cart_items";
        $statement = $this->conn->prepare($deleteCartItemsSql);
        $statement->execute([]);
    }
}