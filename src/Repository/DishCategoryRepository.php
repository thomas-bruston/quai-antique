<?php

namespace Repository;

use Entity\DishCategory;

class DishCategoryRepository extends AbstractRepository
{
    
    public function findAll(): array
    {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT * FROM dish_category ORDER BY titre ASC'
            );
            $stmt->execute();

            return array_map([$this, 'hydrate'], $stmt->fetchAll());

        } catch (\PDOException $e) {
            throw new \RuntimeException('Erreur lors de la récupération des catégories : ' . $e->getMessage());
        }
    }

    public function findById(int $id): ?DishCategory
    {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT * FROM dish_category WHERE category_id = :id LIMIT 1'
            );
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch();

            return $row ? $this->hydrate($row) : null;

        } catch (\PDOException $e) {
            throw new \RuntimeException('Erreur lors de la récupération de la catégorie : ' . $e->getMessage());
        }
    }

    public function create(DishCategory $category): int
    {
        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO dish_category (titre) VALUES (:titre)'
            );
            $stmt->execute([':titre' => $category->getTitre()]);

            return (int) $this->pdo->lastInsertId();

        } catch (\PDOException $e) {
            throw new \RuntimeException('Erreur lors de la création de la catégorie : ' . $e->getMessage());
        }
    }

    public function update(DishCategory $category): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                'UPDATE dish_category SET titre = :titre WHERE category_id = :id'
            );

            return $stmt->execute([
                ':titre' => $category->getTitre(),
                ':id'    => $category->getCategoryId(),
            ]);

        } catch (\PDOException $e) {
            throw new \RuntimeException('Erreur lors de la mise à jour de la catégorie : ' . $e->getMessage());
        }
    }

    public function delete(int $categoryId): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                'DELETE FROM dish_category WHERE category_id = :id'
            );

            return $stmt->execute([':id' => $categoryId]);

        } catch (\PDOException $e) {
            throw new \RuntimeException('Erreur lors de la suppression de la catégorie : ' . $e->getMessage());
        }
    }

    // --- Hydratation ---

    private function hydrate(array $row): DishCategory
    {
        return new DishCategory(
            titre:      $row['titre'],
            categoryId: (int) $row['category_id']
        );
    }
}
