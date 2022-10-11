<?php
declare(strict_types=1);

namespace App\Classes\Car;

use PDO;

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

    function getCars(): array
    {
        $sql = "SELECT * FROM cars INNER JOIN engines ON cars.car_id=engines.car_id";
        $fields = $this->conn->query($sql)->fetchAll();
        return Car::manyFromDatabaseFields($fields);
    }

    function getOne($id): Car
    {
        $sql = "SELECT * FROM cars c INNER JOIN engines e ON e.car_id=c.car_id WHERE c.car_id=:id";
        $statement = $this->conn->prepare($sql);
        $statement->execute([
            ":id" => $id
        ]);
        $fields = $statement->fetchAll(PDO::FETCH_ASSOC)[0];
        return Car::oneFromDatabaseFields($fields);
    }

    function save($params): void
    {
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

    public function getCarsById($id_array): void
    {
        $sql = "SELECT * FROM cars WHERE car_id IN (:id_array)";
        $statement = $this->conn->prepare($sql);
        $statement->execute([
            ':id_array' => $id_array
        ]);
        $fields = $statement->fetchAll();
        print_r($fields);
    }

    public function delete($id): void
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
