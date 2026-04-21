<?php

namespace Entity;

class Menu
{
    private ?int $menuId;
    private string $titre;
    private int $nombreSequences;
    private int $prix;
    private array $dishes;

    public function __construct(
        string $titre,
        int $nombreSequences,
        int $prix,
        ?int $menuId = null,
        array $dishes = []
    ) {
        $this->titre           = $titre;
        $this->nombreSequences = $nombreSequences;
        $this->prix            = $prix;
        $this->menuId          = $menuId;
        $this->dishes          = $dishes;
    }

    public function getMenuId(): ?int { return $this->menuId; }
    public function getTitre(): string { return $this->titre; }
    public function getNombreSequences(): int { return $this->nombreSequences; }
    public function getPrix(): int { return $this->prix; }
    public function getDishes(): array { return $this->dishes; }

    public function setTitre(string $titre): void { $this->titre = $titre; }
    public function setNombreSequences(int $n): void { $this->nombreSequences = $n; }
    public function setPrix(int $prix): void { $this->prix = $prix; }
    public function setDishes(array $dishes): void { $this->dishes = $dishes; }
}
