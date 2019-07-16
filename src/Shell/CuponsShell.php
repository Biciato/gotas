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
        Log::write("info", sprintf("[Class: %s / Method: %s] %s: Atualização de Cupons de Equipamento RTI para USADO após 24 horas às %s.", __class__, __FUNCTION__, JOB_STATUS_INIT, date("d/m/Y H:i:s")));

        $cupons = $this->Cupons->getCuponsResgatadosUsados(true, false, null, true, 1);

        // DebugUtil::printArray($cupons);
        $cuponsIdsAtualizar = array();

        foreach ($cupons as $cupom) {
            $cuponsIdsAtualizar[] = $cupom["id"];
        }

        $rowCount = $this->Cupons->setCuponsResgatadosUsados($cuponsIdsAtualizar);

        if ($rowCount > 0) {
            Log::write("info", sprintf("[Class: %s / Method: %s] Número de Registros alterados: %s", __class__, __FUNCTION__, $rowCount));
        } else {
            Log::write("info", sprintf("[Class: %s / Method: %s] Não houve registros à serem atualizados!", __class__, __FUNCTION__));
        }

        Log::write("info", sprintf("[Class: %s / Method: %s] %s: Atualização de Cupons de Equipamento RTI para USADO após 24 horas às %s.", __class__, __FUNCTION__, JOB_STATUS_END, date("d/m/Y H:i:s")));
    }

    #endregion
}
