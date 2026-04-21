<?php

namespace Repository;

use Entity\Menu;

class MenuRepository extends AbstractRepository
{

    /* Récupère  menus + plats */

    public function findAll(): array
    {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT * FROM menu ORDER BY prix ASC'
            );
            $stmt->execute();
            $rows = $stmt->fetchAll();

            $menus = [];
            foreach ($rows as $row) {
                $menu = $this->hydrate($row);
                $menu->setDishes($this->findDishesByMenu((int) $row['menu_id']));
                $menus[] = $menu;
            }

            return $menus;

        } catch (\PDOException $e) {
            throw new \RuntimeException('Erreur lors de la récupération des menus : ' . $e->getMessage());
        }
    }

    public function findById(int $id): ?Menu
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM menu WHERE menu_id = :id LIMIT 1');
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch();

            if (!$row) return null;

            $menu = $this->hydrate($row);
            $menu->setDishes($this->findDishesByMenu($id));
            return $menu;

        } catch (\PDOException $e) {
            throw new \RuntimeException('Erreur lors de la récupération du menu : ' . $e->getMessage());
        }
    }

    /* Récupère plats */
    
    public function findDishesByMenu(int $menuId): array
    {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT d.* FROM dish d
                 JOIN menu_dish md ON d.dish_id = md.dish_id
                 WHERE md.menu_id = :menu_id'
            );
            $stmt->execute([':menu_id' => $menuId]);
            return $stmt->fetchAll();

        } catch (\PDOException $e) {
            throw new \RuntimeException('Erreur lors de la récupération des plats du menu : ' . $e->getMessage());
        }
    }

    public function create(Menu $menu): int
    {
        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO menu (titre, nombre_sequences, prix) VALUES (:titre, :nombre_sequences, :prix)'
            );
            $stmt->execute([
                ':titre'            => $menu->getTitre(),
                ':nombre_sequences' => $menu->getNombreSequences(),
                ':prix'             => $menu->getPrix(),
            ]);

            $menuId = (int) $this->pdo->lastInsertId();
            $this->syncDishes($menuId, array_column($menu->getDishes(), 'dish_id'));
            return $menuId;

        } catch (\PDOException $e) {
            throw new \RuntimeException('Erreur lors de la création du menu : ' . $e->getMessage());
        }
    }

    public function update(Menu $menu): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                'UPDATE menu SET titre = :titre, nombre_sequences = :nombre_sequences, prix = :prix WHERE menu_id = :id'
            );
            $result = $stmt->execute([
                ':titre'            => $menu->getTitre(),
                ':nombre_sequences' => $menu->getNombreSequences(),
                ':prix'             => $menu->getPrix(),
                ':id'               => $menu->getMenuId(),
            ]);

            $this->syncDishes($menu->getMenuId(), array_column($menu->getDishes(), 'dish_id'));
            return $result;

        } catch (\PDOException $e) {
            throw new \RuntimeException('Erreur lors de la mise à jour du menu : ' . $e->getMessage());
        }
    }

    public function delete(int $menuId): bool
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM menu WHERE menu_id = :id');
            return $stmt->execute([':id' => $menuId]);

        } catch (\PDOException $e) {
            throw new \RuntimeException('Erreur lors de la suppression du menu : ' . $e->getMessage());
        }
    }

    /**
     * Synchronise les plats d'un menu (supprime tout et réinsère)
     */
    private function syncDishes(int $menuId, array $dishIds): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM menu_dish WHERE menu_id = :menu_id');
        $stmt->execute([':menu_id' => $menuId]);

        if (empty($dishIds)) return;

        $stmt = $this->pdo->prepare('INSERT INTO menu_dish (menu_id, dish_id) VALUES (:menu_id, :dish_id)');
        foreach ($dishIds as $dishId) {
            $stmt->execute([':menu_id' => $menuId, ':dish_id' => (int) $dishId]);
        }
    }

    private function hydrate(array $row): Menu
    {
        return new Menu(
            titre:           $row['titre'],
            nombreSequences: (int) $row['nombre_sequences'],
            prix:            (int) $row['prix'],
            menuId:          (int) $row['menu_id']
        );
    }
}
