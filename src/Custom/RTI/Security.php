<?php
namespace App\Custom\RTI;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;

use Cake\Core\Configure;

/**
 * @author Gustavo Souza Gonçalves
 * @date 03/08/2017
 * @path vendor\rti\SecurityClass.php
 */


/**
 * Classe para operações de segurança
 */
class Security
{
    public $prop = null;

    function __construct()
    {
    }

    /**
     * Verifica se usuário está autorizado à acessar uma action
     *
     * @param array $user_logged
     * @param string $minimumProfileType
     * @param string $maximumProfileType
     * @return boolean
     * @author Gustavo Souza Gonçalves
     **/
    public function checkUserIsAuthorized($user_logged, $minimumProfileType, $maximumProfileType = null)
    {
        $profileTypes = Configure::read('profileTypes');

        $minValue = $profileTypes[$minimumProfileType];

        $maxValue = null;

        if (isset($maximumProfileType)) {
            $maxValue = $profileTypes[$maximumProfileType];
        }

        if ($maxValue != null) {
            if (($user_logged['tipo_perfil'] <= $minValue) || ($user_logged['tipo_perfil'] >= $maxValue)) {
                return true;
                // return false;
            } else {
                // return true;
                return false;
            }
        } else {
            if ($user_logged['tipo_perfil'] >= $minValue) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Security::checkUserIsRedeRouteAllowed
     *
     * Verifica se usuário tem permissão de acesso à rede
     *
     * @param array<Usuario> $user
     * @param integer $redesId Id de rede
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 2018/05/16
     *
     * @return bool
     */
    public function checkUserIsRedeRouteAllowed(array $user, int $redesId, $thisInstance)
    {
        // Se usuário logado é Administrador de Rede, verifica se ele tem acesso à rede inicialmente
        if ($user["tipo_perfil"] == Configure::read("profileTypes")["AdminNetworkProfileType"]) {

            // Pega qual é a rede que ele logou

            $rede = $thisInstance->request->session()->read("Network.Main");

            if ($rede["id"] != $redesId) {
                $this->redirectUserNotAuthorized($thisInstance, $user);
                return false;
            }
            return true;
        }
    }

    /**
     * Retorna associação entre usuário e cliente
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 2018/05/15
     *
     * @return boolean Redireciona se usuário não tem associação com cliente (acesso negado)
     **/
    public function checkUserIsClienteRouteAllowed($user, $clienteTable, $clienteHasUsuariosTable, array $clientesIds = array(), int $redesId = null)
    {
        // Verifica se o usuário é um Administrador de Rede ou Administrador da RTI. Se for e tiver algum registro vinculado na pesquisa,
        // então possui acesso.

        // É Administrador RTI / Devel, retorna True
        if ($user["tipo_perfil"] == Configure::read("profileTypes")["AdminDeveloperProfileType"]) {
            return 1;
        }

        // É adminstrador de Rede, verifica se ele tem acesso à alguma unidade
        if ($user["tipo_perfil"] == Configure::read("profileTypes")["AdminNetworkProfileType"]) {

            // Pega o id de todos os clientes daquela rede

            $redesHasClientesTable = TableRegistry::get('RedesHasClientes');
            $redeHasClientes = $redesHasClientesTable->getAllRedesHasClientesIdsByRedesId($redesId);
            $temAcesso = 0;

            foreach ($redeHasClientes as $key => $redeHasCliente) {
                foreach ($clientesIds as $key => $clienteId) {
                    if ($redeHasCliente["clientes_id"] == $clienteId) {
                        $temAcesso = 1;
                        break;
                    }
                }
            }

            if (!$temAcesso) {
                return 0;
            }

            return 1;
        } else if ($user["tipo_perfil"] >= Configure::read("profileTypes")["AdminRegionalProfileType"]
            && $user["tipo_perfil"] <= Configure::read("profileTypes")["WorkerProfileType"]) {

            // Se usuário logado for Regional ou Funcionário, verificar através do ID passado
            $cliente = $clienteTable->getClienteMatrizLinkedToUsuario($user);
            $hasAccess = 0;

            foreach ($clientesIds as $clienteId) {
                if ($clienteId == $cliente["id"]) {
                    $hasAccess = 1;
                    break;
                }
            }

            return $hasAccess;
        }
        // $hasAccess = !is_null(
        //     $clienteHasUsuariosTable->findClienteHasUsuario(
        //         [
        //             'ClientesHasUsuarios.usuarios_id' => $user['id'],
        //             'ClientesHasUsuarios.clientes_id' => $cliente->id,
        //             'ClientesHasUsuarios.tipo_perfil' => $user['tipo_perfil'],
        //         ]
        //     )
        // );

        // if (!$hasAccess) {
        //     $this->redirectUserNotAuthorized($this, $user);
        // }

        // return $cliente;
    }

    /**
     * Redireciona usuário se acesso negado
     */
    public function redirectUserNotAuthorized($objectThis, $user = null)
    {
        $objectThis->Flash->error(Configure::read('messageNotAuthorized'));

        return $objectThis->redirect(['controller' => 'pages', 'action' => 'index']);
    }

    /**
     * Verifica se usuário é administrador de uma rede
     *
     * @return void
     **/
    public function checkIfUserIsMatrizAdmin($cliente, $objectThis)
    {
        if (!is_null($cliente->matriz_id)) {
            $this->redirectUserNotAuthorized($objectThis);
        }
    }
}
