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

    public function validate(array $rules): array
    {
        $data = $this->all();
        $errors = [];

        foreach ($rules as $field => $ruleString) {
            $value = $data[$field] ?? null;
            $fieldRules = explode('|', $ruleString);

            foreach ($fieldRules as $rule) {
                if ($rule === 'required' && (empty($value) && $value !== '0')) {
                    $errors[$field] = "O campo {$field} é obrigatório.";
                }
                if ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = "O campo {$field} deve ser um e-mail válido.";
                }
                if (strpos($rule, 'min:') === 0) {
                    $min = (int) substr($rule, 4);
                    if (strlen($value ?? '') < $min) {
                        $errors[$field] = "O campo {$field} deve ter no mínimo {$min} caracteres.";
                    }
                }
                if (strpos($rule, 'max:') === 0) {
                    $max = (int) substr($rule, 4);
                    if (strlen($value ?? '') > $max) {
                        $errors[$field] = "O campo {$field} deve ter no máximo {$max} caracteres.";
                    }
                }
                if ($rule === 'numeric' && !is_numeric($value) && $value !== null && $value !== '') {
                    $errors[$field] = "O campo {$field} deve ser um número.";
                }
                if ($rule === 'integer' && filter_var($value, FILTER_VALIDATE_INT) === false && $value !== null && $value !== '') {
                    $errors[$field] = "O campo {$field} deve ser um número inteiro.";
                }
                if ($rule === 'alpha_num' && !ctype_alnum((string)$value) && $value !== null && $value !== '') {
                    $errors[$field] = "O campo {$field} deve conter apenas letras e números.";
                }
            }
        }

        if (!empty($errors)) {
            throw new \Core\Exceptions\ValidationException($errors);
        }

        // Retorna apenas os dados validados (segurança contra mass assignment)
        $validated = [];
        foreach ($rules as $field => $rule) {
            $validated[$field] = $data[$field] ?? null;
        }
        
        return $validated;
    }
}
