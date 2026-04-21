<?php

namespace Repository;

use Entity\RestaurantSettings;

class RestaurantSettingsRepository extends AbstractRepository
{
    public function findSettings(): ?RestaurantSettings
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM restaurant_settings LIMIT 1');
            $stmt->execute();
            $row = $stmt->fetch();
            return $row ? $this->hydrate($row) : null;
        } catch (\PDOException $e) {
            throw new \RuntimeException('Erreur paramètres : ' . $e->getMessage());
        }
    }

    public function update(RestaurantSettings $settings): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                'UPDATE restaurant_settings
                 SET heure_ouverture = :heure_ouverture,
                     heure_fermeture = :heure_fermeture,
                     max_convives    = :max_convives
                 WHERE settings_id = :id'
            );
            return $stmt->execute([
                ':heure_ouverture' => $settings->getHeureOuverture(),
                ':heure_fermeture' => $settings->getHeureFermeture(),
                ':max_convives'    => $settings->getMaxConvives(),
                ':id'              => $settings->getSettingsId(),
            ]);
        } catch (\PDOException $e) {
            throw new \RuntimeException('Erreur mise à jour paramètres : ' . $e->getMessage());
        }
    }

    private function hydrate(array $row): RestaurantSettings
    {
        return new RestaurantSettings(
            heureOuverture: $row['heure_ouverture'],
            heureFermeture: $row['heure_fermeture'],
            maxConvives:    (int) $row['max_convives'],
            settingsId:     (int) $row['settings_id']
        );
    }
}
