<?php
declare(strict_types=1);

namespace App\Controllers\Cars;

use App\Classes\Car\Car;
use App\Classes\Car\CarRepository;
use App\Classes\Car\Engine;
use Exception;

class CarController
{
    private CarRepository $carRepository;

    public function __construct(CarRepository $carRepository)
    {
        $this->carRepository = $carRepository;
    }

    /**
     * @return array<Car>
     * */
    public function getAll(int $user_id): array
    {
        return $this->carRepository->getCars($user_id);
    }

    public function getOne(int $id): Car
    {
        return $this->carRepository->getOne($id);
    }

    /**
     * @param array<string,string|int|float> $params
     * @throws Exception
     */
    public function save(array $params, int $user_id): void
    {
        $car = new Car(new Engine((int)$params["horsepower"]), $params["name"], $params["model"], $params["brand"],
            $params["url"], (float)$params["price"], null, (int)$user_id, null);
        $this->carRepository->save($car);
    }

    public function delete(int $id): void
    {
        $this->carRepository->delete($id);
    }
}