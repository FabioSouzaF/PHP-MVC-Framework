<?php

namespace Core\Database\ORM;

use Core\Database\Database;
use PDO;
use Exception;
use ReflectionClass;

abstract class ActiveRecord
{
    protected ?PDO $db = null;
    protected string $table;
    protected string $primaryKey = 'id';
    
    /** @var array<string, mixed> Armazena os atributos originais (como vieram do banco) */
    protected array $original = [];
    
    /** @var array<string, mixed> Armazena os atributos atuais (com possíveis modificações) */
    protected array $attributes = [];
    
    // --- Query Builder State ---
    private array $wheres = [];
    private array $params = [];
    private string $orderBy = '';
    private string $limit = '';

    public function __construct(array $attributes = [])
    {
        $this->db = (new Database())->getConnection();
        
        if (!isset($this->table)) {
            // Tenta adivinhar o nome da tabela (ex: UserModel -> users)
            $reflection = new ReflectionClass($this);
            $className = str_replace('Model', '', $reflection->getShortName());
            $this->table = strtolower($className) . 's'; // simples plural
        }

        $this->fill($attributes);
    }

    /**
     * Preenche os atributos do model.
     */
    public function fill(array $attributes): self
    {
        foreach ($attributes as $key => $value) {
            $this->attributes[$key] = $value;
        }
        return $this;
    }

    // --- Métodos Mágicos para acessar propriedades dinamicamente ($user->name) ---

    public function __get(string $key)
    {
        return $this->attributes[$key] ?? null;
    }

    public function __set(string $key, $value)
    {
        $this->attributes[$key] = $value;
    }

    public function __isset(string $key)
    {
        return isset($this->attributes[$key]);
    }

    public function toArray(): array
    {
        return $this->attributes;
    }

    // --- Static Initializers ---

    public static function query(): static
    {
        return new static();
    }

    // --- Query Builder Methods ---

    public function where(string $column, $operator, $value = null): static
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        $this->wheres[] = "$column $operator ?";
        $this->params[] = $value;
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): static
    {
        $this->orderBy = "ORDER BY $column " . strtoupper($direction);
        return $this;
    }

    public function limit(int $limit): static
    {
        $this->limit = "LIMIT $limit";
        return $this;
    }

    // --- Execução (CRUD e Select) ---

    public static function find(int $id): ?static
    {
        return static::query()->where('id', $id)->first();
    }

    public function first(): ?static
    {
        $this->limit(1);
        $results = $this->get();
        return $results[0] ?? null;
    }

    /**
     * @return static[]
     */
    public function get(): array
    {
        if (!$this->db) return [];

        $sql = "SELECT * FROM {$this->table}";
        
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }
        
        if ($this->orderBy) {
            $sql .= " {$this->orderBy}";
        }
        
        if ($this->limit) {
            $sql .= " {$this->limit}";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($this->params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $instances = [];
        foreach ($rows as $row) {
            $instance = new static();
            $instance->attributes = $row;
            $instance->original = $row; // Salva o estado original
            $instances[] = $instance;
        }

        return $instances;
    }

    public static function create(array $attributes): static
    {
        $instance = new static($attributes);
        $instance->save();
        return $instance;
    }

    public function save(): bool
    {
        if (!$this->db) return false;

        $isNew = empty($this->original);
        
        if ($isNew) {
            return $this->performInsert();
        } else {
            return $this->performUpdate();
        }
    }

    private function performInsert(): bool
    {
        $columns = array_keys($this->attributes);
        $values = array_values($this->attributes);
        
        $placeholders = implode(', ', array_fill(0, count($columns), '?'));
        $columnsStr = implode(', ', $columns);
        
        $sql = "INSERT INTO {$this->table} ({$columnsStr}) VALUES ({$placeholders})";
        
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute($values);
        
        if ($success) {
            $this->attributes[$this->primaryKey] = (int) $this->db->lastInsertId();
            $this->original = $this->attributes; // Atualiza o estado
        }
        
        return $success;
    }

    private function performUpdate(): bool
    {
        // Pega apenas o que mudou
        $dirty = [];
        foreach ($this->attributes as $key => $value) {
            if (!array_key_exists($key, $this->original) || $this->original[$key] !== $value) {
                $dirty[$key] = $value;
            }
        }
        
        if (empty($dirty)) {
            return true; // Nada a atualizar
        }

        $sets = [];
        $values = [];
        foreach ($dirty as $column => $value) {
            $sets[] = "{$column} = ?";
            $values[] = $value;
        }
        
        // Adiciona o ID no final
        $values[] = $this->original[$this->primaryKey];
        
        $setsStr = implode(', ', $sets);
        $sql = "UPDATE {$this->table} SET {$setsStr} WHERE {$this->primaryKey} = ?";
        
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute($values);
        
        if ($success) {
            $this->original = $this->attributes; // Atualiza o estado
        }
        
        return $success;
    }

    public function delete(): bool
    {
        if (!$this->db || empty($this->original)) {
            return false;
        }

        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([$this->original[$this->primaryKey]]);
    }

    // --- Integração com DTOs ---
    
    /**
     * @var string|null Classe do DTO padrão (pode ser sobrescrita nos models filhos)
     */
    protected ?string $dtoClass = null;

    /**
     * Converte esta instância ActiveRecord para um DTO.
     */
    public function toDTO(?string $dtoClass = null)
    {
        $targetClass = $dtoClass ?? $this->dtoClass;
        
        if (!$targetClass) {
            throw new Exception("Nenhum DTO configurado para o Model " . static::class);
        }
        
        if (!method_exists($targetClass, 'fromArray')) {
            throw new Exception("O DTO {$targetClass} deve ter um método estático fromArray().");
        }

        return $targetClass::fromArray($this->attributes);
    }

    /**
     * Retorna os resultados da query builder como um array de DTOs.
     * 
     * @return array
     */
    public function getAsDTO(?string $dtoClass = null): array
    {
        $instances = $this->get();
        $dtos = [];
        foreach ($instances as $instance) {
            $dtos[] = $instance->toDTO($dtoClass);
        }
        return $dtos;
    }
}
