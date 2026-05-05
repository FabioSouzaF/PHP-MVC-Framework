<?php

use App\Site\Controllers\HomeController;
use App\Auth\Middlewares\AuthMiddleware;
use App\Site\Controllers\TestController;

/** @var \Core\Router $router */

$router->get('/', HomeController::class, 'index');

// Exemplo de uma rota protegida pelo AuthMiddleware!
$router->get('/painel', HomeController::class, 'dashboard')
    ->middleware(AuthMiddleware::class);

// --- Rotas de Teste das Novas Features ---
$router->group(['prefix' => '/testes'], function ($router) {
    $router->get('/erro', TestController::class, 'error');

    $router->get('/form', TestController::class, 'form');
    $router->post('/submit', TestController::class, 'submit');

    $router->get('/paginacao', TestController::class, 'pagination');
});
