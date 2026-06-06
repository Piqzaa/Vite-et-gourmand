<?php

namespace App\Repository;

use PDO;

class PlatRepository {
    public function __construct(private PDO $pdo) {}

    public function findByMenuId(int $menuId): array {
        $stmt = $this->pdo->prepare('
            SELECT p.* 
            FROM plat p
            JOIN compose_menu cm ON p.plat_id = cm.plat_id
            WHERE cm.menu_id = ?
        ');
        $stmt->execute([$menuId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findAll(): array {
        $stmt = $this->pdo->query('SELECT * FROM plat ORDER BY libelle');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function save(array $data): bool {
        $stmt = $this->pdo->prepare('
            INSERT INTO plat (libelle, type, image_path)
            VALUES (?, ?, ?)
        ');
        return $stmt->execute([$data['libelle'], $data['type'], $data['image_path'] ?? null]);
    }
}
