<?php

namespace App\Auth\Middlewares;

use Core\Http\Middleware;
use Core\Http\Request;
use Core\Http\Session;

class AuthMiddleware implements Middleware
{
    public function handle(Request $request): bool
    {
        // Verifica se o usuário está logado
        if (!Session::get('user_id')) {
            Session::setFlash('error', 'Acesso negado. Você precisa estar logado.');
            header("Location: /login");
            return false;
        }

        return true;
    }
}
