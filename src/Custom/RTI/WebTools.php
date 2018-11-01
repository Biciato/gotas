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
    public function getPageContent(string $url)
    {
        try {

            $curl = curl_init($url);

            $user_agent = 'Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';

            $header = [];
            $header[] = "Accept: text/xml,text/csv,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
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
                    CURLOPT_USERAGENT => $user_agent, //set user agent
                    CURLOPT_COOKIEFILE => "cookie.txt", //set cookie file
                    CURLOPT_COOKIEJAR => "cookie.txt", //set cookie jar
                    CURLOPT_RETURNTRANSFER => true,     // return web page
                    CURLOPT_HEADER => false,    // don't return headers
                    CURLOPT_FOLLOWLOCATION => true,     // follow redirects
                    CURLOPT_ENCODING => "",       // handle all encodings
                    CURLOPT_AUTOREFERER => true,     // set referer on redirect
                    CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
                    CURLOPT_TIMEOUT => 120,      // timeout on response
                    CURLOPT_MAXREDIRS => 30,       // stop after 10 redirects
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_BUFFERSIZE, 1024 * 1024 * 1024 // curl buffer size in bytes

                ]
            );

            $response = curl_exec($curl);

            $response = trim(preg_replace('/\s+/', ' ', $response));

            // $response = utf8_encode($response);

            Log::write('debug', $response);

            $error = \curl_errno($curl);

            $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            curl_close($curl);

            // só pode prosseguir se houve status= 200
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
}

// $response = curl_exec($curl);

// curl_close($curl);

// $error = \curl_errno($curl);

// $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);


// // while (strpos($response, "  ") !== false){
// //     $response = str_replace("  ", " ", $response, $count);
// // }

// // $response = json_encode($response);
// Log::write('debug', $response);
// // curl_close($curl);

// // só pode prosseguir se houve status= 200
// $result = array();

// if ($status == 200) {
//     // ok
//     // $response = trim($response);
//     $result = (['response' => $response, 'url' => $url, 'status' => 'online', 'statusCode' => $status]);
// } elseif ($status >= 400) {
//     // em qualquer outro caso, gera erro
//     $result = (['response' => $response, 'url' => $url, 'status' => 'offline', 'statusCode' => $status]);
//     // $result = (['response' => null, 'url' => $url, 'status' => 'offline', 'statusCode' => $status]);
// }


// return $result;
