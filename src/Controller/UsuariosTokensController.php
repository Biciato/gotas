<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * UsuariosTokens Controller
 *
 * @property \App\Model\Table\UsuariosTokensTable $UsuariosTokens
 *
 * @method \App\Model\Entity\UsuariosToken[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsuariosTokensController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Usuarios']
        ];
        $usuariosTokens = $this->paginate($this->UsuariosTokens);

        $this->set(compact('usuariosTokens'));
    }

    /**
     * View method
     *
     * @param string|null $id Usuarios Token id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $usuariosToken = $this->UsuariosTokens->get($id, [
            'contain' => ['Usuarios']
        ]);

        $this->set('usuariosToken', $usuariosToken);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $usuariosToken = $this->UsuariosTokens->newEntity();
        if ($this->request->is('post')) {
            $usuariosToken = $this->UsuariosTokens->patchEntity($usuariosToken, $this->request->getData());
            if ($this->UsuariosTokens->save($usuariosToken)) {
                $this->Flash->success(__('The usuarios token has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The usuarios token could not be saved. Please, try again.'));
        }
        $usuarios = $this->UsuariosTokens->Usuarios->find('list', ['limit' => 200]);
        $this->set(compact('usuariosToken', 'usuarios'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Usuarios Token id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $usuariosToken = $this->UsuariosTokens->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $usuariosToken = $this->UsuariosTokens->patchEntity($usuariosToken, $this->request->getData());
            if ($this->UsuariosTokens->save($usuariosToken)) {
                $this->Flash->success(__('The usuarios token has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The usuarios token could not be saved. Please, try again.'));
        }
        $usuarios = $this->UsuariosTokens->Usuarios->find('list', ['limit' => 200]);
        $this->set(compact('usuariosToken', 'usuarios'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Usuarios Token id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $usuariosToken = $this->UsuariosTokens->get($id);
        if ($this->UsuariosTokens->delete($usuariosToken)) {
            $this->Flash->success(__('The usuarios token has been deleted.'));
        } else {
            $this->Flash->error(__('The usuarios token could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
