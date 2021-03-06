<?php

namespace App\Controller;

use \DateTime;
use App\Controller\AppController;
use App\Custom\RTI\DateTimeUtil;
use App\Custom\RTI\Entity\Mensagem;
use App\Custom\RTI\ResponseUtil;
use App\Model\Entity\Gota;
use Cake\Log\Log;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Http\Client\Request;
use Exception;
use stdClass;
use Cake\Collection\Collection;

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
    protected $usuarioLogado = null;

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
        $sessao = $this->getSessionUserVariables();
        $usuario = $sessao["usuarioLogado"];
        $rede = $sessao["rede"];
        $errors = [];
        $errorCodes = [];

        $unidades = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($rede->id, $this->usuarioLogado['id'], false);
        $unidadesObj = [];

        foreach ($unidades as $key => $value) {
            array_push($unidadesObj, [
                'id' => $key,
                'name' => $value
            ]);
        }

        $conditions = [];

        $gotas = $this->Gotas->findGotasByClientesId([$unidadesObj[0]['id']]);

        $queryParams = $this->request->getQueryParams();
        $gotasWithClientName = [];
        foreach ($gotas as $gota) {
            array_push($gotasWithClientName, [
                'cliente' => $gota->cliente->nome_fantasia,
                'clientes_id'=> $gota->clientes_id,
                'habilitado' => $gota->habilitado ? 'Habilitado' : 'Não Habilitado',
                'id' => $gota->id,
                'multiplicador_gota' => number_format(floatval($gota->multiplicador_gota), 2, ',', ''),
                'nome_parametro' => $gota->nome_parametro,
                'tipo_cadastro' => $gota->tipo_cadastro
            ]);
        }

        $dataTableSource = new stdClass();
        $dataTableSource->draw = $queryParams['draw'];
        $dataTableSource->recordsTotal = $gotas->count();
        $dataTableSource->recordsFiltered = $gotas->count();
        $dataTableSource->data = $gotasWithClientName;

        return ResponseUtil::successAPI(MSG_LOAD_DATA_WITH_SUCCESS, ['data_table_source' => $dataTableSource]);
        /* } catch (\Throwable $th) {
            for ($i = 0; $i < count($errors); $i++) {
                Log::write("error", sprintf("[%s] %s: %s", MSG_LOAD_EXCEPTION, $errorCodes[$i], $errors[$i]));
            }

            return ResponseUtil::errorAPI($th->getMessage(), $errors, [], $errorCodes);
        } */
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
            $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
            $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

            if ($usuarioAdministrador) {
                $this->usuarioLogado = $usuarioAdministrar;
            }

            $cliente
                = $this->Clientes->getClienteMatrizLinkedToAdmin($this->usuarioLogado);

            if (is_null($cliente)) {
                $this->securityUtil->redirectUserNotAuthorized($this);
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
        } catch (Exception $e) {
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
            $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
            $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

            if ($usuarioAdministrador) {
                $this->usuarioLogado = $usuarioAdministrar;
            }

            $cliente = $this->Clientes->getClienteMatrizLinkedToAdmin($this->usuarioLogado);

            if (is_null($cliente)) {
                $this->securityUtil->redirectUserNotAuthorized($this);
            }

            $gota = $this->Gotas->getGotaClienteById($id, $cliente->id);

            if ($this->request->is(['post', 'put'])) {
                $data = $this->request->getData();

                $gota = $this->Gotas->patchEntity($gota, $data);

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
            $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
            $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

            if ($usuarioAdministrador) {
                $this->usuarioLogado = $usuarioAdministrar;
            }

            $rede = $this->request->session()->read('Rede.Grupo');

            // pega a matriz da rede

            $clientesIds = [];
            $clientesId = 0;

            $unidadesIds = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($rede->id, $this->usuarioLogado['id'], false);

            foreach ($unidadesIds as $key => $value) {
                $clientesIds[] = $key;
            }

            $conditions = [];

            $unidadesIds = $unidadesIds->toArray();

            if ($this->request->is(['post', 'put'])) {
                // verifica se há consulta de filtro
                $data = $this->request->getData();

                if ($data['filtrar_unidade'] != "") {
                    $clientesIds = [];
                    $clientesIds[] = (int) $data['filtrar_unidade'];
                    $clientesId = $data["filtrar_unidade"];
                }
            }

            $gotas = $this->Gotas->findGotasByClientesId(
                $clientesIds
            );

            $gotas = $this->paginate($gotas, ['limit' => 10]);

            // $arraySet = array('gotas_matriz', 'clientes', 'clientes_id', 'cliente', 'gotas', 'unidadesIds');
            $arraySet = array('gotas_matriz', 'clientes', 'clientesId', 'gotas', 'unidadesIds');

            $this->set(compact($arraySet));
            $this->set('_serialize', $arraySet);
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
            $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
            $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

            if ($usuarioAdministrador) {
                $this->usuarioLogado = $usuarioAdministrar;
            }

            $rede = $this->request->session()->read('Rede.Grupo');

            $unidades_ids = [];

            $clientes_ids = [];

            // pega todas as unidades que o usuário possui acesso

            $unidades_ids = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($rede->id, $this->usuarioLogado['id']);

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
            $sessaoUsuario = $this->getSessionUserVariables();
            $usuarioLogado = $sessaoUsuario["usuarioLogado"];
            $rede = $sessaoUsuario["rede"];
            $usuarioAdministrador = $sessaoUsuario["usuarioAdministrador"];
            $usuarioAdministrar =  $sessaoUsuario["usuarioAdministrar"];

            if ($usuarioAdministrador && $usuarioLogado["tipo_perfil"] == PROFILE_TYPE_ADMIN_DEVELOPER) {
                $this->usuarioLogado = $usuarioAdministrar;
                $usuarioLogado = $usuarioAdministrar;
            }

            // verifica se usuário é pelo menos administrador regional

            if ($this->usuarioLogado['tipo_perfil'] > Configure::read('profileTypes')['AdminLocalProfileType']) {
                $this->securityUtil->redirectUserNotAuthorized($this);
            }

            $unidades = array();

            if ($usuarioLogado["tipo_perfil"] == Configure::read("profileTypes")["AdminNetworkProfileType"]) {
                $unidades = $this->Clientes->getClientesListByRedesId($rede["id"]);
            } else {
                $unidades = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($rede->id, $this->usuarioLogado['id'], false);
            }

            // Verifica permissão do usuário na rede / unidade da rede

            // se usuário não for admin da rede, verifica se tem acesso naquele perfil

            if ($this->usuarioLogado['tipo_perfil'] > Configure::read('profileTypes')['AdminLocalProfileType']) {
                $this->securityUtil->redirectUserNotAuthorized($this);
            }

            // concluiu validação de cliente, agora cadastra uma gota para o cliente em questão

            $gota = $this->Gotas->newEntity();

            if ($this->request->is(['post', 'put'])) {
                $data = $this->request->getData();

                if (empty($data["clientes_id"])) {
                    $this->Flash->error("É necessário informar o Posto de Atendimento!");

                    return $this->redirect(array("controller" => "gotas", "action" => "adicionarGota"));
                }

                $clientesId = $data["clientes_id"];
                // Verifica se há um registro de mesmo nome para aquele cliente, não pode ter dois registros
                $record_exists = $this->Gotas->getGotaClienteByName($clientesId, $data['nome_parametro']);

                if ($record_exists) {
                    $this->Flash->Error(__("Já existe uma gota configurada de nome {0}", $data['nome_parametro']));
                } else {
                    if ($data['multiplicador_gota'] < 0.01) {
                        $this->Flash->error('É necessário informar valor diferente de 0,00 para cadastrar a gota');
                    } else {
                        $gota = $this->Gotas->patchEntity($gota, $data);

                        $gota['clientes_id'] = $clientesId;

                        if ($this->Gotas->save($gota)) {
                            $this->Flash->success(__(Configure::read('messageSavedSuccess')));

                            if ($this->usuarioLogado['tipo_perfil'] >= Configure::read('profileTypes')['AdminNetworkProfileType'] && $this->usuarioLogado['tipo_perfil'] <= Configure::read('profileTypes')['AdminRegionalProfileType']) {
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

            $unidadesId = 0;
            $unidades = $unidades->toArray();

            if (sizeof($unidades) == 1) {
                $unidadesId = array_keys($unidades)[0];
            }

            $arraySet = array('gota', "usuarioLogado", "unidades", "unidadesId");
            $this->set(compact($arraySet));
            $this->set('_serialize', $arraySet);
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
            $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
            $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

            if ($usuarioAdministrador) {
                $this->usuarioLogado = $usuarioAdministrar;
            }

            // verifica se usuário é pelo menos administrador.

            if ($this->usuarioLogado['tipo_perfil'] > Configure::read('profileTypes')['AdminLocalProfileType']) {
                $this->securityUtil->redirectUserNotAuthorized($this);
            }

            $rede = $this->request->session()->read('Rede.Grupo');

            // se usuário não for admin da rede, verifica se tem acesso naquele perfil

            if ($this->usuarioLogado['tipo_perfil'] > Configure::read('profileTypes')['AdminLocalProfileType']) {
                $this->securityUtil->redirectUserNotAuthorized($this);
            }

            $gota = $this->Gotas->getGotasById($id);

            $unidades = $this->Clientes->find('list')->where(array("id" => $gota["clientes_id"]));

            if ($this->request->is(['post', 'put'])) {
                $data = $this->request->getData();

                if (empty($data["clientes_id"])) {
                    $this->Flash->error("É necessário informar o Posto de Atendimento!");

                    return $this->redirect(array("controller" => "gotas", "action" => "editarGota", $id));
                }

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

            $unidadesId = $gota["clientes_id"];

            $arraySet = array("gota", "unidades", "unidadesId");

            $this->set(compact($arraySet));
            $this->set('_serialize', $arraySet);
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
            $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
            $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

            if ($usuarioAdministrador) {
                $this->usuarioLogado = $usuarioAdministrar;
            }

            $rede = $this->request->session()->read('Rede.Grupo');

            $result = $this->_alteraEstadoGota($id, true);
            if ($result[0]) {
                $this->Flash->success(Configure::read('messageEnableSuccess'));

                if ($this->usuarioLogado['tipo_perfil'] >= Configure::read('profileTypes')['AdminNetworkProfileType'] && $this->usuarioLogado['tipo_perfil'] <= Configure::read('profileTypes')['AdminRegionalProfileType']) {
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
            $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
            $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

            if ($usuarioAdministrador) {
                $this->usuarioLogado = $usuarioAdministrar;
            }

            $rede = $this->request->session()->read('Rede.Grupo');

            if ($this->_alteraEstadoGota($id, false)) {
                $this->Flash->success(Configure::read('messageDisableSuccess'));

                if ($this->usuarioLogado['tipo_perfil'] >= Configure::read('profileTypes')['AdminNetworkProfileType'] && $this->usuarioLogado['tipo_perfil'] <= Configure::read('profileTypes')['AdminRegionalProfileType']) {
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

    #region Views para actions de API

    /**
     * ------------------------------------------------------------
     * Nota: Temporária até a saída do projeto em Angular 8
     * ------------------------------------------------------------
     */

    /**
     * Index para Produtos de todos os estabelecimentos
     *
     * Exibe todos os registros de produtos de estabelecimentos e permite-os configurar com privilégios de Adm de sistema
     *
     * @return void
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-12-06
     */
    public function produtosRedes()
    {
        // $cnpjEncontrado = "05508016000123";
        // $cliente = $this->Clientes->getClienteByCNPJ($cnpjEncontrado);

        // DebugUtil::printArray($cliente);
        $sessaoUsuario = $this->getSessionUserVariables();

        $usuario = $sessaoUsuario["usuarioLogado"];

        if ($usuario->tipo_perfil != PROFILE_TYPE_ADMIN_DEVELOPER) {
            $this->Flash->error(USER_NOT_ALLOWED_TO_EXECUTE_FUNCTION);
            return $this->redirect("/");
        }

        $arraySet = [
            "usuario"
        ];

        $this->set(compact($arraySet));
    }

    public function importacaoGotasSefaz()
    {
        // $cnpjEncontrado = "05508016000123";
        // $cliente = $this->Clientes->getClienteByCNPJ($cnpjEncontrado);

        // DebugUtil::printArray($cliente);
        $sessaoUsuario = $this->getSessionUserVariables();

        $usuario = $sessaoUsuario["usuarioLogado"];

        if ($usuario->tipo_perfil != PROFILE_TYPE_ADMIN_DEVELOPER) {
            $this->Flash->error(USER_NOT_ALLOWED_TO_EXECUTE_FUNCTION);
            return $this->redirect("/");
        }

        $arraySet = [
            "usuario"
        ];

        $this->set(compact($arraySet));
    }

    #endregion


    #region API Actions

    public function getGotasClientesAPI()
    {
        $sessao = $this->getSessionUserVariables();
        $usuario = $sessao["usuarioLogado"];
        $rede = $sessao["rede"];

        if ($this->request->is(Request::METHOD_GET)) {
            $errors = [];
            $errorCodes = [];
            $data = $this->request->getQueryParams();
            $clientesId = !empty($data["clientes_id"]) ? (int) $data["clientes_id"] : null;
            $redesId = null;

            if ($usuario->tipo_perfil == PROFILE_TYPE_ADMIN_DEVELOPER) {
                // Só permite especificar a rede se for RTI/Desenvolvedor
                $redesIdSessao = !empty($rede) ? $rede->id : 0;
                $redesId = !empty($data["redes_id"]) ? $data["redes_id"] : $redesIdSessao;
            } else {
                $redesId = $rede->id;
            }

            Log::write("info", sprintf("Info de %s: %s - %s: %s", Request::METHOD_GET, __CLASS__, __METHOD__, print_r($data, true)));

            if (empty($clientesId)) {
                $errors[] = MSG_CLIENTES_ID_NOT_EMPTY;
                $errorCodes[] = MSG_CLIENTES_ID_NOT_EMPTY_CODE;
            }

            try {

                if (count($errors) > 0) {
                    throw new Exception(MSG_LOAD_EXCEPTION, MSG_LOAD_EXCEPTION_CODE);
                }

                $gotas = $this->Gotas->getGotas($clientesId, null, null, null, 1);
                $data = ["data" => ["gotas" => $gotas]];

                return ResponseUtil::successAPI(MSG_LOAD_DATA_WITH_SUCCESS, $data);
            } catch (\Throwable $th) {
                for ($i = 0; $i < count($errors); $i++) {
                    Log::write("error", sprintf("[%s] %s: %s", MSG_LOAD_EXCEPTION, $errorCodes[$i], $errors[$i]));
                }

                return ResponseUtil::errorAPI($th->getMessage(), $errors, [], $errorCodes);
            }
        }
    }

    public function setGotasClientesAPI()
    {

        if ($this->request->is(Request::METHOD_POST)) {

            $data = $this->request->getData();

            Log::write("info", sprintf("Info de %s: %s - %s: %s", Request::METHOD_POST, __CLASS__, __METHOD__, print_r($data, true)));

            $clientesId = !empty($data["clientes_id"]) ? (int) $data["clientes_id"] : null;
            $gotas = !empty($data["gotas"]) ? $data["gotas"] : null;
            $errors = [];
            $errorCodes = [];

            try {
                if (empty($clientesId)) {
                    $errors[] = MSG_CLIENTES_ID_NOT_EMPTY;
                    $errorCodes[] = MSG_CLIENTES_ID_NOT_EMPTY_CODE;
                }

                if (empty($gotas)) {
                    $errors[] = MSG_GOTAS_DATA_EMPTY;
                    $errorCodes[] = MSG_GOTAS_DATA_EMPTY_CODE;
                }

                if (count($errors) > 0) {
                    // encontrou erros, dá exception
                    throw new Exception(MSG_SAVED_EXCEPTION, MSG_SAVED_EXCEPTION_CODE);
                }

                $countSucesso = 0;

                foreach ($gotas as $gotaItem) {
                    $gota = new Gota();
                    $gota->nome_parametro = $gotaItem["nome_parametro"];
                    $gota->multiplicador_gota = number_format($gotaItem["multiplicador_gota"], 3, ".", "");
                    $gota->clientes_id = $clientesId;
                    $gota->tipo_cadastro = 0;
                    $gota->habilitado = true;
                    $gotaSave = $this->Gotas->saveUpdate($gota);

                    $countSucesso += $gotaSave ? 1 : 0;
                }

                if ($countSucesso == count($gotas)) {
                    return ResponseUtil::successAPI(MESSAGE_SAVED_SUCCESS);
                }

                return ResponseUtil::errorAPI(MSG_SAVED_EXCEPTION, []);
            } catch (\Throwable $th) {
                for ($i = 0; $i < count($errors); $i++) {
                    Log::write("error", sprintf("[%s] %s %s", $th->getMessage(), $errors[$i], $errorCodes[$i]));
                }

                return ResponseUtil::errorAPI($th->getMessage(), $errors, [], $errorCodes);
            }
        }
    }

    #endregion

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
        $usuarioLogado = $this->usuarioLogado;

        // if ($usuarioAdministrador) {
        //     $this->usuarioLogado = $usuarioAdministrar;
        //     $usuarioLogado = $usuarioAdministrar;
        // }

        $usuario = $this->Usuarios->newEntity();
        $transportadora = $this->Transportadoras->newEntity();
        $veiculo = $this->Veiculos->newEntity();

        $funcionario = $this->Usuarios->getUsuarioById($this->usuarioLogado['id']);

        $rede = $this->rede;
        $redes_id = $rede->id;

        // Pega unidades que tem acesso
        $clientesIds = [];

        $unidadesIds = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($rede->id, $this->usuarioLogado['id'], false);

        foreach ($unidadesIds as $key => $value) {
            $clientesIds[] = $key;
        }

        // No caso do funcionário, ele só estará em uma unidade, então pega o cliente que ele estiver

        $cliente = $this->Clientes->getClienteById($clientesIds[0]);

        $clientes_id = $cliente["id"];
        $clientesCNPJ = $cliente["cnpj"];

        // o estado do funcionário é o local onde se encontra o estabelecimento.
        $estado_funcionario = $cliente["estado"];

        // na verdade, o perfil deverá ser 6, pois no momento do cadastro do funcionário
        // $usuarioLogadoTipoPerfil = $funcionario->tipo_perfil;
        $usuarioLogadoTipoPerfil = 6;
        $transportadoraPath = 'TransportadorasHasUsuarios.Transportadoras.';
        $veiculoPath = 'UsuariosHasVeiculos.Veiculos.';

        $arraySet = array(
            'usuario',
            'cliente',
            'clientes_id',
            "redes_id",
            "clientesCNPJ",
            'funcionario',
            'estado_funcionario',
            "usuarioLogado",
            'usuarioLogadoTipoPerfil',
            "transportadoraPath",
            "veiculoPath"
        );

        $this->set(compact($arraySet));
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
                    $whereConditions[] = ["gotas.habilitado" => (bool) $data['habilitado']];
                }

                // Data de Criação Início e Fim

                $dataHoje = DateTimeUtil::convertDateToUTC((new DateTime('now'))->format('Y-m-d H:i:s'));
                $dataInicial = strlen($data['auditInsertInicio']) > 0 ? DateTimeUtil::convertDateToUTC($data['auditInsertInicio'], 'd/m/Y') : null;
                $dataFinal = strlen($data['auditInsertFim']) > 0 ? DateTimeUtil::convertDateToUTC($data['auditInsertFim'], 'd/m/Y') : null;

                if (strlen($data['auditInsertInicio']) > 0 && strlen($data['auditInsertFim']) > 0) {

                    if ($dataInicial > $dataFinal) {
                        $this->Flash->error(__(Configure::read('messageDateRangeInvalid')));
                    } elseif ($dataInicial > $dataHoje) {
                        $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid', 'Data de Início')));
                    } else {
                        $whereConditions[] = ['gotas.audit_insert BETWEEN "' . $dataInicial . '" and "' . $dataFinal . '"'];
                    }
                } elseif (strlen($data['auditInsertInicio']) > 0) {

                    if ($dataInicial > $dataHoje) {
                        $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Início'));
                    } else {
                        $whereConditions[] = ['gotas.audit_insert >= ' => $dataInicial];
                    }
                } elseif (strlen($data['auditInsertFim']) > 0) {

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

                $rede = $this->Redes->getRedeById((int) $value);

                $redeItem = array();

                $redeItem['id'] = $rede->id;
                $redeItem['nome_rede'] = $rede->nome_rede;
                $redeItem['gotas'] = array();

                $clientesIds = [];

                // obtem os ids das unidades para saber quais brindes estão disponíveis
                foreach ($rede->redes_has_clientes as $value) {
                    $clientesIds[] = $value->clientes_id;
                }

                $gotas = array();

                $cliente = null;

                $gotasArray = [];

                if (count($clientesIds) > 0) {
                    $gotasArray = $this->Gotas->findGotasByClientesId($clientesIds, $arrayWhereConditions)->toArray();
                }

                $redeItem['gotas'] = $gotasArray;

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
                    $whereConditions[] = ["gotas.habilitado" => (bool) $data['habilitado']];
                }

                // Data de Criação Início e Fim

                $dataHoje = DateTimeUtil::convertDateToUTC((new DateTime('now'))->format('Y-m-d H:i:s'));
                $dataInicial = strlen($data['auditInsertInicio']) > 0 ? DateTimeUtil::convertDateToUTC($data['auditInsertInicio'], 'd/m/Y') : null;
                $dataFinal = strlen($data['auditInsertFim']) > 0 ? DateTimeUtil::convertDateToUTC($data['auditInsertFim'], 'd/m/Y') : null;

                if (strlen($data['auditInsertInicio']) > 0 && strlen($data['auditInsertFim']) > 0) {

                    if ($dataInicial > $dataFinal) {
                        $this->Flash->error(__(Configure::read('messageDateRangeInvalid')));
                    } elseif ($dataInicial > $dataHoje) {
                        $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid', 'Data de Início')));
                    } else {
                        $whereConditions[] = ['gotas.audit_insert BETWEEN "' . $dataInicial . '" and "' . $dataFinal . '"'];
                    }
                } elseif (strlen($data['auditInsertInicio']) > 0) {

                    if ($dataInicial > $dataHoje) {
                        $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Início'));
                    } else {
                        $whereConditions[] = ['gotas.audit_insert >= ' => $dataInicial];
                    }
                } elseif (strlen($data['auditInsertFim']) > 0) {

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

                $rede = $this->Redes->getRedeById((int) $value);

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

                $gotas = $this->gotasUtil->prepareGotasArray($gotas_array);

                /*
                 * se gotas está vazio, verifica pelo campo
                 * matriz id (somente se for filial)
                 */

                if (sizeof($gotas) == 0 && !is_null($cliente->matriz_id)) {
                    $gotas_array = $this->Gotas->findGotasEnabledByClientesId($cliente->matriz_id);

                    $gotas = $this->gotasUtil->prepareGotasArray($gotas_array);
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
