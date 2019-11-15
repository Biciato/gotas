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
use Cake\Log\Log;

/**
 * Classe de manipulação de Gotas
 *
 * @category ClasseDeUtilidades
 * @package  Custom
 * @author   "Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>"
 * @date     11/08/2018
 * @license  http://www.apache.org/licenses/LICENSE-2.0.txt Apache License 2.0
 * @link     https://www.rtibrindes.com.br/Utils/ClasseDeUtilidades
 */
class QRCodeUtil
{
    /**
     * Construtor
     */
    function __construct()
    { }

    /**
     * QRCodeUtil::gerarStringAleatoria
     *
     * Gera uma string aleatória
     *
     * @param integer $tamanho Tamanho da string (Default 32 dígitos
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-05-16
     *
     * @return string
     */
    public static function gerarStringAleatoria(int $tamanho = 32)
    {
        return substr(md5(mt_rand()), 0, $tamanho);
    }

    /**
     * QRCodeUtil::validarUrlQrCode
     *
     * Valida a URL do QRCode
     *
     * @param string $url URL de onde será capturadoos dados
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 01/03/2018
     *
     * @return array $data de Consistência de errors e validação
     */
    public static function validarUrlQrCode(string $url)
    {
        /**
         * Regras:
         * Key: nome da chave;
         * Size: tamanho que deve constar;
         * FixedSize: tamanho deve ser obrigatoriamente conforme size;
         * isOptional: Se é opcional mas está informado
         * index: indice do registro na url
         */

        Log::write("info", __LINE__);

        $errors = [];
        $errorCodes = [];
        if (empty($url) || strlen($url) == 0 || !filter_var($url, FILTER_VALIDATE_URL) === false) {
            // $errorMessage = __("O QR Code informado não está gerado conforme os padrões pré-estabelecidos da SEFAZ, não sendo possível realizar sua importação!");
            $errors = [MSG_QR_CODE_READING_ERROR];
            $errorCodes = [MSG_QR_CODE_READING_ERROR_CODE];

            $result = array(
                "status" => false,
                "message" => MSG_WARNING,
                "errors" => $errors,
                "error_codes" => $errorCodes,
                "data" => null,
                "url_real" => null,
                "estado" => null,
                "tipo_operacao_sefaz" => null
            );

            Log::write("info", __LINE__);
            Log::write("info", $result);

            return $result;

            // ResponseUtil::successAPI('', $result);
            Log::write("info", __LINE__);
            // Retorna Array contendo erros de validações
            return $result;
        }

        Log::write("info", __LINE__);

        $arrayConsistency = array();

        // Tratamento de url para assegurar que é HTTPS
        if (stripos($url, "https://") === false) {
            $url = str_replace("http://", "https://", $url);
        }

        // Obtem estado da URL.

        // Se estado = MG, o modelo é outro...

        Log::write("info", __LINE__);

        $estadoPorURLArray = array(
            "fazenda.mg" => array("estado" => "MG", "qrCodeProcura" => "xhtml?p="),
            "sefaz.rs" => array("estado" => "RS", "qrCodeProcura" => "asp?p="),
            "sefaz.ba" => ["estado" => "BA", "qrCodeProcura" => "aspx?p="]
        );
        $estado = "";
        $qrCodeProcura = "";
        $tratamentoPorEstado = false;

        Log::write("info", __LINE__);

        foreach ($estadoPorURLArray as $site => $item) {
            if (strpos($url, $site) !== false) {
                $estado = $item["estado"];
                $qrCodeProcura = $item["qrCodeProcura"];
                $tratamentoPorEstado = true;
                break;
            }
        }

        Log::write("info", __LINE__);

        if ($tratamentoPorEstado) {
            if ($estado == "RS") {
                $url = str_replace("|", "%7C", $url);
                $url = str_replace("https://www.sefaz.rs.gov.br/NFCE/NFCE-COM.aspx?", "https://www.sefaz.rs.gov.br/ASP/AAE_ROOT/NFE/SAT-WEB-NFE-NFC_QRCODE_1.asp?", $url);
            }

            Log::write("info", __LINE__);

            $posInicioChave = strpos($url, $qrCodeProcura) + strlen($qrCodeProcura);

            $qrCodeConteudo = substr($url, $posInicioChave);

            $explodeString = strpos($url, "|") !== false ? "|" : "%7C";
            $qrCodeArray = explode($explodeString, $qrCodeConteudo);

            $tipoQrCode = count($qrCodeArray) > 6 ? "CONTINGENCIA" : "ONLINE";
            $keysQrCode = [];
            Log::write("info", __LINE__);

            /**
             * chNFe = Chave Nota Fiscal Eletronica
             * nVersao = Número da Versão do QR Code
             * tpAmb = Tipo Ambiente
             * dtEmi = Data Emissão (MG = dia emissão)
             * vlTot = Valor Total
             * digVal = Digest Value da NFC-e
             * csc = Identificador do CSC
             * cHashQRCode = Hash do QR Code
             */

            $status = 1;
            $errorMessage = null;
            $errors = [];
            $errorCodes = [];

            Log::write("info", __LINE__);


            if (in_array($estado, ["MG", "BA"])) {
                $arrayConsistency = [];

                if ($tipoQrCode == "ONLINE") {
                    Log::write("info", __LINE__);

                    $arrayConsistency[] = ["key" => 'chNFe', "size" => 44, "fixedSize" => true, "isOptional" => false, "content" => null, "index" => 0, "estado" => $estado];
                    $arrayConsistency[] = ["key" => 'nVersao', "size" => 1, "fixedSize" => true, "isOptional" => false, "content" => null, "index" => 1, "estado" => $estado];
                    $arrayConsistency[] = ["key" => 'tpAmb', "size" => 1, "fixedSize" => true, "isOptional" => false, "content" => null, "index" => 2, "estado" => $estado];
                    $arrayConsistency[] = ["key" => 'csc', "size" => 6, "fixedSize" => false, "isOptional" => true, "content" => null, "index" => 3, "estado" => $estado];
                    // Na verdade o Hash é requerido. Mas é possível acessar sem este campo na nota.
                    $arrayConsistency[] = ["key" => 'cHashQRCode', "size" => 40, "fixedSize" => true, "isOptional" => true, "content" => null, "index" => 4, "estado" => $estado];
                } else {
                    // @todo
                    Log::write("info", __LINE__);

                    $arrayConsistency[] = ["key" => 'chNFe', "size" => 44, "fixedSize" => true, "isOptional" => false, "content" => null, "index" => 0, "estado" => $estado];
                    $arrayConsistency[] = ["key" => 'nVersao', "size" => 1, "fixedSize" => true, "isOptional" => false, "content" => null, "index" => 1, "estado" => $estado];
                    $arrayConsistency[] = ["key" => 'tpAmb', "size" => 1, "fixedSize" => true, "isOptional" => false, "content" => null, "index" => 2, "estado" => $estado];
                    $arrayConsistency[] = ["key" => 'dtEmi', "size" => 2, "fixedSize" => true, "isOptional" => false, "content" => null, "index" => 4, "estado" => $estado];
                    $arrayConsistency[] = ["key" => 'vlTot', "size" => 15, "fixedSize" => false, "isOptional" => false, "content" => null, "index" => 5, "estado" => $estado];
                    $arrayConsistency[] = ["key" => 'digVal', "size" => 56, "fixedSize" => true, "isOptional" => false, "content" => null, "index" => 7, "estado" => $estado];
                    $arrayConsistency[] = ["key" => 'csc', "size" => 6, "fixedSize" => false, "isOptional" => true, "content" => null, "index" => 8, "estado" => $estado];
                    $arrayConsistency[] = ["key" => 'cHashQRCode', "size" => 40, "fixedSize" => true, "isOptional" => true, "content" => null, "index" => 9, "estado" => $estado];
                }

                if ($tipoQrCode == "ONLINE") {
                    Log::write("info", __LINE__);

                    $keysQrCode = ["chNFe", "nVersao", "tpAmb", "csc", "cHashQRCode"];
                } else {
                    Log::write("info", __LINE__);

                    $keysQrCode = ["chNFe", "nVersao", "tpAmb", "dtEmi", "vlTot", "digVal", "csc", "cHashQRCode"];
                }

                $indexQrCodeArray = 0;
                $qrCodeArrayRetorno = array();
                foreach ($keysQrCode as $chave) {
                    Log::write("info", __LINE__);

                    if (!empty($qrCodeArray[$indexQrCodeArray])) {
                        $qrCodeArrayRetorno[] = array(
                            "key" => $chave,
                            "content" => $qrCodeArray[$indexQrCodeArray]
                        );
                    }
                    $indexQrCodeArray++;
                }

                $arrayConsistencyReturn = [];
                foreach ($arrayConsistency as $item) {
                    Log::write("info", __LINE__);

                    foreach ($qrCodeArrayRetorno as $qrCodeItem) {
                        Log::write("info", __LINE__);

                        if ($item["key"] == $qrCodeItem["key"]) {
                            $a = $item;
                            $a["content"] = $qrCodeItem["content"];
                            $arrayConsistencyReturn[] = $a;
                        }
                    }
                }

                $arrayConsistency = $arrayConsistencyReturn;

                #region Validação de erros
                $sefazErrors = [];

                foreach ($arrayConsistency as $itemConsistency) {
                    Log::write("info", __LINE__);

                    // Se não é opcional e está vazio o conteúdo, é um erro
                    if (!$itemConsistency["isOptional"] && empty($itemConsistency["content"])) {
                        $sefazErrors[] = $itemConsistency["key"];
                    }

                    // Se o tamanho é fixo e o tamanho real difere, também é erro
                    if (($itemConsistency["fixedSize"] && strlen($itemConsistency["content"]) != $itemConsistency["size"]) || (strlen($itemConsistency["content"]) > $itemConsistency["size"])) {
                        $sefazErrors[] = $itemConsistency["key"];
                    }
                }

                if (count($sefazErrors) > 0) {
                    Log::write("info", __LINE__);

                    $status = 0;
                    $errors = [MSG_QR_CODE_READING_ERROR];
                    $errorCodes = [MSG_QR_CODE_READING_ERROR_CODE];
                    $errorMessage = MESSAGE_GENERIC_EXCEPTION;

                    Log::write("info", sprintf("[%s]: %s", MSG_QR_CODE_SEFAZ_MISMATCH_PATTERN_CODE, MSG_QR_CODE_SEFAZ_MISMATCH_PATTERN));
                    Log::write("info", sprintf("Cupom com erro [%s] / Campos com erro: [%s].", $url, implode(" - ", $sefazErrors)));
                }

                #endregion
            }

            $result = array(
                "status" => $status,
                "message" => $errorMessage,
                "errors" => $errors,
                "error_codes" => $errorCodes,
                "data" => $arrayConsistency,
                "url_real" => $url,
                "estado" => $estado,
                "tipo_operacao_sefaz" => $tipoQrCode
            );

            // ResponseUtil::successAPI('', $result);
            Log::write("info", __LINE__);
            // Retorna Array contendo erros de validações
            return $result;
        }

        // código antigo para bahia ou rio grande do sul

        $stringSearch = "sefaz.";
        $index = stripos($url, $stringSearch) + strlen($stringSearch);

        $estado = substr($url, $index, 2);
        $estado = strlen($estado) > 0 ? strtoupper($estado) : $estado;

        $arrayConsistency[] = ["key" => 'chNFe', "size" => 44, "fixedSize" => true, "isOptional" => false, "content" => null, "index" => 0, "estado" => $estado];
        $arrayConsistency[] = ["key" => 'nVersao', "size" => 3, "fixedSize" => true, "isOptional" => false, "content" => null, "index" => 0, "estado" => $estado];
        $arrayConsistency[] = ["key" => 'tpAmb', "size" => 1, "fixedSize" => true, "isOptional" => false, "content" => null, "index" => 0, "estado" => $estado];
        $arrayConsistency[] = ["key" => 'cDest', "size" => 3, "fixedSize" => false, "isOptional" => true, "content" => null, "index" => 0, "estado" => $estado];
        $arrayConsistency[] = ["key" => 'dhEmi', "size" => 50, "fixedSize" => false, "isOptional" => false, "content" => null, "index" => 0, "estado" => $estado];
        $arrayConsistency[] = ["key" => 'vNF', "size" => 15, "fixedSize" => false, "isOptional" => false, "content" => null, "index" => 0, "estado" => $estado];
        $arrayConsistency[] = ["key" => 'vICMS', "size" => 15, "fixedSize" => false, "isOptional" => false, "content" => null, "index" => 0, "estado" => $estado];
        $arrayConsistency[] = ["key" => 'digVal', "size" => 56, "fixedSize" => true, "isOptional" => false, "content" => null, "index" => 0, "estado" => $estado];
        $arrayConsistency[] = ["key" => 'cIdToken', "size" => 6, "fixedSize" => true, "isOptional" => false, "content" => null, "index" => 0, "estado" => $estado];
        $arrayConsistency[] = ["key" => 'cHashQRCode', "size" => 40, "fixedSize" => true, "isOptional" => false, "content" => null, "index" => 0, "estado" => $estado];


        $hasErrors = false;

        $arrayErrors = array();
        $arrayResult = array();

        foreach ($arrayConsistency as $value) {
            $key = $value["key"] . '=';

            // aponta o índice para o início do valor
            $keyIndex = strpos($url, $key);
            $value["index"] = $keyIndex + strlen($key);

            // registro é obrigatório?
            if (!$value["isOptional"]) {
                $errorType = "";

                // é obrigatório mas não encontrado?
                if (strlen($keyIndex) == 0) {
                    $errorType = __("Campo {0} do QR Code deve ser informado", $value["key"]);
                } else {
                    // índice de fim
                    $indexEnd = strpos($url, "&", $keyIndex);

                    // caso extraordinário, trata se o campo for o último da lista
                    if ($value["index"] > $indexEnd) {
                        $indexEnd = strlen($url);
                    }

                    // cálculo de tamanho do valor
                    $length = $indexEnd - $value["index"];

                    // captura conteúdo
                    $value["content"] = substr($url, $value["index"], $indexEnd - $value["index"]);

                    // valida se o campo contem espaços (não é permitido)
                    $containsBlank = strpos($value["content"], " ");

                    // encontrou algum espaço em branco
                    if (strlen($containsBlank) == 0) {
                        // valida se o tamanho do campo é fixo
                        if ($value["fixedSize"]) {
                            if ($length != $value["size"]) {
                                $errorType = __("Campo {0} do QR Code deve conter {1} bytes", $value["key"], $value["size"]);
                            }
                        }
                    } else {
                        $errorType = __(
                            "Campo {0} contêm espaço em branco.",
                            $value["key"]
                        );
                    }
                }

                if (strlen($errorType) > 0) {
                    $value["error"] = $errorType;
                    $arrayErrors[] = $value;
                    $errors[] = $value["error"];
                    $hasErrors = true;
                }
            }

            $arrayResult[] = $value;
        }

        // se houve erro na análise da URL, o usuário deverá informar os dados manualmente
        $errorMessage = null;
        $status = 1;
        // $errors = array();

        if (sizeof($arrayErrors) > 0) {
            $errorMessage = __("O QR Code informado não está gerado conforme os padrões pré-estabelecidos da SEFAZ, não sendo possível realizar sua importação!");
            $status = 0;
            // $errors = $arrayErrors;
        }

        $result = array(
            "status" => $status,
            "message" => $errorMessage,
            "errors" => $errors,
            "error_codes" => $errorCodes,
            "data" => $arrayResult,
            "url_real" => $url,
            "estado" => $estado
        );

        // debug
        // return ResponseUtil::successAPI('', $result);
        // Retorna Array contendo erros de validações
        return $result;
    }
}
