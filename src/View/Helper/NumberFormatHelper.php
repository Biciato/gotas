<?php
/**
 * @author  Gustavo Souza Gonçalves
 * @file    View\Helper\NumberFormatHelper.php
 * @date    13/07/2017
 *
 */

namespace App\View\Helper;

use Cake\View\Helper;

class NumberFormatHelper extends Helper
{
    /**
     * Formata número para padrão CNPJ
     *
     * @param string $number Número do CNPJ
     *
     * @return string CNPJ formatada ##.###.###/####-##
     */
    public function formatNumberToCNPJ(string $number = null)
    {
        if (is_null($number)) {
            return null;
        } else {
            return substr($number, 0, 2) . "." . substr($number, 2, 3) . "." . substr($number, 5, 3)
            . "/" . substr($number, 8, 4) . "-" . substr($number, 12, 2);
        }
    }

    /**
     * Formata número para padrão CPF
     * 
     * @param string $number número do cpf
     *
     * @return string formatada ###.###.###-##
     */
    public function formatNumberToCPF($number = null)
    {
        if (empty($number)) {
            return null;
        }
        
        return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "$1.$2.$3-$4", $number);
    }
}
