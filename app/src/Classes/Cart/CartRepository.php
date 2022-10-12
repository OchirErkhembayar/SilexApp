<?php
declare(strict_types=1);

namespace App\Classes\Cart;

use App\Classes\Database\DatabaseConnection;
use PDO;

class CartRepository
{
    public PDO $conn;

    public function __construct(DatabaseConnection $conn)
    {
        $this->conn = $conn->conn;
    }

    public function getCart(): Cart
    {
        $sql = "SELECT * FROM carts";
        $statement = $this->conn->query($sql);
        if (gettype($statement) === "boolean") {
            throw new \PDOException("Failed to fetch cart.");
        }
        $fields = $statement->fetchAll()[0];
        return Cart::oneFromDatabaseFields($fields);
    }

    /**
     * @return array<CartItem>
     * */
    public function getCartItems(int $cart_id): array
    {
        $sql = "SELECT * FROM cart_items cartitems 
                INNER JOIN cars car ON cartitems.car_id=car.car_id 
                INNER JOIN engines engine ON engine.car_id=car.car_id WHERE cart_id=:cart_id";
        $statement = $this->conn->prepare($sql);
        $statement->execute([
            'cart_id' => $cart_id
        ]);
        $fields = $statement->fetchAll();
        return CartItem::manyFromDatabaseFields($fields);
    }

    public function addToCart(int $car_id, int $cart_id): void
    {
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
            return;
        }
        $sql = "UPDATE cart_items SET quantity=:quantity WHERE cart_item_id=:cart_item_id";
        $statement = $this->conn->prepare($sql);
        $newQuantity = intval($fields[0]["quantity"]);
        $newQuantity++;
        $statement->execute([
            ':quantity' => $newQuantity,
            ':cart_item_id' => $fields[0]["cart_item_id"]
        ]);
    }

    public function removeFromCart(int $cart_item_id): void
    {
        $sql = "DELETE FROM cart_items WHERE cart_item_id=:cart_item_id";
        $statement = $this->conn->prepare($sql);
        $statement->execute([
            ':cart_item_id' => $cart_item_id
        ]);
    }
}