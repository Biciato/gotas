<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Custom\RTI\Security;
use Cake\Core\Configure;
use Cake\Collection\Collection;
use Cake\Event\Event;
use Cake\Routing\Router;
use Cake\Mailer\Email;
use Cake\View\Helper\UrlHelper;
use \DateTime;
use App\Custom\RTI\DateTimeUtil;
use App\Custom\RTI\DebugUtil;

/**
 * BrindesEstoque Controller
 *
 * @property \App\Model\Table\BrindesEstoqueTable $BrindesEstoque
 *
 * @method \App\Model\Entity\BrindesEstoque[] paginate($object = null, array $settings = [])
 */
class BrindesEstoqueController extends AppController
{
    /**
     * ------------------------------------------------------------
     * Fields
     * ------------------------------------------------------------
     */

    protected $usuarioLogado = null;

    /**
     * ------------------------------------------------------------
     * Métodos Comuns
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
            'contain' => ['Brindes', 'Usuarios']
        ];
        $BrindesEstoque = $this->paginate($this->BrindesEstoque);

        $this->set(compact('BrindesEstoque'));
        $this->set('_serialize', ['BrindesEstoque']);
    }

    /**
     * View method
     *
     * @param string|null $id Brindes Estoque id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $BrindesEstoque = $this->BrindesEstoque->get($id, [
            'contain' => ['Brindes', 'Usuarios']
        ]);

        $this->set('BrindesEstoque', $BrindesEstoque);
        $this->set('_serialize', ['BrindesEstoque']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $BrindesEstoque = $this->BrindesEstoque->newEntity();
        if ($this->request->is('post')) {
            $BrindesEstoque = $this->BrindesEstoque->patchEntity($BrindesEstoque, $this->request->getData());
            if ($this->BrindesEstoque->save($BrindesEstoque)) {
                $this->Flash->success(__('The brindes estoque has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The brindes estoque could not be saved. Please, try again.'));
        }
        $brindes = $this->BrindesEstoque->Brindes->find('list', ['limit' => 200]);
        $usuarios = $this->BrindesEstoque->Usuarios->find('list', ['limit' => 200]);
        $this->set(compact('BrindesEstoque', 'brindes', 'usuarios'));
        $this->set('_serialize', ['BrindesEstoque']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Brindes Estoque id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $BrindesEstoque = $this->BrindesEstoque->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $BrindesEstoque = $this->BrindesEstoque->patchEntity($BrindesEstoque, $this->request->getData());
            if ($this->BrindesEstoque->save($BrindesEstoque)) {
                $this->Flash->success(__('The brindes estoque has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The brindes estoque could not be saved. Please, try again.'));
        }
        $brindes = $this->BrindesEstoque->Brindes->find('list', ['limit' => 200]);
        $usuarios = $this->BrindesEstoque->Usuarios->find('list', ['limit' => 200]);
        $this->set(compact('BrindesEstoque', 'brindes', 'usuarios'));
        $this->set('_serialize', ['BrindesEstoque']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Brindes Estoque id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $BrindesEstoque = $this->BrindesEstoque->get($id);
        if ($this->BrindesEstoque->delete($BrindesEstoque)) {
            $this->Flash->success(__('The brindes estoque has been deleted.'));
        } else {
            $this->Flash->error(__('The brindes estoque could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * ------------------------------------------------------------
     * Custom Methods
     * ------------------------------------------------------------
     */

    /**
     * Action para gerenciar estoque de Brinde
     *
     * @param int $brindes_id Id do Brinde
     *
     * @return \Cake\Http\Response|void
     */
    public function gerenciarEstoque($brindes_id)
    {
        $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
        $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

        if ($usuarioAdministrador) {
            $this->usuarioLogado = $usuarioAdministrar;
        }

        $cliente_has_brinde_habilitado = $this->ClientesHasBrindesHabilitados->getBrindeHabilitadoById($brindes_id);

        $clientes_id = $cliente_has_brinde_habilitado->clientes_id;

        $array_set = [
            'cliente_has_brinde_habilitado',
            'clientes_id',
            'brindes_id'
        ];
        $this->set(compact([$array_set]));
        $this->set('_serialize', [$array_set]);
    }

    /**
     * Action para adicionar estoque para Brinde
     *
     * @param int $id Id do Brinde
     *
     * @return \Cake\Http\Response|void
     **/
    public function adicionarEstoque($id)
    {
        $arraySet = array('brinde', 'brindeEstoque', 'brindes_id', 'clientesId');
        $brinde = $this->Brindes->getBrindeById($id);
        $sessaoUsuario = $this->getSessionUserVariables();
        $usuarioAdministrador = $sessaoUsuario["usuarioAdministrador"];
        $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"];

        if ($usuarioAdministrar) {
            $this->usuarioLogado = $usuarioAdministrar;
        }

        $rede = $sessaoUsuario["rede"];
        $cliente = $sessaoUsuario["cliente"];

        if (empty($rede)) {
            $redeHasCliente = $this->RedesHasClientes->getRedesHasClientesByClientesId($brinde["clientes_id"]);
            $rede = $redeHasCliente["rede"];
            $cliente = $redeHasCliente["cliente"];
        }
        $clientesId = $cliente["id"];

        $brindeEstoque = $this->BrindesEstoque->newEntity();

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();

            $quantidade = !empty($data["quantidade"]) ? $data["quantidade"] : null;
            if (empty($quantidade)) {
                $this->Flash->error(MESSAGE_BRINDES_ESTOQUE_QUANTITY_EMPTY);

                $this->set(compact($arraySet));
                return;
            }

            $brindeSave = $this->BrindesEstoque->addBrindeEstoque($brinde["id"], $this->usuarioLogado["id"], $quantidade, TYPE_OPERATION_ADD_STOCK);

            if ($brindeSave) {
                $this->Flash->success(Configure::read('messageSavedSuccess'));

                return $this->redirect(['controller' => 'brindes', 'action' => 'view', $id]);
            }

            $this->Flash->error(Configure::read('messageSavedError'));
        }

        $this->set(compact([$arraySet]));
        // $this->set('_serialize', [$array_set]);
    }

    /**
     * Action para vender um item de Brinde
     *
     * @param int $brindesId Id do Brinde
     *
     * @return \Cake\Http\Response|void
     **/
    public function vendaManualEstoque($brindesId)
    {
        $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
        $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');
        $rede = $this->request->session()->read('Rede.Grupo');

        if ($usuarioAdministrador) {
            $this->usuarioLogado = $usuarioAdministrar;
            $usuarioLogado = $usuarioAdministrar;
        }

        $brinde = $this->Brindes->getBrindeById($brindesId);

        $clientes_id = $brinde->clientes_id;

        $brindeEstoque = $this->BrindesEstoque->newEntity();

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();

            // verifica se possui ponto e virgula
            if (strpos($data['preco'], '.') && strpos($data['preco'], ',')) {

                // se tiver, troca a virgula por ponto

                $data['preco'] = str_replace('.', '', $data['preco']);
                $data['preco'] = str_replace(',', '.', $data['preco']);
            } elseif (strpos($data['preco'], ',')) {
                $data['preco'] = str_replace(',', '.', $data['preco']);
            }

            // @todo @gustavosg Ajustar!
            $totalPontosAGastar = $data['quantidade'] * (float)$data['preco'];

            $usuario = null;
            // Se usuário for nulo, define venda para o usuário avulso
            if (empty($data["usuarios_id"])) {
                $usuario = $this->Usuarios->getUsuariosByProfileType(Configure::read("profileTypes")["DummyUserProfileType"], 1);
            } else {
                $usuario = $this->Usuarios->getUsuarioById($data['usuarios_id']);
            }

            $array_clientes_id = $this->Clientes->getIdsMatrizFiliaisByClienteId($clientes_id);

            // verificar se tem estoque suficiente na loja em questão

            $estoqueAtual = $this->BrindesEstoque->checkBrindeHasEstoqueByBrindesHabilitadosId($brindesId, $data['quantidade']);

            $possuiEstoque = $estoqueAtual["enough"] == true || $brinde["brinde"]["ilimitado"];

            if (!$possuiEstoque) {
                $restante = strlen($estoqueAtual['left']) > 0 ? $estoqueAtual['left'] : 0;
                $this->Flash->error(__('Não há estoque suficiente para vender, solicitado {0}, restam {1}', $data['quantidade'], $restante));
            }

            if ($usuario && $possuiEstoque) {
                // Pegar soma de pontuações do usuário para saber se ele tem saldo
                $usuario->pontuacoes
                    = $this->Pontuacoes->getSumPontuacoesOfUsuario(
                        $usuario['id'],
                        $rede["id"],
                        $array_clientes_id
                    );

                if ($usuario->pontuacoes < $totalPontosAGastar) {
                    $this->Flash->error(__("Usuário com saldo insuficiente. Usuário possui {0} gotas, e o valor necessário de gotas é de {1}.", $usuario->pontuacoes, $totalPontosAGastar));
                } else {
                    //usuário possui pontuação suficiente, cliente tem brinde suficiente, iniciar as transações

                    // Diminuir estoque do cliente
                    $brindeEstoque = $this->BrindesEstoque->addBrindeEstoque(
                        $brinde->id,
                        $usuario->id,
                        $data['quantidade'],
                        Configure::read('stockOperationTypes')['sellTypeGift']
                    );

                    // adicionar brinde resgatado no cadastro do usuário
                    $brindeUsuario
                        = $this->UsuariosHasBrindes->addUsuarioHasBrindes(
                            $rede["id"],
                            $clientes_id,
                            $usuario["id"],
                            $brinde->id,
                            $data['quantidade'],
                            $totalPontosAGastar,
                            TYPE_PAYMENT_POINTS
                        );

                    // salvar pontuação do usuário
                    $pontos = $this->Pontuacoes->addPontuacoesBrindesForUsuario(
                        $clientes_id,
                        $usuario->id,
                        $brinde->id,
                        $totalPontosAGastar,
                        $this->usuarioLogado["id"],
                        true
                    );

                    if ($brindeEstoque && $brindeUsuario && $pontos) {
                        $this->Flash->success('Venda realizada');

                        return $this->redirect(['controller' => 'clientes_has_brindes_habilitados', 'action' => 'configurar_brinde', $brindesId]);
                    }
                }
            }
        }

        $array_set = [
            'brinde',
            'brindeEstoque',
            'cliente',
            'clientes_id',
            'brindesId'
        ];

        $this->set(compact($array_set));
        $this->set('_serialize', $array_set);
    }

    /**
     * ------------------------------------------------------------
     * Relatórios (Dashboard Admin RTI)
     * ------------------------------------------------------------
     */

    /**
     * Exibe a action de Relatorio Estoque de Brindes Habilitados por Redes
     *
     * @return \Cake\Network\Response|null|void
     */
    public function relatorioEstoqueBrindesRedes()
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

        // este relatório não precisa mostrar os brindes RTI Shower

        // TODO: ajustar
        $whereConditions[] = ["brindes.equipamento_rti_shower" => false];

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
             * TODO: tenho que pegar todas as unidades de uma rede
             * uma vez que eu pegue cada unidade, eu tenho que
             * pegar todos os brindes aos quais estão nela.
             */

            $brindesHabilitadosReturn = array();

            $cliente = null;
            foreach ($clientesIds as $key => $clienteId) {

                // $brindesHabilitadosArray = $this->ClientesHasBrindesHabilitados->getBrindesHabilitadosByClienteId(
                $brindesHabilitadosArray = $this->ClientesHasBrindesHabilitados->getBrindesHabilitadosByClienteId(
                    [$clienteId],
                    $arrayWhereConditions
                )->toArray();

                if (sizeof($brindesHabilitadosArray) > 0) {
                    $brindesHabilitadosArrayTmp = array();
                    foreach ($brindesHabilitadosArray as $key => $brindeHabilitado) {


                        $brindeHabilitado = $this->ClientesHasBrindesHabilitados->getBrindeHabilitadoById($brindeHabilitado->id);

                        array_push($brindesHabilitadosArrayTmp, $brindeHabilitado);
                    }

                    $brindesHabilitadosArray = $brindesHabilitadosArrayTmp;

                    $brindesHabilitadosReturn[] = $brindesHabilitadosArray;
                }
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
     * Exibe um relatório completo
     *
     * @param integer $clientesHasBrindesHabilitadoId Id do Brinde habilitado
     * @return \Cake\Network\Response|null|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function relatorioEstoqueBrindesDetalhado(int $clientesHasBrindesHabilitadoId)
    {
        $brinde = $this->ClientesHasBrindesHabilitados->getBrindeHabilitadoById($clientesHasBrindesHabilitadoId);

        $qteRegistros = 10;

        $whereConditions = array();

        if (isset($brinde['id'])) {

            if ($this->request->is('post')) {

                $data = $this->request->getData();

                // tipo de operacao

                if (strlen($data['tipoOperacao']) > 0) {
                    $whereConditions[] = ['tipo_operacao' => (int)$data['tipoOperacao']];
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
                        $whereConditions[] = ['BrindesEstoque.audit_insert BETWEEN "' . $dataInicial . '" and "' . $dataFinal . '"'];
                    }
                } else if (strlen($data['auditInsertInicio']) > 0) {

                    if ($dataInicial > $dataHoje) {
                        $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Início'));
                    } else {
                        $whereConditions[] = ['BrindesEstoque.audit_insert >= ' => $dataInicial];
                    }
                } else if (strlen($data['auditInsertFim']) > 0) {

                    if ($dataFinal > $dataHoje) {
                        $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Fim'));
                    } else {
                        $whereConditions[] = ['BrindesEstoque.audit_insert <= ' => $dataFinal];
                    }
                }
            }

            $rede = $this->RedesHasClientes->getRedesHasClientesByClientesId($brinde->clientes_id)->rede;

            $cliente = $this->Clientes->getClienteById($brinde->clientes_id);

            $historicoEstoqueBrinde = $this->BrindesEstoque->getEstoqueForBrinde($clientesHasBrindesHabilitadoId, null, $whereConditions, $qteRegistros);
        }

        $arraySet = [
            'brinde',
            'cliente',
            'rede',
            'historicoEstoqueBrinde'
        ];

        $this->set(compact($arraySet));
    }

    /**
     * ------------------------------------------------------------
     * Initialize Methods
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

        if (is_null($this->security)) {
            $this->security = new Security();
        }
    }

    /**
     *
     */
    public function initialize()
    {
        parent::initialize();
    }
}
