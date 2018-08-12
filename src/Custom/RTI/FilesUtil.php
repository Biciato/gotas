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
use Cake\Mailer\Files;
use Cake\Core\Configure;
use Cake\Log\Log;
use App\Model\Entity\Usuario;

/**
 * Classe de manipulação de Data
 *
 * @category ClasseDeUtilidades
 * @package  Custom
 * @author   "Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>"
 * @date     23/05/2018
 * @license  http://www.apache.org/licenses/LICENSE-2.0.txt Apache License 2.0
 * @link     https://www.rtibrindes.com.br/Utils/ClasseDeUtilidades
 */
class FilesUtil
{
    /**
     * FilesUtil::uploadFiles
     *
     * Faz upload de Imagens
     *
     * @param string $tempPath Caminho temporário para envio dos arquivos
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 26/05/2018
     *
     * @return array(status, files[]) Lista de Arquivos enviados
     *  files["path", "file"]
     */
    public static function uploadFiles(string $tempPath)
    {
        $status = false;
        $arquivos = array();

        FilesUtil::createPathIfNotExists($tempPath);

        foreach ($_FILES as $key => $file) {
            // gera nome aleatorio para arquivo

            $newName = bin2hex(openssl_random_pseudo_bytes(16));
            $status = true;

            $extension = substr($file["name"], stripos($file["name"], "."));

            $newGeneratedName = $newName . $extension;

            $path = $tempPath . $newName . $extension;

            move_uploaded_file($file["tmp_name"], WWW_ROOT . $tempPath.$newGeneratedName);

            // move o arquivo enviado e retorna sua string à tela principal

            $arquivos[] = array('path' => Configure::read("appAddress") . 'webroot/' . $tempPath . $newGeneratedName, "file" => $newGeneratedName);
        }

        return array("status" => $status, "filesUploaded" => $arquivos);
    }

    /**
     * FilesUtil::downloadFile
     *
     * Efetua download de arquivo através de url
     *
     * @param string $url Link para arquivo
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2018-08-11
     *
     * @return array Array com informações array("response", "url", "status", "statusCode")
     */
    public static function downloadFile($url)
    {
        $curl = curl_init($url);
        $user_agent = 'Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';

        $header = array();
        $header[] = "Accept: text/xml,text/csv,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
        $header[] = "Connection: keep-alive";
        $header[] = "Keep-Alive: 300";

        curl_setopt_array(
            $curl,
            [
                CURLOPT_RETURNTRANSFER => 1, // return web page
                CURLOPT_URL => $url,
                CURLOPT_HTTPHEADER => $header,
                CURLOPT_CUSTOMREQUEST => "GET",        //set request type post or get
                CURLOPT_POST => false,        //set to GET
                CURLOPT_USERAGENT => $user_agent, //set user agent
                CURLOPT_COOKIEFILE => "cookie.txt", //set cookie file
                CURLOPT_COOKIEJAR => "cookie.txt", //set cookie jar
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
        $error = \curl_errno($curl);
        $stringError = curl_error($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        // só pode prosseguir se houve status= 200
        $result = null;

        if ($status == 200) {
            $result = (['response' => $response, 'url' => $url, 'status' => 'online', 'statusCode' => $status]);
        } else {
            // em qualquer outro caso, gera erro
            throw new \Exception(__("Erro ao realizar download de arquivo: {0}", $stringError), $error);
            // $result = (['response' => null, 'url' => $url, 'status' => 'offline', 'statusCode' => $status]);
        }
        return $result;
    }

    public static function createPathIfNotExists(string $path)
    {
        try {
            // debug(WWW_ROOT . $path);
            if (!file_exists(WWW_ROOT. $path)) {
                mkdir($path, 0777, true);
            }
        } catch (\Exception $e) {
            Log::write('error', $e->getMessage());
        }
    }

    /**
     * Move documento informado para novo caminho
     */
    public static function moveDocumentPermanently($originalPath, $newPath, $newName = null, $extension = null)
    {
        try {
            if (is_null($newName)) {
                $newName = bin2hex(openssl_random_pseudo_bytes(16));
            }

            $this->createPathIfNotExists($newPath);

            $extension = "jpg";
            $dotPosition = strlen(strpos($extension, ".")) > 0 ? 1 : 0;

            if (!$dotPosition) {
                $extension = "." . $extension;
            }

            $newGeneratedName = $newName . $extension;

            $newPath = $newPath . $newName . $extension;
            rename($originalPath, $newPath);

            return $newGeneratedName;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao mover documento: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }
}
