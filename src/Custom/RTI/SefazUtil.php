<?php

/**
 * @author Gustavo Souza Gonçalves
 * @since 25/09/2017
 * @path vendor\rti\SefazUtilClass.php
 */

namespace App\Custom\RTI;

use App\Model\Entity\Cliente;
use App\Model\Entity\Usuario;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use DOMDocument;
use \Exception;

/**
 * Classe para operações de conteúdo da SEFAZ
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 2018-06-01
 */
class SefazUtil
{
    public function __construct()
    { }

    /**
     * SefazUtil::obtemDadosHTMLCupomSefaz
     *
     * Realiza tomada de decisão de qual método de conversão dos dados da SEFAZ será utilizado e retorna os objetos prontos
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 10/11/2018
     *
     * @param string $content Conteudo HTML
     * @param array $gotas Array de Gotas do cliente
     * @param string $estado Estado em formato 2 letras Uppercase
     *
     * @return array
     */
    public static function obtemDadosHTMLCupomSefaz(string $content, string $estado)
    {
        // @todo Todos os locais que passam as 'gotas', devem ser removidos
        if (strtoupper($estado) == "RS") {
            // este serviço precisará de revisão
            return self::converteHTMLParaPontuacoesArrayRioGrandeSul($content, []);
        }

        if (strtoupper($estado) == "MG") {
            return self::converteHTMLParaPontuacoesArrayMinasGerais($content);
        }

        if (strtoupper($estado) == "GO") {
            return self::converteHTMLParaPontuacoesArrayGoias($content, []);
        }

        return self::converteHTMLParaPontuacoesArray($content, []);
    }

    /**
     * Obtêm conteúdo de página Sefaz
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 10/11/2018
     *
     * @param string $content               Endereço do site
     * @param int    $gotas                 Array de gotas
     * @param object $pontuacao_comprovante Objeto preparado de Comprovante de Pontuacao
     * @param object $pontuacao             Objeto preparado de Pontuacao
     * @param object $pontuacao_pendente    Objeto preparado de Pontuação Pendente (se houver)
     *
     * @return array objeto contendo resposta
     */
    private static function converteHTMLParaPontuacoesArray(string $content, $gotas)
    {
        try {
            $returnContent = $content;
            $returnContent = trim($returnContent);
            $position_content_start = strpos($returnContent, "tabResult");
            $position_content_end = strpos($returnContent, "table", $position_content_start);
            $returnContent = strtoupper($returnContent);

            // array que ira gravar todas as pontuacoes
            $pontuacoes = array();

            foreach ($gotas as $key => $gota) {
                $content = $returnContent;

                // obtêm nome do parâmetro e o trata
                $parametro = $gota["nome_parametro"];
                $parametro = \strtoupper($parametro);

                // enquanto texto tiver conteúdo a tratar
                while (strlen($content) != 0 && strpos($content, $parametro) != 0) {
                    // procura índice do conteúdo à ser tratado
                    $parameterIndex = strpos($content, $parametro);

                    // pega o local onde está o parâmetro - 1 posição,
                    // para comparar se bate o parâmetro por inteiro
                    $content = substr($content, $parameterIndex - 1);

                    // verifica se a posição anterior ao
                    // parâmetro é igual ao caractere >
                    if ($content[0] == ">") {
                        $content = substr($content, strlen($parametro) + 1);
                        // agora verifica se a posição posterior ao parâmetro é igual a caractere <
                        if ($content[0] == "<") {
                            // palavra à procurar
                            $quantitySeekString = "QTDE.:</STRONG>";

                            // índice inicial da quantidade à procurar
                            $quantityIndexStart = strpos($content, $quantitySeekString) + strlen($quantitySeekString);

                            // índice final da quantidade à procurar
                            $quantityIndexEnd = strpos($content, "</SPAN", $quantityIndexStart);

                            // valor encontrado
                            $quantity = substr($content, $quantityIndexStart, $quantityIndexEnd - $quantityIndexStart);

                            // corrige valor encontrado para float

                            $quantity = \str_replace(",", ".", $quantity);
                            $content = substr($content, $quantityIndexEnd);

                            $pontuacaoItem['gotas_id'] = $gota['id'];
                            $pontuacaoItem['quantidade_multiplicador'] = $quantity;
                            $pontuacaoItem['quantidade_gotas'] = $gota['multiplicador_gota'] * $quantity;

                            $pontuacoes[] = $pontuacaoItem;
                        } else {
                            // margem de segurança para próxima pesquisa
                            $content = substr($content, 30);
                        }
                    }
                }
            }

            return $pontuacoes;
        } catch (\Exception $e) {
            $stringError = __("Erro ao preparar conteúdo html: {0} ", $e->getMessage());

            Log::write('error', $stringError);
        }
    }

    /**
     * Obtêm conteúdo de página Sefaz (Estado de Goiás)
     *
     * @param string $content               Endereço do site
     * @param int    $gotas                 Array de gotas
     * @param object $pontuacao_comprovante Objeto preparado de Comprovante de Pontuacao
     * @param object $pontuacao             Objeto preparado de Pontuacao
     * @param object $pontuacao_pendente    Objeto preparado de Pontuação Pendente (se houver)
     *
     * @return array objeto contendo resposta
     */
    // public function converteHTMLParaPontuacoesArrayGoias(string $content, $gotas, $pontuacao_comprovante, $pontuacao, $pontuacao_pendente = null)
    private static function converteHTMLParaPontuacoesArrayGoias(string $content, $gotas)
    {
        try {
            $returnContent = $content;
            $returnContent = trim($returnContent);
            $returnContent = strtoupper($returnContent);

            // array que ira gravar todas as pontuacoes
            $pontuacoes = array();

            foreach ($gotas as $key => $gota) {
                $content = $returnContent;

                // obtêm nome do parâmetro e o trata
                $parametro = $gota["nome_parametro"];
                $parametro = \strtoupper($parametro);

                // enquanto texto tiver conteúdo a tratar

                while (strlen($content) != 0 && strpos($content, $parametro) != 0) {
                    // procura índice do conteúdo à ser tratado
                    $parameterIndex = strpos($content, $parametro);

                    // pega o local onde está o parâmetro - 1 posição,
                    // para comparar se bate o parâmetro por inteiro
                    $content = substr($content, $parameterIndex - 1);

                    // verifica se a posição anterior ao
                    // parâmetro é igual ao caractere >
                    if ($content[0] == ">") {

                        $content = substr($content, strlen($parametro) + 1);
                        // agora verifica se a posição posterior
                        // ao parâmetro é igual a caractere <
                        if ($content[0] == "<") {
                            // palavra à procurar
                            $quantitySeekString = "LINHA\">";

                            // índice inicial da quantidade à procurar
                            $quantityIndexStart = strpos($content, $quantitySeekString) + strlen($quantitySeekString);

                            // índice final da quantidade à procurar
                            $quantityIndexEnd = strpos($content, "</SPAN", $quantityIndexStart);

                            // valor encontrado
                            $quantity = substr($content, $quantityIndexStart, $quantityIndexEnd - $quantityIndexStart);

                            // corrige valor encontrado para float

                            $quantity = \str_replace(",", ".", $quantity);

                            $content = substr($content, $quantityIndexEnd);

                            $pontuacaoItem['gotas_id'] = $gota['id'];
                            $pontuacaoItem['quantidade_multiplicador'] = $quantity;
                            $pontuacaoItem['quantidade_gotas'] = $gota['multiplicador_gota'] * $quantity;

                            $pontuacoes[] = $pontuacaoItem;
                        } else {
                            // margem de segurança para próxima pesquisa
                            $content = substr($content, 30);
                        }
                    }
                }
            }

            return $pontuacoes;
        } catch (\Exception $e) {
            $stringError = __("Erro ao preparar conteúdo html: {0}", $e->getMessage());

            Log::write('error', $stringError);
        }
    }

    /**
     * SefazUtil::converteHTMLParaPontuacoesArrayMinasGerais
     *
     * Obtêm conteúdo de página Sefaz (Estado de Minas Gerais)
     *
     * @param string $content Endereço do site
     * @param array  $gotas Array de gotas
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-05-19
     *
     * @return array objeto contendo resposta
     */
    private static function converteHTMLParaPontuacoesArrayRioGrandeSul(string $content, $gotas)
    {
        try {
            // Evita erros de DOM Elements
            libxml_use_internal_errors(true);

            $dom = new DOMDocument();
            $dom->loadHTML("<?xml encoding='utf-8' ?>" . $content);
            $items = $dom->getElementsByTagName("table");

            // Index aonde está a lista de produtos
            $nodeProdutos = 9;

            $produtosTemp = $items[$nodeProdutos];
            $produtosNota = array();

            if (!empty($produtosTemp)) {
                $produtosNota = $produtosTemp->childNodes;
            }

            $produtosLength = count($produtosNota);
            $pontuacoes = array();

            foreach ($gotas as $gota) {
                // Começa da posição 1 para ignorar o header da tabela
                for ($i = 1; $i < $produtosLength; $i++) {
                    $produtoNota = $produtosNota[$i];

                    if (stripos($produtoNota->nodeValue, $gota->nome_parametro) !== false) {
                        $itemString = trim($produtoNota->nodeValue);

                        $item = explode(" ", $itemString);
                        $itemLength = count($item);

                        /**
                         * Delimitações do array:
                         * Posição 0: Código do Produto
                         * Posição 1... N: Texto da gota
                         * Posição MAX-3: Qtde
                         * Posiçaõ MAX-2: Unidade
                         * Posição MAX-1: Vl. Unitário
                         * Posição MAX: Vl. Total
                         */
                        $codigo = $item[0];
                        $itemRealLength = $itemLength - 1;
                        $quantidade = $item[$itemRealLength - 3];
                        $valor = trim($item[$itemRealLength - 1]);
                        $nomeProdutoMaxIndex = $itemRealLength - 4;
                        $nomeProduto = "";
                        for ($index = 1; $index <= $nomeProdutoMaxIndex; $index++) {
                            $nomeProduto .= $item[$index] . " ";
                        }
                        $nomeProduto = trim($nomeProduto);

                        // Se o nome do produto REALMENTE bate, adiciona na lista
                        if ($nomeProduto == $gota->nome_parametro) {
                            $pontuacao = array();
                            $pontuacao["gotas_id"] = $gota["id"];
                            $pontuacao["quantidade_multiplicador"] = $quantidade;
                            $pontuacao["valor"] = $valor;
                            $pontuacao["quantidade_gotas"] = $gota["multiplicador_gota"] * (float) $quantidade;

                            $pontuacoes[] = $pontuacao;
                        }
                    }
                }
            }

            return $pontuacoes;
        } catch (\Exception $e) {
            $trace = $e->getTraceAsString();
            $stringError = __("Erro ao preparar conteúdo html: {0}", $e->getMessage());

            Log::write('error', $stringError);
            Log::write('error', $trace);

            throw new Exception($stringError);
        }
    }

    /**
     * SefazUtil::converteHTMLParaPontuacoesArrayMinasGerais
     *
     * Obtêm conteúdo de página Sefaz (Estado de Minas Gerais)
     *
     * @param string $content Endereço do site
     * @param array  $gotas Array de gotas
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-05-19
     *
     * @return array objeto contendo resposta
     */
    private static function converteHTMLParaPontuacoesArrayMinasGerais(string $content)
    {
        try {
            // Evita erros de DOM Elements
            libxml_use_internal_errors(true);
            $dom = new \DOMDocument();

            $dom->loadHTML("<?xml encoding='utf-8' ?>" . $content);

            $items = $dom->getElementById('myTable');
            $itemsNodesHtml = array();

            if (!empty($items)) {
                foreach ($items->childNodes as $node) {
                    $texto = $node->textContent;

                    $texto = trim($texto);

                    if (strlen($texto) > 0) {
                        $textoQuantidade = "Qtde total de ítens: ";

                        // Captura do gotas.nome_parametro
                        $posicaoQuantidade = strpos($texto, " Qtde total de ítens");
                        $descricao = substr($texto, 0, $posicaoQuantidade);
                        $posicaoParentese = strpos($texto, $textoQuantidade);
                        $descricao = substr($texto, 0, $posicaoParentese);
                        $descricao = trim($descricao);
                        $item["descricao"] = $descricao;

                        // Captura de quantidade
                        $posicaoFimTextoQuantidade = strlen($textoQuantidade);
                        $posicaoQuantidadeInicio = strpos($texto, $textoQuantidade) + $posicaoFimTextoQuantidade;
                        $posicaoQuantidadeFim = strpos($texto, " UN", $posicaoQuantidadeInicio) - $posicaoQuantidadeInicio;
                        $quantidade = substr($texto, $posicaoQuantidadeInicio, $posicaoQuantidadeFim);
                        $item["quantidade"] = $quantidade;

                        // Captura de valor
                        $textoReais = "R$ ";
                        $posicaoFimTextoReais = strlen($textoReais);
                        $posicaoReaisInicio = strpos($texto, $textoReais) + $posicaoFimTextoReais;

                        $valor = substr($texto, $posicaoReaisInicio);
                        $item["valor"] = $valor;
                        $itemsNodesHtml[] = $item;
                    }
                }
            }

            if (count($itemsNodesHtml) == 0) {
                throw new Exception(MSG_SEFAZ_CONTINGENCY_MODE, MSG_SEFAZ_CONTINGENCY_MODE_CODE);
            }

            return $itemsNodesHtml;
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $code = $e->getCode();
            Log::write('error', $message);

            throw new Exception($message, $code);
        }
    }

    /**
     * Obtêm endereço da SEFAZ de cada Estado
     *
     * @param string $estado Endereço do cliente
     *
     * @return string $endereco
     */
    public function getUrlSefazByState(string $estado)
    {
        $string = null;

        switch (strtolower($estado)) {
            case 'ac':
                $string = __("http://www.sefaznet.ac.gov.br/nfce/qrcode?chNFe=");
                break;
            case 'al':
                $string = __("http://nfce.sefaz.al.gov.br/QRCode/consultarNFCe.jsp?chNFe=");
                break;
            case 'ap':
                $string = __("https://www.sefaz.ap.gov.br/nfce/nfcep.php?chNFe=");
                break;
            case 'am':
                $string = __("http://sistemas.sefaz.am.gov.br/nfceweb/consultarNFCe.jsp?chNFe=");
                break;
            case 'ba':
                $string = __("https://nfe.sefaz.ba.gov.br/servicos/nfce/modulos/geral/NFCEC_consulta_chave_acesso.aspx?chNFe=");
                break;
            case 'df':
                $string = __("http://dec.fazenda.df.gov.br/ConsultarNFCe.aspx?chNFe=");
                break;
            case 'go':
                $string = __("http://nfe.sefaz.go.gov.br/nfeweb/sites/nfce/danfeNFCe?chNFe=");
                break;
            case 'ma':
                $string = __("http://www.nfce.sefaz.ma.gov.br/portal/consultaNFe.do?method=preFilterCupom&?chNFe=");
                break;
            case 'mt':
                $string = __("https://www.sefaz.mt.gov.br/nfce/consultanfce?chNFe=");
                break;
            case 'ms':
                $string = __("http://www.dfe.ms.gov.br/nfce/qrcode?chNFe=");
                break;
            case 'pa':
                $string = __("https://appnfc.sefa.pa.gov.br/portal/view/consultas/nfce/nfceForm.seam?chNFe=");
                break;
            case 'pb':
                $string = __("https://www.receita.pb.gov.br/nfce?chNFe=");
                break;
            case 'pr':
                $string = __("http://www.fazenda.pr.gov.br/nfce/qrcode?chNFe=");
                break;
            case 'pe':
                $string = __("http://nfce.sefaz.pe.gov.br/nfce-web/consultarNFCe?chNFe=");
                break;
            case 'pi':
                $string = __("http://webas.sefaz.pi.gov.br/nfceweb/consultarNFCe.jsf?chNFe=");
                break;
            case 'rj':
                $string = __("http://www4.fazenda.rj.gov.br/consultaNFCe/QRCode?chNFe=");
                break;
            case 'rn':
                $string = __("http://nfce.set.rn.gov.br/consultarNFCe.aspx?chNFe=");
                break;
            case 'rs':
                $string = __("https://www.sefaz.rs.gov.br/NFCE/NFCE-COM.aspx?chNFe=");
                break;
            case 'ro':
                $string = __("http://www.nfce.sefin.ro.gov.br/consultanfce/consulta.jsp?chNFe=");
                break;
            case 'rr':
                $string = __("https://www.sefaz.rr.gov.br/nfce/servlet/qrcode?chNFe=");
                break;
            case 'sp':
                $string = __("https://www.nfce.fazenda.sp.gov.br/NFCeConsultaPublica/Paginas/ConsultaQRCode.aspx?chNFe=");
                break;
            case 'se':
                $string = __("http://www.nfce.se.gov.br/portal/consultarNFCe.jsp?chNFe=");
                break;
            case 'to':
                $string = __("http://apps.sefaz.to.gov.br/portal-nfce/qrcodeNFCe?chNFe=");
                break;
        }

        return $string;
    }

    /**
     * SefazUtil::obtemDadosXMLCupomSefaz
     *
     * Obtem dados da SEFAZ e converte para Array
     *
     * @param string $xml String XML à ser convertida
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 04/11/2018
     *
     * @return Array("cnpjNotaFiscal", "produtos", "emitente")
     */
    public static function obtemDadosXMLCupomSefaz(string $xml)
    {
        $xmlDataReturn = simplexml_load_string($xml);
        $xmlData = json_decode(json_encode((array) $xmlDataReturn), true);

        $emitente = $xmlData["proc"]["nfeProc"]["NFe"]["infNFe"]["emit"];
        $produtosListaXml = $xmlData["proc"]["nfeProc"]["NFe"]["infNFe"]["det"];

        return array(
            "cnpj" => $emitente["CNPJ"],
            "produtos" => $produtosListaXml,
            "emitente" => $emitente
        );
    }

    /**
     * src\Custom\RTI\SefazUtil.php::obtemProdutosSefaz
     *
     * // @todo Ajustar
     * Processa o conteúdo que chegou do cURL e tranforma em array
     *
     * @param string $conteudo
     * @param string $url
     * @param string $chave
     * @param Cliente $cliente
     * @param Usuario $funcionario
     * @param Usuario $usuario
     *
     * @return array
     */
    public static function obtemProdutosSefaz(string $conteudo, string $url, string $chave, Cliente $cliente, Usuario $funcionario = null, Usuario $usuario = null, bool $importacaoFuturaAtivada = true)
    {
        $dataProcessamento = date("Y-m-d H:i:s");
        $isXML = StringUtil::validarConteudoXML($conteudo);

        // if (Configure::read("debug")) {
        //     Log::write("debug", $conteudo);
        // }

        if ($isXML) {
            $xml = self::obtemDadosXMLCupomSefaz($conteudo);

            // Obtem todos os dados de pontuações e comprovantes
            // Irá mudar se os outros estados tratam o XML de forma diferente. Deve ser analizado
            // @TODO aqui deverá retornar somente os produtos tb.
            // @todo aaaa
            return self::processaDadosCupomXMLSefaz($cliente["cnpj"], $cliente->estado, $url, $chave, $xml, []);
        } else {
            // É HTML
            $pontuacoesHtml = [];

            try {
                // $conteudo = 'a';
                if (strlen($conteudo) == 0) {
                    // Site da SEFAZ possivelmente fora do ar ou em manutenção
                    throw new Exception(MSG_NOT_POSSIBLE_TO_IMPORT_COUPON, MSG_NOT_POSSIBLE_TO_IMPORT_COUPON_CODE);
                }

                $pontuacoesHtml = SefazUtil::obtemDadosHTMLCupomSefaz($conteudo, $cliente->estado);

                if (count($pontuacoesHtml) == 0) {
                    throw new Exception(sprintf(MSG_SEFAZ_NO_DATA_FOUND_TO_IMPORT, $cliente->estado, $chave), MSG_SEFAZ_NO_DATA_FOUND_TO_IMPORT_CODE);
                }

                return $pontuacoesHtml;
            } catch (\Throwable $th) {
                $code = $th->getCode();
                $message = $th->getMessage();

                if ($importacaoFuturaAtivada) {
                    if ($code == MSG_SEFAZ_CONTINGENCY_MODE_CODE) {
                        Log::write("info", sprintf("URL %s não traz as 'gotas' configuradas do posto. Adicionando para processamento posterior.", $url));

                        $pontuacoesPendentesTable = TableRegistry::get("PontuacoesPendentes");

                        // Gera novo registro de pontuação pendente SE ainda não está pendente
                        $pontuacaoPendenteExiste = $pontuacoesPendentesTable->findPontuacaoPendenteAwaitingProcessing($chave, $cliente->estado);
                        if (empty($pontuacaoPendenteExiste)) {
                            $pontuacoesPendentesTable->createPontuacaoPendenteAwaitingProcessing($cliente->id, $usuario->id, $funcionario->id, $url, $chave, $cliente->estado ?? "");
                            Log::write("info", sprintf("Registro pendente gerado para cliente: %s, usuario: %s, funcionário: %s, url: %s, estado: %s. ", $cliente->id, $usuario->id, $funcionario->id ?? "", $url, $cliente->estado));
                        } else {
                            Log::write("info", sprintf("Registro já aguardando processamento, não sendo necessário novo registro. [cliente: %s, usuario: %s, funcionário: %s, url: %s, estado: %s]. ", $cliente->id, $usuario->id, $funcionario->id, $url, $cliente->estado));
                        }
                    }
                }

                throw new Exception($message, $code);
            }
        }
    }

    /**
     * PontuacoesComprovantesController::processaDadosCupomXMLSefaz
     *
     * Processa dados de Cupom da Sefaz
     *
     * @param string $clienteCNPJ CNPJ Cliente
     * @param string $estado Estado
     * @param string $url URL da Chave
     * @param string $chave Chave da URL
     * @param array $xml Dados de XML
     * @param array $gotas As gotas do cliente
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2018-11-05
     *
     * @return array Resultado
     */
    private static function processaDadosCupomXMLSefaz(string $clienteCNPJ, string $estado, string $url, string $chave, array $xmlData, array $gotas)
    {
        $produtosListaXml = $xmlData["produtos"];
        $cnpjNotaFiscalXML = $xmlData["cnpj"];
        $dataProcessamento = date("Y-m-d H:i:s");

        // Confere CNPJ
        if ($clienteCNPJ != $cnpjNotaFiscalXML) {
            // Este erro só pode acontecer, via Web, pois o cliente Mobile não vai passar o estabeleciemnto
            // O Método que chama deverá informar o Estabelecimento em questão.
            // Se CNPJ não bate, informa e encerra
            $success = false;
            $message = __(Configure::read("messageNotPossibleToImportCoupon"));
            $errors = array(
                Configure::read("messagePointOfServiceCNPJNotEqual")
            );
            $data = array();
            $retorno = array(
                "mensagem" => array(
                    "status" => $success,
                    "message" => $message,
                    "errors" => $errors
                ),
                "pontuacoes_comprovantes" => $data
            );

            return $retorno;
        }

        $pontuacoes = array();
        $produtosLista = array();

        $produtosListaXml = empty($produtosListaXml[0]) ? array($produtosListaXml) : $produtosListaXml;

        foreach ($produtosListaXml as $produto) {
            $gotaEncontrada = array_filter($gotas, function ($item) use ($produto) {
                return $item["nome_parametro"] == $produto["prod"]["xProd"];
            });

            $gotaEncontrada = reset($gotaEncontrada);

            if ($gotaEncontrada) {
                // Encontrou alguma gota
                $produto["prod"]["gota"] = $gotaEncontrada;
                $produtosLista[] = $produto;
            }
        }

        if (sizeof($produtosLista) == 0) {
            // Mensagem de erro informando que não foi encontrado gotas

            $success = false;
            $message = __(Configure::read("messageOperationFailureDuringProcessing"));
            $data = array();

            $retorno = array(
                "mensagem" => array(
                    "status" => $success,
                    "message" => $message,
                    "errors" => array(
                        Configure::read("messageOperationFailureDuringProcessing"),
                        Configure::read("messageGotasPointOfServiceNotConfigured")
                    )
                ),
                "pontuacoesComprovante" => $data
            );

            return $retorno;
        }

        $pontuacoesComprovante = array(
            "conteudo" => $url,
            "chave_nfe" => $chave,
            "estado_nfe" => $estado,
            "data" => $dataProcessamento,
            "requer_auditoria" => 0,
            "auditado" => 0
        );

        Log::write("debug", $produtosLista);

        $somaPontuacoes = 0;
        foreach ($produtosLista as $produto) {

            $gota = $produto["prod"]["gota"];

            $pontuacao = array(
                "gotas_id" => $produto["prod"]["gota"]["id"],
                "quantidade_multiplicador" => $produto["prod"]["qCom"],
                "quantidade_gotas" => $gota["multiplicador_gota"] * $produto["prod"]["qCom"],
                "data" => $dataProcessamento,
                // @TODO: armazenar valor do produto para alteração de preço de gota
                // "valor_produto" => $produto["prod"]["vUnCom"]
            );

            $somaPontuacoes += $pontuacao["quantidade_gotas"];
            $pontuacoes[] = $pontuacao;
        }

        return array(
            "mensagem" => array(
                "status" => 1,
                "message" => __(Configure::read("messageCouponImportSuccess")),
                "errors" => array()
            ),
            "pontuacoesComprovante" => $pontuacoesComprovante,
            "pontuacoes" => $pontuacoes,
        );
    }
}
