<div class="auth-wrapper">
    <div class="auth-card">
        <h2>Criar Conta</h2>
        <p class="subtitle">Preencha os dados abaixo para se registrar</p>

        <form action="/register" method="POST">
            <?= $csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="name">Nome completo</label>
                <input type="text" id="name" name="name" class="form-control" required autofocus>
            </div>

            <div class="form-group">
                <label class="form-label" for="email">E-mail</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Senha</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Criar Conta</button>
        </form>

        <div class="text-center text-sm mt-3">
            Já possui uma conta? <a href="/login" class="link">Faça Login</a>
        </div>
    </div>
</div>