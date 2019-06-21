<?php
/**
 * Arquivo para Classe para execução em terminal (shell)
 *
 * @category ClasseDeExecucaoBackground
 * @package  Shell
 * @author   "Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>"
 * @date     05/08/2017
 * @license  http://www.apache.org/licenses/LICENSE-2.0.txt Apache License 2.0
 * @link     https://www.rtibrindes.com.br/Shell/ClasseDeExecucaoBackground
 */

 namespace App\Shell;

use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\Event\Event;
use App\Custom\RTI\DebugUtil;

/**
 * Classe para execução em terminal (shell)
 *
 * @category ClasseDeExecucaoBackground
 * @package  Shell
 * @author   "Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>"
 * @date     05/08/2017
 * @license  http://www.apache.org/licenses/LICENSE-2.0.txt Apache License 2.0
 * @link     https://www.rtibrindes.com.br/Shell/ClasseDeExecucaoBackground
 */
class TestShell extends ExtendedShell
{
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
     * Gera relatório diário e envia para cada administrador de cada loja/matriz
     *
     * @return void
     */
    public function main()
    {
        try {
            $this->out("hello");
            Log::write('info', 'hello');
        } catch (\Exception $e) {
        }
    }

    public function testTimeShift(int $clientesId)
    {
        $quadroHorarios = $this->ClientesHasQuadroHorario->getHorariosCliente(null, $clientesId);

        DebugUtil::printArray($quadroHorarios);
    }

}
