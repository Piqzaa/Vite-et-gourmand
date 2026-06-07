<?php

namespace App\Repository;

use PDO;

class AvisRepository {
    public function __construct(private PDO $pdo) {}

    public function findLatestValidated(int $limit = 3): array {
        $stmt = $this->pdo->prepare('
            SELECT a.note, a.commentaire, u.prenom, u.nom
            FROM avis a
            JOIN utilisateur u ON a.utilisateur_id = u.utilisateur_id
            WHERE a.est_valide = 1
            ORDER BY a.date_publication DESC
            LIMIT :limit
        ');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function save(array $data): bool {
        $stmt = $this->pdo->prepare('
            INSERT INTO avis (note, commentaire, est_valide, date_publication, utilisateur_id, commande_id)
            VALUES (?, ?, 0, NOW(), ?, ?)
        ');
        return $stmt->execute([
            $data['note'], $data['commentaire'], $data['utilisateur_id'], $data['commande_id']
        ]);
    }

    public function existsForCommande(int $commandeId): bool {
        $stmt = $this->pdo->prepare('SELECT avis_id FROM avis WHERE commande_id = ?');
        $stmt->execute([$commandeId]);
        return (bool)$stmt->fetch();
    }

    public function updateStatut(int $avisId, bool $valide): bool {
        $stmt = $this->pdo->prepare('UPDATE avis SET est_valide = ? WHERE avis_id = ?');
        return $stmt->execute([(int)$valide, $avisId]);
    }

    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare('DELETE FROM avis WHERE avis_id = ?');
        return $stmt->execute([$id]);
    }

    public function findPending(): array {
        $stmt = $this->pdo->prepare('
            SELECT a.*, u.prenom AS client_prenom, u.nom AS client_nom, m.titre AS menu_titre
            FROM avis a
            JOIN utilisateur u ON a.utilisateur_id = u.utilisateur_id
            JOIN commande c ON a.commande_id = c.commande_id
            JOIN menu m ON c.menu_id = m.menu_id
            WHERE a.est_valide = 0
            ORDER BY a.date_publication DESC
        ');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
