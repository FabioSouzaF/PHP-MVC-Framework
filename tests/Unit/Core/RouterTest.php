<?php

namespace Tests\Unit\Core;

use Core\Http\Router;
use Tests\TestCase;

class RouterTest extends TestCase
{
    private Router $router;

    protected function setUp(): void
    {
        $this->router = new Router();
    }

    public function test_get_route_is_registered(): void
    {
        $this->router->get('/home', 'HomeController', 'index');

        $routes = $this->getRoutesProperty();
        $this->assertArrayHasKey('/home', $routes['GET']);
    }

    public function test_post_route_is_registered(): void
    {
        $this->router->post('/submit', 'FormController', 'store');

        $routes = $this->getRoutesProperty();
        $this->assertArrayHasKey('/submit', $routes['POST']);
    }

    public function test_route_stores_correct_controller_and_method(): void
    {
        $this->router->get('/usuarios', 'UserController', 'index');

        $routes = $this->getRoutesProperty();
        $route = $routes['GET']['/usuarios'];

        $this->assertSame('UserController', $route['controller']);
        $this->assertSame('index', $route['method']);
    }

    public function test_group_applies_prefix_to_routes(): void
    {
        $this->router->group(['prefix' => '/admin'], function ($router) {
            $router->get('/painel', 'AdminController', 'index');
            $router->get('/usuarios', 'AdminController', 'users');
        });

        $routes = $this->getRoutesProperty();

        $this->assertArrayHasKey('/admin/painel', $routes['GET']);
        $this->assertArrayHasKey('/admin/usuarios', $routes['GET']);
        $this->assertArrayNotHasKey('/painel', $routes['GET'] ?? []);
    }

    public function test_group_middleware_is_inherited_by_all_routes(): void
    {
        $this->router->group(['prefix' => '/admin', 'middleware' => 'AuthMiddleware'], function ($router) {
            $router->get('/painel', 'AdminController', 'index');
            $router->get('/relatorios', 'AdminController', 'reports');
        });

        $routes = $this->getRoutesProperty();

        $this->assertContains('AuthMiddleware', $routes['GET']['/admin/painel']['middlewares']);
        $this->assertContains('AuthMiddleware', $routes['GET']['/admin/relatorios']['middlewares']);
    }

    public function test_individual_route_middleware_is_appended_to_group_middleware(): void
    {
        $this->router->group(['prefix' => '/admin', 'middleware' => 'AuthMiddleware'], function ($router) {
            $router->delete('/usuario/{id}', 'AdminController', 'destroy')
                   ->middleware('AdminOnlyMiddleware');
        });

        $routes = $this->getRoutesProperty();
        $middlewares = $routes['DELETE']['/admin/usuario/{id}']['middlewares'];

        $this->assertContains('AuthMiddleware', $middlewares);
        $this->assertContains('AdminOnlyMiddleware', $middlewares);
        $this->assertCount(2, $middlewares);
    }

    public function test_route_without_group_has_no_middlewares_by_default(): void
    {
        $this->router->get('/publico', 'PublicController', 'index');

        $routes = $this->getRoutesProperty();
        $this->assertEmpty($routes['GET']['/publico']['middlewares']);
    }

    public function test_group_attributes_are_reset_after_group_closes(): void
    {
        $this->router->group(['prefix' => '/admin'], function ($router) {
            $router->get('/interno', 'AdminController', 'index');
        });

        // Rota fora do grupo não deve ter o prefixo
        $this->router->get('/externo', 'PublicController', 'index');

        $routes = $this->getRoutesProperty();
        $this->assertArrayHasKey('/externo', $routes['GET']);
        $this->assertArrayNotHasKey('/admin/externo', $routes['GET'] ?? []);
    }

    // -------------------------------------------------------
    // Helpers privados
    // -------------------------------------------------------

    private function getRoutesProperty(): array
    {
        $reflection = new \ReflectionClass($this->router);
        $property = $reflection->getProperty('routes');
        $property->setAccessible(true);
        return $property->getValue($this->router);
    }
}
