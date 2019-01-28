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

    public function updateBrindesEquipamentosRTI()
    {

    }

    #endregion
}
