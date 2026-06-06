<?php

namespace App\Entity;

class Menu {
    public function __construct(
        private ?int $id,
        private string $titre,
        private string $description,
        private int $nombrePersonneMin,
        private float $prixBase,
        private int $stockDisponible,
        private ?string $conditionsParticulieres = null,
        private ?int $themeId = null,
        private ?int $regimeId = null
    ) {}

    public function getId(): ?int { return $this->id; }
    public function getTitre(): string { return $this->titre; }
    public function getDescription(): string { return $this->description; }
    public function getNombrePersonneMin(): int { return $this->nombrePersonneMin; }
    public function getPrixBase(): float { return $this->prixBase; }
    public function getStockDisponible(): int { return $this->stockDisponible; }
    public function getConditionsParticulieres(): ?string { return $this->conditionsParticulieres; }
    public function getThemeId(): ?int { return $this->themeId; }
    public function getRegimeId(): ?int { return $this->regimeId; }
}
