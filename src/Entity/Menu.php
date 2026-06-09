<?php

namespace App\Entity;

class Menu implements \JsonSerializable {
    public function __construct(
        private ?int $id,
        private string $titre,
        private string $description,
        private int $nombrePersonneMin,
        private float $prixBase,
        private int $stockDisponible,
        private ?string $conditionsParticulieres = null,
        private ?int $themeId = null,
        private ?int $regimeId = null,
        private ?string $themeLabel = null,
        private ?string $regimeLabel = null,
        private ?string $imagePath = null
    ) {}

    public function jsonSerialize(): mixed {
        return [
            'menu_id' => $this->id,
            'titre' => $this->titre,
            'description' => $this->description,
            'nombre_personne_min' => $this->nombrePersonneMin,
            'prix_base' => $this->prixBase,
            'stock_disponible' => $this->stockDisponible,
            'conditions_particulieres' => $this->conditionsParticulieres,
            'theme_id' => $this->themeId,
            'regime_id' => $this->regimeId,
            'theme' => $this->themeLabel,
            'regime' => $this->regimeLabel,
            'image_path' => $this->imagePath
        ];
    }

    public function getId(): ?int { return $this->id; }
    public function getTitre(): string { return $this->titre; }
    public function getDescription(): string { return $this->description; }
    public function getNombrePersonneMin(): int { return $this->nombrePersonneMin; }
    public function getPrixBase(): float { return $this->prixBase; }
    public function getStockDisponible(): int { return $this->stockDisponible; }
    public function getConditionsParticulieres(): ?string { return $this->conditionsParticulieres; }
    public function getThemeId(): ?int { return $this->themeId; }
    public function getRegimeId(): ?int { return $this->regimeId; }
    public function getThemeLabel(): ?string { return $this->themeLabel; }
    public function getRegimeLabel(): ?string { return $this->regimeLabel; }
    public function getImagePath(): ?string { return $this->imagePath; }
}
