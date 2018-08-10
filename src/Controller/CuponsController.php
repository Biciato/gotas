<?php
namespace App\Controller;

use \DateTime;
use App\Controller\AppController;
use App\Custom\RTI\DebugUtil;
use App\Custom\RTI\Security;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Collection\Collection;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Log\Log;
use Cake\Mailer\Email;
use Cake\Routing\Router;
use Cake\View\Helper\UrlHelper;

/**
 * Cupons Controller
 *
 * @property \App\Model\Table\CuponsTable $Cupons
 *
 * @method \App\Model\Entity\Cupom[] paginate($object = null, array $settings = [])
 */
class CuponsController extends AppController
{
    /**
     * ------------------------------------------------------------
     * Fields
     * ------------------------------------------------------------
     */
    protected $user_logged = null;


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
            'contain' => ['Brindes', 'Clientes', 'Usuarios']
        ];
        $cupons = $this->paginate($this->Cupons);

        $this->set(compact('cupons'));
        $this->set('_serialize', ['cupons']);
    }

    /**
     * View method
     *
     * @param string|null $id Cupom id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $cupom = $this->Cupons->get($id, [
            'contain' => ['Brindes', 'Clientes', 'Usuarios']
        ]);

        $this->set('cupom', $cupom);
        $this->set('_serialize', ['cupom']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $cupom = $this->Cupons->newEntity();
        if ($this->request->is('post')) {
            $cupom = $this->Cupons->patchEntity($cupom, $this->request->getData());
            if ($this->Cupons->save($cupom)) {
                $this->Flash->success(__('The cupom has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The cupom could not be saved. Please, try again.'));
        }
        $brindes = $this->Cupons->Brindes->find('list', ['limit' => 200]);
        $clientes = $this->Cupons->Clientes->find('list', ['limit' => 200]);
        $usuarios = $this->Cupons->Usuarios->find('list', ['limit' => 200]);
        $this->set(compact('cupom', 'brindes', 'clientes', 'usuarios'));
        $this->set('_serialize', ['cupom']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Cupom id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $cupom = $this->Cupons->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $cupom = $this->Cupons->patchEntity($cupom, $this->request->getData());
            if ($this->Cupons->save($cupom)) {
                $this->Flash->success(__('The cupom has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The cupom could not be saved. Please, try again.'));
        }
        $brindes = $this->Cupons->Brindes->find('list', ['limit' => 200]);
        $clientes = $this->Cupons->Clientes->find('list', ['limit' => 200]);
        $usuarios = $this->Cupons->Usuarios->find('list', ['limit' => 200]);
        $this->set(compact('cupom', 'brindes', 'clientes', 'usuarios'));
        $this->set('_serialize', ['cupom']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Cupom id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $cupom = $this->Cupons->get($id);
        if ($this->Cupons->delete($cupom)) {
            $this->Flash->success(__('The cupom has been deleted.'));
        } else {
            $this->Flash->error(__('The cupom could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * ------------------------------------------------------------
     * Custom Actions
     * ------------------------------------------------------------
     */

    /**
     * Ação de escolher cupom, visualizada por gerentes à níveis acima
     *
     * @return void
     */
    public function emissaoBrindeSuperiores()
    {
        $urlRedirectConfirmacao = array("controller" => "cupons", "action" => "emissao_brinde_superiores");
        $user_admin = $this->request->session()->read('User.RootLogged');
        $user_managed = $this->request->session()->read('User.ToManage');

        if ($user_admin) {
            $this->user_logged = $user_managed;
        }

        $cliente = $this->security_util->checkUserIsClienteRouteAllowed($this->user_logged, $this->Clientes, $this->ClientesHasUsuarios);

        $cliente = $this->request->session()->read("Network.Unit");


        $arraySet = array(
            "cliente",
            "urlRedirectConfirmacao"
        );

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);
    }

    /**
     * CuponsController::emissaoBrindeAvulso
     *
     * Action para emissão de Brinde Smart Shower Avulso
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 21/06/2018
     *
     * @return void
     */
    public function emissaoBrindeAvulso()
    {
        $urlRedirectConfirmacao = array("controller" => "cupons", "action" => "emissao_brinde_avulso");

        $user_admin = $this->request->session()->read('User.RootLogged');
        $user_managed = $this->request->session()->read('User.ToManage');

        if ($user_admin) {
            $this->user_logged = $user_managed;
        }

        $usuario = $this->Usuarios->newEntity();
        $transportadora = $this->Transportadoras->newEntity();
        $veiculo = $this->Veiculos->newEntity();

        $funcionario = $this->Usuarios->getUsuarioById($this->user_logged['id']);

        $rede = $this->request->session()->read('Network.Main');

        // Pega unidades que tem acesso
        $clientes_ids = [];

        $unidades_ids = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($rede->id, $this->user_logged['id'], false);

        foreach ($unidades_ids as $key => $value) {
            $clientes_ids[] = $key;
        }

        // No caso do funcionário, ele só estará em
        // uma unidade, então pega o cliente que ele estiver

        $cliente = $this->Clientes->getClienteById($clientes_ids[0]);

        $clientes_id = $cliente->id;

        // o estado do funcionário é o local onde se encontra o estabelecimento.
        $estado_funcionario = $cliente->estado;

        $transportadoraPath = "TransportadorasHasUsuarios.Transportadoras.";
        $veiculoPath = "UsuariosHasVeiculos.Veiculos.";

        $arraySet = array(
            "urlRedirectConfirmacao",
            "usuario",
            "cliente",
            "clientes_id",
            "funcionario",
            "estado_funcionario",
            "transportadoraPath",
            "veiculoPath"
        );

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);
    }

    /**
     * Ação de escolher cupom
     *
     * @return void
     *
     * TODO: ver se esta action precisará
     */
    public function escolherBrinde()
    {
        $user_admin = $this->request->session()->read('User.RootLogged');
        $user_managed = $this->request->session()->read('User.ToManage');

        if ($user_admin) {
            $this->user_logged = $user_managed;
        }

        $cliente = $this->security_util->checkUserIsClienteRouteAllowed($this->user_logged, $this->Clientes, $this->ClientesHasUsuarios);
    }

    /**
     * Imprime Bilhete Smart Shower Ticket
     *
     * @return void
     * @author
     **/
    public function brindeShower()
    {
        $user_admin = $this->request->session()->read('User.RootLogged');
        $user_managed = $this->request->session()->read('User.ToManage');

        if ($user_admin) {
            $this->user_logged = $user_managed;
        }

        $cliente = $this->security_util->checkUserIsClienteRouteAllowed($this->user_logged, $this->Clientes, $this->ClientesHasUsuarios);

        $client_to_manage = $this->request->session()->read('ClientToManage');

        if (!is_null($client_to_manage)) {
            $cliente = $client_to_manage;
        }

        $rede = $this->request->session()->read('Network.Main');

        $unidades_ids = [];

        $clientes_ids = [];

        // Se o perfil é até administrador regional, pode filtrar por todas as unidades / unidades que tem acesso
        if ($this->user_logged['tipo_perfil'] <= Configure::read('profileTypes')['AdminRegionalProfileType']) {
            $unidades_ids = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($rede->id, $this->user_logged['id']);

            foreach ($unidades_ids as $key => $value) {
                $clientes_ids[] = $key;
            }
        } else {
            $unidades_ids = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($rede->id, $this->user_logged['id']);

            foreach ($unidades_ids as $key => $value) {
                $clientes_ids[] = $key;
            }
        }

        $conditions = [];

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();

            if (strlen($data['parametro']) > 0) {
                if ($data['opcoes'] == 'nome') {
                    array_push($conditions, ['brindes.nome like' => '%' . $data['parametro'] . '%']);
                } else {
                    array_push($conditions, ['brindes.preco_padrao' => $data['parametro']]);
                }
            }

            if ($data['filtrar_unidade'] != "") {
                $clientes_ids = [];
                $clientes_ids[] = (int)$data['filtrar_unidade'];
            }

        }

        $clientes_has_brindes_habilitados = $this->ClientesHasBrindesHabilitados->getBrindesHabilitadosByClienteId($clientes_ids, $conditions);

        $this->set(compact(['clientesHasBrindesHabilitados', 'cliente']));
    }

    /**
     * Imprime Bilhete Comum
     *
     * @return void
     * @author
     **/
    public function brindeComum()
    {
        $user_admin = $this->request->session()->read('User.RootLogged');
        $user_managed = $this->request->session()->read('User.ToManage');

        if ($user_admin) {
            $this->user_logged = $user_managed;
        }

        $cliente = $this->security_util->checkUserIsClienteRouteAllowed($this->user_logged, $this->Clientes, $this->ClientesHasUsuarios);

        $client_to_manage = $this->request->session()->read('ClientToManage');

        if (!is_null($client_to_manage)) {
            $cliente = $client_to_manage;
        }

        $rede = $this->request->session()->read('Network.Main');

        $unidades_ids = [];

        $clientes_ids = [];

        // Se o perfil é até administrador regional, pode filtrar por todas as unidades / unidades que tem acesso
        if ($this->user_logged['tipo_perfil'] <= Configure::read('profileTypes')['AdminRegionalProfileType']) {
            $unidades_ids = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($rede->id, $this->user_logged['id']);

            foreach ($unidades_ids as $key => $value) {
                $clientes_ids[] = $key;
            }
        } else {
            $unidades_ids = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($rede->id, $this->user_logged['id']);

            foreach ($unidades_ids as $key => $value) {
                $clientes_ids[] = $key;
            }
        }

        $conditions = [];

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();

            if (strlen($data['parametro']) > 0) {
                if ($data['opcoes'] == 'nome') {
                    array_push($conditions, ['brindes.nome like' => '%' . $data['parametro'] . '%']);
                } else {
                    array_push($conditions, ['brindes.preco_padrao' => $data['parametro']]);
                }
            }

            if ($data['filtrar_unidade'] != "") {
                $clientes_ids = [];
                $clientes_ids[] = (int)$data['filtrar_unidade'];
            }

        }

        $clientes_has_brindes_habilitados = $this->ClientesHasBrindesHabilitados->getBrindesHabilitadosByClienteId($clientes_ids, $conditions);

        $this->set(compact(['clientesHasBrindesHabilitados', 'cliente']));
    }

    /**
     * ------------------------------------------------------------
     * Métodos para tickets
     * ------------------------------------------------------------
     */

    /**
     * Exibe o histórico de brindes emitidos da rede do cliente
     *
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function historicoBrindes()
    {
        $user_admin = $this->request->session()->read('User.RootLogged');
        $user_managed = $this->request->session()->read('User.ToManage');

        if ($user_admin) {
            $this->user_logged = $user_managed;
        }

        // pega a rede e as unidades que o usuário tem acesso

        $rede = $this->request->session()->read('Network.Main');

        // Pega unidades que tem acesso
        $clientes_ids = [];

        $unidades_ids = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($rede->id, $this->user_logged['id'], false);

        foreach ($unidades_ids as $key => $value) {
            $clientes_ids[] = $key;
        }

        if ($this->request->is('post')) {

            $data = $this->request->getData();

            if ($data['filtrar_unidade'] != "") {
                $clientes_ids = [];
                $clientes_ids[] = (int)$data['filtrar_unidade'];
            }
        }

        $cliente = $unidades_ids->toArray()[$clientes_ids[0]];

        $cupons = $this->Cupons->getCuponsByClienteIds($clientes_ids, date('Y-m-d'));

        // se não tiver filtros, ordena decrescente pela data
        $this->paginate = ['order' => ['Cupons.data' => 'desc'], 'limit' => 10];

        $this->paginate($cupons);

        $this->set(compact(['cupons', 'cliente', 'unidades_ids']));
    }

    /**
     * Detalhes de um determinado cupom emitido
     *
     * @param int $id Id do Cupom
     *
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function detalhesTicket(int $id = null)
    {
        $user_admin = $this->request->session()->read('User.RootLogged');
        $user_managed = $this->request->session()->read('User.ToManage');

        if ($user_admin) {
            $this->user_logged = $user_managed;
        }

        $cliente = $this->security_util->checkUserIsClienteRouteAllowed($this->user_logged, $this->Clientes, $this->ClientesHasUsuarios);

        $cupom = $this->Cupons->getCuponsById($id);

        $this->set(compact(['cupom']));
    }

    /**
     * ------------------------------------------------------------
     * Métodos para cliente final
     * ------------------------------------------------------------
     */

    /**
     * Action para imprimir cupom emitido do cliente final (usuário)
     *
     * @param string  $cupom_emitido Cupom à ser Emitido
     * @return void
     */
    public function imprimeBrindeComum(string $cupom_emitido)
    {
        try {
            $user_admin = $this->request->session()->read('User.RootLogged');
            $user_managed = $this->request->session()->read('User.ToManage');

            if ($user_admin) {
                $this->user_logged = $user_managed;
            }

            $user_logged = $this->user_logged;

            $cupons = $this->Cupons->getCuponsByCupomEmitido($cupom_emitido);

            $cupons_data = $this->prepareCuponsData($cupons);

            $cupom_id = $cupons_data['cupom_id'];
            $cupom_emitido = $cupons_data['cupom_emitido'];
            $cliente_final = $cupons_data['cliente_final'];
            $clientes_has_brindes_habilitados_id = $cupons_data['clientes_has_brindes_habilitados_id'];
            $clientes_id = $cupons_data['clientes_id'];
            $data_impressao = $cupons_data['data_impressao'];
            $produtos = $cupons_data['produtos'];
            $redes_id = $cupons_data['redes_id'];

            $arraySet = [
                'cupom_id',
                'cupom_emitido',
                'cliente_final',
                'clientes_has_brindes_habilitados_id',
                'clientes_id',
                'cupons',
                'data_impressao',
                'produtos',
                'redes_id',
                'user_logged',
            ];

            $this->set(compact($arraySet));
            $this->set('_serialize', $arraySet);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao obter novo nome para comprovante: {0} em: {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }
    /**
     * Action para reimprimir cupom emitido do cliente final (usuário)
     *
     * @param string  $cupom_emitido Cupom à ser Emitido
     * @return void
     */
    public function reimprimeBrindeComum(string $cupom_emitido)
    {
        try {
            $user_admin = $this->request->session()->read('User.RootLogged');
            $user_managed = $this->request->session()->read('User.ToManage');

            if ($user_admin) {
                $this->user_logged = $user_managed;
            }

            $user_logged = $this->user_logged;

            $cupons = $this->Cupons->getCuponsByCupomEmitido($cupom_emitido);

            $cupons_data = $this->prepareCuponsData($cupons);

            $cupom_id = $cupons_data['cupom_id'];
            $cupom_emitido = $cupons_data['cupom_emitido'];
            $cliente_final = $cupons_data['cliente_final'];
            $clientes_has_brindes_habilitados_id = $cupons_data['clientes_has_brindes_habilitados_id'];
            $clientes_id = $cupons_data['clientes_id'];
            $data_impressao = $cupons_data['data_impressao'];
            $produtos = $cupons_data['produtos'];
            $redes_id = $cupons_data['redes_id'];

            $arraySet = [
                'cupom_id',
                'cupom_emitido',
                'cliente_final',
                'clientes_has_brindes_habilitados_id',
                'cupons',
                'data_impressao',
                'produtos',
                'redes_id',
                'user_logged',
            ];

            $this->set(compact($arraySet));
            $this->set('_serialize', $arraySet);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao obter novo nome para comprovante: {0} em: {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Prepara dados para extraão de array de cupons
     *
     * @param \App\Model\Entity\Cupom $cupons
     * @return void
     */
    private function prepareCuponsData($cupons)
    {
        $data = null;

        if (sizeof($cupons->toArray()) > 0) {

            $cliente_final = $this->Usuarios->getUsuarioById($cupons->toArray()[0]->usuarios_id);

            $cupom_emitido = $cupons->toArray()[0]->cupom_emitido;
            $data_impressao = $cupons->toArray()[0]->data->format('d/m/Y H:i:s');
            $cupom_id = $cupons->toArray()[0]->id;
            $clientes_has_brindes_habilitados_id = $cupons->toArray()[0]->clientes_has_brindes_habilitado->id;
            $redes_id = $cupons->toArray()[0]->cliente->rede_has_cliente->redes_id;
            $clientes_id = $cupons->toArray()[0]->cliente->rede_has_cliente->clientes_id;

            // percorrer o cupom e pegar todos os produtos

            foreach ($cupons as $key => $value) {
                $produto = null;
                $produto['qte'] = $value->quantidade;
                $produto['nome'] = $value->clientes_has_brindes_habilitado->brinde->nome;
                $produto['valor_pago'] = $value->valor_pago;

                $produtos[] = $produto;
            }

            $data = [
                'cliente_final' => $cliente_final,
                'clientes_id' => $clientes_id,
                'clientes_has_brindes_habilitados_id' => $clientes_has_brindes_habilitados_id,
                'cupom_emitido' => $cupom_emitido,
                'cupom_id' => $cupom_id,
                'data_impressao' => $data_impressao,
                'produtos' => $produtos,
                'redes_id' => $redes_id
            ];
        }
        return $data;
    }

    /**
     * Exibe detalhes de um brinde de usuário
     *
     * @param integer $usuarios_has_brindes_id Id de brinde de usuário
     * @return void
     */
    public function verDetalhes(int $cupons_id)
    {
        $user_admin = $this->request->session()->read('User.RootLogged');
        $user_managed = $this->request->session()->read('User.ToManage');

        if ($user_admin) {
            $this->user_logged = $user_managed;
        }

        $user_logged = $this->user_logged;

        $usuario = $this->Usuarios->getUsuarioById($user_logged['id']);

        $cupom = $this->Cupons->getCuponsById($cupons_id);

        $this->set(compact('cupom', 'user_logged'));
        $this->set('_serialize', ['cupom', 'user_logged']);
    }

    /**
     * Action para resgate de cupons (view de funcionário)
     *
     * @return void
     */
    public function resgateCupons()
    {

    }

    /**
     * ------------------------------------------------------------
     * Ajax Methods
     * ------------------------------------------------------------
     */

    /**
     * Serviço AJAX de impressão de brinde
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 01/09/2017
     *
     * @return json object
     */
    public function imprimeBrindeAjax()
    {
        try {

            if ($this->request->is(['post'])) {
                $data = $this->request->getData();

                // Validação de Dados
                $errors = array();
                if (empty($data["brindes_id"])) {
                    $errors[] = __("É necessário selecionar um brinde para resgatar pontos!");
                }
                if (empty($data["clientes_id"])) {
                    $errors[] = __("É necessário selecionar uma unidade de atendimento para resgatar pontos!");
                }

                if (sizeof($errors) > 0) {

                    $mensagem = array("status" => false, "message" => Configure::read("messageOperationFailureDuringProcessing"), "errors" => $errors);

                    $arraySet = array("mensagem");
                    $this->set(compact($arraySet));
                    $this->set("_serialize", $arraySet);

                    return;
                }

                $brindesId = $data["brindes_id"];
                $usuariosId = $data["usuarios_id"] == "conta_avulsa" ? 0 : $data["usuarios_id"];
                $clientesId = $data["clientes_id"];
                $quantidade = $data["quantidade"];
                $funcionariosId = isset($data["funcionarios_id"]) ? (int)$data["funcionarios_id"] : null;
                $contaAvulsa = $data["usuarios_id"] == "conta_avulsa";
                $senhaAtual = isset($data["current_password"]) ? $data["current_password"] : "";

                $retorno = $this->_trataCompraCupom($brindesId, $usuariosId, $clientesId, $quantidade, $funcionariosId, $contaAvulsa, $senhaAtual, false);

                $arraySet = $retorno["arraySet"];
                $mensagem = $retorno["mensagem"];
                $ticket = $retorno["ticket"];
                $cliente = $retorno["cliente"];
                $usuario = $retorno["usuario"];
                $tempo = $retorno["tempo"];
                $tipo_emissao_codigo_barras = $retorno["tipo_emissao_codigo_barras"];

                $is_brinde_smart_shower = $ticket["tipo_principal_codigo_brinde"] <= 4;
                $dados_impressao = null;

                if (!$is_brinde_smart_shower) {
                    $cupons = $this->Cupons->getCuponsByCupomEmitido($ticket["cupom_emitido"])->toArray();

                    $cuponsRetorno = array();

                    foreach ($cupons as $key => $cupom) {
                        $cupom["data"] = $cupom["data"]->format('d/m/Y H:i:s');

                        $cuponsRetorno[] = $cupom;
                    }

                    $dados_impressao = $this->processarCupom($cuponsRetorno);
                }
            }

            // $arraySet = [
            //     'message',
            //     'ticket',
            //     'status',
            //     'cliente',
            //     'usuario',
            //     'tempo',
            //     'tipo_emissao_codigo_barras',
            //     'is_brinde_smart_shower',
            //     'dados_impressao'
            // ];

            $this->set(compact($arraySet));
            $this->set("_serialize", $arraySet);

        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Houve um erro durante o processamento do Ticket. [{0}] ", $e->getMessage());

            Log::write('error', $stringError);

            $messageString = __("Não foi possível obter pontuações do usuário na rede!");
            $trace = $e->getTrace();
            $mensagem = array('status' => false, 'message' => $messageString, 'errors' => $trace);
            $messageStringDebug = __("{0} - {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $messageString, $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write("error", $messageStringDebug);
        }
    }

    /**
     * Web-service para imprimir brinde comum
     *
     * @return json object
     * @deprecated 1.0
     */
    public function geraBrindeComumAjax()
    {
        $result = null;
        $ticket = null;
        $message = null;
        $status = 'error';
        $cupom = null;

        if ($this->request->is(['post'])) {
            $data = $this->request->getData();

            $cliente = $this->Clientes->getClienteById($data['clientes_id']);

            $rede = $this->request->session()->read('Network.Main');

            // pega id de todos os clientes que estão ligados à uma rede

            $redes_has_clientes_query = $this->RedesHasClientes->getRedesHasClientesByRedesId($rede->id);

            $clientes_ids = [];

            foreach ($redes_has_clientes_query as $key => $value) {
                $clientes_ids[] = $value['clientes_id'];
            }

            $array = [];

            $clientes_id = $array;

            $brinde_habilitado = $this->ClientesHasBrindesHabilitados->getBrindeHabilitadoById(
                $data['brindes_id']
            );

            $quantidade = $data['quantidade'];

            if ($data['usuarios_id'] == "conta_avulsa") {
                $usuario = $this->Usuarios->getUsuariosByProfileType(Configure::read('profileTypes')['DummyUserProfileType'], 1);
            } else {
                $usuario = $this->Usuarios->getUsuarioById($data['usuarios_id']);
            }

            $usuario['pontuacoes'] = $this->Pontuacoes->getSumPontuacoesOfUsuario(
                $usuario['id'],
                $clientes_ids
            );

            // validação de senha do usuário

            $senhaValida = false;

            if ($usuario->tipo_perfil < Configure::read('profileTypes')['DummyUserProfileType']) {
                if ((new DefaultPasswordHasher)->check($data['current_password'], $usuario->senha)) {
                    $senhaValida = true;
                }
            } else {
                $senhaValida = true;
            }

            if ($senhaValida == false) {
                $message = 'Senha incorreta para usuário. Nâo foi possível resgatar o brinde';
            } else {

                // Se o usuário tiver pontuações suficientes ou for um usuário de venda avulsa somente
                if (($usuario->pontuacoes >= ($brinde_habilitado->brinde_habilitado_preco_atual->preco * $quantidade) || $usuario->tipo_perfil == Configure::read('profileTypes')['DummyUserProfileType'])) {

                    // verificar se cliente possui usuario em sua lista de usuários. se não tiver, cadastrar

                    $clientes_has_usuarios_conditions = [];

                    array_push($clientes_has_usuarios_conditions, ['ClientesHasUsuarios.usuarios_id' => $usuario['id']]);
                    array_push($clientes_has_usuarios_conditions, ['ClientesHasUsuarios.clientes_id IN' => $clientes_ids]);

                    if ($rede->permite_consumo_gotas_funcionarios) {
                        array_push($clientes_has_usuarios_conditions, ['ClientesHasUsuarios.tipo_perfil >= ' => Configure::read('profileTypes')['AdminNetworkProfileType']]);
                        array_push($clientes_has_usuarios_conditions, ['ClientesHasUsuarios.tipo_perfil <= ' => Configure::read('profileTypes')['UserProfileType']]);
                    } else {
                        array_push($clientes_has_usuarios_conditions, ['ClientesHasUsuarios.tipo_perfil' => Configure::read('profileTypes')['UserProfileType']]);
                    }

                    $clientePossuiUsuario = $this->ClientesHasUsuarios->findClienteHasUsuario($clientes_has_usuarios_conditions);

                    if (is_null($clientePossuiUsuario)) {
                        $this->ClientesHasUsuarios->addNewClienteHasUsuario($cliente->matriz_id, $cliente->id, $usuario->id);
                    }

                    // ------------------------------------------------------------------------
                    // Só diminui pontos se o usuário que estiver sendo vendido não for o avulso!
                    // ------------------------------------------------------------------------
                    if ($usuario->tipo_perfil < Configure::read('profileTypes')['DummyUserProfileType']) {

                    // ------------------- Atualiza pontos à serem debitados -------------------

                    /*
                         * Se há pontuação à debitar, devo verificar quais são as
                         * pontuações do usuário que serão utilizadas, para notificar
                         * quantos pontos ele possui que estão prestes à vencer
                         */

                        $pontuacoesProcessar = $brinde_habilitado->brinde_habilitado_preco_atual->preco * $quantidade;

                        $podeContinuar = true;
                        $pontuacoesPendentesUsoListaSave = [];

                    // Obter pontos não utilizados totalmente
                    // verifica se tem algum pendente para continuar o cálculo sobre ele

                        $pontuacaoPendenteUso = $this
                            ->Pontuacoes
                            ->getPontuacoesPendentesForUsuario(
                                $usuario->id,
                                $clientes_ids,
                                1,
                                null
                            );

                        if ($pontuacaoPendenteUso) {
                            $ultimoId = $pontuacaoPendenteUso->id;
                        } else {
                            $ultimoId = null;
                        }

                        if (!is_null($ultimoId)) {
                        // soma de pontos de todos os brindes usados
                            $pontuacoesBrindesUsados = $this
                                ->Pontuacoes
                                ->getSumPontuacoesPendingToUsageByUsuario(
                                    $usuario->id,
                                    $clientes_ids
                                );

                            $pontuacoesProcessar = $pontuacoesProcessar + $pontuacoesBrindesUsados;
                        }

                        while ($podeContinuar) {
                            $pontuacoesPendentesUso = $this
                                ->Pontuacoes
                                ->getPontuacoesPendentesForUsuario(
                                    $usuario->id,
                                    $clientes_ids,
                                    10,
                                    $ultimoId
                                );

                            $maximoContador = sizeof($pontuacoesPendentesUso->toArray());

                            $contador = 0;
                            foreach ($pontuacoesPendentesUso as $key => $pontuacao) {
                                if ($pontuacoesProcessar >= 0) {
                                    if ($pontuacoesProcessar >= $pontuacao->quantidade_gotas) {
                                        array_push(
                                            $pontuacoesPendentesUsoListaSave,
                                            [
                                                'id' => $pontuacao->id,
                                                'utilizado' => 2
                                            ]
                                        );
                                    } else {
                                        array_push(
                                            $pontuacoesPendentesUsoListaSave,
                                            [
                                                'id' => $pontuacao->id,
                                                'utilizado' => 1
                                            ]
                                        );
                                    }
                                }
                                $pontuacoesProcessar = $pontuacoesProcessar - $pontuacao->quantidade_gotas;

                                $ultimoId = $pontuacao->id;

                                $contador = $contador + 1;

                                if ($contador == $maximoContador) {
                                    $ultimoId = $pontuacao->id + 1;
                                }

                                if ($pontuacoesProcessar <= 0) {
                                    $podeContinuar = false;
                                    break;
                                }
                            }
                        }

                        // Atualiza todos os pontos do usuário

                        $this->Pontuacoes->updatePendingPontuacoesForUsuario($pontuacoesPendentesUsoListaSave);

                        // ---------- Fim de atualiza pontos à serem debitados ----------

                        // Diminuir saldo de pontos do usuário
                        $pontuacaoDebitar = $this->Pontuacoes->addPontuacoesBrindesForUsuario(
                            $cliente->id,
                            $usuario->id,
                            $brinde_habilitado->id,
                            $brinde_habilitado->brinde_habilitado_preco_atual->preco * $quantidade
                        );

                    }

                    // Se for venda avulsa, considera que tem que debitar pontos
                    if ($usuario->tipo_perfil == Configure::read('profileTypes')['DummyUserProfileType']) {
                        $pontuacaoDebitar = true;
                    }

                    if ($pontuacaoDebitar) {
                        // Emitir Cupom e retornar

                        $cupom = $this->Cupons->addCuponsBrindesForUsuario(
                            $brinde_habilitado,
                            $usuario->id,
                            $quantidade
                        );

                         // vincula item resgatado ao cliente final

                        $brinde_usuario = $this->UsuariosHasBrindes->addUsuarioHasBrindes(
                            $usuario->id,
                            $brinde_habilitado->id,
                            $quantidade,
                            $brinde_habilitado->brinde_habilitado_preco_atual->preco,
                            $cupom->id
                        );

                        if ($cupom) {
                            $status = 'success';
                            $cupom->data = (new \DateTime($cupom->data))->format('d/m/Y H:i:s');
                            $ticket = $cupom;
                            $message = null;
                        } else {
                            $status = 'error';
                            $message = "Houve um erro na geração do Ticket. Informe ao suporte.";
                        }
                    }
                } else {
                    $message = "Usuário possui saldo insuficiente. Não foi possível realizar a transação.";
                }
            }

            $message = $message;
            $ticket = $ticket;
            $status = $status;
            $cliente = $cliente;
            $usuario = $usuario;
            $tempo = $brinde_habilitado->brinde->tempo_rti_shower;
        }

        $arraySet = [
            'message',
            'ticket',
            'status',
            'cliente',
            'usuario',
            'tempo'
        ];

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);
    }

    /**
     * Web-service para reimprimir um ticket
     *
     * @return json object
     */
    public function reimprimirBrindeAjax()
    {
        $result = null;
        $ticket = null;
        $message = null;
        $status = 'error';
        $cupom = null;
        $cliente = 0;

        if ($this->request->is(['post'])) {
            $data = $this->request->getData();

            // validação de senha do usuário
            $usuario = $this->Usuarios->getUsuarioById($data['usuarios_id']);

            $senhaValida = false;
            if ((new DefaultPasswordHasher)->check($data['current_password'], $usuario->senha)) {
                $senhaValida = true;
            }

            if ($senhaValida == false) {
                $message = 'Senha incorreta para usuário. Nâo foi possível resgatar o brinde';
            } else {
                $cliente = $this->Clientes->getClienteById($data['clientes_id']);

                $cupom = $this->Cupons->getCupomToReprint(
                    $data['id'],
                    $data['clientes_has_brindes_habilitados_id'],
                    $data['clientes_id'],
                    $data['usuarios_id'],
                    $data['data']
                );

                $cupom->data = $cupom->data->format('d/m/Y H:i:s');

                if ($cupom) {
                    $status = 'success';
                    $ticket = $cupom;
                    $message = null;
                } else {
                    $status = 'error';
                    $message = "Houve um erro na geração do Ticket. Informe ao suporte.";
                }
            }

            $message = $message;
            $ticket = $ticket;
            $status = $status;
            $cliente = $cliente;
            $usuario = $usuario;
            $tempo = isset($cupom) ? $cupom->tempo_banho : null;
        }

        $arraySet = [
            'message',
            'ticket',
            'status',
            'cliente',
            'usuario',
            'tempo'
        ];

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);
    }

    /**
     * Obtem cupom pelo código emitido
     * @return json_encode $data Cupom emitido
     */
    public function getCupomPorCodigo()
    {
        try {
            $result = null;

            if ($this->request->is(['post'])) {
                $data = $this->request->getData();

                $cupom_emitido = $data['cupom_emitido'];

                // TODO: a emissão nova irá utilizar este método
                $cupons = $this->Cupons->getCuponsByCupomEmitido($cupom_emitido);
                $cupons = $cupons->toArray();

                $cupons_array = [];
                foreach ($cupons as $key => $cupom) {
                    $cupom['data'] = $cupom['data']->format('d/m/Y H:i:s');

                    $cupons_array[] = $cupom;
                }

                $result = $this->processarCupom($cupons_array);

                $status = isset($result['status']) ? $result['status'] : null;
                $message = isset($result['message']) ? $result['message'] : null;
                $data = isset($result['data']) ? $result['data'] : null;
            }

            $arraySet = [
                'status',
                'message',
                'data'
            ];

            $this->set(compact($arraySet));
            $this->set("_serialize", $arraySet);

        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao obter cupom por código: {0} em: {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);
        }
    }

    /**
     * Resgata um cupom
     * @return json_encode $data Cupom emitido
     */
    public function resgatarCupomAjax()
    {
        try {
            $status = false;
            $error = __("{0} {1}", Configure::read('messageRedeemCouponError'), Configure::read('callSupport'));

            if ($this->request->is(['post'])) {

                $data = $this->request->getData();

                $cupom_emitido = $data['cupom_emitido'];
                $unidade_funcionario_id = $data['unidade_funcionario_id'];

                $cupons = $this->Cupons->getCuponsByCupomEmitido($cupom_emitido);

                if (!$cupons) {
                    $status = false;
                    $error = __("{0}", Configure::read('messageRecordNotFound'));

                } else {
                    foreach ($cupons->toArray() as $key => $cupom) {
                        $cliente_has_brinde_estoque = $this->ClientesHasBrindesEstoque->getEstoqueForBrindeId($cupom->clientes_has_brindes_habilitados_id);

                        $estoque = $this->ClientesHasBrindesEstoque->addEstoqueForBrindeId(
                            $cupom->clientes_has_brindes_habilitados_id,
                            $cupom->usuarios_id,
                            $cupom->quantidade,
                            (int)Configure::read('stockOperationTypes')['sellTypeGift']
                        );

                        // diminuiu estoque, considera o item do cupom como resgatado
                        if ($estoque) {
                            $cupom_save = $this->Cupons->setCupomResgatadoUsado($cupom->id);

                            // adiciona novo registro de pontuação

                            $pontuacao = $this->Pontuacoes->addPontuacoesBrindesForUsuario(
                                $cupom->clientes_id,
                                $cupom->usuarios_id,
                                $cupom->clientes_has_brindes_habilitados_id,
                                $cupom->valor_pago
                            );
                        }
                    }
                }

                // se chegou até aqui, gravou com sucesso no banco
                $status = true;
                $error = null;
            }

            $arraySet = [
                'status',
                'error'
            ];

            $this->set(compact($arraySet));
            $this->set("_serialize", $arraySet);

        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao resgatar cupom: {0} em: {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);
        }
    }

    /**
     * ------------------------------------------------------------------
     * Métodos de API
     * ------------------------------------------------------------------
     */

    #region Métodos de API

    /**
     * CuponsController::efetuarBaixaCupomAPI
     *
     * Efetua a baixa do brinde de usuário
     *
     * @params $data["cupom_emitido"] Código do cupom para pesquisa
     * @params $data["confirmar"] Confirma se é para efetuar baixa
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 01/08/2018
     *
     * @return json objeto resultado
     */
    public function efetuarBaixaCupomAPI()
    {
        try {

            if ($this->request->is(['post'])) {

                $data = $this->request->getData();
                $confirmar = !empty($data["confirmar"]) ? (bool)$data["confirmar"] : false;
                $cupomEmitido = !empty($data["cupom_emitido"]) ? $data["cupom_emitido"] : "";

                // Validação de funcionário logado
                $funcionarioId = $this->Auth->user()["id"];

                $tipoPerfil = $this->Auth->user()["tipo_perfil"];
                $funcionario["nome"] = $this->Auth->user()["nome"];

                $isFuncionario = false;

                if ($tipoPerfil == Configure::read("profileTypes")["WorkerProfileType"]
                    || $tipoPerfil == Configure::read("profileTypes")["DummyWorkerProfileType"]) {
                    $isFuncionario = true;
                }

                if (!$isFuncionario) {
                    $mensagem = array(
                        "status" => 0,
                        "message" => Configure::read("messageOperationFailureDuringProcessing"),
                        "errors" => array(Configure::read("userNotAllowedToExecuteFunction"))
                    );

                    $arraySet = array("mensagem");

                    $this->set(compact($arraySet));
                    $this->set("_serialize", $arraySet);

                    return;
                }

                $clientesUsuariosIds = $this->ClientesHasUsuarios->getAllClientesIdsByUsuariosId($this->Auth->user()["id"], $tipoPerfil);

                $clienteId = 0;
                if (sizeof($clientesUsuariosIds) > 0) {
                    $clienteId = $clientesUsuariosIds[0];
                }

                $todasUnidadesRedesQuery = $this->RedesHasClientes->getAllRedesHasClientesIdsByClientesId($clienteId);

                $todasUnidadesIds = array();

                foreach ($todasUnidadesRedesQuery as $value) {
                    $todasUnidadesIds[] = $value["clientes_id"];
                }

                if ($cupomEmitido == "" || strlen($cupomEmitido) < 14) {

                    $errors = array("É preciso informar o código do cupom para continuar!");

                    if (strlen($cupomEmitido) <= 14) {
                        $errors[] = "O código do cupom deve ter 14 dígitos!";
                    }

                    $mensagem = array(
                        "status" => 0,
                        "message" => Configure::read("messageOperationFailureDuringProcessing"),
                        "errors" => $errors
                    );

                    $arraySet = array("mensagem");

                    $this->set(compact($arraySet));
                    $this->set("_serialize", $arraySet);

                    return;
                }

                $cupons = $this->Cupons->getCuponsByCupomEmitido($cupomEmitido, $todasUnidadesIds);

                // Verifica se este cupom já foi usado
                $somaTotal = 0;
                $dadosCupons = array();

                $verificado = false;
                $usado = false;
                foreach ($cupons->toArray() as $cupom) {

                    $dadoCupom = array();

                    $somaTotal += (float)$cupom["valor_pago"];

                    $dadoCupom["nome_brinde"] = $cupom["clientes_has_brindes_habilitado"]["brinde"]["nome"];
                    $dadoCupom["quantidade"] = $cupom["quantidade"];
                    $dadoCupom["preco_brinde"] = (float)$cupom["valor_pago"];
                    // imagem brinde
                    $dadoCupom["nome_img_completo"] = $cupom["clientes_has_brindes_habilitado"]["brinde"]["nome_img_completo"];
                    $dadoCupom["data_resgate"] = !empty($cupom["data"]) ? $cupom["data"]->format("d/m/Y H:i:s") : null;
                    $dadosCupons[] = $dadoCupom;

                    if ($cupom["usado"]) {
                        $usado = true;
                        break;
                    }
                    $verificado = true;
                }

                if ($usado) {
                    $mensagem = array(
                        "status" => 0,
                        "message" => Configure::read("messageWarningDefault"),
                        "errors" => array("Este cupom já foi utilizado pelo usuário!")
                    );
                    $resultado = array(
                        "recibo_baixa_cupons" => $dadosCupons
                    );

                    $arraySet = array("mensagem", "resultado");

                    $this->set(compact($arraySet));
                    $this->set("_serialize", $arraySet);

                    return;
                }

                // Se não confirmar, exibir somente os dados de cupom de resgate e perguntar se é o cupom
                if (!$confirmar) {

                    $mensagem = array(
                        "status" => 0,
                        "message" => Configure::read("messageWarningDefault"),
                        "errors" => array("Deseja confirmar o resgate dos brindes à seguir?")
                    );
                    $resultado = array(
                        "recibo_baixa_cupons" => $dadosCupons
                    );

                    $arraySet = array("mensagem", "resultado");

                    $this->set(compact($arraySet));
                    $this->set("_serialize", $arraySet);

                    return;

                }

                if (!$cupons) {
                    // Avisa erro se não for encontrado. Motivos podem ser:
                    // Cupom já foi resgatado
                    // Cupom pertence a outra rede
                    $mensagem = array(
                        "status" => 0,
                        "message" => Configure::read("messageOperationFailureDuringProcessing"),
                        "errors" => array(Configure::read('messageRecordNotFound'))
                    );

                    $arraySet = array("mensagem");

                    $this->set(compact($arraySet));
                    $this->set("_serialize", $arraySet);

                    return;

                } else {
                    foreach ($cupons->toArray() as $key => $cupom) {


                        $cliente_has_brinde_estoque = $this->ClientesHasBrindesEstoque->getEstoqueForBrindeId($cupom->clientes_has_brindes_habilitados_id);

                        $estoque = $this->ClientesHasBrindesEstoque->addEstoqueForBrindeId(
                            $cupom->clientes_has_brindes_habilitados_id,
                            $cupom->usuarios_id,
                            $cupom->quantidade,
                            (int)Configure::read('stockOperationTypes')['sellTypeGift']
                        );

                        // diminuiu estoque, considera o item do cupom como resgatado
                        if ($estoque) {
                            $cupom_save = $this->Cupons->setCupomResgatadoUsado($cupom->id);

                            // adiciona novo registro de pontuação

                            $pontuacao = $this->Pontuacoes->addPontuacoesBrindesForUsuario(
                                $cupom->clientes_id,
                                $cupom->usuarios_id,
                                $cupom->clientes_has_brindes_habilitados_id,
                                $cupom->valor_pago,
                                $this->Auth->user()["id"]
                            );
                        }
                    }
                }

                $mensagem = array(
                    "status" => 1,
                    "message" => __("{0} {1}", Configure::read("messageProcessingCompleted"), Configure::read("messageRedeemCouponRedeemed")),
                    "errors" => array()
                );
                $resultado = array(
                    "soma_gotas" => $somaTotal,
                    "recibo" => $dadosCupons,
                    "funcionario" => $funcionario
                );

                $arraySet = array(
                    "mensagem",
                    "resultado"
                );

                $this->set(compact($arraySet));
                $this->set("_serialize", $arraySet);

                return;
            }

        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $messageString = __("Erro durante resgate do cupom!");
            $messageStringDebug = __("{0} - {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $messageString, $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write("error", $messageStringDebug);
            Log::write("error", $trace);

            $mensagem = array('status' => false, 'message' => $messageString, 'errors' => $trace);
        }
    }

    /**
     * CuponsController::resgatarCupomAPI
     *
     * Serviço REST que resgata um cupom de brinde
     *
     * @param int $clientes_id
     * @param int $brindes_id
     * @param int $quantidade
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 29/06/2018
     *
     * @return json object
     */
    public function resgatarCupomAPI()
    {
        $mensagem = array();
        $arraySet = array();

        if ($this->request->is(['post'])) {
            $data = $this->request->getData();

            Log::write("info", $data);
            // Validação de dados
            $errors = array();
            if (empty($data["brindes_id"])) {
                $errors[] = __("É necessário selecionar um brinde para resgatar pontos!");
            }
            if (empty($data["clientes_id"])) {
                $errors[] = __("É necessário selecionar uma unidade de atendimento para resgatar pontos!");
            }

            if (sizeof($errors) > 0) {

                $mensagem = array("status" => false, "message" => Configure::read("messageOperationFailureDuringProcessing"), "errors" => $errors);

                $arraySet = array("mensagem");
                $this->set(compact($arraySet));
                $this->set("_serialize", $arraySet);

                return;
            }

            $brindesId = $data["brindes_id"];
            $usuario = $this->Auth->user();
            $usuario = $this->Usuarios->getUsuarioById($usuario['id']);
            $usuariosId = $usuario["id"];
            $clientesId = $data["clientes_id"];
            $quantidade = $data["quantidade"];
            $funcionario = $this->Usuarios->getUsuariosByProfileType(Configure::read("profileTypes")["DummyWorkerProfileType"], 1);

            $retorno = $this->_trataCompraCupom($brindesId, $usuariosId, $clientesId, $quantidade, $funcionario["id"], false, "", true);

            $arraySet = $retorno["arraySet"];
            $mensagem = $retorno["mensagem"];
            $ticket = $retorno["ticket"];
            $cliente = $retorno["cliente"];
            $usuario = $retorno["usuario"];
            $tempo = $retorno["tempo"];
            $tipo_emissao_codigo_barras = $retorno["tipo_emissao_codigo_barras"];
        }

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);
    }

    #endregion

    /**
     * CuponsController::resgatarCupomAPI
     *
     * Serviço REST que resgata um cupom de brinde
     *
     * @param int $clientes_id
     * @param int $brindes_id
     * @param int $quantidade
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 29/06/2018
     *
     * @return json object
     */
    public function resgatarCupomBackupAPI()
    {
        $result = null;
        $ticket = null;
        $message = [];
        $cupom = null;

        if ($this->request->is(['post'])) {
            $data = $this->request->getData();

            // Variávies de post
            $cliente = $this->Clientes->getClienteById($data['clientes_id']);
            $brindesId = $data["brindes_id"];
            $quantidade = isset($data["quantidade"]) ? $data["quantidade"] : 1;
            $quantidade = $quantidade < 1 ? 1 : $quantidade;

            // pega id de todos os clientes que estão ligados à uma rede
            $redesHasClientes = $this->RedesHasClientes->getRedesHasClientesByClientesId($cliente->id);
            $rede = $redesHasClientes->rede;
            $clientesIds = $this->RedesHasClientes->getClientesIdsFromRedesHasClientes($rede["id"]);

            $brindeSelecionado = $this->ClientesHasBrindesHabilitados->getBrindeHabilitadoByBrindesIdClientesId($brindesId, $cliente["id"]);

            // Se não encontrado, retorna vazio.
            if (empty($brindeSelecionado)) {

                $mensagem = array(
                    "status" => false,
                    "message" => __("A unidade de atendimento informada não possui o brinde desejado!"),
                    "errors" => array()
                );
                $arraySet = array(
                    "mensagem"
                );

                $this->set(compact($arraySet));
                $this->set("_serialize", $arraySet);

                return;
            } else if ($brindeSelecionado["genero_brindes_cliente"]["tipo_principal_codigo_brinde"] <= 4 && $quantidade > 1) {
                $mensagem = array(
                    "status" => false,
                    "message" => __("Para Brindes do tipo banho, a quantidade deve ser 1!"),
                    "errors" => array()
                );

                $arraySet = array(
                    "mensagem"
                );

                $this->set(compact($arraySet));
                $this->set("_serialize", $arraySet);

                return;

            }

            $usuario = $this->Auth->user();

            $usuario = $this->Usuarios->getUsuarioById($usuario['id']);

            $usuario['pontuacoes']
                = $this->Pontuacoes->getSumPontuacoesOfUsuario(
                $usuario['id'],
                $rede["id"],
                $clientesIds
            );

            // Se o usuário tiver pontuações suficientes
            if ($usuario->pontuacoes >= $brindeSelecionado->brinde_habilitado_preco_atual->preco * $quantidade) {
                // verificar se cliente possui usuario em sua lista de usuários. se não tiver, cadastrar

                $clientes_has_usuarios_conditions = [];

                array_push($clientes_has_usuarios_conditions, ['ClientesHasUsuarios.usuarios_id' => $usuario['id']]);
                array_push($clientes_has_usuarios_conditions, ['ClientesHasUsuarios.clientes_id IN' => $clientesIds]);

                if ($rede->permite_consumo_gotas_funcionarios) {
                    array_push($clientes_has_usuarios_conditions, ['ClientesHasUsuarios.tipo_perfil >= ' => Configure::read('profileTypes')['AdminNetworkProfileType']]);
                    array_push($clientes_has_usuarios_conditions, ['ClientesHasUsuarios.tipo_perfil <= ' => Configure::read('profileTypes')['UserProfileType']]);
                } else {
                    array_push($clientes_has_usuarios_conditions, ['ClientesHasUsuarios.tipo_perfil' => Configure::read('profileTypes')['UserProfileType']]);
                }

                $clientePossuiUsuario = $this->ClientesHasUsuarios->findClienteHasUsuario($clientes_has_usuarios_conditions);

                if (is_null($clientePossuiUsuario)) {
                    $this->ClientesHasUsuarios->saveClienteHasUsuario($cliente["id"], $usuario["id"], $usuario["tipo_perfil"]);
                }

                // ------------------- Atualiza pontos à serem debitados -------------------

                /*
                 * Se há pontuação à debitar, devo verificar quais são as
                 * pontuações do usuário que serão utilizadas, para notificar
                 * quantos pontos ele possui que estão prestes à vencer
                 */

                $pontuacoesProcessar = $brindeSelecionado["brinde_habilitado_preco_atual"]["preco"] * $quantidade;

                $podeContinuar = true;
                $pontuacoesPendentesUsoListaSave = [];

                // Obter pontos não utilizados totalmente
                // verifica se tem algum pendente para continuar o cálculo sobre ele

                $pontuacaoPendenteUso
                    = $this->Pontuacoes->getPontuacoesPendentesForUsuario(
                    $usuario->id,
                    $clientesIds,
                    1,
                    null
                );

                if ($pontuacaoPendenteUso) {
                    $ultimoId = $pontuacaoPendenteUso->id;
                } else {
                    $ultimoId = null;
                }

                if (!is_null($ultimoId)) {
                    // soma de pontos de todos os brindes usados
                    $pontuacoesBrindesUsados = $this
                        ->Pontuacoes
                        ->getSumPontuacoesPendingToUsageByUsuario(
                            $usuario->id,
                            $clientesIds
                        );

                    $pontuacoesProcessar = $pontuacoesProcessar + $pontuacoesBrindesUsados;
                }

                while ($podeContinuar) {
                    $pontuacoesPendentesUso = $this
                        ->Pontuacoes
                        ->getPontuacoesPendentesForUsuario(
                            $usuario->id,
                            $clientesIds,
                            10,
                            $ultimoId
                        );

                    $maximoContador = sizeof($pontuacoesPendentesUso->toArray());

                    $contador = 0;
                    foreach ($pontuacoesPendentesUso as $key => $pontuacao) {
                        if ($pontuacoesProcessar >= 0) {
                            if ($pontuacoesProcessar >= $pontuacao->quantidade_gotas) {
                                array_push(
                                    $pontuacoesPendentesUsoListaSave,
                                    [
                                        'id' => $pontuacao->id,
                                        'utilizado' => 2
                                    ]
                                );
                            } else {
                                array_push(
                                    $pontuacoesPendentesUsoListaSave,
                                    [
                                        'id' => $pontuacao->id,
                                        'utilizado' => 1
                                    ]
                                );
                            }
                        }
                        $pontuacoesProcessar = $pontuacoesProcessar - $pontuacao->quantidade_gotas;

                        $ultimoId = $pontuacao->id;

                        $contador = $contador + 1;

                        if ($contador == $maximoContador) {
                            $ultimoId = $pontuacao->id + 1;
                        }

                        if ($pontuacoesProcessar <= 0) {
                            $podeContinuar = false;
                            break;
                        }
                    }
                }

                // Atualiza todos os pontos do usuário

                $this->Pontuacoes->updatePendingPontuacoesForUsuario($pontuacoesPendentesUsoListaSave);

                // ---------- Fim de atualiza pontos à serem debitados ----------

                // Diminuir saldo de pontos do usuário
                $pontuacaoDebitar = $this->Pontuacoes->addPontuacoesBrindesForUsuario(
                    $cliente->id,
                    $usuario->id,
                    $brindeSelecionado["id"],
                    $brindeSelecionado["brinde_habilitado_preco_atual"]["preco"] * $quantidade
                );

                if ($pontuacaoDebitar) {
                    // Emitir Cupom e retornar

                    $cupom = $this->Cupons->addCupomForUsuario(
                        $brindeSelecionado["id"],
                        $cliente->id,
                        $usuario->id,
                        $brindeSelecionado["brinde_habilitado_preco_atual"]["preco"] * $quantidade,
                        $quantidade
                    );

                     // vincula item resgatado ao cliente final

                    $brindeUsuario = $this->UsuariosHasBrindes->addUsuarioHasBrindes(
                        $usuario->id,
                        $brindeSelecionado["id"],
                        $quantidade,
                        $brindeSelecionado["brinde_habilitado_preco_atual"]["preco"],
                        $cupom->id
                    );

                    if ($cupom) {
                        $status = 'success';
                        $cupom->data = (new \DateTime($cupom->data))->format('d/m/Y H:i:s');
                        $ticket = $cupom;
                        $message = null;
                    } else {
                        $status = 'error';
                        $message = "Houve um erro na geração do Ticket. Informe ao suporte.";
                    }
                } else {
                    $mensagem = array(
                        'status' => false,
                        'message' => "Usuário possui saldo insuficiente. Não foi possível realizar a transação."
                    );
                }
            } else {
                $mensagem = array(
                    'status' => false,
                    'message' => "Usuário possui saldo insuficiente. Não foi possível realizar a transação."
                );
            }
            $ticket = $ticket;
            $status = $status;
            // TODO: Ajustar quais colunas serão obtidas do cliente
            $cliente = $cliente;
            $usuario = $usuario;
            $tempo = $brindeSelecionado->brinde->tempo_rti_shower;
            $tipoEmissaoCodigoBarras = $brindeSelecionado["tipo_codigo_barras"];
            $isBrindeSmartShower = $brindeSelecionado["genero_brindes_cliente"]["tipo_principal_codigo_brinde"] <= 4;
        }

        $arraySet = [
            'mensagem',
            'ticket',
            'status',
            'cliente',
            'usuario',
            'tempo',
            'tipoEmissaoCodigoBarras',
        ];

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);
    }

    /**
     * Web-service to print a shower ticket
     *
     * @return json object
     */
    public function resgatarCupomSmartShowerAPI()
    {
        $result = null;
        $ticket = null;
        $message = [];
        $cupom = null;

        if ($this->request->is(['post'])) {
            $data = $this->request->getData();

            $cliente = $this->Clientes->getClienteById($data['clientes_id']);

            $redesHasClientes = $this->RedesHasClientes->getRedesHasClientesByClientesId($cliente->id);

            $rede = $redesHasClientes->rede;

            // pega id de todos os clientes que estão ligados à uma rede

            $redes_has_clientes_query = $this->RedesHasClientes->getRedesHasClientesByRedesId($rede->id);

            $clientes_ids = [];

            foreach ($redes_has_clientes_query as $key => $value) {
                $clientes_ids[] = $value['clientes_id'];
            }

            $array = [];

            $clientes_id = $array;

            $brinde_habilitado = $this->ClientesHasBrindesHabilitados->getBrindeHabilitadoById($data['brindes_id']);

            $usuario = $this->Auth->user();

            $usuario = $this->Usuarios->getUsuarioById($usuario['id']);

            $usuario['pontuacoes']
                = $this->Pontuacoes->getSumPontuacoesOfUsuario(
                $usuario['id'],
                $rede["id"],
                $clientes_ids
            );

            // Se o usuário tiver pontuações suficientes
            if ($usuario->pontuacoes >= $brinde_habilitado->brinde_habilitado_preco_atual->preco) {
                // verificar se cliente possui usuario em sua lista de usuários. se não tiver, cadastrar

                $clientes_has_usuarios_conditions = [];

                array_push($clientes_has_usuarios_conditions, ['ClientesHasUsuarios.usuarios_id' => $usuario['id']]);
                array_push($clientes_has_usuarios_conditions, ['ClientesHasUsuarios.clientes_id IN' => $clientes_ids]);

                if ($rede->permite_consumo_gotas_funcionarios) {
                    array_push($clientes_has_usuarios_conditions, ['ClientesHasUsuarios.tipo_perfil >= ' => Configure::read('profileTypes')['AdminNetworkProfileType']]);
                    array_push($clientes_has_usuarios_conditions, ['ClientesHasUsuarios.tipo_perfil <= ' => Configure::read('profileTypes')['UserProfileType']]);
                } else {
                    array_push($clientes_has_usuarios_conditions, ['ClientesHasUsuarios.tipo_perfil' => Configure::read('profileTypes')['UserProfileType']]);
                }

                $clientePossuiUsuario = $this->ClientesHasUsuarios->findClienteHasUsuario($clientes_has_usuarios_conditions);

                if (is_null($clientePossuiUsuario)) {
                    $this->ClientesHasUsuarios->addNewClienteHasUsuario($cliente->matriz_id, $cliente->id, $usuario->id);
                }

                // ------------------- Atualiza pontos à serem debitados -------------------

                /*
                 * Se há pontuação à debitar, devo verificar quais são as
                 * pontuações do usuário que serão utilizadas, para notificar
                 * quantos pontos ele possui que estão prestes à vencer
                 */

                $pontuacoesProcessar = $brinde_habilitado->brinde_habilitado_preco_atual->preco;

                $podeContinuar = true;
                $pontuacoesPendentesUsoListaSave = [];

                // Obter pontos não utilizados totalmente
                // verifica se tem algum pendente para continuar o cálculo sobre ele

                $pontuacaoPendenteUso
                    = $this->Pontuacoes->getPontuacoesPendentesForUsuario(
                    $usuario->id,
                    $clientes_ids,
                    1,
                    null
                );

                if ($pontuacaoPendenteUso) {
                    $ultimoId = $pontuacaoPendenteUso->id;
                } else {
                    $ultimoId = null;
                }

                if (!is_null($ultimoId)) {
                    // soma de pontos de todos os brindes usados
                    $pontuacoesBrindesUsados = $this
                        ->Pontuacoes
                        ->getSumPontuacoesPendingToUsageByUsuario(
                            $usuario->id,
                            $clientes_ids
                        );

                    $pontuacoesProcessar = $pontuacoesProcessar + $pontuacoesBrindesUsados;
                }

                while ($podeContinuar) {
                    $pontuacoesPendentesUso = $this
                        ->Pontuacoes
                        ->getPontuacoesPendentesForUsuario(
                            $usuario->id,
                            $clientes_ids,
                            10,
                            $ultimoId
                        );

                    $maximoContador = sizeof($pontuacoesPendentesUso->toArray());

                    $contador = 0;
                    foreach ($pontuacoesPendentesUso as $key => $pontuacao) {
                        if ($pontuacoesProcessar >= 0) {
                            if ($pontuacoesProcessar >= $pontuacao->quantidade_gotas) {
                                array_push(
                                    $pontuacoesPendentesUsoListaSave,
                                    [
                                        'id' => $pontuacao->id,
                                        'utilizado' => 2
                                    ]
                                );
                            } else {
                                array_push(
                                    $pontuacoesPendentesUsoListaSave,
                                    [
                                        'id' => $pontuacao->id,
                                        'utilizado' => 1
                                    ]
                                );
                            }
                        }
                        $pontuacoesProcessar = $pontuacoesProcessar - $pontuacao->quantidade_gotas;

                        $ultimoId = $pontuacao->id;

                        $contador = $contador + 1;

                        if ($contador == $maximoContador) {
                            $ultimoId = $pontuacao->id + 1;
                        }

                        if ($pontuacoesProcessar <= 0) {
                            $podeContinuar = false;
                            break;
                        }
                    }
                }

                // Atualiza todos os pontos do usuário

                $this->Pontuacoes->updatePendingPontuacoesForUsuario($pontuacoesPendentesUsoListaSave);

                // ---------- Fim de atualiza pontos à serem debitados ----------

                // Diminuir saldo de pontos do usuário
                $pontuacaoDebitar = $this->Pontuacoes->addPontuacoesBrindesForUsuario(
                    $cliente->id,
                    $usuario->id,
                    $brinde_habilitado->id,
                    $brinde_habilitado->brinde_habilitado_preco_atual->preco
                );

                if ($pontuacaoDebitar) {
                    // Emitir Cupom e retornar

                    // 1 - Masculino, 2 - Masculino PNE, 3 - Feminino, 4 - Feminino PNE
                    // PNE = Portador de Necessidades Especiais
                    $tipo_banho = null;

                    // Masculino
                    if ($data['sexo'] == 1) {
                        $tipo_banho = 1;
                    } else {
                        // Feminino
                        $tipo_banho = 3;
                    }
                    if ($data['necessidades_especiais']) {
                        $tipo_banho = $tipo_banho + 1;
                    }

                    $cupom = $this->Cupons->addCupomForUsuario(
                        $brinde_habilitado->id,
                        $cliente->id,
                        $usuario->id,
                        $tipo_banho,
                        $brinde_habilitado->brinde->tempo_rti_shower,
                        $brinde_habilitado->brinde_habilitado_preco_atual->preco
                    );

                    // vincula item resgatado ao cliente final

                    $brinde_usuario = $this->UsuariosHasBrindes->addUsuarioHasBrindes(
                        $usuario->id,
                        $brinde_habilitado->id,
                        1,
                        $brinde_habilitado->brinde_habilitado_preco_atual->preco,
                        $cupom->id
                    );

                    if ($cupom) {
                        $mensagem = ['status' => true, 'message' => "Cupom resgatado com sucesso!"];
                        $cupom->data = (new \DateTime($cupom->data))->format('d/m/Y H:i:s');
                        $ticket = $cupom;
                    } else {
                        $mensagem = ['status' => false, 'message' => "Houve um erro na geração do Ticket. Informe ao suporte."];
                    }
                } else {
                    $mensagem = ['status' => false, 'message' => "Usuário possui saldo insuficiente. Não foi possível realizar a transação."];
                }
            } else {
                $mensagem = ['status' => false, 'message' => "Usuário possui saldo insuficiente. Não foi possível realizar a transação."];
            }

            $ticket = $ticket;
            $cliente = $cliente;
            $usuario = $usuario;
            $tempo = $brinde_habilitado->brinde->tempo_rti_shower;
        }

        $arraySet = [
            'mensagem',
            'ticket',
            'status',
            'cliente',
            'usuario',
            'tempo'
        ];

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);
    }

    /**
     * Web-service para imprimir brinde comum
     *
     * @return json object
     */
    public function resgatarCupomComumAPI()
    {
        $result = null;
        $ticket = null;
        $message = [];
        $cupom = null;

        if ($this->request->is(['post'])) {
            $data = $this->request->getData();

            $cliente = $this->Clientes->getClienteById($data['clientes_id']);

            $redesHasClientes = $this->RedesHasClientes->getRedesHasClientesByClientesId($cliente->id);

            $rede = $redesHasClientes->rede;

            // pega id de todos os clientes que estão ligados à uma rede

            $redes_has_clientes_query = $this->RedesHasClientes->getRedesHasClientesByRedesId($rede->id);

            $clientes_ids = [];

            foreach ($redes_has_clientes_query as $key => $value) {
                $clientes_ids[] = $value['clientes_id'];
            }

            $array = [];

            $clientes_id = $array;

            $brinde_habilitado = $this->ClientesHasBrindesHabilitados->getBrindeHabilitadoById(
                $data['brindes_id']
            );

            $quantidade = $data['quantidade'];

            $usuario = $this->Auth->user();

            $usuario = $this->Usuarios->getUsuarioById($usuario['id']);

            $usuario['pontuacoes'] = $this->Pontuacoes->getSumPontuacoesOfUsuario(
                $usuario['id'],
                $rede["id"],
                $clientes_ids
            );

            // Se o usuário tiver pontuações suficientes
            if (($usuario->pontuacoes >= ($brinde_habilitado->brinde_habilitado_preco_atual->preco * $quantidade))) {

                // verificar se cliente possui usuario em sua lista de usuários. se não tiver, cadastrar

                $clientes_has_usuarios_conditions = [];

                array_push($clientes_has_usuarios_conditions, ['ClientesHasUsuarios.usuarios_id' => $usuario['id']]);
                array_push($clientes_has_usuarios_conditions, ['ClientesHasUsuarios.clientes_id IN' => $clientes_ids]);

                if ($rede->permite_consumo_gotas_funcionarios) {
                    array_push($clientes_has_usuarios_conditions, ['ClientesHasUsuarios.tipo_perfil >= ' => Configure::read('profileTypes')['AdminNetworkProfileType']]);
                    array_push($clientes_has_usuarios_conditions, ['ClientesHasUsuarios.tipo_perfil <= ' => Configure::read('profileTypes')['UserProfileType']]);
                } else {
                    array_push($clientes_has_usuarios_conditions, ['ClientesHasUsuarios.tipo_perfil' => Configure::read('profileTypes')['UserProfileType']]);
                }

                $clientePossuiUsuario = $this->ClientesHasUsuarios->findClienteHasUsuario($clientes_has_usuarios_conditions);

                if (is_null($clientePossuiUsuario)) {
                    $this->ClientesHasUsuarios->addNewClienteHasUsuario($cliente->matriz_id, $cliente->id, $usuario->id);
                }

                // ------------------- Atualiza pontos à serem debitados -------------------

                /*
                 * Se há pontuação à debitar, devo verificar quais são as
                 * pontuações do usuário que serão utilizadas, para notificar
                 * quantos pontos ele possui que estão prestes à vencer
                 */

                $pontuacoesProcessar = $brinde_habilitado->brinde_habilitado_preco_atual->preco * $quantidade;

                $podeContinuar = true;
                $pontuacoesPendentesUsoListaSave = [];

                    // Obter pontos não utilizados totalmente
                    // verifica se tem algum pendente para continuar o cálculo sobre ele

                $pontuacaoPendenteUso = $this
                    ->Pontuacoes
                    ->getPontuacoesPendentesForUsuario(
                        $usuario->id,
                        $clientes_ids,
                        1,
                        null
                    );

                if ($pontuacaoPendenteUso) {
                    $ultimoId = $pontuacaoPendenteUso->id;
                } else {
                    $ultimoId = null;
                }

                if (!is_null($ultimoId)) {
                        // soma de pontos de todos os brindes usados
                    $pontuacoesBrindesUsados = $this
                        ->Pontuacoes
                        ->getSumPontuacoesPendingToUsageByUsuario(
                            $usuario->id,
                            $clientes_ids
                        );

                    $pontuacoesProcessar = $pontuacoesProcessar + $pontuacoesBrindesUsados;
                }

                while ($podeContinuar) {
                    $pontuacoesPendentesUso = $this
                        ->Pontuacoes
                        ->getPontuacoesPendentesForUsuario(
                            $usuario->id,
                            $clientes_ids,
                            10,
                            $ultimoId
                        );

                    $maximoContador = sizeof($pontuacoesPendentesUso->toArray());

                    $contador = 0;
                    foreach ($pontuacoesPendentesUso as $key => $pontuacao) {
                        if ($pontuacoesProcessar >= 0) {
                            if ($pontuacoesProcessar >= $pontuacao->quantidade_gotas) {
                                array_push(
                                    $pontuacoesPendentesUsoListaSave,
                                    [
                                        'id' => $pontuacao->id,
                                        'utilizado' => 2
                                    ]
                                );
                            } else {
                                array_push(
                                    $pontuacoesPendentesUsoListaSave,
                                    [
                                        'id' => $pontuacao->id,
                                        'utilizado' => 1
                                    ]
                                );
                            }
                        }
                        $pontuacoesProcessar = $pontuacoesProcessar - $pontuacao->quantidade_gotas;

                        $ultimoId = $pontuacao->id;

                        $contador = $contador + 1;

                        if ($contador == $maximoContador) {
                            $ultimoId = $pontuacao->id + 1;
                        }

                        if ($pontuacoesProcessar <= 0) {
                            $podeContinuar = false;
                            break;
                        }
                    }
                }

                // Atualiza todos os pontos do usuário

                $this->Pontuacoes->updatePendingPontuacoesForUsuario($pontuacoesPendentesUsoListaSave);

                // ---------- Fim de atualiza pontos à serem debitados ----------

                // Diminuir saldo de pontos do usuário
                $pontuacaoDebitar = $this->Pontuacoes->addPontuacoesBrindesForUsuario(
                    $cliente->id,
                    $usuario->id,
                    $brinde_habilitado->id,
                    $brinde_habilitado->brinde_habilitado_preco_atual->preco * $quantidade,
                    $funcionariosId
                );

                if ($pontuacaoDebitar) {
                    // Emitir Cupom e retornar

                    $cupom = $this->Cupons->addCuponsBrindesForUsuario(
                        $brinde_habilitado,
                        $usuario->id,
                        $quantidade
                    );

                         // vincula item resgatado ao cliente final

                    $brinde_usuario = $this->UsuariosHasBrindes->addUsuarioHasBrindes(
                        $usuario->id,
                        $brinde_habilitado->id,
                        $quantidade,
                        $brinde_habilitado->brinde_habilitado_preco_atual->preco,
                        $cupom->id
                    );

                    if ($cupom) {
                        $mensagem = ['status' => true, 'message' => "Cupom resgatado com sucesso!"];
                        $cupom->data = (new \DateTime($cupom->data))->format('d/m/Y H:i:s');
                        $ticket = $cupom;

                    } else {
                        $mensagem = ['status' => false, 'message' => "Houve um erro na geração do Ticket. Informe ao suporte."];
                    }
                } else {
                    $mensagem = ['status' => false, 'message' => "Usuário possui saldo insuficiente. Não foi possível realizar a transação."];
                }
            } else {
                $mensagem = ['status' => false, 'message' => "Usuário possui saldo insuficiente. Não foi possível realizar a transação."];
            }


            $ticket = $ticket;
            $cliente = $cliente;
            $usuario = $usuario;
        }

        $arraySet = [
            'mensagem',
            'ticket',
            'cliente',
            'usuario'
        ];

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);
    }

    /**
     * CuponsController::getCuponsUsuarioAPI
     *
     * Obtem todos os cupons de um Usuário
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     *
     * @return json object
     */
    public function getCuponsUsuarioAPI()
    {
        try {
            if ($this->request->is(['post'])) {
                $data = $this->request->getData();

                $usuario = $this->Auth->user();
                $usuario = $this->Usuarios->getUsuarioById($usuario['id']);

                $whereConditions = array();
                $generoBrindesClientesConditions = array();
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

                // Pesquisa por Redes
                if (isset($data['redes_id'])) {
                    $rede = $this->Redes->getRedeById($data['redes_id']);

                    $clientesIds = $this->RedesHasClientes->getClientesIdsFromRedesHasClientes($rede["id"]);

                    if (sizeof($clientesIds) == 0) {
                        $mensagem = array(
                            'status' => 0,
                            'message' => Configure::read("messageLoadDataWithError"),
                            "errors" => array(__("Não foi encontrado unidades para a rede informada, pois esta rede não existe ou está desabilitada no sistema!"))
                        );

                        $arraySet = ["mensagem"];
                        $this->set(compact($arraySet));
                        $this->set("_serialize", $arraySet);

                        return;
                    }

                    $whereConditions[] = array('Cupons.clientes_id in' => $clientesIds);
                }
                // Pesquisa por uma Unidade da Rede
                else if (isset($data['clientes_id'])) {

                    $whereConditions[] = ['clientes_id' => (int)($data['clientes_id'])];
                }

                // se genero_brindes_id estiver setado, pesquisa por um tipo de brinde

                if (isset($data["genero_brindes_id"])) {
                    $generoBrindesClientesConditions[] = array(
                        "genero_brindes_id" => $data['genero_brindes_id'],
                        "clientes_id in " => $clientesIds
                    );
                }

                if (isset($data["brindes_nome"])) {
                    // $whereConditions[] = array("Cupons.ClientesHasBrindesHabilitados.Brindes.nome like '%" . $data["brindes_nome"] . "%'");
                    $whereConditions[] = array("Brindes.nome like '%" . $data["brindes_nome"] . "%'");
                }

                // Valor pago à compra
                if (isset($data["valor_pago_min"]) && isset($data["valor_pago_max"])) {
                    $whereConditions[] = ["Cupons.valor_pago BETWEEN '{$data["valor_pago_min"]}' AND '{$data["valor_pago_max"]}'"];
                } else if (isset($data["valor_pago_min"])) {
                    $whereConditions[] = ["Cupons.valor_pago >= " => $data["valor_pago_min"]];
                } else if (isset($data["valor_pago_max"])) {
                    $whereConditions[] = ["Cupons.valor_pago <= " => $data["valor_pago_max"]];
                }

                if (isset($data["data_inicio"]) && isset($data["data_fim"])) {
                    $dataInicio = date_format(DateTime::createFromFormat("d/m/Y", $data["data_inicio"]), "Y-m-d");
                    $dataFim = date_format(DateTime::createFromFormat("d/m/Y", $data["data_fim"]), "Y-m-d");

                    $whereConditions[] = ["Cupons.data >= " => $dataInicio. " 00:00:00"];
                    $whereConditions[] = ["Cupons.data <= " => $dataFim. " 23:59:59"];

                } else if (isset($data["data_inicio"])) {
                    $dataInicio = date_format(DateTime::createFromFormat("d/m/Y", $data["data_inicio"]), "Y-m-d");
                    $whereConditions[] = ["Cupons.data >= " => $dataInicio. " 00:00:00"];

                } else if (isset($data["dataFim"])) {
                    $dataFim = date_format(DateTime::createFromFormat("d/m/Y", $data["data_fim"]), "Y-m-d");

                    $whereConditions[] = ["Cupons.data <= " => $dataFim. " 23:59:59"];
                } else {
                    $dataFim = date("Y-m-d 00:00:00");
                    $dataInicio = date('Y-m-d 23:59:59', strtotime("-30 days"));

                    $whereConditions[] = ["Cupons.data >= " => $dataInicio. " 00:00:00"];
                    $whereConditions[] = ["Cupons.data <= " => $dataFim];
                }

                $orderConditionsNew = array();

                foreach ($orderConditions as $key => $order) {
                    $orderConditionsNew["Cupons." . $key] = $order;
                }

                $orderConditions = $orderConditionsNew;

                $resultado = $this->Cupons->getCupons($whereConditions, $generoBrindesClientesConditions, $orderConditions, $paginationConditions);

                // DebugUtil::printArray($resultado);
                $mensagem = $resultado["mensagem"];
                $cupons = $resultado["cupons"];
            }
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $messageString = __("Não foi possível obter dados de cupons do usuário!");

            $mensagem = ['status' => false, 'message' => $messageString, 'errors' => $trace];

            // TODO: @gustavosg melhorar
            Log::write("error", $messageString);
            Log::write("error", $trace);

        }

        $arraySet = ["mensagem", "cupons", "usuario"];
        // $arraySet = ["mensagem", "cupons", "usuario", "whereConditions"];

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);
    }

    #region Métodos Comuns


    /**
     * CuponsController::trataCompraCupom
     *
     * Realiza o tratamento de compra de um Cupom
     *
     * @param integer $brindesId Id de Brinde
     * @param integer $usuariosId Id de Usuários
     * @param integer $clientesId Id da Unidade do Posto
     * @param float $quantidade Quantidade do brinde
     * @param integer $funcionariosId Id de Funcionários (se a compra for feita via dashboard de funcionário)
     * @param bool $usuarioAvulso Indica se é usuário avulso no sistema
     * @param string $senhaAtualUsuario Senha atual do usuário (quando via Web)
     * @param bool $usoViaMobile Indica se o método é chamado via Mobile ou via Web
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 03/07/2018
     *
     * @return array $dados Tratados
     */
    private function _trataCompraCupom(int $brindesId, int $usuariosId, int $clientesId, float $quantidade = null, int $funcionariosId = null, bool $usuarioAvulso = false, string $senhaAtualUsuario = "", bool $usoViaMobile = false)
    {
        $retorno = array();
        $mensagem = array();
        $dados_impressao = array();

        // pega id de todos os clientes que estão ligados à uma rede
        $redesHasClientes = $this->RedesHasClientes->getRedesHasClientesByClientesId($clientesId);
        $rede = $redesHasClientes->rede;
        $clientesIds = $this->RedesHasClientes->getClientesIdsFromRedesHasClientes($rede["id"]);

        $listaCamposClienteSelect = array(
            "id",
            "matriz",
            "ativado",
            "tipo_unidade",
            "nome_fantasia",
            "razao_social",
            "cnpj",
            "endereco",
            "endereco_numero",
            "endereco_complemento",
            "bairro",
            "municipio",
            "estado",
            "pais",
            "cep",
            "latitude",
            "longitude"
        );
        $cliente = $this->Clientes->getClienteById($clientesId, $listaCamposClienteSelect);
        $quantidade = is_null($quantidade) ? 1 : $quantidade;
        $quantidade = $quantidade < 1 ? 1 : $quantidade;

        if ($usuarioAvulso) {
            $usuario = $this->Usuarios->getUsuariosByProfileType(Configure::read("profileTypes")["DummyUserProfileType"], 1);
        } else {
            $usuario = $this->Usuarios->getUsuarioById($usuariosId);
        }

        /**
         * Validação de senha do usuário
         * Só deve ocorrer se não for via mobile.
         */

        $senhaValida = false;
        if ($usuario->tipo_perfil < Configure::read('profileTypes')['DummyUserProfileType'] && !$usoViaMobile) {
            if ((new DefaultPasswordHasher)->check($senhaAtualUsuario, $usuario->senha)) {
                $senhaValida = true;
            }
        } else {
            $senhaValida = true;
        }

        if (!$senhaValida) {
            $mensagem = array(
                "status" => false,
                "message" => Configure::read("messageOperationFailureDuringProcessing"),
                "errors" => "Senha incorreta para usuário. Nâo foi possível resgatar o brinde!",
            );

            $arraySet = array(
                "mensagem"
            );

            $retorno = array(
                "arraySet" => $arraySet,
                "mensagem" => $mensagem
            );
            return $retorno;
        }

        if ($usuarioAvulso) {
            $usuario["pontuacoes"] = 0;
        } else {
            $usuario = $this->Usuarios->getUsuarioById($usuariosId);
            $detalhesPontuacaoResultado = $this->Pontuacoes->getSumPontuacoesOfUsuario(
                $usuariosId,
                $rede["id"],
                $clientesIds
            );

            $usuario['pontuacoes'] = $detalhesPontuacaoResultado["resumo_gotas"]["saldo"];
        }

        $brindeSelecionado = $this->ClientesHasBrindesHabilitados->getBrindeHabilitadoByBrindesIdClientesId($brindesId, $clientesId);

        // Se não encontrado, retorna vazio.
        if (empty($brindeSelecionado)) {

            $mensagem = array(
                "status" => false,
                "message" => Configure::read("messageOperationFailureDuringProcessing"),
                "errors" => array(__("A unidade de atendimento informada não possui o brinde desejado!"))
            );

            $arraySet = array("mensagem");

            $retorno = array(
                "arraySet" => $arraySet,
                "mensagem" => $mensagem,
                "ticket" => null,
                "status" => null,
                "cliente" => null,
                "usuario" => null,
                "tempo" => null,
                "tipo_emissao_codigo_barras" => null,
                "is_brinde_smart_shower" => null,
            );
            return $retorno;
        } else if ($brindeSelecionado["genero_brindes_cliente"]["tipo_principal_codigo_brinde"] <= 4 && $quantidade > 1) {
            $mensagem = array(
                "status" => false,
                "message" => Configure::read("messageOperationFailureDuringProcessing"),
                "errors" => array(__("Para Brindes do tipo banho, a quantidade deve ser 1!"))
            );

            $arraySet = array("mensagem");

            $retorno = array(
                "arraySet" => $arraySet,
                "mensagem" => $mensagem,
                "ticket" => null,
                "status" => null,
                "cliente" => null,
                "usuario" => null,
                "tempo" => null,
                "tipo_emissao_codigo_barras" => null,
                "is_brinde_smart_shower" => null,
            );

            return $retorno;
        }

        // $usuario['pontuacoes'] = $this->Pontuacoes->getSumPontuacoesOfUsuario($usuario['id'], $rede["id"], $clientesIds);

         // Se o usuário tiver pontuações suficientes ou for um usuário de venda avulsa somente
        if ($usuario->pontuacoes >= $brindeSelecionado["brinde_habilitado_preco_atual"]["preco"] * $quantidade
            || $usuario->tipo_perfil == Configure::read('profileTypes')['DummyUserProfileType']) {

            // verificar se cliente possui usuario em sua lista de usuários. se não tiver, cadastrar
            $clientesHasUsuariosConditions = [];

            array_push($clientesHasUsuariosConditions, ['ClientesHasUsuarios.usuarios_id' => $usuario['id']]);
            array_push($clientesHasUsuariosConditions, ['ClientesHasUsuarios.clientes_id IN' => $clientesIds]);

            if ($rede->permite_consumo_gotas_funcionarios) {
                array_push($clientesHasUsuariosConditions, ['ClientesHasUsuarios.tipo_perfil >= ' => Configure::read('profileTypes')['AdminNetworkProfileType']]);
                array_push($clientesHasUsuariosConditions, ['ClientesHasUsuarios.tipo_perfil <= ' => Configure::read('profileTypes')['UserProfileType']]);
            } else {
                array_push($clientesHasUsuariosConditions, ['ClientesHasUsuarios.tipo_perfil' => Configure::read('profileTypes')['UserProfileType']]);
            }

            $clientePossuiUsuario = $this->ClientesHasUsuarios->findClienteHasUsuario($clientesHasUsuariosConditions);

            if (is_null($clientePossuiUsuario)) {
                $this->ClientesHasUsuarios->saveClienteHasUsuario($clientesId, $usuariosId, $usuario["tipo_perfil"]);
            }

            // ------------------------------------------------------------------------
            // Só diminui pontos se o usuário que estiver sendo vendido não for o avulso!
            // ------------------------------------------------------------------------
            if ($usuario->tipo_perfil < Configure::read('profileTypes')['DummyUserProfileType']) {

                // ------------------- Atualiza pontos à serem debitados -------------------

                /*
                 * Se há pontuação à debitar, devo verificar quais são as
                 * pontuações do usuário que serão utilizadas, para notificar
                 * quantos pontos ele possui que estão prestes à vencer
                 */

                $pontuacoesProcessar = $brindeSelecionado["brinde_habilitado_preco_atual"]["preco"] * $quantidade;

                $podeContinuar = true;
                $pontuacoesPendentesUsoListaSave = [];

                // Obter pontos não utilizados totalmente
                // verifica se tem algum pendente para continuar o cálculo sobre ele

                $pontuacaoPendenteUso
                    = $this->Pontuacoes->getPontuacoesPendentesForUsuario(
                    $usuario->id,
                    $clientesIds,
                    1,
                    null
                );

                if ($pontuacaoPendenteUso) {
                    $ultimoId = $pontuacaoPendenteUso->id;
                } else {
                    $ultimoId = null;
                }

                if (!is_null($ultimoId)) {
                    // soma de pontos de todos os brindes usados
                    $pontuacoesBrindesUsados = $this
                        ->Pontuacoes
                        ->getSumPontuacoesPendingToUsageByUsuario(
                            $usuario->id,
                            $clientesIds
                        );

                    $pontuacoesProcessar = $pontuacoesProcessar + $pontuacoesBrindesUsados;
                }

                while ($podeContinuar) {
                    $pontuacoesPendentesUso = $this
                        ->Pontuacoes
                        ->getPontuacoesPendentesForUsuario(
                            $usuario->id,
                            $clientesIds,
                            10,
                            $ultimoId
                        );

                    if (empty($pontuacoesPendentesUso)) {
                        break;
                    }

                    // DebugUtil::printGeneric($pontuacoesPendentesUso);

                    if (sizeof($pontuacoesPendentesUso->toArray()) == 0) {
                        // TODO: conferir o que está acontecendo
                        $podeContinuar = false;
                        break;
                    }

                    $maximoContador = sizeof($pontuacoesPendentesUso->toArray());

                    $contador = 0;
                    foreach ($pontuacoesPendentesUso as $key => $pontuacao) {

                        // DebugUtil::printGeneric($pontuacoesProcessar, 1, 0);
                        // DebugUtil::printGeneric($pontuacao);
                        if (($pontuacoesProcessar >= 0) && ($pontuacoesProcessar >= $pontuacao->quantidade_gotas)) {
                            array_push(
                                $pontuacoesPendentesUsoListaSave,
                                [
                                    'id' => $pontuacao->id,
                                    'utilizado' => 2
                                ]
                            );
                        } else {
                            array_push(
                                $pontuacoesPendentesUsoListaSave,
                                [
                                    'id' => $pontuacao->id,
                                    'utilizado' => 1
                                ]
                            );
                        }

                        $pontuacoesProcessar = $pontuacoesProcessar - $pontuacao["quantidade_gotas"];
                        $ultimoId = $pontuacao->id;
                        $contador = $contador + 1;

                        if ($contador == $maximoContador) {
                            // echo __LINE__;
                            $ultimoId = $pontuacao->id + 1;
                            // die();
                        }

                        if ($pontuacoesProcessar <= 0) {
                            // echo __LINE__;
                            // die();
                            $podeContinuar = false;
                            break;
                        }

                    }
                }

                // Atualiza todos os pontos do usuário
                $this->Pontuacoes->updatePendingPontuacoesForUsuario($pontuacoesPendentesUsoListaSave);

                // ---------- Fim de atualiza pontos à serem debitados ----------

                // Diminuir saldo de pontos do usuário
                $pontuacaoDebitar = $this->Pontuacoes->addPontuacoesBrindesForUsuario(
                    $cliente["id"],
                    $usuario["id"],
                    $brindeSelecionado["id"],
                    $brindeSelecionado["brinde_habilitado_preco_atual"]["preco"] * $quantidade,
                    $funcionariosId
                );
            }

            // Se for venda avulsa, considera que tem que debitar pontos
            if ($usuario->tipo_perfil == Configure::read('profileTypes')['DummyUserProfileType']) {
                $pontuacaoDebitar = true;
            }

            if ($pontuacaoDebitar) {
                 // Emitir Cupom e retornar

                $cupom = $this->Cupons->addCupomForUsuario(
                    $brindeSelecionado["id"],
                    $cliente["id"],
                    $usuario["id"],
                    $brindeSelecionado["brinde_habilitado_preco_atual"]["preco"] * $quantidade,
                    $quantidade
                );

                  // vincula item resgatado ao cliente final

                $brindeUsuario = $this->UsuariosHasBrindes->addUsuarioHasBrindes(
                    $usuario["id"],
                    $brindeSelecionado["id"],
                    $quantidade,
                    $brindeSelecionado["brinde_habilitado_preco_atual"]["preco"],
                    $cupom["id"]
                );

                if ($cupom) {
                    $status = true;
                    $cupom->data = (new \DateTime($cupom->data))->format('d/m/Y H:i:s');
                    $ticket = $cupom;
                    $message = null;


                } else {
                    $mensagem = array(
                        "status" => false,
                        "message" => Configure::read("messageOperationFailureDuringProcessing"),
                        "errors" => array("Houve um erro na geração do Ticket. Informe ao suporte.")
                    );

                    $arraySet = array("mensagem");

                    $retorno = array(
                        "arraySet" => $arraySet,
                        "mensagem" => $mensagem,
                        "ticket" => null,
                        "status" => null,
                        "cliente" => null,
                        "usuario" => null,
                        "tempo" => null,
                        "tipo_emissao_codigo_barras" => null,
                        "is_brinde_smart_shower" => null,
                    );

                    return $retorno;
                }
            } else {
                $mensagem = array(
                    'status' => false,
                    "message" => Configure::read("messageOperationFailureDuringProcessing"),
                    'errors' => array("Usuário possui saldo insuficiente. Não foi possível realizar a transação.")
                );

                $arraySet = array("mensagem");

                $retorno = array(
                    "arraySet" => $arraySet,
                    "mensagem" => $mensagem,
                    "ticket" => null,
                    "status" => null,
                    "cliente" => null,
                    "usuario" => null,
                    "tempo" => null,
                    "tipo_emissao_codigo_barras" => null,
                    "is_brinde_smart_shower" => null,
                );

                return $retorno;
            }
        } else {
            $mensagem = array(
                'status' => false,
                "message" => Configure::read("messageOperationFailureDuringProcessing"),
                'errors' => array("Usuário possui saldo insuficiente. Não foi possível realizar a transação.")
            );

            $arraySet = array("mensagem");

            $retorno = array(
                "arraySet" => $arraySet,
                "mensagem" => $mensagem,
                "ticket" => null,
                "status" => null,
                "cliente" => null,
                "usuario" => null,
                "tempo" => null,
                "tipo_emissao_codigo_barras" => null,
                "is_brinde_smart_shower" => null,
            );

            return $retorno;
        }

        // Se é Banho
        if (!$brindeSelecionado["genero_brindes_cliente"]["tipo_principal_codigo_brinde"] <= 4) {
            $cupons = $this->Cupons->getCuponsByCupomEmitido($ticket["cupom_emitido"])->toArray();

            $cuponsRetorno = array();

            foreach ($cupons as $key => $cupom) {
                $cupom["data"] = $cupom["data"]->format('d/m/Y H:i:s');

                $cuponsRetorno[] = $cupom;
            }

            $dados_impressao = $this->processarCupom($cuponsRetorno);
        }

        // Se chegou até aqui, ocorreu tudo bem
        $mensagem = array(
            "status" => true,
            "message" => Configure::read("messageProcessingCompleted"),
            "errors" => array()
        );

        $arraySet = [
            'mensagem',
            'ticket',
            'cliente',
            'usuario',
            'tempo',
            'tipo_emissao_codigo_barras',
            "is_brinde_smart_shower",
            'dados_impressao'
        ];

        $retorno = array(
            "arraySet" => $arraySet,
            "mensagem" => $mensagem,
            "ticket" => $ticket,
            "status" => $status,
            "cliente" => $cliente,
            "usuario" => $usuario,
            "tempo" => $brindeSelecionado["brinde"]["tempo_rti_shower"],
            "tipo_emissao_codigo_barras" => $brindeSelecionado["tipo_codigo_barras"],
            "is_brinde_smart_shower" => $brindeSelecionado["genero_brindes_cliente"]["tipo_principal_codigo_brinde"] <= 4,
        );

        return $retorno;
    }

    /**
     * Realiza processamento do cupom (tratamento) para json
     */
    private function processarCupom($cupons)
    {
        $user_admin = $this->request->session()->read('User.RootLogged');
        $user_managed = $this->request->session()->read('User.ToManage');

        if ($user_admin) {
            $this->user_logged = $user_managed;
        }

        // DebugUtil::printArray($this->user_logged);
        $funcionario = $this->user_logged;

        // DebugUtil::printArray($cupons);

        // checagem de cupons

        if (sizeof($cupons) > 0) {
            // verifica se o cupom já foi resgatado

            if ($cupons[0]->resgatado) {
                $result = [
                    'status' => false,
                    'message' => __("Cupom já foi resgatado, não é possível novo resgate!")
                ];

                return $result;
            }

            // verifica se o cupom pertence à rede que o funcionário está logado

            $clientes_id = $cupons[0]->clientes_id;
            $clientes_has_brindes_habilitados_id = $cupons[0]->clientes_has_brindes_habilitados_id;

            // pega a rede e procura todas as unidades

            $rede_has_cliente = $this->RedesHasClientes->getRedesHasClientesByClientesId($clientes_id);

            // DebugUtil::printGeneric($rede_has_cliente);
            // die();

            $redes_has_clientes = $this->RedesHasClientes->getRedesHasClientesByRedesId($rede_has_cliente["redes_id"]);

            $encontrou_cupom = false;

            // procura o brinde dentro da rede
            foreach ($redes_has_clientes as $key => $value) {
                if ($clientes_id == $value->clientes_id) {
                    $encontrou_cupom = true;
                    break;
                }
            }

            // agora procura o funcionário dentro da rede

            $clientes_has_usuarios = $this->ClientesHasUsuarios->findClienteHasUsuario(
                [
                    'ClientesHasUsuarios.usuarios_id' => $funcionario['id']
                ]
            );

            $encontrou_usuario = false;

            $unidades_id = 0;

            foreach ($clientes_has_usuarios as $key => $value) {
                $unidades_id = $value->clientes_id;
            }

            // DebugUtil::printGeneric($clientes_has_usuarios);
            // DebugUtil::printArray($funcionario);
            $rede_has_cliente = $this->RedesHasClientes->getRedesHasClientesByClientesId($unidades_id);

            $redes_has_clientes = $this->RedesHasClientes->getRedesHasClientesByRedesId($rede_has_cliente->redes_id);

            $unidade_funcionario_id = 0;
            foreach ($redes_has_clientes as $key => $value) {
                if ($clientes_id == $value->clientes_id) {

                    $unidade_funcionario_id = $clientes_id;
                    $encontrou_usuario = true;
                    break;
                }
            }

            // se não encontrou o brinde na unidade, ou não encontrou o usuário

            if (!$encontrou_cupom || !$encontrou_usuario) {
                return [
                    'status' => false,
                    'message' => __("Cupom pertencente à outra rede, não é possível importar dados!")
                ];
            }

            // Se o brinde não for ilimitado, verifica se ele possui estoque suficiente

            $brindeHabilitado = $this->ClientesHasBrindesHabilitados->getBrindeHabilitadoById($clientes_has_brindes_habilitados_id);

            if (!$brindeHabilitado->brinde->ilimitado) {

                // verifica se a unidade que vai fazer o saque tem estoque

                $quantidade_atual_brinde = $this->ClientesHasBrindesEstoque->getEstoqueAtualForBrindeId($clientes_has_brindes_habilitados_id);

                $resultado_final = $quantidade_atual_brinde - $cupons[0]->quantidade;

                if ($resultado_final < 0) {
                    return [
                        'status' => false,
                        'message' => __("Não há estoque suficiente para resgatar este brinde no momento!")
                    ];
                }
            }

            // passou em todas as validações

            $cupons[0]['unidade_funcionario_id'] = $unidade_funcionario_id;

            return [
                'status' => true,
                'data' => $cupons
            ];
        } else {
            return [
                'status' => false,
                'message' => __("Cupom não encontrado!")
            ];
        }
    }


    /**
     * beforeRender callback
     *
     * @return void
     */
    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);

        // if ($this->request->is('ajax')) {
        //     $this->viewBuilder()->setLayout('ajax');
        // }
    }

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
     *
     */
    public function initialize()
    {
        parent::initialize();
    }

    #endregion

}
