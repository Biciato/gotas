<?php

/**
 * Classe de Utilidades para Html
 *
 * @category ClasseDeUtilidades
 * @package  Custom
 * @author   "Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>"
 * @date     28/02/2020
 * @since    1.1.5
 * @license  http://www.apache.org/licenses/LICENSE-2.0.txt Apache License 2.0
 */

namespace App\Custom\RTI;

/**
 * Classe de manipulação de Data
 *
 * @category ClasseDeUtilidades
 * @package  Custom
 * @author   "Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>"
 * @date     28/02/2020
 * @since    1.1.5
 * @license  http://www.apache.org/licenses/LICENSE-2.0.txt Apache License 2.0
 */
class HtmlUtil
{
    /**
     * Gera Tabela Html
     *
     * Gera uma tabela HTML através de título, cabeçalho e conteúdo.
     * Se informado a opção $toggleLineColor, a linha alterará o cor de fundo
     *
     * @param string $title Título da Tabela
     * @param array $headers Cabeçalho da Tabela
     * @param array $contentData Conteudo
     * @param boolean $toggleLineColor
     *
     * @return string $table Tabela Html em String
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.1.5
     */
    public static function generateHTMLTable(string $title, array $headers, array $contentData, bool $toggleLineColor = false)
    {
        // Cabeçalho
        $table = "
        <table>
            <tr style='text-align:center;'>
                <td colspan=" . sizeof($headers) . " style='margin: 0px auto; border: 1px solid black !important; background-color: #BDD7EE; font-weight: bolder;'>" . strtoupper($title) . "</td>
            </tr>
            <tr>";

        foreach ($headers as $header) {
            $table .= "<td style='border: 1px solid black; background-color: #D6DCE4; font-weight: bolder;'>" . strtoupper($header) . "</td>";
        }
        $table .= "</tr>";

        $backgroundColor = '';
        // Corpo
        foreach ($contentData as $data) {
            $table .= "<tr>";

            $backgroundColor = $toggleLineColor && strlen($backgroundColor) == 0 ? "#a1e8ea" : "#000000";

            $rowStyle = "style='border:1px solid black;" + "background-color: {$backgroundColor};'";
            foreach ($data as $column) {
                $table .= "<td {$rowStyle}>" . $column . "</td>";
            }
            $table .= "</tr>";
        }
        $table .= "</table>";

        return $table;
    }
}
