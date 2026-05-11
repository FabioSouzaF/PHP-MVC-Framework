<div class="auth-wrapper">
    <div class="auth-card">
        <h2>Bem-vindo de volta</h2>
        <p class="subtitle">Faça login para acessar sua conta</p>

        <form action="/login" method="POST">
            <?= $csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="email">E-mail</label>
                <input type="email" id="email" name="email" class="form-control" required autofocus>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Senha</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <div class="form-group flex-between text-sm">
                <label style="cursor: pointer;">
                    <input type="checkbox" name="remember_me" value="1"> Lembrar de mim
                </label>
                <a href="/esqueci-a-senha" class="link">Esqueceu a senha?</a>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Entrar</button>
        </form>

        <div class="text-center text-sm mt-3">
            Não tem uma conta? <a href="/register" class="link">Registre-se</a>
        </div>
    </div>
</div>