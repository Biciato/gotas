<?php

namespace App\Shell;

use Cake\Console\Shell;
use ArrayObject;
use App\Custom\RTI\DebugUtil;
use App\Custom\RTI\SefazUtil;
use App\Custom\RTI\WebTools;
use App\View\Helper;
use App\Controller\AppController;
use App\Custom\RTI\ResponseUtil;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

class PontuacoesPendentesShell extends ExtendedShell
{
    // Fields
    protected $webTools = null;
    protected $sefazUtil = null;

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

        if (is_null($this->webTools)) {
            $this->webTools = new WebTools();
        }

        if (is_null($this->sefazUtil)) {
            $this->sefazUtil = new SefazUtil();
        }
    }

    /**
     * PontuacoesPendentesShell::startProcessPontuacoesPendentes
     *
     * Realiza processamento de todas as pontuações quando o site da sefaz estava offline
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-01-27
     *
     * @return void
     */
    public function startProcessPontuacoesPendentes()
    {
        try {
            Log::write('info', 'Iniciando processamento de cupons pendentes de processamento...');

            $pontuacoesPendentes = $this->PontuacoesPendentes->findAllPontuacoesPendentesAwaitingProcessing();
            $pontuacoesPendentes = $pontuacoesPendentes->toArray();

            $auth = WebTools::loginAPIGotas(WORKER_EMAIL, WORKER_PASSWORD);

            // Log::write('info', 'Testando auth...');
            // DebugUtil::printArray($auth);

            if (sizeof($pontuacoesPendentes) == 0) {
                Log::write('info', 'Não há processamento de cupons pendentes de processamento...');
                return;
            }

            $apiUrl = __SERVER__. "api/pontuacoes_comprovantes/set_comprovante_fiscal_usuario";

            foreach ($pontuacoesPendentes as $key => $pontuacaoPendente) {
                // para cada pontuacao pendente, pega a chave, faz a solicitação e trata como se fosse o fluxo normal
                Log::write('info', __("Iniciando execução sob cupom pendente [{0}] do estado de [{1}]", $pontuacaoPendente->conteudo, $pontuacaoPendente->estado_nfe));

                $data = array(
                    "qr_code" => $pontuacaoPendente["conteudo"],
                    "processamento_pendente" => true,
                    "usuarios_id" => $pontuacaoPendente->usuarios_id,
                    "clientes_id" => $pontuacaoPendente->clientes_id,
                    "funcionarios_id" => $pontuacaoPendente->funcionarios_id
                );

                // return ResponseUtil::successAPI(MSG_LOAD_DATA_WITH_SUCCESS, $data);

                $result = WebTools::callAPI("POST", $apiUrl, $data, DATA_TYPE_MESSAGE_JSON, $auth["token"]);

                // DebugUtil::print($result);
                Log::write('info', __("Finalizado execução sob cupom pendente [{0}] do estado de [{1}]", $pontuacaoPendente->conteudo, $pontuacaoPendente->estado_nfe));
            }
            Log::write('info', 'Finalizado processamento de cupons pendentes de processamento...');

            return;
        } catch (\Throwable $th) {
            $message = $th->getMessage();
            $code =  $th->getCode();

            Log::write("error", sprintf("[Code: %s] %s - Message: %s", $code, MESSAGE_GENERIC_EXCEPTION, $message));
        }
    }
}
