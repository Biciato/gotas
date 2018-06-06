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
}
