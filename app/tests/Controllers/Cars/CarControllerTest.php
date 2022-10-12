<?php
declare(strict_types=1);

namespace Test\Controllers\Cars;

use App\Classes\Car\CarRepository;
use App\Classes\Database\DatabaseConnection;
use App\Controllers\Cars\CarController;
use App\Scripts\Database\Test\Data\Car\CarData;
use PHPUnit\Framework\TestCase;

class CarControllerTest extends TestCase
{
    private CarController $carController;
    /**
     * @var array<string|int,string|float> $fields
     */
    private array $fields;

    public function setUp(): void
    {
        parent::setUp();
        $carData = new CarData();
        $this->fields = $carData::CAR_FIELDS_ARRAY;
        $dbc = new DatabaseConnection("silexCarsTest");
        $this->carController = new CarController(new CarRepository($dbc));
    }

    /**
     * @test
     */
    public function it_can_fetch_all_cars_from_the_database(): void
    {
        $cars = $this->carController->getAll();
        $this->assertObjectHasAttribute("name", $cars[0]);
        $this->assertObjectHasAttribute("brand", $cars[0]);
        $this->assertObjectHasAttribute("model", $cars[0]);
        $this->assertObjectHasAttribute("url", $cars[0]);
        $this->assertObjectHasAttribute("engine", $cars[0]);
    }

    /**
     * @test
     */
    public function it_can_fetch_one_car_by_id(): void
    {
        $car = $this->carController->getAll()[0];
        \assert($car->car_id !== null);
        $car = $this->carController->getOne($car->car_id);
        $this->assertObjectHasAttribute("name", $car);
        $this->assertObjectHasAttribute("brand", $car);
        $this->assertObjectHasAttribute("model", $car);
        $this->assertObjectHasAttribute("url", $car);
        $this->assertObjectHasAttribute("engine", $car);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_save_a_car_to_the_database(): void
    {
        /**
         * @var array<string,int|float|string> $params
         */
       $params = $this->fields[1];
       $result = $this->carController->save($params);
       $this->assertTrue($result);
    }
}