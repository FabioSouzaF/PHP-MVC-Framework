<?php

namespace Core\Utils;

use GdImage; // Importa a classe GdImage

class ImageProcessor
{
    /**
     * Escala (redimensiona) uma imagem para as dimensões especificadas.
     *
     * @param GdImage $image O recurso de imagem GD (agora um objeto GdImage).
     * @param int $newWidth A nova largura desejada.
     * @param int $newHeight A nova altura desejada.
     * @param bool $maintainAspectRatio Se true, ajusta uma das dimensões para manter a proporção original.
     * @return GdImage|false O novo recurso de imagem GD redimensionado, ou false em caso de erro.
     */
    public function scaleImage(GdImage $image, int $newWidth, int $newHeight, bool $maintainAspectRatio = true)
    {
        // A verificação de tipo 'is_resource' não é mais estritamente necessária
        // para o parâmetro $image se ele já for type-hinted como GdImage,
        // mas pode ser útil para depuração se algo inesperado for passado.
        if (!$image instanceof GdImage) {
            error_log("Erro: O argumento \$image não é um objeto GdImage válido.");
            return false;
        }

        $originalWidth = imagesx($image);
        $originalHeight = imagesy($image);

        // Calcular novas dimensões mantendo a proporção, se solicitado
        if ($maintainAspectRatio) {
            $ratio = $originalWidth / $originalHeight;
            if ($newWidth / $newHeight > $ratio) {
                $newWidth = (int)($newHeight * $ratio);
            } else {
                $newHeight = (int)($newWidth / $ratio);
            }
        }

        $scaledImage = imagecreatetruecolor($newWidth, $newHeight);

        // Manter a transparência (especialmente útil para PNG e GIF)
        imagealphablending($scaledImage, false);
        imagesavealpha($scaledImage, true);
        $transparent = imagecolorallocatealpha($scaledImage, 255, 255, 255, 127);
        imagefilledrectangle($scaledImage, 0, 0, $newWidth, $newHeight, $transparent);

        // Redimensionar a imagem com alta qualidade
        if (!imagecopyresampled($scaledImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight)) {
            imagedestroy($scaledImage);
            error_log("Erro ao redimensionar a imagem.");
            return false;
        }

        return $scaledImage;
    }

    /**
     * Converte um recurso de imagem GD para um tipo de imagem específico (JPG, PNG, GIF).
     *
     * @param GdImage $image O recurso de imagem GD (agora um objeto GdImage).
     * @param string $outputType O tipo de saída desejado ('png', 'jpeg', 'gif').
     * @param int $quality Qualidade para JPG (0-100). Ignorado para PNG/GIF.
     * @return string|false Os dados binários da imagem convertida, ou false em caso de erro.
     */
    public function convertImageToType(GdImage $image, string $outputType, int $quality = 90)
    {
        if (!$image instanceof GdImage) {
            error_log("Erro: O argumento \$image não é um objeto GdImage válido.");
            return false;
        }

        ob_start(); // Inicia o buffer de saída

        $outputType = strtolower($outputType);

        switch ($outputType) {
            case 'png':
                // Para PNG, a qualidade é um valor de compressão de 0 (sem compressão) a 9 (máxima compressão).
                // Ajustamos a qualidade de 0-100 para 0-9.
                $pngQuality = round(($quality / 100) * 9);
                if (!imagepng($image, null, $pngQuality)) {
                    ob_end_clean();
                    error_log("Erro ao converter imagem para PNG.");
                    return false;
                }
                break;
            case 'jpeg':
            case 'jpg':
                if (!imagejpeg($image, null, $quality)) {
                    ob_end_clean();
                    error_log("Erro ao converter imagem para JPEG.");
                    return false;
                }
                break;
            case 'gif':
                if (!imagegif($image)) {
                    ob_end_clean();
                    error_log("Erro ao converter imagem para GIF.");
                    return false;
                }
                break;
            default:
                ob_end_clean();
                error_log("Erro: Tipo de saída de imagem não suportado: " . $outputType);
                return false;
        }

        $imageData = ob_get_clean(); // Obtém o conteúdo do buffer e o limpa
        return $imageData;
    }

    /**
     * Converte dados binários de imagem em uma string Base64.
     *
     * @param string $imageData Os dados binários da imagem.
     * @param string $mimeType O tipo MIME da imagem (ex: 'image/png', 'image/jpeg').
     * @param bool $includeDataUri Se true, inclui o prefixo "data:image/..." no Base64.
     * @return string O Base64 da imagem.
     */
    public function convertImageToBase64(string $imageData, string $mimeType = 'image/png', bool $includeDataUri = false): string
    {
        $base64 = base64_encode($imageData);

        // if ($includeDataUri) {
        //     return 'data:' . $mimeType . ';base64,' . $base64;
        // }

        return $base64;
    }

    /**
     * Método auxiliar para carregar uma imagem de uma URL ou string.
     *
     * @param string $input Pode ser uma URL ou os dados binários da imagem.
     * @param bool $isUrl True se o input for uma URL, false se for dados binários.
     * @return GdImage|false O recurso de imagem GD (agora um objeto GdImage), ou false em caso de erro.
     */
    public function loadImage(string $input, bool $isUrl = true)
    {
        $imageData = $isUrl ? file_get_contents($input) : $input;
        // $imageData = file_get_contents($input);

        if ($imageData === false || empty($imageData)) {
            error_log("Erro: Não foi possível obter os dados da imagem de: " . ($isUrl ? $input : "string de dados"));
            return false;
        }

        // imagecreatefromstring retorna um objeto GdImage no PHP 8+
        $image = @imagecreatefromstring($imageData);

        if ($image === false) {
            error_log("Erro: O conteúdo não é uma imagem GD válida.");
            return false;
        }

        return $image;
    }



}
?>