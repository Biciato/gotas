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
        }

        imagecopyresampled($newImage, $image, 0, 0, $valueX, $valueY, $cropWidth, $cropHeight, $imageWidth, $imageHeight);

        if ($typeImage == ".jpg") {
            return imagejpeg($newImage, $imageSource, 90) == true ? 1 : 0;
        } else if ($typeImage == ".png") {
            return imagepng($newImage, $imageSource, 90) == true ? 1 : 0;
        } else {
            return 0;
        }
    }

}
