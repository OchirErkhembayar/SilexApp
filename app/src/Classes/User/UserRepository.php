<?php

namespace App\Classes\User;

use App\Classes\Cart\CartRepository;
use App\Services\Database\DatabaseConnection;
use Exception;
use PDO;
use PDOException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class UserRepository
{
    private PDO $conn;

    public function __construct(DatabaseConnection $conn)
    {
        $this->conn = $conn->conn;
    }

    public function findById(int $id): User
    {
        try {
            $this->conn->beginTransaction();
            $sql = "SELECT username, email, balance, user_id FROM users WHERE user_id=:id";
            $statement = $this->conn->prepare($sql);
            $statement->execute([
               ':id' => $id
            ]);
            $fields = $statement->fetchAll()[0];
            $this->conn->commit();
            return User::oneFromDatabaseFields($fields);
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw new PDOException('Failed to fetch user details');
        }
    }

    public function findByUsername(string $username): ?User
    {
        try {
            $this->conn->beginTransaction();
            $sql = "SELECT username, email, balance, user_id FROM users WHERE username=:username";
            $statement = $this->conn->prepare($sql);
            $statement->execute([
                ':username' => $username
            ]);
            $fieldsArray = $statement->fetchAll();
            if (count($fieldsArray) < 1) {
                throw new ResourceNotFoundException("Couldn't find user");
            }
            $fields = $fieldsArray[0];
            $this->conn->commit();
            return User::oneFromDatabaseFields($fields);
        } catch (Exception $e) {
            $this->conn->rollBack();
            return null;
        }
    }

    public function findByEmail(string $email): User|bool
    {
        try {
            $this->conn->beginTransaction();
            $sql = "SELECT username, email, balance, user_id FROM users WHERE email=:email";
            $statement = $this->conn->prepare($sql);
            $statement->execute([
                ':email' => $email
            ]);
            $fieldsArray = $statement->fetchAll();
            if (count($fieldsArray) < 1) {
                throw new ResourceNotFoundException("Couldn't find user");
            }
            $fields = $fieldsArray[0];
            $this->conn->commit();
            return User::oneFromDatabaseFields($fields);
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function authenticateAndFindUser(string $username, string $password): User|bool
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
            if (count($fieldsArray) < 1) {
                throw new \InvalidArgumentException("Incorrect login credentials");
            }
            $fields = $fieldsArray[0];
            return User::oneFromDatabaseFields($fields);
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function createUser(string $username, string $email, string $password, CartRepository $cartRepository): ?User
    {
        try {
            $this->conn->beginTransaction();
            $sql = "INSERT INTO users (username, password, email, balance) VALUES (:username, :password, :email, :balance)";
            $statement = $this->conn->prepare($sql);
            $statement->execute([
               ':username' => $username,
               ':password' => $password,
               ':email' => $email,
               ':balance' => 1000000
            ]);
            $user_id = $this->conn->lastInsertId();
            $cartRepository->createCart($user_id);
            $this->conn->commit();
            return new User($email, $username, 1000000, $user_id);
        } catch (PDOException $e) {
            $this->conn->rollBack();
            throw $e;
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
            throw new PDOException("Failed to add balance");
        }
    }
}