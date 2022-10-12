<?php
declare(strict_types=1);

namespace App\Classes\Database;

use PDO;

class DatabaseConnection
{
    public PDO $conn;

    public function __construct(string  $db_name)
    {
        $host = "mysql";
        $username = "root";
        $password = "db_pass";
        $this->conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
}