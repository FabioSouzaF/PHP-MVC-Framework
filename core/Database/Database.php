<?php
// app/Core/Database.php

namespace Core\Database;

use PDO;
use PDOException;

class Database {
    private string $host;
    private string $db_name;
    private string $username;
    private string $password;
    public ?PDO $conn = null;

    public function __construct()
    {
        $this->host = $_ENV['DB_HOST'] ?? 'localhost:3306';
        $this->db_name = $_ENV['DB_NAME'] ?? 'minha_nova_base';
        $this->username = $_ENV['DB_USER'] ?? 'root';
        $this->password = $_ENV['DB_PASS'] ?? '';
    }


    /**
     * Obtém uma nova conexão PDO com o banco de dados.
     * @return PDO|null Retorna a instância PDO se a conexão for bem-sucedida, caso contrário, null.
     */
    public function getConnection(): ?PDO {
        $this->conn = null; // Garante que a conexão seja nula antes de tentar conectar

        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            $options = [
                // Define o modo de erro para lançar exceções para que você possa capturá-las
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                // Define o modo de busca padrão para arrays associativos
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                // Desabilita a emulação de prepared statements para segurança e desempenho
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            $this->conn->exec("set names utf8mb4"); // Define o charset para a conexão

        } catch(PDOException $exception) {
            // Em ambiente de desenvolvimento, você pode exibir a mensagem de erro.
            // Em produção, você deve logar o erro e exibir uma mensagem genérica para o usuário.
            error_log("Connection error: " . $exception->getMessage()); // Loga o erro
            echo "Ocorreu um erro ao conectar ao banco de dados. Por favor, tente novamente mais tarde.";
            // Você pode até relançar a exceção ou retornar null e tratar no Model.
            return null;
        }
        return $this->conn;
    }
}