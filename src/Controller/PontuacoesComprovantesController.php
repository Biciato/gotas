<?php
namespace App\Controller;

use \DateTime;
use App\Controller\AppController;
use App\Custom\RTI\DateTimeUtil;
use App\Custom\RTI\DebugUtil;
use App\Custom\RTI\NumberUtil;
use App\Custom\RTI\ImageUtil;
use App\Custom\RTI\ResponseUtil;
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

/**
 * PontuacoesComprovantes Controller
 *
 * @property \App\Model\Table\PontuacoesComprovantesTable $PontuacoesComprovante
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

    #region Métodos de Action

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
                __("Erro ao realizar tratamento de invalidar pontuação: {0} em: {1} ", $e->getMessage(), $trace[1]) : __("Erro ao realizar tratamento de validar pontuação: {0} em: {1} ", $e->getMessage(), $trace[1]);

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
    { }

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

            $rede = $this->request->session()->read('Rede.Grupo');

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

    #endregion

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
                        $funcionariosList = $this->Usuarios->find('list')
                            ->where(
                                array('id in ' => $funcionariosIds)
                            )
                            ->order(
                                array('nome' => 'asc')
                            );

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

            $stringError = __(
                "Erro ao exibir comprovantes de pontuações para usuário: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ",
                $e->getMessage(),
                __FUNCTION__,
                __FILE__,
                __LINE__
            );

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
                        array(
                            'ClientesHasUsuarios.clientes_id in ' => $clientesIdsList
                        )
                    );

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
                        $usuariosWhereConditions[] = ['tipo_perfil' => Configure::read('profileTypes')['UserProfileType']];

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

            return;
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

                    $retorno = $this->processaConteudoSefaz($cliente, $funcionario, $usuario, $gotas, $url, $chave, $estado, $webContent["response"]);

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
                ImageUtil::generateImageFromBase64(
                    $data['image'],
                    Configure::read('imageReceiptPathTemporary') . $data['image_name'] . '.jpg',
                    Configure::read('imageReceiptPathTemporary')
                );

                // rotaciona a imagem guardada temporariamente
                ImageUtil::rotateImage(Configure::read('imageReceiptPathTemporary') . $data['image_name'] . '.jpg', 90);
                $success = true;
            }

            $arraySet = array('success');

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
            $funcionario = $this->getUserLogged();

            $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
            $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

            if ($usuarioAdministrador) {
                $funcionario = $usuarioAdministrar;
            }

            if ($this->request->is('post')) {
                $data = $this->request->getData();

                $dataProcessamento = !empty($data["data_processamento"]) ? $data["data_processamento"] : date("Y-m-d H:i:s");
                $img = !empty($data["image"]) ? $data["image"] : null;
                $nomeImg = null;
                $nomeImgExtensao = null;

                if (!empty($img)) {
                    $nomeImg = $this->PontuacoesComprovantes->generateNewImageCoupon();

                    // move a imagem
                    $nomeImgExtensao = $nomeImg . '.jpg';

                    ImageUtil::generateImageFromBase64(
                        $img,
                        Configure::read('imageReceiptPathTemporary') . $nomeImg,
                        Configure::read('imageReceiptPathTemporary')
                    );

                    // rotaciona a imagem guardada temporariamente
                    ImageUtil::rotateImage(Configure::read('imageReceiptPathTemporary') . $nomeImg, 90);
                }


                $data_array = $data['data'];

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
                            'ClientesHasUsuarios.clientes_id' => $cliente->id
                        ]
                    );

                    // usuário não associado, faz associação
                    if (!$user_associated) {
                        $this->ClientesHasUsuarios->saveClienteHasUsuario(
                            $cliente->id,
                            $usuario->id
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

                    $chaveNFEIndex = strpos($chave_nfe, "chNFe=");
                    if ($chaveNFEIndex > 0) {
                        $chave_nfe = substr($chave_nfe, $chaveNFEIndex + strlen("chNFe="));
                    }

                    $chaveNFEIndex = strpos($chave_nfe, "nVersao=");
                    if ($chaveNFEIndex > 0) {
                        $chave_nfe = substr($chave_nfe, 0, 44);
                    }

                    // na forma manual, eu ja tenho o id de gotas que preciso
                    $gotas_id = $data['gotas_id'];

                    $gotas = $this->Gotas->getGotasById($gotas_id);

                    $quantidade = $data['quantidade_multiplicador'] * $gotas->multiplicador_gota;

                    // $nome_img = $data['nome_img'] . $extension_img;

                    if (is_null($pontuacoes_comprovante)) {
                        $pontuacoes_comprovante = $this->PontuacoesComprovantes->newEntity();

                        $pontuacoes_comprovante['clientes_id'] = $clientes_id;
                        $pontuacoes_comprovante['usuarios_id'] = $usuarios_id;
                        $pontuacoes_comprovante['funcionarios_id'] = $funcionario['id'];
                        $pontuacoes_comprovante['conteudo'] = $conteudo;
                        $pontuacoes_comprovante['nome_img'] = $nomeImgExtensao;
                        $pontuacoes_comprovante['chave_nfe'] = $chave_nfe;
                        $pontuacoes_comprovante['estado_nfe'] = $estado_nfe;
                        $pontuacoes_comprovante['data'] = $dataProcessamento;

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
                        $pontuacoes['data'] = $dataProcessamento;
                        $pontuacoes['expirado'] = false;

                        $pontuacoes = $this->Pontuacoes->save($pontuacoes);
                    }
                }

                // move a imagem para a pasta definitiva
                $this->moveDocumentPermanently(Configure::read('imageReceiptPathTemporary') . $nomeImg, Configure::read('documentReceiptPath'), $nomeImg, ".jpg");

                $pontuacoes_comprovante = $this->PontuacoesComprovantes->getCouponById($pontuacoes_comprovante->id);

                $success = $pontuacoes && $pontuacoes_comprovante;
                $data = array("pontuacoes_comprovantes" => $pontuacoes_comprovante);
            }

            $arraySet = array('success', 'data');

            $this->set(compact($arraySet));
            $this->set("_serialize", $arraySet);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao salvar comprovante: {0} ", $e->getMessage());

            Log::write('error', $stringError);
        }
    }

    #region REST Methods

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
        return;
    }

    /**
     * PontuacoesComprovantes::setComprovanteFiscalUsuarioAPI
     *
     * Serviço REST que processa uma Nota Fiscal Eletrônica
     *
     * @param $data["url"] URL do QR Code
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 06/05/2018
     *
     * @return json object
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

                $retorno = $this->processaCupom($data);

                if ($retorno["mensagem"]["status"]) {
                    $arrayData = array(
                        "pontuacoes_comprovantes" => $retorno["pontuacoes_comprovantes"],
                        "resumo" => $retorno["resumo"]
                    );

                    ResponseUtil::successAPI($retorno["mensagem"]["message"], $arrayData);
                } else {

                    $arrayData = array();

                    foreach ($retorno as $key => $value) {
                        if ($key != "mensagem") {
                            $arrayData[$key] = $value;
                        }
                    }

                    ResponseUtil::errorAPI($retorno["mensagem"]["message"], $retorno["mensagem"]["errors"], $arrayData);
                }
            }

            Log::write('info', 'Finalizado processamento de cupom...');
        } catch (\Exception $e) {
            $trace = $e->getTraceAsString();
            $messageString = __("Erro ao obter conteúdo html de cupom fiscal!");

            $mensagem = ['status' => false, 'message' => $messageString, 'errors' => $trace];

            $messageStringDebug = __(
                "{0} - {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ",
                $messageString,
                $e->getMessage(),
                __FUNCTION__,
                __FILE__,
                __LINE__
            );

            Log::write("error", $messageStringDebug);
            Log::write("error", $trace);
        }
    }

    public function setComprovanteFiscalViaFuncionarioAPI()
    {
        Log::write('info', 'Iniciado processamento de cupom...');
        $mensagem = array();

        $status = false;
        $message = null;
        $errors = array();
        try {
            if ($this->request->is("post")) {
                $data = $this->request->getData();
                $usuariosId = !empty($data["usuarios_id"]) ? (int)$data["usuarios_id"] : null;
                $qrCode = !empty($data["qr_code"]) ? $data["qr_code"] : null;
                $funcionariosId = $this->Auth->user()["id"];

                $errors = array();

                if (empty($usuariosId)) {
                    $errors[] = MESSAGE_PONTUACOES_COMPROVANTES_USUARIOS_ID_EMPTY;
                }

                if (empty($qrCode)) {
                    $errors[] = MESSAGE_QR_CODE_EMPTY;
                }

                if (count($errors) > 0) {
                    $mensagem = array(
                        "status" => 0,
                        "message" => MESSAGE_OPERATION_FAILURE_DURING_PROCESSING,
                        "errors" => $errors
                    );

                    return ResponseUtil::errorAPI($mensagem["message"], $mensagem["errors"]);
                }

                $data = array(
                    "usuarios_id" => $usuariosId,
                    "qr_code" => $qrCode
                );

                $retorno = $this->processaCupom($data);

                $mensagem = $retorno["mensagem"];

                $responseData = array();
                foreach ($retorno as $key => $value) {
                    if ($key != "mensagem") {
                        $responseData[$key] = $value;
                    }
                }

                if ($mensagem["status"]) {
                    return ResponseUtil::successAPI($mensagem["message"], $responseData);
                } else {
                    return ResponseUtil::errorAPI($mensagem["message"], $mensagem["errors"], $responseData);
                }
            }
        } catch (\Exception $e) {
            $trace = $e->getTraceAsString();

            Log::write("error", $message);

            $trace = $e->getTraceAsString();
            $messageString = __("Erro ao obter conteúdo html de cupom fiscal!");

            $mensagem = ['status' => false, 'message' => $messageString, 'errors' => $trace];

            $messageStringDebug = __(
                "{0} - {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ",
                $messageString,
                $e->getMessage(),
                __FUNCTION__,
                __FILE__,
                __LINE__
            );

            Log::write("error", $messageStringDebug);
            Log::write("error", $trace);
        }

        Log::write("info", "Finalizando Processamento de cupom...");
    }

    /**
     * PontuacoesComprovantesController::setPontuacoesUsuarioViaPostoAPI
     *
     * Grava as pontuações de um usuário que abasteceu no posto de atendimento
     * sem precisar de cadastro
     *
     * @return void
     */
    public function setPontuacoesUsuarioViaPostoAPI()
    {
        if ($this->request->is("post")) {
            $data = $this->request->getData();
            $errors = array();

            // Informações do POST
            $cnpj = !empty($data["cnpj"]) ? $data["cnpj"] : null;
            $cpf = !empty($data["cpf"]) ? $data["cpf"] : null;
            $gotasAbastecidasClienteFinal = !empty($data["gotas_abastecidas"]) ? $data["gotas_abastecidas"] : array();
            $qrCode = !empty($data["qr_code"]) ? $data["qr_code"] : null;
            $funcionario = $this->getUserLogged();

            // Validação
            if (empty($cnpj)) {
                $errors[] = MESSAGE_CNPJ_EMPTY;
            }

            if (empty($cpf)) {
                $errors[] = MESSAGE_USUARIOS_CPF_EMPTY;
            }

            if (empty($gotasAbastecidasClienteFinal) && sizeof($gotasAbastecidasClienteFinal) == 0) {
                $errors[] = "Itens da Venda não foram informados!";
            }

            if (sizeof($errors) > 0) {
                ResponseUtil::errorAPI(MESSAGE_OPERATION_FAILURE_DURING_PROCESSING, $errors, $data);
            }

            // Validação CNPJ e CPF
            $validacaoCNPJ = NumberUtil::validarCNPJ($cnpj);
            $validacaoCPF = NumberUtil::validarCPF($cpf);

            if ($validacaoCNPJ == 0) {
                $errors[] = $validacaoCNPJ["message"];
            }

            if ($validacaoCPF["status"] == 0) {
                $errors[] = $validacaoCPF["message"];
            }

            if (sizeof($errors) > 0) {
                ResponseUtil::errorAPI(MESSAGE_OPERATION_FAILURE_DURING_PROCESSING, $errors, $data);
            }

            // Posto de atendimento
            $cliente = $this->Clientes->getClienteByCNPJ($cnpj);

            if (empty($cliente)) {
                $errors[] = sprintf("%s %s", MESSAGE_CNPJ_NOT_REGISTERED_ON_SYSTEM, MESSAGE_CNPJ_EMPTY);
            }

            $chave = null;

            if (strtoupper($cliente["estado"] == "MG")) {
                if (empty($qrCode)) {
                    $qrCode = "CUPOM ECF-MG";
                    $chave = $qrCode;
                } else {
                    $chave = $qrCode;
                }
            } else {
                if (empty($qrCode)) {
                    $errors[] = MESSAGE_COUPON_EMPTY;
                } else if (strpos($qrCode, "sefaz.") == 0) {
                    $errors[] = MESSAGE_COUPON_MISMATCH_FORMAT;
                }

                if (sizeof($errors) > 0) {
                    ResponseUtil::errorAPI(MESSAGE_OPERATION_FAILURE_DURING_PROCESSING, $errors, $data);
                } else {
                    $chave = substr($qrCode, strpos($qrCode, "chNFe=") + strlen("chNFe="), 44);
                }
            }

            if (sizeof($errors) > 0) {
                ResponseUtil::errorAPI(MESSAGE_OPERATION_FAILURE_DURING_PROCESSING, $errors, $data);
            }

            // Fim de Validação

            // Cliente do posto
            $usuario = $this->Usuarios->getUsuarioByCPF($cpf);

            // Se usuário não encontrado, cadastra para futuro acesso
            if (empty($usuario)) {
                $usuario = $this->Usuarios->addUsuarioAguardandoAtivacao($cpf);
            }

            // Se usuário cadastrado, vincula ele ao ponto de atendimento (cliente)
            if ($usuario) {
                $this->ClientesHasUsuarios->saveClienteHasUsuario($cliente["id"], $usuario["id"], 0);
            }

            if (empty($funcionario)) {
                $funcionario = $this->Usuarios->findUsuariosByType(PROFILE_TYPE_DUMMY_WORKER)->first();
            }

            $gotasCliente = $this->Gotas->findGotasEnabledByClientesId($cliente["id"]);
            $pontuacoes = array();
            $data = date("Y-m-d H:i:s");
            $gotasAtualizarPreco = array();
            $gotasCliente = $gotasCliente->toArray();

            foreach ($gotasAbastecidasClienteFinal as $gotaUsuario) {

                $gota = array_filter($gotasCliente, function ($item) use ($gotaUsuario) {
                    return $gotaUsuario["gotas_nome"] == $item["nome_parametro"];
                });

                $gota = array_values($gota);

                if (!empty($gota)) {
                    $gota = $gota[0];
                    $item = array(
                        "multiplicador_gota" => floor($gotaUsuario["gotas_qtde"]),
                        "quantidade_gota" => floor($gota["multiplicador_gota"] * $gotaUsuario["gotas_qtde"]),
                        "clientes_id" => $cliente["id"],
                        "usuarios_id" => $usuario["id"],
                        "funcionarios_id" => $funcionario["id"],
                        "gotas_id" => $gota["id"],
                        "data" => $data
                    );

                    // Confere quais gotas estão com preço desatualizado

                    if ($gotaUsuario["gotas_vl_unit"] != $gota["valor_atual"]) {
                        $gotaPreco = array(
                            "clientes_id" => $cliente["id"],
                            "gotas_id" => $gota["id"],
                            "preco" => $gotaUsuario["gotas_vl_unit"]
                        );

                        // @todo gustavosg: pendente!
                        $gotasAtualizarPreco[] = $gotaPreco;
                    }

                    $pontuacoes[] = $item;
                }
            }

            $pontuacoesComprovante = array(
                "qr_code" => $qrCode
            );

            $pontuacaoComprovanteSave = $this->PontuacoesComprovantes->addPontuacaoComprovanteCupom($cliente["id"], $usuario["id"], $funcionario["id"], $qrCode, $chave, $cliente["estado"], date("Y-m-d H:i:s"), 0, 1);

            foreach ($pontuacoes as $pontuacao) {
                $pontuacaoSave = $this->Pontuacoes->addPontuacaoCupom($cliente["id"], $usuario["id"], $funcionario["id"], $pontuacao["gotas_id"], $pontuacao["multiplicador_gota"], $pontuacao["quantidade_gota"], $pontuacaoComprovanteSave["id"], $data);
            }

            $comprovante = $this->PontuacoesComprovantes->getCouponById($pontuacaoComprovanteSave["id"]);

            $comprovanteResumo = array();
            $comprovanteResumo["chave_nfe"] = $chave;
            $comprovanteResumo["estado_nfe"] = $cliente["estado"];
            $comprovanteResumo["data"] = $data;
            $comprovanteResumo["soma_pontuacoes"] = floor($comprovante["soma_pontuacoes"]);

            foreach ($comprovante["pontuacoes"] as $pontuacao) {
                $item = array(
                    "nome_gota" => $pontuacao["gota"]["nome_parametro"],
                    "quantidade_gotas" => floor($pontuacao["quantidade_gotas"]),
                    "quantidade_multiplicador" => floor($pontuacao["quantidade_multiplicador"])
                );
                $comprovanteResumo["pontuacoes"][] = $item;
            }

            $resumo = array(
                "funcionario" => $funcionario,
                "unidade_atendimento" => $cliente,
                "comprovante_resumo" => $comprovanteResumo
            );

            $retorno = array(
                "pontuacoes_comprovantes" => $pontuacaoComprovanteSave,
                "resumo_envio_pontuacoes" => array(
                    "funcionario" => $funcionario,
                    "unidade_atendimento" => $cliente,
                    "comprovantes_resumo" => $comprovanteResumo
                )
            );
            ResponseUtil::successAPI(MESSAGE_PROCESSING_COMPLETED, $retorno);
        }
    }

    /**
     * Remove Pontuações Ambiente desenvolvimento
     *
     * @return void
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
     * PontuacoesComprovantesController::processaCupom
     *
     * Realiza conjunto de processamento do cupom da SEFAZ
     *
     * @param array $data
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-05-12
     *
     * @return json Response
     */
    private function processaCupom($data)
    {
        $url = isset($data['qr_code']) ? $data["qr_code"] : null;
        $processamentoPendente = isset($data["processamento_pendente"]) ? $data["processamento_pendente"] : false;

        // Verifica se foi informado qr code. Senão já aborta
        if (is_null($url)) {
            $mensagem = array("status" => 0, "message" => __("Parâmetro QR CODE não foi informado!"));

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
        $estado = $validacaoQRCode["estado"];

        if (empty($chaveNfe)) {
            foreach ($validacaoQRCode["data"] as $key => $value) {
                if ($value["key"] == "chNFe") {
                    $chaveNfe = $value["content"];
                }
            }
        }
        $chave = $chaveNfe;

        // Valida se o QR Code já foi importado anteriormente

        $cupomPreviamenteImportado = $this->verificarCupomPreviamenteImportado($chaveNfe, $estado);

        // @todo: Apenas para carater de teste
        // $cupomPreviamenteImportado["status"] = true;

        // Cupom previamente importado, interrompe processamento e avisa usuário
        if (!$cupomPreviamenteImportado["status"] && !$processamentoPendente) {

            $mensagem = $cupomPreviamenteImportado;
            $resposta = array("mensagem" => $mensagem);

            return $resposta;
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

        $webContent = WebTools::getPageContent($url);

        // @todo Só para carater de teste
        // $webContent["statusCode"] = 400;

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
                if ($estado == "MG"){
                    // Se estado == MG, preciso procurar a posição do cnpj com formatação
                    foreach ($cnpjQuery as $key => $value) {
                        $cnpjArray[] = preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "$1.$2.$3/$4-$5", $value["cnpj"]);
                    }

                } else {

                    foreach ($cnpjQuery as $key => $value) {
                        $cnpjArray[] = $value['cnpj'];
                    }

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
                    $cnpjEncontrado = NumberUtil::limparFormatacaoNumeros($cnpjEncontrado);
                    $cliente = $this->Clientes->getClienteByCNPJ($cnpjEncontrado);
                }

                if (empty($cliente)) {
                    $errors = array(__(Configure::read("messageClienteNotFoundByCupomFiscal"), $chave));
                    $mensagem = array(
                        "status" => 0,
                        "message" => __(Configure::read("messageClienteNotFoundByCupomFiscal"), $chave),
                        "errors" => $errors
                    );

                    $arraySet = [
                        "mensagem"
                    ];

                    return ResponseUtil::errorAPI($mensagem["message"], $errors);
                    $this->set(compact($arraySet));
                    $this->set("_serialize", $arraySet);
                    return;
                }
            }

            // Valida se a rede está ativa
            if (!$cliente["rede_has_cliente"]["rede"]["ativado"]) {
                $message = MESSAGE_GENERIC_COMPLETED_ERROR;
                $errors = array(
                    MESSAGE_NETWORK_DESACTIVATED
                );

                $mensagem = array(
                    "status" => 0,
                    "message" => $message,
                    "errors" => $errors
                );

                return array("mensagem" => $mensagem);
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
            // @todo: ajustar nfe inexistente
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

            // @todo Resolver problema importação MG
            $retorno = $this->processaConteudoSefaz($cliente, $funcionario, $usuario, $gotas, $url, $chaveNfe, $estado, $webContent["response"]);

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
                        "quantidade_multiplicador" => floor($pontuacaoItem["quantidade_multiplicador"]),
                        "quantidade_gotas" => floor($pontuacaoItem["quantidade_gotas"]),
                        "data" => $dataProcessamento
                    );

                    $pontuacoesSave[] = $pontuacao;
                }

                $pontuacoesSave = $this->Pontuacoes->insertPontuacoesCupons($pontuacoesSave);

                if ($pontuacoesSave) {
                    // Vincula o usuário que está obtendo gotas ao posto de atendimento se ele já não estiver vinculado
                    $this->ClientesHasUsuarios->saveClienteHasUsuario($cliente["id"], $usuario["id"], true);
                }

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
                $comprovanteResumo["soma_pontuacoes"] = floor($pontuacaoComprovante["soma_pontuacoes"]);

                foreach ($pontuacaoComprovante["pontuacoes"] as $pontuacao) {

                    $item = array(
                        "nome_gota" => $pontuacao["gota"]["nome_parametro"],
                        "quantidade_gotas" => floor($pontuacao["quantidade_gotas"]),
                        "quantidade_multiplicador" => floor($pontuacao["quantidade_multiplicador"])
                    );
                    $comprovanteResumo["pontuacoes"][] = $item;
                }

                $resumo = array(
                    "funcionario" => $funcionarioOperacao,
                    "unidade_atendimento" => $unidadeAtendimento,
                    "comprovante_resumo" => $comprovanteResumo
                );

                $mensagem = $retorno["mensagem"];
                $resumo = $resumo;
                $arraySet = array("mensagem", "pontuacoes_comprovantes", "resumo");

                if ($processamentoPendente) {
                    $pontuacaoPendente = $this->PontuacoesPendentes->findPontuacaoPendenteAwaitingProcessing($chaveNfe, $cliente["estado"]);
                    $this->PontuacoesPendentes->setPontuacaoPendenteProcessed($pontuacaoPendente["id"], $pontuacaoComprovanteId);
                }

                Log::write("info", array("mensagem" => $mensagem, "pontuacoes_comprovantes" => $pontuacaoComprovante, "resumo" => $resumo));
                return array(
                    "mensagem" => $mensagem,
                    "pontuacoes_comprovantes" => $pontuacaoComprovante,
                    "resumo" => $resumo
                );
            } else {
                // Retorna o que veio de erro

                $arraySet = array_keys($retorno);

                foreach ($arraySet as $value) {
                    $$value = $retorno[$value];
                }

                // $this->set(compact($arraySet));
                // $this->set("_serialize", $arraySet);

                return $arraySet;
            }
        } elseif (!$processamentoPendente) {
            // Trata pontuação para ser processada posteriormente (se já não armazenada)

            // Status está anormal, grava para posterior processamento
            $clientesId = empty($cliente) ? null : $cliente["id"];

            // @todo: quando a pontuação pendente for processada, o usuário deve ser adicionado ao vínculo com aquele Ponto de Atendimento

            $pontuacaoPendente = $this
                ->PontuacoesPendentes
                ->createPontuacaoPendenteAwaitingProcessing(
                    $clientesId,
                    $usuario->id,
                    $funcionario->id,
                    $url,
                    $chaveNfe,
                    $estado
                );

            $errors = array(
                Configure::read("messageNotPossibleToImportCouponAwaitingProcessing")
            );
            $data = array();
            $mensagem = array(
                "status" => 0,
                "message" => Configure::read("messageNotPossibleToImportCoupon"),
                "errors" => $errors
            );
            $resumo = null;

            return array(
                "mensagem" => $mensagem,
                "pontuacao_pendente" => $pontuacaoPendente,
                "resumo" => $resumo
            );
        }

        // @todo: remover ao finalizar
        // $webContent = $this->webTools->getPageContent("http://localhost:8080/gasolinacomum.1.html");

        $mensagem = array("status" => $success, "message" => $message, "errors" => $errors);

        $arraySet = [
            'mensagem',
            'pontuacoes_comprovantes',
            "resumo"
        ];

        return array(
            'mensagem' => $mensagem,
            'pontuacoes_comprovantes' => $pontuacoesComprovantes,
            'resumo' => $resumo
        );
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

        if (empty($url) || strlen($url) == 0) {
            $errorMessage = __("O QR Code informado não está gerado conforme os padrões pré- estabelecidos da SEFAZ, não sendo possível realizar sua importação!");
            $status = 0;
            $errors = array("QR Code não informado!");

            $result = array(
                "status" => $status,
                "message" => $errorMessage,
                "errors" => $errors,
                "data" => array()
            );

            // Retorna Array contendo erros de validações
            return $result;
        }

        $arrayConsistency = array();

        // Tratamento de url para assegurar que é HTTPS
        $isHttps = stripos($url, "https://");

        if ($isHttps === false) {
            $url = str_replace("http://", "https://", $url);
        }

        // Obtem estado da URL.

        // Se estado = MG, o modelo é outro...

        if (strpos($url, "fazenda.mg") !== false) {
            $qrCodeProcura = "xhtml?p=";
            $posInicioChave = strpos($url, $qrCodeProcura) + strlen($qrCodeProcura);

            $qrCodeConteudo = substr($url, $posInicioChave);
            $qrCodeArray = explode("|", $qrCodeConteudo);

            $keysQrCodeMG = array("chNFe", "nVersao", "tpAmb", "csc", "cHashQRCode");

            $indexQrCodeArray = 0;
            $qrCodeArrayRetorno = array();
            foreach ($keysQrCodeMG as $chave) {
                if (!empty($qrCodeArray[$indexQrCodeArray])) {
                    $qrCodeArrayRetorno[] = array(
                        "key" => $chave,
                        "content" => $qrCodeArray[$indexQrCodeArray]
                    );
                }
                $indexQrCodeArray++;
            }

            $estado = "MG";

            $status = 1;
            $errorMessage = null;
            $errors = array();

            $result = array(
                "status" => $status,
                "message" => $errorMessage,
                "errors" => $errors,
                "data" => $qrCodeArrayRetorno,
                "estado" => $estado
            );

            // Retorna Array contendo erros de validações
            return $result;
        }

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

        $result = array(
            "status" => $status,
            "message" => $errorMessage,
            "errors" => $errors,
            "data" => $arrayResult,
            "estado" => $estado
        );

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
            $pontuacaoComprovante = $this->PontuacoesComprovantes->findCouponByKey(
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
     * PontuacoesComprovantesController::processaConteudoSefaz
     *
     * // @todo Ajustar
     * Processa o conteúdo que chegou do cURL e tranforma em array
     *
     * @param Cliente $cliente
     * @param Usuario $funcionario
     * @param Usuario $usuario
     * @param array $gotas
     * @param string $url
     * @param string $chave
     * @param string $conteudo
     *
     * @return array
     */
    private function processaConteudoSefaz(Cliente $cliente, Usuario $funcionario, Usuario $usuario, array $gotas, string $url, string $chave, string $estado, string $conteudo)
    {
        $dataProcessamento = date("Y-m-d H:i:s");
        $isXML = StringUtil::validarConteudoXML($conteudo);

        if (Configure::read("debug")) {
            Log::write("debug", $conteudo);
        }

        if ($isXML) {
            $xml = SefazUtil::obtemDadosXMLCupomSefaz($conteudo);

            // Obtem todos os dados de pontuações e comprovantes
            // Irá mudar se os outros estados tratam o XML de forma diferente. Deve ser analizado
            $retorno = $this->processaDadosCupomXMLSefaz($cliente["cnpj"], $estado, $url, $chave, $xml, $gotas);

            return $retorno;
        } else {
            // É HTML

            // Obtem todos os dados de pontuações
            $pontuacoesHtml = SefazUtil::obtemDadosHTMLCupomSefaz($conteudo, $gotas, $estado);

            // Prepara dados de cupom para gravar
            // Só gera o comprovante se tiver alguma pontuação
            if (sizeof($pontuacoesHtml) == 0) {
                $mensagem = array(
                    "status" => 0,
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
                    "status" => 1,
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
     * PontuacoesComprovantesController::processaDadosCupomXMLSefaz
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
    private function processaDadosCupomXMLSefaz(string $clienteCNPJ, string $estado, string $url, string $chave, array $xmlData, array $gotas)
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

        $produtosListaXml = empty($produtosListaXml[0]) ? array($produtosListaXml) : $produtosListaXml;

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
                "status" => 1,
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
