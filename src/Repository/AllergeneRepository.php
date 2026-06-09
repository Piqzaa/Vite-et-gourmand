<?php

namespace App\Repository;

use PDO;

class AllergeneRepository {
    public function __construct(private PDO $pdo) {}

    public function findAll(): array {
        $stmt = $this->pdo->query('SELECT * FROM allergene ORDER BY libelle');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
