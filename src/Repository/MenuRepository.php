<?php

namespace App\Repository;

use PDO;

class MenuRepository {
    public function __construct(private PDO $pdo) {}

    public function findAll(): array {
        $stmt = $this->pdo->query('
            SELECT * FROM menu
            ORDER BY titre
        ');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function findAllAvailable(): array {
        $stmt = $this->pdo->query('
            SELECT * FROM menu
            WHERE stock_disponible > 0
            ORDER BY titre
        ');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?array {
        $stmt = $this->pdo->prepare('
            SELECT m.*, t.libelle as theme, r.libelle as regime
            FROM menu m
            LEFT JOIN theme t ON m.theme_id = t.theme_id
            LEFT JOIN regime r ON m.regime_id = r.regime_id
            WHERE m.menu_id = ?
        ');
        $stmt->execute([$id]);
        $menu = $stmt->fetch(PDO::FETCH_ASSOC);
        return $menu ?: null;
    }

    public function findByFilters(array $filters): array {
        $where = [];
        $params = [];

        if (!empty($filters['prix_max'])) {
            $where[] = 'm.prix_base <= ?';
            $params[] = (float)$filters['prix_max'];
        }
        if (!empty($filters['prix_min'])) {
            $where[] = 'm.prix_base >= ?';
            $params[] = (float)$filters['prix_min'];
        }
        if (!empty($filters['theme'])) {
            $where[] = 'm.theme_id = ?';
            $params[] = (int)$filters['theme'];
        }
        if (!empty($filters['regime'])) {
            $where[] = 'm.regime_id = ?';
            $params[] = (int)$filters['regime'];
        }
        if (!empty($filters['personnes'])) {
            $where[] = 'm.nombre_personne_min <= ?';
            $params[] = (int)$filters['personnes'];
        }

        $sql = '
            SELECT m.menu_id, m.titre, m.description, m.nombre_personne_min,
                m.prix_base, m.stock_disponible,
                t.libelle AS theme,
                r.libelle AS regime,
                (SELECT p.image_path 
                 FROM compose_menu cm 
                 JOIN plat p ON cm.plat_id = p.plat_id 
                 WHERE cm.menu_id = m.menu_id 
                   AND p.type = \'plat\' 
                 LIMIT 1) AS image_path
            FROM menu m
            LEFT JOIN theme t ON m.theme_id = t.theme_id
            LEFT JOIN regime r ON m.regime_id = r.regime_id
        ';

        if (!empty($where)) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findRandomAvailable(int $limit = 3): array {
        $stmt = $this->pdo->prepare('
            SELECT m.menu_id, m.titre, m.prix_base, m.nombre_personne_min,
                   t.libelle AS theme,
                   (SELECT p.image_path 
                    FROM compose_menu cm 
                    JOIN plat p ON cm.plat_id = p.plat_id 
                    WHERE cm.menu_id = m.menu_id 
                      AND p.type = \'plat\' 
                    LIMIT 1) AS image_path
            FROM menu m
            LEFT JOIN theme t ON m.theme_id = t.theme_id
            WHERE m.stock_disponible > 0
            ORDER BY RAND()
            LIMIT :limit
        ');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function decrementStock(int $menuId): bool {
        $stmt = $this->pdo->prepare('UPDATE menu SET stock_disponible = stock_disponible - 1 WHERE menu_id = ?');
        return $stmt->execute([$menuId]);
    }

    public function findPlatsByMenuId(int $menuId): array {
        $stmt = $this->pdo->prepare('
            SELECT p.plat_id, p.libelle AS plat_titre, p.type, p.image_path,
                  GROUP_CONCAT(a.libelle SEPARATOR ", ") AS allergenes
            FROM compose_menu cm
            JOIN plat p ON cm.plat_id = p.plat_id
            LEFT JOIN plat_allergene pa ON p.plat_id = pa.plat_id
            LEFT JOIN allergene a ON pa.allergene_id = a.allergene_id
            WHERE cm.menu_id = :id
            GROUP BY p.plat_id, p.libelle, p.type, p.image_path
        ');
        $stmt->execute([':id' => $menuId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
