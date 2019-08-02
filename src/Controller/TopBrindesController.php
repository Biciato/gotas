<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * TopBrindes Controller
 *
 * @property \App\Model\Table\TopBrindesTable $TopBrindes
 *
 * @method \App\Model\Entity\TopBrinde[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TopBrindesController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Redes', 'Clientes', 'Brindes', 'Usuarios']
        ];
        $topBrindes = $this->paginate($this->TopBrindes);

        $this->set(compact('topBrindes'));
    }

    /**
     * View method
     *
     * @param string|null $id Top Brinde id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $topBrinde = $this->TopBrindes->get($id, [
            'contain' => ['Redes', 'Clientes', 'Brindes', 'Usuarios']
        ]);

        $this->set('topBrinde', $topBrinde);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $topBrinde = $this->TopBrindes->newEntity();
        if ($this->request->is('post')) {
            $topBrinde = $this->TopBrindes->patchEntity($topBrinde, $this->request->getData());
            if ($this->TopBrindes->save($topBrinde)) {
                $this->Flash->success(__('The top brinde has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The top brinde could not be saved. Please, try again.'));
        }
        $redes = $this->TopBrindes->Redes->find('list', ['limit' => 200]);
        $clientes = $this->TopBrindes->Clientes->find('list', ['limit' => 200]);
        $brindes = $this->TopBrindes->Brindes->find('list', ['limit' => 200]);
        $usuarios = $this->TopBrindes->Usuarios->find('list', ['limit' => 200]);
        $this->set(compact('topBrinde', 'redes', 'clientes', 'brindes', 'usuarios'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Top Brinde id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $topBrinde = $this->TopBrindes->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $topBrinde = $this->TopBrindes->patchEntity($topBrinde, $this->request->getData());
            if ($this->TopBrindes->save($topBrinde)) {
                $this->Flash->success(__('The top brinde has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The top brinde could not be saved. Please, try again.'));
        }
        $redes = $this->TopBrindes->Redes->find('list', ['limit' => 200]);
        $clientes = $this->TopBrindes->Clientes->find('list', ['limit' => 200]);
        $brindes = $this->TopBrindes->Brindes->find('list', ['limit' => 200]);
        $usuarios = $this->TopBrindes->Usuarios->find('list', ['limit' => 200]);
        $this->set(compact('topBrinde', 'redes', 'clientes', 'brindes', 'usuarios'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Top Brinde id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $topBrinde = $this->TopBrindes->get($id);
        if ($this->TopBrindes->delete($topBrinde)) {
            $this->Flash->success(__('The top brinde has been deleted.'));
        } else {
            $this->Flash->error(__('The top brinde could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function nacional()
    {
        # code...
    }
    
    public function posto()
    {
        # code...
    }
}
