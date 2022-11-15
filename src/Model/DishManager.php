<?php

namespace App\Model;

use PDO;

class DishManager extends AbstractManager
{
    public const TABLE = 'dish';

    public function selectAllWithCategory(): array
    {
        $query = 'SELECT d.*, c.title category_title FROM ' . self::TABLE . ' d 
                  JOIN ' . CategoryManager::TABLE . ' c ON d.category_id=c.id
                  ORDER BY c.title ASC, d.title ASC';

        return $this->pdo->query($query)->fetchAll();
    }

    public function insert(array $dish): void
    {
        $query = "INSERT INTO " . self::TABLE .
            " (`title`, `description`, `price`, `category_id`, `image`) 
            VALUES (:title, :description, :price, :category, :image)";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue('title', $dish['title'], PDO::PARAM_STR);
        $statement->bindValue('description', $dish['description'], PDO::PARAM_STR);
        $statement->bindValue('price', $dish['price']);
        $statement->bindValue('category', $dish['category']);
        $statement->bindValue('image', $dish['image']);

        $statement->execute();
    }

    public function update(array $dish): bool
    {
        $query = 'UPDATE ' . self::TABLE . ' 
            SET `title` = :title, description=:description, price=:price, category_id=:category';

        if ($dish['image']) {
            $query .= ', image=:image';
        }
        $query .= ' WHERE id=:id';

        $statement = $this->pdo->prepare($query);
        $statement->bindValue('id', $dish['id'], PDO::PARAM_INT);
        $statement->bindValue('title', $dish['title'], PDO::PARAM_STR);
        $statement->bindValue('description', $dish['description'], PDO::PARAM_STR);
        $statement->bindValue('price', $dish['price']);
        $statement->bindValue('category', $dish['category']);
        if ($dish['image']) {
            $statement->bindValue('image', $dish['image']);
        }

        return $statement->execute();
    }
}
