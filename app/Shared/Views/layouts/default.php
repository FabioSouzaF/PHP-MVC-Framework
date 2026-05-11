<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modelo MVC</title>
    <link rel="stylesheet" href="/style.css">
    <style>
        body { font-family: sans-serif; margin: 0; padding: 0; }
        nav { padding: 1rem; background: #333; color: white; }
        nav a { color: white; text-decoration: none; margin-right: 15px; }
        nav a:hover { text-decoration: underline; }
        main { padding: 2rem; }

        /* Reseta a lista e coloca os itens principais em linha */
        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
        }

        /* Define posição relativa para ancorar o submenu */
        nav ul li {
            position: relative;
        }

        /* Botões do menu principal */
        nav ul li a {
            display: block;
            padding: 10px 15px;
            margin-right: 0;
        }

        /* Estilo da caixa do submenu */
        nav ul li ul {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background: #444;
            min-width: 160px;
            flex-direction: column;
            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
            border-radius: 0 0 5px 5px;
        }

        /* Mostra o submenu ao passar o mouse */
        nav ul li:hover > ul {
            display: flex;
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
                </ul>
            </li>
            <li><a href="/logout">Sair</a></li>
        <?php else: ?>
            <li><a href="/login">Login</a></li>
            <li><a href="/register">Registrar</a></li>
        <?php endif; ?>
        </ul>
    </nav>
    <main>
        <?php if ($msg = \Core\Http\Session::getFlash('success')): ?>
            <div style="background: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border-radius: 5px;">
                <?php echo htmlspecialchars($msg); ?>
            </div>
        <?php endif; ?>

        <?php if ($msg = \Core\Http\Session::getFlash('error')): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 15px; border-radius: 5px;">
                <?php echo htmlspecialchars($msg); ?>
            </div>
        <?php endif; ?>

        <?php echo $content ?? ''; ?>
    </main>
</body>
</html>
