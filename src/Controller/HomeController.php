<?php

namespace App\Controller;

use App\Repository\MenuRepository;
use App\Repository\AvisRepository;

class HomeController {
    public function __construct(
        private MenuRepository $menuRepository,
        private AvisRepository $avisRepository
    ) {}

    public function index(): void {
        $menus = $this->menuRepository->findRandomAvailable(3);
        $avis = $this->avisRepository->findLatestValidated(3);

        $title = 'Accueil';
        $description = 'Vite & Gourmand, traiteur bordelais depuis 1999 — Menus faits maison livrés à Bordeaux.';

        require __DIR__ . '/../../views/home.php';
    }
}
