<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modelo MVC</title>
    <link rel="stylesheet" href="/style.css">
    <style>
        /* Reset e Variáveis Básicas */
        :root {
            --primary-color: #2563eb;
            --primary-hover: #1d4ed8;
            --success-color: #10b981;
            --success-hover: #059669;
            --bg-color: #f3f4f6;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --border-color: #d1d5db;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--bg-color);
            color: var(--text-main);
        }

        /* Navbar Refinada */
        nav {
            background: #111827;
            padding: 0 2rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* O sinal ">" garante que afeta apenas o primeiro UL (menu principal) */
        nav>ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            height: 60px;
        }

        nav>ul>li {
            position: relative;
        }

        nav ul li a {
            color: #d1d5db;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: all 0.2s;
            font-weight: 500;
        }

        nav ul li a:hover {
            color: #fff;
            background-color: #374151;
        }

        /* Submenu Corrigido */
        nav ul li ul {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background: #1f2937;
            min-width: 220px;
            /* Aumentado para caber textos maiores */
            height: auto;
            /* Garante que o fundo acompanhe o conteúdo */
            flex-direction: column;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border-radius: 0 0 6px 6px;
            padding: 0.5rem 0;
            z-index: 10;
        }

        nav ul li:hover>ul {
            display: flex;
        }

        nav ul li ul li {
            display: block;
            width: 100%;
        }

        nav ul li ul li a {
            border-radius: 0;
            padding: 0.75rem 1.5rem;
            display: block;
            white-space: nowrap;
            /* Impede que o texto quebre em duas linhas */
        }

        /* Container Principal */
        main {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Alertas / Flash Messages */
        .alert {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 6px;
            font-weight: 500;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* --- UI Components para Autenticação --- */
        .auth-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 150px);
        }

        .auth-card {
            background: #fff;
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .auth-card h2 {
            margin-top: 0;
            margin-bottom: 0.5rem;
            font-size: 1.5rem;
            color: #111827;
            text-align: center;
        }

        .auth-card p.subtitle {
            text-align: center;
            color: var(--text-muted);
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            margin-top: 0;
        }

        /* Formulários */
        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            font-size: 0.875rem;
            color: #374151;
        }

        .form-control {
            width: 100%;
            padding: 0.625rem;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 1rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
        }

        /* Botões */
        .btn {
            display: inline-block;
            width: 100%;
            padding: 0.75rem;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            text-align: center;
            transition: background-color 0.2s;
            box-sizing: border-box;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: #fff;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
        }

        .btn-success {
            background-color: var(--success-color);
            color: #fff;
        }

        .btn-success:hover {
            background-color: var(--success-hover);
        }

        /* Utilitários */
        .flex-between {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .text-sm {
            font-size: 0.875rem;
        }

        .link {
            color: var(--primary-color);
            text-decoration: none;
        }

        .link:hover {
            text-decoration: underline;
        }

        .text-center {
            text-align: center;
        }

        .mt-3 {
            margin-top: 1rem;
        }

        /* Cards de Conteúdo */
        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin: 2rem auto;
            max-width: 900px;
        }

        .card-sm {
            max-width: 500px;
        }

        .card-title {
            margin-top: 0;
            margin-bottom: 0.5rem;
            color: #111827;
            font-size: 1.5rem;
        }

        .text-muted {
            color: var(--text-muted);
        }

        /* Tabelas */
        .table-responsive {
            overflow-x: auto;
            margin: 1.5rem 0;
            border-radius: 6px;
            border: 1px solid var(--border-color);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            margin: 0;
        }

        .table th,
        .table td {
            padding: 0.875rem 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .table th {
            background-color: #f9fafb;
            color: #374151;
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .table tr:last-child td {
            border-bottom: none;
        }

        .table tr:hover {
            background-color: #f9fafb;
        }

        /* Monospace / Tags */
        .font-mono {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        }

        .text-primary {
            color: var(--primary-color);
        }

        /* Blocos de Informação (Info Boxes) */
        .info-box {
            padding: 1rem 1.25rem;
            border-radius: 6px;
            margin: 1.5rem 0;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .info-blue {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            color: #1e3a8a;
        }

        .info-green {
            background: #ecfdf5;
            border-left: 4px solid #10b981;
            color: #065f46;
        }

        /* Blocos de Código Escuro */
        .code-block {
            background: #1e293b;
            color: #e2e8f0;
            padding: 1.25rem;
            border-radius: 6px;
            font-family: ui-monospace, Consolas, monospace;
            overflow-x: auto;
            margin: 1.5rem 0;
            line-height: 1.6;
        }

        .code-var {
            color: #c678dd;
        }

        .code-class {
            color: #e5c07b;
        }

        .code-string {
            color: #98c379;
        }

        .code-comment {
            color: #7f848e;
        }

        .code-number {
            color: #d19a66;
        }

        /* Paginação Nativa */
        .pagination {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
            margin-top: 2rem;
        }

        .pagination a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2rem;
            padding: 0.5rem 0.75rem;
            border: 1px solid var(--primary-color);
            border-radius: 4px;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
        }

        .pagination a.active {
            background: var(--primary-color);
            color: #fff;
        }

        .pagination a:hover:not(.active) {
            background: #eff6ff;
        }

        /* Erros de Formulário */
        .text-error {
            color: #dc2626;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: block;
        }
    </style>
</head>

<body>
    <nav>
        <ul>
            <li><a href="/">Home</a></li>
            <?php if (\Core\Http\Session::get('user_id')): ?>
                <li>
                    <a href="#" style="cursor: default;">Testes ▾</a>
                    <ul>
                        <li><a href="/testes/erro">Erro Fatal</a></li>
                        <li><a href="/testes/form">Formulário</a></li>
                        <li><a href="/testes/paginacao">Paginação</a></li>
                        <li><a href="/testes/dtos">DTOs Tipados</a></li>
                        <li><a href="/testes/orm">ORM (ActiveRecord)</a></li>
                    </ul>
                </li>
                <li style="margin-left: auto;"><a href="/logout">Sair</a></li>
            <?php else: ?>
                <li style="margin-left: auto;"><a href="/login">Login</a></li>
                <li><a href="/register">Registrar</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <main>
        <?php if ($msg = \Core\Http\Session::getFlash('success')): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>

        <?php if ($msg = \Core\Http\Session::getFlash('error')): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>

        <?= $content ?? '' ?>
    </main>
</body>

</html>