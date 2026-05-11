<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * Classe base para todos os testes do framework.
 * Adicione aqui helpers compartilhados entre os testes.
 */
abstract class TestCase extends BaseTestCase
{
    // Exemplo de helper: criar um Request fake para testes
    protected function makeRequest(array $post = [], array $get = [], string $method = 'GET'): \Core\Http\Request
    {
        $_POST = $post;
        $_GET = $get;
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['REQUEST_URI'] = '/test';
        return new \Core\Http\Request();
    }
}
