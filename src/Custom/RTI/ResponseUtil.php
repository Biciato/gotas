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
use Cake\Http\Response;

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

    /**
     * ResponseUtil::successAPI
     *
     * Retorna dados via json à API Mobile
     *
     * @param string $msg Mensagem de sucesso
     * @param array $contentArray Array contendo todos os dados
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2018/12/29
     *
     * @return json_encode Dados json
     */
    public static function successAPI($msg, $contentArray)
    {
        header("HTTP/1.0 200");
        header("Content-Type: application/json");

        $arrayKeys = array_keys($contentArray);
        $mensagem = array(
            "status" => 1,
            "message" => $msg,
            "errors" => array()
        );
        $arraySet = array();
        $arraySet["mensagem"] = $mensagem;

        foreach ($arrayKeys as $key => $item) {
            $arraySet[$item] = $contentArray[$item];
        }

        echo json_encode($arraySet);
        die();
    }

    /**
     * ResponseUtil::errorAPI
     *
     * Retorna erro para API Mobile
     *
     * @param string $msg String da mensagem de erro
     * @param array $errors Array de Erros
     * @param array $data Dados adicionais de retorno
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2018/12/29
     *
     * @return json_encode Dados json
     */
    public static function errorAPI(string $msg, array $errors = array(), array $data = array())
    {
        header("HTTP/1.0 400");
        header("Content-Type: application/json");

        $arrayKeys = array_keys($data);
        $mensagem = array(
            "status" => 0,
            "message" => $msg,
            "errors" => $errors
        );
        $arraySet = array();
        $arraySet["mensagem"] = $mensagem;

        if (sizeof($data) > 0) {
            foreach ($arrayKeys as $key => $item) {
                $arraySet[$item] = $data[$item];
            }
        }

        echo json_encode($arraySet);
        die();
    }
}
