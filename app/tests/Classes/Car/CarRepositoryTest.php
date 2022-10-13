<?php
declare(strict_types=1);

namespace Test\Classes\Car;
use App\Classes\Car\Car;
use App\Classes\Car\CarRepository;
use App\Classes\Database\DatabaseConnection;
use App\Scripts\Database\Test\Data\Car\CarData;
use PHPUnit\Framework\TestCase;

class CarRepositoryTest extends TestCase
{
    private CarRepository $carRepository;

    public function setUp(): void
    {
        parent::setUp();
        $dbc = new DatabaseConnection("silexCarsTest");
        $this->carRepository = new CarRepository($dbc);
    }
    /**
     * @test
     */
    public function it_can_fetch_all_cars_from_the_database(): void
    {
        $this->assertEquals("object", gettype($this->carRepository->getCars(0)[0]));
    }
}