<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Log\Log;
use Cake\Routing\Router;
use Cake\Mailer\Email;
use Cake\I18n\Number;
use Cake\View\Helper\UrlHelper;
use \DateTime;
use App\Custom\RTI\Security;

/**
 * PontuacoesPendentes Controller
 *
 * @property \App\Model\Table\PontuacoesPendentesTable $PontuacoesPendentes
 *
 * @method \App\Model\Entity\PontuacoesPendente[] paginate($object = null, array $settings = [])
 */
class PontuacoesPendentesController extends AppController
{
    protected $usuarioLogado = null;

    /**
     * ------------------------------------------------------------
     * Métodos Comuns
     * ------------------------------------------------------------
     */

    /**
     * Before render callback.
     *
     * @param \App\Controller\Event\Event $event The beforeRender event.
     * @return \Cake\Network\Response|null|void
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
    }

    /**
     * Initialize function
     */
    public function initialize()
    {
        parent::initialize();
    }

    /**
     * ------------------------------------------------------------
     * CRUD Methods
     * ------------------------------------------------------------
     */

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Clientes', 'Usuarios', 'Funcionarios']
        ];
        $pontuacoesPendentes = $this->paginate($this->PontuacoesPendentes);

        $this->set(compact('pontuacoesPendentes'));
        $this->set('_serialize', ['pontuacoesPendentes']);
    }

    /**
     * View method
     *
     * @param string|null $id Pontuacoes Pendente id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $pontuacoesPendente = $this->PontuacoesPendentes->get($id, [
            'contain' => ['Clientes', 'Usuarios', 'Funcionarios']
        ]);

        $this->set('pontuacoesPendente', $pontuacoesPendente);
        $this->set('_serialize', ['pontuacoesPendente']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $pontuacoesPendente = $this->PontuacoesPendentes->newEntity();
        if ($this->request->is('post')) {
            $pontuacoesPendente = $this->PontuacoesPendentes->patchEntity($pontuacoesPendente, $this->request->getData());
            if ($this->PontuacoesPendentes->save($pontuacoesPendente)) {
                $this->Flash->success(__('The pontuacoes pendente has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The pontuacoes pendente could not be saved. Please, try again.'));
        }
        $clientes = $this->PontuacoesPendentes->Clientes->find('list', ['limit' => 200]);
        $usuarios = $this->PontuacoesPendentes->Usuarios->find('list', ['limit' => 200]);
        $funcionarios = $this->PontuacoesPendentes->Funcionarios->find('list', ['limit' => 200]);
        $this->set(compact('pontuacoesPendente', 'clientes', 'usuarios', 'funcionarios'));
        $this->set('_serialize', ['pontuacoesPendente']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Pontuacoes Pendente id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $pontuacoesPendente = $this->PontuacoesPendentes->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $pontuacoesPendente = $this->PontuacoesPendentes->patchEntity($pontuacoesPendente, $this->request->getData());
            if ($this->PontuacoesPendentes->save($pontuacoesPendente)) {
                $this->Flash->success(__('The pontuacoes pendente has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The pontuacoes pendente could not be saved. Please, try again.'));
        }
        $clientes = $this->PontuacoesPendentes->Clientes->find('list', ['limit' => 200]);
        $usuarios = $this->PontuacoesPendentes->Usuarios->find('list', ['limit' => 200]);
        $funcionarios = $this->PontuacoesPendentes->Funcionarios->find('list', ['limit' => 200]);
        $this->set(compact('pontuacoesPendente', 'clientes', 'usuarios', 'funcionarios'));
        $this->set('_serialize', ['pontuacoesPendente']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Pontuacoes Pendente id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $pontuacoesPendente = $this->PontuacoesPendentes->get($id);
        if ($this->PontuacoesPendentes->delete($pontuacoesPendente)) {
            $this->Flash->success(__('The pontuacoes pendente has been deleted.'));
        } else {
            $this->Flash->error(__('The pontuacoes pendente could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * ------------------------------------------------------------
     * AJAX Methods
     * ------------------------------------------------------------
    */
}
