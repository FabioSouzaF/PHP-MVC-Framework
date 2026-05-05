<?php
// Trazemos os erros antigos salvos na sessão pelo Validator
$errors = \Core\Http\Session::getFlash('errors') ?? [];
$old = \Core\Http\Session::getFlash('old') ?? [];
?>
<div style="max-width: 500px; margin: 40px auto; padding: 30px; background: #fff; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
    <h2 style="margin-top: 0;">Teste do Validador</h2>
    <p style="color: #666; margin-bottom: 20px;">Tente enviar este formulário em branco ou com dados inválidos para ver o Validator interceptando a requisição e voltando para esta página com as mensagens de erro em vermelho!</p>

    <form action="/testes/submit" method="POST">
        <?= $csrf_field() ?>
        
        <div style="margin-bottom: 15px;">
            <label style="font-weight: bold; display: block; margin-bottom: 5px;">Nome (Mínimo 3 letras):</label>
            <input type="text" name="nome" value="<?= htmlspecialchars($old['nome'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
            <?php if (isset($errors['nome'])): ?>
                <div style="color: #d32f2f; font-size: 0.9em; margin-top: 5px;"><?= $errors['nome'] ?></div>
            <?php endif; ?>
        </div>

        <div style="margin-bottom: 15px;">
            <label style="font-weight: bold; display: block; margin-bottom: 5px;">Email (Formato Válido):</label>
            <input type="text" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
            <?php if (isset($errors['email'])): ?>
                <div style="color: #d32f2f; font-size: 0.9em; margin-top: 5px;"><?= $errors['email'] ?></div>
            <?php endif; ?>
        </div>

        <div style="margin-bottom: 25px;">
            <label style="font-weight: bold; display: block; margin-bottom: 5px;">Idade (Obrigatório):</label>
            <input type="number" name="idade" value="<?= htmlspecialchars($old['idade'] ?? '') ?>" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
            <?php if (isset($errors['idade'])): ?>
                <div style="color: #d32f2f; font-size: 0.9em; margin-top: 5px;"><?= $errors['idade'] ?></div>
            <?php endif; ?>
        </div>

        <button type="submit" style="width: 100%; padding: 12px; background: #007BFF; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">Testar Validação Automática</button>
    </form>
</div>
