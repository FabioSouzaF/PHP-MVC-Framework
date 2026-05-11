<div style="max-width: 900px; margin: 40px auto; padding: 30px; background: #fff; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
    <h2 style="margin-top: 0;">Teste de DTOs Tipados</h2>
    <p style="color: #555;">
        Os dados abaixo foram buscados via <code>$userModel->findAllAsDTO()</code>, que usa o novo método <code>Model::fetchAs()</code>.<br>
        Em vez de arrays anônimos <code>array&lt;string, mixed&gt;</code>, cada linha é um objeto <strong>UserDTO</strong> com propriedades <code>readonly</code> e tipadas.
    </p>

    <div style="background: #f8f9fa; border-left: 4px solid #007BFF; padding: 12px 16px; margin: 20px 0; border-radius: 4px; font-family: monospace; font-size: 0.9em;">
        <strong>Antes (array):</strong> &nbsp; <span style="color:#c0392b">$user['naem']</span> → erro silencioso em produção<br>
        <strong>Agora (DTO):</strong> &nbsp;&nbsp;&nbsp; <span style="color:#27ae60">$user->name</span> → autocomplete + erro imediato no editor
    </div>

    <p style="color: #666;"><strong>Total de usuários retornados como DTOs:</strong> <?= count($users) ?></p>

    <table border="1" cellpadding="12" cellspacing="0" style="width: 100%; border-collapse: collapse; margin: 10px 0;">
        <thead style="background: #343a40; color: white;">
            <tr>
                <th style="text-align: left; border: 1px solid #555;">$user->id <small style="font-weight:normal">(int)</small></th>
                <th style="text-align: left; border: 1px solid #555;">$user->name <small style="font-weight:normal">(string)</small></th>
                <th style="text-align: left; border: 1px solid #555;">$user->email <small style="font-weight:normal">(string)</small></th>
                <th style="text-align: left; border: 1px solid #555;">$user->created_at <small style="font-weight:normal">(string)</small></th>
                <th style="text-align: left; border: 1px solid #555;">Tipo da variável</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td style="border: 1px solid #ddd;"><?= $user->id ?></td>
                <td style="border: 1px solid #ddd;"><?= htmlspecialchars($user->name) ?></td>
                <td style="border: 1px solid #ddd;"><?= htmlspecialchars($user->email) ?></td>
                <td style="border: 1px solid #ddd;"><?= htmlspecialchars($user->created_at) ?></td>
                <td style="border: 1px solid #ddd; font-family: monospace; color: #007BFF;"><?= get_class($user) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div style="margin-top: 20px; padding: 12px 16px; background: #e8f5e9; border-radius: 4px; border-left: 4px solid #27ae60;">
        <strong>✅ Propriedades são readonly:</strong> nenhum código externo pode modificar os dados do DTO após a criação, garantindo imutabilidade e segurança.
    </div>
</div>
