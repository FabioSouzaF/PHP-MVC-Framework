<?php

return new class {
    public function up(\PDO $db)
    {
        $db->exec("
            CREATE TABLE IF NOT EXISTS `users` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(255) NOT NULL,
                `email` VARCHAR(191) NOT NULL UNIQUE,
                `password` VARCHAR(255) NOT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Inserir usuário de teste padrão (Senha: 123456)
        $db->exec("
            INSERT IGNORE INTO `users` (`name`, `email`, `password`) VALUES 
            ('Usuário Teste', 'teste@teste.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
        ");
    }

    public function down(\PDO $db)
    {
        $db->exec("DROP TABLE IF NOT EXISTS `users`;");
    }
};
