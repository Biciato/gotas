<?php
namespace App\Controller;

use \DateTime;
use App\Controller\AppController;
use App\Custom\RTI\DateTimeUtil;
use App\Custom\RTI\DebugUtil;
use App\Custom\RTI\SefazUtil;
use App\Custom\RTI\StringUtil;
use App\Custom\RTI\WebTools;
use App\Model\Entity\Cliente;
use App\Model\Entity\Usuario;
use App\Model\Entity\Gota;
use App\View\Helper\AddressHelper;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Collection\Collection;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Log\Log;
use Cake\Mailer\Email;
use Cake\Routing\Router;
use Cake\View\Helper\UrlHelper;
use App\Custom\RTI\NumberUtil;

/**
 * PontuacoesComprovantes Controller
 *
 *
 * @method \App\Model\Entity\PontuacoesComprovante[] paginate($object = null, array $settings = [])
 */
class PontuacoesComprovantesController extends AppController
{
    /**
     * ------------------------------------------------------------
     * Fields
     * ------------------------------------------------------------
     */
    protected $address_helper = null;

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
        $pontuacoesComprovantes = $this->paginate($this->PontuacoesComprovantes);

        $this->set(compact('pontuacoesComprovantes'));
        $this->set('_serialize', ['pontuacoesComprovantes']);
    }

    /**
     * View method
     *
     * @param string|null $id Pontuacoes Comprovante id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $pontuacoesComprovante = $this->PontuacoesComprovantes->get($id, [
            'contain' => []
        ]);

        $this->set('pontuacoesComprovante', $pontuacoesComprovante);
        $this->set('_serialize', ['pontuacoesComprovante']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $pontuacoesComprovante = $this->PontuacoesComprovantes->newEntity();
        if ($this->request->is('post')) {
            $pontuacoesComprovante = $this->PontuacoesComprovantes->patchEntity($pontuacoesComprovante, $this->request->getData());
            if ($this->PontuacoesComprovantes->save($pontuacoesComprovante)) {
                $this->Flash->success(__('The pontuacoes comprovante has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The pontuacoes comprovante could not be saved. Please, try again.'));
        }
        $this->set(compact('pontuacoesComprovante'));
        $this->set('_serialize', ['pontuacoesComprovante']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Pontuacoes Comprovante id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $pontuacoesComprovante = $this->PontuacoesComprovantes->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $pontuacoesComprovante = $this->PontuacoesComprovantes->patchEntity($pontuacoesComprovante, $this->request->getData());
            if ($this->PontuacoesComprovantes->save($pontuacoesComprovante)) {
                $this->Flash->success(__('The pontuacoes comprovante has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The pontuacoes comprovante could not be saved. Please, try again.'));
        }
        $this->set(compact('pontuacoesComprovante'));
        $this->set('_serialize', ['pontuacoesComprovante']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Pontuacoes Comprovante id.
     *
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $pontuacoesComprovante = $this->PontuacoesComprovantes->get($id);
        if ($this->PontuacoesComprovantes->delete($pontuacoesComprovante)) {
            $this->Flash->success(__('The pontuacoes comprovante has been deleted.'));
        } else {
            $this->Flash->error(__('The pontuacoes comprovante could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * ------------------------------------------------------------
     * Métodos customizados
     * ------------------------------------------------------------
     */

    /**
     * Realiza aprovação da Pontuação
     *
     * @param int $id Id da pontuação
     *
     * @return void
     */
    public function aprovarPontuacaoComprovante(int $id)
    {
        try {
            if ($this->request->is(['post', 'put'])) {
                $pontuacao_comprovante = $this->PontuacoesComprovantes->setPontuacaoComprovanteApprovedById($id);

                if ($pontuacao_comprovante) {
                    $this->Flash->success(Configure::read('messageApprovedSuccess'));
                } else {
                    $this->Flash->error(Configure::read('messageApprovedFailure'));
                }

                return $this->redirect(
                    [
                        'controller' => 'pontuacoes',
                        'action' => 'detalhesCupom', (int)$id
                    ]
                );
            }
        } catch (\Exception $e) {
            $stringError = __("Erro ao realizar aprovação de pontuação: {0} em: {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Valida pontuação informada
     *
     * @param int $id Id da pontuação
     *
     * @return void
     */
    public function validarPontuacaoComprovante(int $id)
    {
        $this->alterarValidacaoPontuacaoComprovante($id, false);
    }

    /**
     * Invalida pontuação informada
     *
     * @param int $id Id da pontuação
     *
     * @return void
     */
    public function invalidarPontuacaoComprovante(int $id)
    {
        $this->alterarValidacaoPontuacaoComprovante($id, true);
    }

    /**
     * Troca status de validade da pontuação registrada
     *
     * @param int  $id     Id da pontuação
     * @param bool $status Estado
     *
     * @return void
     */
    public function alterarValidacaoPontuacaoComprovante(int $id, bool $status)
    {
        try {
            if ($this->request->is(['post', 'put'])) {
                $pontuacao_comprovante = $this->PontuacoesComprovantes->setPontuacaoComprovanteValidStatusById($id, $status);

                $message_success = $status ? Configure::read('messageInvalidateSuccess') : Configure::read('messageValidateSuccess');
                $message_error = $status ? Configure::read('messageInvalidateError') : Configure::read('messageValidateError');
                if ($pontuacao_comprovante) {
                    $this->Flash->success($message_success);
                } else {
                    $this->Flash->error($message_error);
                }

                return $this->redirect(
                    [
                        'controller' => 'pontuacoes',
                        'action' => 'detalhesCupom', (int)$id
                    ]
                );
            }
        } catch (\Exception $e) {
            $message_error = $status ?
                __("Erro ao realizar tratamento de invalidar pontuação: {0} em: {1} ", $e->getMessage(), $trace[1]) :
                __("Erro ao realizar tratamento de validar pontuação: {0} em: {1} ", $e->getMessage(), $trace[1]);

            $stringError = $message_error;

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Exibe a action de Histórico de Pontuações de um cliente
     *
     * @param int $id Id do usuário
     *
     * @return void
     */
    public function historicoPontuacoes(int $id = null)
    {
        $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
        $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

        if ($usuarioAdministrador) {
            $this->usuarioLogado = $usuarioAdministrar;
        }

        $usuarioLogado = $this->usuarioLogado;

        $usuario = $this->Usuarios->getUsuarioById($this->usuarioLogado['id']);

        $usuario_id = is_null($id) ? $usuario->id : $id;

        if ($this->request->is(['post', 'put'])) {
            $param = $this->request->getData();
        }

        $pontuacoes_comprovantes = $this->PontuacoesComprovantes->getPontuacoesComprovantes(['usuarios_id' => $usuario_id], ['PontuacoesComprovantes.id' => 'DESC']);

        $this->paginate($pontuacoes_comprovantes, ['limit' => 10]);

        $this->set(compact('pontuacoes_comprovantes', 'usuarioLogado'));
        $this->set('_serialize', ['pontuacoes_comprovantes', 'usuarioLogado']);
    }

    /**
     * Exibe a action de remover pontuações
     *
     * @return void
     */
    public function removerPontuacoes()
    {
        if (!$this->securityUtil->checkUserIsAuthorized($this->getUserLogged(), 'AdminDeveloperProfileType')) {
            $this->redirectUserNotAuthorized($this);
        }
    }

    /**
     * Executa remoção de pontuações
     *
     * @return void
     */
    public function executarRemoverPontuacoes()
    {
        if ($this->request->is(['post', 'put'])) {

            $this->Pontuacoes->deleteAll([], []);
            $this->PontuacoesPendentes->deleteAll([], []);
            $this->PontuacoesComprovantes->deleteAll([], []);

            $this->Flash->success('Registros removidos com sucesso.');

            return $this->redirect(
                [
                    'controller' => 'pages',
                    'action' => 'display'
                ]
            );
        }
    }

    /**
     * Exibe a action de Detalhes do Histórico de Pontuações de um cliente
     *
     * @param integer $pontuacao_comprovante_id Id de Pontuação Comprovante
     *
     * @return void
     */
    public function verDetalhes(int $pontuacao_comprovante_id)
    {
        $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
        $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

        if ($usuarioAdministrador) {
            $this->usuarioLogado = $usuarioAdministrar;
        }

        $usuarioLogado = $this->usuarioLogado;

        $pontuacao_comprovante = $this->PontuacoesComprovantes->getPontuacoesComprovantes(['PontuacoesComprovantes.id' => $pontuacao_comprovante_id])->first();

        $this->set(compact('pontuacao_comprovante', 'usuarioLogado'));
        $this->set('_serialize', ['pontuacao_comprovante', 'usuarioLogado']);
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
    public function pesquisarClienteFinalPontuacoes()
    {


    }

    /**
     * Exibe a Action que mostra todas as pontuações de um usuário informado
     *
     * @param integer $usuarios_id Id de Usuário
     *
     * @return \Cake\Http\Response
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function exibirClienteFinalPontuacoes(int $usuarios_id)
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

            $rede = $this->request->session()->read('Rede.Principal');

            $clientes_ids = [];

            // pega id de todos os clientes que estão ligados à uma rede

            $redes_has_clientes_query = $this->RedesHasClientes->getRedesHasClientesByRedesId($rede->id);

            $clientes_ids = [];

            foreach ($redes_has_clientes_query as $key => $value) {
                $clientes_ids[] = $value['clientes_id'];
            }

            $pontuacoes_comprovantes = $this->PontuacoesComprovantes->getAllCouponsForUsuarioInClientes(
                $usuario->id,
                $clientes_ids
            );

            $soma_pontuacao_acumulada = $this->Pontuacoes->getSumPontuacoesObtainedByUsuario($usuario->id, $clientes_ids);

            $soma_pontuacao_utilizada = $this->Pontuacoes->getSumPontuacoesUsedByUsuario($usuario->id, $clientes_ids);

            $soma_pontuacao_acumulada = is_null($soma_pontuacao_acumulada) ? 0 : $soma_pontuacao_acumulada;
            $soma_pontuacao_utilizada = is_null($soma_pontuacao_utilizada) ? 0 : $soma_pontuacao_utilizada;

            $soma_pontuacao_final = $soma_pontuacao_acumulada - $soma_pontuacao_utilizada;

            $this->paginate($pontuacoes_comprovantes, ['limit' => 10, 'order' => ['data' => 'desc']]);

            // debug($pontuacoes_comprovantes->toArray());

            $arraySet = array(
                'pontuacoes_comprovantes',
                'soma_pontuacao_acumulada',
                'soma_pontuacao_utilizada',
                'soma_pontuacao_final',
                'usuario',
                'usuarioLogado',
                'usuarios_id'
            );

            $this->set(compact($arraySet));
            $this->set('_serialize', $arraySet);
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao exibir comprovantes de pontuações para usuário: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

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
     * Exibe a Action de Relatório Comprovantes de Pontuações por Redes
     *
     * @return void
     */
    public function relatorioPontuacoesComprovantesRedes()
    {
        try {

            $redesList = $this->Redes->getRedesList();

            $whereConditions = array();

            $redesArrayIds = array();

            $clientesList = null;
            $funcionariosList = null;

            $pontuacoesComprovantes = array();

            $dataInicial = date('d/m/Y', strtotime('-30 days'));
            $dataFinal = date('d/m/Y');

            foreach ($redesList as $key => $redeItem) {
                $redesArrayIds[] = $key;
            }

            if ($this->request->is(['post'])) {

                $data = $this->request->getData();

                if (strlen($data['redes_id']) == 0) {
                    $this->Flash->error('É necessário selecionar uma rede para filtrar!');

                } else {

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
                            $whereConditions[] = ['PontuacoesComprovantes.audit_insert BETWEEN "' . $dataInicial . '" and "' . $dataFinal . '"'];
                        }

                    } else if (strlen($data['auditInsertInicio']) > 0) {

                        if ($dataInicial > $dataHoje) {
                            $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Início'));
                        } else {
                            $whereConditions[] = ['PontuacoesComprovantes.audit_insert >= ' => $dataInicial];
                        }

                    } else if (strlen($data['auditInsertFim']) > 0) {

                        if ($dataFinal > $dataHoje) {
                            $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Fim'));
                        } else {
                            $whereConditions[] = ['PontuacoesComprovantes.audit_insert <= ' => $dataFinal];
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

                    // unidade selecionada
                    if (strlen($data['clientes_id']) > 0) {
                        $clientesIdsList = [];

                        $clientesIdsList[] = (int)$data['clientes_id'];
                    }

                    /**
                     * Pega todos os funcionários da rede através da lista
                     * de clientesIdsList
                     */

                    //  Ajustar
                    $funcionariosIdsQuery = $this->Usuarios->findFuncionariosRede($rede->id, $clientesIdsList)->toArray();

                    $funcionariosIds = [];

                    foreach ($funcionariosIdsQuery as $key => $funcionario) {
                        $funcionariosIds[] = $funcionario->id;
                    }

                    if (strlen($data['funcionarios_id']) > 0) {
                        $funcionariosIds = [];

                        $funcionariosIds[] = (int)$data['funcionarios_id'];
                    }

                    if (sizeof($funcionariosIds) > 0) {

                        $funcionariosList = $this->Usuarios->find('list')->where([
                            'id in ' => $funcionariosIds
                        ])->order(['nome' => 'asc']);

                        /**
                         * Pega todos os Comprovantes com base na lista de
                         * clientesIds e funcionariosList
                         */

                        $whereConditions[] = [
                            'PontuacoesComprovantes.clientes_id in ' => $clientesIdsList
                        ];
                        $whereConditions[] = [
                            'PontuacoesComprovantes.funcionarios_id in ' => $funcionariosIds
                        ];

                        $pontuacoesComprovantes = $this->PontuacoesComprovantes->getPontuacoesComprovantes($whereConditions);

                    // debug($pontuacoesComprovantes->toArray());
                    } else {
                        $this->Flash->error(Configure::read('messageQueryNoDataToReturn'));
                    }
                }

            }

            $arraySet = [
                'dataInicial',
                'dataFinal',
                'redesList',
                'redes',
                'clientesList',
                'funcionariosList',
                'pontuacoesComprovantes'
            ];

            $this->set(compact($arraySet));

        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao exibir comprovantes de pontuações para usuário: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write('error', $trace);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Exibe a Action de Relatório Comprovantes de Pontuações por Redes
     *
     * @return void
     */
    public function relatorioPontuacoesComprovantesUsuariosRedes()
    {
        try {

            $redesList = $this->Redes->getRedesList();

            $whereConditions = array();

            $redesArrayIds = array();
            $pontuacoesComprovantes = array();
            $clientesList = null;
            $usuariosList = null;
            $dataInicial = date('d/m/Y', strtotime('-30 days'));
            $dataFinal = date('d/m/Y');

            foreach ($redesList as $key => $redeItem) {
                $redesArrayIds[] = $key;
            }

            if ($this->request->is(['post'])) {

                $data = $this->request->getData();

                if (strlen($data['redes_id']) == 0) {
                    $this->Flash->error('É necessário selecionar uma rede para filtrar!');

                } else {

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
                            $whereConditions[] = ['PontuacoesComprovantes.audit_insert BETWEEN "' . $dataInicial . '" and "' . $dataFinal . '"'];
                        }

                    } else if (strlen($data['auditInsertInicio']) > 0) {

                        if ($dataInicial > $dataHoje) {
                            $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Início'));
                        } else {
                            $whereConditions[] = ['PontuacoesComprovantes.audit_insert >= ' => $dataInicial];
                        }

                    } else if (strlen($data['auditInsertFim']) > 0) {

                        if ($dataFinal > $dataHoje) {
                            $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Fim'));
                        } else {
                            $whereConditions[] = ['PontuacoesComprovantes.audit_insert <= ' => $dataFinal];
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

                    // unidade selecionada
                    if (strlen($data['clientes_id']) > 0) {
                        $clientesIdsList = [];

                        $clientesIdsList[] = (int)$data['clientes_id'];
                    }
                    /**
                     * Pega todos os funcionários da rede através da lista
                     * de clientesIdsList
                     */

                    $clientesHasUsuariosIdsArrayList = $this->ClientesHasUsuarios->findClienteHasUsuario(
                        [
                            'ClientesHasUsuarios.clientes_id in ' => $clientesIdsList
                        ]
                    )->toArray();

                    $usuariosIds = [];

                    foreach ($clientesHasUsuariosIdsArrayList as $key => $clienteHasUsuario) {
                        $usuariosIds[] = $clienteHasUsuario->usuarios_id;
                    }

                    if (strlen($data['usuarios_id']) > 0) {
                        $usuariosIds = [];

                        $usuariosIds[] = (int)$data['usuarios_id'];
                    }

                    if (sizeof($usuariosIds) > 0) {
                        /**
                         * Se a empresa não permite que os funcionários consumam gotas,
                         * pegue somente os usuários
                         */

                        $usuariosWhereConditions = [];

                        $usuariosWhereConditions[] = ['id in ' => $usuariosIds];

                        if (!$rede->permite_consumo_gotas_funcionarios) {
                            $usuariosWhereConditions[] = ['tipo_perfil' => Configure::read('profileTypes')['UserProfileType']];
                        }

                        $usuariosList = $this->Usuarios->find('list')->where(
                            $usuariosWhereConditions
                        )->order(['nome' => 'asc']);

                        /**
                         * Pega todos os Comprovantes com base na lista de
                         * clientesIds e funcionariosList
                         */

                        $whereConditions[] = [
                            'PontuacoesComprovantes.clientes_id in ' => $clientesIdsList
                        ];
                        $whereConditions[] = [
                            'PontuacoesComprovantes.usuarios_id in ' => $usuariosIds
                        ];

                        $orderConditions = ['data' => 'asc'];

                        $pontuacoesComprovantes = $this->PontuacoesComprovantes->getPontuacoesComprovantes($whereConditions, $orderConditions);

                        // debug($whereConditions);
                        // debug($pontuacoesComprovantes->toArray());
                    } else {
                        $this->Flash->error(Configure::read('messageQueryNoDataToReturn'));
                    }
                }

            }

            $arraySet = [
                'dataInicial',
                'dataFinal',
                'redesList',
                'redes',
                'clientesList',
                'usuariosList',
                'pontuacoesComprovantes'
            ];

            $this->set(compact($arraySet));

        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao exibir comprovantes de pontuações para usuário: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * ------------------------------------------------------------
     * Ajax Methods
     * ------------------------------------------------------------
     */

    /**
     * Retorna novo nome para comprovantes
     *
     * @return json object
     */
    public function getNewReceiptName()
    {
        try {
            $nome_img = null;

            if ($this->request->is(['post', 'put'])) {
                $nome_img = $this->PontuacoesComprovantes->generateNewImageCoupon();
            }
            $arraySet = ['nome_img'];

            $this->set(compact($arraySet));
            $this->set("_serialize", $arraySet);

        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao obter novo nome para comprovante: {0} em: {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Verifica se já existe cupom anterior processado
     *
     * @return void
     */
    public function findTaxCoupon()
    {
        try {
            $found = null;
            $message = null;

            if ($this->request->is('post')) {
                $data = $this->request->getData();

                $pontuacao_pendente = $this->PontuacoesPendentes->findPontuacaoPendenteAwaitingProcessing($data['chave_nfe'], $data['estado_nfe']);

                $chave_nfe = $data['chave_nfe'];
                $estado_nfe = $data['estado_nfe'];
                $clientes_id = isset($data['clientes_id']) ? $data['clientes_id'] : null;

                if (!$pontuacao_pendente) {

                    $pontuacao_comprovante
                        = $this->PontuacoesComprovantes->findCouponByKey(
                        $chave_nfe,
                        $estado_nfe,
                        $clientes_id
                    );
                }

                $message = "";
                $clear = true;

                if ($pontuacao_pendente) {
                    if ($pontuacao_pendente->registro_processado) {
                        $message = "Este registro já foi importado previamente!";
                    } else {
                        $message = "Este registro está aguardando processamento, não é necessário importar novamente!";
                    }
                } elseif ($pontuacao_comprovante) {
                    $message = "Este registro já foi importado previamente!";
                } else {
                    $clear = false;
                }

                $found = $clear;
            }

            $arraySet = [
                'found',
                'message'
            ];

            $this->set(compact($arraySet));
            $this->set("_serialize", $arraySet);

        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao obter cupom fiscal para consulta: {0} em: {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Obtêm dados de comprovante pelo sistema da SEFAZ
     *
     * @return json object
     *
     * ----------------------------------------------------------------------
     * Aviso!
     *
     * Este método e o método setComprovanteFiscalUsuarioAPI fazem a mesma
     * coisa de finalidade, mas suas execuções são diferentes!
     * Por isto, este método não se deve ser genérico!
     * Caso um dos dois tenha modificações, deve ser analisado o que será
     * impactado!
     * ----------------------------------------------------------------------
     */
    public function saveTaxCoupon()
    {
        try {
            $result = null;

            if ($this->request->is(['post', 'put'])) {
                $data = $this->request->getData();

                $url = $data['url'];

                $startSearch = "chNFe=";
                $startSearchIndex = strpos($url, $startSearch) + strlen($startSearch);
                $chave = substr($url, $startSearchIndex, 44);

                $cliente = $this->Clientes->getClienteByCNPJ($data["clientesCNPJ"]);
                $clientesIds = array();
                $clientesIds[] = $cliente["id"];

                $usuario = $this->Usuarios->getUsuarioById($data['usuarios_id']);
                $funcionario = $this->Auth->user();

                $usuarioAdministrar = $this->request->session()->read("Usuario.Administrar");

                if (!empty($usuarioAdministrar)) {
                    $funcionario = $usuarioAdministrar;
                }

                $dataProcessamento = date("Y-m-d H:i:s");

                $pontuacoesComprovante['clientes_id'] = $cliente->id;
                $pontuacoesComprovante['usuarios_id'] = $usuario["id"];
                $pontuacoesComprovante['funcionarios_id'] = $funcionario["id"];
                $pontuacoesComprovante['conteudo'] = $url;
                $pontuacoesComprovante['chave_nfe'] = $data['chave_nfe'];
                $pontuacoesComprovante['estado_nfe'] = $data['estado_nfe'];
                $pontuacoesComprovante['data'] = $dataProcessamento;
                $pontuacoesComprovante['requer_auditoria'] = false;
                $pontuacoesComprovante['auditado'] = false;

                $pontuacao['clientes_id'] = $cliente->id;
                $pontuacao['usuarios_id'] = $usuario->id;
                $pontuacao['funcionarios_id'] = $funcionario["id"];

                $pontuacao['data'] = $dataProcessamento;

                $gotas = $this->Gotas->findGotasByClientesId($clientesIds);
                $gotas = $gotas->toArray();

                if (sizeof($gotas) == 0) {
                    $success = false;
                    $message = Configure::read("messageGotasPointOfServiceNotConfigured");

                    $pontuacao_pendente = $this->PontuacoesPendentes->createPontuacaoPendenteAwaitingProcessing($cliente["id"], $usuario["id"], $funcionario["id"], $url, $chave, $cliente["estado"]);
                    $arraySet = array(
                        'success',
                        'message',
                        'pontuacao_pendente',
                        'data'
                    );

                    $this->set(compact($arraySet));
                    $this->set("_serialize", $arraySet);
                    return;
                }

                $isEstadoGoias = false;

                if (strpos($url, "nfe.sefaz.go.gov.br") != 0) {
                    $isEstadoGoias = true;
                    $startSearch = "chNFe=";
                    $startSearchIndex = strpos($url, $startSearch) + strlen($startSearch);
                    $chave = substr($url, $startSearchIndex, 44);
                    $url = "http://nfe.sefaz.go.gov.br/nfeweb/jsp/CConsultaCompletaNFEJSF.jsf?parametroChaveAcesso=" . $chave;
                }

                $webContent = WebTools::getPageContent($url);

                // DebugUtil::print($webContent);
                if ($webContent['statusCode'] == 200) {
                    // Status está ok, pode prosseguir com procedimento
                    $conteudo = $webContent["response"];

                    $retorno = $this->_processaConteudoSefaz($cliente, $funcionario, $usuario, $gotas, $url, $chave, $estado, $webContent["response"]);

                    // DebugUtil::print($retorno);
                    // TODO: conferir
                    $mensagem = $retorno["mensagem"];
                    $data = $retorno["data"];

                    $arraySet = array("mensagem", "data");
                    $this->set(compact($arraySet));
                    $this->set("_serialize", $arraySet);

                    return;

                } else {
                    // Status está anormal, grava para posterior processamento

                    $pontuacao_pendente = $this
                        ->PontuacoesPendentes
                        ->createPontuacaoPendenteAwaitingProcessing(
                            $cliente["id"],
                            $usuario["id"],
                            $funcionario["id"],
                            $url,
                            $data['chave_nfe'],
                            $data['estado_nfe']
                        );

                    if ($pontuacao_pendente) {
                        $success = false;
                        $message = Configure::read("messageNotPossibleToImportCoupon");
                        $errors = array(
                            Configure::read("messageNotPossibleToImportCouponAwaitingProcessing")
                        );
                        $data = $pontuacoesComprovante;

                        $arraySet = [
                            'success',
                            'message',
                            'pontuacao_pendente',
                            'data',
                            "errors"
                        ];

                        $this->set(compact($arraySet));
                        $this->set("_serialize", $arraySet);

                        return;
                    }
                }

            }
        } catch (\Exception $e) {
            $stringError = __("Erro ao obter conteúdo html de cupom fiscal: {0} em: {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);
        }
    }

    /**
     * Função que salva uma imagem do cupom caso a leitura não seja bem sucedida
     *
     * @return void
     */
    public function saveImageReceipt()
    {
        try {
            $success = false;

            if ($this->request->is('post')) {
                $data = $this->request->getData();

                // cria uma imagem temporária até o usuário terminar o cadastro
                $this->generateImageFromBase64(
                    $data['image'],
                    Configure::read('imageReceiptPathTemporary') . $data['image_name'] . '.jpg',
                    Configure::read('imageReceiptPathTemporary')
                );

                // rotaciona a imagem guardada temporariamente
                $this->rotateImage(Configure::read('imageReceiptPathTemporary') . $data['image_name'] . '.jpg', 90);
                $success = true;
            }

            $arraySet = ['success'];

            $this->set(compact($arraySet));
            $this->set("_serialize", $arraySet);

        } catch (\Exception $e) {
            $stringError = __("Erro ao salvar comprovante: {0} em: {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Salva recibo de forma manual
     *
     * @return void
     */
    public function saveManualReceipt()
    {
        try {
            $extension_img = '.jpg';

            $funcionario = $this->getUserLogged();

            $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
            $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

            if ($usuarioAdministrador) {
                $funcionario = $usuarioAdministrar;
            }

            if ($this->request->is('post')) {
                $data_array = $this->request->getData()['data'];

                $pontuacoes_comprovante = null;

                foreach ($data_array as $key => $data) {
                    // Pesquisa cliente para verificar associação
                    $cliente = $this->Clientes->getClienteById($data['clientes_id']);

                    $clientes_id = $cliente->id;

                    // pesquisa usuario para verificar associação
                    $usuarios_id = $data['usuarios_id'];
                    $usuario = $this->Usuarios->getUsuarioById($data['usuarios_id']);

                        // procura pelo usuário se está associado
                    $user_associated = $this->ClientesHasUsuarios->findClienteHasUsuario(
                        [
                            'ClientesHasUsuarios.usuarios_id' => $usuario->id,
                            'ClientesHasUsuarios.tipo_perfil' => $usuario->tipo_perfil,
                            'ClientesHasUsuarios.clientes_id' => $cliente->id
                        ]
                    )->first();

                        // usuário não associado, faz associação
                    if (!$user_associated) {
                        $this->ClientesHasUsuarios->saveClienteHasUsuario(
                            $cliente->id,
                            $usuario->id,
                            $usuario->tipo_perfil
                        );
                    }

                    $estado_nfe = $data['estado_nfe'];

                    $conteudo = null;
                    $chave_nfe = null;

                    if (isset($data['chave_nfe'])) {
                        $chave_nfe = $data['chave_nfe'];

                        // monta string para conteudo url
                        $conteudo = $this->sefazUtil->getUrlSefazByState($estado_nfe) . $chave_nfe;
                    }

                    // na forma manual, eu ja tenho o id de gotas que preciso
                    $gotas_id = $data['gotas_id'];

                    $gotas = $this->Gotas->getGotaById($gotas_id);

                    $quantidade = $data['quantidade_multiplicador'] * $gotas->multiplicador_gota;

                    $nome_img = $data['nome_img'] . $extension_img;

                    if (is_null($pontuacoes_comprovante)) {
                        $pontuacoes_comprovante = $this->PontuacoesComprovantes->newEntity();

                        $pontuacoes_comprovante['clientes_id'] = $clientes_id;
                        $pontuacoes_comprovante['usuarios_id'] = $usuarios_id;
                        $pontuacoes_comprovante['funcionarios_id'] = $funcionario['id'];
                        $pontuacoes_comprovante['conteudo'] = $conteudo;
                        $pontuacoes_comprovante['nome_img'] = $nome_img;
                        $pontuacoes_comprovante['chave_nfe'] = $chave_nfe;
                        $pontuacoes_comprovante['estado_nfe'] = $estado_nfe;
                        $pontuacoes_comprovante['data'] = date('Y-m-d H:i:s');

                        // importacao manual, precisa de auditoria
                        $pontuacoes_comprovante['requer_auditoria'] = true;
                        $pontuacoes_comprovante['auditado'] = false;

                        $pontuacoes_comprovante = $this->PontuacoesComprovantes->save($pontuacoes_comprovante);
                    }

                    if ($pontuacoes_comprovante) {
                        $pontuacoes = $this->Pontuacoes->newEntity();

                        $pontuacoes['clientes_id'] = $data['clientes_id'];

                        $pontuacoes['usuarios_id'] = $data['usuarios_id'];
                        $pontuacoes['funcionarios_id'] = $funcionario['id'];
                        $pontuacoes['gotas_id'] = $data['gotas_id'];
                        $pontuacoes['quantidade_multiplicador'] = $quantidade;
                        $pontuacoes['quantidade_gotas']
                            = $quantidade * $gotas->multiplicador_gota;
                        $pontuacoes['pontuacoes_comprovante_id']
                            = $pontuacoes_comprovante->id;
                        $pontuacoes['data'] = date('Y-m-d H:i:s');
                        $pontuacoes['expirado'] = false;

                        $pontuacoes = $this->Pontuacoes->save($pontuacoes);
                    }
                }

                // move a imagem para a pasta definitiva
                $this->moveDocumentPermanently(Configure::read('imageReceiptPathTemporary') . $data['nome_img'] . $extension_img, Configure::read('documentReceiptPath'), $data['nome_img'], $extension_img);

                $pontuacoes_comprovante = $this->PontuacoesComprovantes->getCouponById($pontuacoes_comprovante->id);

                $success = $pontuacoes && $pontuacoes_comprovante;
                $data = $pontuacoes_comprovante;
            }

            $arraySet = [
                'success',
                'data'
            ];

            $this->set(compact($arraySet));
            $this->set("_serialize", $arraySet);

        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao salvar comprovante: {0} ", $e->getMessage());

            Log::write('error', $stringError);
        }
    }

    /**
     * ------------------------------------------------------------
     * Serviços REST
     * ------------------------------------------------------------
     */

    /**
     * PontuacoesComprovantes::getComprovantesFiscaisUsuarioAPI
     *
     * OBtem todos os comprovantes fiscais de um usuário
     *
     * @param $data["redesId"]    Id de Redes
     * @param $data["clientesId"] Id de Clientes
     * @param $data["chaveNFE"]   Chave NFE
     * @param $data["estado"]     Estado NFE
     * @param $data["dataInicio"] Data Início
     * @param $data["dataFim"]    Data Fim
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 2018/05/02
     *
     * @return json Objeto JSON
     */
    public function getComprovantesFiscaisUsuarioAPI()
    {
        $mensagem = array();

        try {

            if ($this->request->is('post')) {

                $data = $this->request->getData();

                // Filtros
                $redesId = isset($data["redes_id"]) && strlen($data["redes_id"]) > 0 ? (int)$data["redes_id"] : null;
                $clientesId = isset($data["clientes_id"]) && strlen($data["clientes_id"]) > 0 ? (int)$data["clientes_id"] : null;
                $chaveNfe = isset($data["chave_nfe"]) && strlen($data["chave_nfe"]) > 0 ? $data["chave_nfe"] : null;
                $estadoNFE = isset($data["estado"]) && strlen($data["estado"]) > 0 ? $data["estado"] : null;
                $dataInicio = isset($data["data_inicio"]) && strlen($data["data_inicio"]) > 0 ?
                    date_format(DateTime::createFromFormat("d/m/Y", $data["data_inicio"]), "Y-m-d")
                    : null;
                $dataFim = isset($data["data_fim"]) && strlen($data["data_fim"]) > 0 ?
                    date_format(DateTime::createFromFormat("d/m/Y", $data["data_fim"]), "Y-m-d")
                    : null;

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

                $clientesIds = array();

                $whereConditions = array();

                // Critérios de pesquisa
                if (!is_null($redesId)) {
                    $rede = $this->Redes->getRedeById($redesId);

                    if (empty($rede) || !$rede["ativado"]) {
                        // Situação rara de acontecer, pois o usuário só irá conseguir selecionar uma rede que está desativada
                        // se a alteração aconteceu durante a utilização
                        $mensagem = ['status' => false, 'message' => __("Não foi possível realizar a operação, pois a rede se encontra desabilitada no sistema!")];

                        $arraySet = ["mensagem"];
                        $this->set(compact($arraySet));
                        $this->set("_serialize", $arraySet);

                        return;
                    } else {

                        foreach ($rede->redes_has_clientes as $key => $value) {
                            $clientesIds[] = $value->clientes_id;
                        }

                    }
                    // else {

                    //     // Situação rara de acontecer, pois o usuário só irá conseguir selecionar uma rede que está desativada
                    //     // se a alteração aconteceu durante a utilização
                    //     $mensagem = ['status' => false, 'message' => __("Não foi encontrado unidades para a rede informada, pois esta rede não existe ou está desabilitada no sistema!")];

                    //     $arraySet = ["mensagem"];
                    //     $this->set(compact($arraySet));
                    //     $this->set("_serialize", $arraySet);

                    //     return;
                    // }
                } else if (!is_null($clientesId)) {
                    $clientesIds[] = $clientesId;
                }

                $usuariosId = $this->Auth->user()["id"];

                $resultado = $this->PontuacoesComprovantes->getPontuacoesComprovantesUsuario(
                    $usuariosId,
                    $redesId,
                    $clientesIds,
                    $chaveNfe,
                    $estadoNFE,
                    $dataInicio,
                    $dataFim,
                    $orderConditions,
                    $paginationConditions

                );

                $mensagem = $resultado["mensagem"];
                $pontuacoes_comprovantes = $resultado["pontuacoes_comprovantes"];
            }
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $messageString = __("Não foi possível obter dados de comprovantes fiscais do usuário!");

            $mensagem = ['status' => false, 'message' => $messageString, 'errors' => $trace];

            $messageStringDebug =
                $stringError = __("{0} - {1} em: {2}. [Função: {3} / Arquivo: {4} / Linha: {5}]  ", $messageString, $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write("error", $messageStringDebug);
        }

        $arraySet = ["mensagem", "pontuacoes_comprovantes"];

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);
    }

    /**
     * PontuacoesComprovantes::setComprovanteFiscalUsuarioAPI
     *
     * Serviço REST que processa uma Nota Fiscal Eletrônica
     *
     * @param $data["url"] URL do QR Code
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 06/05/2018
     *
     * @return json object
     *
     * ----------------------------------------------------------------------
     * Aviso!
     *
     * Este método e o método saveTaxCoupon fazem a mesma coisa de finalidade,
     * mas suas execuções são diferentes!
     * Por isto, este método não se deve ser genérico!
     * Caso um dos dois tenha modificações, deve ser analisado o que será
     * impactado!
     * ----------------------------------------------------------------------
     *
     */
    public function setComprovanteFiscalUsuarioAPI()
    {
        Log::write('info', 'Iniciado processamento de cupom...');
        $mensagem = array();

        $status = false;
        $message = null;
        $errors = array();

        try {
            $result = null;

            if ($this->request->is(['post'])) {
                $data = $this->request->getData();

                $url = isset($data['qr_code']) ? $data["qr_code"] : null;

                // Verifica se foi informado qr code. Senão já aborta
                if (is_null($url)) {
                    $mensagem = array("status" => false, "message" => __("Parâmetro QR CODE não foi informado!"));

                    $arraySet = array("mensagem");
                    $this->set(compact($arraySet));
                    $this->set("_serialize", $arraySet);
                    return;
                }

                $validacaoQRCode = $this->validarUrlQrCode($url);

                // Encontrou erros de validação do QR Code. Interrompe e retorna erro ao usuário
                if ($validacaoQRCode["status"] == false) {
                    $mensagem = array("status" => $validacaoQRCode["status"], "message" => $validacaoQRCode["message"], "errors" => $validacaoQRCode["errors"]);

                    $arraySet = array("mensagem");
                    $this->set(compact($arraySet));
                    $this->set("_serialize", $arraySet);

                    return;
                }

                $chaveNfe = !empty($data["chave_nfe"]) ? $data["chave_nfe"] : null;

                $posInicial = strpos($url, "sefaz.") + 6;
                $posFinal = $posInicial + 2;
                $pos = substr($url, $posInicial, 2);

                $estado = strtoupper($pos);

                if (empty($chaveNfe)) {
                    foreach ($validacaoQRCode["data"] as $key => $value) {
                        if ($value["key"] == "chNFe") {
                            $chaveNfe = $value["content"];
                        }
                    }
                }

                // Valida se o QR Code já foi importado anteriormente

                $cupomPreviamenteImportado = $this->verificarCupomPreviamenteImportado($chaveNfe, $estado);

                // TODO: Apenas para carater de teste
                // $cupomPreviamenteImportado["status"] = true;

                // Cupom previamente importado, interrompe processamento e avisa usuário
                if (!$cupomPreviamenteImportado["status"]) {
                    $mensagem = $cupomPreviamenteImportado;

                    $arraySet = [
                        "mensagem"
                    ];

                    $this->set(compact($arraySet));
                    $this->set("_serialize", $arraySet);

                    return;
                }

                // Não teve erros, dá continuidade ao processo.

                $clienteCNPJ = !empty($data["clientes_cnpj"]) ? $data["clientes_cnpj"] : null;
                $usuariosId = !empty($data["usuarios_id"]) ? $data["usuarios_id"] : null;
                $funcionariosId = !empty($data["funcionarios_id"]) ? $data["funcionarios_id"] : null;

                $funcionario = null;
                $usuarioFinal = null;
                $cliente = null;

                if (empty($funcionariosId)) {
                    $funcionario = $this->Usuarios->getFuncionarioFicticio();
                } else {
                    $funcionario = $this->Usuarios->getUsuarioById($funcionariosId);
                }

                if (empty($usuariosId)) {
                    $usuario = $this->Auth->user();
                    $usuario = $this->Usuarios->getUsuarioById($usuario["id"]);
                } else {
                    $usuario = $this->Usuarios->getUsuarioById($usuariosId);
                }

                // Se cnpj for nulo, a pesquisa deverá ser feita sob todos os cnpjs , e depois, pesquisar eles na nota
                if (!empty($clienteCNPJ)) {
                    $cliente = $this->Clientes->getClienteByCNPJ($clienteCNPJ);
                }

                $isEstadoGoias = false;
                if ($estado == "GO") {
                    $isEstadoGoias = true;
                    $startSearch = "chNFe=";
                    $startSearchIndex = strpos($url, $startSearch) + strlen($startSearch);
                    $chave = substr($url, $startSearchIndex, 44);
                    $url = "http://nfe.sefaz.go.gov.br/nfeweb/jsp/CConsultaCompletaNFEJSF.jsf?parametroChaveAcesso=" . $chave;
                }

                $webContent = $this->webTools->getPageContent($url);

                if ($webContent["statusCode"] == 200) {

                    // Caso Mobile: Cliente não é informado
                    if (empty($cliente)) {
                        /**
                         * Diferente da API AJAX, onde na view é enviado o clientes_id a qual o funcionário
                         * trabalha, na API mobile nem sempre eu terei como saber de qual clientes_id o registro
                         * está vindo.
                         * Neste caso, é necessário saber de qual estado é o cupom em questão, pegar todos
                         * os CNPJ's da base de dados, e validar através de loop.
                         *
                         */

                        $cnpjQuery = $this->Clientes->getClientesCNPJByEstado($estado);

                        $cnpjArray = array();

                        foreach ($cnpjQuery as $key => $value) {
                            $cnpjArray[] = $value['cnpj'];
                        }

                        $cnpjEncontrado = null;
                        foreach ($cnpjArray as $key => $cnpj) {

                            Log::write('debug', __("CNPJ {$cnpj}"));
                            $cnpjPos = strpos($webContent["response"], $cnpj);

                            if ($cnpjPos > 0) {
                                $cnpjEncontrado = $cnpj;
                                break;
                            }
                        }

                    // Se encontrou o cnpj, procura o cliente através do cnpj.
                    // Se não encontrou, significa que a unidade ainda não está cadastrada no sistema,

                    // DebugUtil::print($cnpjArray);

                        if ($cnpjEncontrado) {
                            $cliente = $this->Clientes->getClienteByCNPJ($cnpjEncontrado);
                        }

                        if (empty($cliente)) {
                            $mensagem = array(
                                "status" => false,
                                "message" => __(Configure::read("messageClienteNotFoundByCNPJ", $cnpjEncontrado)), "errors" => array()
                            );

                            $arraySet = [
                                "mensagem"
                            ];
                            $this->set(compact($arraySet));
                            $this->set("_serialize", $arraySet);
                            return;
                        }
                    }


                    // $arrayInexistentes = array(
                    //     "NOTA FISCAL ELETR&Ocirc;NICA INEXISTENTE",
                    //     "NOTA FISCAL ELETRÔNICA INEXISTENTE",
                    // );

                    // $nfeInexistente = -1;

                    // foreach ($arrayInexistentes as $key => $item) {
                    //     $nfeInexistente = strpos($item, $webContent["response"]);

                    //     if ($nfeInexistente >= 0) break;
                    // }
                    $nfeInexistente = -1;
                    // TODO: ajustar nfe inexistente
                    // $nfeInexistente = strpos("NOTA FISCAL ELETRÔNICA INEXISTENTE", $webContent["response"]);

                    if ($nfeInexistente >= 0) {
                        $mensagem = array(
                            "status" => 0,
                            "message" => Configure::read("messageOperationFailureDuringProcessing"),
                            "errors" => array("Nota Fiscal Eletrônica Inexistente!")
                        );

                        $arraySet = [
                            "mensagem"
                        ];

                        $this->set(compact($arraySet));
                        $this->set("_serialize", $arraySet);

                        return;
                    }

                    // obtem todos os multiplicadores (gotas)
                    $gotas = $this->Gotas->findGotasByClientesId(array($cliente["id"]));

                    $gotas = $gotas->toArray();

                    Log::write("debug", $webContent);

                    $retorno = $this->_processaConteudoSefaz($cliente, $funcionario, $usuario, $gotas, $url, $chaveNfe, $estado, $webContent["response"]);

                    if ($retorno["mensagem"]["status"]) {
                        $pontuacaoComprovanteSave = $retorno["pontuacoesComprovante"];

                        $pontuacaoComprovante = $this->PontuacoesComprovantes->addPontuacaoComprovanteCupom(
                            $cliente["id"],
                            $usuario["id"],
                            $funcionario["id"],
                            $pontuacaoComprovanteSave["conteudo"],
                            $pontuacaoComprovanteSave["chave_nfe"],
                            $estado,
                            $pontuacaoComprovanteSave["data"],
                            $pontuacaoComprovanteSave["requer_auditoria"],
                            $pontuacaoComprovanteSave["auditado"]
                        );

                        $pontuacaoComprovanteId = $pontuacaoComprovante["id"];
                        $dataProcessamento = $pontuacaoComprovante["data"];

                        $pontuacoesTemp = $retorno["pontuacoes"];
                        $pontuacoesSave = array();

                        foreach ($pontuacoesTemp as $pontuacaoItem) {

                            $pontuacao = array(
                                "pontuacoes_comprovante_id" => $pontuacaoComprovanteId,
                                "clientes_id" => $cliente["id"],
                                "usuarios_id" => $usuario["id"],
                                "funcionarios_id" => $funcionario["id"],
                                "gotas_id" => $pontuacaoItem["gotas_id"],
                                "quantidade_multiplicador" => $pontuacaoItem["quantidade_multiplicador"],
                                "quantidade_gotas" => $pontuacaoItem["quantidade_gotas"],
                                "data" => $dataProcessamento,

                            );

                            $pontuacoesSave[] = $pontuacao;
                        }

                        $pontuacoesSave = $this->Pontuacoes->insertPontuacoesCupons($pontuacoesSave);

                        $pontuacaoComprovante = $this->PontuacoesComprovantes->getCouponById($pontuacaoComprovante["id"]);

                        $funcionarioOperacao = array(
                            "id" => $funcionario["id"],
                            "nome" => $funcionario["nome"]
                        );
                        $unidadeAtendimento = array(
                            "id" => $cliente["id"],
                            "razao_social" => $cliente["razao_social"],
                            "nome_fantasia" => $cliente["nome_fantasia"],
                            "cnpj" => $cliente["cnpj"]
                        );

                        $comprovanteResumo = array();

                        $comprovanteResumo["chave_nfe"] = $chaveNfe;
                        $comprovanteResumo["estado_nfe"] = $estado;
                        $comprovanteResumo["data"] = $dataProcessamento;
                        $comprovanteResumo["soma_pontuacoes"] = $pontuacaoComprovante["soma_pontuacoes"];

                        foreach ($pontuacaoComprovante["pontuacoes"] as $pontuacao) {

                            $item = array(
                                "nome_gota" => $pontuacao["gota"]["nome_parametro"],
                                "quantidade_gotas" => $pontuacao["quantidade_gotas"],
                                "quantidade_multiplicador" => $pontuacao["quantidade_multiplicador"]
                            );
                            $comprovanteResumo["pontuacoes"][] = $item;
                        }

                        $resumo = array(
                            "funcionario" => $funcionarioOperacao,
                            "unidade_atendimento" => $unidadeAtendimento,
                            "comprovante_resumo" => $comprovanteResumo
                        );

                        $mensagem = $retorno["mensagem"];
                        $pontuacoes_comprovantes = $pontuacaoComprovante;
                        $resumo = $resumo;

                        $arraySet = array("mensagem", "pontuacoes_comprovantes", "resumo");
                        $this->set(compact($arraySet));
                        $this->set("_serialize", $arraySet);

                        return;
                    } else {
                        // Retorna o que veio de erro

                        $arraySet = array_keys($retorno);

                        foreach ($arraySet as $value) {
                            $$value = $retorno[$value];
                        }

                        $this->set(compact($arraySet));
                        $this->set("_serialize", $arraySet);

                        return;
                    }

                } else {
                    // Trata pontuação para ser processada posteriormente

                    // Status está anormal, grava para posterior processamento

                    $clientesId = empty($cliente) ? null : $cliente["id"];

                    $pontuacao_pendente = $this
                        ->PontuacoesPendentes
                        ->createPontuacaoPendenteAwaitingProcessing(
                            $clientesId,
                            $usuario->id,
                            $funcionario->id,
                            $url,
                            $chaveNfe,
                            $estado
                        );

                    $success = false;
                    $message = Configure::read("messageNotPossibleToImportCoupon");
                    $errors = array(
                        Configure::read("messageNotPossibleToImportCouponAwaitingProcessing")
                    );
                    $data = array();

                    $arraySet = [
                        'success',
                        'message',
                        'pontuacao_pendente',
                        'data',
                        "errors"
                    ];

                    $this->set(compact($arraySet));
                    $this->set("_serialize", $arraySet);
                    return;
                }


                // TODO: remover ao finalizar
                // $webContent = $this->webTools->getPageContent("http://localhost:8080/gasolinacomum.1.html");


                /*
                 * Como é automático, preciso verificar se a loja
                 * possui gotas configuradas, se não tiver, preciso verificar a
                 * matriz. Neste ponto ao menos a matriz deve ter a configuração
                 */

                $clientes_ids = [];

                $clientes_ids[] = $cliente->id;

                $conteudo = $url;

                // obtem todos os multiplicadores (gotas)
                $gotas = $this->Gotas->findGotasByClientesId($clientes_ids);

                $gotas = $gotas->toArray();

                $pontuacoesComprovante['clientes_id'] = $cliente->id;
                $pontuacoesComprovante['usuarios_id'] = $usuario->id;
                $pontuacoesComprovante['funcionarios_id'] = $funcionario->id;
                $pontuacoesComprovante['conteudo'] = $conteudo;
                $pontuacoesComprovante['chave_nfe'] = $chaveNfe;
                $pontuacoesComprovante['estado_nfe'] = $estado;
                $pontuacoesComprovante['data'] = date('Y-m-d H:i:s');
                $pontuacoesComprovante['requer_auditoria'] = false;
                $pontuacoesComprovante['auditado'] = false;

                $pontuacao['clientes_id'] = $cliente->id;
                $pontuacao['usuarios_id'] = $usuario->id;
                $pontuacao['funcionarios_id'] = $funcionario->id;

                $pontuacao['data'] = date('Y-m-d H:i:s');

                // Status está ok, pode prosseguir com procedimento
                if ($webContent['statusCode'] == 200) {

                    $processFailed = false;

                    // verifica se nota possui o CNPJ. se o CNPJ for diferente, não autoriza a importação

                    $cnpjPos = strpos($webContent['response'], $cliente->cnpj);

                    if (!$cnpjPos) {
                        // formata o cnpj e procura novamente

                        $cnpjFormatado = substr($cliente->cnpj, 0, 2) . "." . substr($cliente->cnpj, 2, 3) . "." . substr($cliente->cnpj, 5, 3)
                            . "/" . substr($cliente->cnpj, 8, 4) . "-" . substr($cliente->cnpj, 12, 2);

                        $cnpjPos = strpos($webContent['response'], $cnpjFormatado);

                    }

                    if (!$cnpjPos) {
                        $processFailed = true;

                    } else {

                        if ($isEstadoGoias) {
                            $arrayReturn = $this->sefazUtil->convertHtmlToCouponDataGO($webContent['response'], $gotas, $pontuacao, null);
                        } else {

                            $arrayReturn = $this->sefazUtil->convertHtmlToCouponData($webContent['response'], $gotas, $pontuacao, null);
                        }

                        $arraySave = [];

                        foreach ($arrayReturn as $key => $value) {
                            array_push($arraySave, $value);
                        }

                        // DebugUtil::printArray($arraySave);
                        if (sizeof($arraySave[0]["array_pontuacoes_item"]) == 0) {
                            $mensagem = array(
                                "status" => 0,
                                "message" => Configure::read("messageOperationFailureDuringProcessing"),
                                "errors" => array(
                                    Configure::read("messageGotasPointOfServiceNotConfigured")
                                )
                            );

                            $arraySet = array("mensagem");

                            $this->set(compact($arraySet));
                            $this->set("_serialize", $arraySet);

                            return;
                        }

                        // DebugUtil::print($arraySave);
                        foreach ($arraySave as $key => $value) {
                            /*
                             * verifica se tem pontuações à gravar
                             * se não tem, somente configura o registro
                             * pendente como processado
                             */
                            // $array_pontuacao = $value['pontuacao_comprovante_item'];
                            $array_pontuacao = $value['array_pontuacoes_item'];

                            // DebugUtil::printArray($array_pontuacao);

                            $pontuacao_comprovante_id = null;

                            if (sizeof($array_pontuacao) > 0) {
                                // item novo, gera entidade e grava
                                // $pontuacao_comprovante = $value['pontuacao_comprovante_item'];
                                $array_pontuacoes_items = $value['array_pontuacoes_item'];

                                $pontuacao_comprovante = $this->PontuacoesComprovantes->addPontuacaoComprovanteCupom(
                                    $pontuacoesComprovante['clientes_id'],
                                    $pontuacoesComprovante['usuarios_id'],
                                    $funcionario->id,
                                    $pontuacoesComprovante['conteudo'],
                                    $pontuacoesComprovante['chave_nfe'],
                                    $pontuacoesComprovante['estado_nfe'],
                                    $pontuacoesComprovante['data'],
                                    false,
                                    false
                                );

                                // item novo. usa id de pontuacao_comprovante e grava
                                if ($pontuacao_comprovante) {
                                    $pontuacao_comprovante_id = $pontuacao_comprovante->id;

                                    foreach ($array_pontuacoes_items as $key => $item_pontuacao) {

                                        $item_pontuacao = $this->Pontuacoes->addPontuacaoCupom(
                                            $item_pontuacao['clientes_id'],
                                            $item_pontuacao['usuarios_id'],
                                            $funcionario->id,
                                            $item_pontuacao['gotas_id'],
                                            $item_pontuacao['quantidade_multiplicador'],
                                            $item_pontuacao['quantidade_gotas'],
                                            $pontuacao_comprovante->id,
                                            $item_pontuacao['data']
                                        );

                                        if (!$item_pontuacao) {
                                            $processFailed = true;
                                        }
                                    }
                                } else {
                                    $processFailed = true;
                                }
                            } else {
                                $mensagem = array(
                                    "status" => 0,
                                    "message" => Configure::read("messageOperationFailureDuringProcessing"),
                                    "errors" => array(
                                        Configure::read("messageGotasPointOfServiceNotConfigured")
                                    )
                                );

                                $arraySet = array("mensagem");

                                $this->set(compact($arraySet));
                                $this->set("_serialize", $arraySet);

                                return;
                            }
                        }
                    }

                    Log::write('info', 'Finalizado processamento de cupom...');

                    if ($processFailed) {

                        if (!$cnpjPos) {
                            $message = "Não foi localizado o CNPJ da unidade na Nota Fiscal Eletrônica, logo, não é possível importar os dados...";
                        } else {
                            $message = 'Houve erro ao realizar processamento de cupom, o registro não foi gravado...';
                        }
                        Log::write('error', $message);

                        $success = false;

                    } elseif (sizeof($array_pontuacao) == 0) {
                        $estado = $this->address_helper->getStatesBrazil($estado);
                        $success = false;
                        $message =
                            __(
                            'No Cupom Fiscal {0} da SEFAZ do estado {1} não há gotas conforme definições do Ponto de Atendimento!',
                            $chaveNfe,
                            $estado
                        );
                    } else {

                        // Chegou até aqui, então ocorreu tudo bem.
                        // Vincula usuário na rede como cliente

                        $this->ClientesHasUsuarios->saveClienteHasUsuario($clientes_id, $usuario["id"], $usuario["tipo_perfil"]);

                        $pontuacao_comprovante = $this->PontuacoesComprovantes->getCouponById($pontuacao_comprovante->id);
                        $success = true;
                        $message = __("Dados importados com sucesso!");
                        $data = $pontuacao_comprovante;
                    }
                }
            }

            if (empty($pontuacoesComprovante)) {
                $pontuacoesComprovante = array();
            }

            $pontuacoesComprovantes = $pontuacoesComprovante;

            $funcionarioOperacao = array(
                "id" => $funcionario->id,
                "nome" => $funcionario->nome
            );
            $unidadeAtendimento = array(
                "id" => $cliente->id,
                "razao_social" => $cliente->razao_social,
                "nome_fantasia" => $cliente->nome_fantasia,
                "cnpj" => $cliente->cnpj
            );

            $comprovanteResumo = array();

            $comprovanteResumo["chave_nfe"] = $pontuacoesComprovantes["chave_nfe"];
            $comprovanteResumo["estado_nfe"] = $pontuacoesComprovantes["estado_nfe"];
            $comprovanteResumo["data"] = $pontuacoesComprovantes["data"];
            $comprovanteResumo["soma_pontuacoes"] = $pontuacoesComprovantes["soma_pontuacoes"];

            foreach ($pontuacoesComprovantes["pontuacoes"] as $key => $pontuacao) {

                $item = array(
                    "nome_gota" => $pontuacao["gota"]["nome_parametro"],
                    "quantidade_gotas" => $pontuacao["quantidade_gotas"],
                    "quantidade_multiplicador" => $pontuacao["quantidade_multiplicador"]
                );
                $comprovanteResumo["pontuacoes"][] = $item;
            }

            $resumo = array(
                "funcionario" => $funcionarioOperacao,
                "unidade_atendimento" => $unidadeAtendimento,
                "comprovante_resumo" => $comprovanteResumo
            );
            Log::write('info', 'Finalizado processamento de cupom...');

        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $messageString = __("Erro ao obter conteúdo html de cupom fiscal!");

            $mensagem = ['status' => false, 'message' => $messageString, 'errors' => $trace];

            $messageStringDebug = __("{0} - {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $messageString, $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write("error", $messageStringDebug);
            Log::write("error", $trace);
        }

        $mensagem = array("status" => $success, "message" => $message, "errors" => $errors);

        $pontuacoes_comprovantes = $pontuacoesComprovantes;

        $arraySet = [
            'mensagem',
            'pontuacoes_comprovantes',
            "resumo"
        ];

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);
    }


    public function setPontuacoesUsuarioViaPostoAPI()
    {

        $data = array();

        if ($this->request->is("post")) {
            $data = $this->request->getData();
            $arraySet = array("data");

            $this->set(compact($arraySet));
            $this->set("_serialize", $arraySet);
        }

    }

    /**
     * PontuacoesComprovantesController::validarUrlQrCode
     *
     * Valida a URL do QRCode
     *
     * @param string $url URL de onde será capturadoos dados
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 01/03/2018
     *
     * @return array $data de Consistência de errors e validação
     */
    private function validarUrlQrCode(string $url)
    {
        /**
         * Regras:
         * Key: nome da chave;
         * Size: tamanho que deve constar;
         * FixedSize: tamanho deve ser obrigatoriamente conforme size;
         * isOptional: Se é opcional mas está informado
         * index: indice do registro na url
         */

        $arrayConsistency = array();

        // Tratamento de url para assegurar que é HTTPS
        $isHttps = stripos($url, "https://");

        if ($isHttps === false) {
            $url = str_replace("http://", "https://", $url);
        }

        // Obtem estado da URL.

        $stringSearch = "sefaz.";
        $index = stripos($url, $stringSearch) + strlen($stringSearch);

        $estado = substr($url, $index, 2);
        $estado = strlen($estado) > 0 ? strtoupper($estado) : $estado;

        $arrayConsistency[] = ["key" => 'chNFe', "size" => 44, "fixedSize" => true, "isOptional" => false, "content" => null, "index" => 0, "estado" => $estado];
        $arrayConsistency[] = ["key" => 'nVersao', "size" => 3, "fixedSize" => true, "isOptional" => false, "content" => null, "index" => 0, "estado" => $estado];
        $arrayConsistency[] = ["key" => 'tpAmb', "size" => 1, "fixedSize" => true, "isOptional" => false, "content" => null, "index" => 0, "estado" => $estado];
        $arrayConsistency[] = ["key" => 'cDest', "size" => 3, "fixedSize" => false, "isOptional" => true, "content" => null, "index" => 0, "estado" => $estado];
        $arrayConsistency[] = ["key" => 'dhEmi', "size" => 50, "fixedSize" => false, "isOptional" => false, "content" => null, "index" => 0, "estado" => $estado];
        $arrayConsistency[] = ["key" => 'vNF', "size" => 15, "fixedSize" => false, "isOptional" => false, "content" => null, "index" => 0, "estado" => $estado];
        $arrayConsistency[] = ["key" => 'vICMS', "size" => 15, "fixedSize" => false, "isOptional" => false, "content" => null, "index" => 0, "estado" => $estado];
        $arrayConsistency[] = ["key" => 'digVal', "size" => 56, "fixedSize" => true, "isOptional" => false, "content" => null, "index" => 0, "estado" => $estado];
        $arrayConsistency[] = ["key" => 'cIdToken', "size" => 6, "fixedSize" => true, "isOptional" => false, "content" => null, "index" => 0, "estado" => $estado];
        $arrayConsistency[] = ["key" => 'cHashQRCode', "size" => 40, "fixedSize" => true, "isOptional" => false, "content" => null, "index" => 0, "estado" => $estado];

        $hasErrors = false;

        $arrayErrors = array();
        $arrayResult = array();

        foreach ($arrayConsistency as $value) {

            $key = $value["key"] . '=';

            // aponta o índice para o início do valor

            $keyIndex = strpos($url, $key);
            $value["index"] = $keyIndex + strlen($key);

            // registro é obrigatório?
            if (!$value["isOptional"]) {

                $errorType = "";

                // é obrigatório mas não encontrado?
                if (strlen($keyIndex) == 0) {
                    $errorType = __("Campo {0} do QR Code deve ser informado", $value["key"]);
                } else {

                    // índice de fim
                    $indexEnd = strpos($url, "&", $keyIndex);

                    // caso extraordinário, trata se o campo for o último da lista
                    if ($value["index"] > $indexEnd) {
                        $indexEnd = strlen($url);
                    }

                    // cálculo de tamanho do valor
                    $length = $indexEnd - $value["index"];

                    // captura conteúdo

                    $value["content"] = substr($url, $value["index"], $indexEnd - $value["index"]);

                    // valida se o campo contem espaços (não é permitido)

                    $containsBlank = strpos($value["content"], " ");

                    // encontrou algum espaço em branco
                    if (strlen($containsBlank) == 0) {

                        // valida se o tamanho do campo é fixo
                        if ($value["fixedSize"]) {
                            if ($length != $value["size"]) {
                                $errorType = __("Campo {0} do QR Code deve conter {1} bytes", $value["key"], $value["size"]);
                            }
                        }

                    } else {
                        $errorType = __(
                            "Campo {0} contêm espaço em branco.",
                            $value["key"]
                        );
                    }
                }

                if (strlen($errorType) > 0) {
                    $value["error"] = $errorType;
                    $arrayErrors[] = $value;
                    $hasErrors = true;
                }
            }

            $arrayResult[] = $value;
        }

        // se houve erro na análise da URL, o usuário deverá informar os dados manualmente

        $errorMessage = null;
        $status = 1;
        $errors = array();

        if (sizeof($arrayErrors) > 0) {

            $errorMessage = __("O QR Code informado não está gerado conforme os padrões pré- estabelecidos da SEFAZ, não sendo possível realizar sua importação!");
            $status = 0;
            $errors = $arrayErrors;
        }

        $result = array();

        $result = ["status" => $status, "message" => $errorMessage, "errors" => $errors, "data" => $arrayResult];

        // Retorna Array contendo erros de validações
        return $result;
    }

    /**
     * PontuacoesComprovantes::verificarCupomPreviamenteImportado
     *
     * Verifica por um cupom previamente importado
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since  2018-05-09
     *
     * @param string $chaveNfe Chave da Nota Fiscal
     * @param string $estado   Estado da Nota Fiscal
     *
     * @return array $array    Resultado
     */
    public function verificarCupomPreviamenteImportado(string $chaveNfe, string $estado)
    {
        $pontuacaoPendente = $this->PontuacoesPendentes->findPontuacaoPendenteAwaitingProcessing($chaveNfe, $estado);

        if (!$pontuacaoPendente) {
            $pontuacaoComprovante
                = $this->PontuacoesComprovantes->findCouponByKey(
                $chaveNfe,
                $estado
            );
        }

        $status = true;
        $message = null;
        $errors = array();

        if ($pontuacaoPendente) {
            if ($pontuacaoPendente->registro_processado) {
                $message = Configure::read("messageOperationFailureDuringProcessing");
                $errors[] = "Este registro já foi importado previamente!";
                $status = 0;
            } else {
                $message = Configure::read("messageWarningDefault");
                $errors[] = "Este registro está aguardando processamento, não é necessário importar novamente!";
                $status = 0;
            }
        } elseif ($pontuacaoComprovante) {
            $status = 0;
            $message = Configure::read("messageOperationFailureDuringProcessing");
            $errors[] = "Este registro já foi importado previamente!";
        }

        return array("status" => $status, "message" => $message, "errors" => $errors);
    }


    /**
     * Remove Pontuações Ambiente desenvolvimento
     *
     * @return
     */
    public function removerPontuacoesDevAPI()
    {
        try {
            $deletePontuacoes = $this->Pontuacoes->deleteAllPontuacoes();
            $deletePontuacoesComprovantes = $this->PontuacoesComprovantes->deleteAllPontuacoesComprovantes();

            $dadosApagados = "";

            $dadosApagados = $deletePontuacoes ? $dadosApagados . "PONTUAÇÕES , " : $dadosApagados;
            $dadosApagados = $deletePontuacoesComprovantes ? $dadosApagados . "PONTUAÇÕES COMPROVANTES " : $dadosApagados;

            if ($deletePontuacoes || $deletePontuacoesComprovantes) {

                $mensagem = array(
                    "status" => 1,
                    "message" => __("Dados de {0} apagados com sucesso!", $dadosApagados),
                    "errors" => array()
                );
            } else if ($deletePontuacoes == 0 && $deletePontuacoesComprovantes == 0) {
                $mensagem = array(
                    "status" => 0,
                    "message" => __("messageDeleteError"),
                    "errors" => array("Não há dados para serem apagados!")
                );
            }

            $arraySet = array("mensagem");

            $this->set(compact($arraySet));
            $this->set("_serialize", $arraySet);

            return;


        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao remover registros: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write("error", $trace);
        }
    }

    /**
     * Undocumented function
     *
     * @param Cliente $cliente
     * @param Usuario $funcionario
     * @param Usuario $usuario
     * @param array $gotas
     * @param string $url
     * @param string $chave
     * @param string $conteudo
     * @return void
     */
    private function _processaConteudoSefaz(Cliente $cliente, Usuario $funcionario, Usuario $usuario, array $gotas, string $url, string $chave, string $estado, string $conteudo)
    {
        $dataProcessamento = date("Y-m-d H:i:s");
        $isXML = StringUtil::validarConteudoXML($conteudo);

        if (Configure::read("debug")) {
            Log::write("debug", $conteudo);
        }

        if ($isXML) {

            $xml = SefazUtil::obtemDadosXMLCupomSefaz($conteudo);

            // $emitente = $xml["emitente"];
            // $produtosListaXml = $xml["produtos"];
            // $cnpjNotaFiscalXML = $xml["cnpj"];

            // Obtem todos os dados de pontuações e comprovantes
            // Irá mudar se os outros estados tratam o XML de forma diferente. Deve ser analizado
            $retorno = $this->_processaDadosCupomXMLSefaz($cliente["cnpj"], $estado, $url, $chave, $xml, $gotas);

            return $retorno;

        } else {
            // É HTML

            // Obtem todos os dados de pontuações
            $pontuacoesHtml = SefazUtil::obtemDadosHTMLCupomSefaz($conteudo, $gotas, $estado);

            // Prepara dados de cupom para gravar
            // Só gera o comprovante se tiver alguma pontuação
            if (sizeof($pontuacoesHtml) == 0) {
                $mensagem = array(
                    "status" => false,
                    "message" => __(Configure::read("messageNotPossibleToImportCoupon")),
                    "errors" => array(
                        __('No Cupom Fiscal {0} da SEFAZ do estado {1} não há gotas à processar conforme configurações definidas!...', $chave, $estado)
                    )
                );

                return array(
                    "mensagem" => $mensagem,
                    "pontuacoes_comprovantes" => array()
                );

            } else {
                $pontuacoesComprovante = array(
                    "clientes_id" => $cliente["id"],
                    "usuarios_id" => $usuario["id"],
                    "funcionarios_id" => $funcionario["id"],
                    "conteudo" => $url,
                    "chave_nfe" => $chave,
                    "estado_nfe" => $estado,
                    "data" => $dataProcessamento,
                    "requer_auditoria" => 0,
                    "auditado" => 0
                );

                $pontuacoes = array();

                foreach ($pontuacoesHtml as $itemPontuacao) {
                    $pontuacao = array(
                        "pontuacao_comprovante_id" => 0,
                        "clientes_id" => $cliente["id"],
                        "usuarios_id" => $usuario["id"],
                        "funcionarios_id" => $funcionario["id"],
                        "gotas_id" => $itemPontuacao["gotas_id"],
                        "quantidade_multiplicador" => $itemPontuacao["quantidade_multiplicador"],
                        "quantidade_gotas" => $itemPontuacao["quantidade_gotas"],
                        "data" => $dataProcessamento,
                    );

                    $pontuacoes[] = $pontuacao;
                }

                $mensagem = array(
                    "status" => true,
                    "message" => __(Configure::read("messageCouponImportSuccess")),
                    "errors" => array()
                );

                return array(
                    "mensagem" => $mensagem,
                    "pontuacoesComprovante" => $pontuacoesComprovante,
                    "pontuacoes" => $pontuacoes
                );
            }
        }
    }

    /**
     * PontuacoesComprovantesController::_processaDadosCupomXMLSefaz
     *
     * Processa dados de Cupom da Sefaz
     *
     * @param string $clienteCNPJ CNPJ Cliente
     * @param string $estado Estado
     * @param string $url URL da Chave
     * @param string $chave Chave da URL
     * @param array $xml Dados de XML
     * @param array $gotas As gotas do cliente
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2018-11-05
     *
     * @return array Resultado
     */
    private function _processaDadosCupomXMLSefaz(string $clienteCNPJ, string $estado, string $url, string $chave, array $xmlData, array $gotas)
    {
        $produtosListaXml = $xmlData["produtos"];
        $cnpjNotaFiscalXML = $xmlData["cnpj"];
        $dataProcessamento = date("Y-m-d H:i:s");

        // Confere CNPJ
        if ($clienteCNPJ != $cnpjNotaFiscalXML) {

            // Este erro só pode acontecer, via Web, pois o cliente Mobile não vai passar o estabeleciemnto
            // O Método que chama deverá informar o Estabelecimento em questão.
            // Se CNPJ não bate, informa e encerra
            $success = false;
            $message = __(Configure::read("messageNotPossibleToImportCoupon"));
            $errors = array(
                Configure::read("messagePointOfServiceCNPJNotEqual")
            );
            $data = array();
            $retorno = array(
                "mensagem" => array(
                    "status" => $success,
                    "message" => $message,
                    "errors" => $errors
                ),
                "pontuacoes_comprovantes" => $data
            );

            return $retorno;
        }

        $pontuacoes = array();
        $produtosLista = array();

        foreach ($produtosListaXml as $produto) {
            $gotaEncontrada = array_filter($gotas, function ($item) use ($produto) {
                return $item["nome_parametro"] == $produto["prod"]["xProd"];
            });

            $gotaEncontrada = reset($gotaEncontrada);

            if ($gotaEncontrada) {
                // Encontrou alguma gota
                $produto["prod"]["gota"] = $gotaEncontrada;
                $produtosLista[] = $produto;
            }
        }

        if (sizeof($produtosLista) == 0) {
            // Mensagem de erro informando que não foi encontrado gotas

            $success = false;
            $message = __(Configure::read("messageOperationFailureDuringProcessing"));
            $data = array();

            $retorno = array(
                "mensagem" => array(
                    "status" => $success,
                    "message" => $message,
                    "errors" => array(
                        Configure::read("messageOperationFailureDuringProcessing"),
                        Configure::read("messageGotasPointOfServiceNotConfigured")
                    )
                ),
                "pontuacoesComprovante" => $data
            );

            return $retorno;
        }

        $pontuacoesComprovante = array(
            "conteudo" => $url,
            "chave_nfe" => $chave,
            "estado_nfe" => $estado,
            "data" => $dataProcessamento,
            "requer_auditoria" => 0,
            "auditado" => 0
        );

        Log::write("debug", $produtosLista);

        $somaPontuacoes = 0;
        foreach ($produtosLista as $produto) {

            $gota = $produto["prod"]["gota"];

            $pontuacao = array(
                "gotas_id" => $produto["prod"]["gota"]["id"],
                "quantidade_multiplicador" => $produto["prod"]["qCom"],
                "quantidade_gotas" => $gota["multiplicador_gota"] * $produto["prod"]["qCom"],
                "data" => $dataProcessamento,
                // TODO: armazenar valor do produto para alteração de preço de gota
                // "valor_produto" => $produto["prod"]["vUnCom"]
            );

            $somaPontuacoes += $pontuacao["quantidade_gotas"];
            $pontuacoes[] = $pontuacao;
        }

        return array(
            "mensagem" => array(
                "status" => true,
                "message" => __(Configure::read("messageCouponImportSuccess")),
                "errors" => array()
            ),
            "pontuacoesComprovante" => $pontuacoesComprovante,
            "pontuacoes" => $pontuacoes,
        );
    }

    /**
     * ------------------------------------------------------------
     * Métodos Comuns
     * ------------------------------------------------------------
     */

    /**
     * BeforeRender callback
     *
     * @param Event $event Evento
     *
     * @return void
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
     * @return \Cake\Network\Response|null|void
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        if (is_null($this->address_helper)) {
            $this->address_helper = new AddressHelper(new \Cake\View\View());
        }

        // $this->Auth->allow(['getNewReceiptName', 'saveImageReceipt', 'saveTaxCoupon', 'findTaxCoupon']);
    }

    /**
     * Initialize method
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
    }
}
