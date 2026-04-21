<?php
namespace Service;
use Repository\ReservationRepository;
use Repository\RestaurantSettingsRepository;

class ReservationService
{
    private ReservationRepository        $reservationRepository;
    private RestaurantSettingsRepository $settingsRepository;

    public function __construct(
        ReservationRepository $reservationRepository,
        RestaurantSettingsRepository $settingsRepository
    ) {
        $this->reservationRepository = $reservationRepository;
        $this->settingsRepository    = $settingsRepository;
    }

    /* Vérif date mardi–dimanche */
    public function isValidDay(string $date): bool
    {
        $dayOfWeek = (int) date('w', strtotime($date));
        return $dayOfWeek !== 1;
    }

    /* Créneaux 15 minutes */
    public function generateSlots(string $heureOuverture, string $heureFermeture): array
    {
        $slots   = [];
        $current = strtotime($heureOuverture);
        $end     = strtotime($heureFermeture);
        while ($current <= $end) {
            $slots[] = date('H:i', $current);
            $current += 15 * 60;
        }
        return $slots;
    }

    /* Vérif disponibilité */
    public function isAvailable(string $date, int $nbConvives, ?int $excludeReservationId = null): bool
    {
        $settings = $this->settingsRepository->findSettings();
        if ($settings === null) {
            throw new \RuntimeException('Paramètres du restaurant introuvables.');
        }
        $totalConvives = $this->reservationRepository->countConvivesBySlot(
            $date,
            $settings->getHeureOuverture(),
            $settings->getHeureFermeture(),
            $excludeReservationId
        );
        return ($totalConvives + $nbConvives) <= $settings->getMaxConvives();
    }

    /* Retourne les créneaux */
    public function getAvailableSlots(string $date): array
    {
        $settings = $this->settingsRepository->findSettings();
        if ($settings === null) {
            throw new \RuntimeException('Paramètres du restaurant introuvables.');
        }
        return $this->generateSlots(
            $settings->getHeureOuverture(),
            $settings->getHeureFermeture()
        );
    }

    /* Validation réservation */
    public function validate(string $date, string $heure, int $nbConvives, ?int $excludeReservationId = null): array
    {
        $errors = [];

        if (!$this->isValidDay($date)) {
            $errors[] = 'Le restaurant est fermé le lundi.';
        }
        if ($nbConvives < 1) {
            $errors[] = 'Le nombre de convives doit être d\'au moins 1.';
        }
        $slots = $this->getAvailableSlots($date);
        if (!in_array($heure, $slots, true)) {
            $errors[] = 'Le créneau horaire sélectionné est invalide.';
        }
        if (empty($errors)) {
            if (!$this->isAvailable($date, $nbConvives, $excludeReservationId)) {
                $errors[] = 'Le restaurant est complet. Veuillez choisir une autre date.';
            }
        }
        return $errors;
    }
}