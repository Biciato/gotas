<?php

namespace App\Controller;

use App\Controller\AppController;
use App\Custom\RTI\QRCodeUtil;
use App\Custom\RTI\ResponseUtil;
use App\Custom\RTI\SefazUtil;

class SefazController extends AppController
{
    public function test()
    {
        return ResponseUtil::successAPI("", ['data' => ['a' => 'b']]);
    }

    public function getNFSefazQRCodeAPI()
    {
        $errors = [];
        $errorCodes = [];
        if ($this->request->is(Request::METHOD_GET)) {
            $data = $this->request->getQueryParams();
            $qrCode = !empty($data["qr_code"]) ? $data["qr_code"] : null;

            if (empty($qrCode)) {
                // @todo
                $errors[] = MSG_QR_CODE_EMPTY;
                $errorCodes[] = "";
            }

            $validacaoQRCode = QRCodeUtil::validarUrlQrCode($qrCode);

            // Encontrou erros de validação do QR Code. Interrompe e retorna erro ao usuário
            if ($validacaoQRCode["status"] == false) {
                $mensagem = array("status" => $validacaoQRCode["status"], "message" => $validacaoQRCode["message"], "errors" => $validacaoQRCode["errors"], "error_codes" => $validacaoQRCode["error_codes"]);

                // @todo
                $arraySet = array("mensagem");
                $this->set(compact($arraySet));
                $this->set("_serialize", $arraySet);

                return array("mensagem" => $mensagem);
            }

            $estado = $validacaoQRCode["estado"];

            $isEstadoGoias = false;
            if ($estado == "GO") {
                $isEstadoGoias = true;
                $startSearch = "chNFe=";
                $startSearchIndex = strpos($url, $startSearch) + strlen($startSearch);
                $chave = substr($url, $startSearchIndex, 44);
                $url = "http://nfe.sefaz.go.gov.br/nfeweb/jsp/CConsultaCompletaNFEJSF.jsf?parametroChaveAcesso=" . $chave;
            }

            $webContent = WebTools::getPageContent($url);

            if ($webContent["statusCode"] == 200) {
                $cnpjQuery = $this->Clientes->getClientesCNPJByEstado($estado);
                $cnpjArray = array();
                if ($estado == "MG") {
                    // Se estado == MG, preciso procurar a posição do cnpj com formatação
                    foreach ($cnpjQuery as $key => $value) {
                        $cnpjArray[] = preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "$1.$2.$3/$4-$5", $value["cnpj"]);
                    }
                } else {
                    foreach ($cnpjQuery as $key => $value) {
                        $cnpjArray[] = $value['cnpj'];
                    }
                }

                $cnpjEncontrado = null;
                foreach ($cnpjArray as $key => $cnpj) {
                    $cnpjFormatado = NumberUtil::formatarCNPJ($cnpj);
                    // Log::write('debug', __("CNPJ {$cnpj}"));
                    // Log::write('debug', __("CNPJ {$cnpjFormatado}"));
                    $cnpjPos = strpos($webContent["response"], $cnpj) !== false;
                    $cnpjPosFormatado = strpos($webContent["response"], $cnpjFormatado) !== false;

                    if ($cnpjPos || $cnpjPosFormatado) {
                        $cnpjEncontrado = $cnpj;
                        break;
                    }
                }

                // Se encontrou o cnpj, procura o cliente através do cnpj.
                // Se não encontrou, significa que a unidade ainda não está cadastrada no sistema,

                // DebugUtil::print($cnpjArray);

                $cliente = null;
                $rede = null;

                if ($cnpjEncontrado) {
                    $cnpjEncontrado = NumberUtil::limparFormatacaoNumeros($cnpjEncontrado);
                    $cliente = $this->Clientes->getClienteByCNPJ($cnpjEncontrado);
                }

                if (empty($cliente)) {
                    // @todo
                    $errors[] = "Nenhum estabelecimento encontrado na NF";
                    $errorCodes[] = 0;

                    // deste ponto não dá pra continuar se encontrar erro

                    throw new Exception(MESSAGE_GENERIC_EXCEPTION, MESSAGE_GENERIC_EXCEPTION_CODE);
                }

                $rede = $cliente->redes_has_cliente->rede;

                // Valida se a rede está ativa
                if (!$rede->ativado) {
                    $message = MESSAGE_GENERIC_COMPLETED_ERROR;
                    $errors = array(
                        MESSAGE_NETWORK_DESACTIVATED
                    );

                    $errors[] = MESSAGE_NETWORK_DESACTIVATED;
                    // @todo
                    $errorCodes[] = MESSAGE_NETWORK_DESACTIVATED;

                    throw new Exception(MESSAGE_GENERIC_EXCEPTION, MESSAGE_GENERIC_EXCEPTION_CODE);
                }

                // obtem todos os multiplicadores (gotas)
                $gotas = $this->Gotas->findGotasByClientesId(array($cliente->id));
                $gotas = $gotas->toArray();

                // Log::write("debug", $webContent);

                $retorno = SefazUtil::obtemProdutosSefaz($webContent["response"], $url, $chaveNfe, $cliente, null, null);

                // Agora, prepara o retorno conforme os registros NÃO importados

            } else {
                $errors[] = MSG_SEFAZ_NOT_RESPONDING;
                $errorCodes[] = MSG_SEFAZ_NOT_RESPONDING_CODE;
            }

            // $cupom = SefazUtil::

            if (count($errors) > 0) {
                throw new Exception(MESSAGE_GENERIC_EXCEPTION, MESSAGE_GENERIC_EXCEPTION_CODE);
            }
        }
    }
}
