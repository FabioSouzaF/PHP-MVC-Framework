<?php

use App\Auth\Controllers\AuthController;

/** @var \Core\Router $router */

$router->get('/login', AuthController::class, 'showLogin');
$router->post('/login', AuthController::class, 'processLogin');
$router->get('/register', AuthController::class, 'showRegister');
$router->post('/register', AuthController::class, 'processRegister');
$router->get('/logout', AuthController::class, 'logout');
