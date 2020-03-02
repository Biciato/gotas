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
        $table  = "<table>";
        $table .= "    <tr style='text-align:center;'>";
        $table .= "        <td colspan=" . count($headers) . " style='margin: 0px auto; border: 1px solid black !important; background-color: #BDD7EE; font-weight: bolder;'>" . strtoupper($title) . "</td>";
        $table .= "    </tr>";
        $table .= "    <tr>";

        foreach ($headers as $header) {
            $table .= "<td style='border: 1px solid black; background-color: #D6DCE4; font-weight: bolder;'>" . strtoupper($header)
                . " </td>";
        }
        $table .= "</tr>";

        $backgroundColor = '';
        $stepColor = 0;
        // Corpo
        foreach ($contentData as $data) {
            $backgroundColor = $toggleLineColor && $stepColor === 0 ? "#a1e8ea" : "#FFFFFF";
            $rowStyle = "style='border:1px solid black;" . "background-color: {$backgroundColor};'";

            $table .= "<tr {$rowStyle}>";

            foreach ($data as $column) {
                $table .= "<td style='border:1px solid black;'>{$column}</td>";
            }
            $stepColor = $toggleLineColor && $stepColor === 0 ? 1 : 0;
            $table .= "</tr>";
        }
        $table .= "</table>";

        return $table;
    }
}
