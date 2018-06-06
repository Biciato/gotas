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
        if (is_null($number)) {
            return null;
        }

        return substr($number, 0, 3) . "." . substr($number, 3, 3) . "." . substr($number, 6, 3) . "-" . substr($number, 9, 2);
    }
}
