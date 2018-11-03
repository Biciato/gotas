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
use App\Custom\RTI\CryptUtil;

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
class EncryptShell extends ExtendedShell
{

    protected $cryptUtil = null;

    /**
     * Método de inicialização
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        if (is_null($this->cryptUtil)) {
            $this->cryptUtil = new CryptUtil();
        }
    }

    /**
     * Gera relatório diário e envia para cada administrador de cada loja/matriz
     *
     * @return void
     */
    public function main()
    {
    }

    /**
     * Undocumented function
     *
     * @param string $word
     * @return void
     */
    public function encrypt(string $word){
        $this->out($this->cryptUtil->encrypt($word));
    }

    /**
     * Undocumented function
     *
     * @param string $word
     * @return void
     */
    public function decrypt(string $word){
        $this->out($this->cryptUtil->decrypt($word));
    }
}
