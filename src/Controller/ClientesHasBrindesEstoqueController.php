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

/**
 * ClientesHasBrindesEstoque Controller
 *
 * @property \App\Model\Table\ClientesHasBrindesEstoqueTable $ClientesHasBrindesEstoque
 *
 * @method \App\Model\Entity\ClientesHasBrindesEstoque[] paginate($object = null, array $settings = [])
 */
class ClientesHasBrindesEstoqueController extends AppController
{
    /**
     * ------------------------------------------------------------
     * Fields
     * ------------------------------------------------------------
     */

    protected $user_logged = null;

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
        $clientesHasBrindesEstoque = $this->paginate($this->ClientesHasBrindesEstoque);

        $this->set(compact('clientesHasBrindesEstoque'));
        $this->set('_serialize', ['clientesHasBrindesEstoque']);
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
        $clientesHasBrindesEstoque = $this->ClientesHasBrindesEstoque->get($id, [
            'contain' => ['Brindes', 'Usuarios']
        ]);

        $this->set('clientesHasBrindesEstoque', $clientesHasBrindesEstoque);
        $this->set('_serialize', ['clientesHasBrindesEstoque']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $clientesHasBrindesEstoque = $this->ClientesHasBrindesEstoque->newEntity();
        if ($this->request->is('post')) {
            $clientesHasBrindesEstoque = $this->ClientesHasBrindesEstoque->patchEntity($clientesHasBrindesEstoque, $this->request->getData());
            if ($this->ClientesHasBrindesEstoque->save($clientesHasBrindesEstoque)) {
                $this->Flash->success(__('The brindes estoque has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The brindes estoque could not be saved. Please, try again.'));
        }
        $brindes = $this->ClientesHasBrindesEstoque->Brindes->find('list', ['limit' => 200]);
        $usuarios = $this->ClientesHasBrindesEstoque->Usuarios->find('list', ['limit' => 200]);
        $this->set(compact('clientesHasBrindesEstoque', 'brindes', 'usuarios'));
        $this->set('_serialize', ['clientesHasBrindesEstoque']);
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
        $clientesHasBrindesEstoque = $this->ClientesHasBrindesEstoque->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $clientesHasBrindesEstoque = $this->ClientesHasBrindesEstoque->patchEntity($clientesHasBrindesEstoque, $this->request->getData());
            if ($this->ClientesHasBrindesEstoque->save($clientesHasBrindesEstoque)) {
                $this->Flash->success(__('The brindes estoque has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The brindes estoque could not be saved. Please, try again.'));
        }
        $brindes = $this->ClientesHasBrindesEstoque->Brindes->find('list', ['limit' => 200]);
        $usuarios = $this->ClientesHasBrindesEstoque->Usuarios->find('list', ['limit' => 200]);
        $this->set(compact('clientesHasBrindesEstoque', 'brindes', 'usuarios'));
        $this->set('_serialize', ['clientesHasBrindesEstoque']);
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
        $clientesHasBrindesEstoque = $this->ClientesHasBrindesEstoque->get($id);
        if ($this->ClientesHasBrindesEstoque->delete($clientesHasBrindesEstoque)) {
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
        $user_admin = $this->request->session()->read('User.RootLogged');
        $user_managed = $this->request->session()->read('User.ToManage');

        if ($user_admin) {
            $this->user_logged = $user_managed;
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
     * @param int $brindes_id Id do Brinde
     *
     * @return \Cake\Http\Response|void
     **/
    public function adicionarEstoque($brindes_id)
    {
        $user_admin = $this->request->session()->read('User.RootLogged');
        $user_managed = $this->request->session()->read('User.ToManage');

        if ($user_admin) {
            $this->user_logged = $user_managed;
        }

        $brinde = $this->ClientesHasBrindesHabilitados->getBrindeHabilitadoById($brindes_id);

        $clientes_id = $brinde->clientes_id;

        $brinde_estoque = $this->ClientesHasBrindesEstoque->newEntity();

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();

            $data['clientes_has_brindes_habilitados_id'] = $brinde->id;
            $data['usuarios_id'] = $this->user_logged['id'];
            $data['data'] = date('Y-m-d H:i:s');
            $data['tipo_operacao'] = Configure::read('stockOperationTypes')['addType'];

            $brinde_estoque = $this->ClientesHasBrindesEstoque->patchEntity($brinde_estoque, $data);

            if ($this->ClientesHasBrindesEstoque->save($brinde_estoque)) {
                $this->Flash->success(Configure::read('messageSavedSuccess'));

                return $this->redirect(['controller' => 'clientes_has_brindes_habilitados', 'action' => 'configurar_brinde', $brindes_id]);
            }

            $this->Flash->error(Configure::read('messageSavedError'));
        }

        $array_set = [
            'brinde',
            'brinde_estoque',
            'brindes_id',
            'clientes_id'
        ];

        $this->set(compact([$array_set]));
        $this->set('_serialize', [$array_set]);
    }

    /**
     * Action para vender um item de Brinde
     *
     * @param int $brindes_id Id do Brinde
     *
     * @return \Cake\Http\Response|void
     **/
    public function vendaManualEstoque($brindes_id)
    {
        $user_admin = $this->request->session()->read('User.RootLogged');
        $user_managed = $this->request->session()->read('User.ToManage');

        if ($user_admin) {
            $this->user_logged = $user_managed;
        }

        $brinde = $this->ClientesHasBrindesHabilitados->getBrindeHabilitadoById($brindes_id);

        $clientes_id = $brinde->clientes_id;

        $brinde_estoque = $this->ClientesHasBrindesEstoque->newEntity();

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();

            // verifica se possui ponto e virgula
            if (strpos($data['preco'], '.') && strpos($data['preco'], ',')) {

                // se tiver, troca a virgula por ponto

                $data['preco'] = str_replace('.', '', $data['preco']);
                $data['preco'] = str_replace(',', '.', $data['preco']);
            } else if (strpos($data['preco'], ',')) {
                $data['preco'] = str_replace(',', '.', $data['preco']);
            }

            $totalPontosAGastar = $data['quantidade'] * (float)$data['preco'];

            $usuario = $this->Usuarios->getUsuarioById($data['usuarios_id']);

            $array_clientes_id = $this->Clientes->getIdsMatrizFiliaisByClienteId($clientes_id);

            // verificar se tem estoque suficiente na loja em questão

            $estoqueAtual = $this->ClientesHasBrindesEstoque->checkBrindeHasEstoqueByBrindesHabilitadosId($brindes_id, $data['quantidade']);

            if ($estoqueAtual['enough'] == false) {
                $qte = strlen($estoqueAtual['left']) > 0 ? $estoqueAtual['left'] : 0;
                $this->Flash->error(__('Não há estoque suficiente para vender, solicitado {0}, restam {1}', $data['quantidade'], $qte));
            }

            if ($usuario && $estoqueAtual['enough']) {
                // Pegar soma de pontuações do usuário para saber se ele tem saldo
                $usuario->pontuacoes
                    = $this->Pontuacoes->getSumPontuacoesOfUsuario(
                    $usuario['id'],
                    $array_clientes_id
                );

                if ($usuario->pontuacoes < $totalPontosAGastar) {
                    $this->Flash->error(__("Usuário com saldo insuficiente. Usuário possui {0} gotas, e o valor necessário de gotas é de {1}.", $usuario->pontuacoes, $totalPontosAGastar));
                } else {
                    //usuário possui pontuação suficiente, cliente tem brinde suficiente, iniciar as transações

                    // Diminuir estoque do cliente
                    $brinde_estoque = $this->ClientesHasBrindesEstoque->addEstoqueForBrindeId(
                        $brinde->id,
                        $usuario->id,
                        $data['quantidade'],
                        Configure::read('stockOperationTypes')['sellTypeGift']
                    );

                    // adicionar brinde resgatado no cadastro do usuário
                    $brindeUsuario
                        = $this->UsuariosHasBrindes->addUsuarioHasBrindes(
                        $usuario->id,
                        $brinde->id,
                        $data['quantidade'],
                        $totalPontosAGastar
                    );

                    // salvar pontuação do usuário
                    $pontos = $this->Pontuacoes->addPontuacoesBrindesForUsuario(
                        $clientes_id,
                        $usuario->id,
                        $brinde->id,
                        $totalPontosAGastar,
                        $this->user_logged["id"],
                        true
                    );

                    if ($brinde_estoque && $brindeUsuario && $pontos) {
                        $this->Flash->success('Venda realizada');

                        return $this->redirect(['controller' => 'clientes_has_brindes_habilitados', 'action' => 'configurar_brinde', $brindes_id]);
                    }
                }
            }
        }

        $array_set = [
            'brinde',
            'brinde_estoque',
            'cliente',
            'clientes_id',
            'brindes_id'
        ];

        $this->set(compact(
            [
                $array_set
            ]
        ));

        $this->set(
            '_serialize',
            [
                $array_set
            ]
        );
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
                        $whereConditions[] = ['ClientesHasBrindesEstoque.audit_insert BETWEEN "' . $dataInicial . '" and "' . $dataFinal . '"'];
                    }

                } else if (strlen($data['auditInsertInicio']) > 0) {

                    if ($dataInicial > $dataHoje) {
                        $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Início'));
                    } else {
                        $whereConditions[] = ['ClientesHasBrindesEstoque.audit_insert >= ' => $dataInicial];
                    }

                } else if (strlen($data['auditInsertFim']) > 0) {

                    if ($dataFinal > $dataHoje) {
                        $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Fim'));
                    } else {
                        $whereConditions[] = ['ClientesHasBrindesEstoque.audit_insert <= ' => $dataFinal];
                    }
                }
            }

            $rede = $this->RedesHasClientes->getRedesHasClientesByClientesId($brinde->clientes_id)->rede;

            $cliente = $this->Clientes->getClienteById($brinde->clientes_id);

            $historicoEstoqueBrinde = $this->ClientesHasBrindesEstoque->getEstoqueForBrindeId($clientesHasBrindesHabilitadoId, null, $whereConditions, $qteRegistros);
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

        $this->user_logged = $this->getUserLogged();
        $this->set('user_logged', $this->getUserLogged());
    }
}
