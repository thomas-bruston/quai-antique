<?php

namespace Repository;

use Entity\User;

class UserRepository extends AbstractRepository
{

    public function findByEmail(string $email): ?User
    {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT * FROM user WHERE email = :email LIMIT 1'
            );
            $stmt->execute([':email' => $email]);
            $row = $stmt->fetch();

            return $row ? $this->hydrate($row) : null;

        } catch (\PDOException $e) {
            throw new \RuntimeException('Erreur lors de la recherche par email : ' . $e->getMessage());
        }
    }

    public function findById(int $id): ?User
    {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT * FROM user WHERE user_id = :id LIMIT 1'
            );
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch();

            return $row ? $this->hydrate($row) : null;

        } catch (\PDOException $e) {
            throw new \RuntimeException('Erreur lors de la recherche par ID : ' . $e->getMessage());
        }
    }

    public function create(User $user): int
    {
        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO user (email, password, prenom, nom, nombre_convives_defaut, allergies, role)
                 VALUES (:email, :password, :prenom, :nom, :nombre_convives_defaut, :allergies, :role)'
            );
            $stmt->execute([
                ':email'                  => $user->getEmail(),
                ':password'               => $user->getPassword(),
                ':prenom'                 => $user->getPrenom(),
                ':nom'                    => $user->getNom(),
                ':nombre_convives_defaut' => $user->getNombreConvivesDefaut(),
                ':allergies'              => $user->getAllergies(),
                ':role'                   => $user->getRole(),
            ]);

            return (int) $this->pdo->lastInsertId();

        } catch (\PDOException $e) {
            throw new \RuntimeException('Erreur lors de la création de l\'utilisateur : ' . $e->getMessage());
        }
    }

    public function update(User $user): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                'UPDATE user
                 SET email = :email,
                     prenom = :prenom,
                     nom = :nom,
                     nombre_convives_defaut = :nombre_convives_defaut,
                     allergies = :allergies
                 WHERE user_id = :id'
            );

            return $stmt->execute([
                ':email'                  => $user->getEmail(),
                ':prenom'                 => $user->getPrenom(),
                ':nom'                    => $user->getNom(),
                ':nombre_convives_defaut' => $user->getNombreConvivesDefaut(),
                ':allergies'              => $user->getAllergies(),
                ':id'                     => $user->getUserId(),
            ]);

        } catch (\PDOException $e) {
            throw new \RuntimeException('Erreur lors de la mise à jour de l\'utilisateur : ' . $e->getMessage());
        }
    }

    public function updatePassword(int $userId, string $hashedPassword): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                'UPDATE user SET password = :password WHERE user_id = :id'
            );

            return $stmt->execute([
                ':password' => $hashedPassword,
                ':id'       => $userId,
            ]);

        } catch (\PDOException $e) {
            throw new \RuntimeException('Erreur lors de la mise à jour du mot de passe : ' . $e->getMessage());
        }
    }

    public function delete(int $userId): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                'DELETE FROM user WHERE user_id = :id'
            );

            return $stmt->execute([':id' => $userId]);

        } catch (\PDOException $e) {
            throw new \RuntimeException('Erreur lors de la suppression de l\'utilisateur : ' . $e->getMessage());
        }
    }

    public function emailExists(string $email, ?int $excludeUserId = null): bool
    {
        try {
            if ($excludeUserId !== null) {
                $stmt = $this->pdo->prepare(
                    'SELECT COUNT(*) FROM user WHERE email = :email AND user_id != :id'
                );
                $stmt->execute([':email' => $email, ':id' => $excludeUserId]);
            } else {
                $stmt = $this->pdo->prepare(
                    'SELECT COUNT(*) FROM user WHERE email = :email'
                );
                $stmt->execute([':email' => $email]);
            }

            return (int) $stmt->fetchColumn() > 0;

        } catch (\PDOException $e) {
            throw new \RuntimeException('Erreur lors de la vérification de l\'email : ' . $e->getMessage());
        }
    }

    // Hydratation

    private function hydrate(array $row): User
    {
        return new User(
            email:                $row['email'],
            password:             $row['password'],
            prenom:               $row['prenom'],
            nom:                  $row['nom'],
            nombreConvivesDefaut: (int) $row['nombre_convives_defaut'],
            allergies:            $row['allergies'],
            role:                 $row['role'],
            userId:               (int) $row['user_id'],
        );
    }
}
