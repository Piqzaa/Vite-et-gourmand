<?php
namespace App\Controller;

use App\Repository\CommandeRepository;
use App\Service\AuthService;

class StatsController {
    public function __construct(
        private CommandeRepository $commandeRepo,
        private AuthService $authService
    ) {}

    public function api(): void {
        if (!$this->authService->isEmploye()) {
            header('HTTP/1.0 403 Forbidden');
            exit;
        }

        header('Content-Type: application/json');
        
        $filters = [
            'menu_id'    => $_GET['menu_id'] ?? null,
            'date_debut' => $_GET['date_debut'] ?? null,
            'date_fin'   => $_GET['date_fin'] ?? null
        ];

        $totals = $this->commandeRepo->getGlobalStats($filters);
        $chartRaw = $this->commandeRepo->getStatsByMenu($filters);

        $response = [
            'totals' => [
                'ca'  => (float)$totals['total_ttc'],
                'nb'  => (int)$totals['nombre_commandes'],
                'moy' => (float)$totals['panier_moyen']
            ],
            'chart' => [
                'labels'    => array_column($chartRaw, 'titre'),
                'commandes' => array_map('intval', array_column($chartRaw, 'nombre_commandes')),
                'ca'        => array_map('floatval', array_column($chartRaw, 'ca'))
            ]
        ];

        echo json_encode($response);
        exit;
    }
}
