<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\Collection\Collection;
use Cake\Event\Event;
use Cake\Routing\Router;
use Cake\Mailer\Email;
use \DateTime;
use App\Custom\RTI\DateTimeUtil;
use App\Custom\RTI\DebugUtil;

/**
 * UsuariosHasBrindes Controller
 *
 * @property \App\Model\Table\UsuariosHasBrindesTable $UsuariosHasBrindes
 *
 * @method \App\Model\Entity\UsuariosHasBrinde[] paginate($object = null, array $settings = [])
 */
class UsuariosHasBrindesController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Usuarios', 'ClientesHasBrindesHabilitados']
        ];
        $usuariosHasBrindes = $this->paginate($this->UsuariosHasBrindes);

        $this->set(compact('usuariosHasBrindes'));
        $this->set('_serialize', ['usuariosHasBrindes']);
    }

    /**
     * View method
     *
     * @param string|null $id Usuarios Has Brinde id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $usuariosHasBrinde = $this->UsuariosHasBrindes->get($id, [
            'contain' => ['Usuarios', 'ClientesHasBrindesHabilitados']
        ]);

        $this->set('usuariosHasBrinde', $usuariosHasBrinde);
        $this->set('_serialize', ['usuariosHasBrinde']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $usuariosHasBrinde = $this->UsuariosHasBrindes->newEntity();
        if ($this->request->is('post')) {
            $usuariosHasBrinde = $this->UsuariosHasBrindes->patchEntity($usuariosHasBrinde, $this->request->getData());
            if ($this->UsuariosHasBrindes->save($usuariosHasBrinde)) {
                $this->Flash->success(__('The usuarios has brinde has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The usuarios has brinde could not be saved. Please, try again.'));
        }
        $usuarios = $this->UsuariosHasBrindes->Usuarios->find('list', ['limit' => 200]);
        $clientesHasBrindesHabilitados = $this->UsuariosHasBrindes->ClientesHasBrindesHabilitados->find('list', ['limit' => 200]);
        $this->set(compact('usuariosHasBrinde', 'usuarios', 'clientesHasBrindesHabilitados'));
        $this->set('_serialize', ['usuariosHasBrinde']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Usuarios Has Brinde id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $usuariosHasBrinde = $this->UsuariosHasBrindes->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $usuariosHasBrinde = $this->UsuariosHasBrindes->patchEntity($usuariosHasBrinde, $this->request->getData());
            if ($this->UsuariosHasBrindes->save($usuariosHasBrinde)) {
                $this->Flash->success(__('The usuarios has brinde has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The usuarios has brinde could not be saved. Please, try again.'));
        }
        $usuarios = $this->UsuariosHasBrindes->Usuarios->find('list', ['limit' => 200]);
        $clientesHasBrindesHabilitados = $this->UsuariosHasBrindes->ClientesHasBrindesHabilitados->find('list', ['limit' => 200]);
        $this->set(compact('usuariosHasBrinde', 'usuarios', 'clientesHasBrindesHabilitados'));
        $this->set('_serialize', ['usuariosHasBrinde']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Usuarios Has Brinde id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $usuariosHasBrinde = $this->UsuariosHasBrindes->get($id);
        if ($this->UsuariosHasBrindes->delete($usuariosHasBrinde)) {
            $this->Flash->success(__('The usuarios has brinde has been deleted.'));
        } else {
            $this->Flash->error(__('The usuarios has brinde could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Action de exibir histórico de brindes de usuário final
     *
     * @param integer $usuarios_id Id de Usuários
     * @return void
     */
    public function historicoBrindes(int $usuarios_id = null)
    {
        $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
        $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

        if ($usuarioAdministrador) {
            $this->usuarioLogado = $usuarioAdministrar;
        }

        $usuarioLogado = $this->usuarioLogado;

        $usuario = $this->Usuarios->getUsuarioById($this->usuarioLogado['id']);

        $usuario_id = is_null($usuarios_id) ? $usuario->id : $usuarios_id;

        $usuariosHasBrindes = $this->UsuariosHasBrindes->getAllUsuariosHasBrindes(['UsuariosHasBrindes.usuarios_id' => $usuario_id], ['UsuariosHasBrindes.id' => 'DESC']);

        // DebugUtil::printArray($usuariosHasBrindes->toArray());
        $this->paginate($usuariosHasBrindes, ['limit' => 10]);

        $this->set(compact('usuariosHasBrindes', 'usuarioLogado'));
        $this->set('_serialize', ['brindes', 'usuarioLogado']);
    }

    /**
     * ------------------------------------------------------------
     * Métodos para Funcionários (Dashboard de Funcionário)
     * ------------------------------------------------------------
     */

    /**
     * Exibe a Action de Pesquisar Pontuações de Cliente Final
     *
     * @return void
     */
    public function pesquisarClienteFinalBrindes()
    {

    }

    /**
     * Exibe a Action que mostra todos os brindes de um usuário informado
     *
     * @param integer $usuarios_id Id de Usuário
     *
     * @return \Cake\Http\Response
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function exibirClienteFinalBrindes(int $usuarios_id)
    {
        try {
            // se o usuário que estiver cadastrando for um cliente final
            // o id será nulo, senão será o funcionário

            $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
            $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

            if ($usuarioAdministrador) {
                $this->usuarioLogado = $usuarioAdministrar;
            }

            $usuario = $this->Usuarios->getUsuarioById($usuarios_id);

            // pegar rede que usuário está logado e suas unidades

            $rede = $this->request->session()->read('Rede.Grupo');

            $clientes_ids = [];

            // pega id de todos os clientes que estão ligados à uma rede

            $redes_has_clientes_query = $this->RedesHasClientes->getRedesHasClientesByRedesId($rede->id);

            $clientes_ids = [];

            foreach ($redes_has_clientes_query as $key => $value) {
                $clientes_ids[] = $value['clientes_id'];
            }

            // pega os brindes habilitados (que foram habilitados ou não para uma unidade de rede) para uma rede (apenas os ids)
            // motivo: A unidade de rede pode ter habilitado o brinde em um determinado momento,
            // mas depois, por algum motivo, não tem mais habilitado (parou de fornecer, por ex.);

            $brindes_habilitados_ids = [];

            // TODO: ARRUMAR PARA FUNCIONARIO!
            $brindesHabilitadosClientes = $this->ClientesHasBrindesHabilitados->getTodosBrindesByClienteId($clientes_ids);

            if (!$brindesHabilitadosClientes["mensagem"]["status"]) {
                $this->Flash->error($brindesHabilitadosClientes["mensagem"]["message"]);
                $brindesHabilitadosClientes = array();
            } else {
                $brindesConfigurarArrayRetorno = array();
                $brindesHabilitadosClientes = $brindesHabilitadosClientes["data"];

                foreach ($brindesHabilitadosClientes as $brinde) {
                    $brinde["pendente_configuracao"] = empty($brinde["brinde_vinculado"]["tipo_codigo_barras"]);
                    $brindesConfigurarArrayRetorno[] = $brinde;
                }

                $brindesHabilitadosClientes = $brindesConfigurarArrayRetorno;

                $usuarios_has_brindes = $this->UsuariosHasBrindes->getAllUsuariosHasBrindes(
                    [
                        'usuarios_id' => $usuario->id,
                        'clientes_has_brindes_habilitados_id in ' => $brindes_habilitados_ids
                    ]
                );
            }

            // foreach ($brindesHabilitadosClientes as $brinde_habilitado_cliente) {
            //     $brindes_habilitados_ids[] = $brinde_habilitado_cliente['id'];
            // }



            $this->paginate($usuarios_has_brindes, ['limit' => 10, 'order' => ['data' => 'desc']]);

            $array_set = [
                'usuarios_has_brindes',
                'usuario',
                'usuarioLogado',
                'usuarios_id'
            ];

            $this->set(compact($array_set));
            $this->set('_serialize', [$array_set]);
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao exibir brindes para usuário: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * ------------------------------------------------------------
     * Relatórios (Dashboard de Admin RTI)
     * ------------------------------------------------------------
     */

    /**
     * Action que exibe o Relatório de Brindes Adquiridos por Usuários nas Redes
     *
     * @return void
     */
    public function relatorioBrindesUsuariosRedes()
    {
        try {
            $redesList = $this->Redes->getRedesList();

            $whereConditions = array();

            $redesArrayIds = array();
            $pontuacoesComprovantes = array();
            $clientesList = null;
            $usuariosList = null;
            $qteRegistros = 10;
            $dataInicial = date('d/m/Y', strtotime('-30 days'));
            $dataFinal = date('d/m/Y');

            $usuarios = array();

            if ($this->request->is(['post'])) {

                $data = $this->request->getData();

                if (strlen($data['redes_id']) == 0) {
                    $this->Flash->error('É necessário selecionar uma rede para filtrar!');

                } else {

                    // Obtem os parâmetros

                    // Filtro de nome

                    $nomeUsuario = $data['nome'];

                    $qteRegistros = (int)$data['qte_registros'];

                    // Data de Criação Início e Fim

                    $dataHoje = DateTimeUtil::convertDateToUTC((new DateTime('now'))->format('Y-m-d H:i:s'));
                    $dataInicial = strlen($data['auditInsertInicio']) > 0 ? DateTimeUtil::convertDateToUTC($data['auditInsertInicio'], 'd/m/Y') : null;
                    $dataFinal = strlen($data['auditInsertFim']) > 0 ? DateTimeUtil::convertDateToUTC($data['auditInsertFim'], 'd/m/Y') : null;

                    if (strlen($data['auditInsertInicio']) > 0 && strlen($data['auditInsertFim']) > 0) {

                        if ($dataInicial > $dataFinal) {
                            $this->Flash->error(__(Configure::read('messageDateRangeInvalid')));
                        } else if ($dataInicial > $dataHoje) {
                            $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid', 'Data de Início')));
                        } else {
                            $whereConditions[] = ['UsuariosHasBrindes.audit_insert BETWEEN "' . $dataInicial . '" and "' . $dataFinal . '"'];
                        }

                    } else if (strlen($data['auditInsertInicio']) > 0) {

                        if ($dataInicial > $dataHoje) {
                            $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Início'));
                        } else {
                            $whereConditions[] = ['UsuariosHasBrindes.audit_insert >= ' => $dataInicial];
                        }

                    } else if (strlen($data['auditInsertFim']) > 0) {

                        if ($dataFinal > $dataHoje) {
                            $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Fim'));
                        } else {
                            $whereConditions[] = ['UsuariosHasBrindes.audit_insert <= ' => $dataFinal];
                        }
                    }

                    $dataInicial = DateTimeUtil::convertDateToPTBRFormat($dataInicial);
                    $dataFinal = DateTimeUtil::convertDateToPTBRFormat($dataFinal);

                    /**
                     * Faz a pesquisa de comprovantes pela id das unidades da rede
                     */

                    $rede = $this->Redes->getRedeById($data['redes_id']);

                    $clientesIdsList = [];

                    // obtem os ids das unidades para saber quais brindes estão disponíveis
                    foreach ($rede->redes_has_clientes as $key => $value) {
                        $clientesIdsList[] = $value->clientes_id;
                    }

                    $clientesList = $this->Clientes->find('list')
                        ->where(['id in ' => $clientesIdsList])
                        ->order(['nome_fantasia' => 'asc']);

                    /**
                     * Pega todos os funcionários da rede através da lista
                     * de clientesIdsList
                     */

                    $whereConditionsClientesHasUsuarios = array();
                    $whereConditionsClientesHasUsuarios[] = [
                        'ClientesHasUsuarios.clientes_id in ' => $clientesIdsList
                    ];

                    if (strlen($nomeUsuario) > 0) {
                        $whereConditionsClientesHasUsuarios[] = [

                            "Usuario.nome like '%" . $nomeUsuario . "%'"
                        ];
                    }

                    $clientesHasUsuariosIdsArrayList = $this->ClientesHasUsuarios->findClienteHasUsuario(
                        $whereConditionsClientesHasUsuarios
                    );

                    $usuariosIds = [];

                    foreach ($clientesHasUsuariosIdsArrayList as $key => $clienteHasUsuario) {

                        if (!in_array($clienteHasUsuario->usuarios_id, $usuariosIds)) {
                            $usuariosIds[] = $clienteHasUsuario->usuarios_id;
                        }
                    }

                    if (sizeof($usuariosIds) > 0) {
                        /**
                         * Se a empresa não permite que os funcionários consumam gotas,
                         * pegue somente os usuários
                         */

                        $usuariosWhereConditions = array();
                        $usuariosWhereConditions[] = ['id in ' => $usuariosIds];
                        $usuariosWhereConditions[] = ['tipo_perfil' => Configure::read('profileTypes')['UserProfileType']];

                        $usuariosList = $this->Usuarios->find('all')->where(
                            $usuariosWhereConditions
                        )->order(['nome' => 'asc']);

                        /**
                         * Pega todos os Comprovantes com base na lista de
                         * clientesIds e funcionariosList
                         */

                        $orderConditions = ['data' => 'asc'];

                        foreach ($usuariosIds as $key => $usuariosId) {
                            $userWhereConditions = $whereConditions;

                            $userWhereConditions[] = [
                                'UsuariosHasBrindes.usuarios_id' => $usuariosId
                            ];

                            $usuario = $this->Usuarios->getUsuarioById($usuariosId);

                            $usuarioHasBrindes =
                                $this->UsuariosHasBrindes->getAllUsuariosHasBrindes($userWhereConditions, $orderConditions);

                            $usuario['usuarioHasBrindes'] =
                                $qteRegistros > 0 ?
                                $usuarioHasBrindes->limit($qteRegistros)->toArray() :
                                $usuarioHasBrindes->toArray();

                            if (sizeof($usuario['usuarioHasBrindes']) > 0) {
                                $usuarios[] = $usuario;
                            }
                        }
                    } else {
                        $this->Flash->error(Configure::read('messageQueryNoDataToReturn'));
                    }
                }
            }

            $arraySet = [
                'dataInicial',
                'dataFinal',
                'redesList',
                'usuarios',
                'clientesList',
                'usuariosList',
                'pontuacoesComprovantes'
            ];

            $this->set(compact($arraySet));

        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao exibir relatório de brindes adquiridos por usuários: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

}
