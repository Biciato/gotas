<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Custom\RTI\Security;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\Collection\Collection;
use Cake\Event\Event;
use Cake\Routing\Router;
use Cake\Mailer\Email;
use Cake\View\Helper\UrlHelper;
use \DateTime;

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
            $result = null;
            $ticket = null;
            $message = null;
            $status = 'error';
            $cupom = null;

            if ($this->request->is(['post'])) {
                $data = $this->request->getData();

                $cliente = $this->Clientes->getClienteById($data['clientes_id']);

                $rede = $this->request->session()->read('Network.Main');

                $funcionariosId = isset($data["funcionarios_id"]) ? (int)$data["funcionarios_id"] : null;

                // pega id de todos os clientes que estão ligados à uma rede

                $redes_has_clientes_query = $this->RedesHasClientes->getRedesHasClientesByRedesId($rede->id);

                $clientes_ids = [];

                foreach ($redes_has_clientes_query as $key => $value) {
                    $clientes_ids[] = $value['clientes_id'];
                }

                $array = [];

                $clientes_id = $array;

                $quantidade = isset($data['quantidade']) ? $data["quantidade"] : 1;

                $brinde_habilitado = $this->ClientesHasBrindesHabilitados->getBrindeHabilitadoById($data['brindes_id']);

                // Se brinde não for banho e a quantidade é menos que um,
                // valida e retorna mensagem de erro
                if ($brinde_habilitado["genero_brindes_cliente"]["tipo_principal_codigo_brinde"] > 4 && $quantidade < 1) {
                    $message = "Para Brindes que não são do tipo banho, a quantidade deve ser informada!";
                    $status = 'error';
                    $arraySet = [
                        'message',
                        'status',
                    ];

                    $this->set(compact($arraySet));
                    $this->set("_serialize", $arraySet);

                    return;
                }

                $contaAvulsa = $data["usuarios_id"] == "conta_avulsa";

                if ($contaAvulsa) {
                    $usuario = $this->Usuarios->getUsuariosByProfileType(Configure::read('profileTypes')['DummyUserProfileType'], 1);
                    $usuario["pontuaoes"] = 0;
                } else {
                    $usuario = $this->Usuarios->getUsuarioById($data['usuarios_id']);
                    $detalhesPontuacao = $this->Pontuacoes->getSumPontuacoesOfUsuario(
                        $usuario['id'],
                        $rede["id"],
                        $clientes_ids
                    );
                    $usuario['pontuacoes'] = $detalhesPontuacao["saldo"];
                }

                // validação de senha do usuário

                $senha_valida = false;
                if ($usuario->tipo_perfil < Configure::read('profileTypes')['DummyUserProfileType']) {
                    if ((new DefaultPasswordHasher)->check($data['current_password'], $usuario->senha)) {
                        $senha_valida = true;
                    }
                } else {
                    $senha_valida = true;
                }

                if ($senha_valida == false) {
                    $message = 'Senha incorreta para usuário. Nâo foi possível resgatar o brinde';
                } else {

                // Se o usuário tiver pontuações suficientes ou for um usuário de venda avulsa somente
                    if ($usuario->pontuacoes >= $brinde_habilitado->brinde_habilitado_preco_atual->preco * $quantidade || $usuario->tipo_perfil == Configure::read('profileTypes')['DummyUserProfileType']) {
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

                        $cliente_usuario = $this->ClientesHasUsuarios->findClienteHasUsuario($clientes_has_usuarios_conditions);

                        if (is_null($cliente_usuario)) {
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

                            $pontuacoes_to_process = $brinde_habilitado->brinde_habilitado_preco_atual->preco * $quantidade;

                            $can_continue = true;
                            $pontuacoes_pending_usage_save = [];

                        // Obter pontos não utilizados totalmente
                        // verifica se tem algum pendente para continuar o cálculo sobre ele

                            $pontuacao_pending_usage
                                = $this->Pontuacoes->getPontuacoesPendentesForUsuario(
                                $usuario->id,
                                $clientes_ids,
                                1,
                                null
                            );

                            if ($pontuacao_pending_usage) {
                                $last_id = $pontuacao_pending_usage->id;
                            } else {
                                $last_id = null;
                            }

                            if (!is_null($last_id)) {
                                // soma de pontos de todos os brindes usados
                                $pontuacoes_brindes_used = $this
                                    ->Pontuacoes
                                    ->getSumPontuacoesPendingToUsageByUsuario(
                                        $usuario->id,
                                        $clientes_ids
                                    );

                                $pontuacoes_to_process = $pontuacoes_to_process + $pontuacoes_brindes_used;
                            }

                            while ($can_continue) {
                                $pontuacoes_pending_usage = $this
                                    ->Pontuacoes
                                    ->getPontuacoesPendentesForUsuario(
                                        $usuario->id,
                                        $clientes_ids,
                                        10,
                                        $last_id
                                    );

                                $max_count = sizeof($pontuacoes_pending_usage->toArray());

                                $count = 0;
                                foreach ($pontuacoes_pending_usage as $key => $pontuacao) {
                                    if ($pontuacoes_to_process >= 0) {
                                        if ($pontuacoes_to_process >= $pontuacao->quantidade_gotas) {
                                            array_push(
                                                $pontuacoes_pending_usage_save,
                                                [
                                                    'id' => $pontuacao->id,
                                                    'utilizado' => 2
                                                ]
                                            );
                                        } else {
                                            array_push(
                                                $pontuacoes_pending_usage_save,
                                                [
                                                    'id' => $pontuacao->id,
                                                    'utilizado' => 1
                                                ]
                                            );
                                        }
                                    }
                                    $pontuacoes_to_process = $pontuacoes_to_process - $pontuacao->quantidade_gotas;

                                    $last_id = $pontuacao->id;

                                    $count = $count + 1;

                                    if ($count == $max_count) {
                                        $last_id = $pontuacao->id + 1;
                                    }

                                    if ($pontuacoes_to_process <= 0) {
                                        $can_continue = false;
                                        break;
                                    }
                                    break;
                                }
                            }

                        // Atualiza todos os pontos do usuário

                            $this->Pontuacoes->updatePendingPontuacoesForUsuario($pontuacoes_pending_usage_save);

                        // ---------- Fim de atualiza pontos à serem debitados ----------

                        // Diminuir saldo de pontos do usuário
                            $pontuacaoDebitar = $this->Pontuacoes->addPontuacoesBrindesForUsuario(
                                $cliente->id,
                                $usuario->id,
                                $brinde_habilitado->id,
                                $brinde_habilitado->brinde_habilitado_preco_atual->preco * $quantidade,
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
                                $brinde_habilitado->id,
                                $cliente->id,
                                $usuario->id,
                                $brinde_habilitado->brinde_habilitado_preco_atual->preco * $quantidade,
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
                $tipoEmissaoCodigoBarras = $brinde_habilitado["tipo_codigo_barras"];
                $isBrindeSmartShower = $brinde_habilitado["genero_brindes_cliente"]["tipo_principal_codigo_brinde"] <= 4;
                $dadosImpressao = null;

                if (!$isBrindeSmartShower) {
                    $cupons = $this->Cupons->getCuponsByCupomEmitido($ticket["cupom_emitido"])->toArray();

                    $cuponsRetorno = array();

                    foreach ($cupons as $key => $cupom) {
                        $cupom["data"] = $cupom["data"]->format('d/m/Y H:i:s');

                        $cuponsRetorno[] = $cupom;
                    }

                    $dadosImpressao = $this->processarCupom($cuponsRetorno);
                }
            }

            $arraySet = [
                'message',
                'ticket',
                'status',
                'cliente',
                'usuario',
                'tempo',
                'tipoEmissaoCodigoBarras',
                'isBrindeSmartShower',
                'dadosImpressao'
            ];

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

            $senha_valida = false;

            if ($usuario->tipo_perfil < Configure::read('profileTypes')['DummyUserProfileType']) {
                if ((new DefaultPasswordHasher)->check($data['current_password'], $usuario->senha)) {
                    $senha_valida = true;
                }
            } else {
                $senha_valida = true;
            }

            if ($senha_valida == false) {
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

                    $cliente_usuario = $this->ClientesHasUsuarios->findClienteHasUsuario($clientes_has_usuarios_conditions);

                    if (is_null($cliente_usuario)) {
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

                        $pontuacoes_to_process = $brinde_habilitado->brinde_habilitado_preco_atual->preco * $quantidade;

                        $can_continue = true;
                        $pontuacoes_pending_usage_save = [];

                    // Obter pontos não utilizados totalmente
                    // verifica se tem algum pendente para continuar o cálculo sobre ele

                        $pontuacao_pending_usage = $this
                            ->Pontuacoes
                            ->getPontuacoesPendentesForUsuario(
                                $usuario->id,
                                $clientes_ids,
                                1,
                                null
                            );

                        if ($pontuacao_pending_usage) {
                            $last_id = $pontuacao_pending_usage->id;
                        } else {
                            $last_id = null;
                        }

                        if (!is_null($last_id)) {
                        // soma de pontos de todos os brindes usados
                            $pontuacoes_brindes_used = $this
                                ->Pontuacoes
                                ->getSumPontuacoesPendingToUsageByUsuario(
                                    $usuario->id,
                                    $clientes_ids
                                );

                            $pontuacoes_to_process = $pontuacoes_to_process + $pontuacoes_brindes_used;
                        }

                        while ($can_continue) {
                            $pontuacoes_pending_usage = $this
                                ->Pontuacoes
                                ->getPontuacoesPendentesForUsuario(
                                    $usuario->id,
                                    $clientes_ids,
                                    10,
                                    $last_id
                                );

                            $max_count = sizeof($pontuacoes_pending_usage->toArray());

                            $count = 0;
                            foreach ($pontuacoes_pending_usage as $key => $pontuacao) {
                                if ($pontuacoes_to_process >= 0) {
                                    if ($pontuacoes_to_process >= $pontuacao->quantidade_gotas) {
                                        array_push(
                                            $pontuacoes_pending_usage_save,
                                            [
                                                'id' => $pontuacao->id,
                                                'utilizado' => 2
                                            ]
                                        );
                                    } else {
                                        array_push(
                                            $pontuacoes_pending_usage_save,
                                            [
                                                'id' => $pontuacao->id,
                                                'utilizado' => 1
                                            ]
                                        );
                                    }
                                }
                                $pontuacoes_to_process = $pontuacoes_to_process - $pontuacao->quantidade_gotas;

                                $last_id = $pontuacao->id;

                                $count = $count + 1;

                                if ($count == $max_count) {
                                    $last_id = $pontuacao->id + 1;
                                }

                                if ($pontuacoes_to_process <= 0) {
                                    $can_continue = false;
                                    break;
                                }
                            }
                        }

                        // Atualiza todos os pontos do usuário

                        $this->Pontuacoes->updatePendingPontuacoesForUsuario($pontuacoes_pending_usage_save);

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

            $senha_valida = false;
            if ((new DefaultPasswordHasher)->check($data['current_password'], $usuario->senha)) {
                $senha_valida = true;
            }

            if ($senha_valida == false) {
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
                            $cupom_save = $this->Cupons->setCupomAsRedeemed($cupom->id);

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

                $cliente_usuario = $this->ClientesHasUsuarios->findClienteHasUsuario($clientes_has_usuarios_conditions);

                if (is_null($cliente_usuario)) {
                    $this->ClientesHasUsuarios->addNewClienteHasUsuario($cliente->matriz_id, $cliente->id, $usuario->id);
                }

                // ------------------- Atualiza pontos à serem debitados -------------------

                /*
                 * Se há pontuação à debitar, devo verificar quais são as
                 * pontuações do usuário que serão utilizadas, para notificar
                 * quantos pontos ele possui que estão prestes à vencer
                 */

                $pontuacoes_to_process = $brinde_habilitado->brinde_habilitado_preco_atual->preco;

                $can_continue = true;
                $pontuacoes_pending_usage_save = [];

                // Obter pontos não utilizados totalmente
                // verifica se tem algum pendente para continuar o cálculo sobre ele

                $pontuacao_pending_usage
                    = $this->Pontuacoes->getPontuacoesPendentesForUsuario(
                    $usuario->id,
                    $clientes_ids,
                    1,
                    null
                );

                if ($pontuacao_pending_usage) {
                    $last_id = $pontuacao_pending_usage->id;
                } else {
                    $last_id = null;
                }

                if (!is_null($last_id)) {
                    // soma de pontos de todos os brindes usados
                    $pontuacoes_brindes_used = $this
                        ->Pontuacoes
                        ->getSumPontuacoesPendingToUsageByUsuario(
                            $usuario->id,
                            $clientes_ids
                        );

                    $pontuacoes_to_process = $pontuacoes_to_process + $pontuacoes_brindes_used;
                }

                while ($can_continue) {
                    $pontuacoes_pending_usage = $this
                        ->Pontuacoes
                        ->getPontuacoesPendentesForUsuario(
                            $usuario->id,
                            $clientes_ids,
                            10,
                            $last_id
                        );

                    $max_count = sizeof($pontuacoes_pending_usage->toArray());

                    $count = 0;
                    foreach ($pontuacoes_pending_usage as $key => $pontuacao) {
                        if ($pontuacoes_to_process >= 0) {
                            if ($pontuacoes_to_process >= $pontuacao->quantidade_gotas) {
                                array_push(
                                    $pontuacoes_pending_usage_save,
                                    [
                                        'id' => $pontuacao->id,
                                        'utilizado' => 2
                                    ]
                                );
                            } else {
                                array_push(
                                    $pontuacoes_pending_usage_save,
                                    [
                                        'id' => $pontuacao->id,
                                        'utilizado' => 1
                                    ]
                                );
                            }
                        }
                        $pontuacoes_to_process = $pontuacoes_to_process - $pontuacao->quantidade_gotas;

                        $last_id = $pontuacao->id;

                        $count = $count + 1;

                        if ($count == $max_count) {
                            $last_id = $pontuacao->id + 1;
                        }

                        if ($pontuacoes_to_process <= 0) {
                            $can_continue = false;
                            break;
                        }
                    }
                }

                // Atualiza todos os pontos do usuário

                $this->Pontuacoes->updatePendingPontuacoesForUsuario($pontuacoes_pending_usage_save);

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

                $cliente_usuario = $this->ClientesHasUsuarios->findClienteHasUsuario($clientes_has_usuarios_conditions);

                if (is_null($cliente_usuario)) {
                    $this->ClientesHasUsuarios->addNewClienteHasUsuario($cliente->matriz_id, $cliente->id, $usuario->id);
                }

                // ------------------- Atualiza pontos à serem debitados -------------------

                /*
                 * Se há pontuação à debitar, devo verificar quais são as
                 * pontuações do usuário que serão utilizadas, para notificar
                 * quantos pontos ele possui que estão prestes à vencer
                 */

                $pontuacoes_to_process = $brinde_habilitado->brinde_habilitado_preco_atual->preco;

                $can_continue = true;
                $pontuacoes_pending_usage_save = [];

                // Obter pontos não utilizados totalmente
                // verifica se tem algum pendente para continuar o cálculo sobre ele

                $pontuacao_pending_usage
                    = $this->Pontuacoes->getPontuacoesPendentesForUsuario(
                    $usuario->id,
                    $clientes_ids,
                    1,
                    null
                );

                if ($pontuacao_pending_usage) {
                    $last_id = $pontuacao_pending_usage->id;
                } else {
                    $last_id = null;
                }

                if (!is_null($last_id)) {
                    // soma de pontos de todos os brindes usados
                    $pontuacoes_brindes_used = $this
                        ->Pontuacoes
                        ->getSumPontuacoesPendingToUsageByUsuario(
                            $usuario->id,
                            $clientes_ids
                        );

                    $pontuacoes_to_process = $pontuacoes_to_process + $pontuacoes_brindes_used;
                }

                while ($can_continue) {
                    $pontuacoes_pending_usage = $this
                        ->Pontuacoes
                        ->getPontuacoesPendentesForUsuario(
                            $usuario->id,
                            $clientes_ids,
                            10,
                            $last_id
                        );

                    $max_count = sizeof($pontuacoes_pending_usage->toArray());

                    $count = 0;
                    foreach ($pontuacoes_pending_usage as $key => $pontuacao) {
                        if ($pontuacoes_to_process >= 0) {
                            if ($pontuacoes_to_process >= $pontuacao->quantidade_gotas) {
                                array_push(
                                    $pontuacoes_pending_usage_save,
                                    [
                                        'id' => $pontuacao->id,
                                        'utilizado' => 2
                                    ]
                                );
                            } else {
                                array_push(
                                    $pontuacoes_pending_usage_save,
                                    [
                                        'id' => $pontuacao->id,
                                        'utilizado' => 1
                                    ]
                                );
                            }
                        }
                        $pontuacoes_to_process = $pontuacoes_to_process - $pontuacao->quantidade_gotas;

                        $last_id = $pontuacao->id;

                        $count = $count + 1;

                        if ($count == $max_count) {
                            $last_id = $pontuacao->id + 1;
                        }

                        if ($pontuacoes_to_process <= 0) {
                            $can_continue = false;
                            break;
                        }
                    }
                }

                // Atualiza todos os pontos do usuário

                $this->Pontuacoes->updatePendingPontuacoesForUsuario($pontuacoes_pending_usage_save);

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

                $cliente_usuario = $this->ClientesHasUsuarios->findClienteHasUsuario($clientes_has_usuarios_conditions);

                if (is_null($cliente_usuario)) {
                    $this->ClientesHasUsuarios->addNewClienteHasUsuario($cliente->matriz_id, $cliente->id, $usuario->id);
                }

                // ------------------- Atualiza pontos à serem debitados -------------------

                /*
                 * Se há pontuação à debitar, devo verificar quais são as
                 * pontuações do usuário que serão utilizadas, para notificar
                 * quantos pontos ele possui que estão prestes à vencer
                 */

                $pontuacoes_to_process = $brinde_habilitado->brinde_habilitado_preco_atual->preco * $quantidade;

                $can_continue = true;
                $pontuacoes_pending_usage_save = [];

                    // Obter pontos não utilizados totalmente
                    // verifica se tem algum pendente para continuar o cálculo sobre ele

                $pontuacao_pending_usage = $this
                    ->Pontuacoes
                    ->getPontuacoesPendentesForUsuario(
                        $usuario->id,
                        $clientes_ids,
                        1,
                        null
                    );

                if ($pontuacao_pending_usage) {
                    $last_id = $pontuacao_pending_usage->id;
                } else {
                    $last_id = null;
                }

                if (!is_null($last_id)) {
                        // soma de pontos de todos os brindes usados
                    $pontuacoes_brindes_used = $this
                        ->Pontuacoes
                        ->getSumPontuacoesPendingToUsageByUsuario(
                            $usuario->id,
                            $clientes_ids
                        );

                    $pontuacoes_to_process = $pontuacoes_to_process + $pontuacoes_brindes_used;
                }

                while ($can_continue) {
                    $pontuacoes_pending_usage = $this
                        ->Pontuacoes
                        ->getPontuacoesPendentesForUsuario(
                            $usuario->id,
                            $clientes_ids,
                            10,
                            $last_id
                        );

                    $max_count = sizeof($pontuacoes_pending_usage->toArray());

                    $count = 0;
                    foreach ($pontuacoes_pending_usage as $key => $pontuacao) {
                        if ($pontuacoes_to_process >= 0) {
                            if ($pontuacoes_to_process >= $pontuacao->quantidade_gotas) {
                                array_push(
                                    $pontuacoes_pending_usage_save,
                                    [
                                        'id' => $pontuacao->id,
                                        'utilizado' => 2
                                    ]
                                );
                            } else {
                                array_push(
                                    $pontuacoes_pending_usage_save,
                                    [
                                        'id' => $pontuacao->id,
                                        'utilizado' => 1
                                    ]
                                );
                            }
                        }
                        $pontuacoes_to_process = $pontuacoes_to_process - $pontuacao->quantidade_gotas;

                        $last_id = $pontuacao->id;

                        $count = $count + 1;

                        if ($count == $max_count) {
                            $last_id = $pontuacao->id + 1;
                        }

                        if ($pontuacoes_to_process <= 0) {
                            $can_continue = false;
                            break;
                        }
                    }
                }

                // Atualiza todos os pontos do usuário

                $this->Pontuacoes->updatePendingPontuacoesForUsuario($pontuacoes_pending_usage_save);

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

                    $clientesIds = array();

                    if (!is_null($rede)) {

                        foreach ($rede->redes_has_clientes as $key => $value) {
                            $clientesIds[] = $value->clientes_id;
                        }

                        $whereConditions[] = ['clientes_id in' => $clientesIds];
                    } else {
                        $mensagem = ['status' => false, 'message' => __("Não foi encontrado unidades para a rede informada, pois esta rede não existe ou está desabilitada no sistema!")];

                        $arraySet = ["mensagem"];
                        $this->set(compact($arraySet));
                        $this->set("_serialize", $arraySet);

                        return;
                    }
                }
                // Pesquisa por uma Unidade da Rede
                else if (isset($data['clientes_id'])) {

                    $whereConditions[] = ['clientes_id' => (int)($data['clientes_id'])];
                }

                // se filtrar_banho estiver setado, pesquisa ou não por brindes do tipo Smart Shower
                if (isset($data['filtrar_banho'])) {

                    if ($data['filtrar_banho'] == true) {

                        if (isset($data["tipo_banho"])) {

                            $tipoBanho = $data['tipo_banho'];

                            $tipoBanhoMin = 0;
                            $tipoBanhoMax = 0;
                            if ($tipoBanho == 1 || $tipoBanho == 3) {
                                $tipoBanhoMax = $tipoBanho;
                                $tipoBanhoMin = $tipoBanhoMax - 1;
                            } else {
                                $tipoBanhoMin = $tipoBanho;
                                $tipoBanhoMax = $tipoBanhoMin + 1;
                            }

                            $whereConditions[] = ["tipo_banho IN" => [$tipoBanhoMin, $tipoBanhoMax]];
                        } else {
                            $whereConditions[] = ["tipo_banho is not null"];
                        }

                        // tempo de banho
                        if (isset($data["tempo_banho"])) {
                            $whereConditions[] = ["tempo_banho" => $data["tempo_banho"]];
                        }
                    } else {
                        $whereConditions[] = ['tipo_banho IS NULL'];
                    }
                }

                // Valor pago à compra
                if (isset($data["valor_pago_min"]) && isset($data["valor_pago_max"])) {
                    $whereConditions[] = ["valor_pago BETWEEN '{$data["valor_pago_min"]}' AND '{$data["valor_pago_max"]}'"];
                } else if (isset($data["valor_pago_min"])) {
                    $whereConditions[] = ["valor_pago >= " => $data["valorPagoMin"]];
                } else if (isset($data["valor_pago_min"])) {
                    $whereConditions[] = ["valor_pago <= " => $data["valor_pago_max"]];
                }

                if (isset($data["data_inicio"]) && isset($data["data_fim"])) {
                    $dataInicio = date_format(DateTime::createFromFormat("d/m/Y", $data["data_inicio"]), "Y-m-d");
                    $dataFim = date_format(DateTime::createFromFormat("d/m/Y", $data["data_fim"]), "Y-m-d");

                    $whereConditions[] = ["data >= " => $dataInicio];
                    $whereConditions[] = ["data <= " => $dataFim];

                } else if (isset($data["data_inicio"])) {
                    $dataInicio = date_format(DateTime::createFromFormat("d/m/Y", $data["data_inicio"]), "Y-m-d");
                    $whereConditions[] = ["data >= " => $dataInicio];

                } else if (isset($data["dataFim"])) {
                    $dataFim = date_format(DateTime::createFromFormat("d/m/Y", $data["data_fim"]), "Y-m-d");

                    $whereConditions[] = ["data <= " => $dataFim];
                } else {
                    $dataFim = date("Y-m-d");
                    $dataInicio = date('Y-m-d', strtotime("-30 days"));

                    $whereConditions[] = ["data >= " => $dataInicio];
                    $whereConditions[] = ["data <= " => $dataFim];
                }

                $cupons = $this->Cupons->getCupons($whereConditions, $orderConditions, $paginationConditions);
            }

            $mensagem = ['status' => true, 'message' => null];
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $messageString = __("Não foi possível obter dados de cupons do usuário!");

            $mensagem = ['status' => false, 'message' => $messageString, 'errors' => $trace];
        }

        $arraySet = ["mensagem", "cupons", "usuario"];
        // $arraySet = ["mensagem", "cupons", "usuario", "whereConditions"];

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);
    }

    /**
     * ---------------------------------------------
     * Métodos da classe
     * ---------------------------------------------
     */

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

        $funcionario = $this->user_logged;

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

            $redes_has_clientes = $this->RedesHasClientes->getRedesHasClientesByRedesId($rede_has_cliente->redes_id);

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
     * ------------------------------------------------------------
     * Métodos Comuns
     * ------------------------------------------------------------
     */

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
}
