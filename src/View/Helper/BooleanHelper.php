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
    public function convertBooleanToString(bool $param)
    {
        return $param == 1 ? 'Sim' : 'Não';
    }

    /**
     * Undocumented function
     *
     * @param bool $param Parameter
     * 
     * @return string word
     */
    public function convertEnabledToString(bool $param)
    {
        return $param == 1 ? 'Habilitado' : 'Desabilitado';
    }
}