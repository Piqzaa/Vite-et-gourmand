<?php

namespace App\Repository;

use App\Entity\Menu;
use PDO;

class MenuRepository {
    public function __construct(private PDO $pdo) {}

    private function mapToEntity(array $data): Menu {
        return new Menu(
            id: (int)($data['menu_id'] ?? 0),
            titre: $data['titre'],
            description: $data['description'] ?? '',
            nombrePersonneMin: (int)($data['nombre_personne_min'] ?? 0),
            prixBase: (float)($data['prix_base'] ?? 0),
            stockDisponible: (int)($data['stock_disponible'] ?? 0),
            conditionsParticulieres: $data['conditions_particulieres'] ?? null,
            themeId: isset($data['theme_id']) ? (int)$data['theme_id'] : null,
            regimeId: isset($data['regime_id']) ? (int)$data['regime_id'] : null,
            themeLabel: $data['theme'] ?? null,
            regimeLabel: $data['regime'] ?? null,
            imagePath: $data['image_path'] ?? null
        );
    }

    public function findAll(): array {
        $stmt = $this->pdo->query('
            SELECT * FROM menu
            ORDER BY titre
        ');
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map([$this, 'mapToEntity'], $results);
    }
    public function findAllAvailable(): array {
        $stmt = $this->pdo->query('
            SELECT * FROM menu
            WHERE stock_disponible > 0
            ORDER BY titre
        ');
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map([$this, 'mapToEntity'], $results);
    }

    public function findById(int $id): ?Menu {
        $stmt = $this->pdo->prepare('
            SELECT m.*, t.libelle as theme, r.libelle as regime
            FROM menu m
            LEFT JOIN theme t ON m.theme_id = t.theme_id
            LEFT JOIN regime r ON m.regime_id = r.regime_id
            WHERE m.menu_id = ?
        ');
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? $this->mapToEntity($data) : null;
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
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map([$this, 'mapToEntity'], $results);
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
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map([$this, 'mapToEntity'], $results);
    }

    public function decrementStock(int $menuId): bool {
        $stmt = $this->pdo->prepare('UPDATE menu SET stock_disponible = stock_disponible - 1 WHERE menu_id = ? AND stock_disponible > 0');
        $stmt->execute([$menuId]);
        return $stmt->rowCount() > 0;
    }

    public function incrementStock(int $menuId): bool {
        $stmt = $this->pdo->prepare('UPDATE menu SET stock_disponible = stock_disponible + 1 WHERE menu_id = ?');
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

    public function save(array $data): int {
        $stmt = $this->pdo->prepare('
            INSERT INTO menu (titre, description, prix_base, nombre_personne_min, 
                             stock_disponible, theme_id, regime_id, conditions_particulieres)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            $data['titre'], $data['description'], $data['prix_base'], 
            $data['nombre_personne_min'], $data['stock_disponible'], 
            $data['theme_id'], $data['regime_id'], $data['conditions_particulieres']
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->pdo->prepare('
            UPDATE menu SET 
                titre = ?, description = ?, prix_base = ?, 
                nombre_personne_min = ?, stock_disponible = ?, 
                theme_id = ?, regime_id = ?, conditions_particulieres = ?
            WHERE menu_id = ?
        ');
        return $stmt->execute([
            $data['titre'], $data['description'], $data['prix_base'], 
            $data['nombre_personne_min'], $data['stock_disponible'], 
            $data['theme_id'], $data['regime_id'], $data['conditions_particulieres'],
            $id
        ]);
    }

    public function linkPlats(int $menuId, array $platIds): void {
        $stmt = $this->pdo->prepare('INSERT INTO compose_menu (menu_id, plat_id) VALUES (?, ?)');
        foreach ($platIds as $platId) {
            $stmt->execute([$menuId, (int)$platId]);
        }
    }

    public function unlinkPlats(int $menuId): void {
        $stmt = $this->pdo->prepare('DELETE FROM compose_menu WHERE menu_id = ?');
        $stmt->execute([$menuId]);
    }

    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare('DELETE FROM menu WHERE menu_id = ?');
        return $stmt->execute([$id]);
    }

    public function getPlatIds(int $menuId): array {
        $stmt = $this->pdo->prepare('SELECT plat_id FROM compose_menu WHERE menu_id = ?');
        $stmt->execute([$menuId]);
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'plat_id');
    }

    public function beginTransaction(): void { $this->pdo->beginTransaction(); }
    public function commit(): void { $this->pdo->commit(); }
    public function rollBack(): void { $this->pdo->rollBack(); }
}
