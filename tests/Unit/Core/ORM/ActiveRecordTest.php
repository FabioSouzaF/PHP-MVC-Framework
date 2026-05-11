<?php

namespace Tests\Unit\Core\ORM;

use Core\Database\ORM\ActiveRecord;
use Tests\TestCase;
use PDO;
use PDOStatement;

class MockModel extends ActiveRecord
{
    protected string $table = 'mock_table';
    
    // Método para injetar o PDO fake
    public function setPDO(PDO $pdo): void
    {
        $this->db = $pdo;
    }
}

class ActiveRecordTest extends TestCase
{
    private PDO $pdo;
    private PDOStatement $stmt;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Usamos mocks do PHPUnit para simular o banco de dados
        $this->pdo = $this->createMock(PDO::class);
        $this->stmt = $this->createMock(PDOStatement::class);
    }

    public function test_get_builds_correct_query(): void
    {
        $model = new MockModel();
        $model->setPDO($this->pdo);

        $this->pdo->expects($this->once())
             ->method('prepare')
             ->with("SELECT * FROM mock_table WHERE status = ? AND age > ? ORDER BY name DESC LIMIT 10")
             ->willReturn($this->stmt);

        $this->stmt->expects($this->once())
             ->method('execute')
             ->with([1, 18]);

        $this->stmt->expects($this->once())
             ->method('fetchAll')
             ->willReturn([['id' => 1, 'name' => 'Fábio']]);

        $results = $model->where('status', 1)
                         ->where('age', '>', 18)
                         ->orderBy('name', 'DESC')
                         ->limit(10)
                         ->get();

        $this->assertCount(1, $results);
        $this->assertInstanceOf(MockModel::class, $results[0]);
        $this->assertSame('Fábio', $results[0]->name);
    }

    public function test_save_performs_insert_when_new(): void
    {
        $model = new MockModel(['name' => 'Ana', 'email' => 'ana@teste.com']);
        $model->setPDO($this->pdo);

        $this->pdo->expects($this->once())
             ->method('prepare')
             ->with("INSERT INTO mock_table (name, email) VALUES (?, ?)")
             ->willReturn($this->stmt);

        $this->stmt->expects($this->once())
             ->method('execute')
             ->with(['Ana', 'ana@teste.com'])
             ->willReturn(true);

        $this->pdo->expects($this->once())
             ->method('lastInsertId')
             ->willReturn("42");

        $success = $model->save();

        $this->assertTrue($success);
        $this->assertSame(42, $model->id); // O ID deve ser preenchido
    }

    public function test_save_performs_update_when_exists_and_dirty(): void
    {
        // Simulamos que o model veio do banco
        $model = new MockModel();
        $model->fill(['id' => 5, 'name' => 'João']);
        $model->setPDO($this->pdo);
        
        // Hack para simular o "original"
        $reflection = new \ReflectionClass($model);
        $originalProp = $reflection->getProperty('original');
        $originalProp->setAccessible(true);
        $originalProp->setValue($model, ['id' => 5, 'name' => 'João']);

        // Modificamos
        $model->name = 'João Alterado';

        $this->pdo->expects($this->once())
             ->method('prepare')
             ->with("UPDATE mock_table SET name = ? WHERE id = ?")
             ->willReturn($this->stmt);

        $this->stmt->expects($this->once())
             ->method('execute')
             ->with(['João Alterado', 5])
             ->willReturn(true);

        $success = $model->save();
        $this->assertTrue($success);
    }

    public function test_delete_performs_query_with_primary_key(): void
    {
        $model = new MockModel();
        $model->setPDO($this->pdo);
        
        $reflection = new \ReflectionClass($model);
        $originalProp = $reflection->getProperty('original');
        $originalProp->setAccessible(true);
        $originalProp->setValue($model, ['id' => 99]);

        $this->pdo->expects($this->once())
             ->method('prepare')
             ->with("DELETE FROM mock_table WHERE id = ?")
             ->willReturn($this->stmt);

        $this->stmt->expects($this->once())
             ->method('execute')
             ->with([99])
             ->willReturn(true);

        $success = $model->delete();
        $this->assertTrue($success);
    }
}
