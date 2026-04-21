<?php

namespace Entity;

class Dish
{
    private ?int $dishId;
    private int $categoryId;
    private string $titre;
    private string $description;
    private float $prix;

    public function __construct(
        int $categoryId,
        string $titre,
        string $description,
        float $prix,
        ?int $dishId = null
    ) {
        $this->categoryId  = $categoryId;
        $this->titre       = $titre;
        $this->description = $description;
        $this->prix        = $prix;
        $this->dishId      = $dishId;
    }

    // Getters 

    public function getDishId(): ?int
    {
        return $this->dishId;
    }

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPrix(): float
    {
        return $this->prix;
    }

    // Setters

    public function setCategoryId(int $categoryId): void
    {
        $this->categoryId = $categoryId;
    }

    public function setTitre(string $titre): void
    {
        $this->titre = $titre;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setPrix(float $prix): void
    {
        $this->prix = $prix;
    }

}
