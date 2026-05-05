<?php

use App\Site\Controllers\HomeController;
use \App\Auth\Middlewares\AuthMiddleware;
/** @var \Core\Router $router */

$router->get('/', HomeController::class, 'index');

// Exemplo de uma rota protegida pelo AuthMiddleware!
$router->get('/painel', \App\Site\Controllers\HomeController::class, 'dashboard')
       ->middleware(\App\Auth\Middlewares\AuthMiddleware::class);

// --- Rotas de Teste das Novas Features ---
$router->group(['prefix' => '/testes'], function($router) {
    $router->get('/erro', \App\Site\Controllers\TestController::class, 'error');
    
    $router->get('/form', \App\Site\Controllers\TestController::class, 'form');
    $router->post('/submit', \App\Site\Controllers\TestController::class, 'submit');
    
    $router->get('/paginacao', \App\Site\Controllers\TestController::class, 'pagination');
});
