<?php
declare(strict_types=1);

namespace App\Classes\Car;

class Car
{
    public function __construct(public Engine          $engine, public readonly string $name,
                                public readonly string $model, public readonly string $brand, public readonly string
                                $url, public readonly float $price,
                                public readonly ?int    $car_id = null, public readonly ?int $user_id = null, public readonly ?int $cart_item_id=null)
    {
    }

    /**
     * @param array<string,float|string|int> $fields
     * */
    public static function oneFromDatabaseFields(array $fields): Car
    {
        return new Car(new Engine((int)$fields["horsepower"], (int)$fields["engine_id"]), (string)$fields["name"],
            (string)$fields["model"],
            (string)$fields["brand"], (string)$fields["url"], (float)$fields["price"], (int)$fields["car_id"], (int)
            $fields["user_id"]);
    }

    /**
     * @return array<Car>
     * @param array<array<string,float|string|int>> $fields
     * */
    public static function manyFromDatabaseFields(array $fields): array
    {
        return \array_map([Car::class, 'oneFromDatabaseFields'], $fields);
    }
}