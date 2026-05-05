<?php

namespace Core;

use Core\Http\Router;
use Core\Http\Session;

class Application
{
    public Router $router;

    public function __construct()
    {
        Session::init(); // Inicia o gerenciador de sessão e flash messages
        $this->router = new Router();
    }

    public function run()
    {
        $requestUri = $_SERVER['REQUEST_URI'];
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        $path = strtok($requestUri, '?'); 
        $path = '/' . ltrim($path, '/');

        // Carrega as rotas da pasta routes
        $this->router->loadRoutes(APP_ROOT . '/routes');

        $this->router->dispatch($path, $requestMethod);
    }
}