<?php
declare(strict_types=1);

namespace App\Controllers\Cars;

use App\Classes\Car\Car;
use App\Classes\Car\CarRepository;
use Symfony\Component\HttpFoundation\Request;

class CarController
{
    /**
     * @return array<Car>
     * */
    public function getAll(): array
    {
        $carRepository = new CarRepository();
        return $carRepository->getCars();
    }

    public function getOne(int $id): Car
    {
        $carRepository = new CarRepository();
        return $carRepository->getOne($id);
    }

    public function save(Request $request): void
    {
        $carRepository = new CarRepository();
        $carRepository->save($request);
    }

    public function delete(int $id): void
    {
        $carRepository = new CarRepository();
        $carRepository->delete($id);
    }
}