<div class="card">
    <h2 class="card-title">Teste de Paginação Nativa</h2>
    <p class="text-muted">
        Temos um total de <strong><?= $paginacao['total'] ?> usuários</strong> listados aqui. <br>
        O <code class="font-mono text-primary">Model->paginate()</code> está separando eles de
        <strong><?= $paginacao['per_page'] ?> em <?= $paginacao['per_page'] ?></strong>.
    </p>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($paginacao['data'] as $user): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="pagination">
        <?php for ($i = 1; $i <= $paginacao['last_page']; $i++): ?>
            <a href="?page=<?= $i ?>" class="<?= $i === $paginacao['current_page'] ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
</div>