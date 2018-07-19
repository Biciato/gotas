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
use App\Custom\RTI\CryptUtil;
use App\Custom\RTI\DateTimeUtil;
use App\Custom\RTI\EmailUtil;
use App\Custom\RTI\GotasUtil;
use App\Custom\RTI\Security;
use App\Custom\RTI\SefazUtil;
use App\Custom\RTI\WebTools;

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
    protected $security_util = null;
    protected $datetime_util = null;
    protected $email_util = null;
    protected $gotas_util = null;
    protected $sefaz_util = null;
    protected $web_tools = null;
    protected $crypt_util = null;

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

            $this->user_logged = $this->getUserLogged();
            $this->set('user_logged', $this->getUserLogged());
        }

        // Seta encoding de JSON para não fazer escape
        $this->set("_jsonOptions", JSON_UNESCAPED_UNICODE);

        $url = $this->request->here;

        $isMobile = $this->request->header('IsMobile');

        if (!$isMobile) {
            $userAuthenticated = $this->request->session()->read("Auth.User");

            if (!$userAuthenticated && ($url != "/pages" && $url != "/usuarios/login" && $url != "/usuarios/registrar")) {
                $this->response = $this->redirect(['controller' => 'Pages', 'action' => 'display']);
                $this->response->send();
                die();
            }
        }

        $this->set('project_name', 'RTI Brindes');
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
        if (!array_key_exists('_serialize', $this->viewVars) &&
            in_array($this->response->type(), ['application/json', 'application/xml'])) {
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
        $this->response->header('Access-Control-Allow-Origin', '*');
        $this->response->header('Access-Control-Allow-Methods', '*');
        $this->response->header('Access-Control-Allow-Headers', 'X-Requested-With');
        $this->response->header('Access-Control-Allow-Headers', 'Content-Type, x-xsrf-token');
        $this->response->header('Access-Control-Max-Age', '172800');

        $this->_initializeUtils();

        $this->_setUserTemplatePath();

        // $this->Security->requireSecure();
    }



    /**
     * Inicializa as classes de utilidades
     *
     * @return void
     */
    private function _initializeUtils()
    {
        if (is_null($this->crypt_util)) {
            $this->crypt_util = new CryptUtil();
        }

        if (is_null($this->email_util)) {
            $this->email_util = new EmailUtil();
        }

        if (is_null($this->security_util)) {
            $this->security_util = new Security();
        }

        if (is_null($this->datetime_util)) {
            $this->datetime_util = new DateTimeUtil();
        }

        if (is_null($this->gotas_util)) {
            $this->gotas_util = new GotasUtil();
        }

        if (is_null($this->sefaz_util)) {
            $this->sefaz_util = new SefazUtil();
        }

        if (is_null($this->web_tools)) {
            $this->web_tools = new WebTools();
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
        if ($this->request->session()->read("Auth.User")) {
            $user_logged = $this->Auth->user();
        }

        // verifica se está sendo administrado algum usuário, caso contrário prossegue

        $user_admin = $this->request->session()->read('User.RootLogged');
        $user_managed = $this->request->session()->read('User.ToManage');

        if ($user_admin) {
            $user_logged = $user_managed;
        }

        if (!empty($user_logged)) {
            if ($user_logged['tipo_perfil'] == Configure::read('profileTypes')['AdminDeveloperProfileType']) {
                $this->viewBuilder()->setLayout('template_desenvolvedor');

                Router::connect('/', ['controller' => 'pages', 'action' => 'DashboardDesenvolvedor']);
            } else if ($user_logged['tipo_perfil'] >= Configure::read('profileTypes')['AdminNetworkProfileType'] && $user_logged['tipo_perfil'] <= Configure::read('profileTypes')['AdminLocalProfileType']) {
                $this->viewBuilder()->setLayout('template_administrador');
                Router::connect('/', ['controller' => 'pages', 'action' => 'DashboardAdministrador']);
            } else if ($user_logged['tipo_perfil'] == Configure::read('profileTypes')['ManagerProfileType']) {
                $this->viewBuilder()->setLayout('template_gerente');
                Router::connect('/', ['controller' => 'pages', 'action' => 'dashboard_gerente']);

            } else if ($user_logged['tipo_perfil'] == Configure::read('profileTypes')['WorkerProfileType']) {
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

    /**
     * Converte string de base 64 para arquivo jpg
     *
     * @param string $base64_string
     * @param object $output_file
     *
     * @return void
     */
    public function generateImageFromBase64($base64_string, $output_file, $path_destination)
    {
        try {
            $this->createPathIfNotExists($path_destination);
            // abre o arquivo destino para edição
            $ifp = fopen($output_file, 'wb');

            // separa a string por virgulas, para criar os dados

            $data = explode(',', $base64_string);

            // escreve os dados no arquivo destino
            fwrite($ifp, base64_decode($data[1]));

            // fecha o arquivo destino
            fclose($ifp);

            chmod($output_file, 0766);

            return $output_file;
        } catch (\Exception $e) {
            $this->log($e->getMessage());
        }
    }

    /**
     * Rotates a Image
     *
     * @param string $image_path
     * @param int $degrees
     * @return bool
     */
    public function rotateImage(string $image_path, int $degrees)
    {
        try {
            $source = imagecreatefromjpeg($image_path);

            $rotate = \imagerotate($source, $degrees, 0);

            $result = imagejpeg($rotate, $image_path);

            return $result;
        } catch (\Exception $e) {
            Log::write('error', $e->getMessage());
        }
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

}
