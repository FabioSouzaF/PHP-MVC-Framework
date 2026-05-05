# 🚀 Funcionalidades Futuras

Este documento lista as próximas evoluções planejadas para o **PHP-MVC-Framework**. Cada funcionalidade foi pensada para manter a filosofia do projeto: **leveza, modularidade e escolha do desenvolvedor**.

---

## 1. 🗂️ ORM Opcional (ActiveRecord Leve)

**Prioridade:** Alta | **Complexidade:** Média-Alta

Atualmente o framework trabalha com **SQL puro + Prepared Statements**, o que oferece controle total e máxima performance. No entanto, em projetos maiores, um ORM pode acelerar muito o desenvolvimento.

A proposta é criar um **ORM opcional** que o usuário **escolhe ativar** por projeto — sem quebrar nada para quem prefere continuar usando SQL puro.

### Como funcionaria:

O `Core\Database\Model` atual continuará funcionando 100% como está. O ORM seria uma **classe separada** (`Core\Database\ORM\ActiveRecord`) que o usuário estende caso queira:

```php
// Sem ORM (comportamento atual, sempre disponível):
class UserModel extends \Core\Database\Model { ... }

// Com ORM ativado (opcional):
class UserModel extends \Core\Database\ORM\ActiveRecord
{
    protected string $table = 'users';

    // Pronto! Você já ganha os métodos abaixo automaticamente:
}

// Exemplos de uso:
$user = UserModel::find(1);                    // SELECT * FROM users WHERE id = 1
$users = UserModel::where('active', 1)->get(); // SELECT * FROM users WHERE active = 1
$user = UserModel::create(['name' => 'Fabio']);// INSERT INTO users ...
$user->update(['name' => 'Fabio S.']);         // UPDATE users SET ...
$user->delete();                               // DELETE FROM users WHERE id = ...
```

### Princípio de Adoção:
- O ORM **nunca será forçado**. Quem usar `Model` puro não perceberá diferença.
- A documentação deixará explícito: *"Se quiser agilidade, use ORM. Se quiser controle total, use SQL puro."*
- Ambas as abordagens poderão coexistir no mesmo projeto.

---

## 2. 📦 Mais Regras de Validação

**Prioridade:** Média | **Complexidade:** Baixa

Expandir o `Request::validate()` com mais tipos de regras prontas para uso:

| Regra | Descrição |
|---|---|
| `confirmed` | Verifica se o campo `{campo}_confirmation` tem o mesmo valor (ex: senha/confirmar senha) |
| `unique:tabela` | Verifica se o valor ainda não existe no banco de dados |
| `exists:tabela` | Verifica se o valor existe no banco de dados |
| `date` | Valida se o valor é uma data válida |
| `url` | Valida se o valor é uma URL válida |
| `in:a,b,c` | Verifica se o valor está dentro de uma lista de opções |
| `not_in:a,b,c` | Verifica se o valor NÃO está dentro de uma lista de opções |
| `regex:/padrão/` | Valida o valor contra uma expressão regular customizada |

---

## 3. 🔑 Sistema de Autenticação Completo

**Prioridade:** Alta | **Complexidade:** Média

Disponibilizar um módulo `Auth` mais completo incluindo:
- **"Lembrar de mim"**: persistência de sessão via cookie seguro.
- **Reset de Senha**: fluxo de e-mail com token temporário.
- **Verificação de E-mail**: envio de link de confirmação após o registro.
- **Throttling de Login**: bloqueio temporário após X tentativas falhas.

---

## ~~4. 🌐 Suporte a Respostas JSON (API Mode)~~ ✅ Já Implementado!

O método `$this->json()` já está disponível em todos os Controllers do framework.

```php
// Dentro de qualquer Controller:
$this->json(['status' => 'ok', 'data' => $users], 200);
```

Para agrupar rotas de API com prefixo você já pode usar o `group()` existente:

```php
$router->group(['prefix' => '/api/v1'], function ($router) {
    $router->get('/users', UserApiController::class, 'index');
});
```

---

## 5. 📧 Serviço de E-mail

**Prioridade:** Média | **Complexidade:** Média

Uma classe `Core\Mail\Mailer` que encapsula o envio de e-mails via SMTP (usando a biblioteca nativa PHPMailer), configurável pelo `.env`:

```env
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USER=seu@email.com
MAIL_PASS=sua_senha
MAIL_FROM_NAME="PHP MVC Framework"
```

```php
\Core\Mail\Mailer::to('usuario@email.com')
    ->subject('Bem-vindo!')
    ->view('Auth', 'emails/welcome', ['name' => $user['name']])
    ->send();
```

---

## 6. ⚡ Cache Simples

**Prioridade:** Baixa | **Complexidade:** Baixa

Um sistema de cache em arquivo para resultados de queries custosas:

```php
$users = \Core\Cache\Cache::remember('all_users', 300, function() {
    return $this->paginate("SELECT * FROM users");
});
// Armazena o resultado por 300 segundos. Na próxima chamada, lê do cache.
```

---

> 💡 **Nota:** Este arquivo é um documento vivo. Novas ideias podem ser adicionadas aqui a qualquer momento. As funcionalidades serão priorizadas conforme a necessidade dos projetos que utilizam este framework.
