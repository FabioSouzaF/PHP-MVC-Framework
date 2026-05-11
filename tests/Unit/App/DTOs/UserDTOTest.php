<?php

namespace Tests\Unit\App\DTOs;

use App\Auth\DTOs\UserDTO;
use Tests\TestCase;

class UserDTOTest extends TestCase
{
    public function test_from_array_creates_dto_with_correct_values(): void
    {
        $data = [
            'id' => '5',
            'name' => 'Fábio Souza',
            'email' => 'fabio@teste.com',
            'created_at' => '2026-01-01 10:00:00',
        ];

        $dto = UserDTO::fromArray($data);

        $this->assertInstanceOf(UserDTO::class, $dto);
        $this->assertSame(5, $dto->id);
        $this->assertSame('Fábio Souza', $dto->name);
        $this->assertSame('fabio@teste.com', $dto->email);
        $this->assertSame('2026-01-01 10:00:00', $dto->created_at);
    }

    public function test_from_array_casts_id_to_int(): void
    {
        $dto = UserDTO::fromArray(['id' => '42', 'name' => 'Test', 'email' => 'a@b.com', 'created_at' => '']);

        $this->assertIsInt($dto->id);
        $this->assertSame(42, $dto->id);
    }

    public function test_from_array_handles_missing_fields_gracefully(): void
    {
        $dto = UserDTO::fromArray(['id' => '1']);

        $this->assertSame(1, $dto->id);
        $this->assertSame('', $dto->name);
        $this->assertSame('', $dto->email);
        $this->assertSame('', $dto->created_at);
    }

    public function test_to_array_returns_correct_structure(): void
    {
        $dto = UserDTO::fromArray(['id' => '3', 'name' => 'Ana', 'email' => 'ana@teste.com', 'created_at' => '2026-05-01 00:00:00']);

        $array = $dto->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('email', $array);
        $this->assertArrayHasKey('created_at', $array);
        $this->assertSame(3, $array['id']);
    }

    public function test_dto_properties_are_readonly(): void
    {
        $dto = UserDTO::fromArray(['id' => '1', 'name' => 'Test', 'email' => 'test@test.com', 'created_at' => '']);

        $this->expectException(\Error::class);

        // Tentar modificar uma propriedade readonly deve lançar um Error
        $dto->name = 'Alterado';
    }
}
