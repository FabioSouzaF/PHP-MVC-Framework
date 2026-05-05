<?php

use App\Site\Controllers\HomeController;
use \App\Auth\Middlewares\AuthMiddleware;
/** @var \Core\Router $router */

$router->get('/', HomeController::class, 'index');

// Exemplo de uma rota protegida pelo AuthMiddleware!
$router->get('/dashboard', HomeController::class, 'index')
       ->middleware(AuthMiddleware::class);
