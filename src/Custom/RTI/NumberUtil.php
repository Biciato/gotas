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
    { }

    /**
     * NumberUtil::calculaDistanciaLatitudeLongitude
     *
     * Calcula distância entre duas localizações latitude/longitude
     *
     * @author martinstoeckli <https://stackoverflow.com/users/575765/martinstoeckli>
     * @since 2012-04-07
     *
     * @source https://stackoverflow.com/questions/10053358/measuring-the-distance-between-two-coordinates-in-php
     *
     * @param float $latOrigem
     * @param float $longOrigem
     * @param float $latDestino
     * @param float $longDestino
     * @param string $unidade
     *
     * @return float $distancia
     */
    public static function calculaDistanciaLatitudeLongitude($latOrigem, $longOrigem, $latDestino, $longDestino, $unidade = "km")
    {
        // converte valores degraus para Radianos
        $latOrigem = deg2rad($latOrigem);
        $lonOrigem = deg2rad($longOrigem);
        $latDestino = deg2rad($latDestino);
        $lonDestino = deg2rad($longDestino);

        $earthRadius = 0;

        switch ($unidade) {
            case 'km':
                // kilometros
                $earthRadius = 6371;
                break;
            case 'm':
                // metros
                $earthRadius = 6371000;
                break;
            default:
                // milhas
                $earthRadius = 3959;
                break;
        }

        $lonDelta = $lonDestino - $lonOrigem;
        $a = pow(cos($latDestino) * sin($lonDelta), 2) + pow(cos($latOrigem) * sin($latDestino) - sin($latOrigem) * cos($latDestino) * cos($lonDelta), 2);
        $b = sin($latOrigem) * sin($latDestino) + cos($latOrigem) * cos($latDestino) * cos($lonDelta);

        $angle = atan2(sqrt($a), $b);
        return $angle * $earthRadius;
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
            return ["status" => 0, "message" => Configure::read("messageUsuarioCPFNotValidInvalidSize")];
        }

        // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return ["status" => 0, "message" => Configure::read("messageUsuarioCPFNotValidInvalidNumber")];
        }
        // Faz o calculo para validar o CPF
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf{
                    $c} * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf{
                $c} != $d) {
                return ["status" => 0, "message" => Configure::read("messageUsuarioCPFNotValidInvalidNumber")];
            }
        }
        return ["status" => 1, "message" => null];
    }

    /**
     * NumberUtil::validarCNPJ
     *
     * Função que realiza validação de CNPJ
     *
     * @param string $cnpj CNPJ
     *
     * @author Gerador CNPJ
     * @link https://www.geradorcnpj.com/script-validar-cnpj-php.htm
     * @since 20/11/2018
     */
    public static function validarCNPJ(string $cnpj = null)
    {
        try {
            // Verifica se um número foi informado
            if (empty($cnpj)) {
                return false;
            }

            // Elimina possivel mascara
            $cnpj = preg_replace("/[^0-9]/", "", $cnpj);
            $cnpj = str_pad($cnpj, 14, '0', STR_PAD_LEFT);
            $cnpj = strval($cnpj);

            // Verifica se o numero de digitos informados é igual a 11
            if (strlen($cnpj) != 14) {
                return false;
            }

            // Verifica se nenhuma das sequências invalidas abaixo
            // foi digitada. Caso afirmativo, retorna falso
            elseif (
                $cnpj == '00000000000000' ||
                $cnpj == '11111111111111' ||
                $cnpj == '22222222222222' ||
                $cnpj == '33333333333333' ||
                $cnpj == '44444444444444' ||
                $cnpj == '55555555555555' ||
                $cnpj == '66666666666666' ||
                $cnpj == '77777777777777' ||
                $cnpj == '88888888888888' ||
                $cnpj == '99999999999999'
            ) {
                return false;

                // Calcula os digitos verificadores para verificar se o
                // CPF é válido
            } else {

                $j = 5;
                $k = 6;
                $soma1 = 0;
                $soma2 = 0;

                for ($i = 0; $i < 13; $i++) {

                    $j = $j == 1 ? 9 : $j;
                    $k = $k == 1 ? 9 : $k;

                    $soma2 += ($cnpj[$i] * $k);

                    if ($i < 12) {
                        $soma1 += ($cnpj[$i] * $j);
                    }

                    $k--;
                    $j--;
                }

                $digito1 = $soma1 % 11 < 2 ? 0 : 11 - $soma1 % 11;
                $digito2 = $soma2 % 11 < 2 ? 0 : 11 - $soma2 % 11;

                return (($cnpj{
                    12} == $digito1) and ($cnpj{
                    13} == $digito2));
            }
        } catch (\Exception $e) {
            echo $e;
        }
    }

    /**
     * NumberUtil::limparFormatacaoNumeros
     *
     * Limpa formatação de números
     *
     * @param string $data Numero formatado
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 08/11/2018
     *
     * @return string $data Numero sem formato
     */
    public static function limparFormatacaoNumeros(string $data)
    {
        return preg_replace('/[^0-9]/', "", $data);
    }

    /**
     * Undocumented function
     *
     * @param string $cnpj
     * @return void
     */
    public static function formatarCNPJ(string $cnpj)
    {
        return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "$1.$2.$3/$4-$5", $cnpj);
    }

    /**
     * Undocumented function
     *
     * @param string $cnpj
     * @return void
     */
    public static function formatarCPF(string $cpf)
    {
        return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "$1.$2.$3-$4", $cpf);
    }
}
