# 🚀 Funcionalidades Futuras

Este documento lista as próximas evoluções planejadas para o **PHP-MVC-Framework**. Cada funcionalidade foi pensada para manter a filosofia do projeto: **leveza, modularidade e escolha do desenvolvedor**.

---

## ~~1. 🗂️ ORM Opcional (ActiveRecord Leve)~~ ✅ Já Implementado!

**Prioridade:** Alta | **Complexidade:** Média-Alta

O framework agora fornece a classe `Core\Database\ORM\ActiveRecord` para projetos que preferem não escrever SQL manualmente.
Foi também adicionado um comando ao console para gerar models via linha de comando:

```bash
php console make:model NomeDoModel --orm
```

Para mais detalhes e exemplos práticos, consulte o README.md.

---

## 2. 📦 Mais Regras de Validação

**Prioridade:** Média | **Complexidade:** Baixa

Expandir o `Request::validate()` com mais tipos de regras prontas para uso:

| Regra            | Descrição                                                                                |
| ---------------- | ---------------------------------------------------------------------------------------- |
| `confirmed`      | Verifica se o campo `{campo}_confirmation` tem o mesmo valor (ex: senha/confirmar senha) |
| `unique:tabela`  | Verifica se o valor ainda não existe no banco de dados                                   |
| `exists:tabela`  | Verifica se o valor existe no banco de dados                                             |
| `date`           | Valida se o valor é uma data válida                                                      |
| `url`            | Valida se o valor é uma URL válida                                                       |
| `in:a,b,c`       | Verifica se o valor está dentro de uma lista de opções                                   |
| `not_in:a,b,c`   | Verifica se o valor NÃO está dentro de uma lista de opções                               |
| `regex:/padrão/` | Valida o valor contra uma expressão regular customizada                                  |

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

## ~~7. 🧱 Data Transfer Objects (DTOs) — Dados Tipados do Banco~~ ✅ Já Implementado!

**Prioridade:** Média | **Complexidade:** Baixa-Média

Atualmente, as queries retornam **arrays anônimos** (`array<string, mixed>`), o que torna o código mais frágil: qualquer typo num nome de chave (`$user['naem']` em vez de `$user['name']`) só estoura em tempo de execução, e o editor não consegue te dar autocomplete.

A ideia é criar uma camada de **Data Transfer Objects (DTOs)** — classes simples e reutilizáveis que representam os dados de uma tabela de forma **tipada e concreta**.

### Como funcionaria:

O desenvolvedor criaria uma classe DTO para cada entidade, usando o PHP 8 `readonly` para garantir imutabilidade:

```php
// app/Auth/DTOs/UserDTO.php
namespace App\Auth\DTOs;

class UserDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly string $created_at,
    ) {}

    // Factory: converte um array bruto do banco em um DTO tipado
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) $data['id'],
            name: $data['name'],
            email: $data['email'],
            created_at: $data['created_at'],
        );
    }
}
```

O `Model` ganharia dois novos métodos opcionais: `fetchAs()` e `paginateAs()`, que convertem os resultados automaticamente:

```php
// No seu Model:
class User extends Model
{
    protected string $dtoClass = UserDTO::class; // opcional: define o DTO padrão

    public function findAll(): array
    {
        // Retorna um array de UserDTO em vez de um array de arrays
        return $this->fetchAs("SELECT * FROM users ORDER BY name", [], UserDTO::class);
    }
}

// No Controller, você agora tem segurança total de tipos:
$users = $userModel->findAll(); // array de UserDTO

foreach ($users as $user) {
    echo $user->name;       // ✅ Autocomplete no editor
    echo $user->naem;       // ❌ Erro em tempo de desenvolvimento, não em produção
}
```

### Princípio de Adoção:

- DTOs são **100% opcionais**. Quem não quiser, continua usando arrays normais.
- O método `fetchAs()` no Model aceitará qualquer classe com um método `static fromArray()`.
- Compatível tanto com SQL puro quanto com o futuro ORM.

---

## ~~8. 🧪 Testes Unitários (PHPUnit)~~ ✅ Já Implementado!

O PHPUnit 13 foi integrado como dependência de desenvolvimento. A suíte cobre o Validador, o Roteador e os DTOs.

```bash
# Instalar
composer install

# Rodar todos os testes
./vendor/bin/phpunit
```

Consulte o `README.md` para mais detalhes sobre como criar novos testes.

---

> 💡 **Nota:** Este arquivo é um documento vivo. Novas ideias podem ser adicionadas aqui a qualquer momento. As funcionalidades serão priorizadas conforme a necessidade dos projetos que utilizam este framework.
