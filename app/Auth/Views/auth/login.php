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
    <div style="margin-bottom: 15px;">
        <label>
            <input type="checkbox" name="remember_me" value="1"> Lembrar de mim
        </label>
        <br>
        <a href="/esqueci-a-senha" style="font-size: 14px; color: #007BFF;">Esqueci minha senha</a>
    </div>
    <button type="submit">Entrar</button>
</form>
