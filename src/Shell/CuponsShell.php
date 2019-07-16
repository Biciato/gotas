<?php

namespace App\Shell;

use Cake\Console\Shell;
use ArrayObject;
use App\Custom\RTI\DebugUtil;
use App\Custom\RTI\SefazUtil;
use App\Custom\RTI\WebTools;
use App\View\Helper;
use App\Controller\AppController;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use App\Model\Entity\CuponsTransacoes;
use App\Custom\RTI\ShiftUtil;
use Exception;
use DateTime;

/**
 * src\Shell\CuponsShell.php
 *
 * Shell para operações de Cupons
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 2019-01-27
 */
class CuponsShell extends ExtendedShell
{
    #region Fields

    #endregion

    #region Methods

    #region Constructor

    /**
     * PontuacoesPendentesShell::initialize
     *
     * Inicialize da classe
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2018-02-01
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
    }

    #endregion

    /**
     * CuponsShell::updateBrindesEquipamentosRTI
     *
     * Método de atualização de cupons para resgatados / usado, se Equip. RTI
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-01-30
     *
     * @return void
     */
    public function updateBrindesEquipamentosRTI()
    {
        try {
            Log::write("info", sprintf("[Class: %s / Method: %s] %s: Atualização de Cupons de Equipamento RTI para USADO após 24 horas às %s.", __CLASS__, __FUNCTION__, JOB_STATUS_INIT, date("d/m/Y H:i:s")));

            // Obtem lista de redes
            $redes = $this->Redes->getAllRedes(null, null, true);
            $funcionarioFicticio = $this->Usuarios->getFuncionarioFicticio();

            $rowCount = 0;
            foreach ($redes as $rede) {
                // Percorre lista de postos

                foreach ($rede->redes_has_clientes as $redesHasCliente) {
                    $redeId = $rede->id;
                    $cliente = $redesHasCliente->cliente;

                    Log::write("info", sprintf("[Class: %s / Method: %s] Atualizando cupons para posto {%s - %s}...", __CLASS__, __FUNCTION__, $cliente->id, $cliente->nome_fantasia));

                    // Obtem os turnos do posto
                    $postoTurnos = $this->ClientesHasQuadroHorario->getHorariosCliente(null, $cliente->id, null, null, 1);
                    $postoTurnos = $postoTurnos->toArray();

                    // Obtem turno atual
                    $turnoAtual = ShiftUtil::obtemTurnoAtual($postoTurnos);

                    // Trata os cupons
                    $cupons = $this->Cupons->getCuponsResgatadosUsados($cliente->id, true, false, null, true, 1);

                    $rowCountPosto = 0;

                    foreach ($cupons as $cupom) {
                        $success = $this->Cupons->setCupomUsado($cupom->id);

                        $cupomTransacao = new CuponsTransacoes();
                        $cupomTransacao->redes_id = $redeId;
                        $cupomTransacao->clientes_id = $cliente->id;
                        $cupomTransacao->cupons_id = $cupom->id;
                        $cupomTransacao->brindes_id = $cupom->brindes_id;
                        $cupomTransacao->clientes_has_quadro_horario_id = $turnoAtual["id"];
                        $cupomTransacao->funcionarios_id = $funcionarioFicticio->id;
                        $cupomTransacao->tipo_operacao = TYPE_OPERATION_USE;
                        $cupomTransacao->data = new DateTime('now');
                        $success = $this->CuponsTransacoes->saveUpdate($cupomTransacao);
                        $rowCountPosto += $success ? 1 : 0;
                    }

                    $rowCount += $rowCountPosto;

                    if ($rowCountPosto > 0) {
                        Log::write("info", sprintf("[Class: %s / Method: %s] Total atualizações cupons para posto {%s - %s} : %s ...", __CLASS__, __FUNCTION__, $cliente->id, $cliente->nome_fantasia, $rowCountPosto));
                    } else {
                        Log::write("info", sprintf("[Class: %s / Method: %s] Posto {%s - %s} não teve transação de cupons para definir como %s ...", __CLASS__, __FUNCTION__, $cliente->id, $cliente->nome_fantasia, strtoupper(TYPE_OPERATION_USED)));
                    }

                    Log::write("info", sprintf("[Class: %s / Method: %s] Fim atualizando cupons para posto {%s - %s}...", __CLASS__, __FUNCTION__, $cliente->id, $cliente->nome_fantasia));
                }
            }

            if ($rowCount > 0) {
                Log::write("info", sprintf("[Class: %s / Method: %s] Total de Número de Registros alterados: %s", __class__, __FUNCTION__, $rowCount));
            } else {
                Log::write("info", sprintf("[Class: %s / Method: %s] Não houve registros à serem atualizados!", __class__, __FUNCTION__));
            }

            Log::write("info", sprintf("[Class: %s / Method: %s] %s: Atualização de Cupons de Equipamento RTI para USADO após 24 horas às %s.", __class__, __FUNCTION__, JOB_STATUS_END, date("d/m/Y H:i:s")));
        } catch (Exception $e) {
            $message = sprintf("[%s] %s", MESSAGE_GENERIC_EXCEPTION, $e->getMessage());
            Log::write("error", $message);
            throw new Exception($message);
        }
    }

    #endregion
}
