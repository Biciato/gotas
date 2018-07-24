<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Model\Entity;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\ORM\Query;
use Cake\Core\Configure;
use Cake\Event\Event;
use App\Custom\RTI\Security;
use App\Custom\RTI\DateTimeUtil;
use \DateTime;
use App\Custom\RTI\DebugUtil;

/**
 * Gotas Controller
 *
 * @property \App\Model\Table\GotasTable $Gotas
 *
 * @method \App\Model\Entity\Gota[] paginate($object = null, array $settings = [])
 */
class GotasController extends AppController
{
    /**
     * ------------------------------------------------------------
     * Campos
     * ------------------------------------------------------------
     */
    protected $user_logged = null;

    /**
     * ------------------------------------------------------------
     * Métodos Comuns
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

        // Permitir métodos ajax
        // $this->Auth->allow(['getGotasByCliente']);
    }

    /**
     * Initialize function
     */
    public function initialize()
    {
        parent::initialize();

        $this->user_logged = $this->getUserLogged();
        $this->set('user_logged', $this->getUserLogged());
    }

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
            'contain' => ['Clientes']
        ];
        $gotas = $this->paginate($this->Gotas);

        $this->set(compact('gotas'));
        $this->set('_serialize', ['gotas']);
    }

    /**
     * View method
     *
     * @param string|null $id Gota id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $gota = $this->Gotas->get($id, [
            'contain' => ['Clientes']
        ]);

        $this->set('gota', $gota);
        $this->set('_serialize', ['gota']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        try {
            $user_admin = $this->request->session()->read('User.RootLogged');
            $user_managed = $this->request->session()->read('User.ToManage');

            if ($user_admin) {
                $this->user_logged = $user_managed;
            }

            $cliente
                = $this->Clientes->getClienteMatrizLinkedToAdmin($this->user_logged);

            if (is_null($cliente)) {
                $this->security_util->redirectUserNotAuthorized($this);
            }

            $gota = $this->Gotas->newEntity();

            if ($this->request->is(['post', 'put'])) {
                $data = $this->request->getData();

                // Verifica se há um registro de mesmo nome para aquele cliente, não pode ter dois registros
                $record_exists = $this->Gotas->getGotaClienteByName($cliente->id, $data['nome_parametro']);

                if ($record_exists) {
                    $this->Flash->Error(__("Já existe uma gota configurada de nome {0}", $data['nome_parametro']));
                } else {
                    if ($data['multiplicador_gota'] < 0.01) {
                        $this->Flash->error('É necessário informar valor diferente de 0,00 para cadastrar a gota');
                    } else {
                        $gota = $this->Gotas->patchEntity($gota, $data);

                        $gota['clientes_id'] = $cliente->id;

                        if ($this->Gotas->save($gota)) {
                            $this->Flash->success(__(Configure::read('messageSavedSuccess')));
                            return $this->redirect(['controller' => 'gotas', 'action' => 'gotas_minha_rede']);
                        }

                        // se chegou até aqui, houve erro
                        $this->Flash->error(Configure::read('messageSavedError'));
                    }
                }
            }

            $this->set(compact('gota'));
            $this->set('_serialize', ['gota']);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $message = __("Erro ao adicionar gotas de cliente: {0} em: {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $message);

            $this->Flash->error($message);
        }
    }

    /**
     * Edit method
     *
     * @param string|null $id Gota id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        try {
            $user_admin = $this->request->session()->read('User.RootLogged');
            $user_managed = $this->request->session()->read('User.ToManage');

            if ($user_admin) {
                $this->user_logged = $user_managed;
            }

            $cliente = $this->Clientes->getClienteMatrizLinkedToAdmin($this->user_logged);

            if (is_null($cliente)) {
                $this->security_util->redirectUserNotAuthorized($this);
            }

            $gota = $this->Gotas->getGotaClienteById($id, $cliente->id);

            if ($this->request->is(['post', 'put'])) {
                $data = $this->request->getData();

                $gota = $this->Gotas->patchEntity($gota, $data);
                debug($gota);

                $record_exists = false;
                // Verifica se há um registro de mesmo nome para aquele cliente, não pode ter dois registros
                if ($gota->dirty('nome_parametro')) {
                    $record_exists = $this->Gotas->getGotaClienteByName($cliente->id, $data['nome_parametro']);
                    if ($record_exists) {
                        $this->Flash->Error(__("Já existe uma gota configurada de nome {0}", $data['nome_parametro']));
                    }
                }

                if ($record_exists == false) {
                    if ($data['multiplicador_gota'] < 0.01) {
                        $this->Flash->error('É necessário informar valor diferente de 0,00 para cadastrar a gota');
                    } else {
                        $gota['clientes_id'] = $cliente->id;

                        if ($this->Gotas->save($gota)) {
                            $this->Flash->success(__(Configure::read('messageSavedSuccess')));
                            return $this->redirect(['controller' => 'gotas', 'action' => 'gotas_minha_rede']);
                        }

                        // se chegou até aqui, houve erro
                        $this->Flash->error(Configure::read('messageSavedError'));
                    }
                }
            }

            $this->set(compact('gota'));
            $this->set('_serialize', ['gota']);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $message = __("Erro ao editar gotas de cliente: {0} em: {1} ", $e->getMessage(), $trace[1]);
            Log::write('error', $message);

            $this->Flash->error($message);
        }
    }

    /**
     * Delete method
     *
     * @param string|null $id Gota id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        try {
            $this->request->allowMethod(['post', 'delete']);

            $gota = $this->Gotas->get($id);

            if ($this->Gotas->delete($gota)) {
                $this->Flash->success(__(Configure::read('messageDeleteSuccess')));
            } else {
                $this->Flash->error(__(Configure::read('messageDeleteError')));
            }

            return $this->redirect(['action' => 'gotasMinhaRede']);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $message = __("Erro ao deletar gotas de cliente: {0} em: {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $message);

            $this->Flash->error($message);
        }
    }

    /**
     * Exibe cadastro de gotas da rede
     *
     * @return void
     **/
    public function gotasMinhaRede()
    {
        try {
            $user_admin = $this->request->session()->read('User.RootLogged');
            $user_managed = $this->request->session()->read('User.ToManage');

            if ($user_admin) {
                $this->user_logged = $user_managed;
            }

            $rede = $this->request->session()->read('Network.Main');

            // pega a matriz da rede

            $clientes_ids = [];

            $unidades_ids = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($rede->id, $this->user_logged['id'], false);

            foreach ($unidades_ids as $key => $value) {
                $clientes_ids[] = $key;
            }

            $conditions = [];

            $cliente = $unidades_ids->toArray()[$clientes_ids[0]];

            if ($this->request->is(['post', 'put'])) {
                // verifica se há consulta de filtro
                $data = $this->request->getData();

                if ($data['filtrar_unidade'] != "") {
                    $clientes_ids = [];
                    $clientes_ids[] = (int)$data['filtrar_unidade'];
                }

            }

            // pega o primeiro registro de clientes_id
            $clientes_id = $clientes_ids[0];
            $clientes_ids = [];
            $clientes_ids[] = $clientes_id;

            $gotas = $this->Gotas->findGotasByClientesId(
                $clientes_ids
            );

            $this->paginate($gotas, ['limit' => 10]);

            $this->set(compact(['gotas_matriz', 'clientes', 'clientes_id', 'cliente', 'gotas', 'unidades_ids']));
            $this->set('_serialize', ['clientes', 'cliente', 'clientes_id', 'gotas', 'unidades_ids']);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $message = __("Erro ao exibir gotas de cliente: {0} em: {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $message);

            $this->Flash->error($message);
        }
    }

    /**
     * Exibe cadastro de gotas da loja (quando filial)
     *
     * @return void
     **/
    public function gotasMinhaLoja()
    {
        try {
            $user_admin = $this->request->session()->read('User.RootLogged');
            $user_managed = $this->request->session()->read('User.ToManage');

            if ($user_admin) {
                $this->user_logged = $user_managed;
            }

            $rede = $this->request->session()->read('Network.Main');

            $unidades_ids = [];

            $clientes_ids = [];

            // pega todas as unidades que o usuário possui acesso

            $unidades_ids = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($rede->id, $this->user_logged['id']);

            $clientes_ids[] = key($unidades_ids->toArray());

            $gotas = $this->Gotas->findGotasByClientesId($clientes_ids);

            $clientes_id = $clientes_ids[0];

            $this->set(compact(['gotas', 'clientes_id']));
            $this->set('_serialize', ['gotas', 'clientes_id']);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $message = __("Erro ao exibir gotas de cliente: {0} em: {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $message);

            $this->Flash->error($message);
        }
    }

    /**
     * Método da action de Adicionar Gota
     *
     * @param int $cliente_id Id de cliente
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function adicionarGota(int $cliente_id = null)
    {
        try {

            $rede = $this->request->session()->read('Network.Main');
            $user_admin = $this->request->session()->read('User.RootLogged');
            $user_managed = $this->request->session()->read('User.ToManage');

            if ($user_admin) {
                $this->user_logged = $user_managed;
            }

            // verifica se usuário é pelo menos administrador.

            if ($this->user_logged['tipo_perfil'] > Configure::read('profileTypes')['AdminLocalProfileType']) {
                $this->security_util->redirectUserNotAuthorized($this);
            }
            // Verifica permissão do usuário na rede / unidade da rede

            $temAcesso = $this->security_util->checkUserIsClienteRouteAllowed($this->user_logged, $this->Clientes, $this->ClientesHasUsuarios, [$cliente_id], $rede["id"]);

            if (!$temAcesso){
                return $this->security_util->redirectUserNotAuthorized($this, $this->user_logged);
            }

            // se usuário não for admin da rede, verifica se tem acesso naquele perfil

            if ($this->user_logged['tipo_perfil'] > Configure::read('profileTypes')['AdminNetworkProfileType']) {
                $clientes_has_usuarios_query = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($rede->id, $this->user_logged['id'], false);

                $found = false;

                foreach ($clientes_has_usuarios_query->toArray() as $key => $value) {
                    if ($key == $cliente_id) {
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    $this->security_util->redirectUserNotAuthorized($this);
                }
            }

            // concluiu validação de cliente, agora cadastra uma gota para o cliente em questão

            $cliente = $this->Clientes->getClienteById($cliente_id);

            $gota = $this->Gotas->newEntity();

            if ($this->request->is(['post', 'put'])) {
                $data = $this->request->getData();

                // Verifica se há um registro de mesmo nome para aquele cliente, não pode ter dois registros
                $record_exists = $this->Gotas->getGotaClienteByName($cliente->id, $data['nome_parametro']);

                if ($record_exists) {
                    $this->Flash->Error(__("Já existe uma gota configurada de nome {0}", $data['nome_parametro']));
                } else {
                    if ($data['multiplicador_gota'] < 0.01) {
                        $this->Flash->error('É necessário informar valor diferente de 0,00 para cadastrar a gota');
                    } else {
                        $gota = $this->Gotas->patchEntity($gota, $data);

                        $gota['clientes_id'] = $cliente->id;

                        if ($this->Gotas->save($gota)) {
                            $this->Flash->success(__(Configure::read('messageSavedSuccess')));

                            if ($this->user_logged['tipo_perfil'] >= Configure::read('profileTypes')['AdminNetworkProfileType'] && $this->user_logged['tipo_perfil'] <= Configure::read('profileTypes')['AdminRegionalProfileType']) {
                                return $this->redirect(['controller' => 'gotas', 'action' => 'gotas_minha_rede']);
                            } else {
                                return $this->redirect(['controller' => 'gotas', 'action' => 'gotas_minha_loja']);
                            }
                        }

                        // se chegou até aqui, houve erro
                        $this->Flash->error(Configure::read('messageSavedError'));
                    }
                }
            }

            $this->set(compact(['gota', 'cliente']));
            $this->set('_serialize', ['gota', 'cliente']);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $message = __("Erro ao adicionar gotas de cliente: {0} em: {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $message);

            $this->Flash->error($message);
        }
    }

    /**
     * Edit method
     *
     * @param string|null $id Gota id.
     *
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function editarGota($id = null)
    {
        try {
            $user_admin = $this->request->session()->read('User.RootLogged');
            $user_managed = $this->request->session()->read('User.ToManage');

            if ($user_admin) {
                $this->user_logged = $user_managed;
            }

            // verifica se usuário é pelo menos administrador.

            if ($this->user_logged['tipo_perfil'] > Configure::read('profileTypes')['AdminLocalProfileType']) {
                $this->security_util->redirectUserNotAuthorized($this);
            }

            $rede = $this->request->session()->read('Network.Main');

            // se usuário não for admin da rede, verifica se tem acesso naquele perfil

            if ($this->user_logged['tipo_perfil'] > Configure::read('profileTypes')['AdminNetworkProfileType']) {
                $clientes_has_usuarios_query = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($rede->id, $this->user_logged['id'], false);

                $found = false;

                foreach ($clientes_has_usuarios_query->toArray() as $key => $value) {
                    if ($key == $cliente_id) {
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    $this->security_util->redirectUserNotAuthorized($this);
                }
            }

            $gota = $this->Gotas->getGotaById($id);

            if ($this->request->is(['post', 'put'])) {
                $data = $this->request->getData();

                $gota = $this->Gotas->patchEntity($gota, $data);

                $cliente = $this->Clientes->getClienteById($gota->clientes_id);
                $record_exists = false;

                // Verifica se há um registro de mesmo nome para aquele cliente, não pode ter dois registros
                if ($gota->dirty('nome_parametro')) {
                    $record_exists = $this->Gotas->getGotaClienteByName($cliente->id, $data['nome_parametro']);
                    if ($record_exists) {
                        $this->Flash->Error(__("Já existe uma gota configurada de nome {0}", $data['nome_parametro']));
                    }
                }

                if ($record_exists == false) {
                    if ($data['multiplicador_gota'] < 0.01) {
                        $this->Flash->error('É necessário informar valor diferente de 0,00 para cadastrar a gota');
                    } else {
                        $gota['clientes_id'] = $cliente->id;

                        if ($this->Gotas->save($gota)) {
                            $this->Flash->success(__(Configure::read('messageSavedSuccess')));
                            return $this->redirect(['controller' => 'gotas', 'action' => 'gotas_minha_rede']);
                        }

                        // se chegou até aqui houve erro
                        $this->Flash->error(Configure::read('messageSavedError'));
                    }
                }
            }

            $this->set(compact('gota'));
            $this->set('_serialize', ['gota']);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $message = __("Erro ao editar gotas de cliente: {0} em: {1} ", $e->getMessage(), $trace[1]);
            Log::write('error', $message);

            $this->Flash->error($message);
        }
    }

    /**
     * Habilita a gota selecionada
     *
     * @param int $id Id da gota
     *
     * @return \Cake\Network\Response|null|void
     */
    public function habilitarGota(int $id)
    {
        try {
            $result = $this->_alteraEstadoGota($id, true);
            if ($result[0]) {
                $this->Flash->success(Configure::read('messageEnableSuccess'));

                if ($this->user_logged['tipo_perfil'] >= Configure::read('profileTypes')['AdminNetworkProfileType'] && $this->user_logged['tipo_perfil'] <= Configure::read('profileTypes')['AdminRegionalProfileType']) {
                    return $this->redirect(['controller' => 'gotas', 'action' => 'gotas_minha_rede']);
                } else {
                    return $this->redirect(['controller' => 'gotas', 'action' => 'gotas_minha_loja']);
                }
            }

            // se chegou até aqui, exibe mensagem de erro
            $this->Flash->error(Configure::read('messageEnableError'));

            $this->Flash->error($result[1]);

            return $this->redirect(
                [
                    'controller' => 'gotas', 'action' => 'gotas_minha_rede'
                ]
            );

        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $message = __("Erro ao editar gotas de cliente: {0} em: {1} ", $e->getMessage(), $trace[1]);
            Log::write('error', $message);

            $this->Flash->error($message);

            return $this->redirect(
                [
                    'controller' => 'gotas', 'action' => 'gotas_minha_rede'
                ]
            );
        }
    }

    /**
     * Desabilita a gota selecionada
     *
     * @param int $id Id da gota
     *
     * @return \Cake\Network\Response|null|void
     */
    public function desabilitarGota(int $id)
    {
        try {
            if ($this->_alteraEstadoGota($id, false)) {
                $this->Flash->success(Configure::read('messageDisableSuccess'));

                if ($this->user_logged['tipo_perfil'] >= Configure::read('profileTypes')['AdminNetworkProfileType'] && $this->user_logged['tipo_perfil'] <= Configure::read('profileTypes')['AdminRegionalProfileType']) {
                    return $this->redirect(['controller' => 'gotas', 'action' => 'gotas_minha_rede']);
                } else {
                    return $this->redirect(['controller' => 'gotas', 'action' => 'gotas_minha_loja']);
                }
            }

            // se chegou até aqui, exibe mensagem de erro
            $this->Flash->error(Configure::read('messageDisableError'));

            return $this->redirect(
                [
                    'controller' => 'gotas', 'action' => 'gotas_minha_rede'
                ]
            );
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $message = __("Erro ao editar gotas de cliente: {0} em: {1} ", $e->getMessage(), $trace[1]);
            Log::write('error', $message);

            $this->Flash->error($message);
        }
    }


    /**
     * Altera estado da gota selecionada
     *
     * @param int  $id     Id da gota
     * @param bool $status Estado de ativação
     *
     * @return bool $status
     */
    private function _alteraEstadoGota(int $id, bool $status)
    {
        try {
            return $this->Gotas->updateStatusGota($id, $status);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $message = __("Erro ao editar gotas de cliente: {0} em: {1} ", $e->getMessage(), $trace[1]);
            Log::write('error', $message);

            $this->Flash->error($message);
        }
    }

    /**
     * ------------------------------------------------------------
     * Métodos para view de funcionário
     * ------------------------------------------------------------
     */

    /**
     * Action para atribuir gotas (view de funcionário)
     *
     * @return void
     */
    public function atribuirGotas()
    {
        $user_admin = $this->request->session()->read('User.RootLogged');
        $user_managed = $this->request->session()->read('User.ToManage');

        if ($user_admin) {
            $this->user_logged = $user_managed;
        }

        $usuario = $this->Usuarios->newEntity();
        $transportadora = $this->Transportadoras->newEntity();
        $veiculo = $this->Veiculos->newEntity();

        $funcionario = $this->Usuarios->getUsuarioById($this->user_logged['id']);

        $rede = $this->request->session()->read('Network.Main');

        // Pega unidades que tem acesso
        $clientes_ids = [];

        $unidades_ids = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($rede->id, $this->user_logged['id'], false);

        foreach ($unidades_ids as $key => $value) {
            $clientes_ids[] = $key;
        }

        // No caso do funcionário, ele só estará em uma unidade, então pega o cliente que ele estiver

        $cliente = $this->Clientes->getClienteById($clientes_ids[0]);

        $clientes_id = $cliente->id;

        // o estado do funcionário é o local onde se encontra o estabelecimento.
        $estado_funcionario = $cliente->estado;

        // na verdade, o perfil deverá ser 6, pois no momento do cadastro do funcionário
        // $usuario_logado_tipo_perfil = $funcionario->tipo_perfil;
        $usuario_logado_tipo_perfil = 6;
        $this->set(compact(['usuario', 'cliente', 'clientes_id', 'funcionario', 'estado_funcionario', 'usuario_logado_tipo_perfil']));

        $this->set('transportadoraPath', 'TransportadorasHasUsuarios.Transportadoras.');
        $this->set('veiculoPath', 'UsuariosHasVeiculos.Veiculos.');
    }

    /**
     * ------------------------------------------------------------
     * Relatórios Dashboard Admin RTI
     * ------------------------------------------------------------
     */

    /**
     * Action de Relatório de Gotas de Redes
     *
     * @return void
     */
    public function relatorioGotasRedes()
    {
        try {
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

                // nome do parametro
                if (strlen($data['nome_parametro']) > 0) {
                    $whereConditions[] = ["gotas.nome_parametro like '%" . $data['nome_parametro'] . "%'"];
                }

                // registro habilitado?
                if (strlen($data['habilitado']) > 0) {
                    $whereConditions[] = ["gotas.habilitado" => (bool)$data['habilitado']];
                }

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
                        $whereConditions[] = ['gotas.audit_insert BETWEEN "' . $dataInicial . '" and "' . $dataFinal . '"'];
                    }

                } else if (strlen($data['auditInsertInicio']) > 0) {

                    if ($dataInicial > $dataHoje) {
                        $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Início'));
                    } else {
                        $whereConditions[] = ['gotas.audit_insert >= ' => $dataInicial];
                    }

                } else if (strlen($data['auditInsertFim']) > 0) {

                    if ($dataFinal > $dataHoje) {
                        $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Fim'));
                    } else {
                        $whereConditions[] = ['gotas.audit_insert <= ' => $dataFinal];
                    }
                }
            }

            // Monta o Array para apresentar em tela
            $redes = array();

            foreach ($redesArrayIds as $key => $value) {
                $arrayWhereConditions = $whereConditions;

                $redesHasClientesIds = array();

                $rede = $this->Redes->getRedeById((int)$value);

                $redeItem = array();

                $redeItem['id'] = $rede->id;
                $redeItem['nome_rede'] = $rede->nome_rede;
                $redeItem['gotas'] = array();

                $clientesIds = [];

                // obtem os ids das unidades para saber quais brindes estão disponíveis
                foreach ($rede->redes_has_clientes as $key => $value) {
                    $clientesIds[] = $value->clientes_id;
                }

                $gotas = array();

                $cliente = null;

                $gotasArray = $this->Gotas->findGotasByClientesId($clientesIds, $arrayWhereConditions)->toArray();

                $redeItem['gotas'] = $gotasArray;

                if (sizeof($gotasArray) > 0) {
                    array_push($redes, $redeItem);
                }
            }

            $arraySet = [
                'redesList',
                'redes'
            ];

            $this->set(compact($arraySet));

        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao consultar Gotas de Redes: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Action de Relatório de Gotas de Redes
     *
     * @return void
     */
    public function relatorioConsumoGotasUsuarios()
    {
        try {

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

                // nome do parametro
                if (strlen($data['nome_parametro']) > 0) {
                    $whereConditions[] = ["gotas.nome_parametro like '%" . $data['nome_parametro'] . "%'"];
                }

                // registro habilitado?
                if (strlen($data['habilitado']) > 0) {
                    $whereConditions[] = ["gotas.habilitado" => (bool)$data['habilitado']];
                }

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
                        $whereConditions[] = ['gotas.audit_insert BETWEEN "' . $dataInicial . '" and "' . $dataFinal . '"'];
                    }

                } else if (strlen($data['auditInsertInicio']) > 0) {

                    if ($dataInicial > $dataHoje) {
                        $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Início'));
                    } else {
                        $whereConditions[] = ['gotas.audit_insert >= ' => $dataInicial];
                    }

                } else if (strlen($data['auditInsertFim']) > 0) {

                    if ($dataFinal > $dataHoje) {
                        $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Fim'));
                    } else {
                        $whereConditions[] = ['gotas.audit_insert <= ' => $dataFinal];
                    }
                }
            }

             // Monta o Array para apresentar em tela
            $redes = array();

            foreach ($redesArrayIds as $key => $value) {
                $arrayWhereConditions = $whereConditions;

                $redesHasClientesIds = array();

                $rede = $this->Redes->getRedeById((int)$value);

                $redeItem = array();

                $redeItem['id'] = $rede->id;
                $redeItem['nome_rede'] = $rede->nome_rede;
                $redeItem['gotas'] = array();

                $clientesIds = [];

                // obtem os ids das unidades para saber quais brindes estão disponíveis
                foreach ($rede->redes_has_clientes as $key => $value) {
                    $clientesIds[] = $value->clientes_id;
                }

                $gotas = array();

                $cliente = null;

                $gotasArray = $this->Gotas->findGotasByClientesId($clientesIds, $arrayWhereConditions)->toArray();

                /**
                 * Tenho as gotas. Agora, percorrer cada gota, em pontuacoes,
                 * ver quais usuários consumem cada gota
                 */

                $gotasArrayReturn = array();

                $gotaReturn = null;

                foreach ($gotasArray as $key => $gota) {
                    $pontuacoesQuery = $this->Pontuacoes->getUsuariosIdsOfPontuacoesByGotas([$gota->id]);

                    $usuarios = array();

                    $gotaReturn = $gota;

                    if (sizeof($pontuacoesQuery->toArray()) > 0) {

                        foreach ($pontuacoesQuery->toArray() as $key => $pontuacao) {
                            $usuarios[] = $pontuacao->usuario;
                        }

                        $gotaReturn['usuarios'] = $usuarios;

                        $gotasArrayReturn[] = $gotaReturn;
                    }
                }

                $redeItem['gotas'] = $gotasArrayReturn;

                if (sizeof($gotasArray) > 0) {
                    array_push($redes, $redeItem);
                }
            }

        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao consultar Gotas de Redes: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }

        $arraySet = [
            'redesList',
            'redes'
        ];

        $this->set(compact($arraySet));
    }

    /**
     * ------------------------------------------------------------
     * AJAX Methods
     * ------------------------------------------------------------
     */

    /**
     * Obtem gotas por cliente
     *
     * @return void
     */
    public function getGotasByCliente()
    {
        try {
            $gotas = null;

            if ($this->request->is('post')) {
                $data = $this->request->getData();

                $cliente = $this->Clientes->getClienteById($data['clientes_id']);

                /* se cliente é filial, verifica se tem gotas configuradas.
                 * senão, obtêm as gotas da matriz.
                 * se não houver gotas configuradas, retorna array vazio
                 */

                $gotas_array = $this->Gotas->findGotasEnabledByClientesId($cliente->id);

                $gotas = $this->gotas_util->prepareGotasArray($gotas_array);

                /*
                 * se gotas está vazio, verifica pelo campo
                 * matriz id (somente se for filial)
                 */

                if (sizeof($gotas) == 0 && !is_null($cliente->matriz_id)) {
                    $gotas_array = $this->Gotas->findGotasEnabledByClientesId($cliente->matriz_id);

                    $gotas = $this->gotas_util->prepareGotasArray($gotas_array);
                }
            }

            $this->set(compact(['gotas']));
            $this->set("_serialize", ['gotas']);

        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $message = __("Erro ao retornar gotas de cliente: {0} em: {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $message);

            $this->Flash->error($message);
        }
    }
}
