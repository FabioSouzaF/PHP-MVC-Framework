<div style="max-width: 800px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
    <h2 style="margin-top: 0;">Teste de Paginação Nativa</h2>
    <p style="color: #666;">
        Temos um total de <strong><?= $paginacao['total'] ?> usuários</strong> listados aqui. <br>
        O <code>Model->paginate()</code> está separando eles de <strong><?= $paginacao['per_page'] ?> em <?= $paginacao['per_page'] ?></strong>.
    </p>

    <table border="1" cellpadding="12" cellspacing="0" style="width: 100%; border-collapse: collapse; margin: 20px 0;">
        <thead style="background: #f4f4f4;">
            <tr>
                <th style="text-align: left; border: 1px solid #ddd;">ID</th>
                <th style="text-align: left; border: 1px solid #ddd;">Nome</th>
                <th style="text-align: left; border: 1px solid #ddd;">Email</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($paginacao['data'] as $user): ?>
            <tr>
                <td style="border: 1px solid #ddd;"><?= $user['id'] ?></td>
                <td style="border: 1px solid #ddd;"><?= htmlspecialchars($user['name']) ?></td>
                <td style="border: 1px solid #ddd;"><?= htmlspecialchars($user['email']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Controles de Paginação -->
    <div style="display: flex; gap: 8px; justify-content: center; margin-top: 30px;">
        <?php for ($i = 1; $i <= $paginacao['last_page']; $i++): ?>
            <a href="?page=<?= $i ?>" style="
                padding: 8px 16px; 
                border: 1px solid #007BFF; 
                border-radius: 4px;
                text-decoration: none; 
                font-weight: bold;
                color: <?= $i === $paginacao['current_page'] ? '#fff' : '#007BFF' ?>; 
                background: <?= $i === $paginacao['current_page'] ? '#007BFF' : '#fff' ?>;
            ">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
</div>
