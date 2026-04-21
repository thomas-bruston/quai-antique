<?php

namespace Entity;

class DishCategory
{
    private ?int $categoryId;
    private string $titre;

    public function __construct(
        string $titre,
        ?int $categoryId = null
    ) {
        $this->titre      = $titre;
        $this->categoryId = $categoryId;
    }

    // Getters

    public function getCategoryId(): ?int
    {
        return $this->categoryId;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }

    // Setters

    public function setTitre(string $titre): void
    {
        $this->titre = $titre;
    }
}
