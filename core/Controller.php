<?php

namespace Core;

use Core\View\View;
use Core\Http\Session;

abstract class Controller
{
    protected View $view;

    public function __construct()
    {
        $this->view = new View();
    }

    protected function render(string $module, string $viewPath, array $data = [], ?string $layout = 'default'): void
    {
        $this->view->render($module, $viewPath, $data, $layout);
    }

    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    protected function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }

    protected function flash(string $key, string $message): void
    {
        Session::setFlash($key, $message);
    }
}
