<?php

/**
 * Classe de Utilidades para Arquivos em Geral
 *
 * @category ClasseDeUtilidades
 * @package  Custom
 * @author   "Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>"
 * @date     26/05/2018
 * @license  http://www.apache.org/licenses/LICENSE-2.0.txt Apache License 2.0
 * @link     https://www.rtibrindes.com.br/Utils/ClasseDeUtilidades
 */

namespace App\Custom\RTI;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Log\Log;

/**
 * Classe de retorno de resposta
 *
 * @category ClasseDeUtilidades
 * @package  Custom
 * @author   "Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>"
 * @since    15/09/2018
 * @license  http://www.apache.org/licenses/LICENSE-2.0.txt Apache License 2.0
 * @link     https://www.rtibrindes.com.br/Utils/ClasseDeUtilidades
 */
class ResponseUtil
{
    /**
     * ResponseUtil::success
     *
     * Retorna mensagem contendo sucesso
     *
     * @param array $data
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 15/09/2018
     *
     * @return json_encode
     */
    public static function success($data)
    {
        header("HTTP/1.0 200");

        $arraySet = array(
            "msg" => $data
        );
        echo json_encode($arraySet);
        die();
    }

    /**
     * ResponseUtil::error
     *
     * Retorna mensagem contendo erro
     *
     * @param string $description
     * @param string $title
     * @param array $errors
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 15/09/2018
     *
     * @return json_encode
     */
    public static function error(string $description, string $title, array $errors = array())
    {
        header("HTTP/1.0 406");

        $arraySet = array(
            "title" => $title,
            "description" => $description,
            "errors" => $errors
        );
        echo json_encode($arraySet);
        die();
    }
}
