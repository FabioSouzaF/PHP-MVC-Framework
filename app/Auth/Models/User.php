<?php

namespace App\Auth\Models;

use Core\Database\Model;
use App\Auth\DTOs\UserDTO;
use PDO;

class User extends Model
{
    public function findByEmail(string $email)
    {
        if (!$this->db) return null;

        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(array $data)
    {
        if (!$this->db) return false;

        $stmt = $this->db->prepare("INSERT IGNORE INTO users (name, email, password) VALUES (:name, :email, :password)");
        return $stmt->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT)
        ]);
    }

    /**
     * Exemplo de uso de DTOs: retorna todos os usuários como UserDTO tipados.
     *
     * @return UserDTO[]
     */
    public function findAllAsDTO(): array
    {
        return $this->fetchAs("SELECT id, name, email, created_at FROM users ORDER BY name", UserDTO::class);
    }

    public function findByRememberToken(string $token)
    {
        if (!$this->db) return null;
        $stmt = $this->db->prepare("SELECT * FROM users WHERE remember_token = :token");
        $stmt->execute(['token' => $token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateRememberToken(int $id, ?string $token)
    {
        if (!$this->db) return false;
        $stmt = $this->db->prepare("UPDATE users SET remember_token = :token WHERE id = :id");
        return $stmt->execute(['token' => $token, 'id' => $id]);
    }

    public function findByResetToken(string $token)
    {
        if (!$this->db) return null;
        $stmt = $this->db->prepare("SELECT * FROM users WHERE reset_token = :token AND reset_token_expires_at > NOW()");
        $stmt->execute(['token' => $token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function setResetToken(string $email, string $token, string $expiresAt)
    {
        if (!$this->db) return false;
        $stmt = $this->db->prepare("UPDATE users SET reset_token = :token, reset_token_expires_at = :expires_at WHERE email = :email");
        return $stmt->execute([
            'token' => $token,
            'expires_at' => $expiresAt,
            'email' => $email
        ]);
    }

    public function updatePasswordAndClearResetToken(int $id, string $newPassword)
    {
        if (!$this->db) return false;
        $stmt = $this->db->prepare("UPDATE users SET password = :password, reset_token = NULL, reset_token_expires_at = NULL WHERE id = :id");
        return $stmt->execute([
            'password' => password_hash($newPassword, PASSWORD_DEFAULT),
            'id' => $id
        ]);
    }
}
