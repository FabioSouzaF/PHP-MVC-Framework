<?php
use Core\Http\Session;
$errors = Session::getFlash('errors') ?? [];
$old = Session::getFlash('old') ?? [];
?>
<div class="card card-sm">
    <h2 class="card-title">Teste do Validador</h2>
    <p class="text-muted" style="margin-bottom: 1.5rem;">
        Tente enviar este formulário em branco ou com dados inválidos para ver o Validator interceptando a requisição e
        voltando com as mensagens de erro!
    </p>

    <form action="/testes/submit" method="POST">
        <?= $csrf_field() ?>

        <div class="form-group">
            <label class="form-label">Nome (Mínimo 3 letras):</label>
            <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($old['nome'] ?? '') ?>">
            <?php if (isset($errors['nome'])): ?>
                <span class="text-error"><?= $errors['nome'] ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label class="form-label">Email (Formato Válido):</label>
            <input type="text" name="email" class="form-control" value="<?= htmlspecialchars($old['email'] ?? '') ?>">
            <?php if (isset($errors['email'])): ?>
                <span class="text-error"><?= $errors['email'] ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group" style="margin-bottom: 2rem;">
            <label class="form-label">Idade (Obrigatório):</label>
            <input type="number" name="idade" class="form-control" value="<?= htmlspecialchars($old['idade'] ?? '') ?>">
            <?php if (isset($errors['idade'])): ?>
                <span class="text-error"><?= $errors['idade'] ?></span>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary">Testar Validação Automática</button>
    </form>
</div>