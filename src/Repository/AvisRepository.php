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
}
