<?php
declare(strict_types=1);

namespace App\Classes\Order;

use PDO;

class OrderRepository
{
    public PDO $conn;

    public function __construct()
    {
        $host = "mysql";
        $db_name = "silexCars";
        $username = "root";
        $password = "db_pass";
        $this->conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getOrders(): array
    {
        $sql = "SELECT * FROM orders
                JOIN order_items ON orders.order_id=order_items.order_id
                JOIN cars ON cars.car_id=order_items.car_id";
        $statement = $this->conn->query($sql);
        $fieldsArray = $statement->fetchAll(PDO::FETCH_GROUP);
        return Order::manyFromDatabaseFields($fieldsArray);
    }

    public function getOrder($id): array
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