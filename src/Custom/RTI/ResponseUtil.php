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
        header('Content-Type: application/json');
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

        if (count($data) > 0) {
            foreach ($arrayKeys as $key => $item) {
                $arraySet[$item] = $data[$item];
            }
        }

        echo json_encode($arraySet);
        die();
    }

    /**
     * Prepara array de retorno em caso de consulta via paginação
     *
     * @param array $totalData Array de Dados
     * @param array $currentData Dados Atuais
     * @param string $stringLabelReturn Nome do índice de retorno
     * @param array $pagination Array de Paginação
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @data 08/07/2018
     *
     * @return array $dados
     */
    public static function prepareReturnDataPagination(array $totalData, array $currentData = array(), string $stringLabelReturn = null, array $pagination = array())
    {
        if (empty($stringLabelReturn)) {
            $stringLabelReturn = "data";
        }

        $retorno = array();
        $count = count($totalData);

        // DebugUtil::printArray($totalData);
        // DebugUtil::printArray($currentData);

        // Retorna mensagem de que não retornou dados se for page 1. Se for page 2, apenas não exibe.
        if (count($totalData) == 0) {
            $retorno = array(
                "mensagem" => array(
                    "status" => 0,
                    "message" => Configure::read("messageQueryNoDataToReturn"),
                    "errors" => array()
                ),
                $stringLabelReturn => array(
                    "count" => 0,
                    "page_count" => 0,
                    "data" => array()
                ),
            );

            if (count($pagination) > 0) {
                if ($pagination["page"] == 1) {
                    $retorno = array(
                        $stringLabelReturn => array(
                            "count" => 0,
                            "page_count" => 0,
                            "data" => array()
                        ),
                        "mensagem" => array(
                            "status" => 0,
                            "message" => Configure::read("messageQueryNoDataToReturn"),
                            "errors" => array()
                        )
                    );
                } else {
                    $retorno = array(
                        "mensagem" => array(
                            "status" => 0,
                            "message" => Configure::read("messageQueryNoDataToReturn"),
                            "errors" => array()
                        ),
                        $stringLabelReturn => array(
                            "count" => 0,
                            "page_count" => 0,
                            "data" => array()
                        )

                    );
                }
            }
        } else {
            // se tem dados, mas a página atual não tem, é fim de paginação também
            // DebugUtil::printArray($currentData);

            if (count($currentData) == 0) {
                $retorno = array(
                    "mensagem" => array(
                        "status" => 0,
                        "message" => Configure::read("messageQueryPaginationEnd"),
                        "errors" => array()
                    ),
                    $stringLabelReturn => array(
                        "count" => 0,
                        "page_count" => 0,
                        "data" => array()
                    )

                );
            } else {
                // DebugUtil::printArray($totalData);
                $retorno = array(
                    $stringLabelReturn => array(
                        "count" => count($totalData),
                        "page_count" => count($currentData),
                        "data" => $currentData
                    ),
                    "mensagem" => array(
                        "status" => count($totalData) > 0 ? 1 : 0,
                        "message" => count($totalData) > 0 ? Configure::read("messageLoadDataWithSuccess") : Configure::read("messageQueryNoDataToReturn"),
                        "errors" => array()
                    ),
                );
            }
        }

        // DebugUtil::printArray($retorno);
        return $retorno;
    }
}
