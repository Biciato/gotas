<?php

/**
 * @author 	Gustavo Souza Gonçalves
 * @file 	View\Helper\PhoneHelper.php
 * @date 	17/07/2017
 *
 */

namespace App\View\Helper;

use Cake\View\Helper;

class PhoneHelper extends Helper
{

    /**
     * Formata o número de um telefone
     *
     * @param string $phone Número do telefone
     * 
     * @return número formatado
     **/
    public function formatPhone(string $phone = null)
    {
        $newPhone = '';

        if (strlen($phone) == 0) {
            return null;
        }

        if (strlen($phone) ==  11) {
            $newPhone = '('.substr($phone, 0, 2).')'.substr($phone, 2, 5).'-'.substr($phone, 7, 4);
        } else {
            $newPhone = '('.substr($phone, 0, 2).')'.substr($phone, 2, 4).'-'.substr($phone, 6, 4);
        }

        return $newPhone;
    }

}

?>