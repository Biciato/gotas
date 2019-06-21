<?php
/**
 * @author  Gustavo Souza Gonçalves
 * @file    View\Helper\DateUtilHelper.php
 * @date    13/07/2017
 *
 */

namespace App\View\Helper;

use Cake\View\Helper;

/**
 * Classe de manipulação de Data
 * 
 * @category ClasseDeUtilidades
 * @package  Utils
 * @author   "Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>"
 * @license  http://www.apache.org/licenses/LICENSE-2.0.txt Apache License 2.0
 * @link     https://www.rtibrindes.com.br/Utils/ClasseDeUtilidades
 */
class DateUtilHelper extends Helper
{
    /**
     * Format date string to date value
     *
     * @param string $date   Data de para ser formatada
     * @param string $format Formatação
     *
     * @return (DateTime) value
     */
    public function dateToFormat($date = null, $format = null)
    {
        if (isset($date)) {
            return $date->format($format);
        } else {
            return null;
        }
    }
}
