<?php

namespace App\Controller;

use \DateTime;
use App\Controller\AppController;
use App\Custom\RTI\DateTimeUtil;
use App\Custom\RTI\DebugUtil;
use App\Custom\RTI\Entity\Mensagem;
use App\Custom\RTI\NumberUtil;
use App\Custom\RTI\ImageUtil;
use App\Custom\RTI\QRCodeUtil;
use App\Custom\RTI\ResponseUtil;
use App\Custom\RTI\SefazUtil;
use App\Custom\RTI\StringUtil;
use App\Custom\RTI\WebTools;
use App\Model\Entity\Cliente;
use App\Model\Entity\Usuario;
use App\Model\Entity\Gota;
use App\Model\Entity\Pontuacao;
use App\Model\Entity\PontuacoesComprovante;
use App\Model\Entity\PontuacoesPendente;
use App\View\Helper\AddressHelper;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Collection\Collection;
use Cake\Core\Configure;
use Cake\Database\Expression\QueryExpression;
use Cake\Event\Event;
use Cake\Http\Client\Request;
use Cake\Log\Log;
use Cake\Mailer\Email;
use Cake\Routing\Router;
use Cake\View\Helper\UrlHelper;
use Exception;
use stdClass;

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
                        'action' => 'detalhesCupom', (int) $id
                    ]
                );
            }
        } catch (\Exception $e) {
            $stringError = __("[%s]: %s ", MESSAGE_GENERIC_EXCEPTION, $e->getMessage());

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
                        'action' => 'detalhesCupom', (int) $id
                    ]
                );
            }
        } catch (\Exception $e) {
            $message_error = sprintf("[%s]: %s ", MESSAGE_GENERIC_EXCEPTION, $e->getMessage());
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

                        $clientesIdsList[] = (int) $data['clientes_id'];
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

                        $funcionariosIds[] = (int) $data['funcionarios_id'];
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

                        $clientesIdsList[] = (int) $data['clientes_id'];
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

                        $usuariosIds[] = (int) $data['usuarios_id'];
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

    public function correcaoGotas()
    {
        $sessao = $this->getSessionUserVariables();
        $usuario = $sessao["usuarioLogado"];

        if ($usuario->tipo_perfil > PROFILE_TYPE_MANAGER) {
            $this->Flash->error(USER_NOT_ALLOWED_TO_EXECUTE_FUNCTION);
            return $this->redirect("/");
        }
    }

    public function lancamentoManual()
    {
        $sessao = $this->getSessionUserVariables();
        $usuario = $sessao["usuarioLogado"];

        if ($usuario->tipo_perfil >= PROFILE_TYPE_MANAGER) {
            $this->Flash->error(USER_NOT_ALLOWED_TO_EXECUTE_FUNCTION);
            return $this->redirect("/");
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
                $redesId = isset($data["redes_id"]) && strlen($data["redes_id"]) > 0 ? (int) $data["redes_id"] : null;
                $clientesId = isset($data["clientes_id"]) && strlen($data["clientes_id"]) > 0 ? (int) $data["clientes_id"] : null;
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
     * Define pontuações de usuário
     *
     * Este método é utilizado corrigir pontos do usuário de forma manual
     *
     * src/Controller/PontuacoesComprovantesController.php::setGotasManualUsuarioAPI
     *
     * @return void
     */
    public function setGotasManualUsuarioAPI()
    {
        $sessaoUsuario = $this->getSessionUserVariables();
        $usuarioLogado = $sessaoUsuario["usuarioLogado"];
        $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"];
        $errors = [];
        $errorCodes = [];

        if ($usuarioAdministrar) {
            $usuarioLogado = $usuarioAdministrar;
        }

        try {
            // Verifica se o usuário tem permissão, se não tiver, já retorna erro
            if ($usuarioLogado->tipo_perfil > PROFILE_TYPE_MANAGER) {
                throw new Exception(USER_NOT_ALLOWED_TO_EXECUTE_FUNCTION, USER_NOT_ALLOWED_TO_EXECUTE_FUNCTION_CODE);
            }
        } catch (\Throwable $th) {
            $errors[] = $th->getMessage();
            $errorCodes[] = $th->getCode();
            $message = sprintf("[%s] %s: %s", MESSAGE_SAVED_EXCEPTION, $th->getCode(), $th->getMessage());
            Log::write("error", $message);

            return ResponseUtil::errorAPI(MESSAGE_SAVED_EXCEPTION, $errors, [], $errorCodes);
        }

        if ($this->request->is(Request::METHOD_POST)) {
            $data = $this->request->getData();

            Log::write("info", sprintf("Info de %s: %s - %s: %s", Request::METHOD_POST, __CLASS__, __METHOD__, print_r($data, true)));

            // Variáveis
            $redesId = !empty($data["redes_id"]) ? $data["redes_id"] : null;
            $usuariosId = !empty($data["usuarios_id"]) ? (int) $data["usuarios_id"] : null;
            $quantidadeGotas = !empty($data["quantidade_gotas"]) ? $data["quantidade_gotas"] : null;

            $dataProcessamento = new \DateTime('now');
            $dataProcessamento = $dataProcessamento->format("Y-m-d H:i:s");

            // Validação de conteudo

            try {
                $errors = [];
                $errorCodes = [];

                if (empty($usuariosId)) {
                    $errors[] = MSG_USUARIOS_ID_EMPTY;
                    $errorCodes[] = MSG_USUARIOS_ID_EMPTY_CODE;
                }

                if (empty($quantidadeGotas)) {
                    // @todo
                    $errors[] = MSG_QUANTIDADE_GOTAS_EMPTY;
                    $errorCodes[] = MSG_QUANTIDADE_GOTAS_EMPTY_CODE;
                }
            } catch (\Throwable $th) {
                $count = count($errors);

                for ($i = 0; $i < $count; $i++) {
                    $error = $errors[$i];
                    $errorCode = $errorCodes[$i];
                    $message = sprintf("[%s] %s: %s", MESSAGE_GENERIC_EXCEPTION, $errorCode, $error);
                    Log::write("error", $message);
                }

                return ResponseUtil::errorAPI(MESSAGE_GENERIC_EXCEPTION, $errors, [], $errorCodes);
            }

            $cliente = null;
            $usuario = null;

            try {
                $redeHasCliente = $this->RedesHasClientes->findMatrizOfRedesByRedesId($redesId);

                if (empty($redeHasCliente) && empty($redeHasCliente->cliente)) {
                    throw new Exception(MSG_CLIENTES_MATRIZ_NOT_FOUND, MSG_CLIENTES_MATRIZ_NOT_FOUND_CODE);
                }

                $cliente = $redeHasCliente->cliente;
                $usuario = $this->Usuarios->get($usuariosId);
            } catch (\Throwable $th) {
                $message = $th->getMessage();
                $code = $th->getCode();
                Log::write("error", sprintf("[%s] %s: %s", MSG_LOAD_EXCEPTION, $code, $message));

                return ResponseUtil::errorAPI(MESSAGE_GENERIC_EXCEPTION, [$message], [], [$code]);
            }

            $funcionariosId = $usuarioLogado->id;

            try {
                $comprovanteSave = new PontuacoesComprovante();
                $comprovanteSave->clientes_id = $cliente->id;
                $comprovanteSave->usuarios_id = $usuario->id;
                $comprovanteSave->funcionarios_id = $funcionariosId;
                $comprovanteSave->conteudo = GOTAS_ADJUSTMENT_POINTS;
                $comprovanteSave->chave_nfe = GOTAS_ADJUSTMENT_POINTS;
                $comprovanteSave->estado_nfe = $cliente->estado;
                $comprovanteSave->requer_auditoria = false;
                $comprovanteSave->auditado = false;
                $comprovanteSave->data = $dataProcessamento;
                $comprovanteSave->registro_invalido = false;

                $comprovante = $this->PontuacoesComprovantes->saveUpdate($comprovanteSave);

                if ($comprovante) {
                    // Obtem a gota de definição automática para reajuste
                    $gota = $this->Gotas->getGotas($cliente->id, GOTAS_ADJUSTMENT_POINTS, null, null, null, 1);
                    $gota = $gota->first();
                    $pontuacao = new Pontuacao();
                    $pontuacao->clientes_id = $cliente->id;
                    $pontuacao->usuarios_id = $usuario->id;
                    $pontuacao->funcionarios_id = $funcionariosId;
                    $pontuacao->data = $dataProcessamento;
                    $pontuacao->gotas_id = $gota->id;
                    $pontuacao->quantidade_multiplicador = 1;
                    $pontuacao->quantidade_gotas = floor($quantidadeGotas);
                    $pontuacao->pontuacoes_comprovante_id = $comprovante->id;
                    $pontuacao->valor_gota_sefaz = 0;
                    $pontuacao->expirado = 0;
                    $pontuacao->utilizado = 0;

                    $this->Pontuacoes->saveUpdate($pontuacao);
                }

                return ResponseUtil::successAPI(MESSAGE_SAVED_SUCCESS);
            } catch (\Throwable $th) {
                $message = sprintf("[%s] %s: %s", MESSAGE_SAVED_EXCEPTION, $th->getCode(), $th->getMessage());
                Log::write("error", $message);

                return ResponseUtil::errorAPI(MESSAGE_SAVED_EXCEPTION, [$th->getMessage()], [], [$th->getCode()]);
            }
        }
    }

    /**
     * Define pontuações de usuário
     *
     * Este método é utilizado para gravar as informações de pontos do usuário de forma manual,
     * quando se deseja pontuar o usuário sem a intervenção da SEFAZ.
     *
     * src/Controller/PontuacoesComprovantesController.php::setComprovanteFiscalUsuarioManualAPI
     *
     * @return void
     */
    public function setComprovanteFiscalUsuarioManualAPI()
    {
        $sessaoUsuario = $this->getSessionUserVariables();
        $usuarioLogado = $sessaoUsuario["usuarioLogado"];
        $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"];
        $errors = [];
        $errorCodes = [];

        if ($usuarioAdministrar) {
            $usuarioLogado = $usuarioAdministrar;
        }

        try {
            // Verifica se o usuário tem permissão, se não tiver, já retorna erro
            if ($usuarioLogado->tipo_perfil > PROFILE_TYPE_ADMIN_LOCAL) {
                throw new Exception(USER_NOT_ALLOWED_TO_EXECUTE_FUNCTION, USER_NOT_ALLOWED_TO_EXECUTE_FUNCTION_CODE);
            }
        } catch (\Throwable $th) {
            $errors[] = $th->getMessage();
            $errorCodes[] = $th->getCode();
            $message = sprintf("[%s] %s: %s", MESSAGE_SAVED_EXCEPTION, $th->getCode(), $th->getMessage());
            Log::write("error", $message);

            return ResponseUtil::errorAPI(MESSAGE_SAVED_EXCEPTION, $errors, [], $errorCodes);
        }

        if ($this->request->is(Request::METHOD_POST)) {
            $data = $this->request->getData();

            Log::write("info", sprintf("Info de %s: %s - %s: %s", Request::METHOD_GET, __CLASS__, __METHOD__, print_r($data, true)));

            // Variáveis
            $clientesId = !empty($data["clientes_id"]) ? (int) $data["clientes_id"] : null;
            $usuariosId = !empty($data["usuarios_id"]) ? (int) $data["usuarios_id"] : null;
            $pontuacoes = !empty($data["pontuacoes"]) ? $data["pontuacoes"] : null;
            $qrCode = !empty($data["qr_code"]) ? $data["qr_code"] : null;

            $dataProcessamento = new \DateTime('now');
            $dataProcessamento = $dataProcessamento->format("Y-m-d H:i:s");

            // Validação de conteudo

            try {

                $errors = [];
                $errorCodes = [];

                if (empty($clientesId)) {
                    $errors[]  = MSG_CLIENTES_ID_NOT_EMPTY;
                    $errorCodes[] = MSG_CLIENTES_ID_NOT_EMPTY_CODE;
                }

                if (empty($usuariosId)) {
                    $errors[] = MSG_USUARIOS_ID_EMPTY;
                    $errorCodes[] = MSG_USUARIOS_ID_EMPTY_CODE;
                }

                if (empty($pontuacoes) || count($pontuacoes) == 0) {
                    // @todo Continuar se este método de inserção voltar a ser útil
                    $errors[] = "";
                    $errorCodes[] = "";
                }
            } catch (\Throwable $th) {
                $count = count($errors);

                for ($i = 0; $i < $count; $i++) {
                    $error = $errors[$i];
                    $errorCode = $errorCodes[$i];
                    $message = sprintf("[%s] %s: %s", MESSAGE_SAVED_EXCEPTION, $errorCode, $error);
                    Log::write("error", $message);
                }

                return ResponseUtil::errorAPI(MESSAGE_SAVED_EXCEPTION, $errors, [], $errorCodes);
            }

            // Se vazio ou não é um link, o titulo será definido automaticamente
            if (empty($qrCode) || !filter_var($qrCode, FILTER_VALIDATE_URL)) {
                $qrCode = sprintf("Importação manual em %s.", $dataProcessamento);
            }

            $cliente = null;
            $usuario = null;

            try {
                $cliente = $this->Clientes->get($clientesId);
                $usuario = $this->Usuarios->get($usuariosId);
            } catch (\Throwable $th) {
                $message = sprintf("[%s] %s: %s", MESSAGE_SAVED_EXCEPTION, $th->getCode(), $th->getMessage());
                Log::write("error", $message);

                return ResponseUtil::errorAPI(MESSAGE_SAVED_EXCEPTION, [$th->getMessage()], [], [$th->getCode()]);
            }

            $funcionariosId = $usuarioLogado->id;

            try {
                $comprovanteSave = new PontuacoesComprovante();
                $comprovanteSave->clientes_id = $cliente->id;
                $comprovanteSave->usuarios_id = $usuario->id;
                $comprovanteSave->funcionarios_id = $funcionariosId;
                $comprovanteSave->conteudo = $qrCode;
                $chaveNfe = $qrCode;

                if (filter_var($qrCode, FILTER_VALIDATE_URL)) {
                    $result = QRCodeUtil::validarUrlQrCode($qrCode);

                    if ($result["status"]) {
                        $var = array_filter($result["data"], function ($a) {
                            if ($a["key"] == "chNFe") {
                                return $a;
                            }
                        });

                        $chaveNfe = $var[0]["content"];
                    }
                }

                $comprovanteSave->chave_nfe = $chaveNfe;
                $comprovanteSave->estado_nfe = $cliente->estado;
                $comprovanteSave->requer_auditoria = false;
                $comprovanteSave->auditado = false;
                $comprovanteSave->data = $dataProcessamento;
                $comprovanteSave->registro_invalido = false;

                // @todo Fazer método saveUpdate
                $comprovante = $this->PontuacoesComprovantes->saveUpdate($comprovanteSave);

                foreach ($pontuacoes as $pontuacaoItem) {
                    $gota = $this->Gotas->get($pontuacaoItem["gotas_id"]);
                    $pontuacao = new Pontuacao();
                    $pontuacao->clientes_id = $cliente->id;
                    $pontuacao->usuarios_id = $usuario->id;
                    $pontuacao->funcionarios_id = $funcionariosId;
                    $pontuacao->data = $dataProcessamento;
                    $pontuacao->gotas_id = $gota->id;
                    $pontuacao->quantidade_multiplicador = $pontuacaoItem["quantidade_multiplicador"];
                    $pontuacao->quantidade_gotas = floor($pontuacaoItem["quantidade_multiplicador"] * $gota->multiplicador_gota);
                    $pontuacao->pontuacoes_comprovante_id = $comprovante->id;
                    $pontuacao->valor_gota_sefaz = $pontuacaoItem["valor"];
                    $pontuacao->expirado = 0;
                    $pontuacao->utilizado = 0;

                    $this->Pontuacoes->saveUpdate($pontuacao);
                }

                return ResponseUtil::successAPI(MESSAGE_SAVED_SUCCESS);
            } catch (\Throwable $th) {
                $message = sprintf("[%s] %s: %s", MESSAGE_SAVED_EXCEPTION, $th->getCode(), $th->getMessage());
                Log::write("error", $message);

                return ResponseUtil::errorAPI(MESSAGE_SAVED_EXCEPTION, [$th->getMessage()], [], [$th->getCode()]);
            }
        }
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

                Log::write("info", sprintf("Info de %s: %s - %s: %s", Request::METHOD_POST, __CLASS__, __METHOD__, print_r($data, true)));

                $retorno = $this->processaCupom($data);

                if ($retorno->mensagem->status) {
                    $arrayData = array(
                        "pontuacoes_comprovantes" => $retorno->pontuacoes_comprovantes,
                        "resumo" => $retorno->resumo
                    );

                    return ResponseUtil::successAPI($retorno->mensagem->message, $arrayData);
                } else {
                    $arrayData = array();

                    foreach ($retorno as $key => $value) {
                        if ($key != "mensagem") {
                            $arrayData[$key] = $value;
                        }
                    }

                    Log::write("info", $retorno);
                    return ResponseUtil::errorAPI($retorno->mensagem->message, $retorno->mensagem->errors, $arrayData, $retorno->mensagem->error_codes);
                }
            }

            Log::write('info', 'Finalizado processamento de cupom...');
        } catch (\Throwable $th) {
            $code = $th->getCode();
            $message = $th->getMessage();
            $messageLog = sprintf("%s", $th->getMessage(), $code);
            Log::write("error", $messageLog);

            return ResponseUtil::errorAPI(MESSAGE_GENERIC_EXCEPTION, [$message], [], [$code]);
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
                $usuariosId = !empty($data["usuarios_id"]) ? (int) $data["usuarios_id"] : null;
                $qrCode = !empty($data["qr_code"]) ? $data["qr_code"] : null;
                $sessaoUsuario = $this->getSessionUserVariables();
                $usuarioLogado = $sessaoUsuario["usuarioLogado"];
                $cliente = $sessaoUsuario["cliente"];
                $funcionariosId = $usuarioLogado->id;
                $clientesId = $cliente->id;

                $errors = array();

                if (empty($usuariosId)) {
                    $errors[] = MSG_USUARIOS_ID_EMPTY;
                }

                if (empty($qrCode)) {
                    $errors[] = MSG_QR_CODE_EMPTY;
                }

                if (count($errors) > 0) {
                    $mensagem = new Mensagem();

                    $mensagem->status = 0;
                    $mensagem->message = MESSAGE_OPERATION_FAILURE_DURING_PROCESSING;
                    $mensagem->errors = $errors;

                    return ResponseUtil::errorAPI($mensagem->message, $mensagem->errors);
                }

                $data = array(
                    "usuarios_id" => $usuariosId,
                    "qr_code" => $qrCode,
                    "funcionarios_id" => $funcionariosId,
                    "clientes_id" => $clientesId
                );

                $retorno = $this->processaCupom($data);

                $mensagem = $retorno->mensagem;

                $responseData = array();
                foreach ($retorno as $key => $value) {
                    if ($key != "mensagem") {
                        $responseData[$key] = $value;
                    }
                }

                if ($mensagem->status) {
                    Log::write("info", "Finalizando Processamento de cupom...");
                    return ResponseUtil::successAPI($mensagem->message, $responseData);
                } else {
                    Log::write("error", "Erro ao realizar processamento de cupom...");

                    foreach ($mensagem->errors as $error) {
                        Log::write("error", $error);
                    }

                    return ResponseUtil::errorAPI($mensagem->message, $mensagem->errors, $responseData);
                }
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $code = $e->getCode();

            $messageLog = sprintf("%s", $message, $code);
            Log::write("error", sprintf("%s: %s", MESSAGE_GENERIC_EXCEPTION, $messageLog));
            $errors = [$message];

            $errorCodes = [$code];
            return ResponseUtil::errorAPI(MESSAGE_GENERIC_EXCEPTION, $errors, [], $errorCodes);
        }
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
            $errorCodes = [];
            $sessao = $this->getSessionUserVariables();

            Log::write("info", sprintf("Info de %s: %s - %s: %s", Request::METHOD_POST, __CLASS__, __METHOD__, print_r($data, true)));

            // Informações do POST
            $cnpj = !empty($data["cnpj"]) ? preg_replace("/\D/", "", $data["cnpj"]) : null;
            $cpf = !empty($data["cpf"]) ? preg_replace("/\D/", "", $data["cpf"]) : null;
            $gotasAbastecidasClienteFinal = !empty($data["gotas_abastecidas"]) ? $data["gotas_abastecidas"] : array();
            $qrCode = !empty($data["qr_code"]) ? $data["qr_code"] : null;
            $funcionario = $sessao["usuarioLogado"];

            // Validação
            if (empty($cnpj)) {
                $errors[] = MESSAGE_CNPJ_EMPTY;
                $errorCodes[] = 0;
            }

            if (empty($cpf)) {
                $errors[] = MSG_USUARIOS_CPF_EMPTY;
                $errorCodes[] = 0;
            }

            if (empty($qrCode)) {
                $errors[] = MSG_QRCODE_EMPTY;
                $errorCodes[] = MSG_QRCODE_EMPTY_CODE;
            }

            if (empty($gotasAbastecidasClienteFinal) && count($gotasAbastecidasClienteFinal) == 0) {
                $errors[] = "Itens da Venda não foram informados!";
                $errorCodes[] = 0;
            }

            if (sizeof($errors) > 0) {
                for ($i = 0; $i < count($errors); $i++) {
                    Log::error(sprintf("[%s] %s: %s", MESSAGE_GENERIC_EXCEPTION, $errorCodes[$i], $errors[$i]));
                }
                return ResponseUtil::errorAPI(MESSAGE_OPERATION_FAILURE_DURING_PROCESSING, $errors, $data, $errorCodes);
            }

            // Validação CNPJ e CPF
            $validacaoCNPJ = NumberUtil::validarCNPJ($cnpj);
            $validacaoCPF = NumberUtil::validarCPF($cpf);

            if ($validacaoCNPJ == 0) {
                $errors[] = $validacaoCNPJ["message"];
                $errorCodes[] = 0;
            }

            if ($validacaoCPF["status"] == 0) {
                $errors[] = $validacaoCPF["message"];
                $errorCodes[] = 0;
            }

            if (sizeof($errors) > 0) {
                for ($i = 0; $i < count($errors); $i++) {
                    Log::error(sprintf("[%s] %s: %s", MESSAGE_OPERATION_FAILURE_DURING_PROCESSING, $errorCodes[$i], $errors[$i]));
                }
                return ResponseUtil::errorAPI(MESSAGE_OPERATION_FAILURE_DURING_PROCESSING, $errors, $data);
            }

            // Cliente do posto
            $usuario = $this->Usuarios->getUsuarioByCPF($cpf);

            // Posto de atendimento
            $cliente = $this->Clientes->getClienteByCNPJ($cnpj);

            if (empty($cliente)) {
                $errors[] = sprintf("%s %s", MESSAGE_CNPJ_NOT_REGISTERED_ON_SYSTEM, MESSAGE_CNPJ_EMPTY);
                $errorCodes[] = 0;
            }

            $chave = "";

            if (strtoupper($cliente["estado"] == "MG")) {
                if (filter_var($qrCode, FILTER_VALIDATE_URL)) {
                    $qrCodeResult = QRCodeUtil::validarUrlQrCode($qrCode);

                    $qrcodeValue = array_filter($qrCodeResult["data"], function ($a) {
                        return $a["key"] == "chNFe";
                    });

                    $qrcodeValue = $qrcodeValue[0];
                    $chave = $qrcodeValue["content"];
                } else {
                    $errors[] = MSG_QR_CODE_SEFAZ_MISMATCH_PATTERN;
                    $errorCodes[] = MSG_QR_CODE_SEFAZ_MISMATCH_PATTERN_CODE;
                    // $chave = substr($qrCode, strpos($qrCode, "chNFe=") + strlen("chNFe="), 44);
                    // $chave = $qrCode;
                }
            } else {
                if (empty($qrCode)) {
                    $errors[] = MSG_QRCODE_EMPTY;
                    $errorCodes[] = MSG_QRCODE_EMPTY_CODE;
                } elseif (strpos($qrCode, "sefaz.") == 0) {
                    $errors[] = MSG_QRCODE_MISMATCH_FORMAT;
                    $errorCodes[] = 0;
                }

                if (sizeof($errors) > 0) {
                    for ($i = 0; $i < count($errors); $i++) {
                        Log::error(sprintf("[%s] %s: %s", MESSAGE_OPERATION_FAILURE_DURING_PROCESSING, $errorCodes[$i], $errors[$i]));
                    }

                    return ResponseUtil::errorAPI(MESSAGE_OPERATION_FAILURE_DURING_PROCESSING, $errors, $data);
                } else {
                    $chave = substr($qrCode, strpos($qrCode, "chNFe=") + strlen("chNFe="), 44);
                }
            }

            // Faz a pesquisa do QR Code no sistema, se tiver um registro ignora
            $cupomPreviamenteImportado = $this->verificarCupomPreviamenteImportado($chave, $cliente->estado);

            // Cupom previamente importado, interrompe processamento e avisa usuário
            if (!$cupomPreviamenteImportado["status"]) {
                $errors[] = $cupomPreviamenteImportado["errors"][0];
                $errorCodes[] = $cupomPreviamenteImportado["error_codes"][0];

                Log::write("info", sprintf("Cupom previamente importado! QR Code: {%s}.", $qrCode));
            }

            if (sizeof($errors) > 0) {
                for ($i = 0; $i < count($errors); $i++) {
                    Log::error(sprintf("[%s] %s: %s", MESSAGE_OPERATION_FAILURE_DURING_PROCESSING, $errorCodes[$i], $errors[$i]));
                }

                Log::write("info", sprintf("Cupom: {%s}, Usuário: {%s}, Estabelecimento: {%s}", $qrCode, $usuario->id, $cliente->id));

                return ResponseUtil::errorAPI(MESSAGE_OPERATION_FAILURE_DURING_PROCESSING, $errors, $data, $errorCodes);
            }

            // Fim de Validação

            if (strlen($cpf) > 11) {
                Log::write("info", "CNPJ Identificado: " . $cpf);
            }

            // Se usuário não encontrado, cadastra para futuro acesso
            if (empty($usuario)) {
                $usuario = $this->Usuarios->addUsuarioAguardandoAtivacao($cpf);
            }

            // Se usuário cadastrado, vincula ele ao ponto de atendimento (cliente)
            if ($usuario) {
                // @todo se já tiver registro, não faz nada
                $this->ClientesHasUsuarios->saveClienteHasUsuario($cliente["id"], $usuario["id"], 0);
            }

            if (empty($funcionario)) {
                // @todo isto deverá vir antes do saveClientesHasUsuario
                $funcionario = $this->Usuarios->findUsuariosByType(PROFILE_TYPE_DUMMY_WORKER)->first();
            }

            $gotasCliente = $this->Gotas->findGotasEnabledByClientesId($cliente["id"]);
            $pontuacoes = array();
            $data = date("Y-m-d H:i:s");
            $gotasAtualizarPreco = array();
            $gotasCliente = $gotasCliente->toArray();

            #region Pesquisa por todos os produtos extras para gravar se a configuração da rede está definida

            // @TODO conferir!
            $pontuacaoExtra = $this->processaProdutosExtras($cliente, $usuario, $funcionario, $gotasCliente, $gotasAbastecidasClienteFinal, TRANSMISSION_MODE_DIRECT);

            if (!empty($pontuacaoExtra)) {
                $pontuacoes[] = $pontuacaoExtra;
            }

            // $gotasPontosExtras = new Gota();

            // // return ResponseUtil::successAPI('', ['data' => $cliente]);
            // $quantidadeExtra = 0;
            // $pontosExtras = 0;
            // $isPontuacaoExtraProdutoGenerico = $cliente->redes_has_cliente->rede->pontuacao_extra_produto_generico;

            // // Se a rede está com a pontuação extra habilitada, atribui
            // if ($isPontuacaoExtraProdutoGenerico) {
            //     foreach ($gotasAbastecidasClienteFinal as $gotaUsuario) {
            //         $gota = array_filter($gotasCliente, function ($item) use ($gotaUsuario) {
            //             return $gotaUsuario["gotas_nome"] == $item["nome_parametro"];
            //         });

            //         $gota = array_values($gota);

            //         if (empty($gota)) {
            //             $quantidadeExtra += $gotaUsuario["gotas_qtde"];
            //             $pontosExtras += $gotaUsuario["gotas_vl_unit"];
            //         }
            //     }

            //     $gotaBonificacaoPontosExtras = $this->Gotas->getGotaClienteByName($cliente->id, GOTAS_BONUS_EXTRA_POINTS_SEFAZ);

            //     // só adiciona a bonificação se o registro existir, para não dar exception
            //     if (!empty($gotaBonificacaoPontosExtras)) {
            //         $pontuacao = new Pontuacao();
            //         $pontuacao->quantidade_multiplicador = $quantidadeExtra;
            //         $pontuacao->clientes_id = $cliente->id;
            //         $pontuacao->usuarios_id = $usuario->id;
            //         $pontuacao->funcionarios_id = $funcionario->id;
            //         $pontuacao->gotas_id = $gotaBonificacaoPontosExtras->id;
            //         $pontuacao->data = $data;
            //         $pontuacao->quantidade_gotas = floor($pontosExtras);
            //         $pontuacao->valor_gota_sefaz = $pontosExtras;

            //         // Adiciona registro para posterior processamento
            //         $pontuacoes[] = $pontuacao;
            //     }
            // }

            #endregion

            foreach ($gotasAbastecidasClienteFinal as $gotaUsuario) {

                $gota = array_filter($gotasCliente, function ($item) use ($gotaUsuario) {
                    return $gotaUsuario["gotas_nome"] == $item["nome_parametro"];
                });

                $gota = array_values($gota);

                if (!empty($gota)) {
                    $gota = $gota[0];
                    $item = new Pontuacao();
                    $item->quantidade_multiplicador = $gotaUsuario["gotas_qtde"];
                    $item->quantidade_gotas = floor($gota["multiplicador_gota"] * $gotaUsuario["gotas_qtde"]);
                    $item->valor_gota_sefaz = $gotaUsuario["gotas_vl_unit"];
                    $item->clientes_id = $cliente["id"];
                    $item->usuarios_id = $usuario["id"];
                    $item->funcionarios_id = $funcionario["id"];
                    $item->gotas_id = $gota["id"];
                    $item->data = $data;

                    // Confere quais gotas estão com preço desatualizado

                    if ($gotaUsuario["gotas_vl_unit"] != $gota["valor_atual"]) {
                        // $gotaPreco = array(
                        //     "clientes_id" => $cliente["id"],
                        //     "gotas_id" => $gota["id"],
                        //     "preco" => $gotaUsuario["gotas_vl_unit"]
                        // );

                        /**
                         * @todo gustavosg: pendente atualização de preço por envio via REST
                         * Esta informação será somente para caráter de relatório
                         */
                        // $gotasAtualizarPreco[] = $gotaPreco;
                    }

                    $pontuacoes[] = $item;
                }
            }

            if (count($pontuacoes) == 0) {
                $errors[] = sprintf(MSG_GOTAS_NOT_FOUND_IN_COUPON, $qrCode, $cliente->estado);
                $errorCodes[] = MSG_GOTAS_NOT_FOUND_IN_COUPON_CODE;

                for ($i = 0; $i < count($errors); $i++) {
                    Log::error(sprintf("[%s] %s: %s", MESSAGE_OPERATION_FAILURE_DURING_PROCESSING, $errorCodes[$i], $errors[$i]));
                }

                return ResponseUtil::errorAPI(MESSAGE_GENERIC_EXCEPTION, $errors, [], $errorCodes);
            }

            $pontuacoesComprovante = array(
                "qr_code" => $qrCode
            );

            // @todo mudar para $this->PontuacoesComprovantes->saveUpdate($obj);
            $pontuacaoComprovanteSave = $this->PontuacoesComprovantes->addPontuacaoComprovanteCupom($cliente["id"], $usuario["id"], $funcionario["id"], $qrCode, $chave, $cliente["estado"], date("Y-m-d H:i:s"), 0, 1);

            foreach ($pontuacoes as $pontuacaoItem) {
                $pontuacao = new Pontuacao();
                $pontuacao->clientes_id = $cliente->id;
                $pontuacao->usuarios_id = $usuario->id;
                $pontuacao->funcionarios_id = $funcionario->id;
                $pontuacao->gotas_id = $pontuacaoItem->gotas_id;
                $pontuacao->quantidade_multiplicador = $pontuacaoItem->quantidade_multiplicador;
                $pontuacao->quantidade_gotas = $pontuacaoItem->quantidade_gotas;
                $pontuacao->pontuacoes_comprovante_id = $pontuacaoComprovanteSave->id;
                $pontuacao->valor_gota_sefaz = $pontuacaoItem->valor_gota_sefaz;
                $pontuacao->data = new DateTime('now');
                $pontuacaoSave = $this->Pontuacoes->saveUpdate($pontuacao);
                // $pontuacaoSave = $this->Pontuacoes->addPontuacaoCupom($cliente["id"], $usuario["id"], $funcionario["id"], $pontuacao["gotas_id"], $pontuacao["multiplicador_gota"], $pontuacao["quantidade_gota"], $pontuacaoComprovanteSave["id"], $data);
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
            return ResponseUtil::successAPI(MSG_PROCESSING_COMPLETED, $retorno);
        }
    }

    /**
     * PontuacoesComprovantesController::reprocessPointsEntireNetwork
     *
     * Método para reprocessar dados de uma rede inteira.
     * Nota: Este processo ficará desabilitado após a execução para a rede HG no dia 17/01/2020
     *
     *
     * @param int $redesId Id da rede
     *
     * @return void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function reprocessPointsEntireNetwork(int $redesId)
    {
        /**
         * Como será o fluxo deste processo:
         *
         * 1 Obter todos os Cupons Fiscais da rede, com os dados de pontuação;
         * 2 Obter todos os Produtos dos postos, antes de verificar CF a CF;
         * 3 Para cada cupom da lista, obtem primeiro as pontuações envolvidas e as remove SE a obtenção ao site da
         * SEFAZ foi efetuada com sucesso;
         * 4 Grava no banco a nova pontuação;
         * 5 Após todo o processo, subtrair os pontos ja utilizados pelos usuarios
         *
         * Nota: por se tratar de um processo esporádico (provavelmente só será executado uma vez), não será obedecido
         * os principios MVC, então as consultas e execução ficarão todas aqui
         */

        $debug = true;

        // $rede = $this->Redes->get($redesId);

        /**
         * Passo 1: Obter todos os Cupons Fiscais da rede, com os dados de pontuação;
         */

        $where = function (QueryExpression $exp) use ($redesId) {
            $exp->eq("Redes.id", $redesId)
                ->gte("PontuacoesComprovantes.data", "2020-01-17 17:00:00");

            return $exp;
        };
        $selectList = [
            "PontuacoesComprovantes.id",
            "PontuacoesComprovantes.clientes_id",
            "PontuacoesComprovantes.funcionarios_id",
            "PontuacoesComprovantes.usuarios_id",
            "PontuacoesComprovantes.conteudo",
            "PontuacoesComprovantes.chave_nfe",
            "PontuacoesComprovantes.estado_nfe",
            "PontuacoesComprovantes.data",
            "Redes.id",
            "Clientes.id",
            "Clientes.nome_fantasia",
            "Clientes.estado",
            "RedesHasClientes.id",
            "RedesHasClientes.redes_id",
            "RedesHasClientes.clientes_id",
            "Funcionarios.id",
            "Usuarios.id",
            "Usuarios.nome"
        ];

        $join = [
            "Pontuacoes",
            "Clientes.RedesHasClientes.Redes",
            "Funcionarios",
            "Usuarios"
        ];

        $pontuacoesComprovantes = $this->PontuacoesComprovantes->find("all")
            ->where($where)
            ->contain($join)
            ->select($selectList)
            ->toArray();

        $clientesSelectList = [
            "Clientes.id"
        ];

        $where = function (QueryExpression $exp) use ($redesId) {
            $exp->eq("Redes.id", $redesId);

            return $exp;
        };

        $clientesIds = $this->Clientes->find("all")
            ->where($where)
            ->contain(["RedesHasClientes.Redes"])
            ->select($clientesSelectList)
            ->toArray();

        $clientesIds = array_column($clientesIds, "id");

        // Ponto 2 Obter todos os Produtos dos postos, antes de verificar CF a CF;
        $gotasPostos = [];
        foreach ($clientesIds as $clientesId) {
            $whereGotas = function (QueryExpression $exp) use ($clientesId) {
                return $exp->eq("Gotas.clientes_id", $clientesId)
                    ->eq("Gotas.habilitado", true)
                    ->eq("Gotas.tipo_cadastro", 0);
            };

            $gotasPostos[$clientesId] = $this->Gotas->find("all")->where($whereGotas)->toArray();
        }

        // $pontuacoesComprovantes = [];
        // DebugUtil::printArray($pontuacoesComprovantes);
        foreach ($pontuacoesComprovantes as $comprovante) {
            /**
             * 3 Para cada cupom da lista, obtem primeiro as pontuações envolvidas e as remove SE a obtenção ao site da
             * SEFAZ foi efetuada com sucesso;
             */

            // Obtem a lista de gotas

            $contentSefazCoupon = WebTools::getPageContent($comprovante->conteudo);
            $contentCoupon = $contentSefazCoupon["response"];
            $statusCode = $contentSefazCoupon["statusCode"];
            $gotasPosto = $gotasPostos[$comprovante->clientes_id];
            $cliente = $comprovante->cliente;
            $rede = $comprovante->cliente->redes_has_cliente->rede;
            $funcionario = $comprovante->funcionario;
            $usuario = $comprovante->usuario;

            if ($debug) {
                echo sprintf("Obtendo CF {%s} da Sefaz... <br />", $comprovante->conteudo);
                flush();
            }

            // Só faz o processamento se teve sucesso em obter os dados
            if ($statusCode == 200) {

                if ($debug) {
                    echo sprintf("Dados obtidos... <br />");
                    flush();
                }

                // Verifica se usuário estourou o limite de pontuações diarias
                $produtos = SefazUtil::obtemProdutosSefaz($contentCoupon, $comprovante->conteudo, $comprovante->chave_nfe, $cliente, $funcionario, $usuario);

                $dataProcessamento = new DateTime($comprovante->data);
                $dataProcessamento = $dataProcessamento->format("Y-m-d H:i:s");
                $pontuacoes = [];
                $somaMultiplicador = 0;
                $listProductsToCheck = [];
                $listProductsForExtraPoints = $produtos;

                // DebugUtil::printArray($gotasPosto);

                foreach ($gotasPosto as $gota) {
                    foreach ($produtos as $indexProduto => $produto) {
                        $percent = 0;
                        similar_text($gota->nome_parametro, $produto["descricao"], $percent);

                        // Se o percent for no mínimo MIN_PERCENTAGE_SIMILAR_TEXT_GOTAS, adiciona para posterior verificação
                        if ($percent >= MIN_PERCENTAGE_SIMILAR_TEXT_GOTAS) {
                            $pontuacao = new Pontuacao();
                            $pontuacao->clientes_id = $cliente->id;
                            $pontuacao->usuarios_id = $usuario->id;
                            $pontuacao->funcionarios_id = $funcionario->id;
                            $pontuacao->gotas_id = $gota->id;
                            $pontuacao->descricao =  $produto["descricao"];
                            $pontuacao->quantidade_multiplicador =  $produto["quantidade"];
                            $pontuacao->valor_gota_sefaz =  trim($produto["valor"]);
                            $pontuacao->quantidade_gotas =  floor($gota->multiplicador_gota * (float) $produto["quantidade"]);
                            $pontuacao->percent =  $percent;
                            $pontuacao->pontuacoes_comprovante_id = 0;
                            $pontuacao->data = $dataProcessamento;
                            $listProductsToCheck[$gota->id][] = $pontuacao;

                            // Remove o registro da lista de produtos à ser verificado no próximo loop
                            unset($produtos[$indexProduto]);

                            // Localiza no array o item para remoção
                            $output = array_filter($listProductsForExtraPoints, function ($item) use ($pontuacao) {
                                return $item["descricao"] === $pontuacao->descricao;
                            });

                            // Obtem os índices na lista para remoção
                            $indexes = array_keys($output);

                            // Remove os produtos que serão conferidos para pontuação extra
                            foreach ($indexes as $index) {
                                unset($listProductsForExtraPoints[$index]);
                            }
                        }
                    }
                }

                reset($listProductsForExtraPoints);

                // DebugUtil::printArray($listProductsToCheck);

                if ($rede->pontuacao_extra_produto_generico) {
                    $pontuacaoExtra = $this->processaProdutosExtras($cliente, $usuario, $funcionario, $gotasPosto, $listProductsForExtraPoints, TRANSMISSION_MODE_SEFAZ);

                    if (!empty($pontuacaoExtra)) {
                        $pontuacoes[] = $pontuacaoExtra;
                    }
                }

                foreach ($listProductsToCheck as $itemsToCheck) {
                    // Ordena todo mundo pelo maior valor de percent
                    usort($itemsToCheck, function ($itemA, $itemB) {
                        return $itemA->percent >= $itemB->percent;
                    });

                    // Irá retornar um array, obtem o primeiro que é o que tem o maior valor
                    $item = $itemsToCheck[0];
                    $pontuacoes[] = $item;
                    $somaMultiplicador += $item->quantidade_multiplicador;
                }

                if (count($pontuacoes) > 0) {
                    // 4 Grava no banco a nova pontuação;
                    // Remove os registros antigos e grava a nova pontuação

                    if ($debug) {
                        echo sprintf("Pontuações Obtidas. Removendo CF {%s} de usuário {%s}, Posto {%d} - {%s}... <br />", $comprovante->conteudo, $usuario->nome, $cliente->id, $cliente->nome_fantasia);
                        flush();
                    }

                    // Se tem pontuações, deleta as pontuações do Cupom atual, deleta o registro e insere novamente
                    foreach ($comprovante->pontuacoes as $pontuacaoDelete) {
                        $this->Pontuacoes->delete($pontuacaoDelete);
                    }

                    $this->PontuacoesComprovantes->delete($comprovante);

                    // Agora, insere uma nova
                    $pontuacoesComprovante = new PontuacoesComprovante();
                    $pontuacoesComprovante->clientes_id =  $cliente->id;
                    $pontuacoesComprovante->usuarios_id =  $usuario->id;
                    $pontuacoesComprovante->funcionarios_id =  $funcionario->id;
                    $pontuacoesComprovante->conteudo =  $comprovante->conteudo;
                    $pontuacoesComprovante->chave_nfe =  $comprovante->chave_nfe;
                    $pontuacoesComprovante->estado_nfe =  $cliente->estado;
                    $pontuacoesComprovante->data =  $dataProcessamento;
                    $pontuacoesComprovante->requer_auditoria =  0;
                    $pontuacoesComprovante->auditado =  0;

                    $pontuacoesComprovanteSave = $this->PontuacoesComprovantes->saveUpdate($pontuacoesComprovante);

                    if ($debug) {
                        echo sprintf("Inserido CF {%s} para usuário {%d} - {%s}... <br />", $pontuacoesComprovante->conteudo, $usuario->id, $usuario->nome);
                        flush();
                    }

                    $pontuacaoComprovanteId = $pontuacoesComprovanteSave->id;
                    $pontuacoesSave = [];

                    foreach ($pontuacoes as $pontuacao) {
                        $pontuacao->pontuacoes_comprovante_id =  $pontuacaoComprovanteId;
                        $pontuacoesSave[] = $pontuacao;
                    }

                    if (!empty($rede->qte_gotas_minima_bonificacao) && $rede->qte_gotas_minima_bonificacao <= $somaMultiplicador) {
                        $gotaBonificacaoSistema = $this->Gotas->getGotaBonificacaoSefaz($cliente->id);

                        // só adiciona a bonificação se o registro existir na tabela.
                        if (!empty($gotaBonificacaoSistema)) {
                            $pontuacao = $this->Pontuacoes->newEntity();
                            $pontuacao->pontuacoes_comprovante_id = $pontuacaoComprovanteId;
                            $pontuacao->clientes_id = $cliente->id;
                            $pontuacao->usuarios_id = $usuario->id;
                            $pontuacao->funcionarios_id = $funcionario->id;
                            $pontuacao->gotas_id = $gotaBonificacaoSistema->id;
                            $pontuacao->quantidade_multiplicador = 1;
                            $pontuacao->quantidade_gotas = $gotaBonificacaoSistema->multiplicador_gota;
                            $pontuacao->data = $dataProcessamento;
                            $pontuacoesSave[] = $pontuacao;
                        }
                    }

                    foreach ($pontuacoesSave as $pontuacaoSave) {
                        $pontuacoesSave = $this->Pontuacoes->saveUpdate($pontuacaoSave);
                    }
                } else {
                    echo sprintf("No Cupom Fiscal %s da SEFAZ do estado %s não há gotas à processar conforme configurações definidas!...", $comprovante->conteudo, $cliente->estado_nfe);
                    flush();
                }
            }
        }

        // 5 Após todo o processo, subtrair os pontos ja utilizados pelos usuarios

        // Obtem todos os pontos gastos dos usuários

        $where = function (QueryExpression $exp) use ($redesId) {
            return $exp->eq("Redes.id", $redesId)
                ->isNotNull("Pontuacoes.brindes_id");
        };

        $contain = ["Clientes.RedesHasClientes.Redes"];
        $selectPointsList =     [
            "Pontuacoes.id",
            "Pontuacoes.quantidade_multiplicador",
            "Pontuacoes.quantidade_gotas",
            "Pontuacoes.usuarios_id",
            "Clientes.id",
            "Clientes.nome_fantasia",
            "Redes.nome_rede"
        ];

        $pointsOutUsers = $this->Pontuacoes
            ->find("all")
            ->where($where)
            ->contain($contain)
            ->select($selectPointsList)
            ->order([
                "Pontuacoes.data" => "ASC"
            ])
            ->toArray();

        // DebugUtil::printArray($pointsOutUsers);

        foreach ($pointsOutUsers as $pointOut) {
            // Para cada Pontuação de usuário, procura a mais antiga para ir diminuindo seus pontos

            $canContinue = true;
            $pointsPendingUsageListSave = [];
            $pointsToProcess = $pointOut->quantidade_gotas;

            // Obter pontos não utilizados totalmente
            // verifica se tem algum pendente para continuar o cálculo sobre ele

            $lastId = 0;

            $pointPendingUsage = $this->Pontuacoes->getPontuacoesPendentesForUsuario(
                $pointOut->usuarios_id,
                $clientesIds,
                1,
                null
            );

            echo sprintf("Total de pontos pendentes uso: {%s}", $pointPendingUsage);

            if ($pointPendingUsage) {
                $lastId = $pointPendingUsage->id;
            }

            $pointsGiftsUsed = $this
                ->Pontuacoes
                ->getSumPontuacoesPendingToUsageByUsuario(
                    $pointOut->usuarios_id,
                    $clientesIds
                );

            echo sprintf("Total de pontos para subtrair: {%s}", $pointsGiftsUsed);

            $pointsToProcess = $pointsToProcess + $pointsGiftsUsed;
            echo sprintf("Total de pontos à serem processados: {%s}", $pointsToProcess);

            while ($canContinue) {
                $pointPendingUsage = $this->Pontuacoes->getPontuacoesPendentesForUsuario(
                    $pointOut->usuarios_id,
                    $clientesIds,
                    10,
                    $lastId
                )->toArray();

                if (empty($pointPendingUsage)) {
                    break;
                }

                if (count($pointPendingUsage) == 0) {
                    $canContinue = false;
                    break;
                }

                $maxCount = count($pointPendingUsage);

                $count = 0;
                foreach ($pointPendingUsage as $point) {
                    if (($pointsToProcess >= 0) && ($pointsToProcess >= $point->quantidade_gotas)) {
                        $pointsPendingUsageListSave[] = [
                            "id" => $point->id,
                            "utilizado" => 2
                        ];
                    } else {
                        $pointsPendingUsageListSave[] = [
                            "id" => $point->id,
                            'utilizado' => 1
                        ];
                    }
                    $pointsToProcess = $pointsToProcess - $point->quantidade_gotas;

                    if ($pointsToProcess <= 0) {
                        $canContinue = false;
                        break;
                    }
                }

                $lastId = $point->id;

                $count = $count + 1;

                if ($count = $maxCount) {
                    $lastId = $point->id + 1;
                }

                // Atualiza todos os pontos do usuário
                $this->Pontuacoes->updatePendingPontuacoesForUsuario($pointsPendingUsageListSave);

                if ($pointsToProcess <= 0) {
                    $canContinue = false;
                    break;
                }
            }
        }

        echo "Atualização de pontos concluída... <br />";
        die();
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

        Log::write("info", "url antes de sanitize: ");
        Log::write("info", $url);

        $url = filter_var($url, FILTER_SANITIZE_URL);

        Log::write("info", "url após  sanitize: ");
        Log::write("info", $url);
        $processamentoPendente = isset($data["processamento_pendente"]) ? $data["processamento_pendente"] : false;

        // Verifica se foi informado qr code. Senão já aborta
        if (is_null($url)) {
            $mensagem = new Mensagem();
            $mensagem->status = false;
            $mensagem->message = MSG_ERROR;
            $mensagem->errors = [__("Parâmetro QR CODE não foi informado!")];

            $retorno = new stdClass();
            $retorno->mensagem = $mensagem;
            return $retorno;
        }

        $validacaoQRCode = QRCodeUtil::validarUrlQrCode($url);

        // Encontrou erros de validação do QR Code. Interrompe e retorna erro ao usuário
        if ($validacaoQRCode["status"] === false) {
            $mensagem = array("status" => $validacaoQRCode["status"], "message" => $validacaoQRCode["message"], "errors" => $validacaoQRCode["errors"], "error_codes" => $validacaoQRCode["error_codes"]);

            $mensagem = new Mensagem();
            $mensagem->status = false;
            $mensagem->message = $validacaoQRCode["message"];
            $mensagem->errors = $validacaoQRCode["errors"];
            $mensagem->error_codes = $validacaoQRCode["error_codes"];

            $retorno = new stdClass();
            $retorno->mensagem = $mensagem;

            return $retorno;
        }

        $url = $validacaoQRCode["url_real"];
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

        // Cupom previamente importado, interrompe processamento e avisa usuário
        if (!$cupomPreviamenteImportado["status"] && !$processamentoPendente) {
            Log::write("info", sprintf("Cupom previamente importado! QR Code: {%s}.", $data['qr_code']));

            $mensagem = new Mensagem();
            $mensagem->status = $cupomPreviamenteImportado["status"];
            $mensagem->message = $cupomPreviamenteImportado["message"];
            $mensagem->errors = $cupomPreviamenteImportado["errors"];
            $mensagem->error_codes = [];

            $resposta = new stdClass();
            $resposta->mensagem = $mensagem;

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
        // $webContent["statusCode"] = 200;
        // $webContent["content"] = null;

        if ($webContent["statusCode"] == 200) {

            // Verifica se retorno contêm palavras chave informando que o CF não foi encontrado, e retorna exatamente esta mensagem

            $msgSefazNotFound = [
                "Não foi possível obter informações sobre a NFC-e"
            ];

            foreach ($msgSefazNotFound as $msg) {
                $searchMsg = strpos($webContent["response"], $msg) !== false;

                // Se encontrar a mensagem, retorna erro e para o processo
                if ($searchMsg) {
                    $msg = sprintf("Erro SEFAZ: %s", $msg);
                    $errors = [$msg];
                    return ResponseUtil::errorAPI(MSG_WARNING, $errors, [], []);
                }
            }

            // Caso Mobile: Cliente não é informado
            // DEBUG: Para teste sem retorno
            // $cliente = $this->Clientes->get(9);
            // $funcionario = $this->Usuarios->get(108);
            // $usuario = $this->Usuarios->get(10);
            // $gotas = $this->Gotas->findGotasByClientesId([$cliente->id])->toArray();
            // $retorno = $this->obtemProdutosSefaz($cliente, $funcionario, $usuario, $gotas, $url, $chaveNfe, $estado, "");

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
                if ($estado == "MG") {
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
                    $cnpjFormatado = NumberUtil::formatarCNPJ($cnpj);
                    // Log::write('debug', __("CNPJ {$cnpj}"));
                    // Log::write('debug', __("CNPJ {$cnpjFormatado}"));
                    $cnpjPos = strpos($webContent["response"], $cnpj) !== false;
                    $cnpjPosFormatado = strpos($webContent["response"], $cnpjFormatado) !== false;

                    if ($cnpjPos || $cnpjPosFormatado) {
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
                        "errors" => $errors,
                        "error_codes" => []
                    );

                    $arraySet = [
                        "mensagem"
                    ];

                    Log::write("info", $mensagem);
                    Log::write("info", $errors);

                    return ResponseUtil::errorAPI($mensagem["message"], $errors, [], []);
                }
            }

            // Valida se a rede está ativa
            if (!$cliente->redes_has_cliente->rede->ativado) {
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

            // Verifica se usuário estourou o limite de pontuações diarias

            $rede = $cliente->redes_has_cliente->rede;

            $clientesIds = $this->RedesHasClientes->getClientesIdsFromRedesHasClientes($rede->id);

            $qteInsercaoGotas = $this->PontuacoesComprovantes->getCountPontuacoesComprovantesOfUsuario($usuario->id, $clientesIds);

            // Se não for processamento pendente e a quantidade de pontuações do usuário é maior que o permitido pela rede
            if (($rede->quantidade_pontuacoes_usuarios_dia <= $qteInsercaoGotas) && !$processamentoPendente) {
                // usar para teste
                // if ($rede->quantidade_pontuacoes_usuarios_dia <= 1000) {

                $errorMessage = "";
                if ($rede->app_personalizado) {
                    $errorMessage = sprintf(MSG_PONTUACOES_COMPROVANTES_USUARIOS_GOTAS_MAX_REACHED, "Pontos");
                } else {
                    $errorMessage = sprintf(MSG_PONTUACOES_COMPROVANTES_USUARIOS_GOTAS_MAX_REACHED, "Gotas");
                }

                $sessao = $this->getSessionUserVariables();
                $usuarioCheck = $sessao["usuarioLogado"];

                if ($usuarioCheck->tipo_perfil == PROFILE_TYPE_USER) {
                    $errorMessage = "OPS!!! Amanhã você poderá efetuar este resgate...!";
                }

                $error = [$errorMessage];
                $errorCodes = [MSG_PONTUACOES_COMPROVANTES_USUARIOS_GOTAS_MAX_REACHED_CODE];

                Log::write("info", sprintf("Usuário {%s / %s} atingiu máximo de pontuações no dia, na Rede {%s / %s}", $usuario->id, $usuario->nome, $rede->id, $rede->nome_rede));

                return ResponseUtil::errorAPI(MESSAGE_GENERIC_COMPLETED_ERROR, $error, [], $errorCodes);
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

            // Log::write("debug", $webContent);

            // Obtem todos os dados de pontuações
            // Prepara dados de cupom para gravar
            // Só gera o comprovante se tiver alguma pontuação

            #region Verifica se cupom é antigo para posto FORMIGÃO

            // Lista de Clientes que deverá ser verificado qual cupom e qual data será válido
            $clientesIgnoreOldTickets = [
                51 => '2019-12-05'
            ];

            $clientesIdIgnorarCupom = [];

            foreach ($clientesIgnoreOldTickets as $key => $value) {
                $clientesIdIgnorarCupom[] = $key;
            }

            $stringPesquisa = $webContent["response"];
            $posPesquisa = 0;

            if (in_array($cliente->id, $clientesIdIgnorarCupom)) {
                // Localiza

                $anosComparacao = [2019];

                $found = false;
                foreach ($anosComparacao as $anoComparacao) {
                    $posPesquisa = strpos($stringPesquisa, (string) $anoComparacao);

                    if ($posPesquisa) {
                        $found = true;
                        break;
                    }
                }

                // Se encontrou a data, localiza na string a data em questão
                if ($found) {
                    $dateCheck = substr($stringPesquisa, $posPesquisa - 6, 10);
                    $dateCheck = str_replace("/", "-", $dateCheck);
                    $dateNewer = date("Y-m-d", strtotime($dateCheck));

                    if ($dateNewer < $clientesIgnoreOldTickets[$cliente->id]) {

                        $error = [
                            MSG_PONTUACOES_COMPROVANTES_TICKET_NOT_AUTHORIZED
                        ];
                        $errorCodes = [
                            MSG_PONTUACOES_COMPROVANTES_TICKET_NOT_AUTHORIZED_CODE
                        ];

                        return ResponseUtil::errorAPI(MESSAGE_GENERIC_COMPLETED_ERROR, $error, [], $errorCodes);
                    }
                }
            }

            #endregion

            $produtos = SefazUtil::obtemProdutosSefaz($webContent["response"], $url, $chave, $cliente, $funcionario, $usuario);

            $dataProcessamento = new DateTime('now');
            $dataProcessamento = $dataProcessamento->format("Y-m-d H:i:s");
            $pontuacoes = [];
            $somaMultiplicador = 0;
            $listProductsToCheck = [];
            $listProductsForExtraPoints = $produtos;

            foreach ($gotas as $gota) {
                foreach ($produtos as $indexProduto => $produto) {
                    $percent = 0;
                    similar_text($gota->nome_parametro, $produto["descricao"], $percent);

                    // Se o percent for no mínimo MIN_PERCENTAGE_SIMILAR_TEXT_GOTAS, adiciona para posterior verificação
                    if ($percent >= MIN_PERCENTAGE_SIMILAR_TEXT_GOTAS) {
                        $pontuacao = new Pontuacao();
                        $pontuacao->clientes_id = $cliente->id;
                        $pontuacao->usuarios_id = $usuario->id;
                        $pontuacao->funcionarios_id = $funcionario->id;
                        $pontuacao->gotas_id = $gota->id;
                        $pontuacao->descricao =  $produto["descricao"];
                        $pontuacao->quantidade_multiplicador =  $produto["quantidade"];
                        $pontuacao->valor_gota_sefaz =  trim($produto["valor"]);
                        $pontuacao->quantidade_gotas =  floor($gota->multiplicador_gota * (float) $produto["quantidade"]);
                        $pontuacao->percent =  $percent;
                        $pontuacao->pontuacoes_comprovante_id = 0;
                        $pontuacao->data = $dataProcessamento;

                        $listProductsToCheck[$gota->id][] = $pontuacao;

                        // Remove o registro da lista de produtos à ser verificado no próximo loop
                        unset($produtos[$indexProduto]);

                        // Localiza no array o item para remoção
                        $output = array_filter($listProductsForExtraPoints, function ($item) use ($pontuacao) {
                            return $item["descricao"] === $pontuacao->descricao;
                        });

                        // Obtem os índices na lista para remoção
                        $indexes = array_keys($output);

                        // Remove os produtos que serão conferidos para pontuação extra
                        foreach ($indexes as $index) {
                            unset($listProductsForExtraPoints[$index]);
                        }
                    }
                }
            }

            reset($listProductsForExtraPoints);

            if ($rede->pontuacao_extra_produto_generico) {
                $pontuacaoExtra = $this->processaProdutosExtras($cliente, $usuario, $funcionario, $gotas, $listProductsForExtraPoints, TRANSMISSION_MODE_SEFAZ);

                if (!empty($pontuacaoExtra)) {
                    $pontuacoes[] = $pontuacaoExtra;
                }
            }

            foreach ($listProductsToCheck as $key => $itemsToCheck) {
                // Ordena todo mundo pelo maior valor de percent
                usort($itemsToCheck, function ($itemA, $itemB) {
                    return $itemA->percent >= $itemB->percent;
                });

                // Irá retornar um array, obtem o primeiro que é o que tem o maior valor
                $item = $itemsToCheck[0];
                $pontuacoes[] = $item;
                $somaMultiplicador += $item->quantidade_multiplicador;
            }

            if (count($pontuacoes) > 0) {
                $pontuacoesComprovante = new PontuacoesComprovante();
                $pontuacoesComprovante->clientes_id =  $cliente->id;
                $pontuacoesComprovante->usuarios_id =  $usuario->id;
                $pontuacoesComprovante->funcionarios_id =  $funcionario->id;
                $pontuacoesComprovante->conteudo =  $url;
                $pontuacoesComprovante->chave_nfe =  $chave;
                $pontuacoesComprovante->estado_nfe =  $cliente->estado;
                $pontuacoesComprovante->data =  $dataProcessamento;
                $pontuacoesComprovante->requer_auditoria =  0;
                $pontuacoesComprovante->auditado =  0;

                $pontuacoesComprovanteSave = $this->PontuacoesComprovantes->saveUpdate($pontuacoesComprovante);
                $pontuacaoComprovanteId = $pontuacoesComprovanteSave->id;
                $pontuacoesSave = [];

                foreach ($pontuacoes as $pontuacao) {
                    $pontuacao->pontuacoes_comprovante_id =  $pontuacaoComprovanteId;
                    $pontuacoesSave[] = $pontuacao;
                }

                $rede = $cliente->redes_has_cliente->rede;

                if (!empty($rede->qte_gotas_minima_bonificacao) && $rede->qte_gotas_minima_bonificacao <= $somaMultiplicador) {
                    $gotaBonificacaoSistema = $this->Gotas->getGotaBonificacaoSefaz($cliente->id);

                    // só adiciona a bonificação se o registro existir na tabela.
                    if (!empty($gotaBonificacaoSistema)) {
                        $pontuacao = $this->Pontuacoes->newEntity();
                        $pontuacao->pontuacoes_comprovante_id = $pontuacaoComprovanteId;
                        $pontuacao->clientes_id = $cliente->id;
                        $pontuacao->usuarios_id = $usuario->id;
                        $pontuacao->funcionarios_id = $funcionario->id;
                        $pontuacao->gotas_id = $gotaBonificacaoSistema->id;
                        $pontuacao->quantidade_multiplicador = 1;
                        $pontuacao->quantidade_gotas = $gotaBonificacaoSistema->multiplicador_gota;
                        $pontuacao->data = $dataProcessamento;
                        $pontuacoesSave[] = $pontuacao;
                    }
                }

                foreach ($pontuacoesSave as $pontuacaoSave) {
                    $pontuacoesSave = $this->Pontuacoes->saveUpdate($pontuacaoSave);
                }

                if ($pontuacoesSave) {
                    // Vincula o usuário que está obtendo gotas ao posto de atendimento se ele já não estiver vinculado

                    $usuarioClienteCheck = $this->ClientesHasUsuarios->getClienteUsuario($cliente->id, $usuario->id);

                    if (empty($usuarioClienteCheck)) {
                        $this->ClientesHasUsuarios->saveClienteHasUsuario($cliente["id"], $usuario["id"], true, $funcionario->id);
                    }
                }

                $pontuacaoComprovante = $this->PontuacoesComprovantes->getCouponById($pontuacoesComprovanteSave["id"]);

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

                $mensagem = new stdClass();
                $mensagem->status = 1;
                $mensagem->errors = [];
                $mensagem->message = MSG_PONTUACOES_COMPROVANTES_IMPORTED_SUCCESSFULLY;

                // $resumo = $resumo;
                $arraySet = array("mensagem", "pontuacoes_comprovantes", "resumo");

                if ($processamentoPendente && $pontuacoesSave) {
                    $pontuacaoPendente = $this->PontuacoesPendentes->findPontuacaoPendenteAwaitingProcessing($chaveNfe, $cliente->estado);
                    $this->PontuacoesPendentes->setPontuacaoPendenteProcessed($pontuacaoPendente->id, $pontuacaoComprovanteSave->id);
                }

                Log::write("info", array("mensagem" => $mensagem, "pontuacoes_comprovantes" => $pontuacaoComprovante, "resumo" => $resumo));
                $retorno = new stdClass();
                $retorno->mensagem = $mensagem;
                $retorno->pontuacoes_comprovantes = $pontuacaoComprovante;
                $retorno->resumo = $resumo;
                return $retorno;
            } else {
                $mensagem = new Mensagem();
                $mensagem->message = sprintf("No Cupom Fiscal %s da SEFAZ do estado %s não há gotas à processar conforme configurações definidas!...", $chave, $estado);
                $mensagem->status = 0;

                $retorno = new stdClass();
                $retorno->mensagem = $mensagem;

                return $retorno;
            }
        } elseif (!$processamentoPendente) {
            // Trata pontuação para ser processada posteriormente (se já não armazenada)

            // Status está anormal, grava para posterior processamento
            $clientesId = empty($cliente) ? null : $cliente["id"];

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

            $errors = [MSG_NOT_POSSIBLE_TO_IMPORT_COUPON_AWAITING_PROCESSING];
            $errorCodes = [MSG_NOT_POSSIBLE_TO_IMPORT_COUPON_AWAITING_PROCESSING_CODE];
            $data = array();
            $mensagem = new Mensagem();
            $mensagem->status = false;
            $mensagem->message = MSG_NOT_POSSIBLE_TO_IMPORT_COUPON;
            $mensagem->errors[] = MSG_NOT_POSSIBLE_TO_IMPORT_COUPON;
            $mensagem->errorCodes[] = MSG_NOT_POSSIBLE_TO_IMPORT_COUPON_CODE;

            $retorno = new stdClass();
            $retorno->mensagem = $mensagem;
            $retorno->pontuacao_pendente = $pontuacaoPendente;
            $retorno->resumo = null;

            return $retorno;
        }

        Log::write("info", "Pontuação Pendente: " . $pontuacaoPendente);

        if ($processamentoPendente) {
            $pontuacaoPendente = $this->PontuacoesPendentes->findPontuacaoPendenteAwaitingProcessing($chaveNfe, $estado);
            // $this->PontuacoesPendentes->setPontuacaoPendenteProcessed($pontuacaoPendente["id"], $pontuacaoComprovanteId);
            Log::write("info", sprintf("Pontuação pendente [%s] não processada por falha de comunicação à SEFAZ %s!", $pontuacaoPendente->id, $estado));
        }

        $mensagem = array("status" => $success, "message" => $message, "errors" => $errors);

        return array(
            'mensagem' => $mensagem,
            'pontuacoes_comprovantes' => $pontuacoesComprovantes,
            'resumo' => $resumo
        );
    }

    /**
     * Soma produtos extras da nota
     *
     * Soma produtos extras da nota para Pontuar como "Pontuação Extra"
     *
     * @param int $cliente
     * @param int $usuario
     * @param int $funcionario
     * @param App\Model\Entity\Gota[] $gotasCliente
     * @param array $gotasAbastecidasClienteFinal
     * @param string $modoTransmissao
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-12-10
     *
     * @return App\Model\Entity\Pontuacao
     */
    private function processaProdutosExtras($cliente, $usuario, $funcionario, $gotasCliente, $gotasAbastecidasClienteFinal, $modoTransmissao)
    {
        $nomeParametro = $modoTransmissao === TRANSMISSION_MODE_DIRECT ? "gotas_nome" : "descricao";
        $parameterSearch = new stdClass();

        if ($modoTransmissao === TRANSMISSION_MODE_DIRECT) {
            $parameterSearch->description = "gotas_nome";
            $parameterSearch->quantity = "gotas_qtde";
            $parameterSearch->value = "gotas_vl_unit";
        } else {
            $parameterSearch->description = "descricao";
            $parameterSearch->quantity = "quantidade";
            $parameterSearch->value = "valor";
        }

        $quantidadeExtra = 0;
        $pontosExtras = 0;
        $data = (new DateTime('now'))->format("Y-m-d H:i:s");
        $pontuacao = null;

        // Se a rede está com a pontuação extra habilitada, atribui
        foreach ($gotasAbastecidasClienteFinal as $gotaUsuario) {
            $gota = array_filter($gotasCliente, function ($item) use ($gotaUsuario, $parameterSearch) {
                // Limpa caracteres estranhos e compara
                $a = filter_var($gotaUsuario[$parameterSearch->description], FILTER_SANITIZE_STRING);
                $a = str_replace(chr(194), ' ', $a);
                $a = str_replace(chr(160), '', $a);
                $a = preg_replace('!\t+\s+!', ' ', $a);

                $b = filter_var($item["nome_parametro"], FILTER_SANITIZE_STRING);
                $b = str_replace(chr(194), ' ', $b);
                $b = str_replace(chr(160), '', $b);
                $b = preg_replace('!\t+\s+!', ' ', $b);

                return $a === $b;
            });

            $gota = array_values($gota);

            if (empty($gota)) {
                $quantidadeExtra += $gotaUsuario[$parameterSearch->quantity];
                $pontosExtras += $gotaUsuario[$parameterSearch->value];
            }
        }

        $gotaBonificacaoPontosExtras = $this->Gotas->getGotaClienteByName($cliente->id, GOTAS_BONUS_EXTRA_POINTS_SEFAZ);

        // só adiciona a bonificação se o registro existir, para não dar exception
        if (!empty($gotaBonificacaoPontosExtras) && ($quantidadeExtra > 0 && $pontosExtras > 0)) {
            $pontuacao = new Pontuacao();
            $pontuacao->quantidade_multiplicador = $quantidadeExtra;
            $pontuacao->clientes_id = $cliente->id;
            $pontuacao->usuarios_id = $usuario->id;
            $pontuacao->funcionarios_id = $funcionario->id;
            $pontuacao->gotas_id = $gotaBonificacaoPontosExtras->id;
            $pontuacao->data = $data;
            $pontuacao->quantidade_gotas = floor($pontosExtras);
            $pontuacao->valor_gota_sefaz = $pontosExtras;

            // Retorna registro para posterior processamento
            return $pontuacao;
        }

        return null;
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
        $errors = [];
        $errorCodes = [];

        if ($pontuacaoPendente) {
            if ($pontuacaoPendente->registro_processado) {
                $message = Configure::read("messageOperationFailureDuringProcessing");
                $errors[] = MSG_PONTUACOES_COMPROVANTES_QR_CODE_ALREADY_IMPORTED;
                $errorCodes[] = MSG_PONTUACOES_COMPROVANTES_QR_CODE_ALREADY_IMPORTED_CODE;
                $status = 0;
            } else {
                $message = Configure::read("messageWarningDefault");
                $errors[] = "Este registro está aguardando processamento, não é necessário importar novamente!";
                $status = 0;
            }
        } elseif ($pontuacaoComprovante) {
            $status = 0;
            $message = Configure::read("messageOperationFailureDuringProcessing");
            $errors[] = MSG_PONTUACOES_COMPROVANTES_QR_CODE_ALREADY_IMPORTED;
            $errorCodes[] = MSG_PONTUACOES_COMPROVANTES_QR_CODE_ALREADY_IMPORTED_CODE;
        }

        return array("status" => $status, "message" => $message, "errors" => $errors, "error_codes" => $errorCodes);
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
