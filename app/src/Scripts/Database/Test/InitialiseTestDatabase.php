<?php
require_once 'Data/Car/CarData.php';

use App\Scripts\Database\Test\Data\Car\CarData;

try {
    $host = "mysql";
    $db_name = "silexCarsTest";
    $username = "root";
    $password = "db_pass";
    $conn = new PDO("mysql:host=$host", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Creating database\n";
    $sql = "CREATE DATABASE $db_name";
    $conn->exec($sql);
    echo "Database created\n";
    $conn = null;
    echo "Creating tables\n";
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql_arr = ["create table silexCarsTest.cars
    (
        car_id int auto_increment comment 'primary key'
            primary key,
        brand  varchar(255) not null,
        name   varchar(255) not null,
        model  varchar(255) not null,
        url    longtext     not null,
        price  float        not null
    );", "create table silexCarsTest.engines
    (
        horsepower int not null,
        engine_id  int auto_increment comment 'primary key'
            primary key,
        car_id     int not null comment 'foreign key',
        constraint engines_cars_null_fk
            foreign key (car_id) references cars (car_id)
                on delete cascade
    );", "create table silexCarsTest.carts
    (
        cart_id int auto_increment
            primary key
    );
    
    ", "create table silexCarsTest.cart_items
    (
        cart_item_id int auto_increment
            primary key,
        car_id       int not null,
        cart_id      int not null,
        quantity     int not null,
        constraint car_id
            foreign key (car_id) references cars (car_id)
                on delete cascade,
        constraint cart_items_cart_null_fk
            foreign key (cart_id) references carts (cart_id)
                on delete cascade
    );
    
    ", "create table silexCarsTest.orders
    (
        order_id int auto_increment
            primary key
    );
    
    ", "create table silexCarsTest.order_items
    (
        order_item_id int auto_increment
            primary key,
        order_id      int null,
        car_id        int not null,
        quantity      int not null,
        constraint order_items_cars_null_fk
            foreign key (car_id) references cars (car_id),
        constraint order_items_orders_null_fk
            foreign key (order_id) references orders (order_id)
                on delete cascade
    );
    
    "];
    foreach ($sql_arr as $sql) {
        $conn->exec($sql);
    }
    echo "Tables created\n";
    echo "Created one cart\n";
    $sql = "INSERT INTO carts () VALUES ()";
    $conn->exec($sql);
    echo "One cart created\n";
    echo "Creating 4 cars and engines\n";
    $carFieldsArray = CarData::CAR_FIELDS_ARRAY;
    foreach ($carFieldsArray as $fields) {
        $carSql = "INSERT INTO cars VALUES (:car_id,:brand, :name, :model, :url, :price)";
        $engineSql = "INSERT INTO engines VALUES (:horsepower, :engine_id, :car_id)";
        $carStatement = $conn->prepare($carSql);
        $carStatement->execute([
            ':brand' => $fields["brand"],
            ':name' => $fields["name"],
            ':model' => $fields["model"],
            ':url' => $fields["url"],
            ':price' => $fields["price"],
            ':car_id' => $fields["car_id"]
        ]);
        $engineStatement = $conn->prepare($engineSql);
        $engineStatement->execute([
            'horsepower' => $fields["horsepower"],
            "engine_id" => $fields["engine_id"],
            "car_id" => $fields["car_id"]
        ]);
    }
    echo "Created 4 cars and engines\n";
} catch (Exception $e) {
    echo $e->getMessage() . "\n";
}

