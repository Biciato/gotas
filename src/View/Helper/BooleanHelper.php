<?php

/**
 * @author 	Gustavo Souza Gonçalves
 * @file 	View\Helper\BooleanHelper.php
 * @date 	08/07/2017
 *
 */

namespace App\View\Helper;

use Cake\View\Helper;

class BooleanHelper extends Helper
{
    /**
     * Convert boolean to String
     *
     * @param bool $param Parameter
     *
     * @return string word
     */
    public function convertBooleanToString(bool $param = null)
    {
        if (is_null($param)) {
            return null;
        }

        return $param == 0 ? 'Não' : 'Sim';
    }

    /**
     * Undocumented function
     *
     * @param bool $param Parameter
     *
     * @return string word
     */
    public function convertEnabledToString(bool $param = null)
    {
        return $param == 1 ? 'Habilitado' : 'Desabilitado';
    }

    public function convertEquipamentoRTIBooleanToString(bool $param = null)
    {
        if (is_null($param)) {
            return "";
        } else {
            return $param == 1 ? "Equipamento RTI" : "Produtos / Serviços";
        }
    }
}
