<?php

namespace Tests\Unit\Core;

use Core\Http\Request;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class ValidatorTest extends TestCase
{
    // -------------------------------------------------------
    // Regra: required
    // -------------------------------------------------------

    public function test_required_throws_when_field_is_empty(): void
    {
        $this->expectException(ValidationException::class);

        $request = $this->makeRequest(post: ['nome' => '']);
        $request->validate(['nome' => 'required']);
    }

    public function test_required_passes_when_field_has_value(): void
    {
        $request = $this->makeRequest(post: ['nome' => 'Fábio']);
        $result = $request->validate(['nome' => 'required']);

        $this->assertSame('Fábio', $result['nome']);
    }

    public function test_required_passes_with_zero_string(): void
    {
        $request = $this->makeRequest(post: ['quantidade' => '0']);
        $result = $request->validate(['quantidade' => 'required']);

        $this->assertSame('0', $result['quantidade']);
    }

    // -------------------------------------------------------
    // Regra: email
    // -------------------------------------------------------

    public function test_email_throws_when_format_is_invalid(): void
    {
        $this->expectException(ValidationException::class);

        $request = $this->makeRequest(post: ['email' => 'nao-e-email']);
        $request->validate(['email' => 'email']);
    }

    public function test_email_passes_with_valid_address(): void
    {
        $request = $this->makeRequest(post: ['email' => 'fabio@email.com']);
        $result = $request->validate(['email' => 'email']);

        $this->assertSame('fabio@email.com', $result['email']);
    }

    // -------------------------------------------------------
    // Regra: min
    // -------------------------------------------------------

    public function test_min_throws_when_string_is_too_short(): void
    {
        $this->expectException(ValidationException::class);

        $request = $this->makeRequest(post: ['senha' => '123']);
        $request->validate(['senha' => 'min:6']);
    }

    public function test_min_passes_with_sufficient_length(): void
    {
        $request = $this->makeRequest(post: ['senha' => '123456']);
        $result = $request->validate(['senha' => 'min:6']);

        $this->assertSame('123456', $result['senha']);
    }

    // -------------------------------------------------------
    // Regra: max
    // -------------------------------------------------------

    public function test_max_throws_when_string_is_too_long(): void
    {
        $this->expectException(ValidationException::class);

        $request = $this->makeRequest(post: ['bio' => str_repeat('a', 256)]);
        $request->validate(['bio' => 'max:255']);
    }

    // -------------------------------------------------------
    // Regra: numeric
    // -------------------------------------------------------

    public function test_numeric_throws_when_value_is_not_a_number(): void
    {
        $this->expectException(ValidationException::class);

        $request = $this->makeRequest(post: ['preco' => 'abc']);
        $request->validate(['preco' => 'numeric']);
    }

    public function test_numeric_passes_with_decimal(): void
    {
        $request = $this->makeRequest(post: ['preco' => '19.99']);
        $result = $request->validate(['preco' => 'numeric']);

        $this->assertSame('19.99', $result['preco']);
    }

    // -------------------------------------------------------
    // Regra: integer
    // -------------------------------------------------------

    public function test_integer_throws_when_value_is_decimal(): void
    {
        $this->expectException(ValidationException::class);

        $request = $this->makeRequest(post: ['idade' => '18.5']);
        $request->validate(['idade' => 'integer']);
    }

    // -------------------------------------------------------
    // Regra: alpha_num
    // -------------------------------------------------------

    public function test_alpha_num_throws_when_value_has_special_chars(): void
    {
        $this->expectException(ValidationException::class);

        $request = $this->makeRequest(post: ['username' => 'user@name!']);
        $request->validate(['username' => 'alpha_num']);
    }

    // -------------------------------------------------------
    // Regras combinadas
    // -------------------------------------------------------

    public function test_multiple_rules_combined(): void
    {
        $this->expectException(ValidationException::class);

        // Email vazio viola 'required' e 'email'
        $request = $this->makeRequest(post: ['email' => '']);
        $request->validate(['email' => 'required|email']);
    }

    public function test_validate_returns_only_declared_fields(): void
    {
        // Dados extras no POST não devem vazar para o resultado
        $request = $this->makeRequest(post: ['nome' => 'Fábio', 'campo_extra' => 'injetado']);
        $result = $request->validate(['nome' => 'required']);

        $this->assertArrayHasKey('nome', $result);
        $this->assertArrayNotHasKey('campo_extra', $result);
    }

    public function test_validation_exception_contains_correct_errors(): void
    {
        try {
            $request = $this->makeRequest(post: ['email' => 'invalido', 'nome' => '']);
            $request->validate(['email' => 'email', 'nome' => 'required']);
            $this->fail('ValidationException esperada não foi lançada.');
        } catch (ValidationException $e) {
            $errors = $e->getErrors();
            $this->assertArrayHasKey('email', $errors);
            $this->assertArrayHasKey('nome', $errors);
        }
    }
}
