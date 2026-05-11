<?php

use App\Site\Controllers\HomeController;
use App\Auth\Middlewares\AuthMiddleware;
use App\Site\Controllers\TestController;

/** @var \Core\Http\Router $router */

$router->get('/', HomeController::class, 'index');

// Rota protegida individualmente (sem grupo)
$router->get('/painel', HomeController::class, 'dashboard')
    ->middleware(AuthMiddleware::class);

// --- Rotas de Teste das Novas Features ---
// O AuthMiddleware é aplicado a TODAS as rotas do grupo automaticamente.
// Dentro do grupo, rotas individuais ainda podem adicionar middlewares extras via ->middleware().
$router->group(['prefix' => '/testes', 'middleware' => AuthMiddleware::class], function ($router) {
    // Herda o AuthMiddleware do grupo
    $router->get('/erro', TestController::class, 'error');

    // Herda o AuthMiddleware do grupo
    $router->get('/form', TestController::class, 'form');

    // Herda AuthMiddleware do grupo + pode ter mais um middleware extra individual:
    // $router->post('/submit', TestController::class, 'submit')->middleware(OutroMiddleware::class);
    $router->post('/submit', TestController::class, 'submit');

    // Herda o AuthMiddleware do grupo
    $router->get('/paginacao', TestController::class, 'pagination');

    // Demo de DTOs tipados
    $router->get('/dtos', TestController::class, 'dtos');
});
