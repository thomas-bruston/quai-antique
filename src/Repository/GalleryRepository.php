<?php

namespace Repository;

use Entity\Gallery;

class GalleryRepository extends AbstractRepository
{

    public function findAll(): array
    {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT * FROM gallery ORDER BY created_at DESC'
            );
            $stmt->execute();

            return array_map([$this, 'hydrate'], $stmt->fetchAll());

        } catch (\PDOException $e) {
            throw new \RuntimeException('Erreur lors de la récupération de la galerie : ' . $e->getMessage());
        }
    }

    public function findById(int $id): ?Gallery
    {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT * FROM gallery WHERE gallery_id = :id LIMIT 1'
            );
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch();

            return $row ? $this->hydrate($row) : null;

        } catch (\PDOException $e) {
            throw new \RuntimeException('Erreur lors de la récupération de la photo : ' . $e->getMessage());
        }
    }

    public function create(Gallery $gallery): int
    {
        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO gallery (titre, photo) VALUES (:titre, :photo)'
            );
            $stmt->execute([
                ':titre' => $gallery->getTitre(),
                ':photo' => $gallery->getPhoto(),
            ]);

            return (int) $this->pdo->lastInsertId();

        } catch (\PDOException $e) {
            throw new \RuntimeException('Erreur lors de l\'ajout de la photo : ' . $e->getMessage());
        }
    }

    public function update(Gallery $gallery): bool
    {
        try {
            if ($gallery->getPhoto() !== null) {
                $stmt = $this->pdo->prepare(
                    'UPDATE gallery SET titre = :titre, photo = :photo WHERE gallery_id = :id'
                );
                return $stmt->execute([
                    ':titre' => $gallery->getTitre(),
                    ':photo' => $gallery->getPhoto(),
                    ':id'    => $gallery->getGalleryId(),
                ]);
            } else {
                $stmt = $this->pdo->prepare(
                    'UPDATE gallery SET titre = :titre WHERE gallery_id = :id'
                );
                return $stmt->execute([
                    ':titre' => $gallery->getTitre(),
                    ':id'    => $gallery->getGalleryId(),
                ]);
            }
        } catch (\PDOException $e) {
            throw new \RuntimeException('Erreur lors de la mise à jour de la photo : ' . $e->getMessage());
        }
    }

    public function delete(int $galleryId): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                'DELETE FROM gallery WHERE gallery_id = :id'
            );

            return $stmt->execute([':id' => $galleryId]);

        } catch (\PDOException $e) {
            throw new \RuntimeException('Erreur lors de la suppression de la photo : ' . $e->getMessage());
        }
    }

    // Hydratation

    private function hydrate(array $row): Gallery
    {
        return new Gallery(
            titre:     $row['titre'],
            photo:     $row['photo'],
            galleryId: (int) $row['gallery_id']
            
        );
    }
}
