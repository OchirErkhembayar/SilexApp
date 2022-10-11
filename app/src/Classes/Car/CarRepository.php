<?php
declare(strict_types=1);

namespace App\Classes\Car;

use PDO;
use Symfony\Component\HttpFoundation\Request;

class CarRepository
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

    function save(Request $request): void
    {
        $params = $request->request;
        $car_sql = "INSERT INTO cars (name, brand, model, url, price) VALUES (:name, :brand, :model, :url, :price)";
        $car_statement = $this->conn->prepare($car_sql);
        $car_statement->execute([
            ':name' => $params->get("name"),
            ':brand' => $params->get("brand"),
            ':model' => $params->get("model"),
            ':url' => $params->get("url"),
            ':price' =>$params->get("price")
        ]);
        $car_id = $this->conn->lastInsertId();
        $engine_sql = "INSERT INTO engines (horsepower, car_id) VALUES (:horsepower, :car_id)";
        $engine_statement = $this->conn->prepare($engine_sql);
        $engine_statement->execute([
            ':horsepower' => $params->get("horsepower"),
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
