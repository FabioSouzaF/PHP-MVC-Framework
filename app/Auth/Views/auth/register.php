<h2>Registro</h2>
<form action="/register" method="POST">
    <?php echo $csrf_field(); ?>
    <div style="margin-bottom: 10px;">
        <label>Nome:</label><br>
        <input type="text" name="name" required>
    </div>
    <div style="margin-bottom: 10px;">
        <label>Email:</label><br>
        <input type="email" name="email" required>
    </div>
    <div style="margin-bottom: 10px;">
        <label>Senha:</label><br>
        <input type="password" name="password" required>
    </div>
    <button type="submit">Criar Conta</button>
</form>
