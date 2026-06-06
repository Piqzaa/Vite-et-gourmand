<?php

namespace App\Entity;

use DateTime;

class Commande {
    public function __construct(
        private ?int $id,
        private DateTime $dateCommande,
        private DateTime $datePrestation,
        private string $heurePrestation,
        private string $adresseLivraison,
        private bool $estHorsBordeaux,
        private int $nombrePersonnes,
        private float $prixTotalTtc,
        private string $statut = 'en attente',
        private ?string $motifAnnulation = null,
        private bool $pretMateriel = false,
        private bool $materielRendu = false,
        private ?int $utilisateurId = null,
        private ?int $menuId = null
    ) {}

    public function getId(): ?int { return $this->id; }
    public function getDateCommande(): DateTime { return $this->dateCommande; }
    public function getDatePrestation(): DateTime { return $this->datePrestation; }
    public function getHeurePrestation(): string { return $this->heurePrestation; }
    public function getAdresseLivraison(): string { return $this->adresseLivraison; }
    public function isEstHorsBordeaux(): bool { return $this->estHorsBordeaux; }
    public function getNombrePersonnes(): int { return $this->nombrePersonnes; }
    public function getPrixTotalTtc(): float { return $this->prixTotalTtc; }
    public function getStatut(): string { return $this->statut; }
    public function getMotifAnnulation(): ?string { return $this->motifAnnulation; }
    public function isPretMateriel(): bool { return $this->pretMateriel; }
    public function isMaterielRendu(): bool { return $this->materielRendu; }
    public function getUtilisateurId(): ?int { return $this->utilisateurId; }
    public function getMenuId(): ?int { return $this->menuId; }
}
