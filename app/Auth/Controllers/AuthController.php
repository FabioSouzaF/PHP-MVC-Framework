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
        
        if ($user && password_verify($password, $user['password'])) {
            Session::set('user_id', $user['id']);
            
            // Tratamento do Lembrar-me
            if ($request->post('remember_me')) {
                $token = bin2hex(random_bytes(32));
                $userModel->updateRememberToken($user['id'], $token);
                // Cookie válido por 30 dias
                setcookie('remember_me', $token, time() + (86400 * 30), '/', '', false, true); 
            }

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
        $userId = Session::get('user_id');
        if ($userId) {
            $userModel = new User();
            $userModel->updateRememberToken($userId, null);
        }

        Session::destroy();
        Session::init(); 
        setcookie('remember_me', '', time() - 3600, '/'); // Limpa o cookie
        
        $this->flash('success', 'Você saiu da sua conta.');
        $this->redirect('/');
    }

    public function showForgotPassword(Request $request)
    {
        $this->render('Auth', 'auth/forgot_password', []);
    }

    public function sendResetLink(Request $request)
    {
        $email = $request->post('email');
        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', time() + 3600); // 1 hora de validade
            $userModel->setResetToken($email, $token, $expiresAt);

            $mailer = new \Core\Mail\Mailer();
            $resetUrl = "http://" . $_SERVER['HTTP_HOST'] . "/redefinir-senha/" . $token;
            $body = "Olá {$user['name']},<br><br>Você solicitou a redefinição de senha. Clique no link abaixo para criar uma nova senha:<br><br><a href='{$resetUrl}'>{$resetUrl}</a><br><br>Se você não solicitou, ignore este e-mail.";
            
            $mailer->send($email, 'Redefinição de Senha', $body);
        }

        // Retornamos mensagem de sucesso mesmo se o e-mail não existir por segurança
        $this->flash('success', 'Se o e-mail existir, você receberá um link para redefinir a senha.');
        $this->redirect('/login');
    }

    public function showResetPassword(Request $request, string $token)
    {
        $userModel = new User();
        $user = $userModel->findByResetToken($token);

        if (!$user) {
            $this->flash('error', 'Token inválido ou expirado.');
            $this->redirect('/esqueci-a-senha');
            return;
        }

        $this->render('Auth', 'auth/reset_password', ['token' => $token]);
    }

    public function processResetPassword(Request $request, string $token)
    {
        $password = $request->post('password');
        $password_confirmation = $request->post('password_confirmation');

        if ($password !== $password_confirmation) {
            $this->flash('error', 'As senhas não conferem.');
            // Precisa fazer o redirect mantendo o token
            $this->redirect('/redefinir-senha/' . $token);
            return;
        }

        $userModel = new User();
        $user = $userModel->findByResetToken($token);

        if (!$user) {
            $this->flash('error', 'Token inválido ou expirado.');
            $this->redirect('/esqueci-a-senha');
            return;
        }

        if ($userModel->updatePasswordAndClearResetToken($user['id'], $password)) {
            $this->flash('success', 'Senha redefinida com sucesso! Faça o login.');
            $this->redirect('/login');
        } else {
            $this->flash('error', 'Erro ao redefinir a senha.');
            $this->redirect('/redefinir-senha/' . $token);
        }
    }
}
