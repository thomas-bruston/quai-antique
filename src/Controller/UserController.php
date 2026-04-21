<?php

declare(strict_types=1);

namespace Controller;

use Core\Controller;
use Core\Session;
use Repository\UserRepository;
use Repository\ReservationRepository;
use Service\ReservationService;
use Service\AuthService;

/* Espace personnel */

class UserController extends Controller
{
    private UserRepository        $userRepository;
    private ReservationRepository $reservationRepository;
    private ReservationService    $reservationService;

    public function __construct()
    {
        $this->userRepository        = new UserRepository();
        $this->reservationRepository = new ReservationRepository();
        $this->reservationService    = new ReservationService(
            $this->reservationRepository,
            new \Repository\RestaurantSettingsRepository()
        );
    }


    // Réservations

    public function reservations(): void
    {
        $reservations = $this->reservationRepository->findByUser(Session::getUserId());

        $this->render('user/reservations', [
            'reservations' => $reservations,
            'success'      => Session::getFlash('success'),
            'errors'       => Session::getFlash('errors') ?? [],
        ]);
    }

    public function editReservation(string $id): void
{
    $reservation = $this->reservationRepository->findById((int) $id);

    if (!$reservation || $reservation->getUserId() !== Session::getUserId()) {
        $this->redirect('/mon-compte/reservations');
    }

    $this->render('reservation/form', [
        'csrf_token'    => Session::generateCsrfToken(),
        'reservation'   => $reservation,
        'userAllergies' => $reservation->getAllergies(),
        'errors'        => Session::getFlash('errors') ?? [],
    ]);
}

    public function updateReservation(string $id): void
    {
        $this->verifyCsrf();

        $reservation = $this->reservationRepository->findById((int) $id);

        if (!$reservation || $reservation->getUserId() !== Session::getUserId()) {
            $this->redirect('/mon-compte/reservations');
        }

        $date          = trim($this->post('date'));
        $heure         = trim($this->post('heure'));
        $nbConvives    = (int) $this->post('nombre_convives');
        $allergies     = trim($this->post('allergies')) ?: null;

        $errors = $this->reservationService->validate(
            $date, $heure, $nbConvives,
            $reservation->getReservationId()
        );

        if (!empty($errors)) {
            Session::setFlash('errors', $errors);
            $this->redirect('/mon-compte/reservation/' . $id . '/modifier');
        }

        try {
            $reservation->setDate($date);
            $reservation->setHeure($heure);
            $reservation->setNombreConvives($nbConvives);
            $reservation->setAllergies($allergies);
            $this->reservationRepository->update($reservation);

            Session::setFlash('success', 'Réservation modifiée avec succès.');
            $this->redirect('/mon-compte/reservations');

        } catch (\RuntimeException $e) {
            Session::setFlash('errors', [$e->getMessage()]);
            $this->redirect('/mon-compte/reservation/' . $id . '/modifier');
        }
    }

    public function deleteReservation(string $id): void
    {
        $this->verifyCsrf();

        $reservation = $this->reservationRepository->findById((int) $id);

        if (!$reservation || $reservation->getUserId() !== Session::getUserId()) {
            $this->redirect('/mon-compte/reservations');
        }

        try {
            $this->reservationRepository->delete((int) $id);
            Session::setFlash('success', 'Réservation supprimée.');
        } catch (\RuntimeException $e) {
            Session::setFlash('errors', [$e->getMessage()]);
        }

        $this->redirect('/mon-compte/reservations');
    }

    // Profil

    public function profile(): void
    {
        $user = $this->userRepository->findById(Session::getUserId());

        $this->render('user/profile', [
            'csrf_token' => Session::generateCsrfToken(),
            'user'       => $user,
            'errors'     => Session::getFlash('errors') ?? [],
            'success'    => Session::getFlash('success'),
        ]);
    }

    public function updateProfile(): void
    {
        $this->verifyCsrf();

        $user = $this->userRepository->findById(Session::getUserId());

        $nom                   = trim($this->post('nom'));
        $prenom                = trim($this->post('prenom'));
        $email                 = trim($this->post('email'));
        $allergies             = trim($this->post('allergies')) ?: null;
        $nombreConvivesDefaut  = (int) $this->post('nombre_convives_defaut');
        $password              = $this->post('password');
        $passwordConfirm       = $this->post('password_confirm');

        $errors = [];

        if (empty($nom) || empty($prenom)) {
            $errors[] = 'Nom et prénom sont obligatoires.';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Adresse email invalide.';
        }

        if ($this->userRepository->emailExists($email, Session::getUserId())) {
            $errors[] = 'Cette adresse email est déjà utilisée.';
        }

        // Changement de mot de passe optionnel
        if (!empty($password)) {
            $authService = new AuthService();
            $passwordErrors = $authService->validatePassword($password, $passwordConfirm);
            $errors = array_merge($errors, $passwordErrors);
        }

        if (!empty($errors)) {
            Session::setFlash('errors', $errors);
            $this->redirect('/mon-compte/profil');
        }

        try {
            $user->setNom($nom);
            $user->setPrenom($prenom);
            $user->setEmail($email);
            $user->setAllergies($allergies);
            $user->setNombreConvivesDefaut($nombreConvivesDefaut);
            $this->userRepository->update($user);

            if (!empty($password)) {
                $this->userRepository->updatePassword(
                    Session::getUserId(),
                    password_hash($password, PASSWORD_BCRYPT)
                );
            }

            // Mettre à jour la session
            Session::set('user', [
                'id'     => $user->getUserId(),
                'prenom' => $user->getPrenom(),
                'nom'    => $user->getNom(),
                'email'  => $user->getEmail(),
                'role'   => $user->getRole(),
            ]);

            Session::setFlash('success', 'Profil mis à jour avec succès.');
            $this->redirect('/mon-compte/profil');

        } catch (\RuntimeException $e) {
            Session::setFlash('errors', [$e->getMessage()]);
            $this->redirect('/mon-compte/profil');
        }
    }

    // Suppression du compte

    public function deleteAccount(): void
    {
        $this->verifyCsrf();

        try {
            $this->userRepository->delete(Session::getUserId());
            (new AuthService())->logout();
            $this->redirect('/');

        } catch (\RuntimeException $e) {
            Session::setFlash('errors', [$e->getMessage()]);
            $this->redirect('/mon-compte/profil');
        }
    }
}
