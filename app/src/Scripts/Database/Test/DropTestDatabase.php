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
    $sql = "DROP DATABASE $db_name";
    $conn->exec($sql);
} catch (Exception $e) {
    echo $e->getMessage();
}