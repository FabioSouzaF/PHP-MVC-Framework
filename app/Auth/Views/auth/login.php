<h2>Login</h2>
<form action="/login" method="POST">
    <?php echo $csrf_field(); ?>
    <div style="margin-bottom: 10px;">
        <label>Email:</label><br>
        <input type="email" name="email" required>
    </div>
    <div style="margin-bottom: 10px;">
        <label>Senha:</label><br>
        <input type="password" name="password" required>
    </div>
    <button type="submit">Entrar</button>
</form>
