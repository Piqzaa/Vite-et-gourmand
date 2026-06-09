<?php

namespace App\Repository;

use App\Entity\User;
use PDO;

class UserRepository {
    public function __construct(private PDO $pdo) {}

    private function mapToEntity(array $data): User {
        return new User(
            id: (int)$data['utilisateur_id'],
            nom: $data['nom'],
            prenom: $data['prenom'],
            email: $data['email'],
            password: $data['password'] ?? null,
            role: $data['role'],
            gsm: $data['gsm'] ?? '',
            adressePostale: $data['adresse_postale'] ?? '',
            ville: $data['ville'] ?? '',
            actif: (bool)$data['actif']
        );
    }

    public function findByEmail(string $email): ?User {
        $stmt = $this->pdo->prepare('SELECT * FROM utilisateur WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ? $this->mapToEntity($user) : null;
    }

    public function findById(int $id): ?User {
        $stmt = $this->pdo->prepare('SELECT * FROM utilisateur WHERE utilisateur_id = ?');
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? $this->mapToEntity($data) : null;
    }

    public function findByResetToken(string $token): ?User {
        $stmt = $this->pdo->prepare('
            SELECT * FROM utilisateur 
            WHERE reset_token = ? AND reset_token_expire > NOW()
        ');
        $stmt->execute([$token]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? $this->mapToEntity($data) : null;
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

    public function update(int $userId, array $data): bool {
        $fields = [];
        $params = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $params[] = $value;
        }
        $params[] = $userId;
        
        $sql = "UPDATE utilisateur SET " . implode(', ', $fields) . " WHERE utilisateur_id = ?";
        return $this->pdo->prepare($sql)->execute($params);
    }

    public function findByRole(string $role): array {
        $stmt = $this->pdo->prepare('SELECT * FROM utilisateur WHERE role = ? ORDER BY nom');
        $stmt->execute([$role]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map([$this, 'mapToEntity'], $results);
    }
}
