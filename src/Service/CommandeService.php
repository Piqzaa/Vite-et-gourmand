<?php

namespace App\Service;

use App\Entity\Commande;
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

        if ($menu->getStockDisponible() <= 0) {
            throw new Exception("Stock épuisé");
        }

        if ($data['nb_personnes'] < $menu->getNombrePersonneMin()) {
            throw new Exception("Nombre de personnes insuffisant");
        }

        $nbMin = $menu->getNombrePersonneMin();
        if ($nbMin <= 0) {
            throw new Exception("Configuration invalide du menu");
        }

        // Calcul prix
        $prixParPers = $menu->getPrixBase() / $nbMin;
        $prixMenu = $prixParPers * $data['nb_personnes'];
        
        if ($data['nb_personnes'] >= $nbMin + 5) {
            $prixMenu *= 0.90; // Réduction 10%
        }

        // Frais de livraison (CGV : forfait 5€ + 0,59€/km hors Bordeaux)
        $estHorsBordeaux = (stripos($data['ville'], 'bordeaux') === false);
        $distanceKm = (int)($data['distance_km'] ?? 0);
        $prixLivraison = $estHorsBordeaux ? (5.00 + $distanceKm * 0.59) : 0.00;
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

            // Décrémente le stock (atomique : vérifie que stock > 0)
            $stockOk = $this->menuRepository->decrementStock($data['menu_id']);
            if (!$stockOk) {
                throw new Exception("Le stock a été épuisé entre-temps");
            }

            $this->commandeRepository->commit();

            return $commandeId;
        } catch (Exception $e) {
            $this->commandeRepository->rollBack();
            throw $e;
        }
    }

    public function getCommandeDetails(int $id): ?Commande {
        return $this->commandeRepository->findById($id);
    }
}
