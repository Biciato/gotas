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
            curl_setopt($curl, CURLOPT_PORT, $_SERVER['SERVER_PORT']);


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
}
