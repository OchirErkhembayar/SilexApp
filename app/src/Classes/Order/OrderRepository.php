<?php
declare(strict_types=1);

namespace App\Classes\Order;

use _PHPStan_acbb55bae\Nette\Neon\Exception;
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
    public function getOrders(int $user_id): array
    {
        $this->conn->beginTransaction();
        try {
            $sql = "SELECT * FROM orders
                    JOIN order_items ON orders.order_id=order_items.order_id
                    JOIN cars ON cars.car_id=order_items.car_id WHERE orders.user_id=:user_id";
            $statement = $this->conn->prepare($sql);
            $statement->execute([
                ':user_id' => $user_id
            ]);
            $fieldsArray = $statement->fetchAll(PDO::FETCH_GROUP);
            $this->conn->commit();
            return Order::manyFromDatabaseFields($fieldsArray);
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw new \PDOException("Database query failed.");
        }
    }

    /**
     * @return array{order:Order,order_items:array<OrderItem>}
     * */
    public function getOrder(int $id): array
    {
        $this->conn->beginTransaction();
        try {
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
            $this->conn->commit();
            return [
                "order" => $order,
                "order_items" => $order_items
            ];
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw new \PDOException("Database query failed.");
        }
    }

    public function createOrder(int $user_id): bool
    {
        $this->conn->beginTransaction();
        try {
            $orderSql = "INSERT INTO orders (user_id) VALUES (:id)";
            $statement = $this->conn->prepare($orderSql);
            $statement->execute([
                ':id' => $user_id
            ]);
            $order_id = $this->conn->lastInsertId();
            $sql = "SELECT cart_id FROM carts WHERE user_id=:id";
            $statement = $this->conn->prepare($sql);
            $statement->execute([
                ':id' => $user_id
            ]);
            $fields = $statement->fetchAll()[0];
            $cart_id = $fields["cart_id"];
            $cartItemsSql = "SELECT * FROM cart_items WHERE cart_id=:cart_id";
            $statement = $this->conn->prepare($cartItemsSql);
            $statement->execute([
                ':cart_id' => $cart_id
            ]);
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
            $deleteCartItemsSql = "DELETE FROM cart_items WHERE cart_id=:id";
            $statement = $this->conn->prepare($deleteCartItemsSql);
            $statement->execute([
                ':id' => $cart_id
            ]);
            $sql = "SELECT * FROM order_items INNER JOIN cars ON order_items.car_id = cars.car_id WHERE order_id=:id";
            $statement = $this->conn->prepare($sql);
            $statement->execute([
                ':id' => $order_id
            ]);
            $fields = $statement->fetchAll();
            $price = array_reduce($fields, function ($sum, $fields) {
               return $sum + ($fields["quantity"] * $fields["price"]);
            }, 0);
            $sql = "SELECT balance FROM users WHERE user_id=:id";
            $statement = $this->conn->prepare($sql);
            $statement->execute([
                ':id' => $user_id
            ]);
            $currentBalance = $statement->fetchAll()[0]["balance"];
            if ($currentBalance < $price) {
                throw new Exception("You're poor!");
            }
            $sql = "UPDATE users SET balance=balance-:price WHERE user_id=:id";
            $statement = $this->conn->prepare($sql);
            $statement->execute([
               ':price' => $price,
               ':id' => $user_id
            ]);
            return $this->conn->commit();
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw new \PDOException($e->getMessage());
        }
    }
}