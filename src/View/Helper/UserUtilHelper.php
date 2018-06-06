<?php

/**
 * @author  Gustavo Souza Gonçalves
 * @file    View\Helper\UserUtilHelper.php
 * @date    17/07/2017
 *
 */

namespace App\View\Helper;

use Cake\View\Helper;
use Cake\Core\Configure;

class UserUtilHelper extends Helper
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
    
    /**
     * Return gender of User
     *
     * @return (string) user gender
     * @author Gustavo Souza Gonçalves
     **/
    public function getGenderType($param)
    {
        if ($param == 1) {
            return "Masculino";
        } else {
            return "Feminino";
        }
    }
}
