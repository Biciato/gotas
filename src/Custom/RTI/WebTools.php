<?php

namespace App\Custom\RTI;

use App\Controller\AppController;

use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use Exception;

/**
 * @author Gustavo Souza Gonçalves
 * @date 22/09/2017
 * @path vendor\rti\WebToolsClass.php
 */


/**
 * Classe para operações de web
 */
class WebTools
{
    public $prop = null;

    function __construct()
    {
    }

    /**
     * Obtêm conteúdo de página web
     *
     * @param string $url endereço do site
     * @return array objeto contendo resposta
     */
    public static function getPageContent(string $url)
    {
        try {

            $curl = curl_init($url);

            $userAgent = 'Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';

            $header = array();
            // $header[] = "Accept: text/xml,text/csv,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
            $header[] = "Accept: application/xml;application/json;text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
            $header[] = "Connection: keep-alive";
            $header[] = "Keep-Alive: 300";

            curl_setopt_array(
                $curl,
                [
                    // CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_URL => $url,
                    // CURLOPT_ENCODING => 'gzip',
                    // CURLOPT_ENCODING, 'gzip,deflate',
                    CURLOPT_HTTPHEADER => $header,

                    CURLOPT_CUSTOMREQUEST => "GET",        //set request type post or get
                    CURLOPT_POST => false,        //set to GET
                    // CURLOPT_POST           =>false,        //set to GET
                    CURLOPT_USERAGENT => $userAgent, //set user agent
                    CURLOPT_COOKIEFILE => "cookie.txt", //set cookie file
                    CURLOPT_COOKIEJAR => "cookie.txt", //set cookie jar
                    CURLOPT_RETURNTRANSFER => true,     // return web page
                    CURLOPT_HEADER => false,    // don't return headers
                    CURLOPT_FOLLOWLOCATION => true,     // follow redirects
                    CURLOPT_ENCODING => "",       // handle all encodings
                    CURLOPT_AUTOREFERER => true,     // set referer on redirect
                    CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
                    // CURLOPT_TIMEOUT => 120,      // timeout on response
                    CURLOPT_TIMEOUT => 30,      // timeout on response
                    CURLOPT_MAXREDIRS => 30,       // stop after 10 redirects
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_BUFFERSIZE, 1024 * 1024 * 1024 // curl buffer size in bytes

                ]
            );

            $response = curl_exec($curl);

            $response = trim(preg_replace('/\s+/', ' ', $response));

            // Log::write('debug', $response);

            $error = \curl_errno($curl);
            $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            if (strpos($response, "Erro 500") !== false) {
                $status = 500;
            }

            // só pode prosseguir se houve status= 200
            // $status = 400;
            $result = null;

            // @todo SOMENTE PARA TESTES!
            // $response = self::getHtmlTest();

            if ($status == 200) {
                // ok
                // $response = trim($response);
                $result = (['response' => $response, 'url' => $url, 'status' => 'online', 'statusCode' => $status]);
            } elseif ($status >= 400) {
                // em qualquer outro caso, gera erro
                $result = (['response' => null, 'url' => $url, 'status' => 'offline', 'statusCode' => $status]);
            }

            return $result;
            // return json_encode($result);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao obter conteúdo html: {0} em: {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    public static function loginAPIGotas(string $email, string $senha)
    {
        try {
            Log::write("info", sprintf("Iniciando autenticação de: %s.", $email));
            $debug = Configure::read("debug");
            $url = __SERVER__ .  "api/usuarios/token";

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_VERBOSE, $debug);
            // curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_DEFAULT);
            // curl_setopt($curl, CURLOPT_PORT, $_SERVER['SERVER_PORT']);


            $data = array("email" => $email, "senha" => $senha);

            $method = "POST";
            switch ($method) {
                case "POST":
                    curl_setopt($curl, CURLOPT_POST, 1);

                    if ($data) {
                        $dataJson = json_encode($data);
                        curl_setopt($curl, CURLOPT_POST, 1);
                        curl_setopt($curl, CURLOPT_POSTFIELDS, $dataJson);
                        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                            "Accept: application/json",
                            "Content-Type: application/json",
                            "Content-Length: " . strlen($dataJson),
                            "IsMobile: True"
                        ));
                    }
                    break;
                case "PUT":
                    curl_setopt($curl, CURLOPT_PUT, 1);
                    break;
                default:
                    if ($data) {
                        $url = sprintf("%s?%s", $url, http_build_query($data));
                    }
            }
            $result = curl_exec($curl);
            $result = json_decode($result, true);
            $curlErr = curl_errno($curl);
            $curlError = curl_error($curl);

            if ($curlErr) {
                Log::write("error", sprintf("Erro durante processamento cUrl: %s - %s", $curlErr, $curlError));
            }

            curl_close($curl);
            $authentication = array();

            if (!empty($result["usuario"])) {
                $authentication = array(
                    "id" => $result["usuario"]["id"],
                    "token" => $result["usuario"]["token"]
                );
            }

            Log::write("info", sprintf("Fim autenticação."));

            return $authentication;
        } catch (\Exception $e) {
            Log::write("error", sprintf("Message: %s / Trace: %s", $e->getMessage(), $e->getTraceAsString()));
        }
    }

    /**
     * Realiza chamada de API
     *
     * @param [type] $method
     * @param [type] $url
     * @param boolean $data
     * @param string $dataType (json / xml)
     * @param string $authentication
     * @return void
     */
    public static function callAPI(string $method, string $url, $data = array(), string $dataType = DATA_TYPE_MESSAGE_JSON, string $authentication = "")
    {
        try {
            //code...

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            // curl_setopt($curl, CURLOPT_VERBOSE, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

            // https://stackoverflow.com/questions/30426047/correct-way-to-set-bearer-token-with-curl
            // https://stackoverflow.com/questions/6516902/how-to-get-response-using-curl-in-php
            // https://stackoverflow.com/questions/9802788/call-a-rest-api-in-php

            $dataSend = "";

            Log::write("info", __("Chamada à método: {0}", $url));

            if ($dataType == DATA_TYPE_MESSAGE_JSON) {
                $dataSend = json_encode($data);
            } elseif ($dataType == DATA_TYPE_MESSAGE_XML) {
                $dataSend = new \SimpleXMLElement($data);
            }

            switch ($method) {
                case "POST":
                    curl_setopt($curl, CURLOPT_POST, 1);

                    if ($data) {
                        curl_setopt($curl, CURLOPT_POSTFIELDS, $dataSend);

                        $header = array();
                        $header[] = "Accept: application/json";
                        if ($dataType == DATA_TYPE_MESSAGE_JSON) {
                            $header[] = "Content-Type: application/json";
                        } elseif ($dataType == DATA_TYPE_MESSAGE_XML) {
                            $header[] = "Content-Type: application/xml";
                        }
                        $header[] = "Content-Length: " . strlen($dataSend);
                        $header[] = "IsMobile: 1";
                        if (strlen($authentication) > 0) {
                            $header[] = "Authorization: Bearer $authentication";
                        }

                        // Log::write("info", $header);

                        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
                    }
                    break;
                case "PUT":
                    curl_setopt($curl, CURLOPT_PUT, 1);
                    break;
                default:
                    if ($data) {
                        $url = sprintf("%s?%s", $url, http_build_query($data));
                    }
            }

            $curlErr = curl_errno($curl);
            $curlError = curl_error($curl);

            if ($curlErr > 0) {
                throw new Exception($curlError, $curlErr);
            }

            $result = curl_exec($curl);
            curl_close($curl);
            $result = json_decode($result, true);

            Log::write("info", $result);

            Log::write("info", sprintf("Finalização de chamada a método %s...", $method));

            return $result;
        } catch (\Throwable $th) {
            $code = $th->getCode();
            $msg = $th->getMessage();

            Log::write("error", sprintf("[%s] %s - %s", MESSAGE_GENERIC_EXCEPTION, $code, $msg));
        }
    }

    /**
     * WebTools::getHtmlTest
     *
     * Obtem Html de Teste.
     *
     * Obtem HTML de teste para simulações no sistema. SOMENTE PARA DEBUG!
     *
     * @return string
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2020-02-10
     */
    private static function getHtmlTest()
    {
        $var = "<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='pt-br' lang='pt-br' class='ui-mobile'><head><base href='http://nfe.sefaz.ba.gov.br/servicos/nfce/modulos/geral/NFCEC_consulta_danfe.aspx'>
        <meta charset='utf-8'>
    	<meta name='viewport' content='width=device-width, initial-scale=1'>
        <meta http-equiv='X-UA-Compatible' content='IE=9, IE=edge'>
        <title>Nota Fiscal de Consumidor Eletrônica - NFC-e :: Consulta DANFE NFC-e</title>
        <script type='text/javascript' src='/servicos/nfce/ruxitagentjs_ICA27SVfgjqrux_10183200114120852.js' data-dtconfig='rid=RID_234714720|rpid=-1479859639|domain=ba.gov.br|reportUrl=/servicos/nfce/rb_939997cd-8ef5-4543-8275-2c66f4698102|app=ee3498f7edb0e356|srms=1,1,,,|uxrgcm=100,25,300,3;100,25,300,3|featureHash=ICA27SVfgjqrux|lastModification=1580318462093|dtVersion=10183200114120852|tp=500,50,0,1|rdnt=1|uxrgce=1|uxdcw=1500|bp=2|agentUri=/servicos/nfce/ruxitagentjs_ICA27SVfgjqrux_10183200114120852.js'></script><script src='../../arquivos_xslt/jquery.js' type='text/javascript'></script>
        <script src='../../arquivos_xslt/jqueryui.js' type='text/javascript'></script>
        <script src='../../arquivos_xslt/index.js' type='text/javascript'></script>
        <script src='../../arquivos_xslt/jquery.mobile-1.4.5.min.js' type='text/javascript'></script>
        <link href='../../arquivos_xslt/jquery.mobile-1.4.5.min.css' rel='stylesheet' type='text/css'>
        <link href='../../arquivos_xslt/nfceMob.css' rel='stylesheet' type='text/css'>
        <link href='../../arquivos_xslt/nfceMob_ie.css' rel='stylesheet' type='text/css'>
    <meta id='dcngeagmmhegagicpcmpinaoklddcgon'><style data-styled-components=''></style></head><evlist></evlist>
    <body class='ui-mobile-viewport ui-overlay-a'><div data-role='page' data-url='/servicos/nfce/modulos/geral/NFCEC_consulta_danfe.aspx' tabindex='0' class='ui-page ui-page-theme-a ui-page-active' style=''>
        <div id='containerSis'>
            <div class='contentForm'>
                <form method='post' action='./NFCEC_consulta_danfe.aspx' id='form1' enctype='ISO-8859-1' target='_self'>
    <div class='aspNetHidden'>
    <input type='hidden' name='__VIEWSTATE' id='__VIEWSTATE' value='/wEPDwUJNTczODkwOTgzD2QWBAIBD2QWBAIBDw8WAh4HVmlzaWJsZWhkZAIFDw8WAh8AaGRkAgMPDxYCHgRUZXh0BcElPGRpdiBkYXRhLXJvbGU9ImhlYWRlciIgeG1sbnM6bj0iaHR0cDovL3d3dy5wb3J0YWxmaXNjYWwuaW5mLmJyL25mZSIgeG1sbnM6Y2hhdmU9Imh0dHA6Ly9leHNsdC5vcmcvY2hhdmVhY2Vzc28iIHhtbG5zOnI9Imh0dHA6Ly93d3cuc2VycHJvLmdvdi5ici9uZmUvcmVtZXNzYW5mZS54c2QiPg0KICA8aDEgY2xhc3M9InRpdCI+DQogICAgPGltZyBzcmM9Ii4uLy4uL2ltYWdlbnMvbG9nb05GQ2UucG5nIiB3aWR0aD0iOTAiIGhlaWdodD0iNjQiIGFsdD0iTkZDLWUiPg0KICAgIDxwPkRPQ1VNRU5UTyBBVVhJTElBUiBEQSBOT1RBIEZJU0NBTCBERSBDT05TVU1JRE9SIEVMRVRSw5ROSUNBPC9wPg0KICAgIDxwPg0KICAgIDwvcD4NCiAgPC9oMT4NCjwvZGl2Pg0KPGRpdiBkYXRhLXJvbGU9ImNvbnRlbnQiIHhtbG5zOm49Imh0dHA6Ly93d3cucG9ydGFsZmlzY2FsLmluZi5ici9uZmUiIHhtbG5zOmNoYXZlPSJodHRwOi8vZXhzbHQub3JnL2NoYXZlYWNlc3NvIiB4bWxuczpyPSJodHRwOi8vd3d3LnNlcnByby5nb3YuYnIvbmZlL3JlbWVzc2FuZmUueHNkIj4NCiAgPGRpdiBpZD0iY29udGV1ZG8iPg0KICAgIDxkaXYgaWQ9ImF2aXNvcyI+DQogICAgPC9kaXY+DQogICAgPGRpdiBjbGFzcz0idHh0Q2VudGVyIj4NCiAgICAgIDxkaXYgaWQ9InUyMCIgY2xhc3M9InR4dFRvcG8iPlJFREUgSEcgQ09NQlVTVElWRUlTPC9kaXY+DQogICAgICA8ZGl2IGNsYXNzPSJ0ZXh0Ij4NCgkJICAgIENOUEo6DQoJCSAgICAxMy41NjkuMDY0LzAwMjgtNzA8L2Rpdj4NCiAgICAgIDxkaXYgY2xhc3M9InRleHQiPlJPRCBCUiAxMTYgLSBSSU8gQkFISUEsIA0KCQlTL04sDQoJCUtNIDc4NSBFIDMwIE1FVFJPUywNCgkJWk9OQSBSVVJBTCwNCgkJUExBTkFMVE8sDQoJCUJBPC9kaXY+DQogICAgPC9kaXY+DQogICAgPHRhYmxlIGJvcmRlcj0iMCIgYWxpZ249ImNlbnRlciIgY2VsbHBhZGRpbmc9IjAiIGNlbGxzcGFjaW5nPSIwIiBpZD0idGFiUmVzdWx0IiBkYXRhLWZpbHRlcj0idHJ1ZSI+DQogICAgICA8dHIgaWQ9Ikl0ZW0gKyAxIj4NCiAgICAgICAgPHRkIHZhbGlnbj0idG9wIj4NCiAgICAgICAgICA8c3BhbiBjbGFzcz0idHh0VGl0Ij5PTEVPIERJRVNFTCBCLVMxMDwvc3Bhbj4NCiAgICAgICAgICA8c3BhbiBjbGFzcz0iUkNvZCI+KEPDs2RpZ286IDUpPC9zcGFuPg0KICAgICAgICAgIDxicj4NCiAgICAgICAgICA8c3BhbiBjbGFzcz0iUnF0ZCI+DQogICAgICAgICAgICA8c3Ryb25nPlF0ZGUuOjwvc3Ryb25nPjM2MCwwMDM8L3NwYW4+DQogICAgICAgICAgPHNwYW4gY2xhc3M9IlJVTiI+DQogICAgICAgICAgICA8c3Ryb25nPlVOOiA8L3N0cm9uZz5MVDwvc3Bhbj4NCiAgICAgICAgICA8c3BhbiBjbGFzcz0iUnZsVW5pdCI+DQogICAgICAgICAgICA8c3Ryb25nPlZsLiBVbml0Ljo8L3N0cm9uZz7CoDMsNzQ8L3NwYW4+DQogICAgICAgIDwvdGQ+DQogICAgICAgIDx0ZCBhbGlnbj0icmlnaHQiIHZhbGlnbj0idG9wIiBjbGFzcz0idHh0VGl0IG5vV3JhcCI+VmwuIFRvdGFsPGJyPjxzcGFuIGNsYXNzPSJ2YWxvciI+MS4zNDYsNDE8L3NwYW4+PC90ZD4NCiAgICAgIDwvdHI+DQogICAgPC90YWJsZT4NCiAgICA8ZGl2IGlkPSJ0b3RhbE5vdGEiIGNsYXNzPSJ0eHRSaWdodCI+DQogICAgICA8ZGl2IGlkPSJsaW5oYVRvdGFsIj4NCiAgICAgICAgPGxhYmVsPlF0ZC4gdG90YWwgZGUgaXRlbnM6PC9sYWJlbD4NCiAgICAgICAgPHNwYW4gY2xhc3M9InRvdGFsTnVtYiI+MTwvc3Bhbj4NCiAgICAgIDwvZGl2Pg0KICAgICAgPGRpdiBpZD0ibGluaGFUb3RhbCIgY2xhc3M9ImxpbmhhU2hhZGUiPg0KICAgICAgICA8bGFiZWw+VmFsb3IgYSBwYWdhciBSJDo8L2xhYmVsPg0KICAgICAgICA8c3BhbiBjbGFzcz0idG90YWxOdW1iIHR4dE1heCI+MS4zNDYsNDE8L3NwYW4+DQogICAgICA8L2Rpdj4NCiAgICAgIDxkaXYgaWQ9ImxpbmhhRm9ybWEiPg0KICAgICAgICA8bGFiZWw+Rm9ybWEgZGUgcGFnYW1lbnRvOjwvbGFiZWw+DQogICAgICAgIDxzcGFuIGNsYXNzPSJ0b3RhbE51bWIgdHh0VGl0UiI+VmFsb3IgcGFnbyBSJDo8L3NwYW4+DQogICAgICA8L2Rpdj4NCiAgICAgIDxkaXYgaWQ9ImxpbmhhVG90YWwiPg0KICAgICAgICA8bGFiZWwgY2xhc3M9InR4Ij4NCiAgICAgICAgICAgIFZhbGUgQ29tYnVzdMOtdmVsDQogICAgICAgICAgPC9sYWJlbD4NCiAgICAgICAgPHNwYW4gY2xhc3M9InRvdGFsTnVtYiI+MS4zNDYsNDE8L3NwYW4+DQogICAgICA8L2Rpdj4NCiAgICAgIDxkaXYgaWQ9ImxpbmhhVG90YWwiPg0KICAgICAgICA8bGFiZWwgY2xhc3M9InR4Ij5Ucm9jbyA8L2xhYmVsPg0KICAgICAgICA8c3BhbiBjbGFzcz0idG90YWxOdW1iIj5OYU48L3NwYW4+DQogICAgICA8L2Rpdj4NCiAgICAgIDxkaXYgaWQ9ImxpbmhhVG90YWwiIGNsYXNzPSJzcGNUb3AiPg0KICAgICAgICA8bGFiZWwgY2xhc3M9InR4dE9icyI+SW5mb3JtYcOnw6NvIGRvcyBUcmlidXRvcyBUb3RhaXMgSW5jaWRlbnRlcyAoTGVpIEZlZGVyYWwgMTIuNzQxLzIwMTIpwqBSJDwvbGFiZWw+DQogICAgICAgIDxzcGFuIGNsYXNzPSJ0b3RhbE51bWIgdHh0T2JzIj41NDQsNjI8L3NwYW4+DQogICAgICA8L2Rpdj4NCiAgICA8L2Rpdj4NCiAgPC9kaXY+DQogIDxkaXYgaWQ9ImluZm9zIiBjbGFzcz0idHh0Q2VudGVyIj4NCiAgICA8ZGl2IGRhdGEtcm9sZT0iY29sbGFwc2libGUiIGRhdGEtY29sbGFwc2VkLWljb249ImNhcmF0LWQiIGRhdGEtZXhwYW5kZWQtaWNvbj0iY2FyYXQtdSIgZGF0YS1jb2xsYXBzZWQ9ImZhbHNlIj4NCiAgICAgIDxoND5JbmZvcm1hw6fDtWVzIGdlcmFpcyBkYSBOb3RhPC9oND4NCiAgICAgIDx1bCBkYXRhLXJvbGU9Imxpc3R2aWV3IiBkYXRhLWluc2V0PSJmYWxzZSI+DQogICAgICAgIDxsaT4NCiAgICAgICAgICA8c3Ryb25nPkVNSVNTw4NPIE5PUk1BTDwvc3Ryb25nPg0KICAgICAgICAgIDxicj4NCiAgICAgICAgICA8YnI+DQogICAgICAgICAgPHN0cm9uZz5Ow7ptZXJvOiA8L3N0cm9uZz4xMDc5NTE8c3Ryb25nPiBTw6lyaWU6IDwvc3Ryb25nPjI8c3Ryb25nPiBFbWlzc8OjbzogPC9zdHJvbmc+MjcvMDEvMjAyMCAxNzo0OTo0NS0wMzowMCAgLSBWaWEgQ29uc3VtaWRvcg0KPGJyPjxicj48c3Ryb25nPlByb3RvY29sbyBkZSBBdXRvcml6YcOnw6NvOiA8L3N0cm9uZz4zMjkyMDAwODU0MTI5NDUgICAgICAgMjcvMDEvMjAyMCAxNzo0OTo0NC0wMzowMDxicj48YnI+PHN0cm9uZz5BbWJpZW50ZSBkZSBQcm9kdcOnw6NvIC0gDQpWZXJzw6NvIFhNTDogNC4wMCAtIFZlcnPDo28gWFNMVDogMi4wNDwvc3Ryb25nPjwvbGk+DQogICAgICA8L3VsPg0KICAgIDwvZGl2Pg0KICAgIDxkaXYgZGF0YS1yb2xlPSJjb2xsYXBzaWJsZSIgZGF0YS1jb2xsYXBzZWQtaWNvbj0iY2FyYXQtZCIgZGF0YS1leHBhbmRlZC1pY29uPSJjYXJhdC11IiBkYXRhLWNvbGxhcHNlZD0iZmFsc2UiPg0KICAgICAgPGg0PkNoYXZlIGRlIGFjZXNzbzwvaDQ+DQogICAgICA8dWwgZGF0YS1yb2xlPSJsaXN0dmlldyIgZGF0YS1pbnNldD0iZmFsc2UiPg0KICAgICAgICA8bGk+DQogIENvbnN1bHRlIHBlbGEgQ2hhdmUgZGUgQWNlc3NvIGVtDQoNCiAgaHR0cDovL3d3dy5zZWZhei5iYS5nb3YuYnIvbmZjZS9jb25zdWx0YTxicj48YnI+PHN0cm9uZz5DaGF2ZSBkZSBhY2Vzc286PC9zdHJvbmc+PGJyPjxzcGFuIGNsYXNzPSJjaGF2ZSI+MjkyMCAwMTEzIDU2OTAgNjQwMCAyODcwIDY1MDAgMjAwMCAxMDc5IDUxMTAgMDM0MyA1MDcwPC9zcGFuPjwvbGk+DQogICAgICA8L3VsPg0KICAgIDwvZGl2Pg0KICAgIDxkaXYgZGF0YS1yb2xlPSJjb2xsYXBzaWJsZSIgZGF0YS1jb2xsYXBzZWQtaWNvbj0iY2FyYXQtZCIgZGF0YS1leHBhbmRlZC1pY29uPSJjYXJhdC11IiBkYXRhLWNvbGxhcHNlZD0iZmFsc2UiPg0KICAgICAgPGg0PkNvbnN1bWlkb3I8L2g0Pg0KICAgICAgPHVsIGRhdGEtcm9sZT0ibGlzdHZpZXciIGRhdGEtaW5zZXQ9ImZhbHNlIj4NCiAgICAgICAgPGxpPg0KICAgICAgICAgIDxzdHJvbmc+Q05QSjogPC9zdHJvbmc+MTMuNTY5LjA2NC8wMDQ3LTMyPC9saT4NCiAgICAgICAgPGxpPg0KICAgICAgICAgIDxzdHJvbmc+UmF6w6NvIFNvY2lhbDogPC9zdHJvbmc+MTExNTI5IFJFREUgSEcgQ09NQlVTVElWRUlTIExUREE8L2xpPg0KICAgICAgICA8bGk+DQogICAgICAgICAgPHN0cm9uZz5Mb2dyYWRvdXJvOiA8L3N0cm9uZz5BVkVOSURBIFJJT0JBSElBLCANCgkJU04sDQoJCSwNCgkJUExBTkFMVE8sDQoJCUdPVkVSTkFET1IgVkFMQURBUkVTLA0KCQlNRzwvbGk+DQogICAgICA8L3VsPg0KICAgIDwvZGl2Pg0KICAgIDxkaXYgZGF0YS1yb2xlPSJjb2xsYXBzaWJsZSIgZGF0YS1jb2xsYXBzZWQtaWNvbj0iY2FyYXQtZCIgZGF0YS1leHBhbmRlZC1pY29uPSJjYXJhdC11IiBkYXRhLWNvbGxhcHNlZD0iZmFsc2UiPg0KICAgICAgPGg0PkluZm9ybWHDp8O1ZXMgZGUgaW50ZXJlc3NlIGRvIGNvbnRyaWJ1aW50ZTwvaDQ+DQogICAgICA8dWwgZGF0YS1yb2xlPSJsaXN0dmlldyIgZGF0YS1pbnNldD0iZmFsc2UiPg0KICAgICAgICA8bGk+VHJpYnV0b3MgUiQ6IEZlZDoxODEsMDkgRXN0OjM2Myw1MyBNdW46MCwwMCBGb250ZTpJQlBUIDBDMzgyOSB8IE5vdGE6MzQzNTA3IERhdGE6MjcvMDEvMjAgQ3g6MTMgRnVuYzo3MjMgUExBQ0E6T0tSMzI5OCBLTTo3MzQuMzQ1LDAwPC9saT4NCiAgICAgIDwvdWw+DQogICAgPC9kaXY+DQogIDwvZGl2Pg0KICA8ZGl2IGNsYXNzPSJmb290ZXJTZWZhekJhIj4NCiAgICAgIFNFQ1JFVEFSSUEgREEgRkFaRU5EQSBETyBFU1RBRE8gREEgQkFISUENCgk8L2Rpdj4NCjwvZGl2PmRkZJyy9xRsamBfZ2T3MYyvW35IvvhI5XuqzNzlGvDeIRT3'>
    </div>
    <div class='aspNetHidden'>
    	<input type='hidden' name='__VIEWSTATEGENERATOR' id='__VIEWSTATEGENERATOR' value='CBF17B24'>
    	<input type='hidden' name='__EVENTVALIDATION' id='__EVENTVALIDATION' value='/wEdAAKAo39bRvIzqMdYWPuSD8esdjn3fyqxLz+ZJeNkc6yvRwDwTsFx0K4RbjeWrj5r04emoYjSmn34KtsyRPgztzs3'>
    </div>
                    <div id='cont-botoes' style='text-align:center; background-color:white;position: relative; display: block;z-index: 50;'>
                        <div id='menu_botoes' style='text-align:center; background-color:white; padding-top:20px;'>
                            <div class='ui-btn ui-input-btn ui-corner-all ui-shadow'>Visualizar em Abas<input type='submit' name='btn_visualizar_abas' value='Visualizar em Abas' id='btn_visualizar_abas'></div>
                        </div>
                        <br>
                    </div>
                </form>
            </div>
        </div>
        <div style='position:relative;'>
            <span id='txt_xslt'><div data-role='header' xmlns:n='http://www.portalfiscal.inf.br/nfe' xmlns:chave='http://exslt.org/chaveacesso' xmlns:r='http://www.serpro.gov.br/nfe/remessanfe.xsd' role='banner' class='ui-header ui-bar-inherit'>
      <h1 class='tit ui-title' role='heading' aria-level='1'>
        <img src='../../imagens/logoNFCe.png' width='90' height='64' alt='NFC-e'>
        <p>DOCUMENTO AUXILIAR DA NOTA FISCAL DE CONSUMIDOR ELETRÔNICA</p>
        <p>
        </p>
      </h1>
    </div>
    <div data-role='content' xmlns:n='http://www.portalfiscal.inf.br/nfe' xmlns:chave='http://exslt.org/chaveacesso' xmlns:r='http://www.serpro.gov.br/nfe/remessanfe.xsd' class='ui-content' role='main'>
    Não foi possível obter informações sobre a NFC-e
        <div data-role='collapsible' data-collapsed-icon='carat-d' data-expanded-icon='carat-u' data-collapsed='false' class='ui-collapsible ui-collapsible-inset ui-corner-all ui-collapsible-themed-content'><h4 class='ui-collapsible-heading'><a href='#' class='ui-collapsible-heading-toggle ui-btn ui-icon-carat-u ui-btn-icon-left ui-btn-inherit'>Informações gerais da Nota<span class='ui-collapsible-heading-status'> click to collapse contents</span></a></h4><div class='ui-collapsible-content ui-body-inherit' aria-hidden='false'>
          <ul data-role='listview' data-inset='false' class='ui-listview'>
            <li class='ui-li-static ui-body-inherit ui-first-child ui-last-child'>
              <strong>EMISSÃO NORMAL</strong>
              <br>
              <br>
              <strong>Número: </strong>107951<strong> Série: </strong>2<strong> Emissão: </strong>27/01/2020 17:49:45-03:00  - Via Consumidor
    <br><br><strong>Protocolo de Autorização: </strong>329200085412945       27/01/2020 17:49:44-03:00<br><br><strong>Ambiente de Produção -
    Versão XML: 4.00 - Versão XSLT: 2.04</strong></li>
          </ul>
        </div></div>
        <div data-role='collapsible' data-collapsed-icon='carat-d' data-expanded-icon='carat-u' data-collapsed='false' class='ui-collapsible ui-collapsible-inset ui-corner-all ui-collapsible-themed-content'><h4 class='ui-collapsible-heading'><a href='#' class='ui-collapsible-heading-toggle ui-btn ui-icon-carat-u ui-btn-icon-left ui-btn-inherit'>Chave de acesso<span class='ui-collapsible-heading-status'> click to collapse contents</span></a></h4><div class='ui-collapsible-content ui-body-inherit' aria-hidden='false'>
          <ul data-role='listview' data-inset='false' class='ui-listview'>
            <li class='ui-li-static ui-body-inherit ui-first-child ui-last-child'>
      Consulte pela Chave de Acesso em

      http://www.sefaz.ba.gov.br/nfce/consulta<br><br><strong>Chave de acesso:</strong><br><span class='chave'>2920 0113 5690 6400 2870 6500 2000 1079 5110 0343 5070</span></li>
          </ul>
        </div></div>
        <div data-role='collapsible' data-collapsed-icon='carat-d' data-expanded-icon='carat-u' data-collapsed='false' class='ui-collapsible ui-collapsible-inset ui-corner-all ui-collapsible-themed-content'><h4 class='ui-collapsible-heading'><a href='#' class='ui-collapsible-heading-toggle ui-btn ui-icon-carat-u ui-btn-icon-left ui-btn-inherit'>Consumidor<span class='ui-collapsible-heading-status'> click to collapse contents</span></a></h4><div class='ui-collapsible-content ui-body-inherit' aria-hidden='false'>

          <ul data-role='listview' data-inset='false' class='ui-listview'>
            <li class='ui-li-static ui-body-inherit ui-first-child'>
              <strong>CNPJ: </strong>13.569.064/0047-32</li>
            <li class='ui-li-static ui-body-inherit'>
              <strong>Razão Social: </strong>111529 REDE HG COMBUSTIVEIS LTDA</li>
            <li class='ui-li-static ui-body-inherit ui-last-child'>
              <strong>Logradouro: </strong>AVENIDA RIOBAHIA,
    		SN,
    		,
    		PLANALTO,
    		GOVERNADOR VALADARES,
    		MG</li>
          </ul>
        </div></div>
        <div data-role='collapsible' data-collapsed-icon='carat-d' data-expanded-icon='carat-u' data-collapsed='false' class='ui-collapsible ui-collapsible-inset ui-corner-all ui-collapsible-themed-content'><h4 class='ui-collapsible-heading'><a href='#' class='ui-collapsible-heading-toggle ui-btn ui-icon-carat-u ui-btn-icon-left ui-btn-inherit'>Informações de interesse do contribuinte<span class='ui-collapsible-heading-status'> click to collapse contents</span></a></h4><div class='ui-collapsible-content ui-body-inherit' aria-hidden='false'>
          <ul data-role='listview' data-inset='false' class='ui-listview'>
            <li class='ui-li-static ui-body-inherit ui-first-child ui-last-child'>Tributos R$: Fed:181,09 Est:363,53 Mun:0,00 Fonte:IBPT 0C3829 | Nota:343507 Data:27/01/20 Cx:13 Func:723 PLACA:OKR3298 KM:734.345,00</li>
          </ul>
        </div></div>
      </div>
      <div class='footerSefazBa'>
          SECRETARIA DA FAZENDA DO ESTADO DA BAHIA
    	</div>
    </div></span>&nbsp;
        </div>
    </div><div class='ui-loader ui-corner-all ui-body-a ui-loader-default'><span class='ui-icon-loading'></span><h1>loading</h1></div><style>.c5lrbBGOUex0dguYrE { color:#2980b9 !important; }
    .c5lrbBGOUex0dguYrE.ba-outline { outline-color:#2980b9 }
    .c5lrbBGOUex0dguYrE.E1K0N8YhM2I:hover, .c5lrbBGOUex0dguYrE.E1K0N8YhM2I.active { color:#186ea3 !important; }
    .EWvoheEBWwf5f6 { background-color:#2980b9 !important; }
    .EWvoheEBWwf5f6.E1K0N8YhM2I:hover, .EWvoheEBWwf5f6.E1K0N8YhM2I.active { background:#186ea3 !important; }
    .ba-border-primary { border-color:#166a9e !important }
    .Vs3SDiFull73 { background:#2980b9 !important; color:white !important; }
    .Vs3SDiFull73.E1K0N8YhM2I:hover, .Vs3SDiFull73.E1K0N8YhM2I.active { background:#1b72a8 !important; }
    .ba-color-secondary { color:#2980b9 !important;  }
    .ba-color-secondary.E1K0N8YhM2I:hover, .ba-color-secondary.E1K0N8YhM2I.active { color:#1b72a8 !important; }
    .ba-color-tertiary { color:#eb9f2d !important }
    .ba-color-tertiary.E1K0N8YhM2I:hover, .ba-color-tertiary.E1K0N8YhM2I.active { color:#db7f15 !important }
    .wfm5pAuJgf6nmV { background:#eb9f2d !important }
    .ba-bg-tertiary.E1K0N8YhM2I:hover, .ba-bg-tertiary.E1K0N8YhM2I.active { background:#db7f15 !important }</style></body></html>";
        return $var;
    }
}
