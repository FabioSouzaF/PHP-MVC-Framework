<?php

// Bootstrap para os testes: define constantes e carrega o autoloader
require_once __DIR__ . '/../vendor/autoload.php';

define('APP_ROOT', dirname(__DIR__));

// Carrega o .env se existir (para testes que precisem de DB)
if (file_exists(APP_ROOT . '/.env')) {
    \Core\Utils\Env::load(APP_ROOT . '/.env');
}
