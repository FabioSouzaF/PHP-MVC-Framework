<?php

namespace Core\Exceptions;

use Throwable;
use ErrorException;
use Core\View\View;

class Handler
{
    public function register()
    {
        // Converte erros normais (Warning, Notice) em ErrorException para o catch tratar igual
        set_error_handler(function ($level, $message, $file = '', $line = 0) {
            if (error_reporting() & $level) {
                throw new ErrorException($message, 0, $level, $file, $line);
            }
        });

        // Captura todas as exceções não tratadas (Fatal Errors)
        set_exception_handler([$this, 'handleException']);
    }

    public function handleException(Throwable $e)
    {
        if ($e instanceof \Core\Exceptions\ValidationException) {
            \Core\Http\Session::setFlash('errors', $e->getErrors());
            \Core\Http\Session::setFlash('old', $_POST); // Salva os dados antigas para repopular o formulário
            
            $referer = $_SERVER['HTTP_REFERER'] ?? '/';
            header("Location: " . $referer);
            exit;
        }

        $code = $e->getCode();
        if ($code != 404) {
            $code = 500;
        }
        
        http_response_code($code);

        $env = $_ENV['APP_ENV'] ?? 'local';

        // Em ambiente de produção, gera log silencioso
        if ($env === 'production') {
            $this->logError($e);
            echo "<h1>Erro $code</h1>";
            echo "<p>Ops! Ocorreu um erro interno no servidor. Nossa equipe já foi notificada.</p>";
            exit;
        }

        // Em ambiente local, mostra o erro completo na tela
        $this->renderException($e);
    }

    private function logError(Throwable $e)
    {
        $logDir = APP_ROOT . '/storage/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        $date = date('Y-m-d H:i:s');
        $message = "[{$date}] Erro: " . $e->getMessage() . " in " . $e->getFile() . " na linha " . $e->getLine() . PHP_EOL;
        
        error_log($message, 3, $logDir . '/app.log');
    }

    private function renderException(Throwable $e)
    {
        echo "<div style='font-family: sans-serif; padding: 20px; background: #ffebee; border: 1px solid #ef9a9a; border-radius: 5px; color: #b71c1c; margin: 20px;'>";
        echo "<h2 style='margin-top:0;'>⚠️ Fatal Error</h2>";
        echo "<strong>Mensagem:</strong> " . $e->getMessage() . "<br><br>";
        echo "<strong>Arquivo:</strong> " . $e->getFile() . " (Linha: " . $e->getLine() . ")<br><br>";
        echo "<strong>Stack Trace:</strong><pre style='background: #fff; padding: 10px; overflow-x: auto;'>" . $e->getTraceAsString() . "</pre>";
        echo "</div>";
        exit;
    }
}
