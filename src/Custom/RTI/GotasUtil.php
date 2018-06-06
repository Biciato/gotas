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
class GotasUtil
{
    /**
     * Construtor
     */
    function __construct()
    {
    }

    /**
     * Prepara array de gotas formatado em ['gotas_id', 'nome_parametro']
     *
     * @param Cake\ORM\Query $gotas_array Array de Gotas
     *
     * @return array $Gotas Array de gotas
     */
    public function prepareGotasArray(Query $gotas_array)
    {
        $gotas_array_prepared = [];

        foreach ($gotas_array as $key => $value) {
                  array_push($gotas_array_prepared, ['gotas_id' => $value->id, 'nome_parametro' => $value->nome_parametro]);
        }

        return $gotas_array_prepared;
    }
}
