<?php

namespace App\Controller;

use App\Controller\AppController;
use App\Model\Entity;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Event\Event;
use App\Custom\RTI\Security;
use \DateTime;
use App\Custom\RTI\DebugUtil;
use App\Custom\RTI\ResponseUtil;
use App\Model\Entity\Cliente;
use Cake\Http\Client\Request;
use Cake\I18n\Number;
use Exception;

/**
 * Pontuacoes Controller
 *
 * @property \App\Model\Table\PontuacoesTable $Pontuacoes
 *
 * @method \App\Model\Entity\Pontuaco[] paginate($object = null, array $settings = [])
 */
class PontuacoesController extends AppController
{

    /**
     * ------------------------------------------------------------
     * Campos
     * ------------------------------------------------------------
     */
    protected $usuarioLogado = null;

    /**
     * ------------------------------------------------------------
     * Métodos Comuns
     * ------------------------------------------------------------
     */

    /**
     * Before render callback.
     *
     * @param \App\Controller\Event\Event $event The beforeRender event.
     * @return \Cake\Network\Response|null|void
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
    }

    /**
     * Initialize function
     */
    public function initialize()
    {
        parent::initialize();
    }

    /**
     * ------------------------------------------------------------
     * CRUD Methods
     * ------------------------------------------------------------
     */

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Usuarios', 'BrindesHabilitados', 'Gotas']
        ];
        $pontuacoes = $this->paginate($this->Pontuacoes);

        $this->set(compact('pontuacoes'));
        $this->set('_serialize', ['pontuacoes']);
    }

    /**
     * View method
     *
     * @param string|null $id Pontuaco id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $pontuacao = $this->Pontuacoes->get(
            $id,
            [
                'contain' => ['Usuarios', 'BrindesHabilitados', 'Gotas']
            ]
        );

        $this->set('pontuacao', $pontuacao);
        $this->set('_serialize', ['pontuacao']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $pontuacao = $this->Pontuacoes->newEntity();
        if ($this->request->is('post')) {
            $pontuacao = $this->Pontuacoes->patchEntity($pontuacao, $this->request->getData());
            if ($this->Pontuacoes->save($pontuacao)) {
                $this->Flash->success(__('The pontuacao has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The pontuacao could not be saved. Please, try again.'));
        }
        $usuarios = $this->Pontuacoes->Usuarios->find('list', ['limit' => 200]);
        $brindesHabilitados = $this->Pontuacoes->BrindesHabilitados->find('list', ['limit' => 200]);
        $gotas = $this->Pontuacoes->Gotas->find('list', ['limit' => 200]);
        $this->set(compact('pontuacao', 'usuarios', 'brindesHabilitados', 'gotas'));
        $this->set('_serialize', ['pontuacao']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Pontuaco id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $pontuacao = $this->Pontuacoes->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $pontuacao = $this->Pontuacoes->patchEntity($pontuacao, $this->request->getData());
            if ($this->Pontuacoes->save($pontuacao)) {
                $this->Flash->success(__('The pontuacao has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The pontuacao could not be saved. Please, try again.'));
        }
        $usuarios = $this->Pontuacoes->Usuarios->find('list', ['limit' => 200]);
        $brindesHabilitados = $this->Pontuacoes->BrindesHabilitados->find('list', ['limit' => 200]);
        $gotas = $this->Pontuacoes->Gotas->find('list', ['limit' => 200]);
        $this->set(compact('pontuacao', 'usuarios', 'brindesHabilitados', 'gotas'));
        $this->set('_serialize', ['pontuacao']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Pontuaco id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $pontuacao = $this->Pontuacoes->get($id);
        if ($this->Pontuacoes->delete($pontuacao)) {
            $this->Flash->success(__('The pontuacao has been deleted.'));
        } else {
            $this->Flash->error(__('The pontuacao could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Altera a pontuação atribuída ao usuário consumidor
     *
     * @param int $pontuacao_id Id de pontuacao
     *
     * @return void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function alterarClientePontuacao(int $pontuacao_id)
    {
        // ------------------------------------------------------------------------------------------
        // Atenção sobre esta função!
        // não pode ter restrict query na view!
        // motivo: pode ter sido a primeira gota do cliente e um funcionário
        // pode atribuir estas gotas incorretamente, então o gestor não conseguirá fazer o ajuste
        // ------------------------------------------------------------------------------------------

        // se o usuário que estiver logado for
        try {
            $pontuacao = $this->PontuacoesComprovantes->getCouponById($pontuacao_id);

            $cliente = $this->Clientes->getClienteById($pontuacao->cliente->id);

            if ($this->request->is('post')) {
                $data = $this->request->getData();

                $pontuacoes_comprovantes_array_update = ['usuarios_id' => $data['usuarios_id']];

                $usuario = $this->Usuarios->getUsuarioById($data['usuarios_id']);

                $result_update_pontuacao = $this->Pontuacoes->updateAllPontuacoesByComprovantesId($pontuacao->id, $pontuacoes_comprovantes_array_update);

                if ($result_update_pontuacao) {
                    $pontuacoes_array_update = ['usuarios_id' => $data['usuarios_id']];

                    $result_update_pontuacoes_comprovantes = $this->PontuacoesComprovantes->setUsuarioForPontuacaoComprovanteById($pontuacao->id, $usuario->id);

                    // Exibe mensagem de sucesso e redireciona para tela de aprovação
                    if ($result_update_pontuacao && $result_update_pontuacoes_comprovantes) {
                        $this->Flash->success(Configure::read('messageSavedSuccess'));

                        return $this->redirect(['action' => 'detalhes_cupom', $pontuacao->id]);
                    }
                }
                // Se chegou até aqui, deu algo errado.

                $this->Flash->error(Configure::read('messageSavedError'));
            }

            $this->set(compact(['pontuacao', 'cliente']));
            $this->set('_serialize', ['pontuacao', 'cliente']);
        } catch (\Exception $e) {
            $stringError = __("Erro ao exibir página: {0} em: {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Exibe os cupons fiscais da rede / loja
     *
     * @return void
     */
    public function cuponsMinhaRede()
    {
        // se o usuário que estiver logado for
        try {
            $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
            $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

            if ($usuarioAdministrador) {
                $this->usuarioLogado = $usuarioAdministrar;
                $usuarioLogado = $usuarioAdministrar;
            }

            $rede = $this->request->session()->read('Rede.Grupo');

            // Pega unidades que tem acesso

            $unidadesIds = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($rede->id, $this->usuarioLogado['id']);

            $clientesIds = array();

            foreach ($unidadesIds as $key => $value) {
                $clientesIds[] = $key;
            }

            // verifica se usuário é ao menos gerente
            $this->securityUtil->checkUserIsAuthorized($this->usuarioLogado, 'ManagerProfileType');

            $pontuacoes = null;

            $arrayOptions = [];

            if (!$this->request->is(['post'])) {
                // Se não tiver filtrado, consultará a última semana

                $date = date('Y-m-d');
                $end = \strtotime($date);
                $start = \strtotime($date . ' -7 days');
                $arrayOptions[] = array('data between "' . date('Y-m-d 00:00:00', $start) . '" and "' . date('Y-m-d 23:59:59', $end) . '"');

                $pontuacoes = $this->PontuacoesComprovantes->getCouponsByClienteId($clientesIds, $arrayOptions);
            } else {
                $data = $this->request->getData();

                if ($data['filtrar_unidade'] != "") {
                    $clientesIds = [];
                    $clientesIds[] = (int) $data['filtrar_unidade'];
                }

                if (strlen($data['funcionarios_id']) > 0) {
                    $arrayOptions[] = array('funcionarios_id' => (int) $data['funcionarios_id']);
                }

                if (strlen($data['requer_auditoria']) > 0) {
                    $arrayOptions[] = array('requer_auditoria' => $data['requer_auditoria']);
                }

                if (strlen($data['auditado']) > 0) {
                    $arrayOptions[] = array('auditado' => $data['auditado']);
                }

                if (strlen($data['registro_invalido']) > 0) {
                    $arrayOptions[] = array('registro_invalido' => $data['registro_invalido']);
                }

                if (strlen($data['data_inicio']) > 0) {
                    $start = $data['data_inicio'] . ' 00:00:00';

                    $start = $this->datetime_util->convertDateTimeToUTC($start);
                }

                if (strlen($data['data_fim']) > 0) {
                    $end = $data['data_fim'] . ' 23:59:59';

                    $end = $this->datetime_util->convertDateTimeToUTC($end);
                }

                $arrayOptions[] = array('data between "' . $start . '" and "' . $end . '"');

                $pontuacoes = $this->PontuacoesComprovantes->getCouponsByClienteId($clientesIds, $arrayOptions);
            }

            // TODO: Ajustar
            $funcionariosQuery = $this->Usuarios->findFuncionariosRede($rede->id, $clientesIds, null, null, null, PROFILE_TYPE_WORKER)->select(['id', 'nome']);
            $funcionarios = array();

            foreach ($funcionariosQuery as $key => $value) {
                $funcionarios[] = array('value' => $value["id"], 'text' => $value["nome"]);
            }

            // debug($funcionarios);
            $pontuacoes = $this->Paginate($pontuacoes, ['limit' => 10]);

            $pontuacoes_new_array = [];

            foreach ($pontuacoes as $key => $value) {
                $value['soma_pontuacoes'] = $this->Pontuacoes->getSumPontuacoesByComprovanteId($value['id']);

                array_push($pontuacoes_new_array, $value);
            }

            $pontuacoes = null;
            $pontuacoes = $pontuacoes_new_array;

            $arraySet = array('pontuacoes', 'funcionarios', 'cliente', 'unidadesIds');
            $this->set(compact($arraySet));
            $this->set('_serialize', $arraySet);
        } catch (\Exception $e) {
            $stringError = __("Erro ao exibir página: {0} em: {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Exibe detalhes do cupom fiscal
     *
     * @param int $id Identificador do cupom fiscal
     *
     * @return void
     */
    public function detalhesCupom(int $id)
    {
        try {
            $pontuacao = $this->PontuacoesComprovantes->getDetalhesCupomByCouponId($id);

            if (!is_null($pontuacao->nome_img)) {
                $pontuacao->nome_img = Configure::read('documentReceiptPathRead') . $pontuacao->nome_img;
            }

            // DebugUtil::print($pontuacao);
            $this->set(compact('pontuacao'));

            $this->set('_serialize', ['pontuacao']);
        } catch (\Exception $e) {
            $stringError = __("Erro ao exibir página: {0} em: {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Edita uma pontuação
     *
     * @param int $pontuacao_id Id da pontuação
     *
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function editarPontuacao(int $pontuacaoId)
    {
        try {
            $pontuacao = $this->Pontuacoes->getPontuacaoById($pontuacaoId);


            $pontuacao["quantidade_multiplicador"] = Number::precision($pontuacao["quantidade_multiplicador"], 3);

            // Trata conversão de valores para a interface com javascript
            $quantidadeMultiplicador = $pontuacao["quantidade_multiplicador"];
            $quantidadeMultiplicadorArray = explode(",", $quantidadeMultiplicador);
            $inteiro = str_replace(".", "", $quantidadeMultiplicadorArray[0]);
            $fracao = $quantidadeMultiplicadorArray[1];
            $fracao = str_pad($fracao, 3, 0, STR_PAD_RIGHT);
            $quantidadeMultiplicador = sprintf("%s.%s", $inteiro, $fracao);
            $pontuacao["quantidade_multiplicador"] = $quantidadeMultiplicador;


            // DebugUtil::print($pontuacao);
            // DebugUtil::print($pontuacao);
            if ($this->request->is(['post', 'put'])) {
                $data = $this->request->getData();

                $quantidadeMultiplicador = $data['quantidade_multiplicador'];

                $gota = $this->Gotas->getGotasById($pontuacao["gotas_id"]);
                $parametroGota = $gota["multiplicador_gota"];
                $quantidadeGotas = $parametroGota * $quantidadeMultiplicador;
                $result = $this->Pontuacoes->updateQuantidadeGotasByPontuacaoId($pontuacao["id"], $quantidadeGotas, $quantidadeMultiplicador);

                if ($result) {
                    $this->Flash->success(Configure::read('messageSavedSuccess'));

                    return $this->redirect(array('action' => 'detalhes_cupom', $pontuacao->pontuacoes_comprovante_id));
                }

                $this->Flash->error(Configure::read('messageSavedError'));
            }

            $this->set(compact('pontuacao'));

            $this->set('_serialize', ['pontuacao']);
        } catch (\Exception $e) {
            $stringError = __("Erro ao exibir página: {0} em: {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Remove uma pontuacao de um comprovante
     *
     * @param int|null $pontuacao_id Pontuação id.
     *
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function removerPontuacao(int $pontuacao_id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $pontuacao = $this->Pontuacoes->get($pontuacao_id);
        if ($this->Pontuacoes->delete($pontuacao)) {
            $this->Flash->success(__(Configure::read('messageDeleteSuccess')));
        } else {
            $this->Flash->error(__(Configure::read('messageEnableError')));
        }

        return $this->redirect(
            [
                'action' => 'detalhes_cupom',
                (int) $pontuacao->pontuacoes_comprovante_id
            ]
        );
    }

    /**
     * ------------------------------------------------------------
     * Métodos para Funcionários (Dashboard de Funcionário)
     * ------------------------------------------------------------
     */

    /**
     * Exibe detalhes do cupom fiscal
     *
     * @param int $id Identificador do cupom fiscal
     *
     * @return void
     */
    public function detalhesCupomClienteFinal(int $id)
    {
        try {

            $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
            $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

            if ($usuarioAdministrador) {
                $this->usuarioLogado = $usuarioAdministrar;
            }

            $usuarioLogado = $this->usuarioLogado;

            $pontuacao = $this->PontuacoesComprovantes->getDetalhesCupomByCouponId($id);

            $usuarios_id = $pontuacao->usuarios_id;

            $array_set = [
                'pontuacao',
                'usuarios_id',
                'usuarioLogado'
            ];

            $this->set(compact($array_set));
            $this->set("_serialize", $array_set);
        } catch (\Exception $e) {
            $stringError = __("Erro ao exibir página: {0} em: {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * PontuacoesController.php::relatorioEntradaSaida
     *
     * View para Relatório de Entrada e Saída de Pontuações
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-09-11
     *
     * @return void
     */
    public function relatorioEntradaSaida()
    {
        $sessaoUsuario = $this->getSessionUserVariables();

        $usuarioLogado = $sessaoUsuario["usuarioLogado"];
        $usuarioAdministrador = $sessaoUsuario["usuarioAdministrador"];
        $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"];
        $rede = $sessaoUsuario["rede"];
        $cliente = $sessaoUsuario["cliente"];

        if ($usuarioAdministrar) {
            $usuarioLogado = $usuarioAdministrar;
        }

        $clientesId = $usuarioLogado->tipo_perfil >= PROFILE_TYPE_ADMIN_LOCAL ? $cliente->id : null;

        $arraySet = ["clientesId"];
        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);
    }

    /**
     * PontuacoesController.php::relatorioGotas
     *
     * View para Relatório de Entrada e Saída de Pontuações
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-09-17
     *
     * @return void
     */
    public function relatorioGotas()
    {
        $sessaoUsuario = $this->getSessionUserVariables();

        $usuarioLogado = $sessaoUsuario["usuarioLogado"];
        $usuarioAdministrador = $sessaoUsuario["usuarioAdministrador"];
        $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"];
        $rede = $sessaoUsuario["rede"];
        $cliente = $sessaoUsuario["cliente"];

        if ($usuarioAdministrar) {
            $usuarioLogado = $usuarioAdministrar;
        }

        $clientesId = $usuarioLogado->tipo_perfil >= PROFILE_TYPE_ADMIN_LOCAL || !empty($cliente) ? $cliente->id : null;

        $arraySet = ["clientesId"];
        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);
    }


    /**
     * ------------------------------------------------------------
     * Serviços REST
     * ------------------------------------------------------------
     */

    /**
     * PontuacoesController::getPontuacoesRedeAPI
     *
     * @return void
     */
    public function getPontuacoesRedeAPI()
    {
        $mensagem = array();

        $status = false;
        $message = null;
        try {

            $usuario = $this->Auth->user();

            $sessaoUsuario = $this->getSessionUserVariables();
            $usuario = $sessaoUsuario["usuarioLogado"];

            if ($this->request->is("post")) {
                $data = $this->request->getData();

                Log::write("info", sprintf("Info de Post: %s - %s.", __CLASS__, __METHOD__));
                Log::write("info", $data);

                $redesId = $data["redes_id"];
                // $redesId = 2;

                if (!isset($data["redes_id"])) {
                    $message = __("É necessário informar uma rede para obter os pontos!");
                    $status = false;

                    $mensagem = array("status" => $status, "message" => $message);

                    $arraySet = [
                        "mensagem"
                    ];

                    $this->set(compact($arraySet));
                    $this->set("_serialize", $arraySet);

                    return;
                }

                // Obtem os ids de clientes da rede selecionada

                $redeHasClientesQuery = $this->RedesHasClientes->getAllRedesHasClientesIdsByRedesId($redesId);

                $clientesIds = array();

                foreach ($redeHasClientesQuery as $key => $redeHasCliente) {
                    $clientesIds[] = $redeHasCliente->clientes_id;
                }

                if (sizeof($clientesIds) == 0) {
                    $message = __("A rede informada não possui unidades cadastradas!");
                    $status = false;

                    $mensagem = array("status" => $status, "message" => $message);

                    $arraySet = [
                        "mensagem"
                    ];

                    $this->set(compact($arraySet));
                    $this->set("_serialize", $arraySet);

                    return;
                }

                $retorno = $this->Pontuacoes->getSumPontuacoesOfUsuario($usuario["id"], $redesId, $clientesIds);


                $mensagem = $retorno["mensagem"];
                $resumo_gotas = $retorno["resumo_gotas"];
                $arraySet = array(
                    "mensagem",
                    "resumo_gotas"
                );

                $this->set(compact($arraySet));
                $this->set("_serialize", $arraySet);

                return;
            }
            $mensagem = array("status" => 1, "message" => Configure::read("messageLoadDataWithSuccess"));
        } catch (\Exception $e) {
            $messageString = __("Não foi possível obter pontuações do usuário na rede!");
            $trace = $e->getTrace();
            $mensagem = array('status' => false, 'message' => $messageString, 'errors' => $trace);
            $messageStringDebug = __("{0} - {1} em: {2}. [Função: {3} / Arquivo: {4} / Linha: {5}]  ", $messageString, $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write("error", $messageStringDebug);
        }

        $arraySet = array(
            "mensagem",
            "resumo_gotas"
        );

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);

        return;
    }

    /**
     * PontuacoesController::getExtratoPontuacoesAPI
     *
     * Obtem extrato de Pontos de Usuário, e detalha se é brinde ou gota
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 24/07/2018
     *
     * @return json_object $array
     */
    public function getExtratoPontuacoesAPI()
    {
        $mensagem = array();

        $status = false;
        $message = null;
        try {
            $usuario = $this->Auth->user();

            if ($this->request->is("post")) {
                $data = $this->request->getData();

                Log::write("info", sprintf("Info de Post: %s - %s.", __CLASS__, __METHOD__));
                Log::write("info", $data);

                // Condições de Pesquisa
                $redesId = !empty($data["redes_id"]) ? $data["redes_id"] : null;
                $clientesId = !empty($data["clientes_id"]) ? $data["clientes_id"] : null;
                $tipoOperacao = isset($data["tipo_operacao"]) ? $data["tipo_operacao"] : 2;
                $brindesNome = isset($data["nome_pesquisa"]) ? $data["nome_pesquisa"] : "";
                $gotasNomeParametro = isset($data["nome_pesquisa"]) ? $data["nome_pesquisa"] : "";

                $dataInicio = isset($data["data_inicio"]) ? $data["data_inicio"] : null;
                $dataFim = isset($data["data_fim"]) ? $data["data_fim"] : null;

                $orderConditions = array();
                $paginationConditions = array();

                if (isset($data["order_by"])) {
                    $orderConditions = $data["order_by"];
                }

                if (isset($data["pagination"])) {
                    $paginationConditions = $data["pagination"];

                    if ($paginationConditions["page"] < 1) {
                        $paginationConditions["page"] = 1;
                    }
                }

                if (!isset($data["redes_id"]) && !isset($clientesId)) {
                    $mensagem = array(
                        "status" => 0,
                        "message" => Configure::read("messageOperationFailureDuringProcessing"),
                        "errors" => array("É necessário informar uma Rede para obter o extrato!")
                    );

                    $arraySet = [
                        "mensagem"
                    ];

                    $this->set(compact($arraySet));
                    $this->set("_serialize", $arraySet);

                    return;
                }

                if (empty($clientesId) && empty($redesId)) {

                    $mensagem = array(
                        "status" => 0,
                        "message" => Configure::read("messageOperationFailureDuringProcessing"),
                        "errors" => array("É necessário informar um Ponto de Atendimento para obter o extrato!")
                    );

                    $arraySet = [
                        "mensagem"
                    ];

                    $this->set(compact($arraySet));
                    $this->set("_serialize", $arraySet);

                    return;
                }

                $clientesIds = !empty($clientesId) ? [$clientesId] : [];

                $retorno = $this->Pontuacoes->getExtratoPontuacoesOfUsuario(
                    $usuario["id"],
                    $redesId,
                    $clientesIds,
                    $tipoOperacao,
                    $brindesNome,
                    $gotasNomeParametro,
                    $dataInicio,
                    $dataFim,
                    $orderConditions,
                    $paginationConditions
                );

                $mensagem = $retorno["mensagem"];
                $pontuacoes = $retorno["pontuacoes"];
                $arraySet = array(
                    "mensagem",
                    "pontuacoes"
                );

                $this->set(compact($arraySet));
                $this->set("_serialize", $arraySet);

                return;
            }
            $mensagem = array("status" => 1, "message" => Configure::read("messageLoadDataWithSuccess"));
        } catch (\Exception $e) {
            $messageString = __("Não foi possível obter pontuações do usuário na rede!");
            $trace = $e->getTrace();
            $mensagem = array('status' => false, 'message' => $messageString, 'errors' => $trace);
            $messageStringDebug = __("{0} - {1} em: {2}. [Função: {3} / Arquivo: {4} / Linha: {5}]  ", $messageString, $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write("error", $messageStringDebug);
        }

        $arraySet = array(
            "mensagem",
            "resumo_gotas"
        );

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);

        return;
    }

    public function getPontuacoesRelatorioEntradaSaidaAPI()
    {
        $errors = [];
        $errorCodes = [];

        try {
            $sessaoUsuario = $this->getSessionUserVariables();
            $usuarioLogado = $sessaoUsuario["usuarioLogado"];
            $usuarioAdministrador = $sessaoUsuario["usuarioAdministrador"];
            $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"];
            $rede = $sessaoUsuario["rede"];
            $cliente = $sessaoUsuario["cliente"];
            $clientesIdSession = !empty($cliente) ? $cliente->id : null;
            $clientesIds = [];

            if (!empty($usuarioAdministrar)) {
                $usuarioLogado = $usuarioAdministrar;
            }

            if ($this->request->is(Request::METHOD_GET)) {
                // Obtenção dos dados
                $data = $this->request->getQueryParams();
                $clientesId = !empty($data["clientes_id"]) ? $data["clientes_id"] : null;
                $brindesId =  !empty($data["brindes_id"]) ? $data["brindes_id"] : null;
                $dataInicio =  !empty($data["data_inicio"]) ? $data["data_inicio"] : null;
                $dataFim =  !empty($data["data_fim"]) ? $data["data_fim"] : null;
                $tipoRelatorio = !empty($data["tipo_relatorio"]) ? $data["tipo_relatorio"] : null;
                $clientesIds = [];

                if (empty($dataInicio)) {
                    $errors[] = MSG_DATE_BEGIN_EMPTY;
                    $errorCodes[] = MSG_DATE_BEGIN_EMPTY_CODE;
                }

                if (empty($dataFim)) {
                    $errors[] = MSG_DATE_END_EMPTY;
                    $errorCodes[] = MSG_DATE_END_EMPTY_CODE;
                }

                if (empty($tipoRelatorio)) {
                    $errors[] = MSG_REPORT_TYPE_EMPTY;
                    $errorCodes[] = MSG_REPORT_TYPE_EMPTY_CODE;
                }

                $dataInicio = new DateTime(sprintf("%s 00:00:00", $dataInicio));
                $dataFim = new DateTime(sprintf("%s 23:59:59", $dataFim));

                $dataDiferenca = $dataFim->diff($dataInicio);

                // Periodo limite de filtro é 1 ano
                if ($dataDiferenca->y >= 1) {
                    $errors[] = MSG_MAX_FILTER_TIME_ONE_YEAR;
                    $errorCodes[] = MSG_MAX_FILTER_TIME_ONE_YEAR_CODE;
                }

                if ($dataInicio > $dataFim) {
                    $errors[] = MSG_DATE_BEGIN_GREATER_THAN_DATE_END;
                    $errorCodes[] = MSG_DATE_BEGIN_GREATER_THAN_DATE_END_CODE;
                }

                if (count($errors) > 0) {
                    throw new Exception(MSG_LOAD_EXCEPTION, MSG_LOAD_EXCEPTION_CODE);
                }


                // Se usuário logado for no mínimo administrador local, ele não pode selecionar uma unidade de rede
                if (empty($clientesId) && $usuarioLogado->tipo_perfil >= PROFILE_TYPE_ADMIN_LOCAL) {
                    $clientesId = $clientesIdSession;
                    $clientesIds[] = $clientesId;
                } elseif (empty($clientesId) && $usuarioLogado->tipo_perfil < PROFILE_TYPE_ADMIN_LOCAL) {
                    // Caso o operador não especifique a loja, pega as lojas que ele tem acesso e faz pesquisa
                    if ($usuarioLogado->tipo_perfil == PROFILE_TYPE_ADMIN_NETWORK) {
                        $clientesIds = $this->RedesHasClientes->getClientesIdsFromRedesHasClientes($rede->id);
                    }
                } else {
                    $clientesIds[] = $clientesId;
                }

                $clientes = [];

                foreach ($clientesIds as $cliente) {
                    $cliente = $this->Clientes->get($cliente);

                    $clientes[] = new Cliente([
                        "id" => $cliente->id,
                        "nome_fantasia" => $cliente->nome_fantasia,
                        "razao_social" => $cliente->razao_social
                    ]);
                }

                // As gotas que entram/saem do sistema ficam na tabela pontuacoes
                // Existem dois tipos de relatorio: ANALITICO E SINTETICO

                // SINTETICO traz apenas a soma, o periodo, e a unidade
                // analítico traz a soma, periodo, o posto, o usuário, a gota, e o cupom + url

                $data = [];
                $totalEntradas = 0;
                $totalSaidas = 0;

                foreach ($clientes as $cliente) {
                    $entradas = $this->Pontuacoes->getPontuacoesInOutForClientes($cliente->id, $brindesId, $dataInicio, $dataFim, PONTUACOES_TYPE_OPERATION_IN, $tipoRelatorio);
                    $saidas = $this->Pontuacoes->getPontuacoesInOutForClientes($cliente->id, $brindesId, $dataInicio, $dataFim, PONTUACOES_TYPE_OPERATION_OUT, $tipoRelatorio);

                    $entradas = $entradas->toArray();
                    $saidas = $saidas->toArray();
                    $somaEntradas = 0;
                    $somaSaidas = 0;

                    /**
                     * Processo de verificação que analiza se os dois conjuntos de registro estão com os
                     * mesmos periodos informados
                     */
                    foreach ($entradas as $entrada) {
                        // verifica se o periodo de entrada possui em saída
                        $registroEncontrado = false;

                        foreach ($saidas as $saida) {
                            if ($saida["periodo"] == $entrada["periodo"]) {
                                $registroEncontrado = true;
                            }
                        }

                        if (!$registroEncontrado) {
                            $saidas[] = ["periodo" => $entrada["periodo"], "qte_gotas" => 0];
                        }

                        $somaEntradas += $entrada["qte_gotas"];
                    }

                    foreach ($saidas as $saida) {
                        // verifica se o periodo de entrada possui em saída
                        $registroEncontrado = false;

                        foreach ($entradas as $entrada) {
                            if ($entrada["periodo"] == $saida["periodo"]) {
                                $registroEncontrado = true;
                            }
                        }

                        if (!$registroEncontrado) {
                            $entradas[] = ["periodo" => $saida["periodo"], "qte_gotas" => 0];
                        }

                        $somaSaidas += $saida["qte_gotas"];
                    }

                    usort($entradas, function ($a, $b) {

                        return $a["periodo"] > $b["periodo"];
                    });

                    usort($saidas, function ($a, $b) {
                        return $a["periodo"] > $b["periodo"];
                    });

                    $entradasAnalitico = [];
                    $saidasAnalitico = [];

                    // Se o relatório é analítico, o agrupamento dos registros será pelo mês
                    if ($tipoRelatorio == REPORT_TYPE_ANALYTICAL) {

                        foreach ($entradas as $entrada) {
                            $dataAgrupamento = new DateTime($entrada["periodo"]);
                            $dataAgrupamento = $dataAgrupamento->format("Y-m");
                            $entradasAnalitico[$dataAgrupamento]["data"][] = $entrada;
                        }

                        foreach ($saidas as $saida) {
                            $dataAgrupamento = new DateTime($saida["periodo"]);
                            $dataAgrupamento = $dataAgrupamento->format("Y-m");
                            $saidasAnalitico[$dataAgrupamento]["data"][] = $saida;
                        }

                        $entradasAnaliticoTemp = [];
                        foreach ($entradasAnalitico as $entrada) {
                            $somaEntradasAnalitico = 0;

                            foreach ($entrada["data"] as $registroAnalitico) {
                                $somaEntradasAnalitico += $registroAnalitico["qte_gotas"];
                            }

                            $entrada["soma_entradas"] = $somaEntradasAnalitico;
                            $entradasAnaliticoTemp[] = $entrada;
                        }

                        $saidasAnaliticoTemp = [];
                        foreach ($saidasAnalitico as $saida) {
                            $somaSaidasAnalitico = 0;

                            foreach ($saida["data"] as $registroAnalitico) {
                                $somaSaidasAnalitico += $registroAnalitico["qte_gotas"];
                            }

                            $saida["soma_saidas"] = $somaSaidasAnalitico;
                            $saidasAnaliticoTemp[] = $saida;
                        }

                        $entradasAnalitico = $entradasAnaliticoTemp;
                        $saidasAnalitico = $saidasAnaliticoTemp;

                        $entradas = $entradasAnalitico;
                        $saidas = $saidasAnalitico;
                    }

                    $clientesPontuacoes[] = [
                        "cliente" => $cliente,
                        "pontuacoes_entradas" => $entradas,
                        "pontuacoes_saidas" => $saidas,
                        "soma_entradas" => $somaEntradas,
                        "soma_saidas" => $somaSaidas,
                    ];

                    $totalEntradas += $somaEntradas;
                    $totalSaidas += $somaSaidas;
                }

                $pontuacoesReport = ["pontuacoes" => $clientesPontuacoes, "total_entradas" => $totalEntradas, "total_saidas" => $totalSaidas];
                $dadosRelatorio = ['pontuacoes_report' => $pontuacoesReport];
                $dataRetorno = ["data" => $dadosRelatorio];

                return ResponseUtil::successAPI(MSG_LOAD_DATA_WITH_SUCCESS, $dataRetorno);
            }
        } catch (\Throwable $th) {
            $errorMessage = $th->getMessage();
            $errorCode = $th->getCode();

            if (count($errors) == 0) {
                $errors[] = $errorMessage;
                $errorCodes[] = $errorCode;
            }

            for ($i = 0; $i < count($errors); $i++) {
                Log::write("error", sprintf("[%s] %s - %s", MESSAGE_LOAD_DATA_WITH_ERROR, $errorCodes[$i], $errors[$i]));
            }

            return ResponseUtil::errorAPI(MESSAGE_LOAD_DATA_WITH_ERROR, $errors, [], $errorCodes);
        }
    }

    public function relUsuariosFrequenciaMediaAPI()
    {
        $sessaoUsuario = $this->getSessionUserVariables();

        $rede = $sessaoUsuario["rede"];
        $usuarioLogado = $sessaoUsuario["usuarioLogado"];
        // usuarioAdministrador
        // usuarioAdministrar
        // usuarioLogado
        // rede
        // cliente
        if ($this->request->is("post")) {
            $data = $this->request->getData();
            $clientesId = !empty($data["clientes_id"]) ? $data["clientes_id"] : null;
            $nomeUsuario = !empty($data["usuarios_nome"]) ? $data["usuarios_nome"] : null;
            $statusConta = !empty($data["usuarios_status_conta"]) ? $data["usuarios_status_conta"] : null;
            $tipoFrequencia = !empty($data["tipo_frequencia"]) ? $data["tipo_frequencia"] : null;

            if (($usuarioLogado["tipo_perfil"] >= PROFILE_TYPE_ADMIN_NETWORK && $usuarioLogado["tipo_perfil"] <= PROFILE_TYPE_WORKER)
                && (empty($rede))
            ) {
                $erros = array();
                // É necessário informar uma rede
            }
        }
    }
}
