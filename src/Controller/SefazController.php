<?php

namespace App\Controller;

use App\Controller\AppController;
use App\Custom\RTI\DebugUtil;
use App\Custom\RTI\NumberUtil;
use App\Custom\RTI\QRCodeUtil;
use App\Custom\RTI\ResponseUtil;
use App\Custom\RTI\SefazUtil;
use App\Custom\RTI\WebTools;
use App\Model\Entity\Gota;
use Cake\Http\Client\Request;
use Cake\Log\Log;
use Exception;
use stdClass;

class SefazController extends AppController
{
    public function getNFSefazQRCodeAPI()
    {
        $errors = [];
        $errorCodes = [];
        if ($this->request->is(Request::METHOD_GET)) {
            try {
                $data = $this->request->getQueryParams();
                $qrCode = !empty($data["qr_code"]) ? $data["qr_code"] : null;

                if (empty($qrCode)) {
                    $errors[] = MSG_QR_CODE_EMPTY;
                    $errorCodes[] = MSG_QR_CODE_EMPTY_CODE;

                    throw new Exception(MESSAGE_GENERIC_EXCEPTION, MESSAGE_GENERIC_EXCEPTION_CODE);
                }

                $validacaoQRCode = QRCodeUtil::validarUrlQrCode($qrCode);

                // Encontrou erros de validação do QR Code. Interrompe e retorna erro ao usuário
                if ($validacaoQRCode["status"] == false) {
                    $errors = $validacaoQRCode["errors"];
                    $errorCodes = $validacaoQRCode["error_codes"];

                    throw new Exception($validacaoQRCode["message"]);
                }

                $chaveNfeData = array_filter($validacaoQRCode["data"], function ($obj) {
                    if ($obj["key"] == "chNFe") {
                        return $obj;
                    }
                });

                $estado = $validacaoQRCode["estado"];
                $url = $qrCode;
                $isEstadoGoias = false;
                $chaveNfe = null;

                if (count($chaveNfeData) > 0) {
                    $chaveNfeData = $chaveNfeData[0];
                    $chaveNfe = $chaveNfeData["content"];
                }

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
                        $errorCodes[] = MESSAGE_NETWORK_DESACTIVATED;

                        throw new Exception(MESSAGE_GENERIC_EXCEPTION, MESSAGE_GENERIC_EXCEPTION_CODE);
                    }

                    // obtem todos os multiplicadores (gotas)
                    $gotas = $this->Gotas->findGotasByClientesId(array($cliente->id));
                    $gotas = $gotas->toArray();
                    $itens = SefazUtil::obtemProdutosSefaz($webContent["response"], $url, $chaveNfe, $cliente, null, null, false);

                    // Agora, prepara o retorno conforme os registros NÃO importados

                    $retorno = [];
                    $data = new stdClass();
                    $data->cliente = $cliente;
                    $data->rede = $rede;
                    $sefaz = new stdClass();
                    $produtos = new stdClass();
                    $itensTemp = [];
                    foreach ($itens as $item) {
                        $itemEncontrado = false;

                        foreach ($gotas as $gota) {
                            if ($gota->nome_parametro == $item["descricao"]) {
                                // Só irá retornar os itens que ainda não estão cadastrados
                                $itemEncontrado = true;
                                continue;
                            }
                        }

                        if (!$itemEncontrado) {
                            $itemTemp = new stdClass();
                            $itemTemp->nomeParametro = $item["descricao"];
                            $itemTemp->multiplicadorGota = 1.0;
                            $itemTemp->importar = true;
                            $itensTemp[] = $itemTemp;
                        }
                    }

                    $itens = $itensTemp;
                    $produtos->itens = $itens;
                    $produtos->quantidade = count($itens);
                    $sefaz->produtos = $produtos;
                    $data->sefaz = $sefaz;
                    $retorno["data"] = $data;

                    if (count($itens) == 0 && count($gotas) > 0) {
                        throw new Exception("Todos os Produtos contidos no Cupom Fiscal já foram adicionados ao sistema!");
                    }

                    return ResponseUtil::successAPI(MSG_LOAD_DATA_WITH_SUCCESS, $retorno);
                } else {
                    $errors[] = MSG_SEFAZ_NOT_RESPONDING;
                    $errorCodes[] = MSG_SEFAZ_NOT_RESPONDING_CODE;
                }

                // $cupom = SefazUtil::

                if (count($errors) > 0) {
                    throw new Exception(MESSAGE_GENERIC_EXCEPTION, MESSAGE_GENERIC_EXCEPTION_CODE);
                }
            } catch (\Throwable $th) {
                $message = $th->getMessage();
                $code = $th->getCode();

                for ($i = 0; $i < count($errors); $i++) {
                    $c = empty($errorCodes[$i]) ? 0 : $errorCodes[$i];
                    Log::write("error", sprintf("[%s] %s: %s", $message, $errors[$i], $c));
                }

                return ResponseUtil::errorAPI($message, $errors, [], $errorCodes);
            }
        }
    }
}
