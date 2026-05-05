# Modelo MVC Genérico em PHP

Um micro-framework MVC moderno, leve e modular, construído com PHP puro. Projetado para servir como um *template base* ágil para iniciar novos projetos, oferecendo recursos de segurança e arquitetura vistos em frameworks robustos (como Laravel), porém mantendo a simplicidade.

## Recursos Integrados

- **Arquitetura Modular:** Separação limpa de responsabilidades (ex: `app/Site`, `app/Auth`).
- **Roteamento Dinâmico:** Suporte a métodos HTTP, rotas por arquivo e injeção de parâmetros de URL.
- **Middlewares:** Proteja suas rotas e execute lógicas antes do Controller.
- **Classe Request:** Abstração completa de globais (`$_POST`, `$_GET`, etc.).
- **Proteção CSRF Nativa:** Formulários seguros por padrão via Middleware Global.
- **Sessões e Flash Messages:** Gerenciamento elegante de feedback para o usuário.
- **Herança de Views:** Sistema inteligente de layouts globais ou específicos de módulo.

---

## 📁 Estrutura de Diretórios

```text
├── app/                  # Seus módulos (Ex: Site, Auth, Painel)
│   ├── Auth/
│   │   ├── Controllers/
│   │   ├── Middlewares/
│   │   ├── Models/
│   │   └── Views/
│   ├── Shared/           # Arquivos compartilhados (Layouts globais, Middlewares globais)
│   └── Site/
├── core/                 # Núcleo do Framework (NÃO EDITAR)
│   ├── Database/         # Acesso a Dados (Database.php, Model.php)
│   ├── Http/             # Request, Router, Session, Middleware
│   ├── Utils/            # Ferramentas auxiliares (Env, Funcoes, etc)
│   ├── View/             # Motor de Renderização
│   ├── Application.php   # Inicializador Principal
│   └── Controller.php    # Classe Base dos Controllers
├── public/               # Ponto de entrada (Document Root)
│   └── index.php
└── routes/               # Arquivos de Rotas (*.php carregados automaticamente)
```

---

## 🚀 Como Usar

### 1. Criando Rotas
As rotas são definidas dentro de arquivos `.php` na pasta `/routes`. Todos os arquivos nessa pasta são carregados automaticamente.

```php
// routes/site.php
<?php
use App\Site\Controllers\HomeController;
use App\Auth\Middlewares\AuthMiddleware;

/** @var \Core\Router $router */

// Rota GET simples
$router->get('/', HomeController::class, 'index');

// Rota POST com parâmetros na URL (ex: /produto/15)
$router->post('/produto/{id}', HomeController::class, 'atualizar');

// Rota Protegida por Middleware
$router->get('/painel', HomeController::class, 'painel')->middleware(AuthMiddleware::class);
```

### 2. Criando Controllers
Todo controller deve herdar de `Core\Controller`. O objeto `Request` é injetado automaticamente nas funções para capturar dados.

```php
namespace App\Site\Controllers;

use Core\Controller;
use Core\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Resgatando dados (Substitui $_GET e $_POST)
        $termo = $request->get('busca');
        $email = $request->post('email');

        // Retornando uma View
        $this->render('Site', 'home/index', ['title' => 'Página Inicial']);
    }
}
```
**Métodos úteis do Controller:**
- `$this->render('Modulo', 'caminho/view', $dados)`: Renderiza tela html.
- `$this->json($array)`: Retorna uma resposta JSON de API.
- `$this->redirect('/url')`: Redireciona o usuário.
- `$this->flash('chave', 'mensagem')`: Define uma notificação Flash temporária.

### 3. Criando Views e Layouts
As views ficam em `app/Modulo/Views`. Por padrão, a função `$this->render()` sempre tenta injetar a view dentro do layout global localizado em `app/Shared/Views/layouts/default.php`.

Para imprimir variáveis, use o PHP puro. E para proteger os seus formulários POST, sempre use a função injetada `$csrf_field()`:

```php
<!-- app/Auth/Views/auth/login.php -->
<h2>Login</h2>
<form action="/login" method="POST">
    <!-- Gera o token seguro automaticamente -->
    <?php echo $csrf_field(); ?> 
    
    <label>Email:</label>
    <input type="email" name="email" required>
    
    <button type="submit">Entrar</button>
</form>
```

### 4. Flash Messages (Notificações)
Para exibir mensagens de sucesso ou erro (ex: após um login falhar), faça no controller:
```php
$this->flash('error', 'Credenciais inválidas!');
$this->redirect('/login');
```
E as exiba no seu layout (`default.php`):
```php
<?php if ($msg = \Core\Http\Session::getFlash('error')): ?>
    <div class="alerta-erro">
        <?php echo htmlspecialchars($msg); ?>
    </div>
<?php endif; ?>
```

### 5. Middlewares
Middlewares funcionam como "guarda-costas" das rotas.
O **CsrfMiddleware** já vem ativado globalmente pelo roteador para todas as rotas de alteração (`POST`, `PUT`, `DELETE`).

Para criar um novo Middleware (ex: checar se é admin), crie uma classe implementando `Core\Middleware`:
```php
namespace App\Admin\Middlewares;

use Core\Http\Middleware;
use Core\Http\Request;

class AdminMiddleware implements Middleware
{
    public function handle(Request $request): bool
    {
        if (/* não é admin */) {
            header("Location: /acesso-negado");
            return false; // Interrompe a requisição
        }
        return true; // Permite passar para o Controller
    }
}
```

---

## 🚀 Iniciando um Novo Projeto

A forma mais rápida de iniciar um novo projeto a partir deste template é via linha de comando:

1. Clone o repositório sem o histórico de commits (mais leve):
```bash
git clone --depth=1 https://github.com/SeuUsuario/Modelo-MVC NovoProjeto
cd NovoProjeto
```

2. Inicialize o banco de dados e as configurações:
```bash
php console init
```
*O comando `init` irá copiar automaticamente o `.env.example` para `.env`, criar o banco de dados especificado e rodar as Migrations iniciais.*

---

## 🔧 Configurando Banco de Dados (Migrations)
O framework agora possui um sistema CLI de **Migrations** próprio para estruturar o banco de dados sem precisar ficar importando arquivos `.sql`.

As credenciais do banco devem ser inseridas no arquivo `.env` na raiz do projeto:

```env
DB_HOST=localhost
DB_NAME=modelo_mvc
DB_USER=root
DB_PASS=
```

### Criando o Banco (Executando Migrations)
Após configurar o `.env` e criar o database (o database vazio deve existir no SGBD), rode o seguinte comando no terminal na raiz do projeto:

```bash
php console migrate
```
Ele vai ler a pasta `database/migrations/` e criar as tabelas faltantes.

### Criando novas Migrations
Para criar uma nova tabela, use:
```bash
php console make:migration nome_da_sua_tabela
```
Isso gerará um arquivo na pasta `database/migrations/`. Abra-o e digite o SQL de criação dentro do método `up()`.

### Consultando Dados (Models)
A classe `Core\Database\Model` utiliza a conexão feita em `Core\Database\Database`.

```php
namespace App\Auth\Models;

use Core\Database\Model;
use PDO;

class User extends Model
{
    public function findByEmail(string $email)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
```

---

## 🏃 Iniciando o Projeto localmente
Para iniciar um servidor rápido pelo PHP sem depender de Apache ou Nginx, rode no terminal:
```bash
php -S localhost:8000 -t public
```
Acesse `http://localhost:8000` no navegador.
