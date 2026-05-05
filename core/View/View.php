<?php
// core/View.php

namespace Core\View;

class View
{
    /**
     * Renderiza um arquivo de view com os dados fornecidos.
     *
     * @param string $module O nome do módulo (ex: 'Site', 'Auth').
     * @param string $viewPath Caminho da view relativo à pasta do módulo (ex: 'home/index').
     * @param array $data Dados a serem passados para a view.
     * @param string|null $layout Nome do layout opcional (ex: 'default').
     */
    public function render(string $module, string $viewPath, array $data = [], ?string $layout = null): void
    {
        // Closure helper para gerar o input CSRF facilmente nas views
        $data['csrf_field'] = function() {
            $token = \Core\Http\Session::get('csrf_token');
            return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars((string)$token) . '">';
        };

        // Converte o array de dados em variáveis acessíveis dentro da view
        extract($data);

        // Inicia o buffer de saída.
        ob_start();

        // Constrói o caminho completo para o arquivo da view
        $viewFile = APP_ROOT . "/app/$module/Views/$viewPath.php";

        if (!file_exists($viewFile)) {
            throw new \Exception("View file not found: " . $viewFile);
        }

        // Inclui o arquivo da view.
        require_once $viewFile;

        // Obtém o conteúdo da view do buffer
        $viewContent = ob_get_clean();

        // Se um layout for especificado, renderiza a view dentro do layout
        if ($layout) {
            // Tenta achar o layout dentro do próprio módulo
            $layoutFile = APP_ROOT . "/app/$module/Views/layouts/$layout.php";
            
            // Se não achar, tenta num diretório compartilhado genérico
            if (!file_exists($layoutFile)) {
                $layoutFile = APP_ROOT . "/app/Shared/Views/layouts/$layout.php";
            }

            if (!file_exists($layoutFile)) {
                throw new \Exception("Layout file not found: " . $layoutFile);
            }

            // Define a variável $content para o layout
            $content = $viewContent;

            ob_start();
            require_once $layoutFile;
            echo ob_get_clean();
        } else {
            echo $viewContent;
        }
    }
}