<?php
declare(strict_types=1);

try {
    $host = "mysql";
    $db_name = "silexCars";
    $username = "root";
    $password = "db_pass";
    echo "Creating connection\n";
    $conn = new PDO("mysql:host=$host", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Creating database\n";
    $sql = "CREATE DATABASE $db_name";
    $conn->exec($sql);
    $conn = null;
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Creating tables\n";
    $sql_arr = ["create table silexCars.cars
(
    car_id int auto_increment comment 'primary key'
        primary key,
    brand  varchar(255) not null,
    name   varchar(255) not null,
    model  varchar(255) not null,
    url    longtext     not null,
    price  float        not null
);", "create table silexCars.engines
(
    horsepower int not null,
    engine_id  int auto_increment comment 'primary key'
        primary key,
    car_id     int not null comment 'foreign key',
    constraint engines_cars_null_fk
        foreign key (car_id) references silexCars.cars (car_id)
            on delete cascade
);", "CREATE TABLE `users` (
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci

", "create table silexCars.carts
(
    cart_id int auto_increment
        primary key
);

", "create table silexCars.cart_items
(
    cart_item_id int auto_increment
        primary key,
    car_id       int not null,
    cart_id      int not null,
    quantity     int not null,
    constraint car_id
        foreign key (car_id) references silexCars.cars (car_id)
            on delete cascade,
    constraint cart_items_cart_null_fk
        foreign key (cart_id) references silexCars.carts (cart_id)
            on delete cascade
);

", "create table silexCars.orders
(
    order_id int auto_increment
        primary key
);

", "create table silexCars.order_items
(
    order_item_id int auto_increment
        primary key,
    order_id      int null,
    car_id        int not null,
    quantity      int not null,
    constraint order_items_cars_null_fk
        foreign key (car_id) references silexCars.cars (car_id),
    constraint order_items_orders_null_fk
        foreign key (order_id) references silexCars.orders (order_id)
            on delete cascade
);

"];
    foreach ($sql_arr as $sql)
    {
        $conn->exec($sql);
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
