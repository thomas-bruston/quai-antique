<?php

namespace Entity;

class User
{
    private ?int $userId;
    private string $email;
    private string $password;
    private string $prenom;
    private string $nom;
    private int $nombreConvivesDefaut;
    private ?string $allergies;
    private string $role;
    private ?string $createdAt;

    public function __construct(
        string $email,
        string $password,
        string $prenom,
        string $nom,
        int $nombreConvivesDefaut = 1,
        ?string $allergies = null,
        string $role = 'client',
        ?int $userId = null,
        ?string $createdAt = null
    ) {
        $this->email                = $email;
        $this->password             = $password;
        $this->prenom               = $prenom;
        $this->nom                  = $nom;
        $this->nombreConvivesDefaut = $nombreConvivesDefaut;
        $this->allergies            = $allergies;
        $this->role                 = $role;
        $this->userId               = $userId;
        $this->createdAt            = $createdAt;
    }

    // Getters

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getNombreConvivesDefaut(): int
    {
        return $this->nombreConvivesDefaut;
    }

    public function getAllergies(): ?string
    {
        return $this->allergies;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    // Setters

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function setPrenom(string $prenom): void
    {
        $this->prenom = $prenom;
    }

    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function setNombreConvivesDefaut(int $nombreConvivesDefaut): void
    {
        $this->nombreConvivesDefaut = $nombreConvivesDefaut;
    }

    public function setAllergies(?string $allergies): void
    {
        $this->allergies = $allergies;
    }

    public function setRole(string $role): void
    {
        $this->role = $role;
    }

    // Helpers

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    public function getFullName(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }
}
