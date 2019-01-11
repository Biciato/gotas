<?php

namespace App\Custom\RTI;

use \DateTime;
use App\Controller\AppController;
use Cake\Mailer\Files;
use Cake\Core\Configure;
use Cake\Log\Log;

/**
 * Classe de Utilidades para Tempo
 *
 * @category ClasseDeUtilidades
 * @package  Custom
 * @author   "Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>"
 * @since    2019-01-11
 * @license  http://www.apache.org/licenses/LICENSE-2.0.txt Apache License 2.0
 * @link     https://www.rtibrindes.com.br/Utils/ClasseDeUtilidades
 */
class TimeUtil
{
    /**
     * TimeUtil::getTurnoAnteriorAtual
     *
     * Obtem turno anterior e atual conforme quadro de horários informados
     *
     * @param array $quadroHorariosCliente Quadros de Horário do Cliente
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-01-11
     *
     * @return array("turnoAnterior", "turnoAtual")
     */
    public static function getTurnoAnteriorAtual(array $quadroHorariosCliente)
    {
        $horarios = array();
        $horaAtual = date("H");
        $quadroHorariosClienteLength = sizeof($quadroHorariosCliente);
        $tempoTurno = 24 / $quadroHorariosClienteLength;

        foreach ($quadroHorariosCliente as $horarioItem) {
            $horaItem = $horarioItem["horario"]->format("H");
            $fim = $horaItem + $tempoTurno;

            if ($fim >= 24) {
                $fim = $fim - 24;
            }

            $fim = strlen($fim) == 1 ? "0" . $fim : $fim;
            $fim = $fim . ":" . $horarioItem["horario"]->format("i:s");
            $diferencaHora = $horaAtual - $horaItem;
            $horaInicio = $horarioItem["horario"]->format("H:i:s");
            $horaFim = explode(":", $fim)[0];
            $item = array(
                "id" => $horarioItem["id"],
                "dataExibicao" => null,
                "dataConsultaInicio" => null,
                "dataConsultaFim" => null,
                "inicio" => $horarioItem["horario"]->format("H:i:s"),
                "horaInicio" => $horarioItem["horario"]->format("H"),
                "fim" => $fim,
                "horaFim" => $horaFim,
                "diferencaHora" => $diferencaHora
            );

            $horarios[] = $item;
        }

        usort($horarios, function ($a, $b) {
            return ($b["horaInicio"] > $a["horaInicio"]);
        });

        // Pega turno Atual

        $horaComparacao = $horaAtual;

        $processamentoConcluido = false;
        $dataHoje = new DateTime(date("Y-m-d H:i:s"));

        while (!$processamentoConcluido) {
            if ($horaComparacao < 0 && !$processamentoConcluido) {
                $horaComparacao = 23;
                $dataHoje = $dataHoje->modify("-1 day");
            }

            $registro = array_filter($horarios, function ($hora) use ($horaComparacao) {
                return intval($hora["horaInicio"]) == $horaComparacao;
            });

            $registro = array_values($registro);

            if (sizeof($registro) > 0) {
                $registro = $registro[0];
                $dataRegistro = new DateTime($dataHoje->format("Y-m-d"));
                $registro["dataConsultaInicio"] = $dataRegistro->format("Y-m-d") . " " . $registro["inicio"];
                $tempoComparacao = intval($registro["horaInicio"]) + $tempoTurno;

                if ($tempoComparacao >= 24) {
                    $tempoComparacao = $tempoComparacao - 24;
                }

                $registro["dataExibicao"] = $registro["inicio"] . " " . $dataHoje->format("d/m/Y");

                if ($registro["horaFim"] < $registro["horaInicio"]) {
                    $dataRegistro->modify("+1 day");
                }
                $registro["dataConsultaFim"] = $dataRegistro->format("Y-m-d") . " " . $registro["fim"];
            }

            if (!empty($registro)) {
                if (empty($turnoAtual)) {
                    $turnoAtual = $registro;
                } else {
                    $turnoAnterior = $registro;
                }
            }

            $horaComparacao -= 1;
            $processamentoConcluido = !empty($turnoAnterior) && !empty($turnoAtual);
        }

        return array(
            "turnoAnterior" => $turnoAnterior,
            "turnoAtual" => $turnoAtual
        );
    }

}
