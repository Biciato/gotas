<?php

/**
 * Arquivo para Classe para execução em terminal (shell)
 *
 * @category ClasseDeExecucaoBackground
 * @package  Shell
 * @author   "Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>"
 * @date     2019-02-05
 * @license  http://www.apache.org/licenses/LICENSE-2.0.txt Apache License 2.0
 * @link     https://www.rtibrindes.com.br/Shell/ClasseDeExecucaoBackground
 */

namespace App\Shell;

use App\Custom\RTI\DebugUtil;
use App\Controller\AppController;
use App\View\Helper;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Log\Log;

/**
 * Classe para execução em terminal (shell)
 *
 * @category ClasseDeExecucaoBackground
 * @package  Shell
 * @author   "Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>"
 * @since    2019-02-05
 * @license  http://www.apache.org/licenses/LICENSE-2.0.txt Apache License 2.0
 * @link     https://www.rtibrindes.com.br/Shell/ClasseDeExecucaoBackground
 */
class PontuacoesShell extends ExtendedShell
{
    #region Fields

    #endregion

    #region Methods

    /**
     * Método de inicialização
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
    }

    public function updatePontuacoes(Type $var = null)
    {
        $redes = $this->Redes->getRedesHabilitadas();

        foreach ($redes as $rede) {
            $mesesExpiracao = $rede["tempo_expiracao_gotas_usuarios"];

        }
        DebugUtil::print($redes);

        // SELECT TIMESTAMPDIFF(MONTH, DATE_SUB(CURDATE(), INTERVAL 6 MONTH), NOW());

//         SELECT
// 	p.id
// 	,p.clientes_id
// 	,p.usuarios_id
// 	,p.gotas_id
// 	,p.pontuacoes_comprovante_id
// 	,p.expirado
// 	,p.utilizado
// 	,p.data
// -- 	,date_format(p.data, "%m") as dataAquisicao
// -- 	,DATE_FORMAT(p.data, "%d") AS dataAquisicao
// 	,DATEDIFF( NOW(), DATE_FORMAT(p.data, "%Y-%m-%d")) AS dias

// FROM pontuacoes p
// WHERE p.clientes_has_brindes_habilitados_id IS NOT NULL
// AND DATEDIFF(NOW(), DATE_FORMAT(p.data, "%Y-%m-%d"))
// -- and date_format(p.data, "%d") > 20
// ;
    }

    #endregion
}
