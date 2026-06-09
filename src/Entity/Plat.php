<?php

namespace App\Entity;

class Plat {
    public function __construct(
        private ?int $id,
        private string $libelle,
        private string $type,
        private ?string $imagePath = null,
        private array $allergenes = []
    ) {}

    public function getId(): ?int { return $this->id; }
    public function getLibelle(): string { return $this->libelle; }
    public function getType(): string { return $this->type; }
    public function getImagePath(): ?string { return $this->imagePath; }
    public function getAllergenes(): array { return $this->allergenes; }
}
