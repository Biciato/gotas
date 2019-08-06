<?php

/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\Routing\Router;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use App\Custom\RTI\CryptUtil;
use App\Custom\RTI\DateTimeUtil;
use App\Custom\RTI\EmailUtil;
use App\Custom\RTI\GotasUtil;
use App\Custom\RTI\Security;
use App\Custom\RTI\SefazUtil;
use App\Custom\RTI\WebTools;
use App\Custom\RTI\DebugUtil;
use Cake\Log\Log;
use App\Custom\RTI\ResponseUtil;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link http://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{

    /**
     * ------------------------------------------------------------
     * Campos
     * ------------------------------------------------------------
     */
    var $persistModel = true;
    protected $securityUtil = null;
    protected $datetime_util = null;
    protected $emailUtil = null;
    protected $gotasUtil = null;
    protected $sefazUtil = null;
    protected $webTools = null;
    protected $cryptUtil = null;

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        // $this::chooseDatabaseConnection();

        $this->loadComponent(
            'Auth',
            [
                'authError' => 'Você não tem permissão para acessar o local solicitado.',
                'allowedActions' =>
                [
                    'controller' => 'pages',
                    'action' => 'index', 'display'
                ],

                // 'unauthorizedRedirect' => true,
                // 'unauthorizedRedirect' => [
                //     'controller' => 'pages',
                //     'action' => 'display'
                // ],
                // 'unauthorizedRedirect' => "/usuarios/login",
                // 'unauthorizedRedirect' => $this->referer(),
                'loginAction' => [
                    'controller' => 'usuarios',
                    'action' => 'login',
                    // 'prefix' => false
                ],
                'loginRedirect' => [
                    'controller' => 'pages',
                    'action' => 'index'
                ],
                'logoutRedirect' => [
                    'controller' => 'pages',
                    'action' => 'display', 'home'
                ],

                // 'storage' => 'Memory',
                'unauthorizedRedirect' => false,
                'authenticate' => [
                    'Basic' => [
                        'fields' => [
                            'username' => 'email',
                            'password' => 'senha'
                        ],
                        'userModel' => 'usuarios',
                    ],
                    'Form' => [
                        'fields' => [
                            'username' => 'email',
                            'password' => 'senha'
                        ],
                        'scope' => ['usuarios.conta_ativa' => 1],
                        'userModel' => 'usuarios',
                    ],
                    'ADmad/JwtAuth.Jwt' => [
                        'parameter' => 'token',
                        'userModel' => 'Usuarios',
                        'scope' => ['usuarios.conta_ativa' => 1],
                        'fields' => [
                            'email' => 'id'
                        ],
                        'queryDatasource' => true
                    ]
                ],
                'checkAuthIn' => 'Controller.initialize'
            ]
        );

        if ($this->getUserLogged()) {

            $this->usuarioLogado = $this->getUserLogged();
            $this->set('usuarioLogado', $this->getUserLogged());
        }

        // Seta encoding de JSON para não fazer escape
        $this->set("_jsonOptions", JSON_UNESCAPED_UNICODE);

        $this->checkAuthentication();

        $this->set('project_name', 'GOTAS');
        $this->viewBuilder()->theme('TwitterBootstrap');

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');
        $this->loadComponent('Cookie');
        // $this->loadComponent('Security', ['blackHoleCallback' => 'forceSSL']);

        /*
         * Enable the following components for recommended CakePHP security settings.
         * see http://book.cakephp.org/3.0/en/controllers/components/security.html
         */
        // $this->loadComponent('Security');
        // $this->loadComponent('Csrf');

        $this->loadNecessaryModels(Configure::read('models'));
    }

    /**
     * Force SSL
     *
     * @return void
     */
    public function forceSSL()
    {
        return $this->redirect('https://' . env('SERVER_NAME') . $this->here);
    }

    /**
     * Before render callback.
     *
     * @param \Cake\Event\Event $event The beforeRender event.
     *
     * @return \Cake\Network\Response|null|void
     */
    public function beforeRender(Event $event)
    {
        // $user = $this->Auth->user();

        // if (!empty($user)) {
        //     $user = $this->Usuarios->get($user["id"]);
        // }

        // $getRequest = $this->request->is('get');

        // if ($getRequest && (!empty($user))
        //     && ($user["tipo_perfil"] == Configure::read("profileTypes")["UserProfileType"])
        //     && (empty($user["cpf"]) == 1 && empty($user["doc_estrangeiro"] == 1))) {
        //     $controllerAtual = $this->request->getParam("controller");
        //     $actionAtual = $this->request->getParam("action");
        //     $urlDestino = Router::url(array("controller" => "Usuarios", "action" => "editar"));

        //     $urlAtual = strtolower(__("/{0}/{1}", $controllerAtual, $actionAtual));
        //     if ($urlAtual != $urlDestino) {
        //         $this->Flash->error(Configure::read("messageUsuarioProfileDocumentNotFoundError"));
        //         return $this->redirect(array("controller" => "Usuarios", "action" => "editar" , $user["id"]));
        //     }
        // }

        // Trata resposta iOS
        if ($this->request->is('options')) {
            exit(0);
        }

        if (
            !array_key_exists('_serialize', $this->viewVars) &&
            in_array($this->response->type(), ['application/json', 'application/xml'])
        ) {
            $this->set('_serialize', true);
        }
    }

    /**
     * Before filter callback
     *
     * @return \Cake\Event\Event $event The beforeFilter event.
     * @return \Cake\Network\Response|null|void
     **/
    public function beforeFilter(Event $event)
    {
        //  parent::beforeFilter();

        // Trata resposta iOS
        if ($this->request->is('options')) {
            exit(0);
        }


        $this->_initializeUtils();
        $this->_setUserTemplatePath();

        $user = $this->Auth->user();

        if (!empty($user)) {
            $id = $user["id"];
            $token = !empty($user["token"]) ? $user["token"] : null;
            $userToken = $this->UsuariosTokens->getTokenUsuario($id, $token);

            $isMobile = $this->request->header('IsMobile');

            if (empty($userToken)) {
                // Redireciona o usuário se não tiver mais o token
                $this->Auth->logout();
                $this->clearCredentials();

                if ($isMobile) {
                    // Resposta para API
                    return ResponseUtil::logoutResponseAPI();
                }

                // Resposta HTTP
                return $this->redirect($this->Auth->logout());
            }
        } else {
            $this->clearCredentials();
            return $this->checkAuthentication();
        }
        // $this->Security->requireSecure();
    }

    private function checkAuthentication()
    {
        $url = $this->request->here;

        // $this->setCorsHeaders();

        $isMobile = $this->request->header('IsMobile');

        if (!$isMobile) {
            $userAuthenticated = $this->request->session()->read("Auth.User");

            $urlPages = array(
                "/pages",
                "/pages/instalaMobile",
                "/pages/test",
                "/usuarios/login",
                "/usuarios/registrar",
                "/usuarios/esqueci-minha-senha",
                "/usuarios/resetar-minha-senha",
            );

            $found = 0;
            foreach ($urlPages as $page) {
                $indexFound = stripos($url, $page) !== false;

                if ($indexFound) {
                    $found = true;
                    break;
                }
            }

            if (!$userAuthenticated && (!$found)) {
                $this->response = $this->redirect(['controller' => 'Pages', 'action' => 'display']);
                $this->response->send();
                die();
            }
        }
    }

    /**
     * AppController::clearCredentials
     *
     * Limpa todas as credenciais e variável de sessão da sessão atual
     *
     * @return void
     */
    public function clearCredentials()
    {
        $this->request->session()->delete("Usuario.AdministradorLogado");
        $this->request->session()->delete("Usuario.Administrar");
        $this->request->session()->delete('Rede.Grupo');
        $this->request->session()->delete('Rede.PontoAtendimento');
    }

    /**
     * Inicializa as classes de utilidades
     *
     * @return void
     */
    private function _initializeUtils()
    {
        if (is_null($this->cryptUtil)) {
            $this->cryptUtil = new CryptUtil();
        }

        if (is_null($this->emailUtil)) {
            $this->emailUtil = new EmailUtil();
        }

        if (is_null($this->securityUtil)) {
            $this->securityUtil = new Security();
        }

        if (is_null($this->datetime_util)) {
            $this->datetime_util = new DateTimeUtil();
        }

        if (is_null($this->gotasUtil)) {
            $this->gotasUtil = new GotasUtil();
        }

        if (is_null($this->sefazUtil)) {
            $this->sefazUtil = new SefazUtil();
        }

        if (is_null($this->webTools)) {
            $this->webTools = new WebTools();
        }
    }

    /**
     * Verifica qual usuário está logado (se está logado) e determina
     * o layout e o roteamento que irá usar para navegação
     *
     * @return void
     */
    private function _setUserTemplatePath()
    {
        $usuarioLogado = $this->getUserLogged();

        // verifica se está sendo administrado algum usuário, caso contrário prossegue

        $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
        $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

        if ($usuarioAdministrador) {
            $usuarioLogado = $usuarioAdministrar;
        }

        if (!empty($usuarioLogado)) {
            if ($usuarioLogado['tipo_perfil'] == Configure::read('profileTypes')['AdminDeveloperProfileType']) {
                $this->viewBuilder()->setLayout('template_desenvolvedor');

                Router::connect('/', ['controller' => 'pages', 'action' => 'DashboardDesenvolvedor']);
            } else if ($usuarioLogado['tipo_perfil'] >= Configure::read('profileTypes')['AdminNetworkProfileType'] && $usuarioLogado['tipo_perfil'] <= Configure::read('profileTypes')['AdminLocalProfileType']) {
                $this->viewBuilder()->setLayout('template_administrador');
                Router::connect('/', ['controller' => 'pages', 'action' => 'DashboardAdministrador']);
            } else if ($usuarioLogado['tipo_perfil'] == Configure::read('profileTypes')['ManagerProfileType']) {
                $this->viewBuilder()->setLayout('template_gerente');
                Router::connect('/', ['controller' => 'pages', 'action' => 'dashboard_gerente']);
            } else if ($usuarioLogado['tipo_perfil'] == Configure::read('profileTypes')['WorkerProfileType']) {
                $this->viewBuilder()->setLayout('template_funcionario');
                Router::connect('/', ['controller' => 'pages', 'action' => 'DashboardFuncionario']);
            } else {
                $this->viewBuilder()->setLayout('template_usuario');
                Router::connect('/', ['controller' => 'pages', 'action' => 'DashboardUsuario']);
            }
        } else {
            $this->viewBuilder()->setLayout('template_usuario');
        }
    }

    /**
     * Retorna usuário logado pela sessão
     * @return Cake\Auth\Storage Session
     */
    public function getUserLogged()
    {
        // $user = $this->Auth->user();
        return $this->request->session()->read('Auth.User');
    }

    /**
     * Carrega todas as Models necessárias informadas em um array
     * @param array $models
     * @author Gustavo Souza Gonçalves
     * @return void
     */
    public function loadNecessaryModels($models = null)
    {
        foreach ($models as $key => $model) {
            $this->loadModel($model);
        }
    }

    public function getSessionUserVariables()
    {
        $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
        $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');
        $usuarioLogado = $this->getUserLogged();
        $cliente = $this->request->session()->read("Rede.PontoAtendimento");
        $rede = $this->request->session()->read("Rede.Grupo");

        // Certifica que o usuário em questão está vinculado a uma rede
        if (empty($rede) && !empty($cliente)) {
            // verifica qual rede o usuário se encontra (somente funcionários)
            $redeHasCliente = $this->RedesHasClientes->getRedesHasClientesByClientesId($cliente["id"]);
            $rede = $redeHasCliente["rede"];
        }

        if ($usuarioAdministrar && $usuarioLogado["tipo_perfil"] == PROFILE_TYPE_ADMIN_DEVELOPER) {
            $this->usuarioLogado = $usuarioAdministrar;
            $usuarioLogado = $usuarioAdministrar;
        }

        $arraySet = array(
            "usuarioAdministrador",
            "usuarioAdministrar",
            "usuarioLogado",
            "rede",
            "cliente"
        );

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);

        return array(
            "usuarioAdministrador" => $usuarioAdministrador,
            "usuarioAdministrar" => $usuarioAdministrar,
            "usuarioLogado" => $usuarioLogado,
            "rede" => $rede,
            "cliente" => $cliente
        );
    }
    /**
     * Verifica se caminho existe. Se não existir, cria novo caminho
     *
     * @return void
     * @author
     **/
    public function createPathIfNotExists($path)
    {
        try {
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
        } catch (\Exception $e) {
            $this->log($e->getMessage());
        }
    }

    /**
     * Move documento informado para novo caminho
     */
    public function moveDocumentPermanently($originalPath, $newPath, $newName = null, $extension = null)
    {
        try {
            if (is_null($newName)) {
                $newName = bin2hex(openssl_random_pseudo_bytes(16));
            }

            $this->createPathIfNotExists($newPath);

            $extension = "jpg";
            $dotPosition = strlen(strpos($extension, ".")) > 0 ? 1 : 0;

            if (!$dotPosition) {
                $extension = "." . $extension;
            }

            $newGeneratedName = $newName . $extension;

            $newPath = $newPath . $newName . $extension;
            rename($originalPath, $newPath);

            return $newGeneratedName;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao mover documento: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Faz o upload de Imagem para caminho informado
     *
     * @param string $file      Nome da imagem recebida
     * @param string $newPath   Novo caminho
     * @param string $newName   Novo nome da imagem gerada
     * @param string $extension Extensão do arquivo
     *
     * @return string Nome arquivo gerado
     */
    public function uploadImage(array $file, string $newPath, string $newName = null, string $extension = null)
    {
        try {
            if (is_null($newName)) {
                $newName = bin2hex(openssl_random_pseudo_bytes(16));
            }

            $this->createPathIfNotExists($newPath);

            if (is_null($extension)) {
                $extension = substr($file['name'], stripos($file['name'], "."));
            }

            $newGeneratedName = $newName . $extension;

            $newPath = $newPath . $newName . $extension;
            move_uploaded_file($file['tmp_name'], WWW_ROOT . $newPath);

            return $newGeneratedName;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao mover documento: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Remove todos os caracteres não numéricos para guardar sem formatação no BD
     *
     * @param string $value CNPJ
     *
     * @deprecated 1.0 Mudar para NumberUtil::limparFormatacaoNumeros
     * @return string
     */
    public function cleanNumber($value)
    {
        return preg_replace('/[^0-9]/', "", $value);
    }

    /**
     * Remove todos os caracteres não numéricos para guardar sem formatação no BD
     *
     * @param string $value CNPJ
     *
     * @return string
     */
    public function cleanNumberAndLetters($value)
    {
        return preg_replace('/[^A-Za-z0-9?!]/', "", $value);
    }

    /**
     * Define a conexão ao banco
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2017-08-01
     *
     * @return void
     */
    public static function chooseDatabaseConnection()
    {
        // Troca base de dados
        if (Configure::read("environmentMode") == "development") {
            ConnectionManager::alias("devel", "default");
        }
    }
}
