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

    /**
     * PontucoesShell::updatePontuacoesExpiradas
     *
     * Método Shell para atualizar pontuações expiradas, conforme regra de cada rede do sistema
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-02-06
     *
     * @return void
     */
    public function updatePontuacoesExpiradas()
    {
        Log::write("info", sprintf("[Class: %s / Method: %s] %s: Atualização de Pontuações Expiradas às  %s.", __class__, __FUNCTION__, JOB_STATUS_INIT, date("d/m/Y H:i:s")));

        $redes = $this->Redes->getRedesHabilitadas();

        foreach ($redes as $rede) {
            $redesId = $rede["id"];
            $redesNome = $rede["nome_rede"];
            $clientesId = $rede["Clientes"]["id"];
            $clientesNomeFantasia = $rede["Clientes"]["nome_fantasia"];
            $mesesExpiracao = $rede["tempo_expiracao_gotas_usuarios"];

            Log::write("info", sprintf("Realizando procedimento para Rede (%s / %s), Posto: (%s / %s)...", $redesId, $redesNome, $clientesId, $clientesNomeFantasia));

            $result = $this->Pontuacoes->updatePontuacoesPendentesExpiracao($clientesId, $mesesExpiracao);

            Log::write("info", sprintf("Realizado procedimento para Rede (%s / %s), Posto: (%s / %s)...", $redesId, $redesNome, $clientesId, $clientesNomeFantasia));
        }

        Log::write("info", sprintf("[Class: %s / Method: %s] %s: Atualização de Pontuações Expiradas às  %s.", __class__, __FUNCTION__, JOB_STATUS_END, date("d/m/Y H:i:s")));
    }

    #endregion
}
