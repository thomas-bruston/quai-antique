<?php

namespace Entity;

class Reservation
{
    private ?int $reservationId;
    private int $userId;
    private string $date;
    private string $heure;
    private int $nombreConvives;
    private ?string $allergies;
    private ?string $createdAt;

    public function __construct(
        int $userId,
        string $date,
        string $heure,
        int $nombreConvives,
        ?string $allergies = null,
        ?int $reservationId = null,
        ?string $createdAt = null
    ) {
        $this->userId         = $userId;
        $this->date           = $date;
        $this->heure          = $heure;
        $this->nombreConvives = $nombreConvives;
        $this->allergies      = $allergies;
        $this->reservationId  = $reservationId;
        $this->createdAt      = $createdAt;
    }

    // Getters

    public function getReservationId(): ?int
    {
        return $this->reservationId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getHeure(): string
    {
        return $this->heure;
    }

    public function getNombreConvives(): int
    {
        return $this->nombreConvives;
    }

    public function getAllergies(): ?string
    {
        return $this->allergies;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    // Setters 

    public function setDate(string $date): void
    {
        $this->date = $date;
    }

    public function setHeure(string $heure): void
    {
        $this->heure = $heure;
    }

    public function setNombreConvives(int $nombreConvives): void
    {
        $this->nombreConvives = $nombreConvives;
    }

    public function setAllergies(?string $allergies): void
    {
        $this->allergies = $allergies;
    }

    // Helpers

    public function getService(): string
    {
        $heure = (int) substr($this->heure, 0, 2);
        return $heure < 17 ? 'midi' : 'soir';
    }

    public function getDateFormatted(): string
    {
        return date('d/m/Y', strtotime($this->date));
    }

    public function getHeureFormatted(): string
    {
        return substr($this->heure, 0, 5);
    }
}
