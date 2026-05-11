<?php

namespace Core\Database;

use PDO;

abstract class Model
{
    protected ?PDO $db = null;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Executa uma query e retorna os resultados como um array de objetos DTO.
     *
     * @template T
     * @param class-string<T> $dtoClass Classe com método estático fromArray()
     * @return T[]
     */
    public function fetchAs(string $sql, string $dtoClass, array $params = []): array
    {
        if (!$this->db) return [];

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => $dtoClass::fromArray($row), $rows);
    }

    /**
     * Pagina uma query e retorna os resultados como objetos DTO.
     * O campo 'data' conterá instâncias de $dtoClass em vez de arrays.
     *
     * @template T
     * @param class-string<T> $dtoClass Classe com método estático fromArray()
     */
    public function paginateAs(string $sql, string $dtoClass, array $params = [], int $perPage = 15): array
    {
        $result = $this->paginate($sql, $params, $perPage);
        $result['data'] = array_map(fn($row) => $dtoClass::fromArray($row), $result['data']);
        return $result;
    }

    /**
     * Helper para paginar qualquer query nativa automaticamente
     */
    public function paginate(string $sql, array $params = [], int $perPage = 15): array
    {
        if (!$this->db) return [];

        $currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        if ($currentPage < 1) $currentPage = 1;

        // Conta o total de registros englobando a query original
        $countSql = "SELECT COUNT(*) as total FROM ($sql) as count_table";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int) ($countStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

        $lastPage = (int) ceil($total / $perPage);

        // Aplica LIMIT e OFFSET
        $offset = ($currentPage - 1) * $perPage;
        $dataSql = $sql . " LIMIT $perPage OFFSET $offset";
        
        $dataStmt = $this->db->prepare($dataSql);
        $dataStmt->execute($params);
        $data = $dataStmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'data' => $data,
            'current_page' => $currentPage,
            'last_page' => $lastPage,
            'per_page' => $perPage,
            'total' => $total
        ];
    }
}
