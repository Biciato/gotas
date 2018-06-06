<?php
/**
 * @author  Gustavo Souza Gonçalves
 * @file    View\Helper\AddressHelper.php
 * @date    13/07/2017
 *
 */

namespace App\View\Helper;

use Cake\View\Helper;

class AddressHelper extends Helper
{

    
    /**
     * Retorna todos os tipos de Endereço
     *
     * @param int $param Índice de endereço específico para retorno
     *
     * @return array
     */
    public function getAddressType(int $param = null)
    {

        $array = [
            "0" => '',
            "1"=>  "Aeroporto",
            "2"=>  "Alameda",
            "3"=>  "Área",
            "4"=>  "Avenida",
            "5"=>  "Campo ",
            "6"=>  "Chácara",
            "7"=>  "Colônia",
            "8"=>  "Condomínio",
            "9"=>  "Conjunto",
            "10"=>  "Distrito",
            "11"=>  "Esplanada",
            "12"=>  "Estação",
            "13"=>  "Estrada",
            "14"=>  "Favela",
            "15"=>  "Fazenda",
            "16"=>  "Feira",
            "17"=>  "Jardim",
            "18"=>  "Ladeira",
            "19"=>  "Lago",
            "20"=>  "Lagoa",
            "21"=>  "Largo",
            "22"=>  "Loteamento",
            "23"=>  "Morro",
            "24"=>  "Núcleo",
            "25"=>  "Parque",
            "26"=>  "Passarela",
            "27"=>  "Pátio",
            "28"=>  "Praça",
            "29"=>  "Quadra",
            "30"=>  "Recanto",
            "31"=>  "Residencial",
            "32"=>  "Rodovia",
            "33"=>  "Rua",
            "34"=>  "Setor",
            "35"=>  "Sítio",
            "36"=>  "Travessa",
            "37"=>  "Trecho",
            "38"=>  "Trevo",
            "39"=>  "Vale",
            "40"=>  "Vereda",
            "41"=>  "Via",
            "42"=>  "Viaduto",
            "43"=>  "Viela",
            "44"=>  "Vila"
        ];
        
        if (is_null($param)) {
            return $array;
        } elseif ($param == 0) {
            return "";
        } else {
            return $array[$param];
        }
    }

    /**
     * Retorna lista de estados do Brasil
     *
     * @param string $state Estado
     *
     * @return array lista de estado ou item de estado
     **/
    public function getStatesBrazil(string $state = null)
    {
        $array =  [
            null => null,
            'AC'=>'Acre',
            'AL'=>'Alagoas',
            'AP'=>'Amapá',
            'AM'=>'Amazonas',
            'BA'=>'Bahia',
            'CE'=>'Ceará',
            'DF'=>'Distrito Federal',
            'ES'=>'Espírito Santo',
            'GO'=>'Goiás',
            'MA'=>'Maranhão',
            'MT'=>'Mato Grosso',
            'MS'=>'Mato Grosso do Sul',
            'MG'=>'Minas Gerais',
            'PA'=>'Pará',
            'PB'=>'Paraíba',
            'PR'=>'Paraná',
            'PE'=>'Pernambuco',
            'PI'=>'Piauí',
            'RJ'=>'Rio de Janeiro',
            'RN'=>'Rio Grande do Norte',
            'RS'=>'Rio Grande do Sul',
            'RO'=>'Rondônia',
            'RR'=>'Roraima',
            'SC'=>'Santa Catarina',
            'SP'=>'São Paulo',
            'SE'=>'Sergipe',
            'TO'=>'Tocantins',
            '--' => 'Outro'
        ];

        if (isset($state)) {
            return $array[$state];
        } else {
            return $array;
        }
    }

    /**
     * Formata o número passado para formato de CEP ##.###-###
     *
     * @param string $cep CEP à formatar
     *
     * @return string $cep CEP formatado
     */
    public function formatCEP($cep = null)
    {
        if (is_null($cep)) {
            return null;
        } else {
            return substr($cep, 0, 2) . "." . substr($cep, 2, 3) . "-" . substr($cep, 5, 3);
        }
    }
}
