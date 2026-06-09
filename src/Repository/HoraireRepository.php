<?php
namespace App\Repository;
use PDO;

class HoraireRepository {
    public function __construct(private PDO $pdo) {}

    public function update(int $id, string $ouverture, string $fermeture): bool {
        $stmt = $this->pdo->prepare('UPDATE horaire SET heure_ouverture = ?, heure_fermeture = ? WHERE horaire_id = ?');
        return $stmt->execute([$ouverture, $fermeture, $id]);
    }

    public function findAll(): array {
        return $this->pdo->query('SELECT * FROM horaire ORDER BY horaire_id ASC')->fetchAll(PDO::FETCH_ASSOC);
    }
}