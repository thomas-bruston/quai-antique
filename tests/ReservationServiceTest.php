<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Service\ReservationService;
use Repository\ReservationRepository;
use Repository\RestaurantSettingsRepository;
use Entity\RestaurantSettings;

/**
 * Tests unitaires — ReservationService
 * CP7 : style défensif, tests unitaires
 */
class ReservationServiceTest extends TestCase
{
    private ReservationService $service;

    protected function setUp(): void
    {
        // On crée des mocks pour éviter les appels BDD
        $reservationRepo = $this->createMock(ReservationRepository::class);
        $settingsRepo    = $this->createMock(RestaurantSettingsRepository::class);

        // Simuler les paramètres restaurant (19h00 - 21h00)
        $settings = new RestaurantSettings('19:00:00', '21:00:00', 50);
        $settingsRepo->method('findSettings')->willReturn($settings);

        // Simuler 0 convives déjà réservés
        $reservationRepo->method('countConvivesBySlot')->willReturn(0);

        $this->service = new ReservationService($reservationRepo, $settingsRepo);
    }

    // ----------------------------------------------------------------
    // Tests isValidDay()
    // ----------------------------------------------------------------

    public function testLundiEstFerme(): void
    {
        // Trouver le prochain lundi
        $lundi = date('Y-m-d', strtotime('next monday'));
        $this->assertFalse($this->service->isValidDay($lundi));
    }

    public function testMardiEstOuvert(): void
    {
        $mardi = date('Y-m-d', strtotime('next tuesday'));
        $this->assertTrue($this->service->isValidDay($mardi));
    }

    public function testMercrediEstOuvert(): void
    {
        $mercredi = date('Y-m-d', strtotime('next wednesday'));
        $this->assertTrue($this->service->isValidDay($mercredi));
    }

    public function testJeudiEstOuvert(): void
    {
        $jeudi = date('Y-m-d', strtotime('next thursday'));
        $this->assertTrue($this->service->isValidDay($jeudi));
    }

    public function testVendrediEstOuvert(): void
    {
        $vendredi = date('Y-m-d', strtotime('next friday'));
        $this->assertTrue($this->service->isValidDay($vendredi));
    }

    public function testSamediEstOuvert(): void
    {
        $samedi = date('Y-m-d', strtotime('next saturday'));
        $this->assertTrue($this->service->isValidDay($samedi));
    }

    public function testDimancheEstOuvert(): void
    {
        $dimanche = date('Y-m-d', strtotime('next sunday'));
        $this->assertTrue($this->service->isValidDay($dimanche));
    }

    // ----------------------------------------------------------------
    // Tests generateSlots()
    // ----------------------------------------------------------------

    public function testGenerateSlotsRetourneLeBonNombreDeCréneaux(): void
    {
        // 19h00 à 21h00 par tranche de 15 min = 9 créneaux
        $slots = $this->service->generateSlots('19:00:00', '21:00:00');
        $this->assertCount(9, $slots);
    }

    public function testGenerateSlotsCommenceALHeureDOuverture(): void
    {
        $slots = $this->service->generateSlots('19:00:00', '21:00:00');
        $this->assertEquals('19:00', $slots[0]);
    }

    public function testGenerateSlotsTermineALHeureDeFermeture(): void
    {
        $slots = $this->service->generateSlots('19:00:00', '21:00:00');
        $this->assertEquals('21:00', end($slots));
    }

    public function testGenerateSlotsEspacement15Minutes(): void
    {
        $slots = $this->service->generateSlots('19:00:00', '21:00:00');
        $this->assertEquals('19:15', $slots[1]);
        $this->assertEquals('19:30', $slots[2]);
        $this->assertEquals('19:45', $slots[3]);
    }

    // ----------------------------------------------------------------
    // Tests isAvailable()
    // ----------------------------------------------------------------

    public function testDisponibleSiConvivesInferieurAuMax(): void
    {
        $date = date('Y-m-d', strtotime('next tuesday'));
        $this->assertTrue($this->service->isAvailable($date, 4));
    }

    public function testDisponibleSiConvivesEgalAuMax(): void
    {
        $date = date('Y-m-d', strtotime('next tuesday'));
        $this->assertTrue($this->service->isAvailable($date, 50));
    }

    public function testIndisponibleSiConvivesSuperieurAuMax(): void
    {
        // Mock avec 48 convives déjà réservés
        $reservationRepo = $this->createMock(ReservationRepository::class);
        $settingsRepo    = $this->createMock(RestaurantSettingsRepository::class);

        $settings = new RestaurantSettings('19:00:00', '21:00:00', 50);
        $settingsRepo->method('findSettings')->willReturn($settings);
        $reservationRepo->method('countConvivesBySlot')->willReturn(48);

        $service = new ReservationService($reservationRepo, $settingsRepo);
        $date    = date('Y-m-d', strtotime('next tuesday'));

        $this->assertFalse($service->isAvailable($date, 5));
    }

    // ----------------------------------------------------------------
    // Tests validate()
    // ----------------------------------------------------------------

    public function testValidationEchoueSiJourFerme(): void
    {
        $lundi = date('Y-m-d', strtotime('next monday'));
        $errors = $this->service->validate($lundi, '19:00', 2);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('lundi', $errors[0]);
    }

    public function testValidationEchoueSiNbConvivesInvalide(): void
    {
        $mardi  = date('Y-m-d', strtotime('next tuesday'));
        $errors = $this->service->validate($mardi, '19:00', 0);
        $this->assertNotEmpty($errors);
    }

    public function testValidationEchoueSiCreneauInvalide(): void
    {
        $mardi  = date('Y-m-d', strtotime('next tuesday'));
        $errors = $this->service->validate($mardi, '14:00', 2);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('créneau', $errors[0]);
    }

    public function testValidationReussitAvecDonneesValides(): void
    {
        $mardi  = date('Y-m-d', strtotime('next tuesday'));
        $errors = $this->service->validate($mardi, '19:00', 2);
        $this->assertEmpty($errors);
    }
}
