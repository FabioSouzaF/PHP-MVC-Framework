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
