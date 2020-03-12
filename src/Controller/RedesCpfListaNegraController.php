<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use stdClass;

/**
 * RedesCpfListaNegra Controller
 *
 * @property \App\Model\Table\RedesCpfListaNegraTable $RedesCpfListaNegra
 *
 * @method \App\Model\Entity\RedesCpfListaNegra[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class RedesCpfListaNegraController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $rede = $this->rede;

        $this->paginate = [
            'contain' => ['Redes', 'Usuarios']
        ];
        $redesCpfListaNegra = $this->paginate($this->RedesCpfListaNegra);

        $this->set(compact('redesCpfListaNegra'));
    }

    /**
     * View method
     *
     * @param string|null $id Redes Cpf Lista Negra id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $redesCpfListaNegra = $this->RedesCpfListaNegra->get($id, [
            'contain' => ['Redes', 'Usuarios']
        ]);

        $this->set('redesCpfListaNegra', $redesCpfListaNegra);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $redesCpfListaNegra = $this->RedesCpfListaNegra->newEntity();
        if ($this->request->is('post')) {
            $redesCpfListaNegra = $this->RedesCpfListaNegra->patchEntity($redesCpfListaNegra, $this->request->getData());
            if ($this->RedesCpfListaNegra->save($redesCpfListaNegra)) {
                $this->Flash->success(__('The redes cpf lista negra has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The redes cpf lista negra could not be saved. Please, try again.'));
        }
        $redes = $this->RedesCpfListaNegra->Redes->find('list', ['limit' => 200]);
        $usuarios = $this->RedesCpfListaNegra->Usuarios->find('list', ['limit' => 200]);
        $this->set(compact('redesCpfListaNegra', 'redes', 'usuarios'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Redes Cpf Lista Negra id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $redesCpfListaNegra = $this->RedesCpfListaNegra->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $redesCpfListaNegra = $this->RedesCpfListaNegra->patchEntity($redesCpfListaNegra, $this->request->getData());
            if ($this->RedesCpfListaNegra->save($redesCpfListaNegra)) {
                $this->Flash->success(__('The redes cpf lista negra has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The redes cpf lista negra could not be saved. Please, try again.'));
        }
        $redes = $this->RedesCpfListaNegra->Redes->find('list', ['limit' => 200]);
        $usuarios = $this->RedesCpfListaNegra->Usuarios->find('list', ['limit' => 200]);
        $this->set(compact('redesCpfListaNegra', 'redes', 'usuarios'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Redes Cpf Lista Negra id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $redesCpfListaNegra = $this->RedesCpfListaNegra->get($id);
        if ($this->RedesCpfListaNegra->delete($redesCpfListaNegra)) {
            $this->Flash->success(__('The redes cpf lista negra has been deleted.'));
        } else {
            $this->Flash->error(__('The redes cpf lista negra could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    // public function beforeFilter(Event $event)
    // {
    //     // parent::beforeFilter($event);

    //     $sessaoUsuario = $this->getSessionUserVariables();
    //     // $usuarioLogado;
    // }
}
