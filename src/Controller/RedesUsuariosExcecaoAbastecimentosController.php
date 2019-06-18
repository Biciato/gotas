<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Custom\RTI\DebugUtil;

/**
 * RedesUsuariosExcecaoAbastecimentos Controller
 *
 * @property \App\Model\Table\RedesUsuariosExcecaoAbastecimentosTable $RedesUsuariosExcecaoAbastecimentos
 *
 * @method \App\Model\Entity\RedesUsuariosExcecaoAbastecimento[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class RedesUsuariosExcecaoAbastecimentosController extends AppController
{

    /**
     * Index method
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-06-18
     * 
     * @return \Cake\Http\Response|void
     */
    public function index()
    {

        $arraySet = array(
            "nome",
            "cpf",
            "email",
            "usuariosList"
        );

        $sessaoUsuario = $this->getSessionUserVariables();
        $usuarioAdministrador = $sessaoUsuario["usuarioAdministrador"];
        $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"];
        $usuarioLogado = $sessaoUsuario["usuarioLogado"];
        $rede = $sessaoUsuario["rede"];
        $cliente = $sessaoUsuario["cliente"];

        if (!empty($usuarioAdministrar)) {
            $usuarioLogado = $usuarioAdministrar;
            $this->usuarioLogado = $usuarioAdministrar;
        }

        $nome = "";
        $email = "";
        $cpf = "";

        if ($this->request->is("post")) {
            $data = $this->request->getData();

            $nome = !empty($data["nome"]) ? $data["nome"] : null;
            $email = !empty($data["email"]) ? $data["email"] : null;
            $cpf = !empty($data["cpf"]) ? $data["cpf"] : null;
        }

        $usuariosList = $this->RedesUsuariosExcecaoAbastecimentos->findUsuariosExcecaoAbastecimentos($rede->id, $nome, $email, $cpf);

        
        $this->paginate = [
            'contain' => ['Redes', 'Usuarios']
        ];
        $usuariosList = $this->paginate($this->RedesUsuariosExcecaoAbastecimentos);

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);
    }

    /**
     * View method
     *
     * @param string|null $id Redes Usuarios Excecao Abastecimento id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $redesUsuariosExcecaoAbastecimento = $this->RedesUsuariosExcecaoAbastecimentos->get($id, [
            'contain' => ['Redes', 'Usuarios']
        ]);

        $this->set('redesUsuariosExcecaoAbastecimento', $redesUsuariosExcecaoAbastecimento);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $redesUsuariosExcecaoAbastecimento = $this->RedesUsuariosExcecaoAbastecimentos->newEntity();
        if ($this->request->is('post')) {
            $redesUsuariosExcecaoAbastecimento = $this->RedesUsuariosExcecaoAbastecimentos->patchEntity($redesUsuariosExcecaoAbastecimento, $this->request->getData());
            if ($this->RedesUsuariosExcecaoAbastecimentos->save($redesUsuariosExcecaoAbastecimento)) {
                $this->Flash->success(__('The redes usuarios excecao abastecimento has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The redes usuarios excecao abastecimento could not be saved. Please, try again.'));
        }
        $redes = $this->RedesUsuariosExcecaoAbastecimentos->Redes->find('list', ['limit' => 200]);
        $usuarios = $this->RedesUsuariosExcecaoAbastecimentos->Usuarios->find('list', ['limit' => 200]);
        $this->set(compact('redesUsuariosExcecaoAbastecimento', 'redes', 'usuarios'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Redes Usuarios Excecao Abastecimento id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $redesUsuariosExcecaoAbastecimento = $this->RedesUsuariosExcecaoAbastecimentos->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $redesUsuariosExcecaoAbastecimento = $this->RedesUsuariosExcecaoAbastecimentos->patchEntity($redesUsuariosExcecaoAbastecimento, $this->request->getData());
            if ($this->RedesUsuariosExcecaoAbastecimentos->save($redesUsuariosExcecaoAbastecimento)) {
                $this->Flash->success(__('The redes usuarios excecao abastecimento has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The redes usuarios excecao abastecimento could not be saved. Please, try again.'));
        }
        $redes = $this->RedesUsuariosExcecaoAbastecimentos->Redes->find('list', ['limit' => 200]);
        $usuarios = $this->RedesUsuariosExcecaoAbastecimentos->Usuarios->find('list', ['limit' => 200]);
        $this->set(compact('redesUsuariosExcecaoAbastecimento', 'redes', 'usuarios'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Redes Usuarios Excecao Abastecimento id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $redesUsuariosExcecaoAbastecimento = $this->RedesUsuariosExcecaoAbastecimentos->get($id);
        if ($this->RedesUsuariosExcecaoAbastecimentos->delete($redesUsuariosExcecaoAbastecimento)) {
            $this->Flash->success(__('The redes usuarios excecao abastecimento has been deleted.'));
        } else {
            $this->Flash->error(__('The redes usuarios excecao abastecimento could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
