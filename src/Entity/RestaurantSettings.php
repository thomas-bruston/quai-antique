<?php

namespace Entity;

class RestaurantSettings
{
    private ?int $settingsId;
    private string $heureOuverture;
    private string $heureFermeture;
    private int $maxConvives;

    public function __construct(
        string $heureOuverture,
        string $heureFermeture,
        int $maxConvives,
        ?int $settingsId = null
    ) {
        $this->heureOuverture = $heureOuverture;
        $this->heureFermeture = $heureFermeture;
        $this->maxConvives    = $maxConvives;
        $this->settingsId     = $settingsId;
    }

    public function getSettingsId(): ?int { return $this->settingsId; }
    public function getHeureOuverture(): string { return $this->heureOuverture; }
    public function getHeureFermeture(): string { return $this->heureFermeture; }
    public function getMaxConvives(): int { return $this->maxConvives; }

    public function setHeureOuverture(string $h): void { $this->heureOuverture = $h; }
    public function setHeureFermeture(string $h): void { $this->heureFermeture = $h; }
    public function setMaxConvives(int $max): void { $this->maxConvives = $max; }
}
