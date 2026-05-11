<?php

namespace App\Site\DTOs;

/**
 * Data Transfer Object: PedidoDTO
 *
 * Use este DTO para tipar os dados retornados do banco.
 * Adicione os campos correspondentes à tabela como propriedades readonly.
 */
final class PedidoDTO
{
    public function __construct(
        public readonly int $id,
        // TODO: adicione as demais propriedades aqui
    ) {
    }

    /**
     * Factory: converte um array bruto do banco em um DTO tipado.
     * Compatível com Model::fetchAs() e Model::paginateAs().
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) $data['id'],
            // TODO: mapeie os demais campos aqui
        );
    }

    /**
     * Converte o DTO de volta para array (útil para JSON responses).
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            // TODO: adicione os demais campos aqui
        ];
    }
}