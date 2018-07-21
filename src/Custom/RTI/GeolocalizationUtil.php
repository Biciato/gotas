<?php

/**
 * Classe de Utilidades para objetos do tipo Gotas
 *
 * @category ClasseDeUtilidades
 * @package  Custom
 * @author   "Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>"
 * @date     13/10/2017
 * @license  http://www.apache.org/licenses/LICENSE-2.0.txt Apache License 2.0
 * @link     https://www.rtibrindes.com.br/Utils/ClasseDeUtilidades
 */

namespace App\Custom\RTI;

use App\Controller\AppController;
use Cake\ORM\Query;
use Cake\Core\Configure;

/**
 * Classe de manipulação de Gotas
 *
 * @category Utils
 * @package  App\Custom\RTI
 * @author   Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @date     21/07/2018
 * @license  http://www.apache.org/licenses/LICENSE-2.0.txt Apache License 2.0
 * @link     https://www.rtibrindes.com.br/Utils/ClasseDeUtilidades
 */
class GeolocalizationUtil
{
    /**
     * Construtor
     */
    function __construct()
    {
    }

    /**
     * GeolocalizationUtil::convertScale
     *
     * Método que faz conversão de cada grau de latitude e longitude para cálculo de posicionamento
     *
     * @param float $metrics Métrica de escala.
     *  111,12 equivale a 1 grau na escala de latitude e longitude.
     *  11,12 equivale a 0.1 grau na escala de latitude e longitude.
     *
     * @return float
     */
    public static function convertScale($metrics)
    {
        $degreeScaleOriginal = 111.12;

        return $metrics / $degreeScaleOriginal;
    }

    /**
     * GeolocalizationUtil::convertScaleRound
     *
     * Método que faz conversão de cada grau de latitude e longitude para cálculo de posicionamento
     *
     * @param float $metrics Métrica de escala.
     *  111,12 equivale a 1 grau na escala de latitude e longitude.
     *  11,12 equivale a 0.1 grau na escala de latitude e longitude.
     *
     * @return float
     */
    public static function convertScaleRound($metrics)
    {
        return self::convertScale($metrics / 2);
    }
}
