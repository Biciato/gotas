<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Custom\RTI\Security;
use Cake\Core\Configure;
use Cake\Collection\Collection;
use Cake\Event\Event;
use Cake\Log\Log;
use Cake\Routing\Router;
use Cake\Mailer\Email;
use Cake\View\Helper\UrlHelper;
use \DateTime;
use App\Custom\RTI\DateTimeUtil;
use App\Custom\RTI\DebugUtil;

/**
 * clientes_has_brindes_habilitados Controller
 *
 * @property \App\Model\Table\clientes_has_brindes_habilitadosTable $clientes_has_brindes_habilitados
 *
 * @method \App\Model\Entity\ClientesHasBrindesHabilitado[] paginate($object = null, array $settings = [])
 */
class ClientesHasBrindesHabilitadosController extends AppController
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
            'contain' => ['Clientes', 'BrindesHabilitados']
        ];
        $clientes_has_brindes_habilitados = $this->paginate($this->ClientesHasBrindesHabilitados, ['limit' => 10]);

        $this->set(compact('clientes_has_brindes_habilitados'));
        $this->set('_serialize', ['clientes_has_brindes_habilitados']);
    }

    /**
     * View method
     *
     * @param string|null $id Clientes Has Brindes Habilitado id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $clientesHasBrindesHabilitado = $this->ClientesHasBrindesHabilitados->get(
            $id,
            [
                'contain' =>
                    [
                    'Clientes', 'BrindesHabilitados'
                ]
            ]
        );

        $this->set('clientesHasBrindesHabilitado', $clientesHasBrindesHabilitado);
        $this->set('_serialize', ['clientesHasBrindesHabilitado']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $clientesHasBrindesHabilitado = $this->ClientesHasBrindesHabilitados->newEntity();
        if ($this->request->is('post')) {
            $clientesHasBrindesHabilitado = $this->ClientesHasBrindesHabilitados->patchEntity($clientesHasBrindesHabilitado, $this->request->getData());
            if ($this->ClientesHasBrindesHabilitados->save($clientesHasBrindesHabilitado)) {
                $this->Flash->success(__('The clientes has brindes habilitado has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The clientes has brindes habilitado could not be saved. Please, try again.'));
        }
        $clientes = $this->ClientesHasBrindesHabilitados->Clientes->find('list', ['limit' => 200]);
        $brindesHabilitados = $this->ClientesHasBrindesHabilitados->BrindesHabilitados->find('list', ['limit' => 200]);
        $this->set(compact('clientesHasBrindesHabilitado', 'clientes', 'brindesHabilitados'));
        $this->set('_serialize', ['clientesHasBrindesHabilitado']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Clientes Has Brindes Habilitado id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $clientesHasBrindesHabilitado = $this->ClientesHasBrindesHabilitados->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $clientesHasBrindesHabilitado = $this->ClientesHasBrindesHabilitados->patchEntity($clientesHasBrindesHabilitado, $this->request->getData());
            if ($this->ClientesHasBrindesHabilitados->save($clientesHasBrindesHabilitado)) {
                $this->Flash->success(__('The clientes has brindes habilitado has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The clientes has brindes habilitado could not be saved. Please, try again.'));
        }
        $clientes = $this->ClientesHasBrindesHabilitados->Clientes->find('list', ['limit' => 200]);
        $brindesHabilitados = $this->ClientesHasBrindesHabilitados->BrindesHabilitados->find('list', ['limit' => 200]);
        $this->set(compact('clientesHasBrindesHabilitado', 'clientes', 'brindesHabilitados'));
        $this->set('_serialize', ['clientesHasBrindesHabilitado']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Clientes Has Brindes Habilitado id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $clientesHasBrindesHabilitado = $this->ClientesHasBrindesHabilitados->get($id);
        if ($this->ClientesHasBrindesHabilitados->delete($clientesHasBrindesHabilitado)) {
            $this->Flash->success(__('The clientes has brindes habilitado has been deleted.'));
        } else {
            $this->Flash->error(__('The clientes has brindes habilitado could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * ------------------------------------------------------------
     * Custom Actions
     * ------------------------------------------------------------
     */

    /**
     * Exibe a Action para escolher uma unidade da rede, para gerenciar os brindes, habilitar e estoque
     *
     * @return void
     */
    public function escolherUnidadeConfigBrinde()
    {
        $user_admin = $this->request->session()->read('User.RootLogged');
        $user_managed = $this->request->session()->read('User.ToManage');

        if ($user_admin) {
            $this->user_logged = $user_managed;
        }

        $rede = $this->request->session()->read('Network.Main');

        $clientesIdsArray = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($rede->id, $this->user_logged['id'], false);

        if (!is_null($clientesIdsArray)) {
            foreach ($clientesIdsArray as $key => $value) {
                $clientes_ids[] = $key;
            }
        }

        // $redes_has_clientes = $this->RedesHasClientes->getRedesHasClientesByRedesId($rede->id);
        $redes_has_clientes = $this->RedesHasClientes->getRedesHasClientesByRedesId($rede->id, $clientes_ids);

        $redes_has_clientes = $this->paginate(
            $redes_has_clientes,
            [
                'limit' => 10,
                'order' => [
                    'id' => 'asc'
                ]
            ]
        );

        $this->set(compact('redes_has_clientes'));
        $this->set('_serialize', ['redes_has_clientes']);

    }

    /**
     * Exibe a Action para configurar os brindes da unidade selecionada
     *
     * @param integer $clientes_id
     * @return void
     */
    public function configurarBrindesUnidade(int $clientes_id)
    {
        $rede = $this->request->session()->read("Network.Main");
        $user_admin = $this->request->session()->read('User.RootLogged');
        $user_managed = $this->request->session()->read('User.ToManage');

        if ($user_admin) {
            $this->user_logged = $user_managed;
        }

        $temAcesso = $this->security_util->checkUserIsClienteRouteAllowed($this->user_logged, $this->Clientes, $this->ClientesHasUsuarios, [$clientes_id], $rede["id"]);

        if (!$temAcesso) {
            return $this->security_util->redirectUserNotAuthorized($this, $this->user_logged);
        }

        // obtem os brindes habilitados (e não habilitados) da unidade
        $brindesConfigurar = $this->ClientesHasBrindesHabilitados->getTodosBrindesByClienteId([$clientes_id]);

        $brindesConfigurarArrayRetorno = array();

        foreach ($brindesConfigurar as $brinde) {
            $brinde["pendente_configuracao"] = empty($brinde["brindeVinculado"]["tipo_codigo_barras"]);

            $brindesConfigurarArrayRetorno[] = $brinde;
        }

        $brindesConfigurar = $brindesConfigurarArrayRetorno;

        $arraySet = ['brindesConfigurar', 'clientes_id'];
        $this->set(compact($arraySet));
        $this->set('_serialize', $arraySet);
    }

    /**
     * Detalhes de Brinde
     *
     * @param int $brindes_id Id de Brindes
     *
     * @return \Cake\Http\Response|null
     **/
    public function configurarBrinde(int $brindes_id)
    {
        $user_admin = $this->request->session()->read('User.RootLogged');
        $user_managed = $this->request->session()->read('User.ToManage');

        if ($user_admin) {
            $this->user_logged = $user_managed;
        }

        $cliente_has_brinde_habilitado = $this->ClientesHasBrindesHabilitados->getBrindeHabilitadoById($brindes_id);

        // debug($cliente_has_brinde_habilitado);
        $historico_precos = $this->paginate(
            $this->ClientesHasBrindesHabilitadosPreco->getAllPrecoForBrindeHabilitadoId($cliente_has_brinde_habilitado->id),
            ['order' => ['data_preco' => 'desc'], 'limit' => 10]
        );

        $clientes_id = $cliente_has_brinde_habilitado->clientes_id;

        $arraySet = array(
            'historico_precos',
            'cliente_has_brinde_habilitado',
            'clientes_id'
        );
        $this->set(compact($arraySet));
        $this->set('_serialize', $arraySet);
    }

    /**
     * Método para Ativar brindes de uma Rede (Cliente)
     *
     * @return void
     */
    public function ativarBrindes()
    {
        $user_admin = $this->request->session()->read('User.RootLogged');
        $user_managed = $this->request->session()->read('User.ToManage');

        if ($user_admin) {
            $this->user_logged = $user_managed;
        }

        $cliente = $this->security_util->checkUserIsClienteRouteAllowed($this->user_logged, $this->Clientes, $this->ClientesHasUsuarios);

        $rede = $this->request->session()->read('Network.Main');

        $matriz = $this->RedesHasClientes->findMatrizOfRedesByRedesId($rede->id);

        $unidadesIds = [];

        $clientes_ids = [];

        // Pega unidades que tem acesso

        $unidadesIds = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($rede->id, $this->user_logged['id']);

        foreach ($unidadesIds as $key => $value) {
                // $clientes_ids[] = $value['clientes_id'];
            $clientes_ids[] = $key;
        }

        $conditions = [];

        if (sizeof($clientes_ids) > 0) {
            $clientes_id = $clientes_ids[0];
        }

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();

            if (strlen($data['parametro']) > 0) {
                if ($data['opcoes'] == 'nome') {
                    array_push($conditions, ['brindes.nome like' => '%' . $data['parametro'] . '%']);
                } else {
                    array_push($conditions, ['brindes.preco_padrao' => $data['parametro']]);
                }
            }

            if (isset($data['filtrar_unidade']) && $data['filtrar_unidade'] != "") {
                $clientes_id = (int)$data['filtrar_unidade'];
            }
        }

        $brindes_desabilitados = $this->Brindes->getBrindesHabilitarByClienteId($clientes_id, $matriz->clientes_id, $conditions);

        $this->set(compact(['brindes_desabilitados', 'unidades_ids', 'clientes_id']));
        $this->set('_serialize', ['brindes_desabilitados', 'unidades_ids', 'clientes_id']);

    }

    /**
     * Habilita brinde selecionado
     *
     * @param int $brindes_id Id do brinde
     *
     * @return \Cake\Http\Response|null Redireciona para meus_brindes_ativados.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     **/
    public function habilitarBrinde()
    {
        $data = $this->request->query();

        $brindes_id = (int)$data['brindes_id'];
        $clientes_id = (int)$data['clientes_id'];

        $this->_alteraEstadoBrinde($brindes_id, $clientes_id, true);
    }

    /**
     * Desabilita brinde selecionado
     *
     * @param int $brindes_id Id do brinde
     *
     * @return \Cake\Http\Response|null Redireciona para meus_brindes_ativados.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     **/
    public function desabilitarBrinde()
    {
        $data = $this->request->query();

        $brindes_id = (int)$data['brindes_id'];
        $clientes_id = (int)$data['clientes_id'];

        $this->_alteraEstadoBrinde($brindes_id, $clientes_id, false);
    }

    /**
     * Altera Status do Brinde
     *
     * @param int     $brindes_id  Id do brinde
     * @param int     $clientes_id Id do cliente
     * @param boolean $status      Estado
     *
     * @return void
     */
    private function _alteraEstadoBrinde(int $brindes_id, int $clientes_id, $status)
    {
        $user_admin = $this->request->session()->read('User.RootLogged');
        $user_managed = $this->request->session()->read('User.ToManage');

        if ($user_admin) {
            $this->user_logged = $user_managed;
        }

        /**
         * Verifica se a unidade do cliente tem o tipo de brinde configurado.
         * Sem isso, não é possível continuar.
         */

        $brinde = $this->Brindes->getBrindesById($brindes_id);
        $tiposBrindesCliente = $this->TiposBrindesClientes->getTiposBrindesClientesByTiposBrindesRedes($brinde["tipos_brindes_redes_id"], $clientes_id);

        if (empty($tiposBrindesCliente)) {

            $error = $status == 1 ? Configure::read("messageEnableError") : Configure::read("messageDisableError");

            $this->Flash->error(__("{0} - {1}", $error, "Unidade não possui Tipo de Brinde configurado!"));

            return $this->redirect(['action' => 'configurar_brindes_unidade', $clientes_id]);
        }

        // verifica se o cliente tem o brinde habilitado
        $clienteHasBrindeHabilitado = $this->ClientesHasBrindesHabilitados->getBrindeHabilitadoByBrindeId($brindes_id);

        if (is_null($clienteHasBrindeHabilitado)) {
            $clienteHasBrindeHabilitado = $this->ClientesHasBrindesHabilitados->newEntity();
            $clienteHasBrindeHabilitado->brindes_id = $brindes_id;
            $clienteHasBrindeHabilitado->clientes_id = $clientes_id;
            $clienteHasBrindeHabilitado->tipos_brindes_clientes_id = $tiposBrindesCliente["id"];
        } else if (empty($clienteHasBrindeHabilitado["tipos_brindes_clientes_id"])) {
            // Atualiza o vínculo se estiver nulo
            $clienteHasBrindeHabilitado->tipos_brindes_clientes_id = $tiposBrindesCliente["id"];
        }

        $clienteHasBrindeHabilitado->habilitado = $status;

        if ($clienteHasBrindeHabilitado = $this->ClientesHasBrindesHabilitados->save($clienteHasBrindeHabilitado)) {
            /* Se for true, verificar se é registro novo.
             * Se for, é necessário incluir novo preço, definir estoque
             */

            if ($status) {
                if (!is_null($clienteHasBrindeHabilitado)) {
                    /* estoque só deve ser criado para registro nas
                     * seguintes situações.
                     *
                     * 1 - O Brinde está sendo vinculado a um cadastro de loja
                     *     no sistema (Isto é, se ele não foi anteriormente )
                     * 2 - Não é ilimitado
                     * 3 - Se não houver cadastro anterior
                     */
                    $brinde
                        = $this->Brindes->getBrindesById(
                        $clienteHasBrindeHabilitado->brindes_id
                    );

                    if (!$brinde->ilimitado) {
                        $estoque = $this->ClientesHasBrindesEstoque
                            ->getEstoqueForBrindeId(
                                $clienteHasBrindeHabilitado->id,
                                0
                            );

                        if (is_null($estoque)) {
                                // Não tem estoque, criar novo registro vazio
                            $this->ClientesHasBrindesEstoque->addEstoqueForBrindeId($clienteHasBrindeHabilitado->id, $this->user_logged['id'], 0, 0);
                        }
                    }
                    // brinde habilitado, verificar se já tem preço. Se não tiver, cadastra
                    $precos = $this->ClientesHasBrindesHabilitadosPreco->getUltimoPrecoBrindeHabilitadoId($clienteHasBrindeHabilitado->id);

                    if (!isset($precos)) {
                        $this->ClientesHasBrindesHabilitadosPreco->addBrindeHabilitadoPreco(
                            $clienteHasBrindeHabilitado["id"],
                            $clientes_id,
                            (int)Configure::read('giftApprovalStatus')['Allowed'],
                            $brinde["preco_padrao"],
                            $brinde["valor_moeda_venda"]
                        );
                    }
                }
            }
            $this->Flash->success(__(Configure::read('messageSavedSuccess')));
        } else {
                // Erro ao gravar
            $this->Flash->error(__(Configure::read('messageSavedError')));
        }

        if ($status && strlen($clienteHasBrindeHabilitado->tipo_codigo_barras) == 0) {
            return $this->redirect(['action' => 'configurar_tipo_emissao', $clienteHasBrindeHabilitado->id]);
        }

        return $this->redirect(['action' => 'configurar_brindes_unidade', $clientes_id]);
    }

    /**
     * ClientesHasBrindesHabilitadosController::configurarTipoEmissao
     *
     * Configura o tipo de emissão do brinde selecionado
     *
     * @param int $clienteHasBrindeHabilitadoId
     * @return void
     */
    public function configurarTipoEmissao(int $clienteHasBrindeHabilitadoId)
    {
        $user_admin = $this->request->session()->read('User.RootLogged');
        $user_managed = $this->request->session()->read('User.ToManage');

        if ($user_admin) {
            $this->user_logged = $user_managed;
        }

        $brinde_habilitado = $this->ClientesHasBrindesHabilitados->getBrindeHabilitadoById($clienteHasBrindeHabilitadoId);

        $clientes_id = $brinde_habilitado->clientes_id;
        $brindes_id = $brinde_habilitado->brindes_id;

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();

            if (strlen($data["tipo_codigo_barras"]) == 0) {

                $this->Flash->error("É necessário informar o Tipo de Código de Barras!");

                return;
            }

            $brinde_habilitado = $this->ClientesHasBrindesHabilitados->patchEntity($brinde_habilitado, $data);

            $brinde_habilitado = $this->ClientesHasBrindesHabilitados->save($brinde_habilitado);

            if ($brinde_habilitado) {
                $this->Flash->success(Configure::read("messageSavedSuccess"));

                return $this->redirect(["controller" => "clientes_has_brindes_habilitados", "action" => "configurar_brindes_unidade", $clientes_id]);
            } else {
                $this->Flash->success(Configure::read("messageSavedError"));
            }
        }

        $arraySet = [
            "brinde_habilitado",
            "clientes_id",
            "brindes_id",
        ];

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);
    }

    /**
     * Detalhes de Brinde
     *
     * @param int $brindes_id Id de Brindes
     *
     * @return \Cake\Http\Response|null
     **/
    public function detalhesBrinde(int $brindes_id)
    {
        $user_admin = $this->request->session()->read('User.RootLogged');
        $user_managed = $this->request->session()->read('User.ToManage');

        if ($user_admin) {
            $this->user_logged = $user_managed;
        }

        $this->security_util->checkUserIsClienteRouteAllowed($this->user_logged, $this->Clientes, $this->ClientesHasUsuarios);

        $cliente_has_brinde_habilitado = $this->ClientesHasBrindesHabilitados->getBrindeHabilitadoById($brindes_id);

        $historico_precos = $this->paginate($this->ClientesHasBrindesHabilitadosPreco->getAllPrecoForBrindeHabilitadoId($cliente_has_brinde_habilitado->id), ['order' => ['data_preco' => 'desc'], 'limit' => 10]);

        $this->set(compact(['historico_precos', 'cliente_has_brinde_habilitado']));
        $this->set('_serialize', ['historico_precos']);
    }

    /**
     * Método que lista os brindes ativados de um Cliente
     *
     * @return void
     * @author
     **/
    public function meusBrindesAtivados()
    {
        $user_admin = $this->request->session()->read('User.RootLogged');
        $user_managed = $this->request->session()->read('User.ToManage');

        if ($user_admin) {
            $this->user_logged = $user_managed;
        }

        $client_to_manage = $this->request->session()->read('ClientToManage');

        if (isset($client_to_manage)) {
            $cliente = $client_to_manage;
        }

        $rede = $this->request->session()->read('Network.Main');

        $clientes_ids = [];

        // pega todas as unidades que o usuário possui acesso

        $unidadesIds = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($rede->id, $this->user_logged['id']);

        foreach ($unidadesIds as $key => $value) {
            $clientes_ids[] = $key;
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

        $this->paginate($clientes_has_brindes_habilitados, ['limit' => 10]);

        // Infelizmente o paginate não permite (e nem é uma boa ideia) paginar um array.
        // Então o melhor a fazer agora é pegar o preço atualizado de cada brinde habilitado

        $array_return = [];
        foreach ($clientes_has_brindes_habilitados as $key => $value) {
            $value['BrindeHabilitadoPrecoAtual']
                = $this->ClientesHasBrindesHabilitadosPreco->getUltimoPrecoBrindeHabilitadoId($value['id']);

            array_push($array_return, $value);
        }

        $clientes_has_brindes_habilitados = null;
        $clientes_has_brindes_habilitados = $array_return;

        $this->set(compact('clientes_has_brindes_habilitados', 'unidades_ids', 'rede'));
        $this->set('_serialize', ['clientes_has_brindes_habilitados', 'unidades_ids', 'rede']);
    }

    /**
     * ------------------------------------------------------------
     * Métodos para cliente final
     * ------------------------------------------------------------
     */

    /**
     * Action para escolher o brinde à ser resgatado
     *
     * @param integer $clientes_id Id do cliente à pesquisar os brindes
     *
     * @return void
     */
    public function escolherBrindeUnidade(int $clientes_id)
    {
        try {
            $user_admin = $this->request->session()->read('User.RootLogged');
            $user_managed = $this->request->session()->read('User.ToManage');

            if ($user_admin) {
                $this->user_logged = $user_managed;
            }

            $clientes_ids = [];

            $clientes_ids[] = $clientes_id;
            $brindes_habilitados = $this->ClientesHasBrindesHabilitados->getBrindesHabilitadosByClienteId($clientes_ids, []);

            // para pegar o saldo atual, preciso pegar o id de todas as unidades de uma rede e informar

            $redes_id = $this->RedesHasClientes->getRedesHasClientesByClientesId($clientes_id)->redes_id;

            $redes_has_clientes = $this->RedesHasClientes->getRedesHasClientesByRedesId($redes_id);

            $clientes_ids = [];

            foreach ($redes_has_clientes->toArray() as $key => $value) {
                $clientes_ids[] = $value->clientes_id;
            }

            $saldo_atual = $this->Pontuacoes->getSumPontuacoesOfUsuario($this->user_logged['id'], $clientes_ids);

            $this->set(compact('brindes_habilitados', 'saldo_atual', 'redes_id'));
            $this->set('_serialize', ['brindes_habilitados', 'saldo_atual', 'redes_id']);

        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao obter novo nome para comprovante: {0} em: {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Action para resgatar brindes comuns do sistema
     *
     * @param  int $brindes_habilitados_id Id de brindes habilitados
     * @return void
     */
    public function resgatarBrinde(int $brindes_habilitados_id)
    {
        try {
            $user_admin = $this->request->session()->read('User.RootLogged');
            $user_managed = $this->request->session()->read('User.ToManage');

            if ($user_admin) {
                $this->user_logged = $user_managed;
            }

            $brinde_habilitado = $this->ClientesHasBrindesHabilitados->getBrindeHabilitadoById($brindes_habilitados_id);

            // para pegar o saldo atual, preciso pegar o id de todas as unidades de uma rede e informar

            $redes_id = $this->RedesHasClientes->getRedesHasClientesByClientesId($brinde_habilitado->clientes_id)->redes_id;

            $redes_has_clientes = $this->RedesHasClientes->getRedesHasClientesByRedesId($redes_id);

            $clientes_ids = [];

            foreach ($redes_has_clientes->toArray() as $key => $value) {
                $clientes_ids[] = $value->clientes_id;
            }

            $saldo_atual = $this->Pontuacoes->getSumPontuacoesOfUsuario($this->user_logged['id'], $clientes_ids);

            if ($this->request->is(['post', 'put'])) {
                // verifica se o usuário tem saldo suficiente

                $data = $this->request->getData();

                $quantidade = $data['quantidade'];

                if ($quantidade > 0) {
                    // obtem valor total de pontos necessários

                    $valor_produto = $brinde_habilitado->brinde_habilitado_preco_atual->preco * $quantidade;

                    $saldo_restante = $saldo_atual - $valor_produto;

                    if ($saldo_restante < 0) {
                        // saldo ficou negativo, cliente não tem pontos suficientes

                        $this->Flash->error(
                            __('Não é possível resgatar o(s) brinde(s) solicitados, você não possui saldo suficiente. Saldo atual: {0}. Saldo Necessário: {1}.', $saldo_atual, $valor_produto)
                        );
                    } else {
                        // verifica se tem quantidade em estoque suficiente

                        $estoque_atual = $brinde_habilitado->estoque[0];

                        if ($estoque_atual < $quantidade) {
                            $this->Flash->error(
                                __('Estabelecimento não possui quantidade solicitada. Estoque atual: {0}. Estoque solicitado: {1}.', $estoque_atual, $quantidade)
                            );
                        } else {
                            // Não houve impedimentos. Gera o pedido e redireciona para impressão

                            // Guarda os cupons. A retirada dos produtos (diminuição do estoque)
                            // será somente no momento do resgate físico

                            if ($cupom = $this->Cupons->addCuponsBrindesForUsuario($brinde_habilitado, $this->user_logged['id'], $quantidade)) {
                                // Adiciona novo registro de brinde ao usuário

                                $this->UsuariosHasBrindes->addUsuarioHasBrindes(
                                    $cupom->usuarios_id,
                                    $cupom->clientes_has_brindes_habilitados_id,
                                    $cupom->quantidade,
                                    $cupom->valor_pago,
                                    $cupom->id
                                );

                                // Salvou os registros, redireciona para a tela de emissão

                                $this->Flash->success(Configure::read('messageSavedSuccess'));

                                // pega o primeiro cupom guardado e pega seu cupom emitido

                                $cupom_emitido = $cupom->cupom_emitido;

                                return $this->redirect(['controller' => 'Cupons', 'action' => 'imprime_brinde_comum', $cupom_emitido]);
                            }
                        }
                    }
                } else {
                    $this->Flash->error('Não é possível resgatar 0 brindes.');
                }
            }

            $this->set(compact('brinde_habilitado', 'redes_id', 'saldo_atual'));
            $this->set('_serialize', ['brinde_habilitado', 'redes_id', 'saldo_atual']);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao obter novo nome para comprovante: {0} em: {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * ------------------------------------------------------------
     * Métodos Comuns
     * ------------------------------------------------------------
     */

    /**
     * ------------------------------------------------------------
     * Relatórios (Dashboard Admin RTI)
     * ------------------------------------------------------------
     */

    /**
     * Exibe a action de Relatorio Brindes Habilitados por Redes
     *
     * @return \Cake\Network\Response|null|void
     */
    public function relatorioBrindesHabilitadosRedes()
    {
        $redesList = $this->Redes->getRedesList();

        $whereConditions = array();

        $redesArrayIds = array();

        foreach ($redesList as $key => $redeItem) {
            $redesArrayIds[] = $key;
        }

        if ($this->request->is(['post'])) {
            $data = $this->request->getData();

            if (strlen($data['redes_id']) > 0) {
                $redesArrayIds = ['id' => $data['redes_id']];
            }

            if (strlen($data['nome']) > 0) {
                $whereConditions[] = ["brindes.nome like '%" . $data['nome'] . "%'"];
            }

            if (strlen($data['ilimitado']) > 0) {
                $whereConditions[] = ["brindes.ilimitado" => (bool)$data['ilimitado']];
            }

            if (strlen($data['habilitado']) > 0) {
                $whereConditions[] = ["brindes.habilitado" => (bool)$data['habilitado']];
            }

            $dataHoje = DateTimeUtil::convertDateToUTC((new DateTime('now'))->format('Y-m-d H:i:s'));
            $dataInicial = strlen($data['auditInsertInicio']) > 0 ? DateTimeUtil::convertDateToUTC($data['auditInsertInicio'], 'd/m/Y') : null;
            $dataFinal = strlen($data['auditInsertFim']) > 0 ? DateTimeUtil::convertDateToUTC($data['auditInsertFim'], 'd/m/Y') : null;

            // Data de Criação Início e Fim
            if (strlen($data['auditInsertInicio']) > 0 && strlen($data['auditInsertFim']) > 0) {

                if ($dataInicial > $dataFinal) {
                    $this->Flash->error(__(Configure::read('messageDateRangeInvalid')));
                } else if ($dataInicial > $dataHoje) {
                    $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid', 'Data de Início')));
                } else {
                    $whereConditions[] = ['brindes.audit_insert BETWEEN "' . $dataInicial . '" and "' . $dataFinal . '"'];
                }

            } else if (strlen($data['auditInsertInicio']) > 0) {

                if ($dataInicial > $dataHoje) {
                    $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Início'));
                } else {
                    $whereConditions[] = ['brindes.audit_insert >= ' => $dataInicial];
                }

            } else if (strlen($data['auditInsertFim']) > 0) {

                if ($dataFinal > $dataHoje) {
                    $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Fim'));
                } else {
                    $whereConditions[] = ['brindes.audit_insert <= ' => $dataFinal];
                }
            }
        }

         // Monta o Array para apresentar em tela
        $redes = array();

        foreach ($redesArrayIds as $key => $value) {
            $arrayWhereConditions = $whereConditions;

            $redesHasClientesIds = array();

            $usuariosIds = array();

            $rede = $this->Redes->getRedeById((int)$value);

            $redeItem = array();

            $redeItem['id'] = $rede->id;
            $redeItem['nome_rede'] = $rede->nome_rede;
            $redeItem['brindes'] = array();

            $clientesIds = [];

            // obtem os ids das unidades para saber quais brindes estão disponíveis
            foreach ($rede->redes_has_clientes as $key => $value) {
                $clientesIds[] = $value->clientes_id;
            }

            /*
             * NOTA: tenho que pegar todas as unidades de uma rede
             * uma vez que eu pegue cada unidade, eu tenho que
             * pegar todos os brindes aos quais estão nela.
             */

            $brindesHabilitadosReturn = array();

            $cliente = null;
            foreach ($clientesIds as $key => $clienteId) {

                $brindesHabilitadosArray = $this->ClientesHasBrindesHabilitados->getBrindesHabilitadosByClienteId(
                    [$clienteId],
                    $arrayWhereConditions
                )->toArray();

                if (sizeof($brindesHabilitadosArray) > 0)
                    $brindesHabilitadosReturn[] = $brindesHabilitadosArray;
            }

            $redeItem['clientesBrindes'] = $brindesHabilitadosReturn;

            unset($arrayWhereConditions);

            array_push($redes, $redeItem);
        }

        $arraySet = [
            'redesList',
            'redes'
        ];

        $this->set(compact($arraySet));
    }

    /**
     * ------------------------------------------------------------------
     * Métodos de API
     * ------------------------------------------------------------------
     */

    /**
     * ClientesHasBrindesHabilitados::getBrindesUnidadeAPI
     *
     * Obtem todos os Brindes Habilitados de uma Unidade
     *
     * @param $clientes_id Id da unidade que deseja adquirir o brinde
     * @param $tipos_brindes_redes_id Id do tipo de brinde da rede que deseja filtrar
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 23/04/2018
     *
     * @return json Brindes Habilitados
     */
    public function getBrindesUnidadeAPI()
    {
        $mensagem = array();
        $brindes = null;
        $count = 0;

        try {
            if ($this->request->is(['post'])) {
                $data = $this->request->getData();
                $clientesId = isset($data['clientes_id']) ? $data['clientes_id'] : null;

                $precoMin = isset($data["preco_min"]) ? (float)$data["preco_min"] : null;
                $precoMax = isset($data["preco_max"]) ? (float)$data["preco_max"] : null;

                if (empty($clientesId)) {
                    $mensagem = array(
                        "status" => 0,
                        "message" => Configure::read("messageOperationFailureDuringProcessing"),
                        "errors" => array("É necessário informar um Ponto de Atendimento para obter os brindes do Ponto de Atendimento!")
                    );

                    $arraySet = [
                        "mensagem"
                    ];

                    $this->set(compact($arraySet));
                    $this->set("_serialize", $arraySet);

                    return;
                }

                $tiposbrindesRedesid = !empty($data["tipos_brindes_redes_id"]) ? $data["tipos_brindes_redes_id"] : null;

                $whereConditionsBrindes = array();

                if (!empty($data["nome"])) {
                    $whereConditionsBrindes[] = array("Brindes.nome like '%{$data["nome"]}%'");
                }

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

                $tiposBrindesClientesIds = array();

                if (!empty($tiposbrindesRedesId) && ($tiposbrindesRedesId > 0)) {
                    $tiposBrindesClientesIds = $this->TiposBrindesClientes->findTiposBrindesClienteByClientesIdTiposBrindesRedesId($clientesId, $tiposbrindesRedesId);
                }

                $tiposBrindesClientesIds = sizeof($tiposBrindesClientesIds) > 0 ? $tiposBrindesClientesIds : array();

                $tiposBrindesClientesIds = isset($tiposBrindesClientesIds) ? $tiposBrindesClientesIds : array();
                // Campos para retorno à API
                $filterTiposBrindesClientesColumns = array(
                    "id",
                    "tipos_brindes_redes_id",
                    "clientes_id",
                    "habilitado"
                );

                $resultado = $this->ClientesHasBrindesHabilitados->getBrindesPorClienteId(
                    $clientesId,
                    $tiposBrindesClientesIds,
                    $whereConditionsBrindes,
                    $precoMin,
                    $precoMax,
                    $orderConditions,
                    $paginationConditions,
                    $filterTiposBrindesClientesColumns
                );

                // DebugUtil::printArray($resultado);

                $mensagem = $resultado["mensagem"];
                $brindes = $resultado["brindes"];

            }
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $messageString = __("Não foi possível obter dados de brindes da unidade selecionada!");
            $messageStringDebug = __("{0} - {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $messageString, $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write("error", $messageStringDebug);
            Log::write("error", $trace);

            $mensagem = array('status' => false, 'message' => $messageString, 'errors' => $trace);
        }

        $arraySet = array(
            'mensagem',
            'brindes',
        );

        $this->set(compact($arraySet));
        $this->set('_serialize', $arraySet);
    }

    /**
     * ------------------------------------------------------------------
     * Métodos Comuns
     * ------------------------------------------------------------------
     */

    /**
     * Before render callback.
     *
     * @param \App\Controller\Event\Event $event The beforeRender event.
     *
     * @return \Cake\Network\Response|null|void
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
    }

    /**
     * Função herdada de Initialize
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
    }
}
