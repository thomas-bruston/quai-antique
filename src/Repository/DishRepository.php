<?php

namespace Repository;

use Entity\Dish;

class DishRepository extends AbstractRepository
{

    public function findAll(): array
    {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT d.*, c.titre AS category_titre
                 FROM dish d
                 JOIN dish_category c ON d.category_id = c.category_id
                 ORDER BY c.category_id ASC, d.dish_id ASC'
            );
            $stmt->execute();

            return array_map([$this, 'hydrate'], $stmt->fetchAll());

        } catch (\PDOException $e) {
            throw new \RuntimeException('Erreur lors de la récupération des plats : ' . $e->getMessage());
        }
    }

    /* Retourne plats groupés en catégorie */

    public function findAllGroupedByCategory(): array
    {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT d.*, c.titre AS category_titre
                 FROM dish d
                 JOIN dish_category c ON d.category_id = c.category_id
                 ORDER BY c.category_id ASC, d.dish_id ASC'
            );
            $stmt->execute();
            $rows = $stmt->fetchAll();

            $grouped = [];
            foreach ($rows as $row) {
                $grouped[$row['category_titre']][] = $this->hydrate($row);
            }

            return $grouped;

        } catch (\PDOException $e) {
            throw new \RuntimeException('Erreur lors du regroupement des plats : ' . $e->getMessage());
        }
    }

    public function findById(int $id): ?Dish
    {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT * FROM dish WHERE dish_id = :id LIMIT 1'
            );
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch();

            return $row ? $this->hydrate($row) : null;

        } catch (\PDOException $e) {
            throw new \RuntimeException('Erreur lors de la récupération du plat : ' . $e->getMessage());
        }
    }

    public function create(Dish $dish): int
    {
        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO dish (category_id, titre, description, prix)
                 VALUES (:category_id, :titre, :description, :prix)'
            );
            $stmt->execute([
                ':category_id' => $dish->getCategoryId(),
                ':titre'       => $dish->getTitre(),
                ':description' => $dish->getDescription(),
                ':prix'        => $dish->getPrix(),
            ]);

            return (int) $this->pdo->lastInsertId();

        } catch (\PDOException $e) {
            throw new \RuntimeException('Erreur lors de la création du plat : ' . $e->getMessage());
        }
    }

    public function update(Dish $dish): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                'UPDATE dish
                 SET category_id = :category_id,
                     titre       = :titre,
                     description = :description,
                     prix        = :prix
                 WHERE dish_id = :id'
            );

            return $stmt->execute([
                ':category_id' => $dish->getCategoryId(),
                ':titre'       => $dish->getTitre(),
                ':description' => $dish->getDescription(),
                ':prix'        => $dish->getPrix(),
                ':id'          => $dish->getDishId(),
            ]);

        } catch (\PDOException $e) {
            throw new \RuntimeException('Erreur de la mise à jour du plat : ' . $e->getMessage());
        }
    }

    public function delete(int $dishId): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                'DELETE FROM dish WHERE dish_id = :id'
            );

            return $stmt->execute([':id' => $dishId]);

        } catch (\PDOException $e) {
            throw new \RuntimeException('Erreur de la suppression du plat : ' . $e->getMessage());
        }
    }

    // Hydratation

    private function hydrate(array $row): Dish
    {
        return new Dish(
            categoryId:  (int) $row['category_id'],
            titre:       $row['titre'],
            description: $row['description'],
            prix:        (float) $row['prix'],
            dishId:      (int) $row['dish_id']
        );
    }
}
