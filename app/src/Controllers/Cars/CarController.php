<?php
declare(strict_types=1);

namespace App\Controllers\Cars;

use App\Classes\Car\Car;
use App\Classes\Car\CarRepository;
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
    public function save(array $params, int $user_id): bool
    {
        return $this->carRepository->save($params, $user_id);
    }

    public function delete(int $id): void
    {
        $this->carRepository->delete($id);
    }
}