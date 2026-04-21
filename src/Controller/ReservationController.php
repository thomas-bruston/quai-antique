<?php
declare(strict_types=1);
namespace Controller;
use Core\Controller;
use Core\Session;
use Entity\Reservation;
use Repository\ReservationRepository;
use Repository\RestaurantSettingsRepository;
use Service\ReservationService;

class ReservationController extends Controller
{
    private ReservationRepository        $reservationRepository;
    private RestaurantSettingsRepository $settingsRepository;
    private ReservationService           $reservationService;

    public function __construct()
    {
        $this->reservationRepository = new ReservationRepository();
        $this->settingsRepository    = new RestaurantSettingsRepository();
        $this->reservationService    = new ReservationService(
            $this->reservationRepository,
            $this->settingsRepository
        );
    }

    public function form(): void
    {
        if (!Session::isLoggedIn()) {
            Session::set('redirect_after_login', '/reservation');
            $this->redirect('/connexion');
        }
        $user = (new \Repository\UserRepository())->findById(Session::getUserId());
        $this->render('reservation/form', [
            'csrf_token'    => Session::generateCsrfToken(),
            'userAllergies' => $user?->getAllergies(),
            'errors'        => Session::getFlash('errors') ?? [],
        ]);
    }

    public function store(): void
    {
        if (!Session::isLoggedIn()) {
            $this->redirect('/connexion');
        }
        $this->verifyCsrf();
        $date       = trim($this->post('date'));
        $heure      = trim($this->post('heure'));
        $nbConvives = (int) $this->post('nombre_convives');
        $allergies  = trim($this->post('allergies')) ?: null;

        $errors = $this->reservationService->validate($date, $heure, $nbConvives);
        if (!empty($errors)) {
            Session::setFlash('errors', $errors);
            $this->redirect('/reservation');
        }

        try {
            $reservation = new Reservation(
                userId:         Session::getUserId(),
                date:           $date,
                heure:          $heure,
                nombreConvives: $nbConvives,
                allergies:      $allergies
            );
            $this->reservationRepository->create($reservation);
            Session::setFlash('success', 'Votre réservation a bien été enregistrée.');
            $this->redirect('/mon-compte/reservations');
        } catch (\RuntimeException $e) {
            Session::setFlash('errors', [$e->getMessage()]);
            $this->redirect('/reservation');
        }
    }

    public function checkAvailability(): void
    {
        header('Content-Type: application/json');
        $date = trim($this->get('date') ?? '');

        if (empty($date)) {
            echo json_encode(['error' => 'Date manquante.']);
            exit;
        }
        if (!$this->reservationService->isValidDay($date)) {
            echo json_encode(['error' => 'Le restaurant est fermé le lundi.']);
            exit;
        }
        try {
            $heures = $this->reservationService->getAvailableSlots($date);
            $slots  = [];
            foreach ($heures as $heure) {
                $slots[] = [
                    'heure'     => $heure,
                    'available' => $this->reservationService->isAvailable($date, 1),
                ];
            }
            echo json_encode(['slots' => $slots]);
        } catch (\RuntimeException $e) {
            echo json_encode(['error' => 'Erreur lors du chargement des créneaux.']);
        }
        exit;
    }
}