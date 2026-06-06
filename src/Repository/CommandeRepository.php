<?php

namespace App\Repository;

use PDO;

class CommandeRepository {
    public function __construct(private PDO $pdo) {}

    public function findById(int $id): ?array {
        $stmt = $this->pdo->prepare('
            SELECT c.*, m.titre as menu_titre, u.email, u.prenom, u.nom
            FROM commande c
            JOIN menu m ON c.menu_id = m.menu_id
            JOIN utilisateur u ON c.utilisateur_id = u.utilisateur_id
            WHERE c.commande_id = ?
        ');
        $stmt->execute([$id]);
        $commande = $stmt->fetch(PDO::FETCH_ASSOC);
        return $commande ?: null;
    }

    public function findByUserId(int $userId): array {
        $stmt = $this->pdo->prepare('
            SELECT c.*, m.titre as menu_titre
            FROM commande c
            JOIN menu m ON c.menu_id = m.menu_id
            WHERE c.utilisateur_id = ?
            ORDER BY c.date_commande DESC
        ');
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function save(array $data): int {
        $stmt = $this->pdo->prepare('
            INSERT INTO commande 
                (date_commande, date_prestation, heure_prestation, adresse_livraison,
                est_hors_bordeaux, nombre_personnes, prix_total_ttc, statut,
                pret_materiel, materiel_rendu, utilisateur_id, menu_id)
            VALUES 
                (NOW(), :date_prestation, :heure_prestation, :adresse_livraison,
                :est_hors_bordeaux, :nombre_personnes, :prix_total_ttc, :statut,
                :pret_materiel, :materiel_rendu, :utilisateur_id, :menu_id)
        ');
        
        $stmt->execute([
            'date_prestation'   => $data['date_prestation'],
            'heure_prestation'  => $data['heure_prestation'],
            'adresse_livraison' => $data['adresse_livraison'],
            'est_hors_bordeaux' => $data['est_hors_bordeaux'],
            'nombre_personnes'  => $data['nombre_personnes'],
            'prix_total_ttc'    => $data['prix_total_ttc'],
            'statut'            => $data['statut'] ?? 'en attente',
            'pret_materiel'     => $data['pret_materiel'] ?? 0,
            'materiel_rendu'    => $data['materiel_rendu'] ?? 0,
            'utilisateur_id'    => $data['utilisateur_id'],
            'menu_id'           => $data['menu_id']
        ]);
        
        return (int)$this->pdo->lastInsertId();
    }

    public function addSuivi(int $commandeId, string $statut, string $commentaire): bool {
        $stmt = $this->pdo->prepare('
            INSERT INTO suivi_commande (commande_id, statut, commentaire)
            VALUES (?, ?, ?)
        ');
        return $stmt->execute([$commandeId, $statut, $commentaire]);
    }

    public function updateStatut(int $commandeId, string $statut): bool {
        $stmt = $this->pdo->prepare('UPDATE commande SET statut = ? WHERE commande_id = ?');
        return $stmt->execute([$statut, $commandeId]);
    }

    public function beginTransaction(): void { $this->pdo->beginTransaction(); }
    public function commit(): void { $this->pdo->commit(); }
    public function rollBack(): void { $this->pdo->rollBack(); }
}
