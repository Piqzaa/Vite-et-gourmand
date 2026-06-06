<?php

namespace App\Entity;

class User {
    public function __construct(
        private ?int $id,
        private string $nom,
        private string $prenom,
        private string $email,
        private string $role = 'utilisateur',
        private string $gsm = '',
        private string $adressePostale = '',
        private string $ville = '',
        private bool $actif = true
    ) {}

    public function getId(): ?int { return $this->id; }
    public function getNom(): string { return $this->nom; }
    public function getPrenom(): string { return $this->prenom; }
    public function getEmail(): string { return $this->email; }
    public function getRole(): string { return $this->role; }
    public function getGsm(): string { return $this->gsm; }
    public function getAdressePostale(): string { return $this->adressePostale; }
    public function getVille(): string { return $this->ville; }
    public function isActif(): bool { return $this->actif; }

    public function setId(int $id): self { $this->id = $id; return $this; }
    public function setRole(string $role): self { $this->role = $role; return $this; }
}
