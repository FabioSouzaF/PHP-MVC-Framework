<?php

use App\Auth\Controllers\AuthController;
use App\Auth\Middlewares\AuthMiddleware;

/** @var \Core\Http\Router $router */

$router->get('/login', AuthController::class, 'showLogin');
$router->post('/login', AuthController::class, 'processLogin');
$router->get('/register', AuthController::class, 'showRegister');
$router->post('/register', AuthController::class, 'processRegister');
$router->get('/logout', AuthController::class, 'logout')->middleware(AuthMiddleware::class);
