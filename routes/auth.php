<?php

use App\Auth\Controllers\AuthController;
use App\Auth\Middlewares\AuthMiddleware;

/** @var \Core\Http\Router $router */

$router->get('/login', AuthController::class, 'showLogin');
$router->post('/login', AuthController::class, 'processLogin');
$router->get('/register', AuthController::class, 'showRegister');
$router->post('/register', AuthController::class, 'processRegister');
$router->get('/logout', AuthController::class, 'logout')->middleware(AuthMiddleware::class);

// Recuperação de senha
$router->get('/esqueci-a-senha', AuthController::class, 'showForgotPassword');
$router->post('/esqueci-a-senha', AuthController::class, 'sendResetLink');
$router->get('/redefinir-senha/{token}', AuthController::class, 'showResetPassword');
$router->post('/redefinir-senha/{token}', AuthController::class, 'processResetPassword');
