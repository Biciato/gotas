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
    protected $usuarioLogado = null;


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
        $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
        $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

        if ($usuarioAdministrador) {
            $this->usuarioLogado = $usuarioAdministrar;
        }

        $rede = $this->request->session()->read('Rede.Grupo');

        $clientesIdsArray = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($rede->id, $this->usuarioLogado['id'], false);

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
    public function configurarBrindesUnidade(int $clientesId)
    {
        $sessaoUsuario = $this->getSessionUserVariables();
        $usuarioLogado = $sessaoUsuario["usuarioLogado"];
        $rede = $sessaoUsuario["rede"];
        $usuarioAdministrador = $sessaoUsuario["usuarioAdministrador"];
        $usuarioAdministrar =  $sessaoUsuario["usuarioAdministrar"];

        if ($usuarioAdministrador && $usuarioLogado["tipo_perfil"] == PROFILE_TYPE_ADMIN_DEVELOPER) {
            $this->usuarioLogado = $usuarioAdministrar;
            $usuarioLogado = $usuarioAdministrar;
        }

        $temAcesso = $this->securityUtil->checkUserIsClienteRouteAllowed($usuarioLogado, $this->Clientes, $this->ClientesHasUsuarios, [$clientesId], $rede["id"]);

        // DebugUtil::print($temAcesso);
        if (!$temAcesso) {
            return $this->securityUtil->redirectUserNotAuthorized($this, $this->usuarioLogado);
        }

        // obtem os brindes habilitados (e não habilitados) da unidade
        $brindesConfigurar = $this->ClientesHasBrindesHabilitados->getTodosBrindesByClienteId([$clientesId]);

        // DebugUtil::print($brindesConfigurar);

        if (!$brindesConfigurar["mensagem"]["status"]) {
            $this->Flash->error($brindesConfigurar["mensagem"]["message"]);
            $brindesConfigurar = array();
        } else {
            $brindesConfigurarArrayRetorno = array();
            $brindesConfigurar = $brindesConfigurar["data"];

            foreach ($brindesConfigurar as $brinde) {
                $brinde["pendente_configuracao"] = empty($brinde["brinde_vinculado"]["tipo_codigo_barras"]);
                $brindesConfigurarArrayRetorno[] = $brinde;
            }

            $brindesConfigurar = $brindesConfigurarArrayRetorno;
        }

        $arraySet = array('brindesConfigurar', 'clientesId', "usuarioLogado");
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
        $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
        $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

        if ($usuarioAdministrador) {
            $this->usuarioLogado = $usuarioAdministrar;
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
        $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
        $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

        if ($usuarioAdministrador) {
            $this->usuarioLogado = $usuarioAdministrar;
        }

        $cliente = $this->securityUtil->checkUserIsClienteRouteAllowed($this->usuarioLogado, $this->Clientes, $this->ClientesHasUsuarios);

        $rede = $this->request->session()->read('Rede.Grupo');

        $matriz = $this->RedesHasClientes->findMatrizOfRedesByRedesId($rede->id);

        $unidadesIds = [];

        $clientes_ids = [];

        // Pega unidades que tem acesso

        $unidadesIds = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($rede->id, $this->usuarioLogado['id']);

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
    private function _alteraEstadoBrinde(int $brindesId, int $clientesId, $status)
    {
        $sessaoUsuario = $this->getSessionUserVariables();

        $usuarioAdministrador = $sessaoUsuario["usuarioAdministrador"];
        $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"];
        $usuarioLogado = $sessaoUsuario["usuarioLogado"];

        if ($usuarioAdministrador) {
            $this->usuarioLogado = $usuarioAdministrar;
        }

        /**
         * Verifica se a unidade do cliente tem o tipo de brinde configurado.
         * Sem isso, não é possível continuar.
         */

        // verifica se o cliente tem o brinde habilitado
        $clienteHasBrindeHabilitado = $this->ClientesHasBrindesHabilitados->getBrindeHabilitadoByBrindeClienteId($brindesId, $clientesId);

        $brinde = $this->Brindes->getBrindeById($brindesId);

        $tiposBrindesCliente = $this->TiposBrindesClientes->getTiposBrindesClientesByTiposBrindesRedes($brinde["tipos_brindes_redes_id"], $clientesId);

        // die();
        // die($tiposBrindesCliente);
        // die($clienteHasBrindeHabilitado);

        if (empty($tiposBrindesCliente)) {

            $error = $status == 1 ? Configure::read("messageEnableError") : Configure::read("messageDisableError");

            $this->Flash->error(__("{0} - {1}", $error, "Este Posto de Atendimento não possui este Tipo de Brinde configurado para o Brinde!"));

            return $this->redirect(['action' => 'configurar_brindes_unidade', $clientesId]);
        }

        if (empty($clienteHasBrindeHabilitado)) {
            $clienteHasBrindeHabilitado = $this->ClientesHasBrindesHabilitados->newEntity();
            $clienteHasBrindeHabilitado["brindes_id"] = $brindesId;
            $clienteHasBrindeHabilitado["clientes_id"] = $clientesId;
            $clienteHasBrindeHabilitado["tipos_brindes_clientes_id"] = $tiposBrindesCliente["id"];
        } else if (empty($clienteHasBrindeHabilitado["tipos_brindes_clientes_id"])) {
            // Atualiza o vínculo se estiver nulo
            $clienteHasBrindeHabilitado["tipos_brindes_clientes_id"] = $tiposBrindesCliente["id"];
        }

        $clienteHasBrindeHabilitado["habilitado"] = $status;
        $clienteHasBrindeHabilitado = $this->ClientesHasBrindesHabilitados->save($clienteHasBrindeHabilitado);

        // DebugUtil::print($clienteHasBrindeHabilitado);
        if ($clienteHasBrindeHabilitado) {
            /* Se for true, verificar se é registro novo.
             * Se for, é necessário incluir novo preço, definir estoque
             */

            if ($status) {
                /* estoque só deve ser criado para registro nas
                 * seguintes situações.
                 *
                 * 1 - O Brinde está sendo vinculado a um cadastro de loja
                 *     no sistema (Isto é, se ele não foi anteriormente )
                 * 2 - Não é ilimitado
                 * 3 - Se não houver cadastro anterior
                 */
                $brinde
                    = $this->Brindes->getBrindeById(
                    $clienteHasBrindeHabilitado->brindes_id
                );

                if (!$brinde["ilimitado"]) {
                    $estoque = $this->ClientesHasBrindesEstoque
                        ->getEstoqueForBrinde(
                            $clienteHasBrindeHabilitado->id,
                            0
                        );

                    if (is_null($estoque)) {
                        // Não tem estoque, criar novo registro vazio
                        $this->ClientesHasBrindesEstoque->addBrindeEstoque($clienteHasBrindeHabilitado->id, $this->usuarioLogado['id'], 0, 0);
                    }
                }
                    // brinde habilitado, verificar se já tem preço. Se não tiver, cadastra
                $precos = $this->ClientesHasBrindesHabilitadosPreco->getUltimoPrecoBrindeId($clienteHasBrindeHabilitado->id);

                if (!isset($precos)) {
                    $this->ClientesHasBrindesHabilitadosPreco->addBrindePreco(
                        $clienteHasBrindeHabilitado["id"],
                        $clientesId,
                        STATUS_AUTHORIZATION_PRICE_AUTHORIZED,
                        $brinde["preco_padrao"],
                        $brinde["valor_moeda_venda"]
                    );
                }
            }
            $this->Flash->success(__(Configure::read('messageSavedSuccess')));
        } else {
                // Erro ao gravar
            $this->Flash->error(__(Configure::read('messageSavedError')));
        }

        if ($status && strlen($clienteHasBrindeHabilitado["tipo_codigo_barras"]) == 0) {
            return $this->redirect(['action' => 'configurar_tipo_emissao', $clienteHasBrindeHabilitado->id]);
        }

        return $this->redirect(['action' => 'configurar_brindes_unidade', $clientesId]);
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
        $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
        $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

        if ($usuarioAdministrador) {
            $this->usuarioLogado = $usuarioAdministrar;
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
        $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
        $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

        if ($usuarioAdministrador) {
            $this->usuarioLogado = $usuarioAdministrar;
        }

        $this->securityUtil->checkUserIsClienteRouteAllowed($this->usuarioLogado, $this->Clientes, $this->ClientesHasUsuarios);

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
        $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
        $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

        if ($usuarioAdministrador) {
            $this->usuarioLogado = $usuarioAdministrar;
        }

        $clienteAdministrar = $this->request->session()->read('Rede.PontoAtendimento');

        if (isset($clienteAdministrar)) {
            $cliente = $clienteAdministrar;
        }

        $rede = $this->request->session()->read('Rede.Grupo');

        $clientes_ids = [];

        // pega todas as unidades que o usuário possui acesso

        $unidadesIds = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($rede->id, $this->usuarioLogado['id']);

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
                = $this->ClientesHasBrindesHabilitadosPreco->getUltimoPrecoBrindeId($value['id']);

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
            $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
            $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

            if ($usuarioAdministrador) {
                $this->usuarioLogado = $usuarioAdministrar;
            }

            $clientes_ids = [];

            $clientes_ids[] = $clientes_id;
            $brindes_habilitados = $this->ClientesHasBrindesHabilitados->getBrindesHabilitadosByClienteId($clientes_ids, []);

            // para pegar o saldo atual, preciso pegar o id de todas as unidades de uma rede e informar

            $redesId = $this->RedesHasClientes->getRedesHasClientesByClientesId($clientes_id)["redes_id"];

            $redes_has_clientes = $this->RedesHasClientes->getRedesHasClientesByRedesId($redesId);

            $clientes_ids = [];

            foreach ($redes_has_clientes->toArray() as $key => $value) {
                $clientes_ids[] = $value->clientes_id;
            }

            $saldo_atual = $this->Pontuacoes->getSumPontuacoesOfUsuario($this->usuarioLogado['id'], $redesId, $clientes_ids);

            $this->set(compact('brindes_habilitados', 'saldo_atual', 'redesId'));
            $this->set('_serialize', ['brindes_habilitados', 'saldo_atual', 'redesId']);

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
     *
     * @deprecated 1.0
     *
     */
    public function resgatarBrinde(int $brindes_habilitados_id)
    {
        try {
            $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
            $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

            if ($usuarioAdministrador) {
                $this->usuarioLogado = $usuarioAdministrar;
            }

            $brinde_habilitado = $this->ClientesHasBrindesHabilitados->getBrindeHabilitadoById($brindes_habilitados_id);

            // para pegar o saldo atual, preciso pegar o id de todas as unidades de uma rede e informar

            $redesId = $this->RedesHasClientes->getRedesHasClientesByClientesId($brinde_habilitado->clientes_id)["redes_id"];

            $redes_has_clientes = $this->RedesHasClientes->getRedesHasClientesByRedesId($redesId);

            $clientes_ids = [];

            foreach ($redes_has_clientes->toArray() as $key => $value) {
                $clientes_ids[] = $value->clientes_id;
            }

            $saldo_atual = $this->Pontuacoes->getSumPontuacoesOfUsuario($this->usuarioLogado['id'], $redesId, $clientes_ids);

            if ($this->request->is(['post', 'put'])) {
                // verifica se o usuário tem saldo suficiente

                $data = $this->request->getData();

                // $quantidade = $data['quantidade'];
                // Ajuste solicitado pelo Samuel, quantidade sempre será 1
                $quantidade = 1;

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

                            if ($cupom = $this->Cupons->addCuponsBrindesForUsuario($brinde_habilitado, $this->usuarioLogado['id'], $quantidade)) {
                                // Adiciona novo registro de brinde ao usuário

                                $this->UsuariosHasBrindes->addUsuarioHasBrindes(
                                    $redesId,
                                    $clientes_ids[0],
                                    $cupom["usuarios_id"],
                                    $cupom->clientes_has_brindes_habilitados_id,
                                    $cupom->quantidade,
                                    $cupom["valor_pago_gotas"],
                                    $cupom["valor_pago_reais"],
                                    TYPE_PAYMENT_POINTS,
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

            $this->set(compact('brinde_habilitado', 'redesId', 'saldo_atual'));
            $this->set('_serialize', ['brinde_habilitado', 'redesId', 'saldo_atual']);
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
