<?php

namespace App\Controller;

use App\Repository\MenuRepository;

class MenuController {
    public function __construct(
        private MenuRepository $menuRepository
    ) {}

    /**
     * Affiche la page de la liste des menus
     */
    public function index(): void {
        $title = 'Nos menus';
        $description = 'Découvrez tous les menus traiteur de Vite & Gourmand — filtrez par thème, régime, prix et nombre de personnes.';
        
        // On n'a pas besoin de charger les menus ici car le JS s'en charge via l'API
        require __DIR__ . '/../../views/menus.php';
    }

    /**
     * Action API pour retourner la liste des menus filtrée (JSON)
     */
    public function apiList(): void {
        header('Content-Type: application/json');

        $filters = [
            'prix_max'  => $_GET['prix_max'] ?? null,
            'prix_min'  => $_GET['prix_min'] ?? null,
            'theme'     => $_GET['theme'] ?? null,
            'regime'    => $_GET['regime'] ?? null,
            'personnes' => $_GET['personnes'] ?? null
        ];

        $menus = $this->menuRepository->findByFilters($filters);
        echo json_encode($menus);
        exit;
    }
}
