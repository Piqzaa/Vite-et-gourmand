<?php

namespace App\Repository;

use PDO;

class ThemeRepository {
    public function __construct(private PDO $pdo) {}

    public function findAll(): array {
        return $this->pdo->query('SELECT * FROM theme ORDER BY libelle')->fetchAll(PDO::FETCH_ASSOC);
    }
}
