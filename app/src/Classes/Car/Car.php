<?php
declare(strict_types=1);

namespace App\Classes\Car;

class Car
{
    public function __construct(public Engine          $engine, public readonly string $name,
                                public readonly string $model, public readonly string $brand, public readonly string
                                $url, public readonly float $price,
                                public readonly ?int    $car_id = null, public readonly ?int $cart_item_id=null)
    {
    }

    public static function oneFromDatabaseFields($fields): Car
    {
        return new Car(new Engine($fields["horsepower"], $fields["engine_id"]), $fields["name"], $fields["model"],
            $fields["brand"], $fields["url"], $fields["price"], $fields["car_id"]);
    }

    public static function manyFromDatabaseFields($fields): array
    {
        return \array_map('self::oneFromDatabaseFields', $fields);
    }
}