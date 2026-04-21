<?php

declare(strict_types=1);

namespace Controller;

use Core\Controller;
use Core\Session;
use Repository\ReservationRepository;
use Repository\RestaurantSettingsRepository;

/* Dashboard admin + paramètres + gestion des réservations */

class AdminController extends Controller
{
    private ReservationRepository        $reservationRepository;
    private RestaurantSettingsRepository $settingsRepository;
    

    public function __construct()
    {
        $this->reservationRepository = new ReservationRepository();
        $this->settingsRepository    = new RestaurantSettingsRepository();
        
    }

    // Dashboard
    
    public function dashboard(): void
    {
        $this->render('admin/dashboard', []);
    }

    // Paramètres

    public function settings(): void
    {
        $settings = $this->settingsRepository->findSettings();

        $this->render('admin/settings', [
            'settings'   => $settings,
            'csrf_token' => Session::generateCsrfToken(),
            'success'    => Session::getFlash('success'),
            'errors'     => Session::getFlash('errors') ?? [],
        ]);
    }

    public function updateSettings(): void
    {
        $this->verifyCsrf();

        $heureOuverture = trim($this->post('heure_ouverture'));
        $heureFermeture = trim($this->post('heure_fermeture'));
        $maxConvives    = (int) $this->post('max_convives');

        $errors = [];

        if ($maxConvives <= 0) {
            $errors[] = 'Le nombre maximum de convives doit être supérieur à 0.';
        }

        if (!$this->isValidTime($heureOuverture) || !$this->isValidTime($heureFermeture)) {
            $errors[] = 'Les horaires sont invalides.';
        }

        if (!empty($errors)) {
            Session::setFlash('errors', $errors);
            $this->redirect('/admin/parametres');
        }

        try {
            $settings = $this->settingsRepository->findSettings();
            $settings->setHeureOuverture($heureOuverture);
            $settings->setHeureFermeture($heureFermeture);
            $settings->setMaxConvives($maxConvives);
            $this->settingsRepository->update($settings);

            Session::setFlash('success', 'Paramètres mis à jour.');
        } catch (\RuntimeException $e) {
            Session::setFlash('errors', [$e->getMessage()]);
        }

        $this->redirect('/admin/parametres');
    }

    // Réservations 

    public function reservations(): void
    {
        $date = trim($this->get('date') ?? date('Y-m-d'));

        // Validation date

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $date = date('Y-m-d');
        }

        $reservations = $this->reservationRepository->findByDate($date);

        $this->render('admin/reservations', [
            'reservations' => $reservations,
            'date'         => $date,
            'csrf_token'   => Session::generateCsrfToken(),
            'success'      => Session::getFlash('success'),
            'errors'       => Session::getFlash('errors') ?? [],
        ]);
    }

    public function updateReservation(string $id): void
    {
        $this->verifyCsrf();

        $reservation = $this->reservationRepository->findById((int) $id);

        if (!$reservation) {
            $this->redirect('/admin/reservations');
        }

        $date       = trim($this->post('date'));
        $heure      = trim($this->post('heure'));
        $nbConvives = (int) $this->post('nombre_convives');
        $allergies  = trim($this->post('allergies')) ?: null;

        if (empty($date) || empty($heure) || $nbConvives <= 0) {
            Session::setFlash('errors', ['Tous les champs sont obligatoires.']);
            $this->redirect('/admin/reservations?date=' . $reservation->getDate());
        }

        try {
            $reservation->setDate($date);
            $reservation->setHeure($heure);
            $reservation->setNombreConvives($nbConvives);
            $reservation->setAllergies($allergies);
            $this->reservationRepository->update($reservation);

            Session::setFlash('success', 'Réservation modifiée.');
        } catch (\RuntimeException $e) {
            Session::setFlash('errors', [$e->getMessage()]);
        }

        $this->redirect('/admin/reservations?date=' . $date);
    }

    public function deleteReservation(string $id): void
    {
        $this->verifyCsrf();

        $reservation = $this->reservationRepository->findById((int) $id);
        $date        = $reservation?->getDate() ?? date('Y-m-d');

        try {
            $this->reservationRepository->delete((int) $id);
            Session::setFlash('success', 'Réservation supprimée.');
        } catch (\RuntimeException $e) {
            Session::setFlash('errors', [$e->getMessage()]);
        }

        $this->redirect('/admin/reservations?date=' . $date);
    }

    // Helpers

    private function isValidTime(string $time): bool
    {
        return (bool) preg_match('/^\d{2}:\d{2}$/', $time);
    }
}
