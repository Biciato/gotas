<?php

namespace App\Custom\RTI;

use App\Controller\AppController;

use Cake\Core\Configure;
use Cake\Log\Log;

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
            $header[] = "Accept: application/xml;text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
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

            Log::write('debug', $response);

            $error = \curl_errno($curl);
            $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            // só pode prosseguir se houve status= 200
            $status = 400;
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
        $curl = curl_init();

        // $suffix = Configure::read("debug") ? ".local" : ".com.br";
        $suffix = "local";

        $url = "https://sistema.gotas.".$suffix."/api/usuarios/token";

        $data = array("email" => $email, "senha" => $senha);

        // https://stackoverflow.com/questions/30426047/correct-way-to-set-bearer-token-with-curl
        // https://stackoverflow.com/questions/6516902/how-to-get-response-using-curl-in-php
        // https://stackoverflow.com/questions/9802788/call-a-rest-api-in-php

        $method = "POST";
        switch ($method) {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);

                if ($data) {
                    $dataJson = json_encode($data);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $dataJson);
                    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                        "Content-Type: application/json",
                        "Content-Length: " . strlen($dataJson),
                        "IsMobile: 1"
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


        // Optional Authentication:
        // curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        // curl_setopt($curl, CURLOPT_USERPWD, "mobileapiworker@dummy.com:1234");

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

        $curlErr = curl_errno($curl);
        $curlError = curl_error($curl);
        Log::write("info", array("err" => $curlErr, "str" => $curlError));
        $result = curl_exec($curl);
        $result = json_decode($result);
        Log::write("info", array("result" => $result));

        curl_close($curl);

        return $result;
    }

    /**
     * Realiza chamada de API
     *
     * @param [type] $method
     * @param [type] $url
     * @param boolean $data
     * @param string $authentication
     * @return void
     */
    public static function callAPI($method, $url, $data = false, string $authentication = "")
    {
        $curl = curl_init();

        // https://stackoverflow.com/questions/30426047/correct-way-to-set-bearer-token-with-curl
        // https://stackoverflow.com/questions/6516902/how-to-get-response-using-curl-in-php
        // https://stackoverflow.com/questions/9802788/call-a-rest-api-in-php

        switch ($method) {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);

                if ($data) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

                    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                        "Content-Type: application/json",
                        "Content-Length: " . strlen($dataJson),
                        "IsMobile: 1"
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


        // Optional Authentication:
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, "mobileapiworker@dummy.com:1234");

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $curlErr = curl_errno($curl);
        $curlError = curl_error($curl);
        Log::write("info", array("err" => $curlErr, "str" => $curlError));
        $result = curl_exec($curl);

        curl_close($curl);

        return $result;
    }
}
