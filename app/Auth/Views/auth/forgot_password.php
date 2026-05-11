<?php
/**
 * @var string $title
 */
?>

<div class="auth-wrapper">
    <div class="auth-card">
        <h2>Esqueci minha senha</h2>
        <p class="subtitle">
            Informe o seu e-mail abaixo. Se houver uma conta associada, enviaremos um link de recuperação.
        </p>

        <form action="/esqueci-a-senha" method="POST">
            <?= $csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="email">E-mail cadastrado</label>
                <input type="email" id="email" name="email" class="form-control" required autofocus>
            </div>

            <button type="submit" class="btn btn-primary mt-3">
                Enviar Link de Recuperação
            </button>
        </form>

        <div class="text-center text-sm mt-3">
            <a href="/login" class="link">Voltar para o Login</a>
        </div>
    </div>
</div>