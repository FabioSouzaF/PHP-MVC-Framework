<?php

namespace App\Auth\Controllers;

use Core\Controller;
use Core\Http\Request;
use App\Auth\Models\User;
use Core\Http\Session;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        $this->render('Auth', 'auth/login', []);
    }

    public function processLogin(Request $request)
    {
        $email = $request->post('email');
        $password = $request->post('password');

        $userModel = new User();
        $user = $userModel->findByEmail($email);
        error_log(password_hash($password, PASSWORD_DEFAULT));
        if ($user && password_verify($password, $user['password'])) {
            Session::set('user_id', $user['id']);
            $this->flash('success', 'Login realizado com sucesso!');
            $this->redirect('/');
        } else {
            $this->flash('error', 'Credenciais inválidas.');
            $this->redirect('/login');
        }
    }

    public function showRegister(Request $request)
    {
        $this->render('Auth', 'auth/register', []);
    }

    public function processRegister(Request $request)
    {
        $data = [
            'name' => $request->post('name'),
            'email' => $request->post('email'),
            'password' => $request->post('password')
        ];

        $userModel = new User();
        if ($userModel->create($data)) {
            $this->flash('success', 'Conta criada com sucesso! Faça login.');
            $this->redirect('/login');
        } else {
            $this->flash('error', 'Erro ao criar conta. Email já pode estar em uso.');
            $this->redirect('/register');
        }
    }

    public function logout(Request $request)
    {
        Session::destroy();
        Session::init(); 
        $this->flash('success', 'Você saiu da sua conta.');
        $this->redirect('/');
    }
}
