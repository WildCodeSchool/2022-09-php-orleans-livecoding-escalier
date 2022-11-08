<?php

namespace App\Model;

use PDO;

class DishManager extends AbstractManager
{
    public const TABLE = 'dish';

    public function insert(array $dish): void
    {
        $query = "INSERT INTO " . self::TABLE .
            " (`title`, `description`, `price`) 
            VALUES (:title, :description, :price)";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue('title', $dish['title'], PDO::PARAM_STR);
        $statement->bindValue('description', $dish['description'], PDO::PARAM_STR);
        $statement->bindValue('price', $dish['price']);

        $statement->execute();
    }

    public function update(array $dish): bool
    {
        $query = "UPDATE " . self::TABLE . " 
            SET `title` = :title, description=:description, price=:price 
            WHERE id=:id";

        $statement = $this->pdo->prepare($query);
        $statement->bindValue('id', $dish['id'], PDO::PARAM_INT);
        $statement->bindValue('title', $dish['title'], PDO::PARAM_STR);
        $statement->bindValue('description', $dish['description'], PDO::PARAM_STR);
        $statement->bindValue('price', $dish['price']);

        return $statement->execute();
    }
}
