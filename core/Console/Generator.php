<?php

namespace Core\Console;

class Generator
{
    /**
     * Cria um novo arquivo de Teste Unitário
     */
    public static function makeTest(string $name): void
    {
        // Ex: "Core/ValidatorTest" ou "App/DTOs/UserDTOTest"
        $parts = explode('/', $name);
        $className = array_pop($parts);
        $subPath = implode('/', $parts);

        $namespace = 'Tests\\Unit' . ($subPath ? '\\' . str_replace('/', '\\', $subPath) : '');
        $dir = APP_ROOT . '/tests/Unit' . ($subPath ? '/' . $subPath : '');
        $filepath = $dir . '/' . $className . '.php';

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if (file_exists($filepath)) {
            echo "⚠️  Arquivo já existe: $filepath" . PHP_EOL;
            exit(1);
        }

        $content = <<<PHP
<?php

namespace {$namespace};

use Tests\TestCase;

class {$className} extends TestCase
{
    public function test_example(): void
    {
        \$this->assertTrue(true);
    }
}
PHP;

        file_put_contents($filepath, $content);
        echo "✅ Teste criado com sucesso!" . PHP_EOL;
        echo "   📄 {$filepath}" . PHP_EOL;
        echo "   🚀 Rode com: ./vendor/bin/phpunit {$filepath}" . PHP_EOL;
    }

    /**
     * Cria um novo arquivo de DTO
     */
    public static function makeDto(string $name, string $module = 'App'): void
    {
        // Ex: "UserDTO" → app/App/DTOs/UserDTO.php
        // Ex: "Auth/UserDTO" → detecta o módulo
        $parts = explode('/', $name);
        $className = array_pop($parts);

        if (!empty($parts)) {
            $module = array_shift($parts);
        }

        $namespace = "App\\{$module}\\DTOs";
        $dir = APP_ROOT . "/app/{$module}/DTOs";
        $filepath = $dir . '/' . $className . '.php';

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if (file_exists($filepath)) {
            echo "⚠️  Arquivo já existe: $filepath" . PHP_EOL;
            exit(1);
        }

        $content = <<<PHP
<?php

namespace {$namespace};

/**
 * Data Transfer Object: {$className}
 *
 * Use este DTO para tipar os dados retornados do banco.
 * Adicione os campos correspondentes à tabela como propriedades readonly.
 */
final class {$className}
{
    public function __construct(
        public readonly int \$id,
        // TODO: adicione as demais propriedades aqui
    ) {
    }

    /**
     * Factory: converte um array bruto do banco em um DTO tipado.
     * Compatível com Model::fetchAs() e Model::paginateAs().
     */
    public static function fromArray(array \$data): self
    {
        return new self(
            id: (int) \$data['id'],
            // TODO: mapeie os demais campos aqui
        );
    }

    /**
     * Converte o DTO de volta para array (útil para JSON responses).
     */
    public function toArray(): array
    {
        return [
            'id' => \$this->id,
            // TODO: adicione os demais campos aqui
        ];
    }
}
PHP;

        file_put_contents($filepath, $content);
        echo "✅ DTO criado com sucesso!" . PHP_EOL;
        echo "   📄 {$filepath}" . PHP_EOL;
        echo "   💡 Preencha as propriedades e o método fromArray() com os campos da sua tabela." . PHP_EOL;
        echo "   📖 Uso no Model: \$this->fetchAs(\$sql, {$className}::class);" . PHP_EOL;
    }
}
