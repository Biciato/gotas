<?php

/**
 * Classe de Utilidades para objetos do tipo datetime
 *
 * @category ClasseDeUtilidades
 * @package  Custom
 * @author   "Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>"
 * @date     03/08/2017
 * @license  http://www.apache.org/licenses/LICENSE-2.0.txt Apache License 2.0
 * @link     https://www.rtibrindes.com.br/Utils/ClasseDeUtilidades
 */

namespace App\Custom\RTI;

use App\Controller\AppController;

use Cake\Core\Configure;

/**
 * Classe de manipulação de Data
 *
 * @category ClasseDeUtilidades
 * @package  Custom
 * @author   "Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>"
 * @date     03/08/2017
 * @license  http://www.apache.org/licenses/LICENSE-2.0.txt Apache License 2.0
 * @link     https://www.rtibrindes.com.br/Utils/ClasseDeUtilidades
 */
class DateTimeUtil
{
    /**
     * Construtor
     */
    function __construct()
    {
    }

    /**
     * Converte formato Data Hora para padrão UTC de banco
     *
     * @param string $datetime DataHora a ser convertida em formato UTC
     *
     * @return datetime object
     */
    public function convertDateTimeToUTC(string $datetime)
    {
        $date = date_create_from_format('d/m/Y H:i:s', $datetime);

        return date_format($date, 'Y-m-d H:i:s');
    }

    /**
     * Converte formato Data para padrão UTC
     *
     * @param  string   $date   Data a ser convertida
     * @param  string   $format Formato à ser aplicado
     * @return datetime $data
     */
    public static function convertDateToUTC(string $dateToFormat, string $format = null)
    {
        $format = is_null($format) ? 'Y-m-d' : $format;

        $dateToFormat = substr($dateToFormat, 0, 10);

        $dateReturn = (new \Datetime)->createFromFormat($format, $dateToFormat);

        return $dateReturn->format('Y-m-d');
    }

    /**
     * Converte formato Data para padrão UTC
     *
     * @param  string   $date   Data a ser convertida
     * @param  string   $format Formato à ser aplicado
     * @return datetime $data
     */
    public static function convertDateToPTBRFormat(string $dateToFormat, string $format = null)
    {
        $format = is_null($format) ? 'Y-m-d' : $format;

        $dateToFormat = substr($dateToFormat, 0, 10);

        $dateReturn = (new \Datetime)->createFromFormat($format, $dateToFormat);

        return $dateReturn->format('d/m/Y');
    }

    /**
     * Undocumented function
     *
     * @param string $datetime      DataHora a ser reduzida
     * @param int    $days          Quantidade de dias
     * @param string $format_return Formato de retorno
     *
     * @return void
     */
    public function substractDaysFromDateTime(string $datetime, int $days, string $format_return = null)
    {
        try {
            $date_return = strtotime($datetime . ' -' . $days . ' days');

            $format = $format_return;

            if (is_null($format)) {
                $format = 'Y-m-d H:i:s';
            }

            return date($format, $date_return);
        } catch (\Exception $e) {
            // TODO: fazer exception
        }
    }

    /**
     * Undocumented function
     *
     * @param string $datetime      DataHora a ser reduzida
     * @param int    $days          Quantidade de dias
     * @param string $format_return Formato de retorno
     *
     * @return void
     */
    public function substractYearsFromDateTime(string $datetime, int $years, string $format_return = null)
    {
        try {
            $date_return = strtotime($datetime . ' -' . $years . ' years');

            $format = $format_return;

            if (is_null($format)) {
                $format = 'Y-m-d H:i:s';
            }

            return date($format, $date_return);
        } catch (\Exception $e) {
            // TODO: fazer exception
        }
    }

}
