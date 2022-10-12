<?php
declare(strict_types=1);

namespace App\Controllers\Cars;

use App\Classes\Car\Car;
use App\Classes\Car\CarRepository;

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
    public function getAll(): array
    {
        return $this->carRepository->getCars();
    }

    public function getOne(int $id): Car
    {
        return $this->carRepository->getOne($id);
    }

    /**
     * @param array<string,string|int|float> $params
     * @return void
     */
    public function save(array $params): void
    {
        $this->carRepository->save($params);
    }

    public function delete(int $id): void
    {
        $this->carRepository->delete($id);
    }
}