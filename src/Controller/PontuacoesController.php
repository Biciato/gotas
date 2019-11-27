<?php

namespace App\Controller;

use \DateTime;
use \Exception;
use App\Controller\AppController;
use App\Custom\RTI\DebugUtil;
use Cake\Log\Log;
use Cake\Core\Configure;
use Cake\Event\Event;
use App\Custom\RTI\ResponseUtil;
use App\Model\Entity\Cliente;
use Cake\Http\Client\Request;
use Cake\I18n\Number;
use stdClass;

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
    public function relatorioCuponsProcessados()
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
            $date = date('Y-m-d');
            $cpf = "";
            $end = \strtotime($date);
            $start = \strtotime($date . ' -7 days');
            $end = date("Y-m-d 23:59:59", $end);
            $start = date("Y-m-d 00:00:00", $start);

            // TODO: Ajustar
            $funcionariosQuery = $this->Usuarios->findFuncionariosRede($rede->id, $clientesIds, null, null, null, PROFILE_TYPE_WORKER)->select(['id', 'nome']);
            $funcionarios = array();

            foreach ($funcionariosQuery as $key => $value) {
                $funcionarios[] = array('value' => $value["id"], 'text' => $value["nome"]);
            }

            $queryConditions = [];

            $funcionariosId = 0;
            $queryConditionsTemp = $this->request->session()->read("QueryConditions");

            if (!empty($queryConditionsTemp)) {
                $queryConditions = $queryConditionsTemp;
            }

            if (!$this->request->is(['post'])) {
                // Se não tiver filtrado, consultará a última semana

                $data = $this->request->getQueryParams();

                if (empty($queryConditions)) {
                    $date = date('Y-m-d');
                    $end = \strtotime($date);
                    $start = \strtotime($date . ' -7 days');
                    $end = date("Y-m-d 23:59:59", $end);
                    $start = date("Y-m-d 00:00:00", $start);
                } else {
                    $date = date('Y-m-d');
                    $endTemp = \strtotime($date);
                    $startTemp = \strtotime($date . ' -7 days');
                    $endTemp = date("Y-m-d 23:59:59", $endTemp);
                    $startTemp = date("Y-m-d 00:00:00", $startTemp);
                    $start = !empty($queryConditions["start"]) ? $queryConditions['start'] : $startTemp;
                    $end = !empty($queryConditions["end"]) ? $queryConditions['end'] : $endTemp;

                    if (!empty($queryConditions["funcionarios_id"])) {
                        $funcionariosId = $queryConditions["funcionarios_id"];
                        $arrayOptions[] = array('funcionarios_id' => $funcionariosId);
                    }

                    if (!empty($queryConditions["cpf"])) {
                        $cpf = $queryConditions["cpf"];
                        $arrayOptions[] = ["Usuarios.cpf" => $cpf];
                    }
                }

                $arrayOptions[] = array('data between "' . $start . '" and "' . $end . '"');
                $pontuacoes = $this->PontuacoesComprovantes->getCouponsByClienteId($clientesIds, $arrayOptions);

                $pontuacoes = $this->paginate($pontuacoes, ["limit" => 10]);

                $pontuacoes_new_array = [];

                foreach ($pontuacoes as $key => $value) {
                    $value['soma_pontuacoes'] = $this->Pontuacoes->getSumPontuacoesByComprovanteId($value['id']);

                    array_push($pontuacoes_new_array, $value);
                }

                $pontuacoes = null;
                $pontuacoes = $pontuacoes_new_array;
            } else {
                $data = $this->request->getData();

                $funcionariosId = !empty($data["funcionarios_id"]) ? (int) $data["funcionarios_id"] : null;
                $cpf = !empty($data["cpf"]) ? $data["cpf"] : null;

                if ($data['filtrar_unidade'] != "") {
                    $clientesIds = [];
                    $clientesIds[] = (int) $data['filtrar_unidade'];
                }

                if (!empty($funcionariosId)) {
                    $arrayOptions[] = array('funcionarios_id' => $funcionariosId);
                    $queryConditions["funcionarios_id"] = $funcionariosId;
                } else {
                    unset($queryConditions["funcionarios_id"]);
                }

                if (!empty($cpf)) {
                    $cpf = preg_replace("/\D/", "", $cpf);
                    $queryConditions["cpf"] = $cpf;
                } else {
                    unset($queryConditions["cpf"]);
                }

                if (strlen($data['data_inicio']) > 0) {
                    $start = $data['data_inicio'] . ' 00:00:00';

                    $start = $this->datetime_util->convertDateTimeToUTC($start);
                }

                if (strlen($data['data_fim']) > 0) {
                    $end = $data['data_fim'] . ' 23:59:59';

                    $end = $this->datetime_util->convertDateTimeToUTC($end);
                }

                $arrayOptions[] = array('PontuacoesComprovantes.data between "' . $start . '" and "' . $end . '"');

                if (!empty($cpf)) {
                    $arrayOptions[] = ["Usuarios.cpf" => $cpf];
                }

                $queryConditions["start"] = $start;
                $queryConditions["end"] = $end;
                $queryConditions["cpf"] = $cpf;

                $this->request->session()->write("QueryConditions", $queryConditions);

                $pontuacoes = $this->PontuacoesComprovantes->getCouponsByClienteId($clientesIds, $arrayOptions);
                $pontuacoes = $this->paginate($pontuacoes, ['limit' => 10]);

                // DebugUtil::printArray($pontuacoes);
                $pontuacoes_new_array = [];

                foreach ($pontuacoes as $key => $value) {
                    $value['soma_pontuacoes'] = $this->Pontuacoes->getSumPontuacoesByComprovanteId($value['id']);
                    array_push($pontuacoes_new_array, $value);
                }

                $pontuacoes = null;
                $pontuacoes = $pontuacoes_new_array;
            }

            $start = substr($start, 0, 10);
            $end = substr($end, 0, 10);

            $start = explode("-", $start);
            $end = explode("-", $end);
            $start = sprintf("%s/%s/%s", $start[2], $start[1], $start[0]);
            $end = sprintf("%s/%s/%s", $end[2], $end[1], $end[0]);
            $arraySet = array('pontuacoes', 'funcionarios', "funcionariosId",  'cliente', "cpf", 'unidadesIds', "start", "end");
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
     * PontuacoesController.php::relGestaoGotas
     *
     * View para Relatório de Entrada e Saída de Pontuações
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-09-11
     *
     * @return void
     */
    public function relGestaoGotas()
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
     * PontuacoesController.php::relatorioPontuacaoSimplificado
     *
     * View para Relatório de Entrada e Saída de Pontuações
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-09-17
     *
     * @return void
     */
    public function relatorioPontuacaoSimplificado()
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

            $sessaoUsuario = $this->getSessionUserVariables();
            $usuario = $sessaoUsuario["usuarioLogado"];

            $sessaoUsuario = $this->getSessionUserVariables();
            $usuario = $sessaoUsuario["usuarioLogado"];

            if ($this->request->is("post")) {
                $data = $this->request->getData();

                Log::write("info", sprintf("Info de Post: %s - %s.", __CLASS__, __METHOD__));
                Log::write("info", $data);

                $redesId = $data["redes_id"];
                $usuariosId = !empty($data["usuarios_id"]) ? $data["usuarios_id"] : null;
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

                // Se não for usuário e o id não tiver sido especificado
                if (empty($usuariosId) && $usuario->tipo_perfil < PROFILE_TYPE_USER) {
                    $errors = [MSG_USUARIOS_ID_EMPTY];
                    $errorCodes = [MSG_USUARIOS_ID_EMPTY_CODE];
                    return ResponseUtil::errorAPI(MESSAGE_GENERIC_EXCEPTION, $errors, [], $errorCodes);
                } elseif ($usuario->tipo_perfil == PROFILE_TYPE_USER && empty($usuariosId)) {
                    // Se é usuário, atribui o id de usuário
                    $usuariosId = $usuario->id;
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

                $retorno = $this->Pontuacoes->getSumPontuacoesOfUsuario($usuariosId, $redesId, $clientesIds);


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
                    $entradas = $this->Pontuacoes->getPontuacoesInOutForClientes($cliente->id, $brindesId, $dataInicio, $dataFim, TYPE_OPERATION_IN, $tipoRelatorio);
                    // Pontuações de Saída devem vir da tabela de Cupons (pois é o que realmente foi retirado)
                    // Tabela de pontuações só guarda aquilo que foi GASTO
                    $saidas = $this->Pontuacoes->getPontuacoesInOutForClientes($cliente->id, $brindesId, $dataInicio, $dataFim, TYPE_OPERATION_OUT, $tipoRelatorio);

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

    public function getResumePontuacoes()
    {
        // @TODO fazer o resumo
        if ($this->request->is(Request::METHOD_GET)) {

        }
    }

    /**
     * Obtem relatório de movimentação
     *
     * Obtem dados e relatório em formato JSON indicando movimentação de Gotas dos clientes conforme posto selecionado
     *
     * PontuacoesController::getExtratoPontuacoesAPI
     *
     * @param int $clientes_id Id de Cliente
     * @param int $gotas_id Id de Gota
     * @param int $funcionarios_id Id de Funcionário
     * @param string $tipo_relatorio Tipo de Relatório (Analítico / Sintético)
     * @param Date $data_inicio Data Inicio
     * @param Date $data_fim Data Fim
     *
     * @return json_object $data
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-10-24
     */
    public function getRelatorioMovimentacaoGotasAPI()
    {
        $sessao = $this->getSessionUserVariables();
        $rede = $sessao["rede"];
        $usuario = $sessao["usuarioLogado"];
        $usuarioAdministrar = $sessao["usuarioAdministrar"];

        if ($usuarioAdministrar) {
            $usuario = $usuarioAdministrar;
        }

        $errors = [];
        $errorCodes = [];

        try {
            if ($this->request->is(Request::METHOD_GET)) {
                $data = $this->request->getQueryParams();
                $clientesId = !empty($data["clientes_id"]) ? (int) $data["clientes_id"] : null;
                $gotasId = !empty($data["gotas_id"]) ? (int) $data["gotas_id"] : null;
                $funcionariosId = !empty($data["funcionarios_id"]) ? (int) $data["funcionarios_id"] : null;
                $tipoRelatorio = !empty($data["tipo_relatorio"]) ? $data["tipo_relatorio"] : REPORT_TYPE_SYNTHETIC;
                $dataInicio = !empty($data["data_inicio"]) ? $data["data_inicio"] : null;
                $dataFim = !empty($data["data_fim"]) ? $data["data_fim"] : null;

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
                if ($dataDiferenca->m >= 3 && $tipoRelatorio === REPORT_TYPE_ANALYTICAL) {
                    $errors[] = sprintf(MSG_MAX_FILTER_TIME_MONTH, "3");
                    $errorCodes[] = sprintf(MSG_MAX_FILTER_TIME_MONTH_CODE, "3");
                } elseif ($dataDiferenca->y >= 1 && $tipoRelatorio === REPORT_TYPE_SYNTHETIC) {
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

                $clientesIds = $this->RedesHasClientes->getClientesIdsFromRedesHasClientes($rede->id);

                if (!empty($clientesId)) {
                    $clientesIds = [$clientesId];
                }

                $list = [];
                $totalGotas = 0;
                $totalLitros = 0;
                $totalReais = 0;

                foreach ($clientesIds as $clientesIdItem) {
                    $cliente = $this->Clientes->get($clientesIdItem);

                    $pontuacoes = $this->Pontuacoes->getPontuacoesGotasMovimentationForClientes($cliente->id, $gotasId, $funcionariosId, $dataInicio, $dataFim, $tipoRelatorio);

                    $item = new stdClass();
                    $item->cliente = $cliente;

                    $pontuacoesTemp = [];
                    $estabelecimentoGotas = 0;
                    $estabelecimentoLitros = 0;
                    $estabelecimentoReais = 0;

                    if ($tipoRelatorio == REPORT_TYPE_ANALYTICAL) {
                        $dataAgrupamento = "";
                        $somaGotas = 0;
                        $somaLitros = 0;
                        $somaReais = 0;
                        $somaPeriodo = [];

                        foreach ($pontuacoes as $pontuacao) {
                            $dataAgrupamento = $pontuacao->ano . "/" . $pontuacao->mes;
                            $pontuacoesTemp[$dataAgrupamento]["pontuacoes"][] = $pontuacao;
                            $somaPeriodo[$dataAgrupamento]["soma_gotas"] = 0;
                            $somaPeriodo[$dataAgrupamento]["soma_litros"] = 0;
                            $somaPeriodo[$dataAgrupamento]["soma_reais"] = 0;

                            $somaGotas += $pontuacao->quantidade_gotas;
                            $somaLitros += $pontuacao->quantidade_litros;
                            $somaReais += $pontuacao->quantidade_reais;
                        }

                        $estabelecimentoGotas += $somaGotas;
                        $estabelecimentoLitros += $somaLitros;
                        $estabelecimentoReais += $somaReais;

                        $totalGotas += $somaGotas;
                        $totalLitros += $somaLitros;
                        $totalReais += $somaReais;

                        $somaGotas = 0;
                        $somaLitros = 0;
                        $somaReais = 0;

                        foreach ($pontuacoesTemp as $pontuacao) {
                            foreach ($pontuacao["pontuacoes"] as $pontuacaoData) {
                                $dataAgrupamento = $pontuacaoData->ano . "/" . $pontuacaoData->mes;
                                $somaPeriodo[$dataAgrupamento]["soma_gotas"] += $pontuacaoData->quantidade_gotas;
                                $somaPeriodo[$dataAgrupamento]["soma_litros"] += $pontuacaoData->quantidade_litros;
                                $somaPeriodo[$dataAgrupamento]["soma_reais"] += $pontuacaoData->quantidade_reais;
                            }
                        }

                        foreach ($somaPeriodo as $periodo => $soma) {
                            $pontuacoesTemp[$periodo]["soma_gotas"] = $soma["soma_gotas"];
                            $pontuacoesTemp[$periodo]["soma_litros"] = $soma["soma_litros"];
                            $pontuacoesTemp[$periodo]["soma_reais"] = $soma["soma_reais"];
                        }
                    }

                    if (count($pontuacoesTemp) > 0) {
                        $pontuacoes = $pontuacoesTemp;
                    }

                    if ($tipoRelatorio == REPORT_TYPE_SYNTHETIC) {
                        $totalGotas += $pontuacoes->quantidade_gotas;
                        $totalLitros += $pontuacoes->quantidade_litros;
                        $totalReais += $pontuacoes->quantidade_reais;
                        $item->pontuacoes = $pontuacoes;
                    } else {
                        $item->periodos = $pontuacoes;

                        $item->estabelecimento_gotas = $estabelecimentoGotas;
                        $item->estabelecimento_litros = $estabelecimentoLitros;
                        $item->estabelecimento_reais = $estabelecimentoReais;
                    }

                    $list[] = $item;
                }

                $data = [
                    "data" =>
                    [
                        "pontuacoes" => $list,
                        "total_gotas" => $totalGotas,
                        "total_litros" => $totalLitros,
                        "total_reais" => $totalReais,
                    ]
                ];

                return ResponseUtil::successAPI(MSG_LOAD_DATA_WITH_SUCCESS, $data);
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
