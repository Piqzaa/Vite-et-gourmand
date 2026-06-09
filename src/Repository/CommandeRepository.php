<?php

namespace App\Repository;

use App\Entity\Commande;
use DateTime;
use PDO;

class CommandeRepository {
    public function __construct(private PDO $pdo) {}

    private function mapToEntity(array $data): Commande {
        return new Commande(
            id: (int)$data['commande_id'],
            dateCommande: new DateTime($data['date_commande']),
            datePrestation: new DateTime($data['date_prestation']),
            heurePrestation: $data['heure_prestation'],
            adresseLivraison: $data['adresse_livraison'],
            estHorsBordeaux: (bool)$data['est_hors_bordeaux'],
            nombrePersonnes: (int)$data['nombre_personnes'],
            prixTotalTtc: (float)$data['prix_total_ttc'],
            statut: $data['statut'],
            motifAnnulation: $data['motif_annulation'] ?? null,
            pretMateriel: (bool)$data['pret_materiel'],
            materielRendu: (bool)$data['materiel_rendu'],
            utilisateurId: isset($data['utilisateur_id']) ? (int)$data['utilisateur_id'] : null,
            menuId: isset($data['menu_id']) ? (int)$data['menu_id'] : null,
            menuNom: $data['menu_titre'] ?? $data['menu_nom'] ?? null,
            clientNom: $data['nom'] ?? $data['client_nom'] ?? null,
            clientPrenom: $data['prenom'] ?? $data['client_prenom'] ?? null,
            clientEmail: $data['email'] ?? $data['client_email'] ?? null,
            clientGsm: $data['gsm'] ?? $data['client_gsm'] ?? null
        );
    }

    public function findById(int $id): ?Commande {
        $stmt = $this->pdo->prepare('
            SELECT c.*, m.titre as menu_titre, u.email, u.prenom, u.nom
            FROM commande c
            JOIN menu m ON c.menu_id = m.menu_id
            JOIN utilisateur u ON c.utilisateur_id = u.utilisateur_id
            WHERE c.commande_id = ?
        ');
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? $this->mapToEntity($data) : null;
    }

    public function findByUserId(int $userId): array {
        $stmt = $this->pdo->prepare('
            SELECT c.*, m.titre as menu_nom
            FROM commande c
            JOIN menu m ON c.menu_id = m.menu_id
            WHERE c.utilisateur_id = ?
            ORDER BY c.date_commande DESC
        ');
        $stmt->execute([$userId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map([$this, 'mapToEntity'], $results);
    }

    public function getSuiviByCommandeId(int $commandeId): array {
        $stmt = $this->pdo->prepare('
            SELECT * FROM suivi_commande 
            WHERE commande_id = ? 
            ORDER BY date_modif ASC
        ');
        $stmt->execute([$commandeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function hasAvis(int $commandeId): bool
        {
            $stmt = $this->pdo->prepare("SELECT avis_id FROM avis WHERE commande_id = ?");
            $stmt->execute([$commandeId]);
            return (bool) $stmt->fetch();
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

    public function findAllWithDetails(): array {
        $stmt = $this->pdo->prepare('
            SELECT c.*, m.titre as menu_nom, 
                   u.email as client_email, u.prenom as client_prenom, u.nom as client_nom, u.gsm as client_gsm
            FROM commande c
            JOIN menu m ON c.menu_id = m.menu_id
            JOIN utilisateur u ON c.utilisateur_id = u.utilisateur_id
            ORDER BY c.date_commande DESC
        ');
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map([$this, 'mapToEntity'], $results);
    }

    public function getGlobalStats(array $filters = []): array {
        $where = ["statut != 'annulée'"];
        $params = [];

        if (!empty($filters['menu_id'])) {
            $where[] = 'menu_id = ?';
            $params[] = (int)$filters['menu_id'];
        }
        if (!empty($filters['date_debut'])) {
            $where[] = 'date_commande >= ?';
            $params[] = $filters['date_debut'] . ' 00:00:00';
        }
        if (!empty($filters['date_fin'])) {
            $where[] = 'date_commande <= ?';
            $params[] = $filters['date_fin'] . ' 23:59:59';
        }

        $sql = '
            SELECT
                COUNT(*) AS nb_commandes,
                COALESCE(SUM(prix_total_ttc), 0) AS total_ttc,
                COALESCE(AVG(prix_total_ttc), 0) AS panier_moyen
            FROM commande
            WHERE ' . implode(' AND ', $where);

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function getStatsByMenu(array $filters = []): array {
        $where = ["c.statut != 'annulée'"];
        $params = [];

        if (!empty($filters['menu_id'])) {
            $where[] = 'c.menu_id = ?';
            $params[] = (int)$filters['menu_id'];
        }
        if (!empty($filters['date_debut'])) {
            $where[] = 'c.date_commande >= ?';
            $params[] = $filters['date_debut'] . ' 00:00:00';
        }
        if (!empty($filters['date_fin'])) {
            $where[] = 'c.date_commande <= ?';
            $params[] = $filters['date_fin'] . ' 23:59:59';
        }

        $sql = '
            SELECT m.titre, COUNT(c.commande_id) AS nombre_commandes, SUM(c.prix_total_ttc) AS ca
            FROM commande c
            JOIN menu m ON c.menu_id = m.menu_id
            WHERE ' . implode(' AND ', $where) . '
            GROUP BY m.menu_id, m.titre
            ORDER BY nombre_commandes DESC
        ';
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function beginTransaction(): void { $this->pdo->beginTransaction(); }
    public function commit(): void { $this->pdo->commit(); }
    public function rollBack(): void { $this->pdo->rollBack(); }
}
