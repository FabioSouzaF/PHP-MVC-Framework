<?php

namespace Core\Mail;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    private PHPMailer $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);

        // Configurações do servidor
        $this->mail->isSMTP();
        $this->mail->Host       = $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com';
        $this->mail->SMTPAuth   = true;
        $this->mail->Username   = $_ENV['MAIL_USER'] ?? '';
        $this->mail->Password   = $_ENV['MAIL_PASS'] ?? '';
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port       = $_ENV['MAIL_PORT'] ?? 587;
        $this->mail->CharSet    = 'UTF-8';

        $fromEmail = $_ENV['MAIL_FROM_EMAIL'] ?? 'no-reply@seudominio.com';
        $fromName  = $_ENV['MAIL_FROM_NAME'] ?? 'PHP MVC Framework';
        
        $this->mail->setFrom($fromEmail, $fromName);
        $this->mail->isHTML(true);
    }

    /**
     * Envia um e-mail.
     * Se a variável MAIL_DRIVER=log estiver no .env, não envia o e-mail, apenas salva no log local.
     */
    public function send(string $to, string $subject, string $body): bool
    {
        $driver = $_ENV['MAIL_DRIVER'] ?? 'smtp';

        if ($driver === 'log') {
            return $this->logEmail($to, $subject, $body);
        }

        try {
            $this->mail->addAddress($to);
            $this->mail->Subject = $subject;
            $this->mail->Body    = $body;

            return $this->mail->send();
        } catch (Exception $e) {
            error_log("Erro ao enviar e-mail: {$this->mail->ErrorInfo}");
            return false;
        }
    }

    /**
     * Registra o e-mail no arquivo app.log para testes sem SMTP real.
     */
    private function logEmail(string $to, string $subject, string $body): bool
    {
        $logDir = APP_ROOT . '/storage/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $logFile = $logDir . '/app.log';
        $date = date('Y-m-d H:i:s');
        
        $content = "=== NOVO E-MAIL SIMULADO ===\n";
        $content .= "Data: $date\n";
        $content .= "Para: $to\n";
        $content .= "Assunto: $subject\n";
        $content .= "Mensagem:\n$body\n";
        $content .= "============================\n\n";

        file_put_contents($logFile, $content, FILE_APPEND);
        return true;
    }
}
