<?php
/**
 * @author Gustavo Souza Gonçalves
 * @since 25/09/2017
 * @path vendor\rti\SefazUtilClass.php
 */

namespace App\Custom\RTI;

use App\Controller\AppController;
use Cake\Core\Configure;

/**
 * Classe para operações de conteúdo da SEFAZ
 */
class SefazUtil
{
    function __construct()
    {
    }

    /**
     * SefazUtil::obtemDadosHTMLCupomSefaz
     *
     * Realiza tomada de decisão de qual método de conversão dos dados da SEFAZ será utilizado e retorna os objetos prontos
     *
     * @param string $content Conteudo HTML
     * @param array $gotas Array de Gotas do cliente
     * @param string $estado Estado em formato 2 letras Uppercase
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 10/11/2018
     *
     * @return array
     */
    public static function obtemDadosHTMLCupomSefaz(string $content, array $gotas, string $estado)
    {
        if (strtoupper($estado) == "GO") {
            return self::converteHTMLParaPontuacoesArrayGoias($content, $gotas);
        }

        return self::converteHTMLParaPontuacoesArray($content, $gotas);
    }

    /**
     * Obtêm conteúdo de página Sefaz
     *
     * @param string $content               Endereço do site
     * @param int    $gotas                 Array de gotas
     * @param object $pontuacao_comprovante Objeto preparado de Comprovante de Pontuacao
     * @param object $pontuacao             Objeto preparado de Pontuacao
     * @param object $pontuacao_pendente    Objeto preparado de Pontuação Pendente (se houver)
     *
     * @return array objeto contendo resposta
     */
    // public static function converteHTMLParaPontuacoesArray(string $content, $gotas, $clientesId, $usuariosId, $funcionariosId, $data)
    public static function converteHTMLParaPontuacoesArray(string $content, $gotas)
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
    public static function converteHTMLParaPontuacoesArrayGoias(string $content, $gotas)
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
        $xmlData = json_decode(json_encode((array)$xmlDataReturn), true);

        $emitente = $xmlData["proc"]["nfeProc"]["NFe"]["infNFe"]["emit"];
        $produtosListaXml = $xmlData["proc"]["nfeProc"]["NFe"]["infNFe"]["det"];
        $cnpjNotaFiscalXML = $emitente["CNPJ"];

        return array(
            "cnpj" => $emitente["CNPJ"],
            "produtos" => $produtosListaXml,
            "emitente" => $emitente
        );
    }
}
