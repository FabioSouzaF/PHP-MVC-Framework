<?php

namespace App\Auth\DTOs;

/**
 * Data Transfer Object para a entidade User.
 *
 * Garante que os dados vindos do banco sejam tipados e seguros.
 * Uso: em vez de receber array<string, mixed>, você recebe um UserDTO com propriedades tipadas.
 */
final class UserDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly string $created_at,
    ) {
    }

    /**
     * Factory: converte um array bruto do banco em um UserDTO tipado.
     * Compatível com o método Model::fetchAs().
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) $data['id'],
            name: (string) ($data['name'] ?? ''),
            email: (string) ($data['email'] ?? ''),
            created_at: (string) ($data['created_at'] ?? ''),
        );
    }

    /**
     * Converte o DTO de volta para array (útil para JSON responses).
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->created_at,
        ];
    }
}
