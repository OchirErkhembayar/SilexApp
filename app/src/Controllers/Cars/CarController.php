<?php
declare(strict_types=1);

namespace App\Controllers\Cars;

use App\Classes\Car\Car;
use App\Classes\Car\CarRepository;

class CarController
{
    public function getAll(): array
    {
        $carRepository = new CarRepository();
        return $carRepository->getCars();
    }

    public function getOne($id): Car
    {
        $carRepository = new CarRepository();
        return $carRepository->getOne($id);
    }

    public function save($params): void
    {
        $carRepository = new CarRepository();
        $carRepository->save($params);
    }

    public function delete($params): void
    {
        $carRepository = new CarRepository();
        $id = $params->get("id");
        $carRepository->delete($id);
    }
}