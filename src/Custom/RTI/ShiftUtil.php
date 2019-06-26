<?php

namespace App\Custom\RTI;

use \DateTime;
use App\Controller\AppController;
use Cake\Mailer\Files;
use Cake\Core\Configure;
use Cake\Log\Log;
use App\Model\Entity\ClientesHasQuadroHorario;

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
class ShiftUtil
{
    /**
     * TimeUtil::getTurnoAnteriorAtual
     *
     * Obtem turno anterior e atual conforme quadro de horários informados
     *
     * @param array $quadroHorariosCliente Quadros de Horário do Cliente
     * @param int $turno Turno Anterior ou Atual (Anterior = 0 / Atual = 1)
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-01-11
     *
     * @return array("turnoAnterior", "turnoAtual")
     */
    public static function getTurnoAnteriorAtual(array $quadroHorariosCliente, int $turno)
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

        return $turno == 1 ? $turnoAtual : $turnoAnterior;
    }

    /**
     * TimeUtil::obtemTurnoAtual
     *
     * Retorna o quadro de horário atual
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-06-21
     *
     * @param \App\Model\Entity\ClientesHasQuadroHorario[] $horarios
     *
     * @return array Turno Atual em Array
     */
    public static function obtemTurnoAtual(array $horarios)
    {
        if (empty($horarios) || count($horarios) == 0) {
            throw new \Exception("Quadro de Horários não configurado!");
        }

        $horasPesquisa = array();

        // obtem hora atual em segundos
        $horaAtualTotalSegundos = TimeUtil::transformaHoraSegundos(date("H"), date("i"), date("s"));

        // obtem todas as horas e calcula a diferença
        foreach ($horarios as $itemHorario) {
            $horaPesquisa = array();
            $horaPesquisa["id"] = $itemHorario->id;
            $horaPesquisa["horario"] = $itemHorario->horario;
            $horaComparacaoSegundos = TimeUtil::transformaHoraSegundos($horaPesquisa["horario"]->format("H"), $horaPesquisa["horario"]->format("i"), $horaPesquisa["horario"]->format("s"));
            $comparacao = $horaAtualTotalSegundos - $horaComparacaoSegundos;
            $horaPesquisa["diferenca"] = $comparacao;
            $horasPesquisa[] = $horaPesquisa;
        }

        // Reordena conforme diferença
        usort($horasPesquisa, function ($a, $b) {
            return $a["diferenca"] >= 0 && $a["diferenca"] >= $b["diferenca"];
        });

        if (count($horasPesquisa) == 0) {
            throw new \Exception("Erro durante cálculo de segundos dos turnos");
        }

        // Retorna o primeiro registro onde a diferença é maior que 0

        foreach ($horasPesquisa as $item) {
            if ($item["diferenca"] >= 0) {
                return $item;
            }
        }
    }

    /**
     * ShiftUtil::regridePeriodoTurnos
     *
     * Regride o horário de cada turno
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-06-25
     *
     * @param array $horarios Horarios
     * @param array $turnoAtual Turno Atual
     * @param \DateTime $dataInicio Data Inicio
     * @param \DateTime $dataFim Data Fim
     *
     * @return array $turnos Turnos de horários novos reposicionados
     */
    public static function regridePeriodoTurnos(array $horarios, array $turnoAtual, \DateTime $dataInicio, \DateTime $dataFim)
    {
        // DebugUtil::printArray($horarios, false);
        // Reposiciona os turnos se data de fim informada
        if (!empty($dataFim)) {
            $horaTurnoAtual = new DateTime($turnoAtual["horario"]->format("Y-m-d H:i:s"));

            $horaFimEncontrado = null;
            $qteHorarios = count($horarios);
            $ultimoTurnoEncontrado = new ClientesHasQuadroHorario();

            $diferencaTurnos = 24 / $qteHorarios;
            $ultimoTurnoHorarios = end($horarios);

            // Número de horas à reduzir
            $dataAdicionar = $diferencaTurnos * 3600;

            while ($horaFimEncontrado == null) {
                if ($horaTurnoAtual->getTimestamp() <= $dataFim->getTimestamp()) {
                    $horaFimEncontrado = $horaTurnoAtual;

                    $ultimoTurnoEncontrado->clientes_id = $ultimoTurnoHorarios->clientes_id;
                    $ultimoTurnoEncontrado->horario = $horaFimEncontrado;
                    $ultimoTurnoEncontrado->redes_id = $ultimoTurnoHorarios->redes_id;

                    break;
                }

                $horaTurnoAtual->modify(sprintf("-%s seconds", $dataAdicionar));
            }

            $turnos = array();
            $horaInicioEncontrado = null;
            $turnos[] = $ultimoTurnoEncontrado;
            $horaInicioPesquisa = new DateTime($horaFimEncontrado->format("Y-m-d H:i:s"));

            while (empty($horaInicioEncontrado)) {

                $horaInicioPesquisa->modify(sprintf("-%s seconds", $dataAdicionar));
                $hora = new DateTime($horaInicioPesquisa->format("Y-m-d H:i:s"));

                $turno = new ClientesHasQuadroHorario();
                $turno->clientes_id = $ultimoTurnoHorarios->clientes_id;
                $turno->horario = $hora;
                $turno->redes_id = $ultimoTurnoEncontrado->redes_id;

                $turnos[] = $turno;

                if ($horaInicioPesquisa->getTimestamp() <= $dataInicio->getTimestamp()) {
                    $horaInicioEncontrado = $horaInicioPesquisa;
                    break;
                }
            }

            usort($turnos, function ($a, $b) {
                return $a->horario >= $b->horario;
            });


            foreach ($horarios as $horarioItem) {
                foreach ($turnos as $turno) {
                    if ($horarioItem->horario->format("H:i:s") == $turno->horario->format("H:i:s")) {
                        $turno->id = $horarioItem->id;
                    }
                }
            }

            // DebugUtil::printArray($turnos);
            return $turnos;
        }
    }
}
