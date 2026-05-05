<?php 

namespace Core\Utils;

class Funcoes {
    public static function formatarData($data) {
        if (empty($data)) {
            return '';
        }
        $date = new \DateTime($data);
        return $date->format('d/m/Y H:i:s');
    }

    public static function formatarMoeda($valor) {
        return number_format($valor, 2, ',', '.');
    }

    public static function gerarToken() {
        return bin2hex(random_bytes(16));
    }

    public function dividirListaEmGrupos(array $listaOriginal, int $tamanhoGrupo): array
    {
        $listaDividida = [];
        $contador = 0;
        $subListaAtual = [];

        foreach ($listaOriginal as $item) {
            $subListaAtual[] = $item;
            $contador++;

            if ($contador % $tamanhoGrupo === 0) {
                $listaDividida[] = $subListaAtual;
                $subListaAtual = [];
            }
        }

        if (!empty($subListaAtual)) {
            $listaDividida[] = $subListaAtual;
        }

        return $listaDividida;
    }
}