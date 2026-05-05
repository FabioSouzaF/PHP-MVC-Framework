<?php

namespace Core\Http;

class Request
{
    private array $getParams;
    private array $postParams;
    private array $serverParams;

    public function __construct()
    {
        $this->getParams = $_GET;
        $this->postParams = $_POST;
        $this->serverParams = $_SERVER;
    }

    public function get(string $key, $default = null)
    {
        return $this->getParams[$key] ?? $default;
    }

    public function post(string $key, $default = null)
    {
        return $this->postParams[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($this->getParams, $this->postParams);
    }

    public function method(): string
    {
        return strtoupper($this->serverParams['REQUEST_METHOD'] ?? 'GET');
    }

    public function uri(): string
    {
        return $this->serverParams['REQUEST_URI'] ?? '/';
    }

    public function getPath(): string
    {
        $path = strtok($this->uri(), '?');
        return '/' . ltrim($path, '/');
    }
}
