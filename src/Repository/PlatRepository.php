<?php

namespace App\Repository;

use App\Entity\Plat;
use PDO;

class PlatRepository {
    public function __construct(private PDO $pdo) {}

    private function mapToEntity(array $data): Plat {
        $allergenes = !empty($data['allergenes']) ? 
            (is_array($data['allergenes']) ? $data['allergenes'] : explode('||', $data['allergenes'])) 
            : [];

        return new Plat(
            id: (int)$data['plat_id'],
            libelle: $data['libelle'],
            type: $data['type'],
            imagePath: $data['image_path'] ?? null,
            allergenes: $allergenes
        );
    }

    public function findByMenuId(int $menuId): array {
        $stmt = $this->pdo->prepare('
            SELECT p.* 
            FROM plat p
            JOIN compose_menu cm ON p.plat_id = cm.plat_id
            WHERE cm.menu_id = ?
        ');
        $stmt->execute([$menuId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map([$this, 'mapToEntity'], $results);
    }

    public function findAll(): array {
        $stmt = $this->pdo->query('SELECT * FROM plat ORDER BY libelle');
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map([$this, 'mapToEntity'], $results);
    }

    public function findAllWithAllergenes(): array {
        $stmt = $this->pdo->query('
            SELECT p.*, 
                GROUP_CONCAT(a.libelle SEPARATOR "||") AS allergenes
            FROM plat p
            LEFT JOIN plat_allergene pa ON p.plat_id = pa.plat_id
            LEFT JOIN allergene a ON pa.allergene_id = a.allergene_id
            GROUP BY p.plat_id
            ORDER BY p.type, p.libelle
        ');
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map([$this, 'mapToEntity'], $results);
    }
    public function save(array $data): int {
        $stmt = $this->pdo->prepare('
            INSERT INTO plat (libelle, type, image_path)
            VALUES (?, ?, ?)
        ');
        $stmt->execute([$data['libelle'], $data['type'], $data['image_path'] ?? null]);
        return (int)$this->pdo->lastInsertId();
    }

    public function linkAllergenes(int $platId, array $allergeneIds): void {
        $stmt = $this->pdo->prepare('INSERT INTO plat_allergene (plat_id, allergene_id) VALUES (?, ?)');
        foreach ($allergeneIds as $allergeneId) {
            $stmt->execute([$platId, (int)$allergeneId]);
        }
    }

    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare('DELETE FROM plat WHERE plat_id = ?');
        return $stmt->execute([$id]);
    }

    public function beginTransaction(): void { $this->pdo->beginTransaction(); }
    public function commit(): void { $this->pdo->commit(); }
    public function rollBack(): void { $this->pdo->rollBack(); }
}
