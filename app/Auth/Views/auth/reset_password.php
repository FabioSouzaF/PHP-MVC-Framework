<?php
/**
 * @var string $token
 */
?>

<div style="max-width: 400px; margin: 50px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
    <h2>Redefinir Senha</h2>
    
    <p style="color: #666; font-size: 14px;">
        Digite sua nova senha abaixo.
    </p>

    <form action="/redefinir-senha/<?= htmlspecialchars($token) ?>" method="POST">
        <?= $csrf_field() ?>

        <div style="margin-bottom: 15px;">
            <label for="password" style="display: block; margin-bottom: 5px;">Nova Senha:</label>
            <input type="password" id="password" name="password" required style="width: 100%; padding: 8px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 15px;">
            <label for="password_confirmation" style="display: block; margin-bottom: 5px;">Confirme a Nova Senha:</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required style="width: 100%; padding: 8px; box-sizing: border-box;">
        </div>

        <button type="submit" style="width: 100%; padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;">
            Salvar Nova Senha
        </button>
    </form>
</div>
