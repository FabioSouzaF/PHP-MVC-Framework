<div style="max-width: 900px; margin: 40px auto; padding: 30px; background: #fff; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
    <h2 style="margin-top: 0;">Teste de ORM (ActiveRecord)</h2>
    <p style="color: #555;">
        O ciclo de vida a seguir foi executado no <code>TestController::orm()</code> para demonstrar o ORM:
    </p>

    <div style="background: #282c34; color: #abb2bf; border-radius: 6px; padding: 15px; font-family: monospace; font-size: 0.9em; margin-bottom: 20px;">
        <span style="color: #c678dd;">$user</span> = <span style="color: #e5c07b;">UserORM</span>::create([<br>
        &nbsp;&nbsp;&nbsp;&nbsp;<span style="color: #98c379;">'name'</span> => <span style="color: #98c379;">'Usuário ORM...'</span>,<br>
        &nbsp;&nbsp;&nbsp;&nbsp;<span style="color: #98c379;">'email'</span> => <span style="color: #98c379;">'orm...@teste.com'</span><br>
        ]);<br><br>
        <span style="color: #c678dd;">$user</span>->name = <span style="color: #c678dd;">$user</span>->name . <span style="color: #98c379;">' (Editado)'</span>;<br>
        <span style="color: #c678dd;">$user</span>->save(); <span style="color: #5c6370;">// Realizou um UPDATE</span><br><br>
        <span style="color: #5c6370;">// Buscou e converteu automaticamente para DTOs tipados</span><br>
        <span style="color: #c678dd;">$dtos</span> = <span style="color: #e5c07b;">UserORM</span>::query()->orderBy(<span style="color: #98c379;">'id'</span>, <span style="color: #98c379;">'DESC'</span>)->limit(<span style="color: #d19a66;">10</span>)->getAsDTO();<br><br>
        <span style="color: #c678dd;">$user</span>->delete(); <span style="color: #5c6370;">// Deletou apenas o de teste</span>
    </div>

    <p style="color: #666;"><strong>Últimos 10 registros da tabela <code>users</code> convertidos para <code>UserDTO</code>:</strong></p>

    <table border="1" cellpadding="12" cellspacing="0" style="width: 100%; border-collapse: collapse; margin: 10px 0;">
        <thead style="background: #343a40; color: white;">
            <tr>
                <th style="text-align: left; border: 1px solid #555;">ID</th>
                <th style="text-align: left; border: 1px solid #555;">Name</th>
                <th style="text-align: left; border: 1px solid #555;">Email</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($dtos as $dto): ?>
            <tr>
                <td style="border: 1px solid #ddd;"><?= $dto->id ?></td>
                <td style="border: 1px solid #ddd;"><?= htmlspecialchars($dto->name) ?></td>
                <td style="border: 1px solid #ddd;"><?= htmlspecialchars($dto->email) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($dtos)): ?>
                <tr><td colspan="3" style="text-align:center; padding:20px; color:#999;">Nenhum usuário encontrado.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div style="margin-top: 20px; padding: 12px 16px; background: #e8f5e9; border-radius: 4px; border-left: 4px solid #27ae60;">
        <strong>✅ Sucesso!</strong> O ORM lidou com toda a montagem das queries dinamicamente e evitou manipulação direta de arrays, integrando perfeitamente com os DTOs para renderização segura.
    </div>
</div>
