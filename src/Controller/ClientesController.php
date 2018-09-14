<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Model\Entity;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Event\Event;
use App\Custom\RTI\Security;
use Aura\Intl\Exception;
use \DateTime;
use App\Custom\RTI\DateTimeUtil;
use App\Custom\RTI\FilesUtil;
use App\Custom\RTI\ImageUtil;
use App\Custom\RTI\DebugUtil;

/**
 * Clientes Controller
 *
 * @property \App\Model\Table\ClientesTable $Clientes
 *
 * @method \App\Model\Entity\Cliente[] paginate($object = null, array $settings = [])
 */
class ClientesController extends AppController
{
    protected $user_logged = null;
    protected $security = null;


    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $conditions = [];

        if ($this->request->is('post')) {

            $data = $this->request->getData();

            if (sizeof($data) > 0) {
                if ($data['opcoes'] == 'cnpj') {
                    $value = $this->cleanNumber($data['parametro']);
                } else {
                    $value = $data['parametro'];
                }

                array_push($conditions, [$data['opcoes'] . ' like ' => '%' . $value . '%']);
            }
        }

        $clientes = $this->Clientes->getAllClientes($conditions);
        $this->paginate($clientes, ['limit' => 10]);


        // $clientes = $this->paginate($this->Clientes->find()->where(['matriz_id IS' => null]));

        $this->set(compact('clientes'));
        $this->set('_serialize', ['clientes']);
    }

    /**
     * Ativa um registro
     *
     * @return boolean
     */
    public function ativar()
    {
        $query = $this->request->query();

        $this->_alteraEstadoCliente((int)$query['clientes_id'], true, $query['return_url']);
    }

    /**
     * Desativa um registro
     *
     * @return boolean
     */
    public function desativar()
    {
        $query = $this->request->query();

        $this->_alteraEstadoCliente((int)$query['clientes_id'], false, $query['return_url']);
    }

    /**
     * Undocumented function
     *
     * @param int $clientes_id
     * @param bool $estado
     * @return void
     */
    private function _alteraEstadoCliente(int $clientes_id, bool $estado, array $return_url)
    {
        try {

            $this->request->allowMethod(['post']);

            $result = $this->Clientes->changeStateEnabledCliente($clientes_id, $estado);
            if ($result) {
                if ($estado) {
                    $this->Flash->success(__(Configure::read('messageEnableSuccess')));
                } else {
                    $this->Flash->success(__(Configure::read('messageDisableSuccess')));
                }
            } else {
                if ($estado) {
                    $this->Flash->success(__(Configure::read('messageEnableError')));
                } else {
                    $this->Flash->success(__(Configure::read('messageDisableError')));
                }
            }

            return $this->redirect($return_url);
        } catch (\Exception $e) {
            $stringError = __("Erro ao realizar procedimento de alteração de estado de cliente: {0} em: {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Exibe o cadastro da rede
     */
    public function dadosMinhaRede()
    {
        $user_admin = $this->request->session()->read('User.RootLogged');
        $user_managed = $this->request->session()->read('User.ToManage');

        if ($user_admin) {
            $this->user_logged = $user_managed;
        }

        $cliente = $this->Clientes->getClienteMatrizLinkedToUsuario($this->user_logged);

        $client_to_manage = $this->request->session()->read('ClientToManage');

        if (!is_null($client_to_manage)) {
            $cliente = $client_to_manage;
        }

        $filiais = $this->Clientes->getClienteFiliais($cliente['id']);

        $conditions = [];

        if ($this->request->is('post')) {
            $data = $this->request->getData();

            if (sizeof($data) > 0) {
                if ($data['opcoes'] == 'cnpj') {
                    $value = $this->cleanNumber($data['parametro']);
                } else {
                    $value = $data['parametro'];
                }

                array_push($conditions, [$data['opcoes'] . ' like ' => '%' . $value . '%']);
            }
        }

        $filiais = $this->Clientes->getClienteFiliais($cliente->id, $conditions);

        $this->paginate($filiais, ['limit' => 10]);

        $this->set('filiais', $filiais);

        $this->set(compact('cliente'));
        $this->set('_serialize', ['cliente']);
    }

    /**
     * View method
     *
     * @param string|null $id Cliente id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function verDetalhes($id = null)
    {
        $cliente = $this->Clientes->getClienteById($id);

        $this->set('cliente', $cliente);
        $this->set('_serialize', ['cliente']);
    }

    /**
     * Adiciona uma loja ou posto
     * @param int $redes_id Id de rede
     * @author Gustavo Souza Gonçalves
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     */
    public function adicionar(int $redes_id = null)
    {
        $cliente = $this->Clientes->newEntity();

        $user_admin = $this->request->session()->read('User.RootLogged');
        $user_managed = $this->request->session()->read('User.ToManage');

        if ($user_admin) {
            $this->user_logged = $user_managed;
        }

        $rede = $this->Redes->getRedeById($redes_id);

        if ($this->request->is('post')) {
            $cliente = $this->Clientes->patchEntity($cliente, $this->request->getData());

            if ($this->Clientes->addClient($redes_id, $cliente)) {
                $this->Flash->success(__("Registro gravado com sucesso."));

                return $this->redirect(
                    [
                        'controller' => 'redes',
                        'action' => 'ver_detalhes',
                        $rede->id
                    ]
                );
            }

            $this->Flash->error(__("Não foi possível gravar o registro!"));
        }
        $clientes = $this->Clientes->find('list', ['limit' => 200]);

        $this->set('user_logged', $this->user_logged);
        $this->set(compact('cliente', 'clientes', 'rede'));
        $this->set('_serialize', ['cliente', 'rede']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Cliente id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function editar($id = null)
    {
        try {
            $user_admin = $this->request->session()->read('User.RootLogged');
            $user_managed = $this->request->session()->read('User.ToManage');

            if ($user_admin) {
                $this->user_logged = $user_managed;
            }

            $cliente = $this->Clientes->getClienteById($id);

            if ($this->request->is(['patch', 'post', 'put'])) {
                $cliente = $this->Clientes->patchEntity($cliente, $this->request->getData());

                if ($this->Clientes->updateClient($cliente)) {
                    $this->Flash->success(__('O registro foi atualizado com sucesso.'));

                    return $this->redirect(
                        [
                            'controller' => 'redes',
                            'action' => 'ver_detalhes',
                            $cliente->rede_has_cliente->redes_id
                        ]
                    );

                }
                $this->Flash->error(__('O registro não pode ser atualizado.'));

                $this->Flash->error($result['message']);
            }

            $this->set(compact('cliente', 'cliente'));
            $this->set('_serialize', ['cliente']);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao obter cupom fiscal para consulta: {0} em: {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);

        }
    }

    /**
     * Delete method
     *
     * @param string|null $id Cliente id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete()
    {
        $data = $this->request->query();
        $cliente_id = $data['cliente_id'];
        $return_url = $data['return_url'];

        $this->request->allowMethod(['post', 'delete']);
        $cliente = $this->Clientes->get($cliente_id);
        if ($this->Clientes->delete($cliente)) {
            $this->Flash->success(__('O registro {0} foi removido com sucesso.', $cliente->nome_fantasia));
        } else {
            $this->Flash->error(__('The cliente could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Gerencia um colaborador
     *
     * @param int $id Id do colaborador (Funcionário)
     *
     * @return void
     */
    public function gerenciarColaborador(int $id = null)
    {
        try {
            $profileTypes = Configure::read('profileTypes');

            $user_admin = $this->request->session()->read('User.RootLogged');
            $user_managed = $this->request->session()->read('User.ToManage');

            if ($user_admin) {
                $this->user_logged = $user_managed;
            }

            $cliente = $this->Clientes->getClienteMatrizLinkedToUsuario($this->user_logged);

            $loja = $this->Clientes->getClienteById($id);

            if ($cliente['id'] != $loja['id']) {
                $cliente = $loja;
            }

            $conditions = [];

            if ($this->request->is(['post', 'put'])) {
                $data = $this->request->getData();

                if (isset($data['opcoes'])) {
                    if ($data['opcoes'] == 'cpf') {
                        $value = $this->cleanNumber($data['parametro']);
                    } else {
                        $value = $data['parametro'];
                    }

                    array_push(
                        $conditions,
                        [
                            'Usuarios.' . $data['opcoes'] . ' like' => '%' . $value . '%'
                        ]
                    );
                }

                $params = $this->request->query;

                if (sizeof($params) > 0) {
                    $matrizId = $params['matriz_id'];
                    $clienteId = $params['cliente_id'];
                    $usuarioId = $params['usuario_id'];
                    $action = $params['action'];

                    $usuarioToManage = $this->Usuarios->getUsuarioById($usuarioId);

                    if ($action == 'add') {
                        $this->ClientesHasUsuarios->addNewClienteHasUsuario($matrizId, $clienteId, $usuarioId);
                        $this->Flash->success("Usuário atribuído com sucesso.");

                        Log::write(
                            'info',
                            __(
                                "Usuário [({0}) - {1}] adicionou operador [({2}) ({3})] como colaborador na empresa [({4})({5})]",
                                $this->user_logged['id'],
                                $this->user_logged['nome'],
                                $usuarioToManage->id,
                                $usuarioToManage->nome,
                                $cliente->id,
                                $cliente->razao_social
                            )
                        );
                    } elseif ($action == 'remove') {
                        $this->ClientesHasUsuarios->removeClienteHasUsuario($matrizId, $clienteId, $usuarioId);
                        $this->Flash->success("Usuário removido com sucesso.");

                        Log::write(
                            'info',
                            __(
                                "Usuário [({0}) - {1}] removeu operador [({2}) ({3})] como colaborador na empresa [({4})({5})]",
                                $this->user_logged['id'],
                                $this->user_logged['nome'],
                                $usuarioToManage->id,
                                $usuarioToManage->nome,
                                $cliente->id,
                                $cliente->razao_social
                            )
                        );
                    }

                    $this->redirect(['action' => 'gerenciar_colaborador', $cliente->id]);
                }
            }
            $usuarios
                = $this->Usuarios->getUsuariosAssociatedWithClient(
                $cliente,
                $profileTypes['ManagerProfileType'],
                $profileTypes['WorkerProfileType'],
                $conditions
            );

            $this->paginate(
                $usuarios,
                [
                    'limit' => 10
                ]
            );

            $this->set('user_logged', $this->user_logged);
            $this->set('cliente', $cliente);
            $this->set('usuarios', $usuarios);
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $message = __(
                "Não foi possível realizar o procedimento. Entre em contato com o suporte. Descrição do erro: {0} em: {1}",
                $e->getMessage(),
                $trace[1]
            );
            $this->Flash->error($message);

            Log::write('error', $message);
        }
    }

    /**
     * Action que seleciona unidade para administrar
     *
     * @param int $cliente_id Id de cliente
     *
     * @return \Cake\Http\Response|void
     */
    public function administrarUnidades(int $cliente_id = null)
    {
        $user_admin = $this->request->session()->read('User.RootLogged');
        $user_managed = $this->request->session()->read('User.ToManage');

        if ($user_admin) {
            $this->user_logged = $user_managed;
        }

        $cliente = $this->security_util->checkUserIsClienteRouteAllowed(
            $this->user_logged,
            $this->Clientes,
            $this->ClientesHasUsuarios
        );

        $conditions = [];

        if ($this->request->is('post')) {
            $data = $this->request->getData();

            if (sizeof($data) > 0) {
                if ($data['opcoes'] == 'cnpj') {
                    $value = $this->cleanNumber($data['parametro']);
                } else {
                    $value = $data['parametro'];
                }

                array_push(
                    $conditions,
                    [
                        $data['opcoes'] . ' like ' => '%' . $value . '%'
                    ]
                );
            }

            if (!is_null($cliente_id)) {
                $cliente = $this->Clientes->getClienteById($cliente_id);
                $this->request->session()->write('ClientToManage', $cliente);

                $usuario = $this->user_logged;

                Log::write('info', __("Operador [{0} - {1}] iniciou gerenciamento de unidade [{2} - {3}].", $usuario['id'], $usuario['nome'], $cliente['id'], $cliente['razao_social']));

                return $this->redirect('/');
            }
        }

        if ($this->user_logged['tipo_perfil'] == Configure::read('profileTypes')['AdminDeveloperProfileType']) {
            $clientes = $this->Clientes->getAllClientes($conditions);
        } else {
            $clientes = $this->Clientes->getClienteFiliais($cliente->id, $conditions);
        }

        $this->paginate($clientes, ['limit' => 10]);

        $this->set(compact(['clientes']));

        $this->set('_serialize', ['clientes']);
    }

    /**
     * Encerra a administração de uma unidade (Modo Admin RTI)
     *
     * @return void
     */
    public function encerrarAdministracaoUnidades()
    {
        $this->viewBuilder()->setLayout('none');
        $this->request->session()->delete('ClientToManage');

        return $this->redirect('/');
    }

    /**
     * Exibe os dados de um cliente (local de abastecimento) para um usuário
     *
     * @param integer $pontuacao_comprovante_id Id de Comprovante da Pontuação (nele tem todos os dados)
     *
     * @return void
     */
    public function dadosClienteAtendimentoUsuario(int $pontuacao_comprovante_id)
    {
        $pontuacao_comprovante = $this->PontuacoesComprovantes->getCouponById($pontuacao_comprovante_id);

        $user_admin = $this->request->session()->read('User.RootLogged');
        $user_managed = $this->request->session()->read('User.ToManage');

        if ($user_admin) {
            $this->user_logged = $user_managed;
        }

        $user_logged = $this->user_logged;

        $cliente = $this->Clientes->getClienteById($pontuacao_comprovante->cliente->id);
        $usuarios_id = $pontuacao_comprovante->usuarios_id;

        $arraySet = ['cliente', 'user_logged', 'usuarios_id'];

        $this->set(compact($arraySet));
        $this->set('_serialize', $arraySet);
    }

    /**
     * Configura propaganda para a unidade de atendimento
     *
     * @return void
     */
    public function configurarPropaganda(int $clientesId = null)
    {
        try {
            $user_admin = $this->request->session()->read('User.RootLogged');
            $user_managed = $this->request->session()->read('User.ToManage');

            if ($user_admin) {
                $this->user_logged = $user_managed;
            }

            // Se usuário não tem acesso, redireciona
            if (!$this->security_util->checkUserIsAuthorized($this->user_logged, "AdminNetworkProfileType", "AdminRegionalProfileType")) {
                $this->security_util->redirectUserNotAuthorized($this);
            }

            if (is_null($clientesId)) {
                $clientesId = $this->request->session()->read('Network.Unit')["id"];
            }
            $cliente = $this->Clientes->getClienteById($clientesId);

            $imagem = __("{0}{1}{2}", Configure::read("webrootAddress"), Configure::read("imageClientPathRead"), $cliente["propaganda_img"]);

            $imagemOriginal = null;

            if (strlen($cliente["propaganda_img"]) > 0) {
                // O caminho tem que ser pelo cliente, pois a mesma imagem será usada para todas as unidades
                $imagemOriginal = __("{0}{1}", Configure::read("imageClientPath"), $cliente["propaganda_img"]);
            }

            if ($this->request->is(['post', 'put'])) {

                $data = $this->request->getData();

                $trocaImagem = 0;

                if (strlen($data['crop-height']) > 0) {

                    // imagem já está no servidor, deve ser feito apenas o resize e mover ela da pasta temporária
                    // obtem dados de redimensionamento

                    $height = $data["crop-height"];
                    $width = $data["crop-width"];
                    $valueX = $data["crop-x1"];
                    $valueY = $data["crop-y1"];

                    $propagandaLink = $data["propaganda_link"];
                    $propagandaImg = $data["img-upload"];

                    $imagemOrigem = __("{0}{1}", Configure::read("imageClientPathTemp"), $data["img-upload"]);

                    $imagemDestino = __("{0}{1}", Configure::read("imageClientPath"), $data["img-upload"]);
                    $resizeSucesso = ImageUtil::resizeImage($imagemOrigem, 600, 600, $valueX, $valueY, $width, $height, 90);

                    // Se imagem foi redimensionada, move e atribui o nome para gravação
                    if ($resizeSucesso == 1) {
                        rename($imagemOrigem, $imagemDestino);
                        $data["propaganda_img"] = $data["img-upload"];

                        $trocaImagem = 1;
                    }
                }

                $cliente = $this->Redes->patchEntity($cliente, $data);

                if ($this->Clientes->updateClient($cliente)) {

                    if ($trocaImagem == 1 && !is_null($imagemOriginal)) {
                        unlink($imagemOriginal);
                    }

                    $this->Flash->success(__(Configure::read('messageSavedSuccess')));

                    if ($this->user_logged["tipo_perfil"] >= Configure::read("profileTypes")["AdminDeveloperProfileType"]
                        && $this->user_logged["tipo_perfil"] <= Configure::read("profileTypes")["AdminRegionalProfileType"]) {

                        return $this->redirect(
                            array(
                                "controller" => "RedesHasClientes", 'action' => 'propagandaEscolhaUnidades'
                            )
                        );
                    } else if ($this->user_logged["tipo_perfil"] >= Configure::read("profileTypes")["AdminLocalProfileType"]) {
                        return $this->redirect(
                            array(
                                "controller" => "Pages", 'action' => 'display'
                            )
                        );
                    }
                }
                $this->Flash->error(__(Configure::read('messageSavedError')));
            }

            $arraySet = array(
                "cliente",
                "imagem"
            );

            $this->set(compact($arraySet));
            $this->set("_serialize", $arraySet);

        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $messageString = __("Não foi possível obter dados de Pontos de Atendimento!");

            $messageStringDebug =
                __("{0} - {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $messageString, $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write("error", $messageStringDebug);
            Log::write("error", $trace);
        }
    }


    /**
     * ------------------------------------------------------------
     * Métodos REST
     * ------------------------------------------------------------
     */

    /**
     * ClientesController::getClientesListAPI
     *
     * Obtem lista de Clientes
     *
     * @param int $redesId Id da Rede (opcional)
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2018-09-10
     *
     * @return json_encode
     */
    public function getClientesListAPI()
    {
        // header("HTTP/1.0 404 Not Found");
        // exit;
        // throw new \Exception("Erro");
        // return;
        $rede = $this->request->session()->read("Network.Main");
        $redesId = $rede["id"];

        // Caso o método seja chamado via post
        if ($this->request->is("post")){
            $data = $this->request->getData();

            if (!empty($data["redesId"])) {
                $redesId = $data["redesId"];
            }
        }

        $selectList = array(
            "Clientes.id",
            "Clientes.nome_fantasia",
            "Clientes.razao_social",
            "Clientes.propaganda_img"
        );

        $redeHasClientes = $this->RedesHasClientes->getRedesHasClientesByRedesId($redesId, array(), $selectList);

        $clientes = array();

        foreach($redeHasClientes as $redeHasCliente){
            $clientes[] = $redeHasCliente["Clientes"];
        }

        $msg = $clientes;

        $arraySet = array("msg");

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);

    }

    /**
     * ------------------------------------------------------------
     * Métodos AJAX
     * ------------------------------------------------------------
     */

    /**
     * ClientesController::enviaImagemPropaganda
     *
     * Envia imagem de rede de forma assíncrona
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 06/08/2018
     *
     * @return json_object
     */
    public function enviaImagemPropaganda()
    {
        $mensagem = null;
        $status = false;
        $message = __("Erro durante o envio da imagem. Tente novamente!");

        $arquivos = array();
        try {
            if ($this->request->is('post')) {

                $data = $this->request->getData();

                $arquivos = FilesUtil::uploadFiles(Configure::read("imageClientPathTemp"));

                $status = true;
                $message = __("Envio concluído com sucesso!");
            }
        } catch (\Exception $e) {
            $messageString = __("Não foi possível enviar imagem de rede!");
            $trace = $e->getTrace();
            $mensagem = array('status' => false, 'message' => $messageString, 'errors' => $trace);
            $messageStringDebug = __("{0} - {1} em: {2}. [Função: {3} / Arquivo: {4} / Linha: {5}]  ", $messageString, $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write("error", $messageStringDebug);
        }

        $mensagem = array("status" => true, "message" => null);

        $result = array("mensagem" => $mensagem, "arquivos" => $arquivos);

        // echo json_encode($result);
        $arraySet = array(
            "arquivos",
            "mensagem"
        );

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);
    }

    /**
     * ------------------------------------------------------------
     * Relatórios - Administrativo RTI
     * ------------------------------------------------------------
     */

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

        $this->Auth->allow(['enviaImagemPropaganda']);
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
