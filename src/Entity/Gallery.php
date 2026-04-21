<?php

namespace Entity;

class Gallery
{
    private ?int $galleryId;
    private string $titre;
    private ?string $photo; 
    private ?string $createdAt;

    public function __construct(
        string $titre,
        ?string $photo = null,
        ?int $galleryId = null,
        ?string $createdAt = null
    ) {
        $this->titre     = $titre;
        $this->photo     = $photo;
        $this->galleryId = $galleryId;
        $this->createdAt = $createdAt;
    }

    // Getters

    public function getGalleryId(): ?int
    {
        return $this->galleryId;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    // Setters

    public function setTitre(string $titre): void
    {
        $this->titre = $titre;
    }

    public function setPhoto(?string $photo): void
    {
        $this->photo = $photo;
    }

    // Helpers

    public function getPhotoPath(): string
    {
        return '/images/captions/' . $this->photo;
    }

}
