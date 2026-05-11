<?php

return new class {
    public function up(\PDO $db)
    {
        // Adiciona campos para "Lembrar-me" e "Esqueci a Senha"
        $db->exec("ALTER TABLE users 
            ADD COLUMN remember_token VARCHAR(255) NULL AFTER password,
            ADD COLUMN reset_token VARCHAR(255) NULL AFTER remember_token,
            ADD COLUMN reset_token_expires_at DATETIME NULL AFTER reset_token
        ");
    }

    public function down(\PDO $db)
    {
        $db->exec("ALTER TABLE users 
            DROP COLUMN remember_token,
            DROP COLUMN reset_token,
            DROP COLUMN reset_token_expires_at
        ");
    }
};
