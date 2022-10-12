<?php
declare(strict_types=1);

namespace Test\Controllers\Cars;

use App\Classes\Car\Car;
use App\Classes\Car\CarRepository;
use App\Classes\Database\DatabaseConnection;
use App\Controllers\Cars\CarController;
use App\Scripts\Database\Test\Data\Car\CarData;
use PHPUnit\Framework\TestCase;

class CarControllerTest extends TestCase
{
    private CarController $carController;
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
        $this->assertSame("object", \gettype($cars[0]));
    }

    /**
     * @test
     */
    public function it_can_fetch_one_car_by_id(): void
    {
        $car = Car::oneFromDatabaseFields($this->fields[0]);
        $this->assertEquals($car, $this->carController->getOne(1));
    }
}