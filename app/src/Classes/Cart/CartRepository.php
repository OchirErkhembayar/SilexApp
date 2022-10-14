<?php
declare(strict_types=1);

namespace App\Classes\Cart;

use App\Classes\Database\DatabaseConnection;
use Exception;
use PDO;
use PDOException;

class CartRepository
{
    public PDO $conn;

    public function __construct(DatabaseConnection $conn)
    {
        $this->conn = $conn->conn;
    }

    public function getCart(int $user_id): Cart
    {
        $this->conn->beginTransaction();
        try {
            $sql = "SELECT * FROM carts WHERE user_id=:user_id";
            $statement = $this->conn->prepare($sql);
            $statement->execute([
                ':user_id' => $user_id
            ]);
            $fields = $statement->fetchAll()[0];
            $this->conn->commit();
            return Cart::oneFromDatabaseFields($fields);
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw new PDOException("Failed to get cart");
        }
    }

    /**
     * @return array<CartItem>
     * */
    public function getCartItems(int $cart_id): array
    {
        $this->conn->beginTransaction();
        try {
            $sql = "SELECT * FROM cart_items cartitems 
                    INNER JOIN cars car ON cartitems.car_id=car.car_id 
                    INNER JOIN engines engine ON engine.car_id=car.car_id WHERE cart_id=:cart_id";
            $statement = $this->conn->prepare($sql);
            $statement->execute([
                'cart_id' => $cart_id
            ]);
            $fields = $statement->fetchAll();
            $this->conn->commit();
            return CartItem::manyFromDatabaseFields($fields);
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw new PDOException("Failed to get cart items");
        }
    }

    public function createCart(int $user_id): bool
    {
        try {
            $this->conn->beginTransaction();
            $sql = "INSERT INTO carts VALUES (:user_id)";
            $statement = $this->conn->prepare($sql);
            $statement->execute([
               ':user_id' => $user_id
            ]);
            return $this->conn->commit();
        } catch (Exception $e) {
            return $this->conn->rollBack();
        }
    }

    public function addToCart(int $car_id, int $cart_id): bool
    {
        try {
            $this->conn->beginTransaction();
            $sql = "SELECT * FROM cart_items WHERE cart_id=:cart_id AND car_id=:car_id";
            $statement = $this->conn->prepare($sql);
            $statement->execute([
                ':cart_id' => $cart_id,
                'car_id' => $car_id
            ]);
            $fields = $statement->fetchAll();
            if (count($fields) === 0) {
                $sql = "INSERT INTO cart_items (car_id, cart_id, quantity) VALUES (:car_id, :cart_id, 1)";
                $statement = $this->conn->prepare($sql);
                $statement->execute([
                    ':car_id' => $car_id,
                    ':cart_id' => $cart_id
                ]);
                return $this->conn->commit();
            }
            $sql = "UPDATE cart_items SET quantity=:quantity WHERE cart_item_id=:cart_item_id";
            $statement = $this->conn->prepare($sql);
            $newQuantity = intval($fields[0]["quantity"]);
            $newQuantity++;
            $statement->execute([
                ':quantity' => $newQuantity,
                ':cart_item_id' => $fields[0]["cart_item_id"]
            ]);
            return $this->conn->commit();
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw new PDOException("Failed to add to cart " . $e->getMessage());
        }
    }

    public function getCartQuantity(int $user_id): int
    {
        try {
            $sql = "SELECT cart_id FROM carts WHERE user_id=:id";
            $statement = $this->conn->prepare($sql);
            $statement->execute([
                ':id' => $user_id
            ]);
            $fields = $statement->fetchAll()[0];
            $sql = "SELECT SUM(quantity) FROM cart_items WHERE cart_id=:id";
            $statement = $this->conn->prepare($sql);
            $statement->execute([
                ':id' => $fields["cart_id"]
            ]);
            return intval(((array)$statement->fetch(PDO::FETCH_NUM))[0]);
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw new PDOException("Failed to get cart quantity");
        }
    }

    public function editCartQuantity(int $cart_item_id, int $quantity): bool
    {
        try {
            $this->conn->beginTransaction();
            $sql = "UPDATE cart_items SET quantity=:quantity WHERE cart_item_id=:id";
            $statement = $this->conn->prepare($sql);
            $statement->execute([
                ':quantity' => $quantity,
                ':id' => $cart_item_id
            ]);
            return $this->conn->commit();
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function removeFromCart(int $cart_item_id): bool
    {
        $this->conn->beginTransaction();
        try {
            $sql = "DELETE FROM cart_items WHERE cart_item_id=:cart_item_id";
            $statement = $this->conn->prepare($sql);
            $statement->execute([
                ':cart_item_id' => $cart_item_id
            ]);
            return $this->conn->commit();
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw new PDOException("Failed to get cart");
        }
    }
}