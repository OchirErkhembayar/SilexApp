<?php

namespace App\Classes\User;

use App\Classes\Database\DatabaseConnection;
use Exception;
use PDO;

class UserRepository
{
    public PDO $conn;

    public function __construct(DatabaseConnection $conn)
    {
        $this->conn = $conn->conn;
    }

    public function fetchUserInfo(int $id): User
    {
        try {
            $this->conn->beginTransaction();
            $sql = "SELECT username, email, balance, user_id FROM users WHERE user_id=:id";
            $statement = $this->conn->prepare($sql);
            $statement->execute([
               'id' => $id
            ]);
            $fields = $statement->fetchAll()[0];
            $this->conn->commit();
            return User::oneFromDatabaseFields($fields);
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw new \PDOException('Failed to fetch user details');
        }
    }

    public function getAuthenticateUser(string $username, string $password): User|bool
    {
        try {
            $this->conn->beginTransaction();
            $sql = "SELECT * FROM users WHERE username=:username AND password=:password";
            $statement = $this->conn->prepare($sql);
            $statement->execute([
                ':username' => $username,
                ':password' => $password
            ]);
            $fieldsArray = $statement->fetchAll();
            if (count($fieldsArray) === 0) {
                throw new \InvalidArgumentException("Incorrect login credentials");
            }
            $fields = $fieldsArray[0];
            return User::oneFromDatabaseFields($fields);
        } catch (Exception $e) {
            return $this->conn->rollBack();
        }
    }

    public function createUser(string $username, string $email, string $password): User|bool
    {
        try {
            $this->conn->beginTransaction();
            $sql = "INSERT INTO users VALUES (:username, :password, :email)";
            $statement = $this->conn->prepare($sql);
            $statement->execute([
               ':username' => $username,
               ':password' => $password,
               ':email' => $email
            ]);
            $user_id = $this->conn->lastInsertId();
            $sql = "SELECT username, email, balance FROM users where user_id=:id";
            $statement = $this->conn->prepare($sql);
            $statement->execute([
                ':id' => $user_id
            ]);
            $params = $statement->fetchAll()[0];
            $sql = "INSERT INTO carts VALUES (:user_id)";
            $statement = $this->conn->prepare($sql);
            $statement->execute([
                ':user_id' => $user_id
            ]);
            $this->conn->commit();
            return User::oneFromDatabaseFields($params);
        } catch (Exception $e) {
            return $this->conn->rollBack();
        }
    }

    public function addBalance(int $user_id, float $amount): void
    {
        try {
            $this->conn->beginTransaction();
            $sql = "UPDATE users SET balance = balance + :amount WHERE user_id=:user_id";
            $statement = $this->conn->prepare($sql);
            $statement->execute([
               ':amount' => $amount,
               ':user_id' => $user_id
            ]);
            $this->conn->commit();
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw new \PDOException("Failed to add balance");
        }
    }
}