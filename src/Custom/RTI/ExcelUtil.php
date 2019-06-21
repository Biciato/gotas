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
            <tr style='text-align:center;'>
                <td colspan=" . sizeof($headers) . " style='margin: 0px auto; border: 1px solid black !important; background-color: #BDD7EE; font-weight: bolder;'>".strtoupper($title)."</td>";
        $excelContent .= "<tr>";

        foreach ($headers as $header) {
            $excelContent .= "<td style='border: 1px solid black; background-color: #D6DCE4; font-weight: bolder;'>" . strtoupper($header) . "</td>";
        }
        $excelContent .= "</tr>";

        // Corpo
        foreach ($contentData as $data) {
            $excelContent .= "<tr>";
            foreach ($data as $column) {
                $excelContent .= "<td style='border:1px solid black;'>" . $column . "</td>";
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
