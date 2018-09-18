<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Log\Log;
use Cake\Routing\Router;
use Cake\Mailer\Email;
use Cake\I18n\Number;
use Cake\View\Helper\UrlHelper;
use \DateTime;
use App\Custom\RTI\DateTimeUtil;
use Firebase\JWT\JWT;
use Cake\Utility\Security;
use Cake\Network\Exception\UnauthorizedException;
use App\Custom\RTI\EmailUtil;
use App\Custom\RTI\NumberUtil;
use App\Custom\RTI\DebugUtil;
use App\Custom\RTI\ExcelUtil;
use App\Custom\RTI\ResponseUtil;



/**
 * Usuarios Controller
 *
 * @property \App\Model\Table\UsuariosTable $Usuarios
 *
 * @method \App\Model\Entity\Usuario[] paginate($object = null, array $settings = [])
 */
class UsuariosController extends AppController
{
    protected $user_logged = null;

    /**
     * ------------------------------------------------------------
     * Auth Methods
     * ------------------------------------------------------------
     */

    /**
     * Login method
     *
     * @return void
     */
    public function login()
    {
        $recoverAccount = null;

        $email = '';

        $message = '';

        if ($this->request->is('post')) {
            $data = $this->request->getData();

            $result = $this->Usuarios->checkUsuarioIsLocked($data);

            $email = $data['email'];
            if ($result['actionNeeded'] == 0) {
                $user = $this->Auth->identify();

                if ($user) {
                    $this->Auth->setUser($user);

                    $this->Usuarios->updateLoginRetry($user, true);

                    if ($user['tipo_perfil'] > Configure::read('profileTypes')['AdminDeveloperProfileType'] && $user['tipo_perfil'] < Configure::read('profileTypes')['UserProfileType']) {
                        $cliente = $this->Clientes->getClienteMatrizLinkedToUsuario($user);

                        if ($cliente) {
                            $this->request->session()->write('Network.Unit', $cliente);
                        }

                        // verifica qual rede o usuário se encontra (somente funcionários)

                        $rede_has_cliente = $this->RedesHasClientes->getRedesHasClientesByClientesId(
                            $cliente->id
                        );

                        $rede = $rede_has_cliente->rede;

                        $this->request->session()->write('Network.Main', $rede);
                    }

                    // return $this->redirect($this->Auth->redirectUrl());
                    return $this->redirect(['controller' => 'pages', 'action' => 'display']);
                } else {

                    $user = $this->Usuarios->getUsuarioByEmail($email);

                    $this->Usuarios->updateLoginRetry($user, false);

                    $this->Flash->error("Usuário ou senha ínvalidos, tente novamente");
                }
            } elseif ($result['actionNeeded'] != 0) {
                $message = $result['message'];
                $recoverAccount = $result['actionNeeded'];
            } else {
                $message = $this->Usuarios->updateLoginRetry($data, false);
            }

            if (strlen($message) > 0) {
                $this->Flash->error(__($message));
            }
        }

        $this->set('recoverAccount', $recoverAccount);
        $this->set('email', $email);
        $this->set('message', $message);
        $this->set('_serialize', ['message']);
    }

    /**
     * Logoff method
     *
     * @return void
     */
    public function logout()
    {
        // limpa as informações de session
        $this->request->session()->delete('User.RootLogged');
        $this->request->session()->delete('Cliente');
        $this->request->session()->delete('ClientToManage');
        $this->request->session()->delete('Auth.User');

        $user_managed = null;
        if (isset($user_admin)) {
            $user_admin = $user_logged;
            $user_logged = $this->request->session()->read('User.ToManage');
        }

        return $this->redirect($this->Auth->logout());
    }

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
    }

    /**
     * Initialize function
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');

        // Permitir aos usuários se registrarem e efetuar login e logout.

        // TODO: remover findUsuario
        $this->Auth->allow(['registrar', 'registrarAPI', 'esqueciMinhaSenhaAPI', 'loginAPI', 'login', 'logout', 'esqueciMinhaSenha', 'reativarConta', 'resetarMinhaSenha', 'getUsuarioByCPF', 'getUsuarioByEmail', 'uploadDocumentTemporaly', "testAPI"]);
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
        $conditions = [];

        $entire_network = false;

        $search_by_rede = false;

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();

            if ($data['opcoes'] == 'cpf') {
                $value = $this->cleanNumber($data['parametro']);
            } else {
                $value = $data['parametro'];
            }

            $search_by_rede = $data['opcoes'] == 'nome_rede';

            if ($search_by_rede) {

                array_push(
                    $conditions,
                    [
                        $data['opcoes'] . ' like ' => '%' . $value . '%'
                    ]
                );
            } else {
                array_push(
                    $conditions,
                    [
                        'usuarios.' . $data['opcoes'] . ' like ' => '%' . $value . '%'
                    ]
                );
            }

            if (isset($data['incluir_filiais']) && $data['incluir_filiais']) {
                $entire_network = true;
            }
        }


        array_push($conditions, ['tipo_perfil >= ' => Configure::read('profileTypes')['AdminDeveloperProfileType']]);
        $usuarios = $this->Usuarios->findAllUsuarios($conditions);

        $usuarios = $this->paginate($usuarios, ['limit' => 10, 'order' => ['tipo_perfil' => 'ASC']]);

        $this->set(compact('usuarios'));
        $this->set('_serialize', ['usuarios']);
    }

    /**
     * View method
     *
     * @param string|null $id Usuario id.
     *
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     *  When record not found.
     */
    public function view($id = null)
    {
        $usuario = $this->Usuarios->get(
            $id,
            [
                'contain' => []
            ]
        );

        $this->set('usuario', $usuario);
        $this->set('_serialize', ['usuario']);
    }

    /**
     * Exibe detalhes de usuário (action para administrador)
     *
     * @param string|null $id Usuario id.
     *
     * @return \Cake\Http\Response|void
     *
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function detalhesUsuario($id = null)
    {
        $usuario = $this->Usuarios->get(
            $id,
            [
                'contain' => []
            ]
        );

        $this->set('usuario', $usuario);
        $this->set('_serialize', ['usuario']);
    }

    /**
     * Método para editar dados de usuário (modo de administrador)
     *
     * @param string|null $id Usuario id.
     *
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     *
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function editarUsuario($id = null)
    {
        try {

            $user_admin = $this->request->session()->read('User.RootLogged');
            $user_managed = $this->request->session()->read('User.ToManage');

            if ($user_admin) {
                $this->user_logged = $user_managed;
            }

            $rede = $this->request->session()->read('Network.Main');

            $usuario = $this->Usuarios->get(
                $id,
                [
                    'contain' => []
                ]
            );

            if ($this->request->is(['post', 'put'])) {
                $usuario = $this->Usuarios->patchEntity($usuario, $this->request->getData(), ['validate' => 'EditUsuarioInfo']);

                $errors = $usuario->errors();

                $usuario = $this->Usuarios->save($usuario);
                if ($usuario) {
                    $this->Flash->success(__(Configure::read('messageSavedSuccess')));

                    $url = Router::url(['controller' => 'Usuarios', 'action' => 'meus_clientes']);
                    return $this->response = $this->response->withLocation($url);

                }
                $this->Flash->error(__(Configure::read('messageSavedError')));

                // exibe os erros logo acima identificados
                foreach ($errors as $key => $error) {
                    $key = key($error);
                    $this->Flash->error(__("{0}", $error[$key]));
                }
            }

            $usuario_logado_tipo_perfil = (int)Configure::read('profileTypes')['UserProfileType'];

            $this->set(compact(['usuario', 'usuario_logado_tipo_perfil']));
            $this->set('_serialize', ['usuario', 'usuario_logado_tipo_perfil']);
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao editar dados de usuário: {0} em: {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Exibe action de perfil do usuário
     *
     * @return \Cake\Http\Response|void
     */
    public function meuPerfil()
    {
        $user_admin = $this->request->session()->read('User.RootLogged');
        $user_managed = $this->request->session()->read('User.ToManage');

        if ($user_admin) {
            $this->user_logged = $user_managed;
        }

        $usuario = $this->Usuarios->get($this->user_logged['id']);

        $this->set('usuario', $usuario);
        $this->set('_serialize', ['usuario']);
    }

    /**
     * UsuariosController::meuPerfilAPI
     *
     * Retorna os dados de perfil do usuário logado
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 2018/05/09
     *
     * @return json object
     */
    public function meuPerfilAPI()
    {
        $mensagem = array();

        $status = false;
        $message = null;
        $errors = array();

        try {
            $usuario = $this->Usuarios->getUsuarioById($this->Auth->user()["id"]);

            $mensagem = ['status' => true, 'message' => $message, 'errors' => $errors];
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $messageString = __("Erro ao obter dados de usuário!");

            $errors = $trace;
            $mensagem = ['status' => false, 'message' => $messageString, 'errors' => $errors];

            $messageStringDebug = __("{0} - {1} . [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $messageString, $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write("error", $messageStringDebug);
            Log::write("error", $trace);
        }

        $arraySet = [
            "mensagem",
            "usuario"
        ];

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);
    }

    /**
     * UsuariosController::setPerfilAPI
     *
     * Atualiza o cadastro do usuário logado via API.
     *
     * @param int      $data["tipo_perfil"]            Tipo de Perfil. Somente leitura
     * @param string   $data["nome"]                   Nome Completo
     * @param date     $data["data_nasc"]              Data de Nascimento. Formato DDDD/MM/YY
     * @param bool     $data["sexo"]                   Masculino / Feminino (1 / 0)
     * @param bool     $data["necessidades_especiais"] Necessidades Especiais :
     * @param string   $data["cpf"]                    Cpf : CPF do Usuário.
     * @param string   $data["foto_documento"]         Foto Documento : URL da carteira de identidade / Doc. Estrangeiro.
     * @param string   $data["doc_estrangeiro"]        Doc Estrangeiro : Documento Estrangeiro.
     * @param string   $data["email"]                  Email : Email de cadastro. Somente leitura.
     * @param string   $data["senha"]                  Senha : Somente leitura neste serviço
     * @param string   $data["telefone"]               Telefone . 10 dígitos para TEL, 11 para CEL
     * @param string   $data["endereco"]               Endereco
     * @param string   $data["endereco_numero"]        Endereco Numero . Vazio = S/N
     * @param string   $data["endereco_complemento"]   Endereco Complemento .
     * @param string   $data["bairro"]                 Bairro
     * @param string   $data["municipio"]              Municipio
     * @param string   $data["estado"]                 Estado
     * @param string   $data["pais"]                   Pais
     * @param string   $data["cep"]                    Cep. Formato 99999999
     * @param string   $data["token_senha"]            Token Senha : Token de Senha. Utilizado para reset de senha. Somente leitura
     * @param datetime $data["data_expiracao_token"]   Data Expiracao Token. Somente Leitura
     * @param bool     $data["conta_ativa"]            Conta Ativa : 1 = Ativo. 0 = Desativada. Somente leitura aqui. Somente Leitura
     * @param bool     $data["conta_bloqueada"]        Conta Bloqueada : [1 = Bloqueada/0 = Desbloqueada]. Somente leitura. Somente Leitura
     * @param int      $data["tentativas_login"]       Tentativas Login : Somente Leitura.
     * @param datetime $data["ultima_tentativa_login"] Ultima Tentativa Login : Somente Leitura
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 2018/05/10
     *
     * @return json object
     */
    public function setPerfilAPI()
    {
        $mensagem = array();

        $status = false;
        $message = null;
        $errors = array();

        try {
            $usuario = $this->Usuarios->getUsuarioById($this->Auth->user()["id"]);

            if ($this->request->is("post")) {

                $data = $this->request->getData();

                // DebugUtil::printArray($data);

                // validação de cpf
                if (isset($data["cpf"])) {
                    $result = NumberUtil::validarCPF($data["cpf"]);

                    if (!$result["status"]) {
                        $mensagem["status"] = false;
                        $mensagem["message"] = __($result["message"], $data["cpf"]);
                        $arraySet = ["mensagem"];

                        $this->set(compact($arraySet));
                        $this->set("_serialize", $arraySet);

                        return;
                    }
                }

                // validação de e-mail

                if (isset($data["email"])) {
                    $resultado = EmailUtil::validateEmail($data["email"]);

                    if (!$resultado["status"]) {
                        $mensagem = [
                            "status" => $resultado["status"],
                            "message" => __($resultado["message"], $data["email"])
                        ];

                        $arraySet = ["mensagem"];

                        $this->set(compact($arraySet));
                        $this->set("_serialize", $arraySet);

                        return;
                    }
                }

                // Faz o tratamento do envio da imagem ao servidor, se especificado

                if (isset($data["foto"])) {

                    $foto = $data["foto"];

                    $nomeImagem = $foto["image_name"];
                    $base64Imagem = $foto["value"];
                    $extensao = $foto["extension"];

                    $resultado = $this->generateImageFromBase64(
                        $base64Imagem,
                        Configure::read("temporaryDocumentUserPath") . $nomeImagem . "." . $extensao,
                        Configure::read("temporaryDocumentUserPath")
                    );

                    // Move o arquivo gerado
                    $fotoPerfil = $this->moveDocumentPermanently(
                        Configure::read("temporaryDocumentUserPath") . $nomeImagem . "." . $extensao,
                        Configure::read("documentUserPath"),
                        null,
                        $extensao
                    );

                    // Remove o array do item de gravação e passa a imagem
                    unset($data["foto"]);

                    $data["foto_perfil"] = $fotoPerfil;
                }

                // Remove os campos da atualizacao que não são permitidos fazer update

                if (isset($data["tipo_perfil"])) {
                    $data["tipo_perfil"] = $usuario["tipo_perfil"];
                }

                if (isset($data["senha"])) {
                    unset($data["senha"]);
                }

                if (isset($data["token_senha"])) {
                    unset($data["token_senha"]);
                }

                if (isset($data["data_expiracao_token"])) {
                    unset($data["data_expiracao_token"]);
                }

                if (isset($data["conta_ativa"])) {
                    unset($data["conta_ativa"]);
                }

                if (isset($data["conta_bloqueada"])) {
                    unset($data["conta_bloqueada"]);
                }

                if (isset($data["tentativas_login"])) {
                    unset($data["tentativas_login"]);
                }

                if (isset($data["ultima_tentativa_login"])) {
                    unset($data["ultima_tentativa_login"]);
                }

                // Faz o patch da entidade
                $usuario = $this->Usuarios->patchEntity($usuario, $data, ['validate' => 'EditUsuarioInfo']);

                $errors = $usuario->errors();

                // Gravação
                $usuario = $this->Usuarios->save($usuario);

                // Atualização com sucesso, retorna mensagem
                if ($usuario) {
                    $status = true;
                    $message = Configure::read("messageSavedSuccess");
                } else {
                    // Atualização com erro, retorna mensagem de erro.
                    $status = false;
                    $message = __("{0} Confira as informações e tente novamente.", Configure::read("messageSavedError"));
                }
            }
            $mensagem = ['status' => $status, 'message' => $message, 'errors' => $errors];
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $messageString = __("Erro ao atualizar dados de usuário!");

            $errors = $trace;
            $mensagem = ['status' => false, 'message' => $messageString, 'errors' => $errors];

            $messageStringDebug = __("{0} - {1} em: {2}. [Função: {3} / Arquivo: {4} / Linha: {5}]  ", $messageString, $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write("error", $messageStringDebug);
        }

        $arraySet = [
            "mensagem",
            "usuario"
        ];

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);
    }

    /**
     * Adiciona Conta de usuário
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function registrar()
    {
        $usuario = $this->Usuarios->newEntity();
        $transportadora = $this->Transportadoras->newEntity();
        $veiculo = $this->Veiculos->newEntity();

        $user_admin = $this->request->session()->read('User.RootLogged');
        $user_managed = $this->request->session()->read('User.ToManage');

        if ($user_admin) {
            $this->user_logged = $user_managed;
        }

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();

            $usuarioData = $data;

            $cliente = null;

            if (isset($this->user_logged)) {

                $cliente_has_usuario =
                    $this->ClientesHasUsuarios->findClienteHasUsuario(
                    [
                        'ClientesHasUsuarios.usuarios_id' => $this->user_logged['id'],
                        'ClientesHasUsuarios.tipo_perfil' => $this->user_logged['tipo_perfil']
                    ]
                )->first();

                $cliente_id = isset($cliente_has_usuario) ? $cliente_has_usuario->clientes_id : null;

                $client_to_manage = $this->request->session()->read('ClientToManage');

                $transportadoraData = $usuarioData['TransportadorasHasUsuarios']['Transportadoras'];
                $veiculosData = $usuarioData['UsuariosHasVeiculos']['Veiculos'];
            }

            unset($usuarioData['TransportadorasHasUsuarios']);
            unset($usuarioData['UsuariosHasVeiculos']);
            unset($usuarioData['transportadora']);

            if ($usuarioData['doc_invalido'] == true) {
                $nomeDoc = strlen($usuarioData['cpf']) > 0 ? $usuarioData['cpf'] : $usuarioData['doc_estrangeiro'];
                $nomeDoc = $this->cleanNumberAndLetters($nomeDoc);
                $nomeDoc = $nomeDoc . '.jpg';

                $currentPath = Configure::read('temporaryDocumentUserPath');
                $newPath = Configure::read('documentUserPath');

                $fotoDocumento = $this->moveDocumentPermanently($currentPath . $nomeDoc, $newPath, null, '.jpg');

                $usuarioData['foto_documento'] = $fotoDocumento;
                $usuarioData['aguardando_aprovacao'] = true;
                $usuarioData['data_limite_aprovacao'] = date('Y-m-d H:i:s', strtotime('+7 days'));
            }

            if (isset($this->user_logged)) {
                $veiculoDataBase = $this->Veiculos->getVeiculoByPlaca($veiculosData['placa']);

                if ($veiculosData) {
                    if ($veiculoDataBase) {
                        $veiculo = $veiculoDataBase;
                    } else {
                        $veiculo = $this->Veiculos->patchEntity($veiculo, $veiculosData);
                        $veiculo = $this->Veiculos->save($veiculo);
                    }
                }

                if (strlen($transportadoraData['cnpj']) > 0 || strlen($transportadoraData['nome_fantasia']) > 0 || strlen($transportadoraData['razao_social']) > 0) {
                    $transportadoraDataBase = $this->Transportadoras->findTransportadoraByCNPJ($transportadoraData['cnpj']);

                    if ($transportadoraDataBase) {
                        $transportadora = $transportadoraDataBase;
                    } else {
                        $transportadora = $this->Transportadoras->patchEntity($transportadora, $transportadoraData);

                        $transportadora = $this->Transportadoras->save($transportadora);
                    }
                }
            }

            $password_encrypt = $this->crypt_util->encrypt($usuarioData['senha']);

            if (strlen($usuarioData['doc_estrangeiro']) > 0) {
                $usuario = $this->Usuarios->patchEntity($usuario, $usuarioData, [
                    'validate' => 'CadastroEstrangeiro'
                ]);
            } else {
                $usuario = $this->Usuarios->patchEntity($usuario, $usuarioData, ['validate' => 'Default']);
            }

            if (strlen($usuarioData['doc_estrangeiro']) == 0 && strlen($usuarioData['cpf']) == 0) {
                $this->Flash->error(__("Deve ser informado o CPF ou Documentação Estrangeira do novo usuário!"));

                $this->set(compact(['usuario']));
                $this->set("_serialize", ['usuario']);

                return;
            }
            $errors = $usuario->errors();

            $usuario = $this->Usuarios->save($usuario);

            if ($usuario) {
                // guarda uma senha criptografada de forma diferente no DB (para acesso externo)
                $this->UsuariosEncrypted->setUsuarioEncryptedPassword($usuario['id'], $password_encrypt);

                if (isset($this->user_logged)) {
                    if ($transportadora) {
                        $this->TransportadorasHasUsuarios->addTransportadoraHasUsuario($transportadora->id, $usuario->id);
                    }

                    if ($veiculo) {
                        $this->UsuariosHasVeiculos->addUsuarioHasVeiculo($veiculo->id, $usuario->id);
                    }

                    if (isset($cliente_id)) {
                        $this->ClientesHasUsuarios->saveClienteHasUsuario($cliente_id, $usuario->id, $usuario->tipo_perfil);
                    }
                }

                $this->Flash->success(__('O usuário foi criado com sucesso.'));

                if (isset($this->user_logged)) {

                    if ($this->user_logged['tipo_perfil'] == (int)Configure::read('profileTypes')['AdminDeveloperProfileType']) {
                        return $this->redirect(['action' => 'index']);
                    } else if ($this->user_logged['tipo_perfil'] >= (int)Configure::read('profileTypes')['AdminDeveloperProfileType'] && $this->user_logged['tipo_perfil'] <= (int)Configure::read('profileTypes')['ManagerProfileType']) {
                        return $this->redirect(['action' => 'meus_clientes']);
                    } else {
                        return $this->redirect(['controller' => 'pages', 'action' => 'index']);
                    }
                } else {
                    return $this->redirect(
                        [
                            'controller' => 'usuarios',
                            'action' => 'login'
                        ]
                    );
                }
            } else {
                $this->Flash->error(__('O usuário não pode ser registrado.'));

                foreach ($errors as $key => $error) {
                    $this->Flash->error($key . ": " . implode(",", $error));
                }
            }
        }

        $usuario_logado_tipo_perfil = (int)Configure::read('profileTypes')['UserProfileType'];

        $this->set(compact(['usuario', 'usuario_logado_tipo_perfil']));
        $this->set('_serialize', ['usuario', 'transportadora']);
        $this->set('transportadoraPath', 'TransportadorasHasUsuarios.Transportadoras.');
        $this->set('veiculoPath', 'UsuariosHasVeiculos.Veiculos.');
        $this->set('user_logged', $this->user_logged);
    }

    /**
     * Adiciona Conta de usuário
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function registrarAPI()
    {
        $usuario = $this->Usuarios->newEntity();

        $mensagem = [];

        $usuarioRegistrado = null;

        $errors = array();

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();

            $tipoPerfil = isset($data["tipo_perfil"]) ? $data["tipo_perfil"] : null;

            if (isset($tipoPerfil) && $tipoPerfil >= Configure::read("profileTypes")["DummyWorkerProfileType"]) {
                // Funcionário ou usuário fictício não precisa de validação de cpf

                $this->Usuarios->validator()->remove('cpf');
            } else {
                $data['tipo_perfil'] = (int)Configure::read('profileTypes')['UserProfileType'];
            }
            $data["doc_invalido"] = false;

             // validação de cpf

            $canContinue = false;

            // Remove os campos da inserção que não são permitidos ao fazer insert

            if (isset($data["token_senha"])) {
                unset($data["token_senha"]);
            }

            if (isset($data["data_expiracao_token"])) {
                unset($data["data_expiracao_token"]);
            }

            if (isset($data["conta_ativa"])) {
                unset($data["conta_ativa"]);
            }

            if (isset($data["conta_bloqueada"])) {
                unset($data["conta_bloqueada"]);
            }

            if (isset($data["tentativas_login"])) {
                unset($data["tentativas_login"]);
            }

            if (isset($data["ultima_tentativa_login"])) {
                unset($data["ultima_tentativa_login"]);
            }

            if (!isset($data["email"])) {
                $errors[] = array("email" => "Email deve ser informado!");
                $canContinue = false;
            } else {
                $resultado = EmailUtil::validateEmail($data["email"]);

                if (!$resultado["status"]) {
                    $mensagem = [
                        "status" => $resultado["status"],
                        "message" => __($resultado["message"], $data["email"])
                    ];

                    $arraySet = ["mensagem"];

                    $this->set(compact($arraySet));
                    $this->set("_serialize", $arraySet);

                    return;
                }
                $canContinue = true;
            }

            if (!isset($data["cpf"]) && $tipoPerfil < (int)Configure::read("DummyWorkerProfileType")) {
                $errors[] = array("CPF" => "CPF Deve ser informado!");
                $canContinue = false;
            } else {

                // Valida se o usuário em questão não é ficticio
                if ($tipoPerfil < (int)Configure::read("DummyWorkerProfileType")) {

                    $result = NumberUtil::validarCPF($data["cpf"]);

                    if (!$result["status"]) {
                        $mensagem["status"] = false;
                        $mensagem["message"] = __($result["message"], $data["cpf"]);
                        $arraySet = ["mensagem"];

                        $this->set(compact($arraySet));
                        $this->set("_serialize", $arraySet);

                        return;
                    }
                }


                $canContinue = true;
            }

            // Faz o tratamento do envio da imagem ao servidor, se especificado

            if (isset($data["foto"])) {

                $foto = $data["foto"];

                $nomeImagem = $foto["image_name"];
                $base64Imagem = $foto["value"];
                $extensao = $foto["extension"];

                $resultado = $this->generateImageFromBase64(
                    $base64Imagem,
                    Configure::read("temporaryDocumentUserPath") . $nomeImagem . "." . $extensao,
                    Configure::read("temporaryDocumentUserPath")
                );

                // Move o arquivo gerado
                $fotoPerfil = $this->moveDocumentPermanently(
                    Configure::read("temporaryDocumentUserPath") . $nomeImagem . "." . $extensao,
                    Configure::read("documentUserPath"),
                    null,
                    $extensao
                );

                // Remove o array do item de gravação e passa a imagem
                unset($data["foto"]);

                $data["foto_perfil"] = $fotoPerfil;
            }

            $usuarioData = $data;

            // verifica se o usuário já está registrado

            $usuarioJaExiste = $this->Usuarios->getUsuarioByEmail($usuarioData['email']);

            if ($canContinue) {

            // verifica se usuário já existe no sistema
                if ($usuarioJaExiste) {
                    $mensagem = [
                        'status' => false,
                        'message' => "Usuário " . $usuarioData['email'] . " já existe no sistema!"
                    ];
                } else {
                // senão, grava no banco

                    $password_encrypt = $this->crypt_util->encrypt($usuarioData['senha']);
                    $usuario = $this->Usuarios->patchEntity($usuario, $usuarioData);

                    foreach ($usuario->errors() as $key => $erro) {
                        $errors[] = $erro;
                    }

                    $usuario = $this->Usuarios->save($usuario);

                    if ($usuario) {
                        // Realiza login de autenticação
                        $usuario = [
                            'id' => $usuario->id,
                            'token' => JWT::encode(
                                [
                                    'sub' => $usuario->id,
                                    'exp' => time() + 604800
                                ],
                                Security::salt()
                            )
                        ];

                        $mensagem = array(
                            "status" => true,
                            "message" => "Usuário registrado com sucesso!",
                            "errors" => $errors
                        );
                    } else {
                        $mensagem = [
                            'status' => false,
                            'message' => __("{0}, {1}", Configure::read('messageGenericCompletedError'), Configure::read('messageGenericCheckFields')),
                            'errors' => $errors
                        ];
                    }
                }
            } else {
                $mensagem = [
                    'status' => false,
                    'message' => __("{0}, {1}", Configure::read('messageGenericCompletedError'), Configure::read('messageGenericCheckFields')),
                    'errors' => $errors
                ];
            }
        }

        $arraySet = [
            'usuario',
            'mensagem'
        ];

        $this->set(compact($arraySet));
        $this->set('_serialize', $arraySet);
    }

    /**
     * Obtêm token de autenticação
     *
     * @return void
     */
    public function loginAPI()
    {
        $usuario = $this->Auth->identify();

        if (!$usuario) {
            throw new UnauthorizedException('Usuário ou senha inválidos');
        }

        $mensagem = [
            'status' => true,
            'message' => Configure::read('messageUserLoggedInSuccessfully')
        ];


        $usuario = [
            'id' => $usuario['id'],
            'token' => JWT::encode(
                [
                    'id' => $usuario['id'],
                    'sub' => $usuario['id'],
                    'exp' => time() + 604800
                ],
                Security::salt()
            )
        ];

        $arraySet = [
            'mensagem',
            'usuario'
        ];

        $this->set(compact($arraySet));
        $this->set('_serialize', $arraySet);
    }

    /**
     * Obtêm token de autenticação
     *
     * @return void
     */
    public function logoutAPI()
    {
        $usuario = $this->Auth->user();

        if (!$usuario) {
            throw new UnauthorizedException('Usuário ou senha inválidos');
        }

        $mensagem = [
            'status' => true,
            'message' => Configure::read('messageUserLoggedOutSuccessfully')
        ];


        $usuario = [
            'id' => $usuario['id'],
            'token' => JWT::encode(
                [
                    'id' => $usuario['id'],
                    'sub' => $usuario['id'],
                    'exp' => time() + 1
                ],
                Security::salt()
            )
        ];

        $this->Auth->logout();

        $arraySet = [
            'mensagem'
        ];

        $this->set(compact($arraySet));
        $this->set('_serialize', $arraySet);
    }

    /**
     * Edit method
     *
     * @param string|null $id Usuario id.
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

            $usuario = $this->Usuarios->getUsuarioById($id);

            $redes = [];

            $redesConditions = [];

            $clientesHasUsuariosWhere = [];

            if ($usuario->tipo_perfil != (int)Configure::read('profileTypes')['AdminDeveloperProfileType']) {

                array_push($clientesHasUsuariosWhere, ['ClientesHasUsuarios.usuarios_id' => $id]);
                array_push($clientesHasUsuariosWhere, ['ClientesHasUsuarios.tipo_perfil' => $usuario->tipo_perfil]);

                $clientesHasUsuariosQuery = $this->ClientesHasUsuarios->findClienteHasUsuario($clientesHasUsuariosWhere);

                if (sizeof($clientesHasUsuariosQuery->toArray()) > 0) {
                // tenho o cliente alocado, pegar agora a rede que ele está
                    $clienteHasUsuario = $clientesHasUsuariosQuery->toArray()[0];
                    $cliente = $clienteHasUsuario->cliente;

                    $redeHasCliente = $this->RedesHasClientes->getRedesHasClientesByClientesId($clienteHasUsuario->clientes_id);

                    $rede = $redeHasCliente->rede;
                }

            }
            // pegar a rede a qual se encontra o usuário
            if (isset($redes_id)) {
                $redesConditions[] = ['id' => $redes_id];
            }

            if ($this->user_logged['tipo_perfil'] == Configure::read('profileTypes')['AdminDeveloperProfileType']) {
                $redes = $this->Redes->getRedesList($redesConditions);
            } else if ($this->user_logged['tipo_perfil'] == Configure::read('profileTypes')['AdminNetworkProfileType']) {
                // pega o Id de cliente que o usuário se encontra
                // AdminLocalProfileType

                // TODO: terminar de ajustar
                $clienteId = $this->RedesHasClientesAdministradores->getRedesHasClientesAdministradorByUsuariosId($this->user_logged['id']);
            }

            if ($this->request->is(['post', 'put'])) {

                if ($this->user_logged['tipo_perfil'] == Configure::read('profileTypes')['AdminDeveloperProfileType']) {
                    $this->Usuarios->validator('EditUsuarioInfo')->remove('cpf');
                }

                // verifica se o usuário está na mesma rede,
                // e/ou se o perfil é o mesmo. caso contrário, atualiza

                $usuario_compare = $this->request->getData();

                if ($usuario->tipo_perfil != (int)Configure::read('profileTypes')['AdminDeveloperProfileType']) {
                    if (!empty($clienteHasUsuario)) {
                        if ($clienteHasUsuario->clientes_id != $usuario_compare['clientes_id']
                            || $clienteHasUsuario->tipo_perfil != $usuario_compare['tipo_perfil']) {
                            $this->ClientesHasUsuarios->updateClienteHasUsuarioRelationship($clienteHasUsuario->id, (int)$usuario_compare['clientes_id'], $usuario_compare['id'], (int)$usuario_compare['tipo_perfil']);
                        }
                    }
                }

                $usuario = $this->Usuarios->patchEntity($usuario, $this->request->getData(), ['validate' => 'EditUsuarioInfo']);

                unset($usuario['senha']);

                if ($this->Usuarios->save($usuario)) {
                    $this->Flash->success(__('O usuário foi gravado com sucesso.'));

                    if ($this->user_logged['tipo_perfil'] == Configure::read('profileTypes')['AdminDeveloperProfileType']) {
                        $url = Router::url(['controller' => 'Usuarios', 'action' => 'index']);

                        return $this->response = $this->response->withLocation($url);
                    } else {
                        $url = Router::url(['controller' => 'Usuarios', 'action' => 'meu_perfil']);

                        return $this->response = $this->response->withLocation($url);
                    }
                } else {
                    $this->Flash->error(__('O usuário não pode ser atualizado.'));
                }
            }
            $usuarioLogadoTipoPerfil = $this->user_logged['tipo_perfil'];

            $arraySet = array('usuario', 'usuarioLogadoTipoPerfil', 'rede', 'redes', 'redesId', 'cliente');
            $this->set(compact($arraySet));
            $this->set('_serialize', $arraySet);
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao realizar edição de dados de usuário: {0} em: {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Delete method
     *
     * @param string|null $id Usuario id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete()
    {
        $data = $this->request->query();
        $usuario_id = $data['usuario_id'];
        $return_url = $data['return_url'];

        $this->request->allowMethod(['post', 'delete']);
        $usuario = $this->Usuarios->get($usuario_id);

        // se o usuário a ser removido é usuário, remove todos os registros à ele vinculados
        if ($usuario->tipo_perfil == (int)Configure::read('profileTypes')['UserProfileType']) {

            $this->UsuariosHasBrindes->deleteAllUsuariosHasBrindesByUsuariosId($usuario->id);
            $this->UsuariosHasVeiculos->deleteAllUsuariosHasVeiculosByUsuariosId($usuario->id);
            $this->TransportadorasHasUsuarios->deleteAllTransportadorasHasUsuariosByUsuariosId($usuario->id);
            $this->PontuacoesPendentes->deleteAllPontuacoesPendentesByUsuariosId($usuario->id);
            $this->Pontuacoes->deleteAllPontuacoesByUsuariosId($usuario->id);
            $this->PontuacoesComprovantes->deleteAllPontuacoesComprovantesByUsuariosId($usuario->id);
            $this->Cupons->deleteAllCuponsByUsuariosId($usuario->id);

            $this->ClientesHasUsuarios->deleteAllClientesHasUsuariosByUsuariosId($usuario->id);

            $this->UsuariosEncrypted->deleteUsuariosEncryptedByUsuariosId($usuario->id);

            $this->Usuarios->delete($usuario);

            $this->Flash->success(__(Configure::read('messageDeleteSuccess')));
            return $this->redirect($return_url);
        } else if ($usuario->tipo_perfil == (int)Configure::read('profileTypes')['AdminDeveloperProfileType']) {

            // admin rti / desenvolvedor não há problema
            // em ser removido, pois não realizam atendimento

            if ($this->Usuarios->delete($usuario)) {
                $this->Flash->success(__(Configure::read('messageDeleteSuccess')));
                return $this->redirect($return_url);
            }
        } else {

            // se não for usuário final, deve-se pesquisar um outro funcionário da mesma unidade
            // se não houver, informar que não é possível remover o único funcionário da rede, que o melhor a se fazer é desabilitá-lo

            // verifica se é o único funcionário da unidade é o único cadastrado

            // primeiro, descobre o código da unidade

            $unidade_usuario = $this->ClientesHasUsuarios->findClienteHasUsuario(['ClientesHasUsuarios.usuarios_id' => $usuario->id, 'ClientesHasUsuarios.tipo_perfil' => $usuario->tipo_perfil])->first();

            // com o código da unidade, verifica se há outro usuário vinculado

            $clientes_has_usuarios = $this->ClientesHasUsuarios->findClienteHasUsuario(
                [
                    'ClientesHasUsuarios.clientes_id' => $unidade_usuario->clientes_id,
                    'ClientesHasUsuarios.tipo_perfil <' => (int)Configure::read('profileTypes')['UserProfileType'],
                    'ClientesHasUsuarios.usuarios_id != ' => $usuario->id
                ]
            )->first();

            if (isset($clientes_has_usuarios)) {

                $usuario_destino_id = $clientes_has_usuarios->usuarios_id;

                // remove / atualiza todas as tabelas necessárias

                $this->UsuariosHasBrindes->deleteAllUsuariosHasBrindesByUsuariosId($usuario->id);

                $this->UsuariosHasVeiculos->deleteAllUsuariosHasVeiculosByUsuariosId($usuario->id);

                $this->TransportadorasHasUsuarios->deleteAllTransportadorasHasUsuariosByUsuariosId($usuario->id);

                $this->PontuacoesPendentes->updateAllPontuacoesPendentes(['funcionarios_id' => $usuario_destino_id], ['funcionarios_id' => $usuario->id]);

                $this->Pontuacoes->updateAllPontuacoes(['funcionarios_id' => $usuario_destino_id], ['funcionarios_id' => $usuario->id]);

                $this->PontuacoesComprovantes->updateAllPontuacoesComprovantes(['funcionarios_id' => $usuario_destino_id], ['funcionarios_id' => $usuario->id]);

                // realiza a exclusão do funcionário
                $this->Cupons->deleteAllCuponsByUsuariosId($usuario->id);

                $this->ClientesHasUsuarios->deleteAllClientesHasUsuariosByUsuariosId($usuario->id);

                $this->UsuariosEncrypted->deleteUsuariosEncryptedByUsuariosId($usuario->id);

                $this->Usuarios->delete($usuario);

                $this->Flash->success(__(Configure::read('messageDeleteSuccess')));
                return $this->redirect($return_url);

            } else {
                // não há outro usuário, então deve-se verificar se há alguma informação vinculada à ele. se não houver, pode remover.

                $found = false;

                $clientes_has_brindes_estoque = $this->ClientesHasBrindesEstoque->find('all')->where(['usuarios_id' => $usuario->id])->toArray();

                $found = sizeof($clientes_has_brindes_estoque) > 0 ? true : false;

                if (!$found) {
                    $cupons = $this->Cupons->find('all')->where(['usuarios_id' => $usuario->id])->toArray();

                    $found = sizeof($cupons) > 0 ? true : false;
                }

                if (!$found) {
                    $pontuacoes = $this->Pontuacoes->find('all')->where(['funcionarios_id' => $usuario->id])->toArray();

                    $found = sizeof($pontuacoes) > 0 ? true : false;
                }

                if (!$found) {
                    $pontuacoes_comprovantes = $this->PontuacoesComprovantes->find('all')->where(['funcionarios_id' => $usuario->id])->toArray();

                    $found = sizeof($pontuacoes_comprovantes) > 0 ? true : false;
                }

                if (!$found) {
                    $pontuacoes_pendentes = $this->PontuacoesPendentes->find('all')->where(['funcionarios_id' => $usuario->id])->toArray();

                    $found = sizeof($pontuacoes_pendentes) > 0 ? true : false;
                }

                if ($found) {
                    // isso significa que não é permitido remover o
                    // usuário em questão, pois não tem como migrar os dados cadastrados
                    $this->Flash->error(__("O usuário não pode ser deletado, pois é o único da unidade. Os dados dos clientes ficarão 'órfãos', e por isto, a operação não é permitida."));

                    return $this->redirect(['action' => $return_url]);
                } else {
                    // realiza a remoção do usuário

                    $this->UsuariosHasBrindes->deleteAllUsuariosHasBrindesByUsuariosId($usuario->id);
                    $this->UsuariosHasVeiculos->deleteAllUsuariosHasVeiculosByUsuariosId($usuario->id);
                    $this->TransportadorasHasUsuarios->deleteAllTransportadorasHasUsuariosByUsuariosId($usuario->id);
                    $this->PontuacoesPendentes->deleteAllPontuacoesPendentesByUsuariosId($usuario->id);
                    $this->Pontuacoes->deleteAllPontuacoesByUsuariosId($usuario->id);
                    $this->PontuacoesComprovantes->deleteAllPontuacoesComprovantesByUsuariosId($usuario->id);
                    $this->Cupons->deleteAllCuponsByUsuariosId($usuario->id);

                    $this->UsuariosEncrypted->deleteUsuariosEncryptedByUsuariosId($usuario->id);

                    $this->ClientesHasUsuarios->deleteAllClientesHasUsuariosByUsuariosId($usuario->id);

                    $this->Usuarios->delete($usuario);

                    $this->Flash->success(__(Configure::read('messageDeleteSuccess')));
                    return $this->redirect($return_url);
                }
            }

        }
    }

    /**
     * Adiciona conta de usuário (cliente final, usado por um funcionário)
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function adicionarConta(int $redes_id = null)
    {
        $usuario = $this->Usuarios->newEntity();

        $transportadora = $this->Usuarios->TransportadorasHasUsuarios->newEntity();
        $veiculo = $this->Usuarios->UsuariosHasVeiculos->newEntity();

        $user_admin = $this->request->session()->read('User.RootLogged');
        $user_managed = $this->request->session()->read('User.ToManage');

        if ($user_admin) {
            $this->user_logged = $user_managed;
        }

        $rede = $this->request->session()->read('Network.Main');

        $client_to_manage = $this->request->session()->read('ClientToManage');

        $redes = [];

        $redes_conditions = [];

        // Pega unidades que tem acesso
        $clientes_ids = [];

        $unidades_ids = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($rede->id, $this->user_logged['id'], false);

        foreach ($unidades_ids as $key => $value) {
            $clientes_ids[] = $key;
        }

        // No caso do funcionário, ele só estará em uma unidade, então pega o cliente que ele estiver

        $cliente = $this->Clientes->getClienteById($clientes_ids[0]);

        if (isset($redes_id)) {
            $redes_conditions[] = ['id' => $redes_id];
        }

        if ($this->user_logged['tipo_perfil'] == Configure::read('profileTypes')['AdminDeveloperProfileType']) {

            if (is_null($redes_id) && isset($rede)) {
                $redes_id = $rede->id;
            }

            if (isset($redes_id)) {
                $rede = $this->Redes->getRedeById($redes_id);
            }

            $redes = $this->Redes->getRedesList($redes_conditions);
        }

        if ($this->request->is('post')) {
            $data = $this->request->getData();

            $usuarioData = $data;

            // guarda qual é a unidade que está sendo cadastrada
            $clientes_id = $cliente->id;

            $tipo_perfil = $data['tipo_perfil'];

            $cliente = null;
            if (isset($this->user_logged)) {
                // só vincula usuário à uma rede se for tipo de perfil entre
                // AdminNetworkProfileType e WorkerProfileType

                if ($this->user_logged['tipo_perfil'] >= Configure::read('profileTypes')['AdminNetworkProfileType']
                    && $this->user_logged['tipo_perfil'] <= Configure::read('profileTypes')['WorkerProfileType']) {
                    $cliente = $this->Clientes->getClienteMatrizLinkedToUsuario($this->user_logged);
                }

                // Cadastra o usuário no cliente da session,
                // se estiver em modo de Admin

                if (!is_null($client_to_manage)) {
                    $cliente = $client_to_manage;
                }

                $transportadoraData = null;
                $veiculosData = null;

                if (isset($usuarioData['TransportadorasHasUsuarios'])) {
                    $transportadoraData = $usuarioData['TransportadorasHasUsuarios']['Transportadoras'];
                }

                if (isset($usuarioData['UsuariosHasVeiculos'])) {
                    $veiculosData = $usuarioData['UsuariosHasVeiculos']['Veiculos'];
                }
            }

            unset($usuarioData['TransportadorasHasUsuarios']);
            unset($usuarioData['UsuariosHasVeiculos']);
            unset($usuarioData['transportadora']);

            $usuario['matriz_id'] = $cliente['id'];

            if ($usuarioData['doc_invalido'] == true) {
                if (isset($usuarioData['cpf'])) {
                    $nomeDoc = strlen($usuarioData['cpf']) > 0 ? $usuarioData['cpf'] : $usuarioData['doc_estrangeiro'];

                    $nomeDoc = $this->cleanNumberAndLetters($nomeDoc);
                } else {
                    $nomeDoc = $usuarioData['doc_estrangeiro'];
                }

                $nomeDoc = $nomeDoc . '.jpg';

                $currentPath = Configure::read('temporaryDocumentUserPath');
                $newPath = Configure::read('documentUserPath');

                $fotoDocumento = $this->moveDocumentPermanently($currentPath . $nomeDoc, $newPath, null, '.jpg');

                $usuarioData['foto_documento'] = $fotoDocumento;
                $usuarioData['aguardando_aprovacao'] = true;
                $usuarioData['data_limite_aprovacao'] = date('Y-m-d H:i:s', strtotime('+7 days'));
            }

            if (isset($this->user_logged)) {
                $veiculoDataBase = $this->Veiculos->getVeiculoByPlaca($veiculosData['placa']);

                if ($veiculoDataBase) {
                    $veiculo = $veiculoDataBase;
                } elseif (isset($veiculosData)) {
                    $veiculo = $this->Veiculos->patchEntity($veiculo, $veiculosData);
                    $veiculo = $this->Veiculos->save($veiculo);
                }

                if (strlen($transportadoraData['cnpj']) > 0 || strlen($transportadoraData['nome_fantasia']) > 0 || strlen($transportadoraData['razao_social']) > 0) {
                    $transportadoraDataBase = $this->Transportadoras->findTransportadoraByCNPJ($transportadoraData['cnpj']);

                    if ($transportadoraDataBase) {
                        $transportadora = $transportadoraDataBase;
                    } else {
                        $transportadora = $this->Transportadoras->patchEntity($transportadora, $transportadoraData);

                        $transportadora = $this->Transportadoras->save($transportadora);
                    }
                }
            }

            if (strlen($usuarioData['doc_estrangeiro']) > 0) {
                $usuario
                    = $this->Usuarios->patchEntity(
                    $usuario,
                    $usuarioData,
                    [
                        'validate' => 'CadastroEstrangeiro'
                    ]
                );

                // assegura que em um cadastro de estrangeiro, não tenha CPF vinculado.
                $usuario['cpf'] = null;
            } else {
                $usuario
                    = $this->Usuarios->patchEntity(
                    $usuario,
                    $usuarioData
                );
            }

            $password_encrypt = $this->crypt_util->encrypt($usuarioData['senha']);

            $usuario = $this->Usuarios->formatUsuario(0, $usuario);

            $errors = $usuario->errors();

            if ($usuario = $this->Usuarios->save($usuario)) {
                // guarda uma senha criptografada de forma diferente no DB (para acesso externo)
                $this->UsuariosEncrypted->setUsuarioEncryptedPassword($usuario['id'], $password_encrypt);

                if ($transportadora) {
                    $this->TransportadorasHasUsuarios->addTransportadoraHasUsuario($transportadora->id, $usuario->id);
                }

                if ($veiculo) {
                    $this->UsuariosHasVeiculos->addUsuarioHasVeiculo($veiculo->id, $usuario->id);
                }

                // a vinculação só será feita se não for um Admin RTI
                if ($tipo_perfil != Configure::read('profileTypes')['AdminDeveloperProfileType']) {

                    if ($tipo_perfil == Configure::read('profileTypes')['AdminNetworkProfileType']) {

                        // Se usuário for administrador geral da rede, guarda na tabela de redes_has_clientes_administradores

                        // ele ficará alocado na matriz
                        if ($clientes_id == "") {
                            $rede_has_cliente = $this->RedesHasClientes->findMatrizOfRedesByRedesId($rede->id);

                            $clientes_id = $rede_has_cliente->clientes_id;
                        }

                        $rede_has_cliente = $this->RedesHasClientes->getRedesHasClientesByClientesId($clientes_id);

                        $result = $this->RedesHasClientesAdministradores->addRedesHasClientesAdministradores(
                            $rede_has_cliente->id,
                            $usuario->id
                        );
                    }

                    /**
                     * Agora vincula o usuário ao cliente. Se for cliente final,
                     * será considerado um 'consumidor', caso contrário,
                     * será considerado equipe
                     */

                    $this->ClientesHasUsuarios->saveClienteHasUsuario($clientes_id, $usuario->id, $usuario->tipo_perfil);
                }

                $this->Flash->success(__('O usuário foi salvo.'));

                // se o id da rede está definido, volta para o cadastro da rede
                // caso contrário, volta para o índex de administrador


                // se quem está fazendo o cadastro é administrador da rede até gerente
                if ($this->user_logged['tipo_perfil'] >= (Configure::read('profileTypes')['AdminNetworkProfileType'])
                    && $this->user_logged['tipo_perfil'] <= Configure::read('profileTypes')['ManagerProfileType']) {

                    // se cadastrou um usuário, retorna à meus clientes,
                    // caso contrário, retorna à usuários da rede
                    if ($usuario['tipo_perfil'] == Configure::read('profileTypes')['UserProfileType']) {
                        return $this->redirect(['action' => 'meus_clientes']);
                    } else {
                        return $this->redirect(['action' => 'usuarios_rede', $redes_id]);
                    }
                } else {
                    // se é administrador rti
                    if ($this->user_logged['tipo_perfil'] == Configure::read('profileTypes')['AdminDeveloperProfileType']) {
                        // se está mexendo em um cadastro de rede,
                        // redireciona para os usuários daquela rede
                        if (isset($redes_id)) {
                            return $this->redirect(['action' => 'usuarios_rede', $redes_id]);
                        }
                        return $this->redirect(['action' => 'index']);
                    } else {
                        return $this->redirect(['controller' => 'pages', 'action' => 'index']);
                    }
                }
            }

            $this->Flash->error(__('O usuário não pode ser registrado. '));

            // exibe os erros logo acima identificados
            foreach ($errors as $key => $error) {
                $key = key($error);
                $this->Flash->error(__("{0}", $error[$key]));
            }
        }

        // $usuario_logado_tipo_perfil = $this->user_logged['tipo_perfil'];
        // na verdade, o perfil deverá ser 6, pois no momento do cadastro do funcionário
        // $usuario_logado_tipo_perfil = $funcionario->tipo_perfil;
        $usuario_logado_tipo_perfil = 6;
        $this->set(compact(['usuario', 'rede', 'redes', 'redes_id', 'usuario_logado_tipo_perfil']));
        $this->set('_serialize', ['usuario', 'rede', 'redes', 'redes_id', 'usuario_logado_tipo_perfil']);

        $this->set('transportadoraPath', 'TransportadorasHasUsuarios.Transportadoras.');
        $this->set('veiculoPath', 'UsuariosHasVeiculos.Veiculos.');
    }

    /**
     * Adiciona conta de usuário (cliente final, usado por um funcionário)
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function adicionarOperador()
    {
        $usuario = $this->Usuarios->newEntity();

        $rede = $this->request->session()->read("Network.Main");
        $redes_id = $rede["id"];

        $user_admin = $this->request->session()->read('User.RootLogged');
        $user_managed = $this->request->session()->read('User.ToManage');

        if ($user_admin) {
            $this->user_logged = $user_managed;
        }

        $usuario_logado_tipo_perfil = $this->user_logged['tipo_perfil'];

        $user_logged = $this->user_logged;

        $rede = $this->request->session()->read('Network.Main');

        $client_to_manage = $this->request->session()->read('ClientToManage');

        $redes = [];

        $redes_conditions = [];

        if (isset($redes_id)) {
            $redes_conditions[] = ['id' => $redes_id];
        }

        if ($this->user_logged['tipo_perfil'] == Configure::read('profileTypes')['AdminDeveloperProfileType']) {

            if (is_null($redes_id) && isset($rede)) {
                $redes_id = $rede->id;
            }

            if (isset($redes_id)) {
                $rede = $this->Redes->getRedeById($redes_id);
            }

        }

        $redes = $this->Redes->getRedesList($redes_conditions);

        if ($this->request->is('post')) {
            $data = $this->request->getData();

            $usuarioData = $data;

            // guarda qual é a unidade que está sendo cadastrada
            $clientes_id = (int)$data['clientes_id'];

            $tipo_perfil = $data['tipo_perfil'];

            $cliente = null;

            // Se quem está cadastrando é um Funcionário >= Administrador Comum, pega o local onde o Funcionário está e vincula ao mesmo lugar.

            if ($user_logged['tipo_perfil'] >= Configure::read('profileTypes')['AdminLocalProfileType']
                && $user_logged['tipo_perfil'] <= Configure::read('profileTypes')['WorkerProfileType']) {

                $cliente = $this->request->session()->read('Network.Unit');

                $data['clientes_id'] = $cliente->id;
                $clientes_id = $cliente->id;

            }

            // Se o tipo de perfil não for Administrador de Rede Regional ao menos, o Usuário deve estar vinculado à uma unidade!
            // Regional já está em algum lugar, pois antes ele foi um Administrador Comum!

            if ($usuarioData['tipo_perfil'] > Configure::read('profileTypes')['AdminRegionalProfileType']
                && strlen($data['clientes_id']) == 0) {
                $this->Flash->error(Configure::read('messageUserRegistrationClientNotNull'));

                $arraySet = [
                    'usuario',
                    'rede',
                    'redes',
                    'redes_id',
                    'usuario_logado_tipo_perfil',
                    'user_logged'
                ];

                $this->set(compact($arraySet));
                $this->set('_serialize', $arraySet);

                return;
            }

            if (isset($this->user_logged)) {
                // só vincula usuário à uma rede se for tipo de perfil entre
                // AdminNetworkProfileType e WorkerProfileType

                if ($this->user_logged['tipo_perfil'] >= Configure::read('profileTypes')['AdminNetworkProfileType']
                    && $this->user_logged['tipo_perfil'] <= Configure::read('profileTypes')['WorkerProfileType']) {
                    $cliente = $this->Clientes->getClienteMatrizLinkedToUsuario($this->user_logged);
                }

                // Cadastra o usuário no cliente da session,
                // se estiver em modo de Admin

                if (!is_null($client_to_manage)) {
                    $cliente = $client_to_manage;
                }
            }

            $usuario = $this->Usuarios->patchEntity($usuario, $usuarioData);

            $password_encrypt = $this->crypt_util->encrypt($usuarioData['senha']);

            $usuario = $this->Usuarios->formatUsuario(0, $usuario);

            $errors = $usuario->errors();

            if ($usuario = $this->Usuarios->save($usuario)) {
                // guarda uma senha criptografada de forma diferente no DB (para acesso externo)
                $this->UsuariosEncrypted->setUsuarioEncryptedPassword($usuario['id'], $password_encrypt);

                // a vinculação só será feita se não for um Admin RTI
                if ($tipo_perfil != Configure::read('profileTypes')['AdminDeveloperProfileType']) {

                    if ($tipo_perfil == Configure::read('profileTypes')['AdminNetworkProfileType']) {

                        // Se usuário for administrador geral da rede, guarda na tabela de redes_has_clientes_administradores

                        // ele ficará alocado na matriz
                        if ($clientes_id == "") {
                            $rede_has_cliente = $this->RedesHasClientes->findMatrizOfRedesByRedesId($rede->id);

                            $clientes_id = $rede_has_cliente->clientes_id;
                        }

                        $rede_has_cliente = $this->RedesHasClientes->getRedesHasClientesByClientesId($clientes_id);

                        $result = $this->RedesHasClientesAdministradores->addRedesHasClientesAdministradores(
                            $rede_has_cliente->id,
                            $usuario->id
                        );
                    }

                    /**
                     * Agora vincula o usuário ao cliente. Se for cliente final,
                     * será considerado um 'consumidor', caso contrário,
                     * será considerado equipe
                     */

                    $this->ClientesHasUsuarios->saveClienteHasUsuario($clientes_id, $usuario->id, $usuario->tipo_perfil);
                }

                $this->Flash->success(__('O usuário foi salvo.'));

                // se cadastrou um usuário, retorna à meus clientes,
                // caso contrário, retorna à usuários da rede
                if ($usuario['tipo_perfil'] == Configure::read('profileTypes')['UserProfileType']) {
                    return $this->redirect(['action' => 'meus_clientes']);
                } else if ($usuario['tipo_perfil'] == Configure::read('profileTypes')['AdminDeveloperProfileType']) {
                    return $this->redirect(['action' => 'index']);
                } else {
                    if (isset($redes_id)) {
                        return $this->redirect(['action' => 'usuarios_rede']);
                    }
                    return $this->redirect(['action' => 'index']);
                }

            }

            $this->Flash->error(__('O usuário não pode ser registrado. '));

            // exibe os erros logo acima identificados
            foreach ($errors as $key => $error) {
                $key = key($error);
                $this->Flash->error(__("{0}", $error[$key]));
            }
        }

        $arraySet = [
            'usuario',
            'rede',
            'redes',
            'redes_id',
            'usuario_logado_tipo_perfil',
            'user_logged'
        ];

        $this->set(compact($arraySet));
        $this->set('_serialize', $arraySet);
    }

    /**
     * Adiciona conta de usuário (cliente final, usado por um funcionário)
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function editarOperador(int $usuarios_id = null)
    {
        $usuario = $this->Usuarios->getUsuarioById($usuarios_id);

        $user_admin = $this->request->session()->read('User.RootLogged');
        $user_managed = $this->request->session()->read('User.ToManage');

        if ($user_admin) {
            $this->user_logged = $user_managed;
        }

        $rede = $this->request->session()->read('Network.Main');

        $cliente_has_usuario = $this->ClientesHasUsuarios->findClienteHasUsuario(
            [
                'ClientesHasUsuarios.usuarios_id' => $usuarios_id,
                'ClientesHasUsuarios.tipo_perfil <= ' => Configure::read('profileTypes')['WorkerProfileType']
            ]
        )->first();

        $clientes_id = $cliente_has_usuario->clientes_id;

        // se a rede estiver nula, procura pela rede através do clientes_has_usuarios

        if (!isset($redes_id)) {
            $rede_has_cliente = $this->RedesHasClientes->getRedesHasClientesByClientesId($cliente_has_usuario->clientes_id);

            $rede = $this->Redes->getAllRedes('all', ['id' => $rede_has_cliente->redes_id])->first();
        }

        $client_to_manage = $this->request->session()->read('ClientToManage');

        $redes_id = $rede->id;

        $redes = $this->Redes->getRedesList(['id' => $rede->id]);

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();

            $usuarioData = $data;

            // guarda qual é a unidade que está sendo cadastrada
            $clientes_id = (int)$data['clientes_id'];

            $tipo_perfil = $data['tipo_perfil'];

            $cliente = null;
            if (isset($this->user_logged)) {
                // só vincula usuário à uma rede se for tipo de perfil entre
                // AdminNetworkProfileType e WorkerProfileType

                if ($this->user_logged['tipo_perfil'] >= Configure::read('profileTypes')['AdminNetworkProfileType']
                    && $this->user_logged['tipo_perfil'] <= Configure::read('profileTypes')['WorkerProfileType']) {
                    $cliente = $this->Clientes->getClienteMatrizLinkedToUsuario($this->user_logged);
                }

                // Cadastra o usuário no cliente da session, se estiver em modo de Admin

                if (!is_null($client_to_manage)) {
                    $cliente = $client_to_manage;
                }
            }

            $usuario = $this->Usuarios->patchEntity($usuario, $usuarioData);

            $usuario = $this->Usuarios->formatUsuario($usuario['id'], $usuario);

            $errors = $usuario->errors();

            if ($usuario = $this->Usuarios->save($usuario)) {
                // a vinculação só será feita se não for um Admin RTI
                if ($tipo_perfil != Configure::read('profileTypes')['AdminDeveloperProfileType']) {

                    if ($tipo_perfil == Configure::read('profileTypes')['AdminNetworkProfileType']) {

                        // Se usuário for administrador geral da rede, guarda na tabela de redes_has_clientes_administradores

                        // ele ficará alocado na matriz
                        if ($clientes_id == "") {
                            $rede_has_cliente = $this->RedesHasClientes->findMatrizOfRedesByRedesId($rede->id);

                            $clientes_id = $rede_has_cliente->clientes_id;
                        }

                        $rede_has_cliente = $this->RedesHasClientes->getRedesHasClientesByClientesId($clientes_id);

                        $result = $this->RedesHasClientesAdministradores->addRedesHasClientesAdministradores(
                            $rede_has_cliente->id,
                            $usuario->id
                        );
                    }

                    /**
                     * Agora vincula o usuário ao cliente. Se for cliente final,
                     * será considerado um 'consumidor', caso contrário,
                     * será considerado equipe
                     */

                    /**
                     * Se o operador não for adm de rede, nem regional, atualiza o vínculo.
                     */

                    if ($usuario->tipo_perfil >= (int)Configure::read('profileTypes')['AdminNetworkProfileType']) {
                        $this->ClientesHasUsuarios->updateClienteHasUsuarioRelationship($cliente_has_usuario->id, $clientes_id, $usuario->id, $usuario->tipo_perfil);
                    }
                }

                $this->Flash->success(__('O usuário foi salvo.'));

                return $this->redirect(['action' => 'usuarios_rede', $redes_id]);
            }

            $this->Flash->error(__('O usuário não pode ser registrado. '));

            // exibe os erros logo acima identificados
            foreach ($errors as $key => $error) {
                $key = key($error);
                $this->Flash->error(__("{0}", $error[$key]));
            }
        }

        $usuario_logado_tipo_perfil = $this->user_logged['tipo_perfil'];
        $this->set(compact(['usuario', 'rede', 'redes', 'redes_id', 'clientes_id', 'usuario_logado_tipo_perfil']));
        $this->set('_serialize', ['usuario', 'rede', 'redes', 'redes_id', 'clientes_id', 'usuario_logado_tipo_perfil']);
    }


    /**
     * Exibe todos os usuários aguardando aprovação do documento
     *
     * @return \Cake\Http\Response|void
     * @author
     **/
    public function usuariosAguardandoAprovacao()
    {

        $conditions = [];

        $entire_network = false;

        array_push($conditions, ['tipo_perfil > ' => Configure::read('profileTypes')['AdminDeveloperProfileType']]);

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();

            if ($data['opcoes'] == 'cpf') {
                $value = $this->cleanNumber($data['parametro']);
            } else {
                $value = $data['parametro'];
            }

            array_push(
                $conditions,
                [
                    'usuarios.' . $data['opcoes'] . ' like' => '%' . $value . '%'
                ]
            );
        }

        $usuarios = $this->Usuarios->findUsuariosAwaitingApproval();

        $usuarios = $usuarios->where($conditions);

        $usuarios = $this->paginate($usuarios, ['limit' => 10, 'order' => ['tipo_perfil' => 'ASC']]);

        $this->set('usuarios', $usuarios);
    }

    /**
     * Aprova usuário com documentação imprecisa
     *
     * @param int $userId Id de usuário
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function aprovarDocumentoUsuario(int $userId = null)
    {
        try {
            $usuario = $this->Usuarios->getUsuarioById($userId);

            $usuario->foto_documento = Configure::read('documentUserPathRead') . $usuario->foto_documento;

            if ($this->request->is(['post', 'put'])) {
                $usuario = $this->Usuarios->getUsuarioById($userId);
                $usuario->aguardando_aprovacao = false;
                $usuario->data_limite_aprovacao = null;

                if ($this->Usuarios->save($usuario)) {
                    $this->Flash->success(__("O usuário {0} foi autorizado.", $usuario->nome));

                    $this->redirect(['action' => 'usuarios_aguardando_aprovacao']);
                } else {
                    $this->Flash->error(__("Não foi possível autorizar o usuário, tente novamente."));
                }
            }

            $this->set('usuario', $usuario);
        } catch (\Exception $e) {
        }
    }

    /**
     * Action de Esqueci minha Senha
     *
     * @return \Cake\Http\Response|void
     * @author Gustavo Souza Gonçalves
     */
    public function esqueciMinhaSenha()
    {
        $message = [];

        if ($this->request->is('post')) {
            $usuario = $this->Usuarios->getUsuarioByEmail($this->request->data['email']);
            if (is_null($usuario)) {
                $messageString = __(Configure::read("messageEmailNotFound"));
                $message[] = ['status' => false, 'message' => $messageString];

                $this->Flash->error($messageString);
            } else {
                // gera o token que o usuário irá utilizar para recuperar a senha

                $token = bin2hex(openssl_random_pseudo_bytes(32));
                $url = Router::url(['controller' => 'usuarios', 'action' => 'resetar_minha_senha', $token], true);
                $timeout = time() + DAY;

                if ($this->Usuarios->setUsuarioTokenPasswordRequest($usuario->id, $token, $timeout)) {
                    $this->_sendResetEmail($url, $usuario);

                    $this->Flash->success(__("Email enviado para {0} com o token de resetar a senha.", $usuario->email));

                    return $this->redirect(['action' => 'login']);
                } else {
                    $this->Flash->error('Houve um erro ao solicitar o token de resetar a senha.');
                }
            }
        }

        $arraySet = [
            'message',
        ];

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);
    }

    /**
     * Action de Esqueci minha Senha
     *
     * @return \Cake\Http\Response|void
     * @author Gustavo Souza Gonçalves
     */
    public function esqueciMinhaSenhaAPI()
    {
        $mensagem = [];

        if ($this->request->is('post')) {
            $usuario = $this->Usuarios->getUsuarioByEmail($this->request->data['email']);
            if (is_null($usuario)) {
                $messageString = Configure::read("messageEmailNotFound");
                $mensagem[] = ['status' => false, 'message' => $messageString];

            } else {
                // gera o token que o usuário irá utilizar para recuperar a senha

                $token = bin2hex(openssl_random_pseudo_bytes(32));
                $url = Router::url(['controller' => 'usuarios', 'action' => 'resetar_minha_senha', $token], true);
                $timeout = time() + DAY;

                if ($this->Usuarios->setUsuarioTokenPasswordRequest($usuario->id, $token, $timeout)) {
                    $this->_sendResetEmail($url, $usuario);

                    $messageString = __("Email  enviado para {0} com o token de resetar a senha.", $usuario->email);

                    $mensagem[] = ['status' => true, 'message' => $messageString];
                } else {

                    $messageString = __('Houve um erro ao solicitar o token de resetar a senha.');
                    $mensagem[] = ['status' => false, 'message' => $messageString];
                }
            }
        }

        $arraySet = [
            'mensagem',
        ];

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);
    }

    /**
     * Action de reativar conta
     *
     * @return \Cake\Http\Response|void
     * @author
     **/
    public function reativarConta($email = null)
    {
        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();

            $this->loadNecessaryModels(['Veiculos', 'UsuariosHasVeiculos']);

            $usuario = $this->Usuarios->getUsuarioByEmail($email);

            if ($usuario['tipo_perfil'] < Configure::read('profileTypes')['UserProfileType']) {
                $this->Flash->error(Configure::read("messageDenyErrorPrivileges"));

                return $this->redirect(['controller' => 'usuarios', 'action' => 'reativar_conta']);
            }

            $usuariosComCarros = $this->Veiculos->getUsuariosByVeiculo($data['placa']);

            $placaEncontrada = false;

            foreach ($usuariosComCarros as $value) {
                foreach ($value->usuarios_has_veiculos as $veiculos) {
                    if ($veiculos['usuarios_id'] == $usuario['id']) {
                        $placaEncontrada = true;
                        break;
                    }
                }
            }

            $dataNascimentoConfere = ($usuario['data_nasc']->format('Y-m-d') == $data['data_nasc']);

            if ($placaEncontrada && $dataNascimentoConfere) {
                $this->Usuarios->reativarConta($usuario['id']);

                $this->Flash->success('Conta recuperada com sucesso. Para continuar, realize o login');

                return $this->redirect(['action' => 'login']);
            } else {
                $this->Flash->error('Informações não conferem. Tente novamente.');
            }
        }
    }

    /**
     * Action de Resetar senha
     *
     * @return \Cake\Http\Response|void
     */
    public function resetarMinhaSenha($tokenSenha = null)
    {
        if ($tokenSenha) {
            $usuario = $this->Usuarios->findUsuarioAwaitingPasswordReset($tokenSenha, time());
            if ($usuario) {
                if (!empty($this->request->data)) {
                    // Limpa campos de requisição de token
                    $this->request->data['token_senha'] = null;
                    $this->request->data['data_expiracao_senha'] = null;
                    $this->request->data['tipo_perfil'] = $usuario['tipo_perfil'];

                    $usuario = $this->Usuarios->patchEntity($usuario, $this->request->data);

                    if ($this->Usuarios->save($usuario)) {
                        $this->Flash->set(__('Sua senha foi atualizada.'));
                        return $this->redirect(array('action' => 'login'));
                    } else {
                        $this->Flash->error(__('A senha não pode ser atualizada. Tente novamente.'));
                    }
                }
            } else {
                $this->Flash->error('Token inválido ou expirado. Solicite novamente o reset da senha.');
                $this->redirect(['action' => 'esqueci_minha_senha']);
            }
            unset($usuario->password);
            $this->set(compact('usuario'));
        } else {
            $this->redirect('/');
        }
    }

    /**
     * Action de Trocar senha
     *
     * @param int $id
     * @return \Cake\Http\Response|void
     */
    public function alterarSenha($id = null)
    {
        try {
            $usuario = $this->Usuarios->get($id);

            if ($this->request->is('get')) {
                $usuario->senha = null;
            }

            $user_admin = $this->request->session()->read('User.RootLogged');
            $user_managed = $this->request->session()->read('User.ToManage');

            if ($user_admin) {
                $this->user_logged = $user_managed;
            }

            $this->set('usuario', $usuario);
            if ($this->request->is(['post', 'put'])) {
                $usuario = $this->Usuarios->getUsuarioById($id);

                if ($usuario) {
                    if (!empty($this->request->getData())) {
                        $password_encrypt = $this->crypt_util->encrypt($this->request->getData()['senha']);

                        // Limpa campos de requisição de token
                        $this->request->data['token_senha'] = null;
                        $this->request->data['data_expiracao_senha'] = null;
                        $this->request->data['tipo_perfil'] = $usuario['tipo_perfil'];

                        $usuario = $this->Usuarios->patchEntity($usuario, $this->request->data);

                        if ($this->Usuarios->save($usuario)) {
                            $this->Flash->success(__('A senha foi atualizada.'));

                            // atualiza a senha criptografada de forma diferente no DB (para acesso externo)
                            $this->UsuariosEncrypted->setUsuarioEncryptedPassword($usuario['id'], $password_encrypt);

                            if ($this->user_logged['tipo_perfil'] == (int)Configure::read('profileTypes')['AdminDeveloperProfileType']) {
                                return $this->redirect(array('action' => 'index'));
                            } else if ($this->user_logged['tipo_perfil'] >= (int)Configure::read('profileTypes')['AdminNetworkProfileType'] && $this->user_logged['tipo_perfil'] <= (int)Configure::read('profileTypes')['WorkerProfileType']) {
                                return $this->redirect(['controller' => 'pages', 'action' => 'index']);
                            } else {
                                return $this->redirect(['controller' => 'usuarios', 'action' => 'meu_perfil']);
                            }

                        } else {
                            $this->Flash->error(__('A senha não pode ser atualizada. Tente novamente.'));
                        }
                    }
                } else {
                    $this->Flash->error('A senha não pode ser atualizada. Foi informada corretamente?');
                    $this->redirect(['action' => 'alterar_senha']);
                }
                unset($usuario->password);
                $this->set(compact('usuario'));
            }
        } catch (\Exception $e) {
            $stringError = __("Erro ao realizar procedimento de troca de senha: {0} em: {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Exibe os usuários de uma rede
     *
     *
     * @return \Cake\Http\Response|void
     */
    public function usuariosRede()
    {
        $rede = $this->request->session()->read("Network.Main");
        $redes_id = $rede["id"];
        $cliente = $this->request->session()->read('Network.Unit');
        $client_to_manage = $this->request->session()->read('ClientToManage');

        $user_admin = $this->request->session()->read('User.RootLogged');
        $user_managed = $this->request->session()->read('User.ToManage');

        if ($user_admin) {
            $this->user_logged = $user_managed;
        }

        $clientesIds = array();

        $conditions = [];

        $clientes_ids = [];

        // se for developer / rti / rede, mostra todas as unidades da rede

        $unidades_ids = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($redes_id, $this->user_logged['id']);

        if (!is_null($unidades_ids)) {
            foreach ($unidades_ids as $key => $value) {
                $clientes_ids[] = $key;
            }
        }

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();

            if ($data['opcoes'] == 'cpf') {
                $value = $this->cleanNumber($data['parametro']);
            } else {
                $value = $data['parametro'];
            }

            array_push(
                $conditions,
                [
                    'usuarios.' . $data['opcoes'] . ' like' => '%' . $value . '%'
                ]
            );

            if ($data['filtrar_unidade'] != "") {
                $clientes_ids = [];
                $clientes_ids[] = (int)$data['filtrar_unidade'];
            }
        }

        if (sizeof($clientes_ids) == 0) {
            $clientes_ids[] = 0;
        }

        $usuarios = $this->Usuarios->findFuncionariosRede(
            $redes_id,
            $clientes_ids,
            $conditions
        );

        $user_logged = $this->user_logged;

        // debug($usuarios->toArray());
        $usuarios = $this->paginate($usuarios, ['limit' => 10]);

        $this->set(compact('usuarios', 'unidades_ids', 'redes_id', 'user_logged'));
        $this->set('_serialize', ['usuarios', 'unidades_ids', 'redes_id', 'user_logged']);
    }

    /**
     * Exibe os administradores de uma rede
     *
     * @param int $redes_id Id da rede
     *
     * @return \Cake\Http\Response|void
     */
    public function administradoresRede()
    {
        $rede = $this->request->session()->read("Network.Main");
        $redes_id = $rede["id"];

        $cliente = $this->request->session()->read('Network.Unit');
        $client_to_manage = $this->request->session()->read('ClientToManage');

        $user_admin = $this->request->session()->read('User.RootLogged');
        $user_managed = $this->request->session()->read('User.ToManage');

        if ($user_admin) {
            $this->user_logged = $user_managed;
            $user_logged = $this->user_logged;
        }

        $conditions = [];

        // define que só poderá buscar administradores e administradores regionais para esta tela

        array_push($conditions, ['usuarios.tipo_perfil' => Configure::read('profileTypes')['AdminNetworkProfileType']]);

        $clientes_ids = [];

        $unidades_ids = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($redes_id, $this->user_logged['id']);

        if (!is_null($unidades_ids)) {
            foreach ($unidades_ids as $key => $value) {
                $clientes_ids[] = $key;
            }
        }

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();

            if ($data['opcoes'] == 'cpf') {
                $value = $this->cleanNumber($data['parametro']);
            } else {
                $value = $data['parametro'];
            }

            array_push(
                $conditions,
                [
                    'usuarios.' . $data['opcoes'] . ' like' => '%' . $value . '%'
                ]
            );

            if ($data['filtrar_unidade'] != "") {
                $clientes_ids = [];
                $clientes_ids[] = (int)$data['filtrar_unidade'];
            }
        }

        if (sizeof($clientes_ids) == 0) {
            $clientes_ids[] = 0;
        }

        $usuarios = $this->Usuarios->findFuncionariosRede(
            $redes_id,
            $clientes_ids,
            $conditions
        );

        $usuarios = $this->paginate($usuarios, ['limit' => 10]);

        $arraySet = [
            "usuarios",
            "unidades_ids",
            "redes_id",
            "user_logged"
        ];

        $this->set(compact($arraySet));
        $this->set('_serialize', $arraySet);
    }



    /**
     * Exibe os administradores de uma rede
     *
     * @param int $redes_id Id da rede
     *
     * @return \Cake\Http\Response|void
     */
    public function administradoresRegionaisComuns()
    {
        $rede = $this->request->session()->read('Network.Main');
        $redes_id = $rede["id"];
        $cliente = $this->request->session()->read('Network.Unit');
        $client_to_manage = $this->request->session()->read('ClientToManage');

        $user_admin = $this->request->session()->read('User.RootLogged');
        $user_managed = $this->request->session()->read('User.ToManage');

        if ($user_admin) {
            $this->user_logged = $user_managed;
            $user_logged = $this->user_logged;
        }

        $conditions = [];

        // define que só poderá buscar administradores e administradores regionais para esta tela

        array_push($conditions, ['usuarios.tipo_perfil >= ' => Configure::read('profileTypes')['AdminRegionalProfileType']]);
        array_push($conditions, ['usuarios.tipo_perfil <= ' => Configure::read('profileTypes')['AdminLocalProfileType']]);

        $entire_network = false;

        $clientes_ids = [];

        $unidades_ids = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($redes_id, $this->user_logged['id']);

        if (!is_null($unidades_ids)) {
            foreach ($unidades_ids as $key => $value) {
                $clientes_ids[] = $key;
            }
        }

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();

            if ($data['opcoes'] == 'cpf') {
                $value = $this->cleanNumber($data['parametro']);
            } else {
                $value = $data['parametro'];
            }

            array_push(
                $conditions,
                [
                    'usuarios.' . $data['opcoes'] . ' like' => '%' . $value . '%'
                ]
            );

            if ($data['filtrar_unidade'] != "") {
                $clientes_ids = [];
                $clientes_ids[] = (int)$data['filtrar_unidade'];
            }
        }

        if (sizeof($clientes_ids) == 0) {
            $clientes_ids[] = 0;
        }

        $usuarios = $this->Usuarios->findFuncionariosRede(
            $redes_id,
            $clientes_ids,
            $conditions
        );

        $usuarios = $this->paginate($usuarios, ['limit' => 10]);

        $arraySet = [
            'usuarios',
            'unidades_ids',
            'rede',
            'redes_id',
            "user_logged"

        ];
        $this->set(compact($arraySet));
        $this->set('_serialize', $arraySet);
    }

    /**
     * Método para listar todos os usuários de uma rede
     *
     * @return \Cake\Http\Response|void
     **/
    public function meusClientes()
    {
        $user_admin = $this->request->session()->read('User.RootLogged');
        $user_managed = $this->request->session()->read('User.ToManage');

        if ($user_admin) {
            $this->user_logged = $user_managed;
        }

        $rede = $this->request->session()->read('Network.Main');

        // pega id de todos os clientes que estão ligados à uma rede

        $redes_has_clientes_query = $this->RedesHasClientes->getRedesHasClientesByRedesId($rede->id);

        $clientes_ids = [];

        foreach ($redes_has_clientes_query as $key => $value) {
            $clientes_ids[] = $value['clientes_id'];
        }
        $conditions = [];

        $conditions[] = ['clientes_id IN ' => $clientes_ids];

        $entire_network = false;

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();

            if ($data['opcoes'] == 'cpf') {
                $value = $this->cleanNumber($data['parametro']);
            } else {
                $value = $data['parametro'];
            }

            if ($data['incluir_filiais']) {
                $entire_network = true;
            }

            array_push(
                $conditions,
                [
                    $data['opcoes'] . ' like' => '%' . $value . '%'
                ]
            );
        }

        $usuarios = $this->Usuarios->getUsuarios($conditions);

        $this->paginate($usuarios, ['limit' => 10]);

        $this->set(compact('usuarios'));
        $this->set('_serialize', ['usuarios']);
    }

    /**
     * Exibe a view para gerenciamento de um determinado usuário
     *
     * @return \Cake\Http\Response|void
     */
    public function administrarUsuario()
    {
        try {
            // verificar se quem está acessando é de fato um administrador RTI

            if (!$this->security_util->checkUserIsAuthorized($this->getUserLogged(), 'AdminDeveloperProfileType', 'AdminDeveloperProfileType')) {
                $this->security_util->redirectUserNotAuthorized($this);
            }

            $conditions = [];

            // condições básicas do sistema
            // só pode gerenciar de administradores de redes à cliente-final

            $conditions[] = ['tipo_perfil >= ' => Configure::read('profileTypes')['AdminNetworkProfileType']];

            if ($this->request->is('post')) {
                $data = $this->request->getData();

                if ($data['opcoes'] == 'cpf') {
                    $value = $this->cleanNumber($data['parametro']);
                } else {
                    $value = $data['parametro'];
                }

                array_push(
                    $conditions,
                    [
                        'usuarios.' . $data['opcoes'] . ' like' => '%' . $value . '%'
                    ]
                );
            }

            $usuarios = $this->Usuarios->findAllUsuarios($conditions);

            $this->paginate($usuarios, ['limit' => 10, 'order' => ['matriz_id' => 'ASC']]);

            $this->set(compact(['usuarios']));
            $this->set('_serialize', ['usuarios']);
        } catch (\Exception $e) {
            $stringError = __("Erro: {0} em: {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Inicia o gerenciamento de um determinado usuário
     *
     * @return \Cake\Http\Response|void
     */
    public function iniciarAdministracaoUsuario()
    {
        // verifica se o usuário é um administrador RTI / developer

        if (!$this->security_util->checkUserIsAuthorized($this->getUserLogged(), 'AdminDeveloperProfileType', 'AdminDeveloperProfileType')) {
            $this->security_util->redirectUserNotAuthorized($this);
        }

        $query = $this->request->query;

        $user_admin = $this->getUserLogged();

        $user_managed = $this->Usuarios->getUsuarioById($query['usuarios_id']);

        $cliente = $this->Clientes->getClienteMatrizLinkedToUsuario($user_managed);

        // pega qual é a rede que o usuário está vinculado

        /**
         * Se o usuário for do tipo Usuário comum, não tem problema ele ainda não estar vinculado
         * pois quando fizer um abastecimento o script vai vincular.
         * Se for Níveis acimas, aí tem problema.
         */

        $clienteHasUsuario = $this->ClientesHasUsuarios->findClienteHasUsuario(
            [
                'ClientesHasUsuarios.usuarios_id' => $user_managed->id,
                'ClientesHasUsuarios.tipo_perfil' => $user_managed->tipo_perfil
            ]
        )->first();

        // DebugUtil::print($clienteHasUsuario);

        if (empty($clienteHasUsuario) && $user_managed["tipo_perfil"] == Configure::read("profileTypes")["UserProfileType"]) {
            $this->Flash->error("Este usuário não pode ser administrado pois não possui vinculo ainda à uma rede / ponto de atendimento!");

            return $this->redirect(['controller' => 'usuarios', 'action' => 'administrarUsuario']);
        }

        $redeHasCliente = $this->RedesHasClientes->getRedesHasClientesByClientesId(
            $clienteHasUsuario->clientes_id
        );

        $rede = $redeHasCliente->rede;

        $this->request->session()->write('Network.Main', $rede);
        $this->request->session()->write('Network.Unit', $cliente);

        $this->request->session()->write("User.RootLogged", $user_admin);
        $this->request->session()->write("User.ToManage", $user_managed);

        return $this->redirect(['controller' => 'pages', 'action' => 'display']);
    }

    /**
     * Finaliza o gerenciamento de um determinado usuário
     *
     * @return \Cake\Http\Response|void
     */
    public function finalizarAdministracaoUsuario()
    {
        $this->request->session()->delete("User.RootLogged");
        $this->request->session()->delete("User.ToManage");
        $this->request->session()->delete('Network.Main');
        $this->request->session()->delete('ClientToManage');

        return $this->redirect(['controller' => 'pages', 'action' => 'display']);
    }

    /**
     * ------------------------------------------------------------
     * Métodos comuns para usuários gerenciadores
     * ------------------------------------------------------------
     */

    /**
     * Action para habilitar usuário
     *
     * @return \Cake\Http\Response|void
     */
    public function habilitarUsuario()
    {
        $query = $this->request->query;

        $result = $this->_alteraContaAtivaUsuario((int)$query['usuarios_id'], true);

        if ($result) {
            $this->Flash->success(Configure::read('messageEnableSuccess'));

            return $this->redirect($query['return_url']);
        }
    }

    /**
     * Action para desabilitar usuário
     *
     * @return \Cake\Http\Response|void
     */
    public function desabilitarUsuario()
    {
        $query = $this->request->query;

        $result = $this->_alteraContaAtivaUsuario((int)$query['usuarios_id'], false);

        if ($result) {
            $this->Flash->success(Configure::read('messageDisableSuccess'));

            return $this->redirect($query['return_url']);
        }
    }

    /**
     * Altera estado de conta ativa de usuário
     *
     * @param int  $usuarios_id Id de usuário
     * @param bool $status      Estado da conta
     *
     * @return \Cake\Http\Response|void
     */
    private function _alteraContaAtivaUsuario(int $usuarios_id, bool $status)
    {
        return
            $this->Usuarios->changeAccountEnabledByUsuarioId(
            $usuarios_id,
            $status
        );
    }

    /**
     * ------------------------------------------------------------
     * Métodos para Funcionários (Dashboard de Funcionário)
     * ------------------------------------------------------------
     */

    /**
     * Abre action para pesquisa de cliente (para abrir tela de alteração de cadastro,
     * dados de veículos e transportadoras)
     *
     * @return void
     */
    public function pesquisarClienteAlterarDados()
    {

    }

    /**
     * Método para editar dados de usuário (modo de administrador)
     *
     * @param string|null $id Usuario id.
     *
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     *
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function editarCadastroUsuarioFinal($id = null)
    {
        try {

            $user_admin = $this->request->session()->read('User.RootLogged');
            $user_managed = $this->request->session()->read('User.ToManage');

            if ($user_admin) {
                $this->user_logged = $user_managed;
            }

            $rede = $this->request->session()->read('Network.Main');

            $usuario = $this->Usuarios->get(
                $id,
                [
                    'contain' => []
                ]
            );

            if ($this->request->is(['post', 'put'])) {
                $usuario = $this->Usuarios->patchEntity($usuario, $this->request->getData(), ['validate' => 'EditUsuarioInfo']);

                $errors = $usuario->errors();

                $usuario = $this->Usuarios->save($usuario);
                if ($usuario) {
                    $this->Flash->success(__(Configure::read('messageSavedSuccess')));

                    $url = Router::url(['controller' => 'Pages', 'action' => 'display']);
                    return $this->response = $this->response->withLocation($url);

                }
                $this->Flash->error(__(Configure::read('messageSavedError')));

                // exibe os erros logo acima identificados
                foreach ($errors as $key => $error) {
                    $key = key($error);
                    $this->Flash->error(__("{0}", $error[$key]));
                }
            }

            $usuario_logado_tipo_perfil = (int)Configure::read('profileTypes')['UserProfileType'];

            $this->set(compact(['usuario', 'usuario_logado_tipo_perfil']));
            $this->set('_serialize', ['usuario', 'usuario_logado_tipo_perfil']);
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao editar dados de usuário: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * ------------------------------------------------------------
     * Métodos comuns para todos os usuários
     * ------------------------------------------------------------
     */

    public function relatorios()
    {
        # code...
    }

    /**
     * ------------------------------------------------------------
     * Relatórios (Dashboard de Admin RTI)
     * ------------------------------------------------------------
     */

    /**
     * Relatório de Equipe de cada Rede
     *
     * @return \Cake\Http\Response|void
     */
    public function relatorioEquipeRedes()
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
                $whereConditions[] = ["nome like '%" . $data['nome'] . "%'"];
            }

            if (strlen($data['tipo_perfil']) > 0) {
                $whereConditions[] = ["tipo_perfil " => $data['tipo_perfil']];
            }

            if (strlen($data['sexo']) > 0) {
                $whereConditions[] = ['sexo' => (bool)$data['sexo']];
            }

            if (strlen($data['conta_ativa']) > 0) {
                $whereConditions[] = ['conta_ativa' => (bool)$data['conta_ativa']];
            }

            if (strlen($data['conta_bloqueada']) > 0) {
                $whereConditions[] = ['conta_bloqueada' => (bool)$data['conta_bloqueada']];
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
                    $whereConditions[] = ['usuarios.audit_insert BETWEEN "' . $dataInicial . '" and "' . $dataFinal . '"'];
                }

            } else if (strlen($data['auditInsertInicio']) > 0) {

                if ($dataInicial > $dataHoje) {
                    $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Início'));
                } else {
                    $whereConditions[] = ['usuarios.audit_insert >= ' => $dataInicial];
                }

            } else if (strlen($data['auditInsertFim']) > 0) {

                if ($dataFinal > $dataHoje) {
                    $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Fim'));
                } else {
                    $whereConditions[] = ['usuarios.audit_insert <= ' => $dataFinal];
                }
            }
        }

        // Monta o Array para apresentar em tela
        $redes = array();

        foreach ($redesArrayIds as $key => $value) {
            $arrayWhereConditions = $whereConditions;

            $redesHasClientesIds = array();

            $usuariosIds = array();

            $rede = $this->Redes->getRedeById((int)$value);

            $redeItem = array();

            $redeItem['id'] = $rede->id;
            $redeItem['nome_rede'] = $rede->nome_rede;
            $redeItem['usuarios'] = array();

            $unidades_ids = [];

            // obtem os ids das unidades para saber quais brindes estão disponíveis
            foreach ($rede->redes_has_clientes as $key => $value) {
                $unidades_ids[] = $value->clientes_id;
            }

            $usuarios = $this->Usuarios->findFuncionariosRede(
                $rede->id,
                $unidades_ids,
                $whereConditions
            );

            $redeItem['usuarios'] = $usuarios;

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
     * Relatório de Usuários Cadastrados
     *
     * @return \Cake\Http\Response|void
     */
    public function relatorioUsuariosCadastrados()
    {
        $whereConditions = array();

        $dataInicial = date('d/m/Y', strtotime('-30 days'));
        $dataFinal = date('d/m/Y');

        $whereConditions[] = ["tipo_perfil " => Configure::read('profileTypes')['UserProfileType']];

        if ($this->request->is(['post'])) {
            $data = $this->request->getData();

            if ($data['opcoes'] == 'cpf') {
                $value = $this->cleanNumber($data['parametro']);
            } else {
                $value = $data['parametro'];
            }

            $whereConditions[] = [
                "usuarios." . $data['opcoes'] . ' like' => '%' . $value . '%'
            ];

            if (strlen($data['sexo']) > 0) {
                $whereConditions[] = ['usuarios.sexo' => (bool)$data['sexo']];
            }

            if (strlen($data['conta_ativa']) > 0) {
                $whereConditions[] = ['usuarios.conta_ativa' => (bool)$data['conta_ativa']];
            }

            if (strlen($data['conta_bloqueada']) > 0) {
                $whereConditions[] = ['usuarios.conta_bloqueada' => (bool)$data['conta_bloqueada']];
            }

            $dataHoje = DateTimeUtil::convertDateToUTC((new DateTime('now'))->format('Y-m-d H:i:s'));

            // Data de Nascimento Inicio e Fim

            $dataInicialNascimento = strlen($data['dataNascimentoInicio']) > 0 ? DateTimeUtil::convertDateToUTC($data['dataNascimentoInicio'], 'd/m/Y') : null;
            $dataFinalNascimento = strlen($data['dataNascimentoFim']) > 0 ? DateTimeUtil::convertDateToUTC($data['dataNascimentoFim'], 'd/m/Y') : null;

            if (strlen($data['dataNascimentoInicio']) > 0 && strlen($data['dataNascimentoFim']) > 0) {

                if ($dataInicialNascimento > $dataFinalNascimento) {
                    $this->Flash->error(__(Configure::read('messageDateRangeInvalid')));
                } else if ($dataInicial > $dataHoje) {
                    $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid', 'Data de Início')));
                } else {
                    $whereConditions[] = ['usuarios.data_nasc BETWEEN "' . $dataInicialNascimento . '" and "' . $dataFinalNascimento . '"'];
                }

            } else if (strlen($data['dataNascimentoInicio']) > 0) {

                if ($dataInicialNascimento > $dataHoje) {
                    $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Início'));
                } else {
                    $whereConditions[] = ['usuarios.data_nasc >= ' => $dataInicialNascimento];
                }

            } else if (strlen($data['dataNascimentoFim']) > 0) {

                if ($dataFinalNascimento > $dataHoje) {
                    $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Fim'));
                } else {
                    $whereConditions[] = ['usuarios.data_nasc <= ' => $dataFinalNascimento];
                }
            }
            // Data de Criação Início e Fim

            $dataInicialInsercao = strlen($data['auditInsertInicio']) > 0 ? DateTimeUtil::convertDateToUTC($data['auditInsertInicio'], 'd/m/Y') : null;
            $dataFinalInsercao = strlen($data['auditInsertFim']) > 0 ? DateTimeUtil::convertDateToUTC($data['auditInsertFim'], 'd/m/Y') : null;

            if (strlen($data['auditInsertInicio']) > 0 && strlen($data['auditInsertFim']) > 0) {

                if ($dataInicialInsercao > $dataFinalInsercao) {
                    $this->Flash->error(__(Configure::read('messageDateRangeInvalid')));
                } else if ($dataInicialInsercao > $dataHoje) {
                    $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid', 'Data de Início')));
                } else {
                    $whereConditions[] = ['usuarios.audit_insert BETWEEN "' . $dataInicialInsercao . '" and "' . $dataFinalInsercao . '"'];
                }

            } else if (strlen($data['auditInsertInicio']) > 0) {

                if ($dataInicialInsercao > $dataHoje) {
                    $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Início'));
                } else {
                    $whereConditions[] = ['usuarios.audit_insert >= ' => $dataInicialInsercao];
                }

            } else if (strlen($data['auditInsertFim']) > 0) {

                if ($dataFinalInsercao > $dataHoje) {
                    $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Fim'));
                } else {
                    $whereConditions[] = ['usuarios.audit_insert <= ' => $dataFinalInsercao];
                }
            }
        }

        $usuarios = $this->Usuarios->findAllUsuarios(
            $whereConditions
        );

        $arraySet = [
            'dataInicial',
            'dataFinal',
            'usuarios',
        ];

        $this->set(compact($arraySet));
    }

    /**
     * Relatório de Usuários Cadastrados por Rede
     *
     * @return \Cake\Http\Response|void
     */
    public function relatorioUsuariosRedes()
    {
        try {
            $redesList = $this->Redes->getRedesList();

            $whereConditions = array();

            $redesArrayIds = array();

            $redes = array();

            $qteRegistros = 10;

            foreach ($redesList as $key => $redeItem) {
                $redesArrayIds[] = $key;
            }

            $whereConditions[] = ["tipo_perfil" => Configure::read('profileTypes')['UserProfileType']];

            if ($this->request->is(['post'])) {
                $data = $this->request->getData();

                if (strlen($data['redes_id']) == 0) {
                    $this->Flash->error('É necessário selecionar uma rede para filtrar!');

                    return $this->redirect([
                        'controller' => 'Usuarios',
                        'action' => 'relatorioUsuariosRedes'
                    ]);
                }

                if (strlen($data['redes_id']) > 0) {
                    $redesArrayIds = ['id' => $data['redes_id']];
                }

                if (strlen($data['nome']) > 0) {
                    $whereConditions[] = ["nome like '%" . $data['nome'] . "%'"];
                }

                if (strlen($data['sexo']) > 0) {
                    $whereConditions[] = ['sexo' => (bool)$data['sexo']];
                }

                if (strlen($data['conta_ativa']) > 0) {
                    $whereConditions[] = ['conta_ativa' => (bool)$data['conta_ativa']];
                }

                if (strlen($data['conta_bloqueada']) > 0) {
                    $whereConditions[] = ['conta_bloqueada' => (bool)$data['conta_bloqueada']];
                }

                $qteRegistros = (int)$data['qte_registros'];

                $dataHoje = DateTimeUtil::convertDateToUTC((new DateTime('now'))->format('Y-m-d H:i:s'));
                $dataInicial = strlen($data['auditInsertInicio']) > 0 ? DateTimeUtil::convertDateToUTC($data['auditInsertInicio'], 'd/m/Y') : null;
                $dataFinal = strlen($data['auditInsertFim']) > 0 ? DateTimeUtil::convertDateToUTC($data['auditInsertFim'], 'd/m/Y') : null;

            // Data de Nascimento Inicio e Fim

                $dataInicialNascimento = strlen($data['dataNascimentoInicio']) > 0 ? DateTimeUtil::convertDateToUTC($data['dataNascimentoInicio'], 'd/m/Y') : null;
                $dataFinalNascimento = strlen($data['dataNascimentoFim']) > 0 ? DateTimeUtil::convertDateToUTC($data['dataNascimentoFim'], 'd/m/Y') : null;

                if (strlen($data['dataNascimentoInicio']) > 0 && strlen($data['dataNascimentoFim']) > 0) {

                    if ($dataInicialNascimento > $dataFinalNascimento) {
                        $this->Flash->error(__(Configure::read('messageDateRangeInvalid')));
                    } else if ($dataInicial > $dataHoje) {
                        $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid', 'Data de Início')));
                    } else {
                        $whereConditions[] = ['usuarios.data_nasc BETWEEN "' . $dataInicialNascimento . '" and "' . $dataFinalNascimento . '"'];
                    }

                } else if (strlen($data['dataNascimentoInicio']) > 0) {

                    if ($dataInicialNascimento > $dataHoje) {
                        $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Início'));
                    } else {
                        $whereConditions[] = ['usuarios.data_nasc >= ' => $dataInicialNascimento];
                    }

                } else if (strlen($data['dataNascimentoFim']) > 0) {

                    if ($dataFinalNascimento > $dataHoje) {
                        $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Fim'));
                    } else {
                        $whereConditions[] = ['usuarios.data_nasc <= ' => $dataFinalNascimento];
                    }
                }
            // Data de Criação Início e Fim

                $dataInicialInsercao = strlen($data['auditInsertInicio']) > 0 ? DateTimeUtil::convertDateToUTC($data['auditInsertInicio'], 'd/m/Y') : null;
                $dataFinalInsercao = strlen($data['auditInsertFim']) > 0 ? DateTimeUtil::convertDateToUTC($data['auditInsertFim'], 'd/m/Y') : null;

                if (strlen($data['auditInsertInicio']) > 0 && strlen($data['auditInsertFim']) > 0) {

                    if ($dataInicialInsercao > $dataFinalInsercao) {
                        $this->Flash->error(__(Configure::read('messageDateRangeInvalid')));
                    } else if ($dataInicial > $dataHoje) {
                        $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid', 'Data de Início')));
                    } else {
                        $whereConditions[] = ['usuarios.audit_insert BETWEEN "' . $dataInicialInsercao . '" and "' . $dataFinalInsercao . '"'];
                    }

                } else if (strlen($data['auditInsertInicio']) > 0) {

                    if ($dataInicialInsercao > $dataHoje) {
                        $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Início'));
                    } else {
                        $whereConditions[] = ['usuarios.audit_insert >= ' => $dataInicialInsercao];
                    }

                } else if (strlen($data['auditInsertFim']) > 0) {

                    if ($dataFinalInsercao > $dataHoje) {
                        $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Fim'));
                    } else {
                        $whereConditions[] = ['usuarios.audit_insert <= ' => $dataFinalInsercao];
                    }
                }

            // Monta o Array para apresentar em tela

                foreach ($redesArrayIds as $key => $value) {
                    $usuariosConditions = $whereConditions;

                    $redesHasClientesIds = array();

                    $usuariosIds = array();

                    $rede = $this->Redes->getRedeById((int)$value);

                    $redeItem = array();

                    $redeItem['id'] = $rede->id;
                    $redeItem['nome_rede'] = $rede->nome_rede;
                    $redeItem['usuarios'] = array();

                    $unidades_ids = [];

            // obtem os ids das unidades para saber quais brindes estão disponíveis
                    foreach ($rede->redes_has_clientes as $key => $value) {
                        $unidades_ids[] = $value->clientes_id;
                    }

                    $redesConditions = [];

                    $redesConditions[] = ['id' => $rede->id];

                    $usuarios = $this->Usuarios->findAllUsuariosByRede($rede->id, $usuariosConditions);

                    if ($qteRegistros > 0) {
                        $redeItem['usuarios'] = $usuarios->limit($qteRegistros);
                    } else {
                        $redeItem['usuarios'] = $usuarios;
                    }

                    unset($arrayWhereConditions);

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

            $stringError = __("Erro ao exibir relatório de usuário: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * ------------------------------------------------------------
     * REST Methods
     * ------------------------------------------------------------
     */

    /**
     * UsuariosController::_consultaUsuariosFidelizados
     *
     * Consulta Usuários Fidelizados conforme requisição
     *
     * @param array $data $Array de Post
     * @param int $redesId Id da Rede
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 12/09/2018
     *
     * @return array $dados
     */
    private function _consultaUsuariosFidelizados(array $data, int $redesId)
    {
        if (!empty($data["redesId"]) && $data["redesId"] > 0) {
            $redesId = (int)$data["redesId"];
        }

        // ResponseUtil::success($data);

        $clientesIds = !empty($data["clientesIds"]) ? $data["clientesIds"] : null;
        $nome = !empty($data["nome"]) ? $data["nome"] : null;
        $cpf = !empty($data["cpf"]) ? $data["cpf"] : null;
        $veiculo = !empty($data["veiculo"]) ? $data["veiculo"] : null;
        $documentoEstrangeiro = !empty($data["documentoEstrangeiro"]) ? $data["documentoEstrangeiro"] : null;
        $status = isset($data["status"]) && strlen($data["status"]) > 0 ? $data["status"] : null;
        $dataInicio = !empty($data["dataInicio"]) ? $data["dataInicio"] : null;
        $dataFim = !empty($data["dataFim"]) ? $data["dataFim"] : null;

        if (gettype($clientesIds) == "integer") {
            $clientesIds = array($clientesIds);
        }

        if (!is_null($clientesIds) && sizeof($clientesIds) == 0) {
            $clientesIds = $this->RedesHasClientes->getClientesIdsFromRedesHasClientes($redesId);
        }

        return $this->ClientesHasUsuarios->getUsuariosFidelizadosClientes(
            $clientesIds,
            $nome,
            $cpf,
            $veiculo,
            $documentoEstrangeiro,
            $status,
            $dataInicio,
            $dataFim
        );
    }

    /**
     * Obtem dados de usuários fidelizados
     *
     * @return void
     */
    public function getUsuariosFidelizadosAPI()
    {
        $rede = $this->request->session()->read("Network.Main");
        $redesId = $rede["id"];

        $data = array();
        if ($this->request->is("post")) {
            $data = $this->request->getData();

            $usuarios = $this->_consultaUsuariosFidelizados($data, $redesId);
        }

        if (sizeof($usuarios) > 0) {
            ResponseUtil::success($usuarios);
        } else {
            ResponseUtil::error(Configure::read("messageLoadDataNotFound"), Configure::read("messageWarningDefault"));
        }
    }

    /**
     * UsuariosController::generateExcelUsuariosFidelizadosAPI
     *
     * Gera relatório de usuários fidelizados pela rede
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 12/09/2018
     *
     * @return json_encode Dados de excel em json_encode
     */
    public function generateExcelUsuariosFidelizadosAPI()
    {
        $rede = $this->request->session()->read("Network.Main");
        $redesId = $rede["id"];

        $data = array();
        if ($this->request->is("post")) {
            $data = $this->request->getData();

            $usuarios = $this->_consultaUsuariosFidelizados($data, $redesId);
        }

        $cabecalho = array(
            "Usuário",
            "CPF",
            "Documento Estrangeiro",
            "Conta Ativa",
            "Gotas Adquiridas",
            "Gotas Utilizadas",
            "Gotas Expiradas",
            "Saldo Atual",
            "Moeda Adquirida ",
            "Data Cadastro na Rede",
        );

        if (sizeof($usuarios) == 0) {
            ResponseUtil::error(Configure::read("messageLoadDataNotFound"), Configure::read("messageWarningDefault"));
        }

        $usuariosArray = array();
        $usuarioTemp = array();
        foreach ($usuarios as $usuario) {
            $usuarioTemp["nome"] = $usuario["nome"];
            $usuarioTemp["cpf"] = $usuario["cpf"];
            $usuarioTemp["docEstrangeiro"] = $usuario["docEstrangeiro"];
            $usuarioTemp["contaAtiva"] = $usuario["contaAtiva"] == 1 ? "Sim" : "Nâo";
            $usuarioTemp["gotasAdquiridas"] = $usuario["gotasAdquiridas"];
            $usuarioTemp["gotasUtilizadas"] = $usuario["gotasUtilizadas"];
            $usuarioTemp["gotasExpiradas"] = $usuario["gotasExpiradas"];
            $usuarioTemp["saldoAtual"] = $usuario["saldoAtual"];
            $usuarioTemp["totalMoedaAdquirida"] = $usuario["totalMoedaAdquirida"];
            $usuarioTemp["dataVinculo"] = $usuario["dataVinculo"];

            $usuariosArray[] = $usuarioTemp;
        }

        $usuarios = $usuariosArray;

        $excel = ExcelUtil::generateExcel("Relatório de Usuários Fidelizados", $cabecalho, $usuarios);

        ResponseUtil::success($excel);
    }

    // /**
    //  * Função apenas para teste de acesso e benchmark
    //  *
    //  * @return void
    //  */
    // public function testAPI()
    // {
    //     $value = $this->Cupons->find()->toArray();
    //     // $value = bin2hex(openssl_random_pseudo_bytes(10));

    //     $arraySet = array("value");

    //     $this->set(compact($arraySet));
    //     $this->set("_serialize", $arraySet);
    // }



    /**
     * ------------------------------------------------------------
     * Ajax Methods
     * ------------------------------------------------------------
     */

    /**
     * Envia documento do cliente para autorização posterior
     *
     * @return \Cake\Http\Response|void
     **/
    public function uploadDocumentTemporary()
    {
        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();

            $this->generateImageFromBase64(
                $data['image'],
                Configure::read('temporaryDocumentUserPath') . $data['imageName'] . '.jpg',
                Configure::read('temporaryDocumentUserPath')
            );

            $img = $data;

            $arraySet = ['img'];

            $this->set(compact($arraySet));
            $this->set("_serialize", $arraySet);
        }
    }

    /**
     * Encontra usuário pelo cpf
     *
     * @return entity\usuario $user
     *
     **/
    public function getUsuarioByCPF()
    {
        try {
            if ($this->request->is(['post', 'put', 'ajax'])) {
                $data = $this->request->getData();

                // DebugUtil::print($data);
                $user = $this->Usuarios->getUsuarioByCPF($data['cpf']);

                if ($data['id'] != 0) {

                    if ($user !== null && $user->id == $data['id']) {
                        $user = null;
                    }
                }

                if (!empty($user)) {
                    $user['data_nasc'] = $user['data_nasc']->format('d/m/Y');
                }
            }
            $arraySet = ['user'];

            $this->set(compact($arraySet));
            $this->set("_serialize", $arraySet);
        } catch (\Exception $e) {
            Log::write('debug', $e->getMessage());
        }
    }

    /**
     * Encontra usuario pelo e-mail
     *
     * @return entity\usuario $user
     * @author Gustavo Souza Gonçalves
     **/
    public function getUsuarioByEmail()
    {
        try {
            if ($this->request->is(['post', 'put'])) {
                $data = $this->request->getData();
                $user = $this->Usuarios->getUsuarioByEmail($data['email']);

                if ($data['id'] != 0) {

                    if ($user->id == $data['id']) {
                        $user = null;
                    }
                }

                $arraySet = ['user'];

                $this->set(compact($arraySet));
                $this->set("_serialize", $arraySet);
            }
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao obter usuário por e-mail: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);

            $arraySet = ['stringError'];

            $this->set(compact($arraySet));
            $this->set("_serialize", $arraySet);
        }
    }

    /**
     * Encontra usuário por parâmetro
     * Este método só retorna perfis do tipo Usuários
     *
     * @return json object
     * @author Gustavo Souza Gonçalves
     */
    public function findUsuario()
    {
        try {
            $result = null;

            if ($this->request->is(['post', 'put'])) {
                $data = $this->request->getData();

                if (strlen($data['parametro']) >= 3) {

                    $rede = $this->request->session()->read('Network.Main');

                    $usuarios = array();
                    $funcionariosCliente = array();

                    $restringirUsuariosRede = isset($data["restrict_query"]) ? $data["restrict_query"] : false;

                    $veiculoEncontrado = null;

                    if ($rede->permite_consumo_gotas_funcionarios) {

                        if ($data['opcao'] == 'nome') {
                            // Pesquisa por Nome
                            $funcionariosCliente = $this->Usuarios->getUsuariosByName($data['parametro'], $rede['id'], array(), true, array())->toArray();

                            // DebugUtil::printArray($funcionariosCliente, false);
                        } elseif ($data['opcao'] == 'doc_estrangeiro') {
                            // Pesquisa por Documento Estrangeiro
                            $funcionariosCliente = $this->Usuarios->getUsuariosByDocumentoEstrangeiro($data['parametro'], $rede['id'], array(), true, array())->toArray();

                        } elseif ($data['opcao'] == 'cpf') {
                            // Pesquisa por CPF

                            $funcionariosCliente[] = $this->Usuarios->getUsuarioByCPF($data["parametro"], $rede["id"], array(), true, array());
                        } else {
                            // Pesquisa por Placa
                            $retorno = $this->Veiculos->getUsuariosClienteByVeiculo($data['parametro'], $rede["id"], array(), true);

                            $veiculoEncontrado = $retorno["veiculo"];
                            $funcionariosCliente = $retorno["usuarios"];
                        }

                        // aqui não preciso fazer merge de funcionariosCliente com Usuários, pois ainda não teve a pesquisa dos usuários em si
                    }

                    // ---------- Daqui pra baixo não filtra por funcionários ----------

                    if ($data['opcao'] == 'nome') {
                        // Pesquisa por Nome

                        if ($restringirUsuariosRede) {
                            $usuarios = $this->Usuarios->getUsuariosByName($data['parametro'], $rede["id"], array(), false, array())->toArray();
                        } else {
                            $usuarios = $this->Usuarios->getUsuariosByName($data['parametro'], null, array(), false, array())->toArray();
                        }

                        // DebugUtil::printArray($usuarios, false);
                    } elseif ($data['opcao'] == 'doc_estrangeiro') {
                        // Pesquisa por Documento Estrangeiro

                        if ($restringirUsuariosRede) {
                            $usuarios = $this->Usuarios->getUsuariosByDocumentoEstrangeiro($data['parametro'], $rede['id'], array(), false, array())->toArray();
                        } else {
                            $usuarios = $this->Usuarios->getUsuariosByDocumentoEstrangeiro($data['parametro'], null, array(), false, array())->toArray();
                        }

                    } elseif ($data['opcao'] == 'cpf' && !isset($user)) {
                        // Pesquisa por CPF

                        if ($restringirUsuariosRede) {
                            $usuario = $this->Usuarios->getUsuarioByCPF($data["parametro"], $rede["id"], array(), false, array());
                        } else {
                            $usuario = $this->Usuarios->getUsuarioByCPF($data["parametro"], null, array(), false, array());
                        }

                        $usuarios[] = $usuario;
                    } else {

                        // Pesquisa por Placas

                        if ($restringirUsuariosRede) {
                            $retorno = $this->Veiculos->getUsuariosClienteByVeiculo($data['parametro'], $rede["id"], array(), false);
                        } else {
                            $retorno = $this->Veiculos->getUsuariosClienteByVeiculo($data['parametro'], null, array(), false);
                        }

                        $veiculoEncontrado = $retorno["veiculo"];
                        $usuarios = $retorno["usuarios"];

                        // print_r($data);
                        // echo PHP_EOL;
                        // echo __LINE__;
                        // echo PHP_EOL;
                        // print_r($retorno);
                    }

                    $usuarios = array_merge($funcionariosCliente, $usuarios);

                    $usuariosTemp = array();

                    foreach ($usuarios as $key => $value) {
                        if (!empty($value)) {
                            $pontuacoes = $this->Pontuacoes->getSumPontuacoesOfUsuario($value['id'], $rede["id"], array());

                            $value->pontuacoes = $pontuacoes["resumo_gotas"]["saldo"];
                            $value['data_nasc'] = !empty($value['data_nasc']) ? $value["data_nasc"]->format('d/m/Y') : null;

                            $usuariosTemp[] = $value;
                        }
                    }

                    $usuarios = $usuariosTemp;

                    $error = false;
                    $count = sizeof($usuarios);
                    $message = "";
                } else {
                    $error = true;
                    $message = "O argumento de pesquisa deve ser maior que 3 caracteres!";
                }

                $arraySet = [
                    "error",
                    "count",
                    "message",
                    "usuarios",
                    "veiculoEncontrado"
                ];

                $this->set(compact($arraySet));
                $this->set("_serialize", $arraySet);
            }
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $messageString = __("Erro ao pesquisar usuário!");

            $errors = $trace;
            $mensagem = ['status' => false, 'message' => $messageString, 'errors' => $errors];

            $messageStringDebug = __("{0} - {1} . [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $messageString, $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write("error", $messageStringDebug);
            Log::write("error", $trace);
        }
    }

    /**
     * Encontra usuário por Id
     *
     * @return \Cake\Http\Response|void
     */
    public function findUsuarioById()
    {
        try {
            $result = null;

            $user = null;
            $count = null;

            if ($this->request->is(['post'])) {
                $data = $this->request->getData();

                // verifica cliente (Usuário deve estar vinculado à rede, seja matriz ou filial)

                $usuariosId = isset($data["usuarios_id"]) ? (int)$data["usuarios_id"] : null;

                if (!empty($usuariosId)) {
                    $clientes_ids = $this->Clientes->getIdsMatrizFiliaisByClienteId($data['clientes_id']);

                    $cliente_has_usuario = $this->ClientesHasUsuarios->findClienteHasUsuarioInsideNetwork($data['usuarios_id'], $clientes_ids);

                    // achou usuário, retorna o objeto
                    if ($cliente_has_usuario->usuario) {
                        // consulta de pontuação, se encontrou usuário

                        $cliente_has_usuario->usuario['data_nasc'] = $cliente_has_usuario->usuario['data_nasc']->format('d/m/Y');

                        $cliente_has_usuario->usuario['pontuacoes']
                            = Number::precision(
                            $this->Pontuacoes->getSumPontuacoesOfUsuario(
                                $data['usuarios_id'],
                                null,
                                $clientes_ids
                            ),
                            2
                        );

                        // $result = json_encode(['user' => $cliente_has_usuario->usuario, 'count' => 1]);
                        $user = $cliente_has_usuario->usuario;
                        $count = 1;

                    }

                }
            }

            $arraySet = [
                'user',
                'count'
            ];

            $this->set(compact($arraySet));
            $this->set("_serialize", $arraySet);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao procurar usuário[{0}] ", $e->getMessage());

            Log::write('error', $stringError);
            Log::write('error', $trace);
        }
    }

    /**
     * ------------------------------------------------------------
     * Helpers
     * ------------------------------------------------------------
     */

    /**
     * Helper que envia e-mail ao usuário para resetar a senha
     *
     * @param string $url     Url de envio do link
     * @param object $usuario Entidade Usuário
     *
     * @return \Cake\Http\Response|void
     */
    private function _sendResetEmail($url, $usuario)
    {
        $email = new Email();
        $email->template('resetpw');
        $email->emailFormat('both');
        $email->to($usuario->email, $usuario->nome);
        $email->subject('Reset your password');
        $email->viewVars(['url' => $url, 'username' => $usuario->nome]);
        if ($email->send()) {
            $this->Flash->success(__('Verifique seu email pela requisição de reset de senha'));
        } else {
            $this->Flash->error(__('Erro ao enviar email :') . $email->smtpError);
        }
    }
}
