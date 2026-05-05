<?php

namespace Core\Console;

use Core\Database\Database;
use PDO;
use Exception;

class Migrator
{
    private PDO $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function applyMigrations()
    {
        $this->createMigrationsTable();
        $appliedMigrations = $this->getAppliedMigrations();

        $newMigrations = [];
        $files = scandir(APP_ROOT . '/database/migrations');
        $toApply = array_diff($files, $appliedMigrations);

        foreach ($toApply as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $migration = require_once APP_ROOT . '/database/migrations/' . $file;
            
            echo "Atingindo migration: $file" . PHP_EOL;
            try {
                $migration->up($this->db);
                echo "Migration aplicada: $file" . PHP_EOL;
                $newMigrations[] = $file;
            } catch (Exception $e) {
                echo "ERRO ao aplicar $file: " . $e->getMessage() . PHP_EOL;
                exit(1);
            }
        }

        if (!empty($newMigrations)) {
            $this->saveMigrations($newMigrations);
            echo "Todas as migrations foram aplicadas com sucesso." . PHP_EOL;
        } else {
            echo "O banco de dados já está atualizado." . PHP_EOL;
        }
    }

    private function createMigrationsTable()
    {
        $this->db->exec("CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    private function getAppliedMigrations()
    {
        $stmt = $this->db->prepare("SELECT migration FROM migrations");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    private function saveMigrations(array $migrations)
    {
        $str = implode(",", array_map(fn($m) => "('$m')", $migrations));
        $stmt = $this->db->prepare("INSERT INTO migrations (migration) VALUES $str");
        $stmt->execute();
    }

    public function makeMigration(string $name)
    {
        $date = date('Ymd_His');
        $filename = 'm_' . $date . '_' . $name . '.php';
        $filepath = APP_ROOT . '/database/migrations/' . $filename;

        $content = <<<PHP
<?php

return new class {
    public function up(\PDO \$db)
    {
        // \$db->exec("CREATE TABLE {$name} (id INT AUTO_INCREMENT PRIMARY KEY)");
    }

    public function down(\PDO \$db)
    {
        // \$db->exec("DROP TABLE {$name}");
    }
};
PHP;
        file_put_contents($filepath, $content);
        echo "Migration criada com sucesso: $filename" . PHP_EOL;
    }

    public static function initProject()
    {
        echo "Iniciando setup do projeto..." . PHP_EOL;

        $envFile = APP_ROOT . '/.env';
        $envExample = APP_ROOT . '/.env.example';

        if (!file_exists($envFile)) {
            if (file_exists($envExample)) {
                copy($envExample, $envFile);
                echo "[1/3] Arquivo .env criado a partir de .env.example." . PHP_EOL;
                \Core\Utils\Env::load($envFile);
            } else {
                echo "Aviso: .env.example não encontrado." . PHP_EOL;
            }
        } else {
            echo "[1/3] Arquivo .env já existe. Pulando." . PHP_EOL;
        }

        $host = $_ENV['DB_HOST'] ?? 'localhost';
        $user = $_ENV['DB_USER'] ?? 'root';
        $pass = $_ENV['DB_PASS'] ?? '';
        $dbname = $_ENV['DB_NAME'] ?? 'modelo_mvc';

        echo "[2/3] Checando banco de dados '$dbname'..." . PHP_EOL;
        try {
            $pdo = new PDO("mysql:host=$host", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            echo "      Banco de dados pronto!" . PHP_EOL;
        } catch (Exception $e) {
            echo "ERRO ao conectar/criar o banco de dados: " . $e->getMessage() . PHP_EOL;
            exit(1);
        }

        echo "[3/3] Rodando migrations..." . PHP_EOL;
        $migrator = new self();
        $migrator->applyMigrations();

        echo PHP_EOL . "🎉 Projeto inicializado com sucesso! Digite 'php -S localhost:8000 -t public' para iniciar o servidor local." . PHP_EOL;
    }
}
