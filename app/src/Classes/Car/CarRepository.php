<?php
declare(strict_types=1);

namespace App\Classes\Car;

use App\Classes\Database\DatabaseConnection;
use PDO;
use Symfony\Component\HttpFoundation\Request;

class CarRepository
{
    public PDO $conn;

    public function __construct(DatabaseConnection $conn)
    {
        $this->conn = $conn->conn;
    }

    /**
     * @return array<Car>
     * */
    function getCars(): array
    {
        $sql = "SELECT * FROM cars INNER JOIN engines ON cars.car_id=engines.car_id";
        $result = $this->conn->query($sql);
        if (!$result) {
            throw new \PDOException("Failed to fetch cars.");
        }
        $fields = $result->fetchAll();
        return Car::manyFromDatabaseFields($fields);
    }

    function getOne(int $id): Car
    {
        $sql = "SELECT * FROM cars c INNER JOIN engines e ON e.car_id=c.car_id WHERE c.car_id=:id";
        $statement = $this->conn->prepare($sql);
        $statement->execute([
            ":id" => $id
        ]);
        $fields = $statement->fetchAll(PDO::FETCH_ASSOC)[0];
        return Car::oneFromDatabaseFields($fields);
    }

    function save(array $params): void
    {
        $car_sql = "INSERT INTO cars (name, brand, model, url, price) VALUES (:name, :brand, :model, :url, :price)";
        $car_statement = $this->conn->prepare($car_sql);
        $car_statement->execute([
            'name' => $params["name"],
            'brand' => $params["brand"],
            'model' => $params["model"],
            'url' => $params["url"],
            'price' => $params["price"]
        ]);
        $car_id = $this->conn->lastInsertId();
        $engine_sql = "INSERT INTO engines (horsepower, car_id) VALUES (:horsepower, :car_id)";
        $engine_statement = $this->conn->prepare($engine_sql);
        $engine_statement->execute([
            ':horsepower' => $params["horsepower"],
            ':car_id' => $car_id
        ]);
    }

    public function delete(int $id): void
    {
        $car_sql = "DELETE FROM cars WHERE car_id=:car_id";
        $car_statement = $this->conn->prepare($car_sql);
        $car_statement->execute([
            ':car_id' => $id
        ]);
        $engine_sql = "DELETE FROM engines WHERE car_id=:car_id";
        $engine_statement = $this->conn->prepare($engine_sql);
        $engine_statement->execute([
            ':car_id' => $id
        ]);
    }
}
