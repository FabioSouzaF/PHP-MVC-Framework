<?php

namespace App\Auth\Models;

use Core\Database\ORM\ActiveRecord;
use App\Auth\DTOs\UserDTO;

/**
 * Modelo de usuário utilizando o ORM (ActiveRecord).
 * 
 * Atributos dinâmicos baseados no banco de dados:
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $created_at
 */
class UserORM extends ActiveRecord
{
    // Opcional: Se não definir $table, ele vai adivinhar 'userorms', então definimos manualmente
    protected string $table = 'users';

    // Opcional: Define a qual DTO esta classe pertence para permitir $user->toDTO()
    protected ?string $dtoClass = UserDTO::class;
}
