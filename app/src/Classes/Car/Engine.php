<?php
declare(strict_types=1);

namespace App\Classes\Car;

class Engine
{
    public function __construct(public readonly int $horsepower, public readonly int $id = 0)
    {
    }
}