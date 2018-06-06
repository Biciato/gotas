<?php

/**
 * @author  Gustavo Souza Gonçalves
 * @file    View\Helper\ClienteUtilHelper.php
 * @date    17/07/2017
 *
 */

namespace App\View\Helper;

use Cake\View\Helper;

class ClienteUtilHelper extends Helper
{

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function getTypeUnity($param = null)
    {
        $array = [
            '0' => 'Loja',
            '1' => 'Posto'
        ];

        return $array[$param];
    }
}
