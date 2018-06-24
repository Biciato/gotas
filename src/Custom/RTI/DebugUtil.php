<?php

/**
 * Classe de Utilidades para objetos do tipo Debug
 *
 * @category ClasseDeUtilidades
 * @package  Custom
 * @author   "Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>"
 * @date     24/06/2018
 * @license  http://www.apache.org/licenses/LICENSE-2.0.txt Apache License 2.0
 */

namespace App\Custom\RTI;

use App\Controller\AppController;
use Cake\Core\Configure;

class DebugUtil
{
    /**
     * Construtor
     */
    function __construct()
    {
    }

    /**
     * DebugUtil::printArray
     *
     * Exibe array em modo console formatado
     *
     * @param array $array Array a ser exibido
     * @param boolean $die Interromper execução após exibição
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date   24/06/2018
     *
     * @return void
     */
    public static function printArray(array $array, bool $die = true)
    {
        echo "<pre>";
        print_r($array);
        echo "</pre>";

        if ($die){
            die();
        }
    }
}
