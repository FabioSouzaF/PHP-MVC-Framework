<?php

namespace Core\Http;

class Router
{
    protected array $routes = [];
    protected ?array $lastRoute = null;

    public function get(string $uri, string $controllerClassName, string $methodName)
    {
        return $this->addRoute('GET', $uri, $controllerClassName, $methodName);
    }

    public function post(string $uri, string $controllerClassName, string $methodName)
    {
        return $this->addRoute('POST', $uri, $controllerClassName, $methodName);
    }

    public function put(string $uri, string $controllerClassName, string $methodName)
    {
        return $this->addRoute('PUT', $uri, $controllerClassName, $methodName);
    }

    public function delete(string $uri, string $controllerClassName, string $methodName)
    {
        return $this->addRoute('DELETE', $uri, $controllerClassName, $methodName);
    }

    protected function addRoute(string $method, string $uri, string $controllerClassName, string $methodName)
    {
        $this->routes[$method][$uri] = [
            'controller' => $controllerClassName,
            'method' => $methodName,
            'middlewares' => []
        ];
        
        $this->lastRoute = ['method' => $method, 'uri' => $uri];
        return $this; 
    }

    public function middleware(string $middlewareClass)
    {
        if ($this->lastRoute) {
            $method = $this->lastRoute['method'];
            $uri = $this->lastRoute['uri'];
            $this->routes[$method][$uri]['middlewares'][] = $middlewareClass;
        }
        return $this;
    }

    public function loadRoutes(string $routesDir)
    {
        $router = $this; 
        if (is_dir($routesDir)) {
            foreach (glob($routesDir . '/*.php') as $routeFile) {
                require_once $routeFile;
            }
        }
    }

    public function dispatch(string $pathInfo, string $requestMethod)
    {
        $pathInfo = rtrim($pathInfo, '/'); 
        if ($pathInfo === '') {
            $pathInfo = '/';
        }

        $routesForMethod = $this->routes[$requestMethod] ?? [];

        // 1. Tenta encontrar uma rota exata
        if (isset($routesForMethod[$pathInfo])) {
            $route = $routesForMethod[$pathInfo];
            $this->callController($route['controller'], $route['method'], [], $route['middlewares']);
            return;
        }

        // 2. Tenta encontrar uma rota com parâmetros
        foreach ($routesForMethod as $uriPattern => $route) {
            $regex = preg_replace('/\{([a-zA-Z0-9_-]+)\}/', '([a-zA-Z0-9_-]+)', $uriPattern);
            $regex = '#^' . $regex . '$#';

            if (preg_match($regex, $pathInfo, $matches)) {
                array_shift($matches); 
                $params = $matches; 

                $this->callController($route['controller'], $route['method'], $params, $route['middlewares']);
                return;
            }
        }

        http_response_code(404);
        echo "404 Not Found";
    }

    protected function callController(string $controllerClassName, string $methodName, array $params = [], array $middlewares = [])
    {
        $request = new Request();

        // Middlewares globais (como proteção CSRF em toda a aplicação)
        $globalMiddlewares = [
            \App\Shared\Middlewares\CsrfMiddleware::class
        ];

        $allMiddlewares = array_merge($globalMiddlewares, $middlewares);

        foreach ($allMiddlewares as $middlewareClass) {
            if (class_exists($middlewareClass)) {
                $middleware = new $middlewareClass();
                if (!$middleware->handle($request)) {
                    return; 
                }
            }
        }

        if (!class_exists($controllerClassName)) {
            die("Controller '$controllerClassName' not found.");
        }

        $controller = new $controllerClassName();

        if (!method_exists($controller, $methodName)) {
            die("Method '$methodName' not found in controller '$controllerClassName'.");
        }

        array_unshift($params, $request);

        call_user_func_array([$controller, $methodName], $params);
    }
}