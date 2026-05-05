<?php

namespace App\Shared\Middlewares;

use Core\Http\Middleware;
use Core\Http\Request;
use Core\Http\Session;

class CsrfMiddleware implements Middleware
{
    public function handle(Request $request): bool
    {
        // Garante que existe um token na sessão
        if (!Session::get('csrf_token')) {
            Session::set('csrf_token', bin2hex(random_bytes(32)));
        }

        // Valida o token apenas em requisições que alteram estado
        if (in_array($request->method(), ['POST', 'PUT', 'DELETE'])) {
            $token = $request->post('csrf_token') ?? $request->get('csrf_token');
            
            if (!$token || !hash_equals(Session::get('csrf_token'), $token)) {
                http_response_code(403);
                echo "Erro 403: Token CSRF inválido ou ausente.";
                return false;
            }
        }

        return true;
    }
}
