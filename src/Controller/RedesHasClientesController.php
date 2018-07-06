<?php

/**
 * Arquivo para Classe para execução em terminal (shell)
 *
 * @category Class
 * @package  App\Controller
 * @author   "Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>"
 * @date     24/11/2017
 * @license  http://www.apache.org/licenses/LICENSE-2.0.txt Apache License 2.0
 * @link     https://www.rtibrindes.com.br/
 */

namespace App\Controller;

use App\Controller\AppController;
use App\Model\Entity;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\ORM\Query;
use Cake\Core\Configure;
use Cake\Event\Event;
use App\Custom\RTI\Security;

/**
 * RedesHasClientes Controller
 *
 * @property \App\Model\Table\RedesHasClientesTable $RedesHasClientes
 *
 * @method \App\Model\Entity\RedesHasCliente[] paginate($object = null, array $settings = [])
 */
class RedesHasClientesController extends AppController
{

    /**
     * ------------------------------------------------------------
     * Métodos Comuns
     * ------------------------------------------------------------
     */


    /**
     * BeforeRender callback
     *
     * @param Event $event
     *
     * @return \Cake\Http\Response|void
     */
    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);

        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        }
    }

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

        // Permitir aos usuários se registrarem e efetuar login e logout.
        $this->Auth->allow(['getAllClientesFromRede']);
    }

    /**
     * Métodos de controller
     *
     */

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Redes', 'Clientes']
        ];
        $redesHasClientes = $this->paginate($this->RedesHasClientes);

        $this->set(compact('redesHasClientes'));
        $this->set('_serialize', ['redesHasClientes']);
    }

    /**
     * View method
     *
     * @param string|null $id Redes Has Cliente id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $redesHasCliente = $this->RedesHasClientes->get($id, [
            'contain' => ['Redes', 'Clientes']
        ]);

        $this->set('redesHasCliente', $redesHasCliente);
        $this->set('_serialize', ['redesHasCliente']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $redesHasCliente = $this->RedesHasClientes->newEntity();
        if ($this->request->is('post')) {
            $redesHasCliente = $this->RedesHasClientes->patchEntity($redesHasCliente, $this->request->getData());
            if ($this->RedesHasClientes->save($redesHasCliente)) {
                $this->Flash->success(__('The redes has cliente has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The redes has cliente could not be saved. Please, try again.'));
        }
        $redes = $this->RedesHasClientes->Redes->find('list', ['limit' => 200]);
        $clientes = $this->RedesHasClientes->Clientes->find('list', ['limit' => 200]);
        $this->set(compact('redesHasCliente', 'redes', 'clientes'));
        $this->set('_serialize', ['redesHasCliente']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Redes Has Cliente id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $redesHasCliente = $this->RedesHasClientes->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $redesHasCliente = $this->RedesHasClientes->patchEntity($redesHasCliente, $this->request->getData());
            if ($this->RedesHasClientes->save($redesHasCliente)) {
                $this->Flash->success(__('The redes has cliente has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The redes has cliente could not be saved. Please, try again.'));
        }
        $redes = $this->RedesHasClientes->Redes->find('list', ['limit' => 200]);
        $clientes = $this->RedesHasClientes->Clientes->find('list', ['limit' => 200]);
        $this->set(compact('redesHasCliente', 'redes', 'clientes'));
        $this->set('_serialize', ['redesHasCliente']);
    }

    /**
     * Remove uma unidade da Rede
     *
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete()
    {

        /**
         * Basicamente, os registros aos quais pertencem esta unidade serão apagados.
         * Entretanto, há o problema das redes e pontuações.
         * As gotas adquiridas pelos clientes, assim como suas pontuações gastas,
         * devem ser transmitidas para outra unidade, esta sendo a matriz.
         * Quais serão os passos a serem executados:
         * 1 - É matriz?
         * 1.1 - Possui unidades ainda cadastradas?
         * 1.1.1 - Sim -> Não deixa remover. Outra unidade deve ser definida como a 'matriz'.
         * 1.1.2 - Não
         * 1.1.2.1 - Possui outras unidades?
         * 1.1.2.1.1 - Sim -> Com certeza haverá uma unidade cadastrada como matriz.
         * Atualiza todos os registros com o id da rede.
         * 1.1.2.1.2 - Não -> Apaga tudo.
         */

        $query = $this->request->query;

        try {
            $this->request->allowMethod(['post', 'delete']);

            $redes_has_clientes_id = $query['redes_has_clientes_id'];

            $rede_has_cliente = $this->RedesHasClientes->getRedesHasClientesById($redes_has_clientes_id);

            // se rede é matriz
            if ($rede_has_cliente->cliente->matriz) {
                /**
                 * Verifica se tem outras unidades cadastradas. não deve apagar
                 * uma matriz existindo uma filial, os dados devem ser 'migrados'
                 */

                $redes_has_clientes = $this->RedesHasClientes->getRedesHasClientesByRedesId($rede_has_cliente->redes_id);

                // Verificar se é maior que 1, pois se tiver 2 ou mais, significa que tem uma filial além da matriz.
                if (sizeof($redes_has_clientes->toArray()) > 1) {

                    $this->Flash->error(Configure::read('messageDeleteMainCompanyDeny'));

                    return $this->redirect($query['return_url']);

                } else {

                    /**
                     * Executa a limpa de dados de uma matriz,
                     * remove todos os dados.
                     */

                    // pontuações do cliente

                    $redes_has_clientes_ids = [];
                    $redes_has_clientes_ids[] = $rede_has_cliente->id;

                    $clientes_ids = [];
                    $clientes_ids[] = $rede_has_cliente->clientes_id;

                    $this->PontuacoesPendentes
                        ->deleteAllPontuacoesPendentesByClienteIds($clientes_ids);

                    $this->Pontuacoes->deleteAllPontuacoesByClientesIds($clientes_ids);

                    $this->PontuacoesComprovantes->deleteAllPontuacoesComprovantesByClientesIds($clientes_ids);


                    // gotas

                    $this->Gotas->deleteAllGotasByClientesIds($clientes_ids);

                    $this->Cupons->deleteAllCuponsByClientesIds($clientes_ids);

                    // brindes

                    $this->UsuariosHasBrindes->deleteAllUsuariosHasBrindesByClientesIds($clientes_ids);

                    $this->ClientesHasBrindesEstoque->deleteAllClientesHasBrindesEstoqueByClientesIds($clientes_ids);

                    $this->ClientesHasBrindesHabilitadosPreco->deleteAllClientesHasBrindesHabilitadosPrecoByClientesIds($clientes_ids);

                    $this->ClientesHasBrindesHabilitados->deleteAllClientesHasBrindesHabilitadosByClientesIds($clientes_ids);

                    $this->Brindes->deleteAllBrindesByClientesIds($clientes_ids);

                    // apagar os usuários que são da rede (Administradores da Rede até funcionários)

                    $where_conditions = [];

                    $where_conditions[] = ['tipo_perfil >= ' => Configure::read('profileTypes')['AdminNetworkProfileType']];
                    $where_conditions[] = ['tipo_perfil <= ' => Configure::read('profileTypes')['WorkerProfileType']];

                    $this->Usuarios->deleteAllUsuariosByClienteIds(
                        $clientes_ids,
                        $where_conditions
                    );

                    // Não apaga os usuários que estão vinculados, mas remove o vínculo

                    $this->ClientesHasUsuarios->deleteAllClientesHasUsuariosByClientesIds($clientes_ids);

                    // Remove a unidade de rede
                    $this->RedesHasClientes->deleteRedesHasClientesByClientesIds($clientes_ids);

                    $this->Clientes->deleteClientesByIds($clientes_ids);

                }
            } else {
                /**
                 * Migra os dados de uma unidade filial para matriz
                 * (alguns dados são apagados)
                 */

                // pega a matriz, os dados serão migrados para ela.
                $matriz = $this->RedesHasClientes->findMatrizOfRedesByRedesId($rede_has_cliente->redes_id);

                // pontuações do cliente

                $this->PontuacoesPendentes
                    ->setPontuacoesPendentesToMainCliente($rede_has_cliente->clientes_id, $matriz->clientes_id);

                $this->Pontuacoes->setPontuacoesToMainCliente($rede_has_cliente->clientes_id, $matriz->clientes_id);

                $this->PontuacoesComprovantes->setPontuacoesComprovantesToMainCliente($rede_has_cliente->clientes_id, $matriz->clientes_id);

                // gotas

                // no caso de gotas, elas devem ser transferidas para a matriz, mas serem desativadas
                $this->Gotas->setGotasToMainCliente($rede_has_cliente->clientes_id, $matriz->clientes_id);

                $this->Cupons->setCuponsToMainCliente($rede_has_cliente->clientes_id, $matriz->clientes_id);

                // brindes

                $this->ClientesHasBrindesHabilitadosPreco->setClientesHasBrindesHabilitadosPrecoToMainCliente($rede_has_cliente->clientes_id, $matriz->clientes_id);

                $this->ClientesHasBrindesHabilitados->setClientesHasBrindesHabilitadosToMainCliente($rede_has_cliente->clientes_id, $matriz->clientes_id);

                $this->Brindes->setBrindesToMainCliente($rede_has_cliente->clientes_id, $matriz->clientes_id);

                /**
                 * Não se pode apagar os usuários que são da rede (Administradores
                 * da Rede até funcionários), pois pode ter brindes, cupons,
                 * pontuações vinculadas à eles. Então eles serão desativados,
                 * mas alocados na matriz
                 */

                $where_conditions = [];

                $where_conditions[] = ['tipo_perfil >= ' => Configure::read('profileTypes')['AdminNetworkProfileType']];
                $where_conditions[] = ['tipo_perfil <= ' => Configure::read('profileTypes')['WorkerProfileType']];

                $this->Usuarios->disableUsuariosOfCliente(
                    $rede_has_cliente->clientes_id,
                    $where_conditions
                );

                // Atualiza o vínculo dos usuários

                $this->ClientesHasUsuarios->setClientesHasUsuariosToMainCliente($rede_has_cliente->clientes_id, $matriz->clientes_id);

                // Remove a unidade de rede

                $clientes_ids = [];
                $clientes_ids[] = $rede_has_cliente->clientes_id;

                $this->RedesHasClientes->deleteRedesHasClientesByClientesIds($clientes_ids);
                $this->Clientes->deleteClientesByIds($clientes_ids);
            }

            $this->Flash->success(Configure::read("messageDeleteSuccess"));

            return $this->redirect($query['return_url']);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao realizar remoção de unidade de uma rede: {0} em: {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);

            return $this->redirect($query['return_url']);
        }

    }

    /**
     * ------------------------------------------------------------
     * Relatórios - Administrativo RTI
     * ------------------------------------------------------------
     */

    /**
     * Relatóriod e Unidades de cada Rede
     *
     * @return \Cake\Http\Response|void
     */
    public function relatorioUnidadesRedes()
    {
        $redesList = $this->Redes->getRedesList();

        $whereConditions = array();

        if (sizeof($whereConditions) > 0) {
            $redes = $redes->where($whereConditions);
        }

        if ($this->request->is(['post'])) {
            $data = $this->request->getData();

            if (strlen($data['redes_id']) > 0) {

                $whereConditions[] = ['id' => $data['redes_id']];
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
                    $whereConditions[] = ['Clientes.audit_insert BETWEEN "' . $dataInicial . '" AND "' . $dataFinal . '"'];
                }

            } else if (strlen($data['auditInsertInicio']) > 0) {

                if ($dataInicial > $dataHoje) {
                    $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Início'));
                } else {
                    $whereConditions[] = ['Clientes.audit_insert >= ' => $dataInicial];
                }

            } else if (strlen($data['auditInsertFim']) > 0) {

                if ($dataFinal > $dataHoje) {
                    $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Fim'));
                } else {
                    $whereConditions[] = ['Clientes.audit_insert <= ' => $dataFinal];
                }

            }

        }

        // Monta o Array para apresentar em tela
        $redes = array();

        $arrayWhereConditions = $whereConditions;

        foreach ($redesList as $key => $value) {
            $clientesIds = array();

            $rede['id'] = $key;
            $rede['nome_rede'] = $value;
            $rede['clientes'] = array();

            $clientesArray = $this->RedesHasClientes->find('all')
                ->where(['redes_id' => $key])->select('clientes_id');

            foreach ($clientesArray as $key => $cliente) {
                $clientesIds[] = $cliente->clientes_id;
            }

            $arrayWhereConditions[] = ['Clientes.id in ' => $clientesIds];

            $clientes = $this->Clientes->getAllClientes($arrayWhereConditions)->toArray();

            $rede['clientes'] = $clientes;


            unset($arrayWhereConditions[sizeof($arrayWhereConditions) - 1]);

            array_push($redes, $rede);
        }

        $arraySet = [
            'redesList',
            'redes'
        ];

        $this->set(compact($arraySet));
    }

    /**
     * -------------------------------------------------------------------------
     * AJAX Methods
     * -------------------------------------------------------------------------
     */

    /**
     * Obtem todos os clientes da rede
     *
     * @return void
     */
    public function getAllClientesFromRede()
    {
        try {
            $redes = null;

            if ($this->request->is('post')) {
                $data = $this->request->getData();

                if ($data['redes_id'] != "") {
                    $redes_list = $this->RedesHasClientes->getRedesHasClientesByRedesId((int)$data['redes_id']);

                    $redes = $redes_list->toArray();
                }
            }

            $arraySet = ['redes'];

            $this->set(compact($arraySet));
            $this->set("_serialize", $arraySet);

        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar unidades de uma rede: {0} em: {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }


    /**
     * -------------------------------------------------------------------------
     * Serviços REST
     * -------------------------------------------------------------------------
     */

    /**
     * Obtem todos os clientes da rede
     *
     * @return void
     */
    public function getUnidadesRedesAPI()
    {
        $mensagem = array();

        $status = true;
        $message = null;
        $errors = array();

        try {
            $redes = null;

            if ($this->request->is('post')) {
                $usuario = $this->Auth->user();

                $data = $this->request->getData();

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

                if ($data['redes_id'] != "") {
                    $redesHasClientesQuery = $this->RedesHasClientes->getRedesHasClientesByRedesId((int)$data['redes_id']);

                    $clientesIds = array();

                    foreach ($redesHasClientesQuery as $key => $redeHasCliente) {
                        $clientesIds[] = $redeHasCliente->clientes_id;
                    }

                    if (sizeof($clientesIds) == 0) {
                        $message = __("Não há clientes cadastrados para a unidade selecionada!");
                        $status = false;
                        $mensagem = array('status' => $status, "message" => $message);


                    } else {

                        $whereConditions = array();
                        $whereConditions[] = array("id in " => $clientesIds);

                        if (isset($data["nome_fantasia"])) {
                            $whereConditions[] = array("nome_fantasia like '%{$data["nome_fantasia"]}%'");
                        }

                        if (isset($data["razao_social"])) {
                            $whereConditions[] = array("razao_social like '%{$data["razao_social"]}%'");
                        }

                        if (isset($data["cnpj"])) {
                            $whereConditions[] = array("cnpj like '%{$data["cnpj"]}%'");
                        }

                        $resultado = $this->Clientes->getClientes($whereConditions, $orderConditions, $paginationConditions);

                        $clientes = $resultado["clientes"];

                        // Se chegou até aqui, ocorreu tudo bem

                        $mensagem = $resultado["mensagem"];
                    }
                }
            }
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $messageString = __("Não foi possível obter dados de unidades de uma rede!");

            $mensagem = ['status' => false, 'message' => $messageString, 'errors' => $trace];

            $messageStringDebug =
                $stringError = __("{0} - {1} em: {2}. [Função: {3} / Arquivo: {4} / Linha: {5}]  ", $messageString, $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write("error", $messageStringDebug);
        }

        $arraySet = ['clientes', "mensagem"];

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);

    }
}
