<?php

namespace Repository;

use Entity\Reservation;

class ReservationRepository extends AbstractRepository
{

    public function findById(int $id): ?Reservation
    {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT * FROM reservation WHERE reservation_id = :id LIMIT 1'
            );
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch();

            return $row ? $this->hydrate($row) : null;

        } catch (\PDOException $e) {
            throw new \RuntimeException('Erreur lors de la recherche de la réservation : ' . $e->getMessage());
        }
    }

    /* Réservations par date décroissante */

    public function findByUser(int $userId): array
    {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT * FROM reservation
                 WHERE user_id = :user_id
                 ORDER BY date DESC, heure DESC'
            );
            $stmt->execute([':user_id' => $userId]);

            return array_map([$this, 'hydrate'], $stmt->fetchAll());

        } catch (\PDOException $e) {
            throw new \RuntimeException('Erreur lors de la récupération des réservations : ' . $e->getMessage());
        }
    }

    /* Toutes les réservations du jour - admin */

    public function findByDate(string $date): array
    {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT r.*, u.prenom, u.nom, u.email
                 FROM reservation r
                 JOIN user u ON r.user_id = u.user_id
                 WHERE r.date = :date
                 ORDER BY r.heure ASC'
            );
            $stmt->execute([':date' => $date]);

            return $stmt->fetchAll();

        } catch (\PDOException $e) {
            throw new \RuntimeException('Erreur lors de la récupération des réservations par date : ' . $e->getMessage());
        }
    }

    /* Vérif la disponibilité */

    public function countConvivesBySlot(string $date, string $heureOuverture, string $heureFermeture, ?int $excludeReservationId = null): int
    {
        try {
            if ($excludeReservationId !== null) {
                $stmt = $this->pdo->prepare(
                    'SELECT COALESCE(SUM(nombre_convives), 0)
                     FROM reservation
                     WHERE date = :date
                       AND heure >= :heure_ouverture
                       AND heure <= :heure_fermeture
                       AND reservation_id != :exclude_id'
                );
                $stmt->execute([
                    ':date'            => $date,
                    ':heure_ouverture' => $heureOuverture,
                    ':heure_fermeture' => $heureFermeture,
                    ':exclude_id'      => $excludeReservationId,
                ]);
            } else {
                $stmt = $this->pdo->prepare(
                    'SELECT COALESCE(SUM(nombre_convives), 0)
                     FROM reservation
                     WHERE date = :date
                       AND heure >= :heure_ouverture
                       AND heure <= :heure_fermeture'
                );
                $stmt->execute([
                    ':date'            => $date,
                    ':heure_ouverture' => $heureOuverture,
                    ':heure_fermeture' => $heureFermeture,
                ]);
            }

            return (int) $stmt->fetchColumn();

        } catch (\PDOException $e) {
            throw new \RuntimeException('Erreur lors du comptage des convives : ' . $e->getMessage());
        }
    }

    public function create(Reservation $reservation): int
    {
        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO reservation (user_id, date, heure, nombre_convives, allergies)
                 VALUES (:user_id, :date, :heure, :nombre_convives, :allergies)'
            );
            $stmt->execute([
                ':user_id'         => $reservation->getUserId(),
                ':date'            => $reservation->getDate(),
                ':heure'           => $reservation->getHeure(),
                ':nombre_convives' => $reservation->getNombreConvives(),
                ':allergies'       => $reservation->getAllergies(),
            ]);

            return (int) $this->pdo->lastInsertId();

        } catch (\PDOException $e) {
            throw new \RuntimeException('Erreur lors de la création de la réservation : ' . $e->getMessage());
        }
    }

    public function update(Reservation $reservation): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                'UPDATE reservation
                 SET date            = :date,
                     heure           = :heure,
                     nombre_convives = :nombre_convives,
                     allergies       = :allergies
                 WHERE reservation_id = :id'
            );

            return $stmt->execute([
                ':date'            => $reservation->getDate(),
                ':heure'           => $reservation->getHeure(),
                ':nombre_convives' => $reservation->getNombreConvives(),
                ':allergies'       => $reservation->getAllergies(),
                ':id'              => $reservation->getReservationId(),
            ]);

        } catch (\PDOException $e) {
            throw new \RuntimeException('Erreur lors de la mise à jour de la réservation : ' . $e->getMessage());
        }
    }

    public function delete(int $reservationId): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                'DELETE FROM reservation WHERE reservation_id = :id'
            );

            return $stmt->execute([':id' => $reservationId]);

        } catch (\PDOException $e) {
            throw new \RuntimeException('Erreur lors de la suppression de la réservation : ' . $e->getMessage());
        }
    }

    // Hydratation

    private function hydrate(array $row): Reservation
    {
        return new Reservation(
            userId:         (int) $row['user_id'],
            date:           $row['date'],
            heure:          $row['heure'],
            nombreConvives: (int) $row['nombre_convives'],
            allergies:      $row['allergies'],
            reservationId:  (int) $row['reservation_id']
            
        );
    }
}
