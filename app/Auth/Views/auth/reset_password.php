<?php
/**
 * @var string $token
 */
?>

<div class="auth-wrapper">
    <div class="auth-card">
        <h2>Redefinir Senha</h2>
        <p class="subtitle">Crie uma nova senha segura para sua conta.</p>

        <form action="/redefinir-senha/<?= htmlspecialchars($token) ?>" method="POST">
            <?= $csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="password">Nova Senha</label>
                <input type="password" id="password" name="password" class="form-control" required autofocus>
            </div>

            <div class="form-group">
                <label class="form-label" for="password_confirmation">Confirme a Nova Senha</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                    required>
            </div>

            <button type="submit" class="btn btn-success mt-3">
                Salvar Nova Senha
            </button>
        </form>
    </div>
</div>