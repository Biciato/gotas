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

/**
 * RedesHasClientesAdministradores Controller
 *
 * @property \App\Model\Table\RedesHasClientesAdministradoresTable $RedesHasClientesAdministradores
 *
 * @method \App\Model\Entity\RedesHasClientesAdministradore[] paginate($object = null, array $settings = [])
 */
class RedesHasClientesAdministradoresController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['RedesHasClientes', 'Usuarios']
        ];
        $redesHasClientesAdministradores = $this->paginate($this->RedesHasClientesAdministradores);

        $this->set(compact('redesHasClientesAdministradores'));
        $this->set('_serialize', ['redesHasClientesAdministradores']);
    }

    /**
     * View method
     *
     * @param string|null $id Redes Has Clientes Administradore id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $redesHasClientesAdministradore = $this->RedesHasClientesAdministradores->get($id, [
            'contain' => ['RedesHasClientes', 'Usuarios']
        ]);

        $this->set('redesHasClientesAdministradore', $redesHasClientesAdministradore);
        $this->set('_serialize', ['redesHasClientesAdministradore']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $redesHasClientesAdministradore = $this->RedesHasClientesAdministradores->newEntity();
        if ($this->request->is('post')) {
            $redesHasClientesAdministradore = $this->RedesHasClientesAdministradores->patchEntity($redesHasClientesAdministradore, $this->request->getData());
            if ($this->RedesHasClientesAdministradores->save($redesHasClientesAdministradore)) {
                $this->Flash->success(__('The redes has clientes administradore has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The redes has clientes administradore could not be saved. Please, try again.'));
        }
        $redesHasClientes = $this->RedesHasClientesAdministradores->RedesHasClientes->find('list', ['limit' => 200]);
        $usuarios = $this->RedesHasClientesAdministradores->Usuarios->find('list', ['limit' => 200]);
        $this->set(compact('redesHasClientesAdministradore', 'redesHasClientes', 'usuarios'));
        $this->set('_serialize', ['redesHasClientesAdministradore']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Redes Has Clientes Administradore id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $redesHasClientesAdministradore = $this->RedesHasClientesAdministradores->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $redesHasClientesAdministradore = $this->RedesHasClientesAdministradores->patchEntity($redesHasClientesAdministradore, $this->request->getData());
            if ($this->RedesHasClientesAdministradores->save($redesHasClientesAdministradore)) {
                $this->Flash->success(__('The redes has clientes administradore has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The redes has clientes administradore could not be saved. Please, try again.'));
        }
        $redesHasClientes = $this->RedesHasClientesAdministradores->RedesHasClientes->find('list', ['limit' => 200]);
        $usuarios = $this->RedesHasClientesAdministradores->Usuarios->find('list', ['limit' => 200]);
        $this->set(compact('redesHasClientesAdministradore', 'redesHasClientes', 'usuarios'));
        $this->set('_serialize', ['redesHasClientesAdministradore']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Redes Has Clientes Administradore id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $redesHasClientesAdministradore = $this->RedesHasClientesAdministradores->get($id);
        if ($this->RedesHasClientesAdministradores->delete($redesHasClientesAdministradore)) {
            $this->Flash->success(__('The redes has clientes administradore has been deleted.'));
        } else {
            $this->Flash->error(__('The redes has clientes administradore could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * ------------------------------------------------------------
     * Relatórios - Administrativo RTI
     * ------------------------------------------------------------
     */


}
