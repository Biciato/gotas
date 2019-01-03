<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * ClientesHasQuadroHorario Controller
 *
 * @property \App\Model\Table\ClientesHasQuadroHorarioTable $ClientesHasQuadroHorario
 *
 * @method \App\Model\Entity\ClientesHasQuadroHorario[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ClientesHasQuadroHorarioController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Clientes']
        ];
        $ClientesHasQuadroHorario = $this->paginate($this->ClientesHasQuadroHorario);

        $this->set(compact('ClientesHasQuadroHorario'));
    }

    /**
     * View method
     *
     * @param string|null $id Clientes Quadro Horario id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $ClientesHasQuadroHorario = $this->ClientesHasQuadroHorario->get($id, [
            'contain' => ['Clientes']
        ]);

        $this->set('ClientesHasQuadroHorario', $ClientesHasQuadroHorario);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $ClientesHasQuadroHorario = $this->ClientesHasQuadroHorario->newEntity();
        if ($this->request->is('post')) {
            $ClientesHasQuadroHorario = $this->ClientesHasQuadroHorario->patchEntity($ClientesHasQuadroHorario, $this->request->getData());
            if ($this->ClientesHasQuadroHorario->save($ClientesHasQuadroHorario)) {
                $this->Flash->success(__('The clientes quadro horario has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The clientes quadro horario could not be saved. Please, try again.'));
        }
        $clientes = $this->ClientesHasQuadroHorario->Clientes->find('list', ['limit' => 200]);
        $this->set(compact('ClientesHasQuadroHorario', 'clientes'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Clientes Quadro Horario id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $ClientesHasQuadroHorario = $this->ClientesHasQuadroHorario->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $ClientesHasQuadroHorario = $this->ClientesHasQuadroHorario->patchEntity($ClientesHasQuadroHorario, $this->request->getData());
            if ($this->ClientesHasQuadroHorario->save($ClientesHasQuadroHorario)) {
                $this->Flash->success(__('The clientes quadro horario has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The clientes quadro horario could not be saved. Please, try again.'));
        }
        $clientes = $this->ClientesHasQuadroHorario->Clientes->find('list', ['limit' => 200]);
        $this->set(compact('ClientesHasQuadroHorario', 'clientes'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Clientes Quadro Horario id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $ClientesHasQuadroHorario = $this->ClientesHasQuadroHorario->get($id);
        if ($this->ClientesHasQuadroHorario->delete($ClientesHasQuadroHorario)) {
            $this->Flash->success(__('The clientes quadro horario has been deleted.'));
        } else {
            $this->Flash->error(__('The clientes quadro horario could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
