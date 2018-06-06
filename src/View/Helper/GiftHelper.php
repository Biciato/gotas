<?php

/**
 * @author  Gustavo Souza Gonçalves
 * @file    View\Helper\GiftHelper.php
 * @date    17/07/2017
 *
 */

namespace App\View\Helper;

use Cake\View\Helper;

class GiftHelper extends Helper
{

    /**
     * Formata o número de um telefone
     *
     * @param string $status Número do telefone
     *
     * @return string formatada
     **/
    public function formatGift(string $status)
    {
        
        switch ($status) {
            case 0:
                return "Aguardando Autorização";
                break;

            case 1:
                return "Autorizado";
                break;
            
            case 2:
                return "Negado";
                break;
            default:
                # code...
                break;
        }
    }

    /**
     * Retorna o tipo de brinde
     *
     * @param boolean $giftType Verdadeiro = Smart Shower / Falso = Comum
     * @return string
     */
    public function getGiftType(bool $giftType)
    {
        return (bool) $giftType ? "Smart Shower" : "Brinde Comum";
    }
}
