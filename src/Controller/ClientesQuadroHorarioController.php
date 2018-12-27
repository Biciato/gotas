<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * ClientesQuadroHorario Controller
 *
 * @property \App\Model\Table\ClientesQuadroHorarioTable $ClientesQuadroHorario
 *
 * @method \App\Model\Entity\ClientesQuadroHorario[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ClientesQuadroHorarioController extends AppController
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
        $clientesQuadroHorario = $this->paginate($this->ClientesQuadroHorario);

        $this->set(compact('clientesQuadroHorario'));
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
        $clientesQuadroHorario = $this->ClientesQuadroHorario->get($id, [
            'contain' => ['Clientes']
        ]);

        $this->set('clientesQuadroHorario', $clientesQuadroHorario);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $clientesQuadroHorario = $this->ClientesQuadroHorario->newEntity();
        if ($this->request->is('post')) {
            $clientesQuadroHorario = $this->ClientesQuadroHorario->patchEntity($clientesQuadroHorario, $this->request->getData());
            if ($this->ClientesQuadroHorario->save($clientesQuadroHorario)) {
                $this->Flash->success(__('The clientes quadro horario has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The clientes quadro horario could not be saved. Please, try again.'));
        }
        $clientes = $this->ClientesQuadroHorario->Clientes->find('list', ['limit' => 200]);
        $this->set(compact('clientesQuadroHorario', 'clientes'));
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
        $clientesQuadroHorario = $this->ClientesQuadroHorario->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $clientesQuadroHorario = $this->ClientesQuadroHorario->patchEntity($clientesQuadroHorario, $this->request->getData());
            if ($this->ClientesQuadroHorario->save($clientesQuadroHorario)) {
                $this->Flash->success(__('The clientes quadro horario has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The clientes quadro horario could not be saved. Please, try again.'));
        }
        $clientes = $this->ClientesQuadroHorario->Clientes->find('list', ['limit' => 200]);
        $this->set(compact('clientesQuadroHorario', 'clientes'));
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
        $clientesQuadroHorario = $this->ClientesQuadroHorario->get($id);
        if ($this->ClientesQuadroHorario->delete($clientesQuadroHorario)) {
            $this->Flash->success(__('The clientes quadro horario has been deleted.'));
        } else {
            $this->Flash->error(__('The clientes quadro horario could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
