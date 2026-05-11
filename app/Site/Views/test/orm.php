<div class="card">
    <h2 class="card-title">Teste de ORM (ActiveRecord)</h2>
    <p class="text-muted">
        O ciclo de vida a seguir foi executado no <code class="font-mono text-primary">TestController::orm()</code> para demonstrar o ORM:
    </p>

    <div class="code-block">
        <span class="code-var">$user</span> = <span class="code-class">UserORM</span>::create([<br>
        &nbsp;&nbsp;&nbsp;&nbsp;<span class="code-string">'name'</span> => <span class="code-string">'Usuário ORM...'</span>,<br>
        &nbsp;&nbsp;&nbsp;&nbsp;<span class="code-string">'email'</span> => <span class="code-string">'orm@teste.com'</span><br>
        ]);<br><br>
        <span class="code-var">$user</span>->name = <span class="code-var">$user</span>->name . <span class="code-string">' (Editado)'</span>;<br>
        <span class="code-var">$user</span>->save(); <span class="code-comment">// Realizou um UPDATE</span><br><br>
        <span class="code-comment">// Buscou e converteu automaticamente para DTOs tipados</span><br>
        <span class="code-var">$dtos</span> = <span class="code-class">UserORM</span>::query()->orderBy(<span class="code-string">'id'</span>, <span class="code-string">'DESC'</span>)->limit(<span class="code-number">10</span>)->getAsDTO();<br><br>
        <span class="code-var">$user</span>->delete(); <span class="code-comment">// Deletou apenas o de teste</span>
    </div>

    <p><strong>Últimos 10 registros da tabela <code class="font-mono text-primary">users</code> convertidos para <code class="font-mono text-primary">UserDTO</code>:</strong></p>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dtos as $dto): ?>
                <tr>
                    <td><?= $dto->id ?></td>
                    <td><?= htmlspecialchars($dto->name) ?></td>
                    <td><?= htmlspecialchars($dto->email) ?></td>
                </tr>
                <?php endforeach; ?>
                
                <?php if(empty($dtos)): ?>
                    <tr><td colspan="3" class="text-center text-muted" style="padding: 2rem;">Nenhum usuário encontrado.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="info-box info-green">
        <strong>✅ Sucesso!</strong> O ORM lidou com toda a montagem das queries dinamicamente e evitou manipulação direta de arrays, integrando perfeitamente com os DTOs para renderização segura.
    </div>
</div>