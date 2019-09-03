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
     * @param bool $die Interromper execução após exibição
     * @param bool $formatted Exibe formatado
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date   24/06/2018
     *
     * @return void
     */
    public static function printArray($array, bool $die = true, bool $formatted = true)
    {
        if ($formatted) {
            // echo json_encode($array);
            echo "<pre>";
            print_r($array);
            echo "</pre>";
        } else {
            print_r($array);
        }

        if ($die) {
            die();
        }
    }

    /**
     * DebugUtil::print
     *
     * Exibe objeto na tela
     *
     * @param array $item Array a ser exibido
     * @param boolean $die Interromper execução após exibição
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date   24/06/2018
     *
     * @return void
     */
    public static function print($item, bool $identify = true, bool $die = true, bool $formatted = true)
    {
        if ($identify) {
            echo gettype($item);
            if (gettype($item) == "array") {
                self::printArray($item, $die, $formatted);
            } else if (gettype($item) == "object") {
                self::printArray($item, $die, $formatted);
            } else if (gettype($item) == "integer") {
                echo PHP_EOL;
                echo $item;
                echo PHP_EOL;
            } else if (gettype($item) == "double") {
                echo PHP_EOL;
                echo $item;
                echo PHP_EOL;
            }
        }

        if ($die) {
            die();
        }
    }
}
