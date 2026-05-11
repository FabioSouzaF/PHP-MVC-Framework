<?php

namespace App\Site\Controllers;

use Core\Controller;
use Core\Http\Request;
use App\Auth\Models\User;

class TestController extends Controller
{
    /**
     * Testa o Error Handler Global
     */
    public function error()
    {
        // Forçaremos uma divisão por zero para disparar um Fatal Error (DivisionByZeroError)
        $a = 10;
        $b = 0;
        return $a / $b;
    }

    /**
     * Renderiza o formulário para teste do Validator
     */
    public function form()
    {
        $this->render('Site', 'test/form', ['title' => 'Teste do Validador']);
    }

    /**
     * Recebe os dados e testa o Validator
     */
    public function submit(Request $request)
    {
        $dados = $request->validate([
            'nome' => 'required|min:3',
            'email' => 'required|email',
            'idade' => 'required'
        ]);

        // Se chegar aqui, a validação passou perfeitamente!
        $this->flash('success', "Validação concluída com sucesso! Nome: {$dados['nome']}");
        $this->redirect('/testes/form');
    }

    /**
     * Testa a Paginação Nativa
     */
    public function pagination()
    {
        $userModel = new User();
        $db = (new \Core\Database\Database())->getConnection();

        // 1. Injeta usuários fakes caso o banco esteja muito vazio
        $countStmt = $db->query("SELECT COUNT(*) FROM users");
        if ($countStmt->fetchColumn() < 20) {
            for ($i = 1; $i <= 25; $i++) {
                $userModel->create([
                    'name' => "Usuário de Teste $i",
                    'email' => "fake$i@teste.com",
                    'password' => '123'
                ]);
            }
        }

        // 2. Testa o método paginate na vida real
        $resultado = $userModel->paginate("SELECT * FROM users ORDER BY id DESC", [], 5); // 5 por página para forçar a criação dos botões

        $this->render('Site', 'test/pagination', [
            'title' => 'Teste de Paginação',
            'paginacao' => $resultado
        ]);
    }

    /**
     * Demonstra o uso de DTOs tipados
     */
    public function dtos()
    {
        $userModel = new User();

        // fetchAs() retorna um array de UserDTO em vez de arrays anônimos
        $users = $userModel->findAllAsDTO();

        $this->render('Site', 'test/dtos', [
            'title' => 'Teste de DTOs Tipados',
            'users' => $users
        ]);
    }

    /**
     * Demonstra o uso do ORM (ActiveRecord)
     */
    public function orm()
    {
        // 1. Inserção (Create)
        $novoUsuario = \App\Auth\Models\UserORM::create([
            'name' => 'Usuário ORM ' . rand(100, 999),
            'email' => 'orm' . rand(100, 999) . '@teste.com',
            'password' => password_hash('123456', PASSWORD_DEFAULT)
        ]);

        // 2. Atualização (Update)
        $novoUsuario->name = $novoUsuario->name . ' (Editado)';
        $novoUsuario->save();

        // 3. Busca usando Query Builder e convertendo para DTO
        $dtos = \App\Auth\Models\UserORM::query()
            ->orderBy('id', 'DESC')
            ->limit(10)
            ->getAsDTO();

        // 4. Deleção (apenas do que acabamos de criar para não sujar o banco)
        $novoUsuario->delete();

        $this->render('Site', 'test/orm', [
            'title' => 'Teste de ORM (ActiveRecord)',
            'dtos' => $dtos
        ]);
    }
}
