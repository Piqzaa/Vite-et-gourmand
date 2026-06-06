<?php

namespace App\Repository;

use PDO;

class UserRepository {
    public function __construct(private PDO $pdo) {}

    public function findByEmail(string $email): ?array {
        $stmt = $this->pdo->prepare('SELECT * FROM utilisateur WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public function findById(int $id): ?array {
        $stmt = $this->pdo->prepare('SELECT * FROM utilisateur WHERE utilisateur_id = ?');
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public function save(array $data): bool {
        $stmt = $this->pdo->prepare('
            INSERT INTO utilisateur (nom, prenom, gsm, email, password, adresse_postale, ville, role, actif)
            VALUES (:nom, :prenom, :gsm, :email, :password, :adresse_postale, :ville, :role, :actif)
        ');
        return $stmt->execute($data);
    }
}
