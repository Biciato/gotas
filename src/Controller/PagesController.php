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

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Log\Log;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\I18n\Number;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link http://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class PagesController extends AppController
{

    /**
     * ------------------------------------------------------------
     * Fields
     * ------------------------------------------------------------
     */
    protected $usuarioLogado = null;

    /**
     * Initialize function
     */
    public function initialize()
    {
        parent::initialize();

        $this->Auth->allow(['display']);
    }

    /**
     * Displays a view
     *
     * @param string ...$path Path segments.
     * @return void|\Cake\Network\Response
     * @throws \Cake\Network\Exception\ForbiddenException When a directory traversal attempt.
     * @throws \Cake\Network\Exception\NotFoundException When the view file could not
     *   be found or \Cake\View\Exception\MissingTemplateException in debug mode.
     */
    public function display(...$path)
    {

        $sessaoUsuario = $this->getSessionUserVariables();

        $usuarioLogado = $sessaoUsuario["usuarioLogado"];

        // $user = $this->request->session()->read('Auth.User');
        $user = $this->Auth->user();

        if (!$this->request->is(['post'])) {

            if (!$user) {
                $this->redirect(
                    [
                        'controller' => 'usuarios',
                        'action' => 'login'
                    ]
                );
            }
        }



        $usuarioAdministrador = $sessaoUsuario["usuarioAdministrador"];
        $usuarioAdministrar  = $sessaoUsuario["usuarioAdministrar"];

        if ($usuarioAdministrador) {
            $this->usuarioLogado = $usuarioAdministrar->toArray();
        }

        $this->setDashboard($this->usuarioLogado);

        $count = count($path);
        if (!$count) {
            return $this->redirect('/');
        }
        if (in_array('..', $path, true) || in_array('.', $path, true)) {
            throw new ForbiddenException();
        }
        $page = $subpage = null;

        if (!empty($path[0])) {
            $page = $path[0];
        }
        if (!empty($path[1])) {
            $subpage = $path[1];
        }
        $this->set(compact('page', 'subpage'));

        try {
            $this->render(implode('/', $path));
        } catch (MissingTemplateException $e) {
            if (Configure::read('debug')) {
                throw $e;
            }
            throw new NotFoundException();
        }
    }

    /**
     * Configura a dashboard a ser usada pelo usuário logado
     *
     * @param array $usuarioLogado Usuário logado
     *
     * @return void
     */
    public function setDashboard(array $usuarioLogado = null)
    {
        if (!empty($usuarioLogado)) {
            if ($usuarioLogado['tipo_perfil'] == Configure::read('profileTypes')['AdminDeveloperProfileType']) {
                $this->dashboardDesenvolvedor();
            } else if ($usuarioLogado['tipo_perfil'] >= Configure::read('profileTypes')['AdminNetworkProfileType'] && $usuarioLogado['tipo_perfil'] <= Configure::read('profileTypes')['AdminLocalProfileType']) {
                $this->dashboardAdministrador();
            } else if ($usuarioLogado['tipo_perfil'] == Configure::read('profileTypes')['ManagerProfileType']) {
                $this->dashboardGestor();
            } else if ($usuarioLogado['tipo_perfil'] == Configure::read('profileTypes')['WorkerProfileType']) {
                $this->dashboardFuncionario();
            } else {
                $this->dashboardCliente();
            }
        } else {
            $this->dashboardCliente();
        }

    }

    /**
     * Displays a developer dashboard view
     *
     */
    public function dashboardDesenvolvedor()
    {
        $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
        $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

        if ($usuarioAdministrador) {
            $this->usuarioLogado = $usuarioAdministrar;
        }

        if ($this->usuarioLogado['tipo_perfil'] > 0) {
            $this->flash->warning('Esta dashboard só pode ser visualizada por um desenvolvedor');
            $this->redirectUrl(['controller' => 'pages', ['action' => 'index']]);
        }
    }


    /**
     * Displays a admin dashboard view
     *
     */
    public function dashboardAdministrador()
    {
        try {
            $brindes_aguardando_autorizacao = [];

            $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
            $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

            if ($usuarioAdministrador) {
                $this->usuarioLogado = $usuarioAdministrar;
            }

            $rede = $this->request->session()->read('Rede.Grupo');

            // Pega unidades que tem acesso
            $clientesIds = [];

            $unidadesIds = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($rede->id, $this->usuarioLogado['id'], false);

            foreach ($unidadesIds as $key => $value) {
                $clientesIds[] = $key;
            }

            if (count($clientesIds) > 0) {
                $brindes_aguardando_autorizacao = $this->ClientesHasBrindesHabilitadosPreco->getPrecoAwaitingAuthorizationByClientesId($clientesIds);
            }

            $clientes_id = null;

            if (count($clientesIds) == 1) {
                $clientes_id = $clientesIds[0];
            }

            $this->set(compact(['brindes_aguardando_autorizacao', 'cliente_admin', 'clientes_id']));
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $this->Flash->error(__("Não foi possível realizar o procedimento. Entre em contato com o suporte. Descrição do erro: {0} em: {1}", $e->getMessage(), $trace[1]));


            Log::write('error', __("Não foi possível realizar o procedimento. Entre em contato com o suporte. Descrição do erro: {0} em: {1}", $e->getMessage(), $trace[1]));
        }
    }


    /**
     * Displays a manager dashboard view
     *
     */
    public function dashboardGestor()
    {
        $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
        $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

        if ($usuarioAdministrador) {
            $this->usuarioLogado = $usuarioAdministrar;
        }
    }


    /**
     * Configura a dashboard para funcionário
     *
     */
    public function dashboardFuncionario()
    {
        $sessaoUsuario = $this->getSessionUserVariables();
        $usuarioAdministrador = $sessaoUsuario["usuarioAdministrador"];
        $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"];
        $usuarioLogado = $sessaoUsuario["usuarioLogado"];
        $cliente = $sessaoUsuario["cliente"];
        $rede = $sessaoUsuario["rede"];

        $usuario = $this->Usuarios->newEntity();
        $transportadora = $this->Transportadoras->newEntity();
        $veiculo = $this->Veiculos->newEntity();

        $funcionario = $this->Usuarios->getUsuarioById($this->usuarioLogado['id']);

        $rede = $this->request->session()->read('Rede.Grupo');

        // Pega unidades que tem acesso
        $clientesIds = [];

        $unidades_ids = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($rede["id"], $this->usuarioLogado['id'], false);

        foreach ($unidades_ids as $key => $value) {
            $clientesIds[] = $key;
        }

        // No caso do funcionário, ele só estará em uma unidade, então pega o cliente que ele estiver

        $cliente = $this->Clientes->getClienteById($clientesIds[0]);

        // o estado do funcionário é o local onde se encontra o estabelecimento.
        $estado_funcionario = $cliente->estado;

        // na verdade, o perfil deverá ser 6, pois no momento do cadastro do funcionário
        // $usuarioLogadoTipoPerfil = $funcionario->tipo_perfil;
        $usuarioLogadoTipoPerfil = 6;
        $this->set(compact(['usuario', 'cliente', 'funcionario', 'estado_funcionario', 'usuarioLogadoTipoPerfil', 'usuarioLogado']));

        $this->set('transportadoraPath', 'TransportadorasHasUsuarios.Transportadoras.');
        $this->set('veiculoPath', 'UsuariosHasVeiculos.Veiculos.');
    }

    /**
     * Exibe a dashboard de um cliente
     *
     * @return void
     */
    public function dashboardCliente()
    {
        if ($this->usuarioLogado) {

            // id do usuário
            $usuarios_id = $this->usuarioLogado['id'];

            // localiza quais as unidades o usuário tem pontuacao

            $unidades_ids_query = $this->PontuacoesComprovantes->getAllClientesIdFromCoupons(['usuarios_id' => $usuarios_id]);

            $unidades_ids = [];

            foreach ($unidades_ids_query->toArray() as $key => $value) {
                $unidades_ids[] = $value->clientes_id;
            }

            $redes = array();

            if (count($unidades_ids) > 0) {

            // obtem o id de redes através dos ids de clientes, de forma distinta

                $redes_array = $this->RedesHasClientes->getRedesHasClientesByClientesIds($unidades_ids);

                $redes_ids = [];

                foreach ($redes_array->toArray() as $key => $value) {
                    $redes_ids[] = $value->redes_id;
                }

            /* agora tenho o id das redes que o usuário está vinculado.
                 * Pegar informações de cada rede, total de
                 * pontos acumulados, e brindes fornecidos
                 */

                $redes = [];

                foreach ($redes_ids as $key => $rede_id) {

                    $rede = $this->Redes->getRedeById($rede_id);

                    $rede->nome_img = strlen($rede->nome_img) > 0 ? Configure::read('imageNetworkPathRead') . $rede->nome_img : null;

                // pega o id das unidades para obter a soma de pontos
                    $unidades_ids = [];
                    foreach ($rede->redes_has_clientes as $key => $value) {
                        $unidades_ids[] = $value->clientes_id;
                    }

                    $soma_pontos = $this->Pontuacoes->getSumPontuacoesOfUsuario($usuarios_id, $rede["id"], $unidades_ids);

                    $rede['soma_pontos'] = Number::precision($soma_pontos, 2);
                    $redes[] = $rede;
                }

            }

            // debug($redes);

            $this->set(compact('redes'));
            $this->set('_serialize', ['redes']);
        }
    }
}
