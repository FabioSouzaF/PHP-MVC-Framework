<?php

// public/index.php

// 1. Carrega o autoloader do Composer
require_once __DIR__ . '/../vendor/autoload.php';

// 2. Define a constante para o diretório raiz do projeto (útil para includes/requires)
define('APP_ROOT', dirname(__DIR__));

\Core\Utils\Env::load(APP_ROOT . '/.env');

// A sessão agora é iniciada através do Core\Session na Application

// 3. Registra o Tratador Global de Exceções
$errorHandler = new \Core\Exceptions\Handler();
$errorHandler->register();

// 4. Inclui a classe Application
use Core\Application;

//4. define timezone para sao paulo
date_default_timezone_set('America/Sao_Paulo');
ini_set('post_max_size', '50M');
ini_set('upload_max_filesize', '50M');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
    http_response_code(200);
    exit();
}

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");


// 5. Cria e executa a aplicação
$app = new Application();
$app->run();

