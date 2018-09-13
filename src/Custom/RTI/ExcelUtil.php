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
use Cake\Mailer\Excel;
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
class ExcelUtil
{
    public static function generateExcel($title, $headers, $contentData)
    {
        $file = "demo.xls";
        // <meta http-equiv='content-type' content='application/xhtml+xml; charset=UTF-8' />
        // Cabeçalho
        $excelContent = "
        <html>
        <meta http-equiv='content-type' content='text/html; charset=UTF-8' />
        <table>
            <tr>
                <td colspan=" . sizeof($headers) . " style='margin: 0px auto'>TÍTULO</td>
            <tr style='background-color: #a0efe6; font-weight: bolder;'>
        ";

        foreach ($headers as $header) {
            $excelContent .= "<td style='border: 1px solid black;'>" . strtoupper($header) . "</td>";
        }
        $excelContent .= "</tr>";

        // Corpo
        foreach ($contentData as $data) {
            $excelContent .= "<tr>";
            foreach ($data as $column) {
                $excelContent .= "<td>" . $column . "</td>";
            }
            $excelContent .= "</tr>";
        }
        $excelContent .= "</table>
        </html>
        ";
        return json_encode($excelContent);
        // return base64_encode($excelContent);
    }
}
