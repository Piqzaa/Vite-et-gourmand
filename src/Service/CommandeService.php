<?php

namespace App\Service;

use App\Repository\CommandeRepository;
use App\Repository\MenuRepository;
use DateTime;
use Exception;

class CommandeService {
    public function __construct(
        private CommandeRepository $commandeRepository,
        private MenuRepository $menuRepository,
        private MailService $mailService
    ) {}

    public function createCommande(array $data, int $userId): int {
        $menu = $this->menuRepository->findById($data['menu_id']);
        if (!$menu) {
            throw new Exception("Menu introuvable");
        }

        if ($menu['stock_disponible'] <= 0) {
            throw new Exception("Stock épuisé");
        }

        if ($data['nb_personnes'] < $menu['nombre_personne_min']) {
            throw new Exception("Nombre de personnes insuffisant");
        }

        // Calcul prix
        $prixParPers = $menu['prix_base'] / $menu['nombre_personne_min'];
        $prixMenu = $prixParPers * $data['nb_personnes'];
        
        if ($data['nb_personnes'] >= $menu['nombre_personne_min'] + 5) {
            $prixMenu *= 0.90; // Réduction 10%
        }

        $estHorsBordeaux = (stripos($data['ville'], 'bordeaux') === false);
        $prixLivraison = $estHorsBordeaux ? 5.00 : 0.00;
        $prixTotal = round($prixMenu + $prixLivraison, 2);

        try {
            $this->commandeRepository->beginTransaction();

            $commandeId = $this->commandeRepository->save([
                'date_prestation'   => $data['date_livraison'],
                'heure_prestation'  => $data['heure_livraison'],
                'adresse_livraison' => $data['adresse_livraison'] . ', ' . $data['ville'],
                'est_hors_bordeaux' => $estHorsBordeaux ? 1 : 0,
                'nombre_personnes'  => $data['nb_personnes'],
                'prix_total_ttc'    => $prixTotal,
                'utilisateur_id'    => $userId,
                'menu_id'           => $data['menu_id']
            ]);

            $this->commandeRepository->addSuivi($commandeId, 'en attente', 'Commande reçue via le nouveau système');

            // Décrémente le stock
            $this->menuRepository->decrementStock($data['menu_id']);

            $this->commandeRepository->commit();

            return $commandeId;
        } catch (Exception $e) {
            $this->commandeRepository->rollBack();
            throw $e;
        }
    }

    public function getCommandeDetails(int $id): ?array {
        return $this->commandeRepository->findById($id);
    }
}
