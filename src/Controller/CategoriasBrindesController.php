<?php

namespace App\Controller;

use App\Controller\AppController;
use App\Custom\RTI\ResponseUtil;
use App\Custom\RTI\DebugUtil;
use Exception;
use App\Model\Entity\CategoriasBrinde;
use DateTime;

/**
 * CategoriasBrindes Controller
 *
 * @property \App\Model\Table\CategoriasBrindesTable $CategoriasBrindes
 *
 * @method \App\Model\Entity\CategoriasBrinde[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CategoriasBrindesController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    { }

    /**
     * View method
     *
     * @param string|null $id Categorias Brinde id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $categoriasBrinde = $this->CategoriasBrindes->get($id, [
            'contain' => ['Redes', 'Usuarios']
        ]);

        $this->set('categoriasBrinde', $categoriasBrinde);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $categoriasBrinde = $this->CategoriasBrindes->newEntity();
        if ($this->request->is('post')) {
            $categoriasBrinde = $this->CategoriasBrindes->patchEntity($categoriasBrinde, $this->request->getData());
            if ($this->CategoriasBrindes->save($categoriasBrinde)) {
                $this->Flash->success(__('The categorias brinde has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The categorias brinde could not be saved. Please, try again.'));
        }
        $redes = $this->CategoriasBrindes->Redes->find('list', ['limit' => 200]);
        $usuarios = $this->CategoriasBrindes->Usuarios->find('list', ['limit' => 200]);
        $this->set(compact('categoriasBrinde', 'redes', 'usuarios'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Categorias Brinde id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $categoriasBrinde = $this->CategoriasBrindes->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $categoriasBrinde = $this->CategoriasBrindes->patchEntity($categoriasBrinde, $this->request->getData());
            if ($this->CategoriasBrindes->save($categoriasBrinde)) {
                $this->Flash->success(__('The categorias brinde has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The categorias brinde could not be saved. Please, try again.'));
        }
        $redes = $this->CategoriasBrindes->Redes->find('list', ['limit' => 200]);
        $usuarios = $this->CategoriasBrindes->Usuarios->find('list', ['limit' => 200]);
        $this->set(compact('categoriasBrinde', 'redes', 'usuarios'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Categorias Brinde id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $categoriasBrinde = $this->CategoriasBrindes->get($id);
        if ($this->CategoriasBrindes->delete($categoriasBrinde)) {
            $this->Flash->success(__('The categorias brinde has been deleted.'));
        } else {
            $this->Flash->error(__('The categorias brinde could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    #region REST Services

    public function getCategoriasBrindesAPI()
    {
        $sessaoUsuario = $this->getSessionUserVariables();

        $usuarioLogado = $sessaoUsuario["usuarioLogado"];
        $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"] ?? null;
        $rede = $sessaoUsuario["rede"];

        if (!empty($usuarioAdministrar)) {
            $usuarioLogado = $usuarioAdministrar;
        }

        $nome = null;
        $habilitado = null;

        if ($this->request->is("get")) {
            $data = $this->request->getQueryParams();

            $nome = $data["nome"] ?? null;
            $habilitado = $data["habilitado"] ?? null;
        }

        $dataRetorno = array();
        $categoriasBrindes = $this->CategoriasBrindes->getCategoriasBrindes($rede->id, $nome, $habilitado);
        $categoriasBrindes = $categoriasBrindes->toArray();

        $dataRetorno["categorias_brindes"] = $categoriasBrindes;

        ResponseUtil::successAPI("Sucesso", $dataRetorno);
    }

    public function saveCategoriasBrindesAPI()
    {
        $sessaoUsuario = $this->getSessionUserVariables();

        $usuarioLogado = $sessaoUsuario["usuarioLogado"];
        $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"] ?? null;
        $rede = $sessaoUsuario["rede"];

        if (!empty($usuarioAdministrar)) {
            $usuarioLogado = $usuarioAdministrar;
        }

        try {
            $nome = null;

            if ($this->request->is("post")) {
                $data = $this->request->getData();

                $nome = $data["nome"] ?? null;
            }

            if (empty($nome)) {
                throw new Exception(MESSAGE_CATEGORIAS_BRINDES_NOME_EMPTY);
            }

            $categoriaBrinde = new CategoriasBrinde();
            $categoriaBrinde->redes_id = $rede->id;
            $categoriaBrinde->nome = $nome;
            $categoriaBrinde->habilitado = true;
            $categoriaBrinde->data = new DateTime('now');
            $categoriaBrinde->audit_user_insert_id = $usuarioLogado->id;

            $categoriaBrinde = $this->CategoriasBrindes->saveUpdate($categoriaBrinde);

            if ($categoriaBrinde) {
                ResponseUtil::successAPI(MESSAGE_SAVED_SUCCESS, ['categoria_brinde' => $categoriaBrinde]);
            }

        } catch (\Throwable $th) {
            $message = sprintf("[%s] %s", MESSAGE_SAVED_EXCEPTION, $th->getMessage());
            Log::write("error", $message);

            return ResponseUtil::errorAPI(MESSAGE_SAVED_EXCEPTION, [$th->getMessage()]);
        }
    }

    #endregion
}
