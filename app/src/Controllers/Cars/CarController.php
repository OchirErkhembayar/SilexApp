<?php
declare(strict_types=1);

namespace App\Controllers\Cars;

use App\Classes\Car\Car;
use App\Classes\Car\CarRepository;
use Symfony\Component\HttpFoundation\Request;

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

    public function save(Request $request): void
    {
        $this->carRepository->save($request);
    }

    public function delete(int $id): void
    {
        $this->carRepository->delete($id);
    }
}