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
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function findByResetToken(string $token): ?array {
        $stmt = $this->pdo->prepare('
            SELECT * FROM utilisateur 
            WHERE reset_token = ? AND reset_token_expire > NOW()
        ');
        $stmt->execute([$token]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function setResetToken(int $userId, string $token, string $expire): bool {
        $stmt = $this->pdo->prepare('UPDATE utilisateur SET reset_token = ?, reset_token_expire = ? WHERE utilisateur_id = ?');
        return $stmt->execute([$token, $expire, $userId]);
    }

    public function updatePassword(int $userId, string $hashedPassword): bool {
        $stmt = $this->pdo->prepare('UPDATE utilisateur SET password = ?, reset_token = NULL, reset_token_expire = NULL WHERE utilisateur_id = ?');
        return $stmt->execute([$hashedPassword, $userId]);
    }

    public function save(array $data): bool {
        $stmt = $this->pdo->prepare('
            INSERT INTO utilisateur (nom, prenom, gsm, email, password, adresse_postale, ville, role, actif)
            VALUES (:nom, :prenom, :gsm, :email, :password, :adresse_postale, :ville, :role, :actif)
        ');
        return $stmt->execute($data);
    }

    public function updateActif(int $userId, bool $actif): bool {
        $stmt = $this->pdo->prepare('UPDATE utilisateur SET actif = ? WHERE utilisateur_id = ?');
        return $stmt->execute([(int)$actif, $userId]);
    }

    public function findByRole(string $role): array {
        $stmt = $this->pdo->prepare('SELECT utilisateur_id, nom, prenom, email, actif FROM utilisateur WHERE role = ? ORDER BY nom');
        $stmt->execute([$role]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
