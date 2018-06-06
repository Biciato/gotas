<?php

/**
 * Classe de Utilidades para objetos do tipo Gotas
 *
 * @category ClasseDeUtilidades
 * @package  Custom
 * @author   "Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>"
 * @date     13/10/2017
 * @license  http://www.apache.org/licenses/LICENSE-2.0.txt Apache License 2.0
 * @link     https://www.rtibrindes.com.br/Utils/ClasseDeUtilidades
 */

namespace App\Custom\RTI;

use App\Controller\AppController;
use Cake\ORM\Query;
use Cake\Core\Configure;

/**
 * Classe de manipulação de Gotas
 *
 * @category ClasseDeUtilidades
 * @package  Custom
 * @author   "Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>"
 * @date     03/08/2017
 * @license  http://www.apache.org/licenses/LICENSE-2.0.txt Apache License 2.0
 * @link     https://www.rtibrindes.com.br/Utils/ClasseDeUtilidades
 */
class NumberUtil
{
    /**
     * Construtor
     */
    function __construct()
    {
    }

    /**
     * NumberUtil::validarCPF
     *
     * Função que valida o CPF informado
     *
     * @param string $cpf CPF à ser validado
     *
     * @author @rafael-neri
     * @link https://gist.github.com/rafael-neri/ab3e58803a08cb4def059fce4e3c0e40
     *
     * @return bool
     */
    public static function validarCPF(string $cpf)
    {
        // Extrai somente os números
        $cpf = preg_replace('/[^0-9]/is', '', $cpf);

        // Verifica se foi informado todos os digitos corretamente
        if (strlen($cpf) != 11) {
            return ["status" => 0, "message" => Configure::read("messageUserCPFNotValidInvalidSize")];
        }

        // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return ["status" => 0, "message" => Configure::read("messageUserCPFNotValidInvalidNumber")];
        }
        // Faz o calculo para validar o CPF
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf {
                    $c} * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf {
                $c} != $d) {
                return ["status" => 0, "message" => Configure::read("messageUserCPFNotValidInvalidNumber")];
            }
        }
        return ["status" => 1, "message" => null];
    }
}
