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
    </style>
</head>
<body>
    <nav>
        <a href="/">Home</a>
        <?php if (\Core\Http\Session::get('user_id')): ?>
            <a href="/testes/erro">Erro</a>
            <a href="/testes/form">Formulário</a>
            <a href="/testes/submit">Submit</a>
            <a href="/testes/paginacao">Paginacao</a>

            <a href="/logout">Sair</a>
        <?php else: ?>
            <a href="/login">Login</a>
            <a href="/register">Registrar</a>
        <?php endif; ?>
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
