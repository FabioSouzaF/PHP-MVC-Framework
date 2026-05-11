<div class="card">
    <h2 class="card-title">Teste de DTOs Tipados</h2>
    <p class="text-muted">
        Os dados abaixo foram buscados via <code class="font-mono text-primary">$userModel->findAllAsDTO()</code>, que
        usa o novo método <code class="font-mono text-primary">Model::fetchAs()</code>.<br>
        Em vez de arrays anônimos <code class="font-mono">array&lt;string, mixed&gt;</code>, cada linha é um objeto
        <strong>UserDTO</strong> com propriedades <code class="font-mono">readonly</code> e tipadas.
    </p>

    <div class="info-box info-blue font-mono">
        <strong>Antes (array):</strong> &nbsp; <span style="color:#dc2626">$user['naem']</span> → erro silencioso em
        produção<br>
        <strong>Agora (DTO):</strong> &nbsp;&nbsp;&nbsp; <span style="color:#059669">$user->name</span> → autocomplete +
        erro imediato no editor
    </div>

    <p><strong>Total de usuários retornados como DTOs:</strong> <?= count($users) ?></p>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>$user->id <span class="text-muted text-sm">(int)</span></th>
                    <th>$user->name <span class="text-muted text-sm">(string)</span></th>
                    <th>$user->email <span class="text-muted text-sm">(string)</span></th>
                    <th>$user->created_at <span class="text-muted text-sm">(string)</span></th>
                    <th>Tipo da variável</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user->id ?></td>
                        <td><?= htmlspecialchars($user->name) ?></td>
                        <td><?= htmlspecialchars($user->email) ?></td>
                        <td><?= htmlspecialchars($user->created_at) ?></td>
                        <td class="font-mono text-primary"><?= get_class($user) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="info-box info-green">
        <strong>✅ Propriedades são readonly:</strong> nenhum código externo pode modificar os dados do DTO após a
        criação, garantindo imutabilidade e segurança.
    </div>
</div>