<?php

namespace App\Repository;

use PDO;

class RegimeRepository {
    public function __construct(private PDO $pdo) {}

    public function findAll(): array {
        return $this->pdo->query('SELECT * FROM regime ORDER BY libelle')->fetchAll(PDO::FETCH_ASSOC);
    }
}
