<?php
declare(strict_types=1);

namespace Test\Classes\Car;

use App\Classes\Car\Engine;
use App\Scripts\Database\Test\Data\Car\CarData;
use Exception;
use PHPUnit\Framework\TestCase;
use App\Classes\Car\Car;

class CarTest extends TestCase
{
    /**
     * @test
     * @throws Exception
     */
    public function it_can_create_a_car_from_database_fields(): void
    {
        $fields = [
            "car_id" => 1,
            "name" => "TestCar",
            "model" => "TestModel",
            "brand" => "TestBrand",
            "url" => "TestURL",
            "price" => 1337,
            "horsepower" => 1337,
            "engine_id" => 1
        ];
        $engine = new Engine(1337, 1);
        $car = new Car($engine, $fields["name"], $fields["model"], $fields["brand"], $fields["url"], $fields["price"],
            $fields["car_id"]);
        $this->assertEquals($car, Car::oneFromDatabaseFields($fields));
    }

    /**
     * @test
     * @throws Exception
     */
    public function it_can_create_many_cars_from_database_fields(): void
    {
        $fieldsArray = CarData::CAR_FIELDS_ARRAY;
        $carsArray = [];
        foreach ($fieldsArray as $fields)
        {
            $engine = new Engine($fields["horsepower"], $fields["engine_id"]);
            $car = new Car($engine, $fields["name"], $fields["model"], $fields["brand"], $fields["url"], $fields["price"],
                $fields["car_id"]);
            $carsArray[] = $car;
        }
        $this->assertEquals($carsArray, Car::manyFromDatabaseFields($fieldsArray));
    }
}