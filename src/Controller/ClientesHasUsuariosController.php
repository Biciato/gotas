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

/**
 * ClientesHasUsuarios Controller
 *
 * @property \App\Model\Table\ClientesHasUsuariosTable $ClientesHasUsuarios
 *
 * @method \App\Model\Entity\ClientesHasUsuario[] paginate($object = null, array $settings = [])
 */
class ClientesHasUsuariosController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Clientes', 'Usuarios']
        ];
        $clientesHasUsuarios = $this->paginate($this->ClientesHasUsuarios);

        $this->set(compact('clientesHasUsuarios'));
        $this->set('_serialize', ['clientesHasUsuarios']);
    }

    /**
     * View method
     *
     * @param string|null $id Clientes Has Usuario id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $clientesHasUsuario = $this->ClientesHasUsuarios->get($id, [
            'contain' => ['Clientes', 'Usuarios']
        ]);

        $this->set('clientesHasUsuario', $clientesHasUsuario);
        $this->set('_serialize', ['clientesHasUsuario']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $clientesHasUsuario = $this->ClientesHasUsuarios->newEntity();
        if ($this->request->is('post')) {
            $clientesHasUsuario = $this->ClientesHasUsuarios->patchEntity($clientesHasUsuario, $this->request->getData());
            if ($this->ClientesHasUsuarios->save($clientesHasUsuario)) {
                $this->Flash->success(__('The clientes has usuario has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The clientes has usuario could not be saved. Please, try again.'));
        }
        $clientes = $this->ClientesHasUsuarios->Clientes->find('list', ['limit' => 200]);
        $usuarios = $this->ClientesHasUsuarios->Usuarios->find('list', ['limit' => 200]);
        $this->set(compact('clientesHasUsuario', 'clientes', 'usuarios'));
        $this->set('_serialize', ['clientesHasUsuario']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Clientes Has Usuario id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $clientesHasUsuario = $this->ClientesHasUsuarios->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $clientesHasUsuario = $this->ClientesHasUsuarios->patchEntity($clientesHasUsuario, $this->request->getData());
            if ($this->ClientesHasUsuarios->save($clientesHasUsuario)) {
                $this->Flash->success(__('The clientes has usuario has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The clientes has usuario could not be saved. Please, try again.'));
        }
        $clientes = $this->ClientesHasUsuarios->Clientes->find('list', ['limit' => 200]);
        $usuarios = $this->ClientesHasUsuarios->Usuarios->find('list', ['limit' => 200]);
        $this->set(compact('clientesHasUsuario', 'clientes', 'usuarios'));
        $this->set('_serialize', ['clientesHasUsuario']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Clientes Has Usuario id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete()
    {
        $data = $this->request->query();
        $clientes_has_usuarios_id = $data['clientes_has_usuarios_id'];
        $return_url = $data['return_url'];

        $this->request->allowMethod(['post', 'delete']);
        $clientesHasUsuario = $this->ClientesHasUsuarios->get($clientes_has_usuarios_id);
        if ($this->ClientesHasUsuarios->delete($clientesHasUsuario)) {
            $this->Flash->success(__('The clientes has usuario has been deleted.'));
        } else {
            $this->Flash->error(__('The clientes has usuario could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Usuario id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function editarAdministracao($id = null)
    {
        try {
            $user_admin = $this->request->session()->read('User.RootLogged');
            $user_managed = $this->request->session()->read('User.ToManage');

            if ($user_admin) {
                $this->user_logged = $user_managed;
                $user_logged = $user_managed;
            }

            $usuario = $this->Usuarios->getUsuarioById($id);

            $clientes_has_usuarios_conditions = [];

            array_push($clientes_has_usuarios_conditions, ['ClientesHasUsuarios.usuarios_id' => $id]);
            array_push($clientes_has_usuarios_conditions, ['ClientesHasUsuarios.tipo_perfil' => $usuario->tipo_perfil]);

            $clientes_has_usuarios_query = $this->ClientesHasUsuarios->findClienteHasUsuario($clientes_has_usuarios_conditions);

            // debug($clientes_has_usuarios_query->toArray());
            // die();
            // tenho o cliente alocado, pegar agora a rede que ele está
            $cliente_has_usuario = $clientes_has_usuarios_query->toArray()[0];
            $cliente = $cliente_has_usuario->cliente;

            $rede_has_cliente = $this->RedesHasClientes->getRedesHasClientesByClientesId($cliente_has_usuario->clientes_id);

            $rede = $this->Redes->getRedeById($rede_has_cliente->rede->id);

            $clientes_ids = [];

            foreach ($rede->redes_has_clientes as $key => $value) {
                array_push($clientes_ids, $value->clientes_id);
            }

            $where_conditions = [];

            array_push($where_conditions, ['id IN' => $clientes_ids]);

            $clientes = $this->Clientes->getAllClientes($where_conditions);

            $arraySet = array('usuario', 'usuario_logado_tipo_perfil', 'rede', 'clientes', "user_logged");

            $this->set(compact($arraySet));
            $this->set('_serialize', $arraySet);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao realizar remoção de unidade de uma rede: {0} em: {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);

            return $this->redirect($query['return_url']);
        }
    }

    /**
     * Permite a administração a um determinado administrador
     *
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     */
    public function atribuirAdministracao()
    {
        try {
            $query = $this->request->query();

            $clientes_id = (int)$query['clientes_id'];
            $usuarios_id = (int)$query['usuarios_id'];
            $tipo_perfil = (int)$query['tipo_perfil'];

            // atualiza o usuário para ser definido como 'Administrador Regional'

            $usuario = $this->Usuarios->getUsuarioById($usuarios_id);

            $usuario->tipo_perfil = Configure::read('profileTypes')['AdminRegionalProfileType'];

            // salva o usuário

            $usuario = $this->Usuarios->addUpdateUsuario($usuario);

            $result = $this->ClientesHasUsuarios->saveClienteHasUsuario($clientes_id, $usuarios_id, $tipo_perfil);

            // atualiza todos os outros registros de administrador do usuário citado,
            // dentro daquela rede

            // pega os ids de clientes que pertencem à uma rede

            $rede_has_cliente = $this->RedesHasClientes->getRedesHasClientesByClientesId($clientes_id);

            $rede = $this->Redes->getRedeById($rede_has_cliente->rede->id);

            $clientes_ids = [];

            foreach ($rede->redes_has_clientes as $key => $value) {
                array_push($clientes_ids, $value->clientes_id);
            }

            $update_array = [];
            $select_array = [];

            array_push($update_array, ['tipo_perfil ' => Configure::read('profileTypes')['AdminRegionalProfileType']]);

            array_push($select_array, ['clientes_id IN ' => $clientes_ids]);
            array_push($select_array, ['usuarios_id' => $usuario->id]);
            array_push($select_array, ['tipo_perfil' => Configure::read('profileTypes')['AdminLocalProfileType']]);

            $this->ClientesHasUsuarios->updateClientesHasUsuarioRelationship($update_array, $select_array);

            if ($result) {
                $this->Flash->success(Configure::read('messageEnableSuccess'));

                return $this->redirect(
                    [
                        'controller' => 'clientes_has_usuarios',
                        'action' => 'editar_administracao', $usuarios_id
                    ]
                );
            }

            $this->Flash->error(Configure::read('messageEnableError'));

        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao realizar remoção de unidade de uma rede: {0} em: {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);

            return $this->redirect($query['return_url']);
        }

    }

    /**
     * Remove a administração a um determinado administrador
     *
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     */
    public function desatribuirAdministracao()
    {
        try {
            $query = $this->request->query();

            $id = $query['id'];
            $usuarios_id = $query['usuarios_id'];

            $clientes_id = $query['clientes_id'];

            // verifica se o usuário tem só um registro vinculado
            // ele não pode se desvincular completamente de uma rede

            // pega os ids de clientes que pertencem à uma rede

            $rede_has_cliente = $this->RedesHasClientes->getRedesHasClientesByClientesId($clientes_id);

            $rede = $this->Redes->getRedeById($rede_has_cliente->rede->id);

            $clientes_ids = [];

            foreach ($rede->redes_has_clientes as $key => $value) {
                array_push($clientes_ids, $value->clientes_id);
            }

            $clientes_has_usuarios_conditions = [];

            array_push($clientes_has_usuarios_conditions, ['ClientesHasUsuarios.usuarios_id' => $usuarios_id]);
            array_push($clientes_has_usuarios_conditions, ['ClientesHasUsuarios.clientes_id IN' => $clientes_ids]);

            array_push($clientes_has_usuarios_conditions, ['ClientesHasUsuarios.tipo_perfil >= ' => Configure::read('profileTypes')['AdminRegionalProfileType']]);
            array_push($clientes_has_usuarios_conditions, ['ClientesHasUsuarios.tipo_perfil <= ' => Configure::read('profileTypes')['AdminLocalProfileType']]);

            $clientes_has_usuarios_query = $this->ClientesHasUsuarios->findClienteHasUsuario($clientes_has_usuarios_conditions);

            if (sizeof($clientes_has_usuarios_query->toArray()) == 1) {

                $this->Flash->error("Não é possível remover a permissão do administrador. Ele deve ter ao menos um vínculo à uma Unidade da Rede!");

                return $this->redirect(
                    [
                        'controller' => 'clientes_has_usuarios',
                        'action' => 'editar_administracao', $usuarios_id
                    ]
                );
            }

            // se tem mais de um vínculo, permite remover

            $result = $this->ClientesHasUsuarios->removeClienteHasUsuario($id);

            if ($result) {

                // Usuário virou Admin Regional para Admin comum, atualiza os registros
                if (sizeof($clientes_has_usuarios_query->toArray()) == 2) {

                    // atualiza o usuário
                    $usuario = $this->Usuarios->getUsuarioById($usuarios_id);

                    $usuario->tipo_perfil = Configure::read('profileTypes')['AdminLocalProfileType'];

                    // salva o usuário

                    $usuario = $this->Usuarios->addUpdateUsuario($usuario);

                    $update_array = [];
                    $select_array = [];

                    array_push($update_array, ['tipo_perfil ' => Configure::read('profileTypes')['AdminLocalProfileType']]);

                    array_push($select_array, ['clientes_id IN ' => $clientes_ids]);
                    array_push($select_array, ['usuarios_id' => $usuario->id]);
                    array_push($select_array, ['tipo_perfil' => Configure::read('profileTypes')['AdminRegionalProfileType']]);

                    $this->ClientesHasUsuarios->updateClientesHasUsuarioRelationship($update_array, $select_array);
                }
                $this->Flash->success(Configure::read('messageDisableSuccess'));

                return $this->redirect(
                    [
                        'controller' => 'clientes_has_usuarios',
                        'action' => 'editar_administracao', $usuarios_id
                    ]
                );
            }

            $this->Flash->error(Configure::read('messageDisableError'));

        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao realizar remoção de unidade de uma rede: {0} em: {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);

            return $this->redirect($query['return_url']);
        }

    }
}
