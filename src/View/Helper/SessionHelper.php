<?php

/**
 * @author  Gustavo Souza Gonçalves
 * @file    View\Helper\SessionHelper.php
 * @date    17/07/2017
 *
 */

namespace App\View\Helper;

use Cake\View\Helper;
use Cake\Core\Configure;

class SessionHelper extends Helper
{

    /**
     * Retorna o tipo de perfil
     *
     * @param int $param Tipo procurado
     *
     * @return string user type
     **/
    public function getProfileType($param = null)
    {
        $profileTypes = Configure::read('profileTypesTranslated');

        return $profileTypes[(int) $param];
    }
}
