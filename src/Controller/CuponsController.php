<?php
namespace App\Controller;

use \DateTime;
use \Exception;
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
use App\Custom\RTI\DateTimeUtil;
use App\Custom\RTI\TimeUtil;
use App\Custom\RTI\ResponseUtil;

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
        $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
        $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

        if ($usuarioAdministrador) {
            $this->usuarioLogado = $usuarioAdministrar;
        }

        $cliente = $this->securityUtil->checkUserIsClienteRouteAllowed($this->usuarioLogado, $this->Clientes, $this->ClientesHasUsuarios);

        $cliente = $this->request->session()->read("Rede.PontoAtendimento");

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

        $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
        $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

        if ($usuarioAdministrador) {
            $this->usuarioLogado = $usuarioAdministrar;
        }
        $usuarioLogado = $this->usuarioLogado;

        $usuario = $this->Usuarios->newEntity();
        $transportadora = $this->Transportadoras->newEntity();
        $veiculo = $this->Veiculos->newEntity();

        $funcionario = $this->Usuarios->getUsuarioById($this->usuarioLogado['id']);

        $rede = $this->request->session()->read('Rede.Grupo');

        // Pega unidades que tem acesso
        $clientes_ids = [];

        $unidades_ids = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($rede->id, $this->usuarioLogado['id'], false);

        foreach ($unidades_ids as $key => $value) {
            $clientes_ids[] = $key;
        }

        // No caso do funcionário, ele só estará em
        // uma unidade, então pega o cliente que ele estiver

        $cliente = $this->Clientes->getClienteById($clientes_ids[0]);

        $clientes_id = $cliente->id;

        // o estado do funcionário é o local onde se encontra o estabelecimento.
        $estado_funcionario = $cliente["estado"];

        $transportadoraPath = "TransportadorasHasUsuarios.Transportadoras.";
        $veiculoPath = "UsuariosHasVeiculos.Veiculos.";

        $arraySet = array(
            "urlRedirectConfirmacao",
            "usuarioLogado",
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
        $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
        $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

        if ($usuarioAdministrador) {
            $this->usuarioLogado = $usuarioAdministrar;
        }

        $cliente = $this->securityUtil->checkUserIsClienteRouteAllowed($this->usuarioLogado, $this->Clientes, $this->ClientesHasUsuarios);
    }

    /**
     * Imprime Bilhete Smart Shower Ticket
     *
     * @return void
     * @author
     **/
    public function brindeShower()
    {
        $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
        $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

        if ($usuarioAdministrador) {
            $this->usuarioLogado = $usuarioAdministrar;
        }

        $cliente = $this->securityUtil->checkUserIsClienteRouteAllowed($this->usuarioLogado, $this->Clientes, $this->ClientesHasUsuarios);

        $clienteAdministrar = $this->request->session()->read('Rede.PontoAtendimento');

        if (!is_null($clienteAdministrar)) {
            $cliente = $clienteAdministrar;
        }

        $rede = $this->request->session()->read('Rede.Grupo');

        $unidades_ids = [];

        $clientes_ids = [];

        // Se o perfil é até administrador regional, pode filtrar por todas as unidades / unidades que tem acesso
        if ($this->usuarioLogado['tipo_perfil'] <= Configure::read('profileTypes')['AdminRegionalProfileType']) {
            $unidades_ids = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($rede->id, $this->usuarioLogado['id']);

            foreach ($unidades_ids as $key => $value) {
                $clientes_ids[] = $key;
            }
        } else {
            $unidades_ids = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($rede->id, $this->usuarioLogado['id']);

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

        $clientes_has_brindes_habilitados = $this->ClientesHasBrindesHabilitados->getBrindesHabilitadosByClienteId(
            $clientes_ids,
            $conditions
        );

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
        $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
        $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

        if ($usuarioAdministrador) {
            $this->usuarioLogado = $usuarioAdministrar;
        }

        $cliente = $this->securityUtil->checkUserIsClienteRouteAllowed($this->usuarioLogado, $this->Clientes, $this->ClientesHasUsuarios);

        $clienteAdministrar = $this->request->session()->read('Rede.PontoAtendimento');

        if (!is_null($clienteAdministrar)) {
            $cliente = $clienteAdministrar;
        }

        $rede = $this->request->session()->read('Rede.Grupo');

        $unidades_ids = [];

        $clientes_ids = [];

        // Se o perfil é até administrador regional, pode filtrar por todas as unidades / unidades que tem acesso
        if ($this->usuarioLogado['tipo_perfil'] <= Configure::read('profileTypes')['AdminRegionalProfileType']) {
            $unidades_ids = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($rede->id, $this->usuarioLogado['id']);

            foreach ($unidades_ids as $key => $value) {
                $clientes_ids[] = $key;
            }
        } else {
            $unidades_ids = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($rede->id, $this->usuarioLogado['id']);

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
        $sessaoUsuario = $this->getSessionUserVariables();
        $usuarioAdministrador = $sessaoUsuario["usuarioAdministrador"];
        $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"];

        if ($usuarioAdministrador) {
            $this->usuarioLogado = $usuarioAdministrar;
            $usuarioLogado = $usuarioAdministrar;
        }

        // pega a rede e as unidades que o usuário tem acesso

        $rede = $sessaoUsuario["rede"];

        // Pega unidades que tem acesso
        $clientesIds = [];

        $unidadesAtendimento = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($rede->id, $this->usuarioLogado['id'], false);

        foreach ($unidadesAtendimento as $key => $value) {
            $clientesIds[] = $key;
        }

        $nomeUsuarios = null;
        $unidadeSelecionado = null;
        $brindeSelecionado = null;
        $nomeBrindes = null;
        $valorMinimo = null;
        $valorMaximo = null;
        $date = 'd/m/Y';
        $dataFim = date($date);
        $dataInicio = date($date, strtotime("-30 day"));
        $dataFimPesquisa = date($date);
        $dataInicioPesquisa = date($date, strtotime("-30 day"));

        $brindes = $this->Brindes->findBrindes($rede["id"]);
        // @todo usar o findBrindes
        // $brindes = $this->Brindes->getBrindesByClientes($clientesIds)->find("list");

        $data = array();

        if ($this->request->is('post')) {
            $data = $this->request->getData();

            $this->request->session()->write("QueryConditions", $data);
        } else {
            $data = $this->request->session()->read("QueryConditions");
        }

        if (!empty($data) && count($data) > 0) {

            $unidadeSelecionado = strlen($data["unidadeSelecionado"]) > 0 ? $data["unidadeSelecionado"] : null;
            $brindeSelecionado = strlen($data["brindeSelecionado"]) > 0 ? $data["brindeSelecionado"] : null;
            $nomeUsuarios = !empty($data["nomeUsuarios"]) ? $data["nomeUsuarios"] : null;
            $valorMinimo = strlen($data["valorMinimo"]) > 0 ? str_replace(",", "", $data["valorMinimo"]) : null;
            $valorMaximo = strlen($data["valorMaximo"]) > 0 ? str_replace(",", "", $data["valorMaximo"]) : null;
            $dataInicio = !empty($data["dataInicio"]) ? $data["dataInicio"] : null;
            $dataFim = !empty($data["dataFim"]) ? $data["dataFim"] : null;

            if (!empty($dataInicio)) {
                $dataInicioPesquisa = date_format(date_create_from_format("d/m/Y", $dataInicio), "Y-m-d");
            }

            if (!empty($dataFim)) {
                $dataFimPesquisa = date_format(date_create_from_format("d/m/Y", $dataFim), "Y-m-d");
            }
        }

        if (empty($valorMinimo)) {
            $valorMinimo = 0;
        }

        if (empty($valorMaximo)) {
            $valorMaximo = 9999999999999999;
        }

        if ($valorMinimo > $valorMaximo) {
            $this->Flash->error("Valor Mínimo não pode ser maior que Valor Máximo!");
        }

        if ($dataInicioPesquisa > $dataFimPesquisa) {
            $this->Flash->error("Data de Início não pode ser maior que Data de Fim!");
        }

        if (!empty($unidadeSelecionado)) {
            $clientesIds = [];
            $clientesIds[] = (int)$unidadeSelecionado;
        }

        $cupons = array();
        // @todo ajustar
        // $cupons = $this->Cupons->getExtratoCuponsClientes($clientesIds, $brindeSelecionado, $nomeUsuarios, $valorMinimo, $valorMaximo, $dataInicioPesquisa, $dataFimPesquisa);

        // Paginação
        // $cupons = $this->Paginate($cupons, array('order' => ['Cupons.data' => 'desc'], 'limit' => 10));

        $arraySet = array("cupons", "unidadesAtendimento", "brindes", "brindeSelecionado", "dataFim", "dataInicio");
        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);
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
        $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
        $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

        if ($usuarioAdministrador) {
            $this->usuarioLogado = $usuarioAdministrar;
        }

        $rede = $this->request->session()->read('Rede.Grupo');

        $cliente = $this->securityUtil->checkUserIsClienteRouteAllowed($this->usuarioLogado, $this->Clientes, $this->ClientesHasUsuarios, array(), $rede["id"]);

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
            $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
            $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

            if ($usuarioAdministrador) {
                $this->usuarioLogado = $usuarioAdministrar;
            }

            $usuarioLogado = $this->usuarioLogado;

            $cupons = $this->Cupons->getCuponsByCupomEmitido($cupom_emitido);

            $cupons_data = $this->prepareCuponsData($cupons);

            $cupom_id = $cupons_data['cupom_id'];
            $cupom_emitido = $cupons_data['cupom_emitido'];
            $cliente_final = $cupons_data['cliente_final'];
            $brindes_id = $cupons_data['brindes_id'];
            $clientes_id = $cupons_data['clientes_id'];
            $data_impressao = $cupons_data['data_impressao'];
            $produtos = $cupons_data['produtos'];
            $redes_id = $cupons_data['redes_id'];

            $arraySet = [
                'cupom_id',
                'cupom_emitido',
                'cliente_final',
                'brindes_id',
                'clientes_id',
                'cupons',
                'data_impressao',
                'produtos',
                'redes_id',
                'usuarioLogado',
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
            $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
            $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

            if ($usuarioAdministrador) {
                $this->usuarioLogado = $usuarioAdministrar;
            }

            $usuarioLogado = $this->usuarioLogado;

            $cupons = $this->Cupons->getCuponsByCupomEmitido($cupom_emitido);

            $cupons_data = $this->prepareCuponsData($cupons);

            $cupom_id = $cupons_data['cupom_id'];
            $cupom_emitido = $cupons_data['cupom_emitido'];
            $cliente_final = $cupons_data['cliente_final'];
            $brindes_id = $cupons_data['brindes_id'];
            $clientes_id = $cupons_data['clientes_id'];
            $data_impressao = $cupons_data['data_impressao'];
            $produtos = $cupons_data['produtos'];
            $redes_id = $cupons_data['redes_id'];

            $arraySet = [
                'cupom_id',
                'cupom_emitido',
                'cliente_final',
                'brindes_id',
                'cupons',
                'data_impressao',
                'produtos',
                'redes_id',
                'usuarioLogado',
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
     * Prepara dados para extração de array de cupons
     *
     * @param \App\Model\Entity\Cupom $cupons
     * @return void
     */
    private function prepareCuponsData($cupons)
    {
        $data = null;

        if (count($cupons->toArray()) > 0) {

            $cliente_final = $this->Usuarios->getUsuarioById($cupons->toArray()[0]->usuarios_id);

            $cupom_emitido = $cupons->toArray()[0]->cupom_emitido;
            $data_impressao = $cupons->toArray()[0]->data->format('d/m/Y H:i:s');
            $cupom_id = $cupons->toArray()[0]->id;
            $brindes_id = $cupons->toArray()[0]->clientes_has_brindes_habilitado->id;
            $redes_id = $cupons->toArray()[0]->cliente->rede_has_cliente->redes_id;
            $clientes_id = $cupons->toArray()[0]->cliente->rede_has_cliente->clientes_id;

            // percorrer o cupom e pegar todos os produtos

            foreach ($cupons as $key => $value) {
                $produto = null;
                $produto['qte'] = $value->quantidade;
                $produto['nome'] = $value->clientes_has_brindes_habilitado->brinde->nome;
                $produto['valor_pago_gotas'] = $value->valor_pago_gotas;
                $produto['valor_pago_reais'] = $value->valor_pago_reais;

                $produtos[] = $produto;
            }

            $data = [
                'cliente_final' => $cliente_final,
                'clientes_id' => $clientes_id,
                'brindes_id' => $brindes_id,
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
        $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
        $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

        if ($usuarioAdministrador) {
            $this->usuarioLogado = $usuarioAdministrar;
        }

        $usuarioLogado = $this->usuarioLogado;

        $usuario = $this->Usuarios->getUsuarioById($usuarioLogado['id']);

        $cupom = $this->Cupons->getCuponsById($cupons_id);

        $this->set(compact('cupom', 'usuarioLogado'));
        $this->set('_serialize', ['cupom', 'usuarioLogado']);
    }

    /**
     * Action para resgate de cupons (view de funcionário)
     *
     * @return void
     */
    public function resgateCupons()
    {
        $sessaoUsuario = $this->getSessionUserVariables();
        $usuarioAdministrador = $sessaoUsuario["usuarioAdministrador"];
        $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"];
        $usuarioLogado = $sessaoUsuario["usuarioLogado"];

        $arraySet = array("usuarioLogado");

        $this->set(compact($arraySet));
        $this->set($arraySet);
    }

    /**
     * Action para Relatorio de Caixa de Funcionário
     *
     * @return void
     */
    public function relatorioCaixaFuncionario()
    {
        $arraySet = array(
            "dadosVendaFuncionarios",
            "totalGeral",
            "tituloTurno",
            "dataInicio",
            "dataFim"
        );

        $sessaoUsuario = $this->getSessionUserVariables();

        $filtrarTurnoAnteriorList = array(
            1 => "Sim",
            0 => "Nâo"
        );

        $filtrarTurnoAnterior = null;
        $rede = $sessaoUsuario["rede"];
        $cliente = $sessaoUsuario["cliente"];

        if ($sessaoUsuario["usuarioAdministrar"]) {
            $this->usuarioLogado = $sessaoUsuario["usuarioAdministrar"];
        }

        $dadosVendaFuncionarios = array();
        $totalGeral = array();

        $tituloTurno = "";

        $quadroHorariosCliente = $this->ClientesHasQuadroHorario->getHorariosCliente($rede["id"], $cliente["id"]);
        $quadroHorariosCliente = $quadroHorariosCliente->toArray();
        $quadroHorariosClienteLength = count($quadroHorariosCliente);

        if (empty($quadroHorariosCliente) || $quadroHorariosClienteLength == 0) {
            $this->set(compact($arraySet));
            $this->set("_serialize", $arraySet);

            return $this->Flash->error(MESSAGE_ESTABLISHMENT_WITHOUT_TIME_SHIFTS);
        }

        if ($this->request->is("post")) {

            $data = $this->request->getData();

            $turno = $data["turno"];

            $tituloTurno = $turno ? "Turno: Atual" : "Turno: Anterior";

            $turnoOperacao = TimeUtil::getTurnoAnteriorAtual($quadroHorariosCliente, $turno);
            // $turnoAtual = $turnos["turnoAtual"];
            // $turnoAnterior = $turnos["turnoAnterior"];

            // Turno Anterior:
            // Se a hora do turno anterior é maior que a do turno atual, então é um dia a menos.
            // Turno Atual:
            // Se o horário do turno atual é maior que a hora atual, então turno anterior e turno atual é um dia a menos.

            $turnoInicio = $turnoOperacao["dataConsultaInicio"];
            $turnoFim = $turnoOperacao["dataConsultaFim"];



            // Obtem lista de funcionários da unidade

            $funcionariosArray = $this->ClientesHasUsuarios->getAllUsersByClienteId(
                $cliente["id"],
                PROFILE_TYPE_WORKER,
                1
            );
            $funcionariosArray = $funcionariosArray->toArray();

            // Lista dos IDS de funcionários
            $funcionariosIdsList = array();

            foreach ($funcionariosArray as $funcionario) {
                $funcionariosIdsList[$funcionario["id"]] = $funcionario["nome"];
            }

            // Obtem os brindes habilitados do posto de atendimento

            $brindes = $this->Brindes->findBrindes(null, $cliente["id"]);
            $brindes = $brindes->toArray();
            $dadosPesquisaCuponsArray = array();

            foreach ($brindes as $brinde) {
                $dadosPesquisaCuponsArray[] = array(
                    "id" => $brinde->id,
                    "nomeBrinde" => $brinde->nome,
                    "clientesId" => $cliente->id
                );
            };

            // Obtem os dados dos cupons

            // Fechamento Anterior

            $cuponsFuncionarios = array();
            $dadosVendaFuncionarios = array();
            $funcionarios = array();

            $dataInicio = null;
            $dataFim = null;

            foreach ($funcionariosIdsList as $funcionarioId => $funcionarioNome) {

                $funcionario = array();

                $funcionario["id"] = $funcionarioId;
                $funcionario["nome"] = $funcionarioNome;

                $dadosTurno = array();
                $somaResgatados = 0;
                $somaUsados = 0;
                $somaGotas = 0;
                $somaDinheiro = 0;
                $somaBrindes = 0;
                $somaCompras = 0;

                // Soma total de todos os funcionários
                $totalResgatados = null;
                $totalUsados = null;
                $totalGotas = null;
                $totalDinheiro = null;
                $totalBrindes = null;
                $totalCompras = null;

                foreach ($dadosPesquisaCuponsArray as $cupomPesquisa) {
                    $dataInicio = $turnoInicio;
                    $dataFim = $turnoFim;

                    $cupons = $this->Cupons->find("all")->where(
                        array(
                            "brindes_id" => $cupomPesquisa["id"],
                            "clientes_id" => $cupomPesquisa["clientesId"],
                            "funcionarios_id" => $funcionarioId,
                            "data BETWEEN '{$dataInicio}' AND '{$dataFim}'"
                        )
                    );

                    $cuponsArray = $cupons->toArray();

                    $dados = array();
                    $resgatados = 0;
                    $usados = 0;
                    $gotas = 0;
                    $dinheiro = 0;
                    $brindes = 0;
                    $compras = 0;

                    foreach ($cuponsArray as $cupom) {
                        $resgatados = $cupom["resgatado"] ? $resgatados + 1 : $resgatados;

                        $dinheiro += $cupom["valor_pago_reais"];
                        $gotas += $cupom["valor_pago_gotas"];

                        // Se Com Desconto / Gotas ou Reais (sendo pago em reais)
                        $compras += ($cupom["tipo_venda"] == TYPE_SELL_DISCOUNT_TEXT
                            || ($cupom["tipo_venda"] == TYPE_SELL_CURRENCY_OR_POINTS_TEXT && !empty($cupom["valor_pago_reais"])))
                            ? 1 : 0;
                        // Se cupom = Isento / Gotas ou Reais (sendo pago em gotas)
                        $brindes += ($cupom["tipo_venda"] == TYPE_SELL_FREE_TEXT
                            || ($cupom["tipo_venda"] == TYPE_SELL_CURRENCY_OR_POINTS_TEXT && !empty($cupom["valor_pago_gotas"])))
                            ? 1 : 0;

                        $usados = $cupom["usado"] ? $usados + 1 : $usados;
                    }

                    // somatória parcial

                    $dados = array(
                        "idBrinde" => $cupomPesquisa["id"],
                        "nomeBrinde" => $cupomPesquisa["nomeBrinde"],
                        "resgatados" => $resgatados,
                        "usados" => $usados,
                        "gotas" => $gotas,
                        "dinheiro" => $dinheiro,
                        "brindes" => $brindes,
                        "compras" => $compras,
                        "dataInicio" => date("d/m/Y H:i:s", strtotime($dataInicio)),
                        "dataFim" => date("d/m/Y H:i:s", strtotime($dataFim))
                    );

                    $somaResgatados += $resgatados;
                    $somaUsados += $usados;
                    $somaGotas += $gotas;
                    $somaDinheiro += $dinheiro;
                    $somaBrindes += $brindes;
                    $somaCompras += $compras;
                    $dadosTurno[] = $dados;
                }

                // aqui acabou do funcionário
                $soma = array(
                    "somaResgatados" => $somaResgatados,
                    "somaUsados" => $somaUsados,
                    "somaGotas" => $somaGotas,
                    "somaDinheiro" => $somaDinheiro,
                    "somaBrindes" => $somaBrindes,
                    "somaCompras" => $somaCompras,
                );

                $totalResgatados += $somaResgatados;
                $totalUsados += $somaUsados;
                $totalGotas += $somaGotas;
                $totalDinheiro += $somaDinheiro;
                $totalBrindes += $somaBrindes;
                $totalCompras += $somaCompras;
                $funcionario["soma"] = $soma;
                $funcionario["turno"] = array(
                    "dataInicio" => date("d/m/Y H:i:s", strtotime($turnoInicio)),
                    "dataFim" => date("d/m/Y H:i:s", strtotime($turnoFim)),
                    "dados" => $dadosTurno
                );
                $dadosVendaFuncionarios[] = $funcionario;
            }
            $dataInicio = date("d/m/Y H:i", strtotime($turnoInicio));
            $dataFim = date("d/m/Y H:i", strtotime($turnoFim));

            if (count($dadosVendaFuncionarios) == 0) {
                $this->Flash->warning(MESSAGE_QUERY_DOES_NOT_CONTAIN_DATA);
            }

            $totalGeral = array(
                "totalResgatados" => $totalResgatados,
                "totalUsados" => $totalUsados,
                "totalGotas" => $totalGotas,
                "totalDinheiro" => $totalDinheiro,
                "totalBrindes" => $totalBrindes,
                "totalCompras" => $totalCompras,
            );
        }

        // DebugUtil::print($dadosVendaFuncionarios);

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);
    }

    /**
     * Action para Relatorio de Caixa de Funcionário via Gerente
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-06-09
     * 
     * @return void
     */
    public function relatorioCaixaFuncionariosGerente()
    {
        $arraySet = array(
            "dadosVendaFuncionarios",
            "funcionariosList",
            "funcionarioSelected",
            "totalGeral",
            "tituloTurno",
            "tipoRelatorio",
            "dataInicio",
            "dataFim"
        );

        $date = date("Y-m-d H:i");
        // Uma semana atrás
        $diaInicio = strtotime('-1 week', strtotime($date));

        $dataInicio = date("Y-m-d 00:00", $diaInicio);
        $dataFim = date("Y-m-d H:i", strtotime($date));

        $sessaoUsuario = $this->getSessionUserVariables();

        $filtrarTurnoAnterior = null;

        $rede = $sessaoUsuario["rede"];
        $cliente = $sessaoUsuario["cliente"];

        if ($sessaoUsuario["usuarioAdministrar"]) {
            $this->usuarioLogado = $sessaoUsuario["usuarioAdministrar"];
        }

        $dadosVendaFuncionarios = array();
        $totalGeral = array();

        // Pega todos os funcionários do posto do gerente alocado
        $funcionariosList = $this->Usuarios->findAllUsuarios(null, array($cliente["id"], null, null, PROFILE_TYPE_WORKER))->find("list");
        $funcionarioSelected = 0;
        $tipoRelatorio = "Sintético";
        $tituloTurno = "";

        $quadroHorariosCliente = $this->ClientesHasQuadroHorario->getHorariosCliente($rede["id"], $cliente["id"]);
        $quadroHorariosCliente = $quadroHorariosCliente->toArray();
        $quadroHorariosClienteLength = count($quadroHorariosCliente);

        if (empty($quadroHorariosCliente) || $quadroHorariosClienteLength == 0) {
            $this->set(compact($arraySet));
            $this->set("_serialize", $arraySet);

            return $this->Flash->error(MESSAGE_ESTABLISHMENT_WITHOUT_TIME_SHIFTS);
        }

        if ($this->request->is("post")) {

            $data = $this->request->getData();

            // Define datas
            $dataInicio = !empty($data["data_inicio_envio"]) ?  $data["data_inicio_envio"] : $dataInicio;
            $dataFim = !empty($data["data_fim_envio"]) ?  $data["data_fim_envio"] : $dataFim;

            $turno = 1;

            $tituloTurno = $turno ? "Turno: Atual" : "Turno: Anterior";

            $turnoOperacao = TimeUtil::getTurnoAnteriorAtual($quadroHorariosCliente, $turno);
            // $turnoAtual = $turnos["turnoAtual"];
            // $turnoAnterior = $turnos["turnoAnterior"];

            // Turno Anterior:
            // Se a hora do turno anterior é maior que a do turno atual, então é um dia a menos.
            // Turno Atual:
            // Se o horário do turno atual é maior que a hora atual, então turno anterior e turno atual é um dia a menos.

            $turnoInicio = $turnoOperacao["dataConsultaInicio"];
            $turnoFim = $turnoOperacao["dataConsultaFim"];



            // Obtem lista de funcionários da unidade

            $funcionariosArray = $this->ClientesHasUsuarios->getAllUsersByClienteId(
                $cliente["id"],
                PROFILE_TYPE_WORKER,
                1
            );
            $funcionariosArray = $funcionariosArray->toArray();

            // Lista dos IDS de funcionários
            $funcionariosIdsList = array();

            foreach ($funcionariosArray as $funcionario) {
                $funcionariosIdsList[$funcionario["id"]] = $funcionario["nome"];
            }

            // Obtem os brindes habilitados do posto de atendimento

            $brindes = $this->Brindes->findBrindes(null, $cliente["id"]);
            $brindes = $brindes->toArray();
            $dadosPesquisaCuponsArray = array();

            foreach ($brindes as $brinde) {
                $dadosPesquisaCuponsArray[] = array(
                    "id" => $brinde->id,
                    "nomeBrinde" => $brinde->nome,
                    "clientesId" => $cliente->id
                );
            };

            // Obtem os dados dos cupons

            // Fechamento Anterior

            $cuponsFuncionarios = array();
            $dadosVendaFuncionarios = array();
            $funcionarios = array();

            foreach ($funcionariosIdsList as $funcionarioId => $funcionarioNome) {

                $funcionario = array();

                $funcionario["id"] = $funcionarioId;
                $funcionario["nome"] = $funcionarioNome;

                $dadosTurno = array();
                $somaResgatados = 0;
                $somaUsados = 0;
                $somaGotas = 0;
                $somaDinheiro = 0;
                $somaBrindes = 0;
                $somaCompras = 0;

                // Soma total de todos os funcionários
                $totalResgatados = null;
                $totalUsados = null;
                $totalGotas = null;
                $totalDinheiro = null;
                $totalBrindes = null;
                $totalCompras = null;

                foreach ($dadosPesquisaCuponsArray as $cupomPesquisa) {
                    $cupons = $this->Cupons->find("all")->where(
                        array(
                            "brindes_id" => $cupomPesquisa["id"],
                            "clientes_id" => $cupomPesquisa["clientesId"],
                            "funcionarios_id" => $funcionarioId,
                            "data BETWEEN '{$dataInicio}' AND '{$dataFim}'"
                        )
                    );

                    $cuponsArray = $cupons->toArray();

                    $dados = array();
                    $resgatados = 0;
                    $usados = 0;
                    $gotas = 0;
                    $dinheiro = 0;
                    $brindes = 0;
                    $compras = 0;

                    foreach ($cuponsArray as $cupom) {
                        $resgatados = $cupom["resgatado"] ? $resgatados + 1 : $resgatados;

                        $dinheiro += $cupom["valor_pago_reais"];
                        $gotas += $cupom["valor_pago_gotas"];

                        // Se Com Desconto / Gotas ou Reais (sendo pago em reais)
                        $compras += ($cupom["tipo_venda"] == TYPE_SELL_DISCOUNT_TEXT
                            || ($cupom["tipo_venda"] == TYPE_SELL_CURRENCY_OR_POINTS_TEXT && !empty($cupom["valor_pago_reais"])))
                            ? 1 : 0;
                        // Se cupom = Isento / Gotas ou Reais (sendo pago em gotas)
                        $brindes += ($cupom["tipo_venda"] == TYPE_SELL_FREE_TEXT
                            || ($cupom["tipo_venda"] == TYPE_SELL_CURRENCY_OR_POINTS_TEXT && !empty($cupom["valor_pago_gotas"])))
                            ? 1 : 0;

                        $usados = $cupom["usado"] ? $usados + 1 : $usados;
                    }

                    // somatória parcial

                    $dados = array(
                        "idBrinde" => $cupomPesquisa["id"],
                        "nomeBrinde" => $cupomPesquisa["nomeBrinde"],
                        "resgatados" => $resgatados,
                        "usados" => $usados,
                        "gotas" => $gotas,
                        "dinheiro" => $dinheiro,
                        "brindes" => $brindes,
                        "compras" => $compras,
                        "dataInicio" => date("d/m/Y H:i:s", strtotime($dataInicio)),
                        "dataFim" => date("d/m/Y H:i:s", strtotime($dataFim))
                    );

                    $somaResgatados += $resgatados;
                    $somaUsados += $usados;
                    $somaGotas += $gotas;
                    $somaDinheiro += $dinheiro;
                    $somaBrindes += $brindes;
                    $somaCompras += $compras;
                    $dadosTurno[] = $dados;
                }

                // aqui acabou do funcionário
                $soma = array(
                    "somaResgatados" => $somaResgatados,
                    "somaUsados" => $somaUsados,
                    "somaGotas" => $somaGotas,
                    "somaDinheiro" => $somaDinheiro,
                    "somaBrindes" => $somaBrindes,
                    "somaCompras" => $somaCompras,
                );

                $totalResgatados += $somaResgatados;
                $totalUsados += $somaUsados;
                $totalGotas += $somaGotas;
                $totalDinheiro += $somaDinheiro;
                $totalBrindes += $somaBrindes;
                $totalCompras += $somaCompras;
                $funcionario["soma"] = $soma;
                $funcionario["turno"] = array(
                    "dataInicio" => date("d/m/Y H:i:s", strtotime($turnoInicio)),
                    "dataFim" => date("d/m/Y H:i:s", strtotime($turnoFim)),
                    "dados" => $dadosTurno
                );
                $dadosVendaFuncionarios[] = $funcionario;
            }

            if (count($dadosVendaFuncionarios) == 0) {
                $this->Flash->warning(MESSAGE_QUERY_DOES_NOT_CONTAIN_DATA);
            }

            $totalGeral = array(
                "totalResgatados" => $totalResgatados,
                "totalUsados" => $totalUsados,
                "totalGotas" => $totalGotas,
                "totalDinheiro" => $totalDinheiro,
                "totalBrindes" => $totalBrindes,
                "totalCompras" => $totalCompras,
            );
        }

        // DebugUtil::print($dadosVendaFuncionarios);

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);
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
            $sessaoUsuario = $this->getSessionUserVariables();
            $usuarioLogado = $sessaoUsuario["usuarioLogado"];

            $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"];

            if ($usuarioAdministrar) {
                $usuarioLogado = $usuarioAdministrar;
                $this->usuarioLogado = $usuarioAdministrar;
            }

            if ($this->request->is(['post'])) {
                $data = $this->request->getData();

                $brindesId = !empty($data["brindes_id"]) ? $data["brindes_id"] : null;
                $clientesId = !empty($data["clientes_id"]) ? $data["clientes_id"] : null;
                $funcionariosId = !empty($data["funcionarios_id"]) ? (int)$data["funcionarios_id"] : $usuarioLogado["id"];
                $usuariosId = !empty($data["usuarios_id"]) ? $data["usuarios_id"] : 0;
                $tipoPagamento = !empty($data["tipo_pagamento"]) ? $data["tipo_pagamento"] : null;
                $currentPassword = !empty($data["current_password"]) ? $data["current_password"] : null;
                $senhaAtual = !empty($data["current_password"]) ? $data["current_password"] : "";
                $vendaAvulsa = !empty($data["venda_avulsa"]) ? $data["venda_avulsa"] : false;

                // Definido pelo Samuel, cliente só pode retirar 1 por vez
                $quantidade = 1;

                Log::write("debug", $data);

                // DebugUtil::printArray($data);
                // Validação de Dados
                $errors = array();
                if (empty($brindesId)) {
                    $errors[] = __("É necessário selecionar um brinde para resgatar pontos!");
                }
                if (empty($clientesId)) {
                    $errors[] = __("É necessário selecionar uma unidade de atendimento para resgatar pontos!");
                }
                if (empty($tipoPagamento) || !in_array($tipoPagamento, array(TYPE_PAYMENT_MONEY, TYPE_PAYMENT_POINTS))) {
                    // Evita se o usuário alterar diretamente no html de conitnuar e submeter
                    $errors[] = __(MESSAGE_CUPOM_TYPE_PAYMENT_REQUIRED);
                }

                if (count($errors) > 0) {

                    $mensagem = array("status" => 0, "message" => Configure::read("messageOperationFailureDuringProcessing"), "errors" => $errors);

                    $arraySet = array("mensagem");
                    $this->set(compact($arraySet));
                    $this->set("_serialize", $arraySet);

                    return;
                }

                $retorno = $this->trataCompraCupom(
                    $brindesId,
                    $usuariosId,
                    $clientesId,
                    (float)$quantidade,
                    $funcionariosId,
                    $tipoPagamento,
                    $vendaAvulsa,
                    $senhaAtual,
                    false
                );

                $arraySet = $retorno["arraySet"];
                $mensagem = $retorno["mensagem"];

                if (!$mensagem["status"]) {
                    $this->set(compact($arraySet));
                    $this->set("_serialize", $arraySet);
                    return;
                }
                $ticket = $retorno["ticket"];
                $cliente = $retorno["cliente"];
                $usuario = $retorno["usuario"];
                // @todo: temp
                $tempo = $retorno["tempo"];
                $tipo_emissao_codigo_barras = $retorno["tipo_emissao_codigo_barras"];

                // $is_brinde_smart_shower = $ticket["tipo_principal_codigo_brinde"] <= 4;
                $is_brinde_smart_shower = !empty($retorno["is_brinde_smart_shower"]) ? $retorno["is_brinde_smart_shower"] : 0;
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
            $rede = $this->request->session()->read('Rede.Grupo');

            // pega id de todos os clientes que estão ligados à uma rede

            $redes_has_clientes_query = $this->RedesHasClientes->getRedesHasClientesByRedesId($rede->id);
            $clientes_ids = array();

            foreach ($redes_has_clientes_query as $key => $value) {
                $clientes_ids[] = $value['clientes_id'];
            }

            $array = [];
            $clientes_id = $array;

            $brinde_habilitado = $this->ClientesHasBrindesHabilitados->getBrindeHabilitadoById(
                $data['brindes_id']
            );

            // $quantidade = $data['quantidade'];
            // Definido pelo Samuel, cliente só pode retirar 1 por vez
            $quantidade = 1;

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
                if (strlen($data["current_password"]) == 0) {
                    $message = 'Informe a senha do usuário.';
                } else {
                    $message = 'Senha incorreta para usuário. Nâo foi possível resgatar o brinde';
                }
            } else {

                // Se o usuário tiver pontuações suficientes ou for um usuário de venda avulsa somente
                if (($usuario->pontuacoes >= ($brinde_habilitado->preco_atual->preco * $quantidade) || $usuario->tipo_perfil == Configure::read('profileTypes')['DummyUserProfileType'])) {

                    // verificar se cliente possui usuario em sua lista de usuários. se não tiver, cadastrar

                    $clientes_has_usuarios_conditions = [];

                    array_push($clientes_has_usuarios_conditions, ['ClientesHasUsuarios.usuarios_id' => $usuario['id']]);
                    array_push($clientes_has_usuarios_conditions, ['ClientesHasUsuarios.clientes_id IN' => $clientes_ids]);

                    // @todo gustavosg Testar tipo_perfil
                    array_push($clientes_has_usuarios_conditions, ['ClientesHasUsuarios.tipo_perfil' => Configure::read('profileTypes')['UserProfileType']]);

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

                        $pontuacoesProcessar = $brinde_habilitado->preco_atual->preco * $quantidade;

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

                            $maximoContador = count($pontuacoesPendentesUso->toArray());

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
                            $brinde_habilitado->preco_atual->preco * $quantidade
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
                            $rede["id"],
                            $cliente["id"],
                            $usuario["id"],
                            $brinde_habilitado->id,
                            $quantidade,
                            $brinde_habilitado->preco_atual->preco,
                            TYPE_PAYMENT_POINTS,
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
            $tempo = $brinde_habilitado->brinde->tempo_uso_brinde;
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
                if (strlen($data["current_password"]) == 0) {
                    $message = 'Informe a senha do usuário.';
                } else {
                    $message = 'Senha incorreta para usuário. Nâo foi possível resgatar o brinde';
                }
            } else {
                $cliente = $this->Clientes->getClienteById($data['clientes_id']);

                $cupom = $this->Cupons->getCupomToReprint(
                    $data['id'],
                    $data['brindes_id'],
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
        $arraySet = array('status', 'error');
        try {
            $status = false;
            $error = __("{0} {1}", Configure::read('messageRedeemCouponError'), Configure::read('callSupport'));

            if ($this->request->is(['post'])) {
                $data = $this->request->getData();
                $funcionariosId = $this->Auth->user()["id"];

                $cupomEmitido = $data['cupom_emitido'];

                $cupons = $this->Cupons->getCuponsByCupomEmitido($cupomEmitido);

                if (!$cupons) {
                    $status = false;
                    $error = __("{0}", MESSAGE_RECORD_NOT_FOUND);

                    $this->set(compact($arraySet));
                    return;
                }
                foreach ($cupons->toArray() as $key => $cupom) {
                    $brindesEstoque = $this
                        ->BrindesEstoque
                        ->getEstoqueForBrinde($cupom["brindes_id"]);

                    // $brindeSave = $this->BrindesEstoque->addBrindeEstoque($brinde["id"], $this->usuarioLogado["id"], $quantidade, TYPE_OPERATION_ADD_STOCK);
                    $estoque = $this->BrindesEstoque->addBrindeEstoque(
                        $cupom["brindes_id"],
                        $cupom["usuarios_id"],
                        $cupom["quantidade"],
                        (int)Configure::read('stockOperationTypes')['sellTypeGift']
                    );

                    // diminuiu estoque, considera o item do cupom como resgatado
                    if ($estoque) {
                        $cupom_save = $this->Cupons->setCuponsResgatadosUsados(array($cupom["id"]));

                        // adiciona novo registro de pontuação
                        $pontuacao = $this->Pontuacoes->addPontuacoesBrindesForUsuario(
                            $cupom["clientes_id"],
                            $cupom["usuarios_id"],
                            $cupom["brindes_id"],
                            $cupom["valor_pago_gotas"],
                            $cupom["valor_pago_reais"],
                            $funcionariosId,
                            true
                        );
                    }
                }

                // se chegou até aqui, gravou com sucesso no banco
                $status = true;
                $error = null;
            }

            $this->set(compact($arraySet));
            $this->set("_serialize", $arraySet);
        } catch (\Exception $e) {
            $trace = $e->getTraceAsString();
            $stringError = __("Erro ao resgatar cupom: {0}", $e->getMessage());

            // @todo gustavosg melhorar log
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

                if ($tipoPerfil == PROFILE_TYPE_WORKER || $tipoPerfil == PROFILE_TYPE_DUMMY_WORKER) {
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

                $clientesUsuariosIds = $this->ClientesHasUsuarios->getAllClientesIdsByUsuariosId($funcionarioId, $tipoPerfil);

                $clienteId = 0;
                if (count($clientesUsuariosIds) > 0) {
                    $clienteId = $clientesUsuariosIds[0];
                }

                // $clienteId = 2;

                $todasUnidadesRedesQuery = $this->RedesHasClientes->getAllRedesHasClientesIdsByClientesId($clienteId);

                $todasUnidadesIds = array();

                foreach ($todasUnidadesRedesQuery as $value) {
                    $todasUnidadesIds[] = $value["clientes_id"];
                }

                if ($cupomEmitido == "" || strlen($cupomEmitido) < 14) {

                    $errors = array(__(Configure::read("messageFieldEmptyDefault"), "CUPOM EMITIDO"));

                    if (strlen($cupomEmitido) <= 14) {
                        $errors[] = __(Configure::read("messageFieldDigitsMinimum"), "CUPOM EMITIDO", 14);
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
                $cupons = $cupons->toArray();

                // DebugUtil::print($cupons);
                // Verifica se este cupom já foi usado
                $somaTotalGotas = 0;
                $somaTotalReais = 0;
                $dadosCupons = array();

                $verificado = false;
                $resgatados = array();
                $cuponsPendentes = array();

                foreach ($cupons as $cupom) {

                    $dadoCupom = array();

                    $somaTotalGotas += (float)$cupom["valor_pago_gotas"];
                    $somaTotalReais += (float)$cupom["valor_pago_reais"];

                    $dadoCupom["nome"] = $cupom["brinde"]["nome"];
                    $dadoCupom["quantidade"] = $cupom["quantidade"];
                    $dadoCupom["preco_brinde_gotas"] = (float)$cupom["valor_pago_gotas"];
                    $dadoCupom["preco_brinde_reais"] = (float)$cupom["valor_pago_reais"];
                    // imagem brinde
                    $dadoCupom["nome_img_completo"] = $cupom["brinde"]["nome_img_completo"];
                    $dadoCupom["data_resgate"] = !empty($cupom["data"]) ? $cupom["data"]->format("d/m/Y H:i:s") : null;
                    $dadosCupons[] = $dadoCupom;

                    if ($cupom["resgatado"]) {
                        $resgatados[] = true;
                    } else {
                        $cuponsPendentes[] = $cupom;
                    }

                    $verificado = true;
                }

                // Se a quantidade de cupons é igual a de resgatados, já resgatou tudo
                if (count($resgatados) == count($cupons)) {
                    $mensagem = array(
                        "status" => 0,
                        "message" => Configure::read("messageWarningDefault"),
                        "errors" => array("Este cupom já foi resgatado pelo usuário!")
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

                // Não encontrou cupom
                if (count($cupons) == 0) {
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
                }

                $brindesNaoResgatados = array();
                $dadosCupons = array();

                $verificado = false;
                $resgatados = array();

                // Processa somente os cupons pendentes
                foreach ($cuponsPendentes as $cupom) {

                    $brindesEstoque = $this->BrindesEstoque->getActualStockForBrindesEstoque($cupom["brindes_id"]);

                    $prosseguir = ($cupom["brinde"]["ilimitado"] || $brindesEstoque["estoque_atual"] >= $cupom["quantidade"]);

                    if ($prosseguir) {

                        $tipoSaida = "";

                        if (($cupom["tipo_venda"] == TYPE_SELL_CURRENCY_OR_POINTS_TEXT && !empty($cupom["valor_pago_gotas"]) || $cupom["tipo_venda"] == TYPE_SELL_FREE_TEXT)) {
                            $tipoSaida = TYPE_OPERATION_SELL_BRINDE;
                        } else {
                            $tipoSaida = TYPE_OPERATION_SELL_CURRENCY;
                        }

                        $estoque = $this->BrindesEstoque->addBrindeEstoque(
                            $cupom["brindes_id"],
                            $cupom["usuarios_id"],
                            $cupom["quantidade"],
                            // TYPE_SELL
                            $tipoSaida
                        );

                        // diminuiu estoque, considera o item do cupom como resgatado
                        if ($estoque) {
                            $cupomSave = null;

                            // Equipamento RTI?
                            if ($cupom["brinde"]["tipo_equipamento"] == TYPE_EQUIPMENT_RTI) {
                                $cupomSave = $this->Cupons->setCupomResgatado($cupom["id"]);
                            } else {
                                $cupomSave = $this->Cupons->setCuponsResgatadosUsados(array($cupom["id"]));
                            }

                            // adiciona novo registro de pontuação

                            $pontuacao = $this->Pontuacoes->addPontuacoesBrindesForUsuario(
                                $cupom->clientes_id,
                                $cupom->usuarios_id,
                                $cupom->brindes_id,
                                $cupom->valor_pago_gotas,
                                $cupom->valor_pago_reais,
                                $this->Auth->user()["id"],
                                true
                            );
                        }

                        // Obtem dados de retorno

                        $dadoCupom = array();

                        $somaTotalGotas += (float)$cupom["valor_pago_gotas"];
                        $somaTotalReais += (float)$cupom["valor_pago_reais"];

                        $dadoCupom["nome"] = $cupom["brinde"]["nome"];
                        $dadoCupom["quantidade"] = $cupom["quantidade"];
                        $dadoCupom["preco_brinde_gotas"] = (float)$cupom["valor_pago_gotas"];
                        $dadoCupom["preco_brinde_reais"] = (float)$cupom["valor_pago_reais"];
                        // imagem brinde
                        $dadoCupom["nome_img_completo"] = $cupom["brinde"]["nome_img_completo"];
                        $dadoCupom["data_resgate"] = !empty($cupom["data"]) ? $cupom["data"]->format("d/m/Y H:i:s") : null;
                        $dadosCupons[] = $dadoCupom;
                    } else {
                        // Brindes que não foram resgatados devido o estoque ser insuficiente
                        $brindesNaoResgatados[] = array(
                            "nome" => $cupom["brinde"]["nome"],
                            "quantidade" => $cupom["quantidade"]
                        );
                    }
                }

                $errors = array();

                if (count($brindesNaoResgatados) > 0) {
                    $errors[] = "Alguns brindes não foram resgatados pois não há estoque suficiente. Verifique com o estabelecimento!";
                }
                $mensagem = array(
                    "status" => 1,
                    "message" => __(
                        "{0} {1}",
                        Configure::read("messageProcessingCompleted"),
                        Configure::read("messageRedeemCouponRedeemed")
                    ),
                    "errors" => $errors
                );
                $resultado = array(
                    "soma_gotas" => $somaTotalGotas,
                    "soma_reais" => $somaTotalReais,
                    "recibo" => $dadosCupons,
                    "funcionario" => $funcionario,
                    "brindes_nao_resgatados" => $brindesNaoResgatados
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
            $trace = $e->getTraceAsString();

            $messageString = __("Erro durante resgate do cupom!");
            $messageStringDebug = __("{0} - {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $messageString, $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write("error", $messageStringDebug);
            Log::write("error", $trace);

            $mensagem = array('status' => false, 'message' => $messageString, 'errors' => $trace);
        }
    }

    /**
     * CuponsController::efetuarEstornoCupomAPI
     *
     * Efetua estorno de cupom do cliente
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-02-09
     *
     * @return json_encode Dados json
     */
    public function efetuarEstornoCupomAPI()
    {
        $usuarioLogado = $this->Auth->user();

        if ($this->request->is("post")) {
            $data = $this->request->getData();
            $cupomEmitido = !empty($data["cupom_emitido"]) ? $data["cupom_emitido"] : null;
            $confirmacao = !empty($data["confirmar"]) ? $data["confirmar"] : 0;

            if (empty($cupomEmitido)) {
                $errors = array(MESSAGE_CUPOM_PRINTED_EMPTY);
                return ResponseUtil::errorAPI(MESSAGE_OPERATION_FAILURE_DURING_PROCESSING, $errors);
            }

            $cupom = $this->Cupons->getCupomByCupomEmitido($cupomEmitido, 0);

            if (empty($cupom)) {
                $errors = array(MESSAGE_CUPOM_PRINTED_ALREADY_CANCELLED);
                return ResponseUtil::errorAPI(MESSAGE_OPERATION_FAILURE_DURING_PROCESSING, $errors, array());
            }

            if (empty($cupomEmitido)) {
                $errors = array(MESSAGE_CUPOM_PRINTED_DOES_NOT_EXIST);
                return ResponseUtil::errorAPI(MESSAGE_OPERATION_FAILURE_DURING_PROCESSING, $errors, array());
            }

            $mesmoUsuario = 0;
            $funcionariosRedeLista = array();
            $clientesId = $cupom["clientes_id"];

            $clientesRede = $this->RedesHasClientes->getAllRedesHasClientesIdsByClientesId($clientesId);
            $clientesIds = array();

            foreach ($clientesRede as $cliente) {
                $clientesIds[] = $cliente["clientes_id"];
            }

            // Verifica se o cupom está na rede (se o usuário logado for funcionário da loja)

            if ($usuarioLogado["tipo_perfil"] == PROFILE_TYPE_WORKER) {
                // Se for funcionário da loja, tem que verificar se o usuário que o atendeu ainda existe e se é realmente desta loja

                $sessaoUsuario = $this->getSessionUserVariables();

                $usuariosConditions = array(
                    sprintf("Usuarios.tipo_perfil between %s AND %s", PROFILE_TYPE_ADMIN_NETWORK, PROFILE_TYPE_WORKER),
                    "Clientes.ativado" => 1
                );

                $funcionariosRedeQuery = $this->Usuarios->findAllUsuariosByRede($sessaoUsuario["rede"]["id"], $usuariosConditions)->select(array("Usuarios.id"));
                $funcionariosRedeQuery = $funcionariosRedeQuery->toArray();

                foreach ($funcionariosRedeQuery as $funcionario) {
                    $funcionariosRedeLista[] = $funcionario["id"];
                }

                $pertenceCupomRede = in_array($cupom["funcionarios_id"], $funcionariosRedeLista);

                if (!$pertenceCupomRede) {
                    // Se chegou neste ponto, duas situações aconteceram:
                    // 1 - O usuário que está tentando estornar é do tipo funcionário e não está na lista
                    // 2 - É outro usuário

                    $errors = array(MESSAGE_CUPOM_ANOTHER_NETWORK);

                    return ResponseUtil::errorAPI(MESSAGE_OPERATION_FAILURE_DURING_PROCESSING, $errors);
                }

                // Pertence a rede, fazer procedimento de estorno
                return $this->realizaProcessamentoEstornoCupom($cupom, $usuarioLogado);
            } elseif ($usuarioLogado["tipo_perfil"] == PROFILE_TYPE_USER && $usuarioLogado["id"] != $cupom["usuarios_id"]) {
                // Encerra fluxo, somente próprio usuário pode cancelar seu cupom
                $errors = array("Somente o próprio cliente pode cancelar seu cupom!");

                return ResponseUtil::errorAPI(MESSAGE_OPERATION_FAILURE_DURING_PROCESSING, $errors);
            } elseif ($usuarioLogado["tipo_perfil"] == PROFILE_TYPE_USER && $usuarioLogado["id"] == $cupom["usuarios_id"]) {
                // Somente o próprio usuário pode cancelar seu cupom

                // Se o cupom já tiver sido resgatado e usado, não é possível estorno

                if ($cupom["resgatado"] && $cupom["usado"]) {
                    $errors = array(MESSAGE_CUPOM_PRINTED_CANNOT_BE_CANCELLED);
                    return ResponseUtil::errorAPI(MESSAGE_OPERATION_FAILURE_DURING_PROCESSING, $errors);
                } else {
                    return $this->realizaProcessamentoEstornoCupom($cupom, $usuarioLogado);
                }
            }
        }
    }

    /**
     * CuponsController::realizaProcessamentoEstornoCupom
     *
     * Executa o processamento de estorno do cupom se o mesmo possui está ok para estorno
     *
     * @param Cupon $cupom
     * @param mixed $usuarioLogado
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-02-09
     *
     * @return array SuccessAPI/ErrorAPI Mensagem de sucesso / erro
     */
    public function realizaProcessamentoEstornoCupom(\App\Model\Entity\Cupon $cupom, array $usuarioLogado)
    {
        // Se o brinde for do tipo Equipamentos RTI, não pode cancelar
        $tipoBrindeRede = $cupom["clientes_has_brindes_habilitado"]["brinde"]["tipo_brinde_rede"];

        if ($tipoBrindeRede["equipamento_rti"]) {
            $errors = array(MESSAGE_CUPOM_PRINTED_CANNOT_BE_CANCELLED);
            return ResponseUtil::errorAPI(MESSAGE_OPERATION_FAILURE_DURING_PROCESSING, $errors);
        } else {
            $brindesCupomEstornados = array();

            $cupomApagado = $this->Cupons->setCupomEstornado($cupom["id"]);
            // Remove usuarios has brindes
            $usuarioHasBrindesCupom = $this->UsuariosHasBrindes->getUsuariosHasBrindesByCuponsId($cupom["id"]);
            $rowCount = $this->UsuariosHasBrindes->deleteBrindeByCupomId($cupom["id"]);

            if (!empty($usuarioHasBrindesCupom) && $rowCount > 0) {
                foreach ($usuarioHasBrindesCupom as $itemCupom) {
                    $clientesBrindesHabilitadosId = $itemCupom["clientes_has_brindes_habilitado"]["id"];
                    $quantidade = $itemCupom["quantidade"];
                    $brindesCupomEstornados[] = array(
                        "quantidade" => $quantidade,
                        "nome" => $itemCupom["clientes_has_brindes_habilitado"]["brinde"]["nome"]
                    );
                    $devolucao = $this->ClientesHasBrindesEstoque->addBrindeEstoque($clientesBrindesHabilitadosId, $usuarioLogado["id"], $quantidade, STOCK_OPERATION_TYPES_RETURN_TYPE);
                }
            }
            $retorno = array(
                "cupom" => $cupom["cupom_emitido"],
                "brindes" => $brindesCupomEstornados,
                "qteBrindesEstornados" => count($brindesCupomEstornados)
            );

            // Se teve ou não teve registro, retorna informando que foi cancelado, pois
            // o registro terá sido removido e se teve, estoque foi adicionado
            return ResponseUtil::successAPI(MESSAGE_CUPOM_PRINTED_CANCELLED, $retorno);
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

            // Log::write("info", $data);
            // Validação de dados
            $errors = array();
            // Via API o default é PONTOS
            $data['tipo_pagamento'] = TYPE_PAYMENT_POINTS;

            if (empty($data["brindes_id"])) {
                $errors[] = __("É necessário selecionar um brinde para resgatar pontos!");
            }

            if (empty($data["clientes_id"])) {
                $errors[] = __("É necessário selecionar uma unidade de atendimento para resgatar pontos!");
            }

            if (empty($data['tipo_pagamento']) || !in_array($data['tipo_pagamento'], array(TYPE_PAYMENT_MONEY, TYPE_PAYMENT_POINTS))) {
                // Evita se o usuário alterar diretamente no html de conitnuar e submeter
                $errors[] = __(MESSAGE_CUPOM_TYPE_PAYMENT_REQUIRED);
            }

            if (count($errors) > 0) {
                $mensagem = array("status" => 0, "message" => Configure::read("messageOperationFailureDuringProcessing"), "errors" => $errors);
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
            // Definido pelo Samuel, cliente só pode retirar 1 por vez
            $quantidade = 1;
            $funcionario = $this->Usuarios->getUsuariosByProfileType(Configure::read("profileTypes")["DummyWorkerProfileType"], 1);
            $tipoPagamento = $data["tipo_pagamento"];

            $retorno = $this->trataCompraCupom(
                $brindesId,
                $usuariosId,
                $clientesId,
                $quantidade,
                $funcionario["id"],
                $tipoPagamento,
                false,
                "",
                true
            );

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

                $usuariosId = $this->Auth->user()["id"];
                $valorPagoMin = !empty($data["valor_pago_min"]) ? (float)$data["valor_pago_min"] : null;
                $valorPagoMax = !empty($data["valor_pago_max"]) ? (float)$data["valor_pago_max"] : null;
                $dataInicio = !empty($data["data_inicio"]) ? date_format(DateTime::createFromFormat("d/m/Y", $data["data_inicio"]), "Y-m-d") : null;
                $dataFim = !empty($data["data_fim"]) ? date_format(DateTime::createFromFormat("d/m/Y", $data["data_fim"]), "Y-m-d") : null;
                $brindesNome = !empty($data["brindes_nome"]) ? $data["brindes_nome"] : null;
                $tipoVenda = !empty($data["tipo_venda"]) ? $data["tipo_venda"] : TYPE_SELL_CURRENCY_OR_POINTS_TEXT;
                $redesId = !empty($data["redes_id"]) ? $data["redes_id"] : null;
                $clientesId = !empty($data["clientes_id"]) ? $data["clientes_id"] : null;

                if (!in_array($tipoVenda, array(TYPE_SELL_FREE_TEXT, TYPE_SELL_DISCOUNT_TEXT, TYPE_SELL_CURRENCY_OR_POINTS_TEXT))) {
                    $message = MESSAGE_LOAD_DATA_WITH_ERROR;
                    ResponseUtil::errorAPI($message, array(TYPE_SELL_EMPTY));
                }

                $whereConditions = array("Cupons.usuarios_id" => $usuariosId);

                if (!empty($tipoVenda)) {
                    $whereConditions[] = array("Brindes.tipo_venda" => $tipoVenda);
                }

                $orderConditions = array();
                $paginationConditions = array();
                $redesId = 0;
                $clientesIds = array();

                if (isset($data["order_by"])) {
                    $orderConditions = $data["order_by"];
                } else {
                    $orderConditions = array("data" => "DESC");
                }

                if (isset($data["pagination"])) {
                    $paginationConditions = $data["pagination"];

                    if ($paginationConditions["page"] < 1) {
                        $paginationConditions["page"] = 1;
                    }
                }

                // Pesquisa por Redes
                if (!empty($redesId)) {
                    $clientesIds = $this->RedesHasClientes->getClientesIdsFromRedesHasClientes($redesId);

                    if (count($clientesIds) == 0) {
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
                elseif (!empty($clientesId)) {
                    $whereConditions[] = array('Cupons.clientes_id' => $clientesId);
                }

                if (isset($data["brindes_nome"])) {
                    // $whereConditions[] = array("Cupons.ClientesHasBrindesHabilitados.Brindes.nome like '%" . $data["brindes_nome"] . "%'");
                    $whereConditions[] = array("Brindes.nome like '%" . $data["brindes_nome"] . "%'");
                }

                // Valor pago à compra
                if (!empty($valorPagoMin) && !empty($valorPagoMax)) {
                    $whereConditions[] = array("Cupons.valor_pago_gotas BETWEEN '{$valorPagoMin}' AND '{$valorPagoMax}'");
                    $whereConditions[] = array("Cupons.valor_pago_reais BETWEEN '{$valorPagoMin}' AND '{$valorPagoMax}'");
                } elseif (!empty($valorPagoMin)) {
                    $whereConditions[] = array("Cupons.valor_pago_gotas >= " => $valorPagoMin);
                    $whereConditions[] = array("Cupons.valor_pago_reais >= " => $valorPagoMin);
                } elseif (!empty($valorPagoMax)) {
                    $whereConditions[] = array("Cupons.valor_pago_gotas <= " => $valorPagoMax);
                    $whereConditions[] = array("Cupons.valor_pago_reais <= " => $valorPagoMax);
                }

                if (!empty($dataInicio) && !empty($dataFim)) {

                    $whereConditions[] = array("Cupons.data >= " => $dataInicio . " 00:00:00");
                    $whereConditions[] = array("Cupons.data <= " => $dataFim . " 23:59:59");
                } elseif (!empty($dataInicio)) {
                    $whereConditions[] = array("Cupons.data >= " => $dataInicio . " 00:00:00");
                } elseif (!empty($dataFim)) {
                    $whereConditions[] = array("Cupons.data <= " => $dataFim . " 23:59:59");
                } else {
                    $dataFim = date("Y-m-d 23:59:59");
                    $dataInicio = date('Y-m-d 00:00:00', strtotime("-30 days"));

                    $whereConditions[] = ["Cupons.data >= " => $dataInicio];
                    $whereConditions[] = ["Cupons.data <= " => $dataFim];
                }

                $orderConditionsNew = array();

                foreach ($orderConditions as $key => $order) {
                    $orderConditionsNew["Cupons." . $key] = $order;
                }

                $orderConditions = $orderConditionsNew;

                $resultado = $this->Cupons->getCupons($whereConditions, $orderConditions, $paginationConditions);

                // DebugUtil::printArray($resultado);
                $mensagem = $resultado["mensagem"];
                $cupons = $resultado["cupons"];
            }
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $messageString = __("Não foi possível obter dados de cupons do usuário!");

            $mensagem = ['status' => false, 'message' => $messageString, 'errors' => $trace];

            // @todo: @gustavosg melhorar
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
     * @param integer $funcionariosId Id de Funcionários
     *              (se a compra via dash de funcionário)
     * @param string $tipoPagamento Tipo de Moeda (se é Gotas ou Reais)
     * @param bool $vendaAvulsa Indica se é usuário avulso no sistema
     * @param string $senhaAtualUsuario Senha atual do usuário (quando via Web)
     * @param bool $usoViaMobile Via Mobile ou via Web (Uso via mobile não pede confirmação de senha)
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 03/07/2018
     *
     * @return array $dados Tratados
     */
    private function trataCompraCupom(int $brindesId, int $usuariosId, int $clientesId, float $quantidade = null, int $funcionariosId = null, string $tipoPagamento = "", $vendaAvulsa = false, string $senhaAtualUsuario = "", bool $usoViaMobile = false)
    {
        $retorno = array();
        $mensagem = array();

        // pega id de todos os clientes que estão ligados à uma rede
        $redesHasClientes = $this->RedesHasClientes->getRedesHasClientesByClientesId($clientesId);
        $rede = $redesHasClientes["rede"];
        $clientesIds = $this->RedesHasClientes->getClientesIdsFromRedesHasClientes($rede["id"]);

        // Verifica se o cliente final estourou o número de compras por dia na Rede

        $retornoCompras = 0;

        // Validação acontecerá somente se for usuário identificado
        if (!empty($usuariosId)) {

            $retornoCompras = $this->UsuariosHasBrindes->checkNumberRescuesUsuarioRede($rede["id"], $usuariosId);

            if ($retornoCompras >= $rede["quantidade_consumo_usuarios_dia"]) {
                $message = "Usuário já atingiu o número de compras permitido por dia na rede!";
                $mensagem = array(
                    "status" => 0,
                    "message" => Configure::read("messageOperationFailureDuringProcessing"),
                    "errors" => array($message),
                );

                $arraySet = array(
                    "mensagem"
                );

                $retorno = array(
                    "arraySet" => $arraySet,
                    "mensagem" => $mensagem,
                    "ticket" => null,
                    "cliente" => null,
                    "usuario" => null,
                    "tempo" => null,
                    "tipo_emissao_codigo_barras" => null
                );
                return $retorno;
            }
        }

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
            "longitude",
            "propaganda_img"
        );
        $cliente = $this->Clientes->getClienteById($clientesId, $listaCamposClienteSelect);

        $brinde = $this->Brindes->getBrindeById($brindesId);

        // Se não encontrado brinde, retorna msg erro com vazio.
        if (empty($brinde)) {

            $mensagem = array(
                "status" => 0,
                "message" => Configure::read("messageOperationFailureDuringProcessing"),
                "errors" => array(MESSAGE_CLIENTE_DOES_NOT_HAVE_BRINDE)
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

        $tipoVendaBrinde = $brinde["tipo_venda"];

        $precoGotas = 0;
        $precoReais = 0;
        if ($tipoVendaBrinde == TYPE_SELL_FREE_TEXT) {
            // Se Isento
            $precoGotas = 0;
            $precoReais = 0;
        } elseif ($tipoVendaBrinde == TYPE_SELL_DISCOUNT_TEXT) {
            // Se brinde com desconto
            $precoGotas = $brinde["preco_atual"]["preco"];
            $precoReais = $brinde["preco_atual"]["valor_moeda_venda"];
        } else {
            // Se brinde = cobrança normal
            if ($tipoPagamento == TYPE_PAYMENT_MONEY) {
                $precoReais = $brinde["preco_atual"]["valor_moeda_venda"];
            } else {
                $precoGotas = $brinde["preco_atual"]["preco"];
            }
        }
        // Verifica se o brinde em questão está com preço zerado.
        /**
         * Apesar desta situação ser difícil de acontecer, pois o FrontEnd possui restrições, devo verificar da seguinte forma:
         * 1 - Isento:
         * Não precisa de preço
         * 2 - Com Desconto;
         * Os dois preços precisam estar configurados
         * 3 - Gotas ou Reais
         * Aqui depende da transação. Se tiver como Dinheiro, então o campo 'valor_moeda_venda' não pode estar vazio.
         * Caso contrário, o campo 'preco'
         */
        $error = false;
        if ($tipoVendaBrinde == TYPE_SELL_DISCOUNT_TEXT && (empty($precoGotas) && empty($precoReais))) {
            $mensagemErro = "Preço de Gotas e Reais do Brinde não estão configurados realizar a venda!";
            $error = true;
        } else if ($tipoVendaBrinde == TYPE_SELL_CURRENCY_OR_POINTS_TEXT) {
            if (empty($precoGotas) && $tipoPagamento == TYPE_PAYMENT_POINTS) {
                $mensagemErro = "Preço de Gotas do Brinde não estão configurados para realizar a venda!";
                $error = true;
            } elseif (empty($precoReais) && $tipoPagamento == TYPE_PAYMENT_MONEY) {
                $mensagemErro = "Preço de Reais do Brinde não estão configurados para realizar a venda!";
                $error = true;
            }
        }

        if ($error) {
            //  Comunique seu gestor!
            $mensagem = array(
                "status" => 0,
                "message" => Configure::read("messageOperationFailureDuringProcessing"),
                "errors" => array(
                    sprintf("%s %s", $mensagemErro, "Comunique seu gestor!")
                )
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
        $mensagem = array(
            "status" => 0,
            "message" => "",
            "errors" => array(__(""))
        );

        // Se for equipamento RTI, a quantidade máxima é 1
        if ($brinde["equipamento_rti"] == TYPE_EQUIPMENT_RTI) {
            $quantidade = 1;
        }

        if (($vendaAvulsa) && (empty($usuariosId))) {
            $usuario = $this->Usuarios->getUsuariosByProfileType(PROFILE_TYPE_DUMMY_USER, 1);
            $usuariosId = $usuario["id"];
        } else {
            $usuario = $this->Usuarios->getUsuarioById($usuariosId);
        }

        /**
         * Validação de senha do usuário
         * Só deve ocorrer se não for via mobile.
         */

        $senhaValida = false;

        if ($usoViaMobile) {
            $senhaValida = true;
        } elseif ($vendaAvulsa) {
            // Venda Avulsa não precisa de confirmação de senha, conforme solicitação do Samuel
            $senhaValida = true;
        } elseif (($usuario["tipo_perfil"] < Configure::read('profileTypes')['DummyUserProfileType']) || !$vendaAvulsa) {

            if ((new DefaultPasswordHasher)->check($senhaAtualUsuario, $usuario->senha)) {
                $senhaValida = true;
            }
        } else {
            $senhaValida = true;
        }

        if (!$senhaValida) {
            if (strlen($senhaAtualUsuario) == 0) {
                $message = 'Informe a senha do usuário.';
            } else {
                $message = 'Senha incorreta para usuário. Nâo foi possível resgatar o brinde';
            }

            $mensagem = array(
                "status" => 0,
                "message" => Configure::read("messageOperationFailureDuringProcessing"),
                "errors" => array($message),
            );

            $arraySet = array(
                "mensagem"
            );

            $retorno = array(
                "arraySet" => $arraySet,
                "mensagem" => $mensagem,
                "ticket" => null,
                "cliente" => null,
                "usuario" => null,
                "tempo" => null,
                "tipo_emissao_codigo_barras" => null
            );
            return $retorno;
        }

        // if ($vendaAvulsa) {
        //     $usuario["pontuacoes"] = 0;
        // } else {
        // $usuario = $this->Usuarios->getUsuarioById($usuariosId);
        $detalhesPontuacaoResultado = $this->Pontuacoes->getSumPontuacoesOfUsuario(
            $usuariosId,
            $rede["id"],
            $clientesIds
        );

        $usuario['pontuacoes'] = $detalhesPontuacaoResultado["resumo_gotas"]["saldo"];
        // }


        // Se o usuário tiver pontuações suficientes ou for venda avulsa
        if (($usuario["pontuacoes"] >= $brinde["preco_atual"]["preco"] * $quantidade) || $vendaAvulsa) {

            // verificar se cliente possui usuario em sua lista de usuários. se não tiver, cadastrar
            $clientesHasUsuariosConditions = array();

            $clientesHasUsuariosConditions[] = array('ClientesHasUsuarios.usuarios_id' => $usuario['id']);
            $clientesHasUsuariosConditions[] = array('ClientesHasUsuarios.clientes_id IN' => $clientesIds);

            $clientePossuiUsuario = $this->ClientesHasUsuarios->findClienteHasUsuario($clientesHasUsuariosConditions);

            if (is_null($clientePossuiUsuario)) {
                $this->ClientesHasUsuarios->saveClienteHasUsuario($clientesId, $usuariosId);
            }

            // Realiza a venda de pontuações

            try {

                /**
                 * Se for desconto ou o tipo de venda é gotas ou reais e o tipo de transação for pontos...
                 * Devo descontar do usuário os pontos necessários, atualizando cada registro,
                 * para diminuir o saldo do mesmo
                 */
                if (($tipoVendaBrinde == TYPE_SELL_DISCOUNT_TEXT)
                    || ($tipoVendaBrinde == TYPE_SELL_CURRENCY_OR_POINTS_TEXT && $tipoPagamento == TYPE_PAYMENT_POINTS)
                ) {
                    $pontuacoesProcessar = $precoGotas * $quantidade;

                    $podeContinuar = true;
                    $pontuacoesPendentesUsoListaSave = [];

                    // Obter pontos não utilizados totalmente
                    // verifica se tem algum pendente para continuar o cálculo sobre ele

                    $pontuacaoPendenteUso = $this->Pontuacoes->getPontuacoesPendentesForUsuario(
                        $usuario["id"],
                        $clientesIds,
                        1,
                        null
                    );

                    if ($pontuacaoPendenteUso) {
                        $ultimoId = $pontuacaoPendenteUso["id"];
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
                        $pontuacoesPendentesUso = $this->Pontuacoes->getPontuacoesPendentesForUsuario(
                            $usuario->id,
                            $clientesIds,
                            10,
                            $ultimoId
                        );

                        $pontuacoesPendentesUso = $pontuacoesPendentesUso->toArray();
                        if (empty($pontuacoesPendentesUso)) {
                            break;
                        }

                        if (count($pontuacoesPendentesUso) == 0) {
                            $podeContinuar = false;
                            break;
                        }

                        $maximoContador = count($pontuacoesPendentesUso);

                        $contador = 0;
                        foreach ($pontuacoesPendentesUso as $pontuacao) {

                            if (($pontuacoesProcessar >= 0) && ($pontuacoesProcessar >= $pontuacao->quantidade_gotas)) {
                                array_push(
                                    $pontuacoesPendentesUsoListaSave,
                                    array(
                                        'id' => $pontuacao->id,
                                        'utilizado' => 2
                                    )
                                );
                            } else {
                                array_push(
                                    $pontuacoesPendentesUsoListaSave,
                                    array(
                                        'id' => $pontuacao->id,
                                        'utilizado' => 1
                                    )
                                );
                            }

                            $pontuacoesProcessar = $pontuacoesProcessar - $pontuacao["quantidade_gotas"];
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
                }

                // efetua saida na tabela de estoque

                $estoque = $this->BrindesEstoque->addBrindeEstoque($brinde["id"], $usuario["id"], $quantidade, TYPE_OPERATION_SELL_CURRENCY);

                // atribui uso de pontuações ao usuário se o Brinde não for Isento
                if ($tipoVendaBrinde != TYPE_SELL_FREE_TEXT) {
                    $this->Pontuacoes->addPontuacoesBrindesForUsuario(
                        $cliente["id"],
                        $usuario["id"],
                        $brinde["id"],
                        $precoGotas * $quantidade,
                        $precoReais * $quantidade,
                        $funcionariosId,
                        false
                    );
                }

                // Emitir Cupom e retornar
                $cupom = $this->Cupons->addCupomForUsuario(
                    $brinde["id"],
                    $cliente["id"],
                    $funcionariosId,
                    $usuario["id"],
                    $precoGotas * $quantidade,
                    $precoReais * $quantidade,
                    $quantidade,
                    // Dinheiro
                    $tipoVendaBrinde
                );

                // vincula item resgatado ao cliente final
                $brindeUsuario = $this->UsuariosHasBrindes->addUsuarioHasBrindes(
                    $rede["id"],
                    $cliente["id"],
                    $usuario["id"],
                    $brinde["id"],
                    $quantidade,
                    $precoGotas,
                    $precoReais,
                    $tipoPagamento,
                    $cupom["id"]
                );

                $mensagem = array(
                    "status" => 1,
                    "message" => Configure::read("messageProcessingCompleted"),
                    "errors" => array()
                );

                if (empty($cupom)) {

                    $mensagem = array(
                        "status" => 0,
                        "message" => Configure::read("messageOperationFailureDuringProcessing"),
                        "errors" => array("Houve um erro na geração do Ticket. Informe ao suporte.")
                    );
                }
                $arraySet = array(
                    'mensagem',
                    'ticket',
                    'cliente',
                    'usuario',
                    'tempo',
                    'tipo_emissao_codigo_barras',
                    "is_brinde_smart_shower",
                    'dados_impressao'
                );

                $retorno = array(
                    "arraySet" => $arraySet,
                    "mensagem" => $mensagem,
                    "ticket" => $cupom,
                    "status" => $mensagem["status"],
                    "cliente" => $cliente,
                    "usuario" => $usuario,
                    "tempo" => $brinde["tempo_uso_brinde"],
                    "tipo_emissao_codigo_barras" => $brinde["tipo_codigo_barras"],
                    "is_brinde_smart_shower" => $brinde["tipos_brindes_cliente"]["tipo_principal_codigo_brinde"] <= 4,
                );

                return $retorno;
                // Gera o cupom
            } catch (\Exception $e) {
                $message = $e->getMessage();
                $trace = $e->getTraceAsString();
                $messageLog = sprintf("Exceção durante processamento de cupom: %s %s [Função: %s / Arquivo: %s, Linha: %s].", $message, $trace, __FUNCTION__, __FILE__, __LINE__);
                Log::write("error", $messageLog);
            }


            // Se é Banho
            $tipoPrincipalCodigoBrinde = $brinde["tipos_brindes_cliente"]["tipo_principal_codigo_brinde"];
            $isBrindeSmartShower = is_numeric($tipoPrincipalCodigoBrinde) && $tipoPrincipalCodigoBrinde <= 4;
            $cupons = $this->Cupons->getCuponsByCupomEmitido($ticket["cupom_emitido"])->toArray();
            $cuponsRetorno = array();

            foreach ($cupons as $key => $cupom) {
                $cupom["data"] = $cupom["data"]->format('d/m/Y H:i:s');
                $cuponsRetorno[] = $cupom;
            }

            $dados_impressao = $this->processarCupom($cuponsRetorno);

            // Se chegou até aqui, ocorreu tudo bem
            $mensagem = array(
                "status" => 1,
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
                "tempo" => $brinde["tempo_uso_brinde"],
                "tipo_emissao_codigo_barras" => $brinde["tipo_codigo_barras"],
                "is_brinde_smart_shower" => $isBrindeSmartShower,
                "dados_impressao" => $dados_impressao
            );

            return $retorno;
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
                "dados_impressao" => null
            );

            return $retorno;
        }
    }

    /**
     * Realiza processamento do cupom (tratamento) para json
     */
    private function processarCupom($cupons)
    {
        // @todo gustavosg é bom revisar este processo
        $sessaoUsuario = $this->getSessionUserVariables();
        $usuarioAdministrador = $sessaoUsuario["usuarioAdministrador"];
        $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"];

        if ($usuarioAdministrador) {
            $this->usuarioLogado = $usuarioAdministrar;
        }

        $funcionario = $this->usuarioLogado;

        if (empty($funcionario)) {
            $funcionario = $this->Usuarios->getFuncionarioFicticio();
        }

        $funcionariosId = $funcionario["id"];

        // checagem de cupons

        $clientesVendaId = 0;
        try {
            $clienteHasUsuario = $this->ClientesHasUsuarios->findClienteHasUsuario(array('ClientesHasUsuarios.usuarios_id' => $funcionariosId));

            $clientesVendaId = $clienteHasUsuario["clientes_id"];

            if (empty($clientesVendaId)) {
                throw new Exception("Este funcionário não está vinculado à um posto de atendimento!");
            }
        } catch (\Throwable $th) {
            $trace = $th->getTraceAsString();
            $message = $th->getMessage();
            $stringError = __("Erro ao buscar Posto de Atendimento: {0}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $message, __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write("error", $trace);

            ResponseUtil::error($stringError, $message);
        }

        $cuponsRetorno = array();
        if (count($cupons) > 0) {
            foreach ($cupons as $cupom) {
                # code...
                // verifica se o cupom já foi resgatado

                if ($cupom["resgatado"]) {

                    // Qualquer cupom resgatado do código signifca que o cupom inteiro já foi resgatado
                    $result = array(
                        'status' => false,
                        'message' => MESSAGE_CUPOM_ALREADY_RETRIEVED
                    );

                    return $result;
                }

                // verifica se o cupom pertence à rede que o funcionário está logado

                $clientesId = $cupom["clientes_id"];
                $brindesId = $cupom["brindes_id"];

                // pega a rede e procura todas as unidades

                $redeHasCliente = $this->RedesHasClientes->getRedesHasClientesByClientesId($clientesId);

                $redesHasClientes = $this->RedesHasClientes->getRedesHasClientesByRedesId($redeHasCliente["redes_id"]);

                $encontrouCupom = false;

                // procura o brinde dentro da rede
                foreach ($redesHasClientes as $key => $value) {
                    if ($clientesId == $value["clientes_id"]) {
                        $encontrouCupom = true;
                        break;
                    }
                }

                // agora procura o funcionário dentro da rede
                if ($funcionario["tipo_perfil"] == PROFILE_TYPE_DUMMY_WORKER) {

                    // Se não estiver autenticado, ele estará fazendo o processo via celular
                    // então não tem como localizar, pois não há funcionario autenticado.
                    // O funcionário será o mesmo do cupom
                    $encontrouUsuario = true;
                    $unidade_funcionario_id = $clientesId;
                } else {

                    $encontrouUsuario = false;

                    $redeHasCliente = $this->RedesHasClientes->getRedesHasClientesByClientesId($clientesVendaId);
                    $redesHasClientes = $this->RedesHasClientes->getRedesHasClientesByRedesId($redeHasCliente->redes_id);

                    $unidadeFuncionarioId = 0;
                    foreach ($redesHasClientes as $key => $value) {
                        if ($clientesId == $value["clientes_id"]) {

                            $unidadeFuncionarioId = $clientesId;
                            $encontrouUsuario = true;
                            break;
                        }
                    }
                }

                // se não encontrou o brinde na unidade, ou não encontrou o usuário

                if (!$encontrouCupom || !$encontrouUsuario) {
                    return [
                        'status' => false,
                        'message' => __("Cupom pertencente à outra rede, não é possível importar dados!")
                    ];
                }

                // Se o brinde não for ilimitado, verifica se ele possui estoque suficiente

                $brinde = $this->Brindes->getBrindeById($brindesId);

                if (!$brinde->ilimitado) {

                    // verifica se a unidade que vai fazer o saque tem estoque

                    $quantidadeAtualBrinde = $this->BrindesEstoque->getEstoqueAtualForBrindeId($brindesId);

                    $resultadoFinal = $quantidadeAtualBrinde - $cupom["quantidade"];

                    if ($resultadoFinal < 0) {
                        return array(
                            'status' => false,
                            'message' => __("Não há estoque suficiente para resgatar este brinde no momento!")
                        );
                    }
                }

                // passou em todas as validações

                $cupom['unidade_funcionario_id'] = $unidadeFuncionarioId;

                $cuponsRetorno[] = $cupom;
            }

            return [
                'status' => true,
                'data' => $cuponsRetorno
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
