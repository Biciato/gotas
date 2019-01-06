<?php
namespace App\Shell;

use Cake\Console\Shell;
use ArrayObject;
use App\Custom\RTI\WebTools;
use App\Custom\RTI\SefazUtil;
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

class PontuacoesPendentesShell extends ExtendedShell
{
    // Fields
    protected $webTools = null;
    protected $sefazUtil = null;

    /**
     * Undocumented function
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
     * Realiza processamento de todas as pontuações
     * quando o site da sefaz estava offline
     *
     * @return void
     */
    public function startProcessPontuacoesPendentes()
    {
        $this->out("hello");
        Log::write('info', 'Iniciando processamento de cupons pendentes de processamento...');

        $pontuacoes_pendentes = $this->PontuacoesPendentes->findAllPontuacoesPendentesAwaitingProcessing();

        if (sizeof($pontuacoes_pendentes->toArray()) == 0) {
            Log::write('info', 'Não há processamento de cupons pendentes de processamento...');
        } else {
            $array_save =[];

            $html_content = null;

            foreach ($pontuacoes_pendentes as $key => $pontuacao_pendente) {
                // para cada pontuacao pendente, procura o cliente,
                // pega suas gotas (multiplicadores) e faz o tratamento

                Log::write('info', __("Iniciando execução sob cupom pendente [{0}] do estado de [{1}]", $pontuacao_pendente->chave_nfe, $pontuacao_pendente->estado_nfe));

                $cliente = $this->Clientes->getClienteById($pontuacao_pendente->clientes_id);
                $clientes_id = is_null($cliente->matriz_id)? $cliente->id : $cliente->matriz_id;

                /*
                * Como é automático, preciso verificar se a loja
                * possui gotas configuradas, se não tiver, preciso verificar a
                * matriz. Neste ponto ao menos a matriz deve ter a configuração
                */

                // obtem todos os multiplicadores (gotas)
                $gotas = $this->Gotas->findGotasByClientesId($cliente->id);

                $gotas = $gotas->toArray();

                if (sizeof($gotas) == 0 && !is_null($cliente->matriz_id)) {
                    $gotas = $this->Gotas->findGotasByClientesId($cliente->matriz_id);

                    $gotas = $gotas->toArray();
                }

                $html_content = $this->webTools->getPageContent($pontuacao_pendente->conteudo);

                // $html_content = $this->webTools->getPageContent("http://localhost:8080/gasolinacomum.1.html");

                $pontuacoes_comprovante['clientes_id'] = $cliente->id;
                $pontuacoes_comprovante['usuarios_id'] = $pontuacao_pendente->usuarios_id;
                $pontuacoes_comprovante['funcionarios_id'] = $pontuacao_pendente->funcionarios_id;
                $pontuacoes_comprovante['conteudo'] = $pontuacao_pendente->conteudo;
                $pontuacoes_comprovante['chave_nfe'] = $pontuacao_pendente->chave_nfe;
                $pontuacoes_comprovante['estado_nfe'] = $pontuacao_pendente->estado_nfe;
                $pontuacoes_comprovante['data'] = $pontuacao_pendente->data;
                $pontuacoes_comprovante['requer_auditoria'] = false;
                $pontuacoes_comprovante['auditado'] = false;

                $pontuacao['clientes_id'] = $cliente->id;
                $pontuacao['usuarios_id'] = $pontuacao_pendente->usuarios_id;
                $pontuacao['funcionarios_id'] = $pontuacao_pendente->funcionarios_id;

                $pontuacao['data'] = $pontuacao_pendente->data->format('Y-m-d H:i:s');

                $array_return
                    = $this->sefazUtil->convertHtmlToCouponData(
                        $html_content['response'],
                        $gotas,
                        $pontuacoes_comprovante,
                        $pontuacao,
                        $pontuacao_pendente
                    );

                foreach ($array_return as $key => $value) {
                    array_push($array_save, $value);
                }
            }
            Log::write('info', 'Iniciando gravação de informações adquiridas pela Sefaz para os cupons pendentes de processamento...');

            $process_failed = false;

            // Atualiza os registros se o site da sefaz retornou os dados

            if (!is_null($html_content) && $html_content['statusCode'] == 200) {
                foreach ($array_save as $key => $value) {
                    /*
                     * verifica se tem pontuações à gravar
                     * se não tem, somente configura o registro
                     * pendente como processado
                     */

                    $array_pontuacao = $value['array_pontuacoes_item'];

                    $pontuacao_comprovante_id = null;

                    if (sizeof($array_pontuacao)>0) {
                        // item novo, gera entidade e grava
                        $pontuacao_comprovante = $value['pontuacao_comprovante_item'];

                        $pontuacao_comprovante
                            = $this->PontuacoesComprovantes->addPontuacaoComprovanteCupom(
                                $pontuacao_comprovante['clientes_id'],
                                $pontuacao_comprovante['usuarios_id'],
                                $pontuacao_comprovante['funcionarios_id'],
                                $pontuacao_comprovante['conteudo'],
                                $pontuacao_comprovante['chave_nfe'],
                                $pontuacao_comprovante['estado_nfe'],
                                $pontuacao_comprovante['data'],
                                false,
                                false
                            );

                        // item novo. usa id de pontuacao_comprovante e grava os registros dependentes
                        if ($pontuacao_comprovante) {
                            $pontuacao_comprovante_id = $pontuacao_comprovante->id;

                            foreach ($array_pontuacao as $key => $item_pontuacao) {
                                $item_pontuacao = $this->Pontuacoes->addPontuacaoCupom(
                                    $item_pontuacao['clientes_id'],
                                    $item_pontuacao['usuarios_id'],
                                    $item_pontuacao['funcionarios_id'],
                                    $item_pontuacao['gotas_id'],
                                    $item_pontuacao['quantidade_multiplicador'],
                                    $item_pontuacao['quantidade_gotas'],
                                    $pontuacao_comprovante->id,
                                    $item_pontuacao['data']
                                );

                                if (!$item_pontuacao) {
                                        $process_failed=true;
                                }
                            }
                        }
                    } else {
                        Log::write('warning', __('No Cupom Fiscal {0} da SEFAZ de {1} não há gotas à processar conforme configurações definidas!...', $pontuacao_pendente->chave_nfe, $pontuacao_pendente->estado_nfe ));
                    }

                    // busca registro de pontuacao_pendente no bd e atualiza
                    $pontuacao_pendente = $value['pontuacao_pendente_item'];

                    if (!$process_failed) {
                        $this->PontuacoesPendentes->setPontuacaoPendenteProcessed($pontuacao_pendente['id'], $pontuacao_comprovante_id);
                    }
                }
                Log::write('info', 'Finalizado processamento de cupons pendentes de processamento...');
            } else {
                Log::write('warning', 'Site da SEFAZ indisponível no momento, aguardando próximo fluxo...');
            }
        }
    }
}
