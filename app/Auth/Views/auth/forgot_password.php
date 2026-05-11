<?php
/**
 * @var string $title
 */
?>

<div style="max-width: 400px; margin: 50px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
    <h2>Esqueci minha senha</h2>
    
    <p style="color: #666; font-size: 14px;">
        Informe o seu e-mail abaixo. Se houver uma conta associada, você receberá um link para redefinir a senha.
    </p>

    <form action="/esqueci-a-senha" method="POST">
        <?= $csrf_field() ?>

        <div style="margin-bottom: 15px;">
            <label for="email" style="display: block; margin-bottom: 5px;">E-mail:</label>
            <input type="email" id="email" name="email" required style="width: 100%; padding: 8px; box-sizing: border-box;">
        </div>

        <button type="submit" style="width: 100%; padding: 10px; background-color: #007BFF; color: white; border: none; border-radius: 4px; cursor: pointer;">
            Enviar Link de Recuperação
        </button>
    </form>
    
    <div style="margin-top: 15px; text-align: center;">
        <a href="/login" style="color: #007BFF; text-decoration: none;">Voltar para o Login</a>
    </div>
</div>
