<?php

/**
 * Classe de Utilidades para objetos do tipo Image
 *
 * @category ClasseDeUtilidades
 * @package  Custom
 * @author   "Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>"
 * @date     05/08/2017
 * @license  http://www.apache.org/licenses/LICENSE-2.0.txt Apache License 2.0
 * @link     https://www.rtibrindes.com.br/Utils/ClasseDeUtilidades
 */

namespace App\Custom\RTI;

use App\Controller\AppController;
use Cake\Mailer\Image;
use Cake\Core\Configure;
use Cake\Log\Log;
use App\Model\Entity\Usuario;

/**
 * Classe de manipulação de Data
 *
 * @category ClasseDeUtilidades
 * @package  Custom
 * @author   "Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>"
 * @date     23/05/2018
 * @license  http://www.apache.org/licenses/LICENSE-2.0.txt Apache License 2.0
 * @link     https://www.rtibrindes.com.br/Utils/ClasseDeUtilidades
 */
class ImageUtil
{

    /**
     * Converte string de base 64 para arquivo jpg
     *
     * @param string $base64String
     * @param object $outputFile
     *
     * @return void
     */
    public static function generateImageFromBase64($base64String, $outputFile, $pathDestination)
    {
        try {
            FilesUtil::createPathIfNotExists($pathDestination);
            // abre o arquivo destino para edição
            $ifp = fopen($outputFile, 'wb');

            // separa a string por virgulas, para criar os dados

            $data = explode(',', $base64String);

            // escreve os dados no arquivo destino
            fwrite($ifp, base64_decode($data[1]));

            // fecha o arquivo destino
            fclose($ifp);

            chmod($outputFile, 0766);

            return $outputFile;
        } catch (\Exception $e) {
            Log::write("error", $e->getMessage());
        }
    }

    /**
     * ImageUtil::resizeImage
     *
     * Faz crop da imagem
     *
     * @param string $imageSource Caminho origem da imagem
     * @param float $cropWidth Largura da nova imagem
     * @param float $cropHeight Altura da nova imagem
     * @param float $valueX Valor horizontal
     * @param float $valueY Valor vertical
     * @param float $imageWidth Largura da imagem que será feito o crop
     * @param float $imageHeight Altura da imagem que será feito o crop
     * @param integer $quality Valor Quantidade
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 2018/05/23
     *
     * @return bool
     */
    public static function resizeImage(string $imageSource, float $cropWidth, float $cropHeight, float $valueX, float $valueY, float $imageWidth, float $imageHeight, int $quality)
    {
        $image = null;
        $typeImage = null;

        $newImage = imagecreatetruecolor($cropWidth, $cropHeight);

        if (strpos($imageSource, ".jpg") || strpos($imageSource, ".jpeg")) {
            $typeImage = ".jpg";
            $image = imagecreatefromjpeg($imageSource);
        } else if (strpos($imageSource, ".png")) {
            $typeImage = ".png";
            $image = imagecreatefrompng($imageSource);
        } else if (strpos($imageSource, ".bmp")) {
            $typeImage = ".bmp";
            $image = imagecreatefrombmp($imageSource);
        }

        if (empty($image)) {
            throw new \Exception("Erro durante carregamento de imagem. Aguarde o total carregamento da mesma!");
        }
        
        imagecopyresampled($newImage, $image, 0, 0, $valueX, $valueY, $cropWidth, $cropHeight, $imageWidth, $imageHeight);

        if ($typeImage == ".jpg") {
            return imagejpeg($newImage, $imageSource, 90) == true ? 1 : 0;
        } else if ($typeImage == ".png") {
            return imagepng($newImage, $imageSource, 9) == true ? 1 : 0;
        } else if ($typeImage == ".bmp") {
            return imagebmp($newImage, $imageSource, 9) == true ? 1 : 0;
        } else {
            return 0;
        }
    }



    /**
     * Rotates a Image
     *
     * @param string $imagePath
     * @param int $degrees
     * @return bool
     */
    public static function rotateImage(string $imagePath, int $degrees)
    {
        try {
            $source = imagecreatefromjpeg($imagePath);

            $rotate = \imagerotate($source, $degrees, 0);

            $result = imagejpeg($rotate, $imagePath);

            return $result;
        } catch (\Exception $e) {
            Log::write('error', $e->getMessage());
        }
    }
}
