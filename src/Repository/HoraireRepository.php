<?php
namespace App\Repository;
use PDO;

class HoraireRepository {
    public function __construct(private PDO $pdo) {}

    public function findAll(): array {
        return $this->pdo->query('SELECT * FROM horaire ORDER BY horaire_id ASC')->fetchAll(PDO::FETCH_ASSOC);
    }
}