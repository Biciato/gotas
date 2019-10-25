<?php

namespace App\Controller;

use App\Controller\AppController;
use App\Custom\RTI\ResponseUtil;
use App\Custom\RTI\DebugUtil;
use Exception;
use App\Model\Entity\CategoriasBrinde;
use DateTime;
use Cake\Log\Log;

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

    /**
     * CategoriasBrindesController.php::getCategoriaBrindeAPI
     *
     * Obtem uma CategoriaBrinde
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-07-28
     *
     * @param int $id Id do Registro
     *
     * @return json_encode(App\Model\Entity\CategoriasBrinde) JSON
     */
    public function getCategoriaBrindeAPI()
    {
        $sessaoUsuario = $this->getSessionUserVariables();

        $usuarioLogado = $sessaoUsuario["usuarioLogado"];
        $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"] ?? null;
        $rede = $sessaoUsuario["rede"];

        if (!empty($usuarioAdministrar)) {
            $usuarioLogado = $usuarioAdministrar;
        }

        $id = 0;

        try {
            if ($this->request->is("get")) {
                $data = $this->request->getQueryParams();

                $id = $data["id"] ?? null;
            }

            if (empty($id)) {
                throw new Exception(MSG_CATEGORIAS_BRINDES_ID_EMPTY);
            }

            $categoriaBrinde = $this->CategoriasBrindes->get($id);

            if (empty($categoriaBrinde)) {
                throw new Exception(MESSAGE_RECORD_NOT_FOUND);
            }

            if ($categoriaBrinde->redes_id != $rede->id) {
                throw new Exception(MESSAGE_RECORD_DOES_NOT_BELONG_NETWORK);
            }

            $retorno = ['categoria_brinde' => $categoriaBrinde];
            return ResponseUtil::successAPI(MSG_LOAD_DATA_WITH_SUCCESS, ['data' => $retorno]);
        } catch (\Throwable $th) {
            $message = sprintf("[%s] %s", MSG_LOAD_EXCEPTION, $th->getMessage());
            Log::write("error", $message);

            return ResponseUtil::errorAPI(MSG_LOAD_EXCEPTION, [$th->getMessage()]);
        }
    }

    /**
     * CategoriasBrindesController.php::getCategoriasBrindesAPI
     *
     * Obtem CategoriasBrindes
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-07-28
     *
     * @param int redes_id Id da Rede (se cliente APP)
     * @param string nome Filtro de nome
     * @param bool habilitado Se registro habilitado ou não
     *
     * @return json_encode(App\Model\Entity\CategoriasBrinde[]) JSON
     */
    public function getCategoriasBrindesAPI()
    {
        $sessaoUsuario = $this->getSessionUserVariables();

        $usuarioLogado = $sessaoUsuario["usuarioLogado"];
        $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"] ?? null;
        $rede = $sessaoUsuario["rede"];
        $redesId = !empty($rede) ? $rede->id : 0;

        if (!empty($usuarioAdministrar)) {
            $usuarioLogado = $usuarioAdministrar;
        }

        try {
            $nome = null;
            $habilitado = null;

            if ($this->request->is("get")) {
                $data = $this->request->getQueryParams();

                $nome = $data["nome"] ?? null;
                $habilitado = $data["habilitado"] ?? null;

                if (empty($rede)) {
                    $redesId = $data["redes_id"] ?? 0;

                    if (empty($redesId)) {
                        throw new Exception(MSG_CATEGORIAS_BRINDES_REDES_ID_EMPTY, MSG_CATEGORIAS_BRINDES_REDES_ID_EMPTY_CODE);
                    }
                }
            }

            $dataRetorno = array();
            $categoriasBrindes = $this->CategoriasBrindes->getCategoriasBrindes($redesId, $nome, $habilitado);
            $categoriasBrindes = $categoriasBrindes->toArray();
            $dataRetorno["categorias_brindes"] = $categoriasBrindes;

            return ResponseUtil::successAPI(MSG_LOAD_DATA_WITH_SUCCESS, ['data' => $dataRetorno]);
        } catch (\Throwable $th) {
            $message = sprintf("[%s] %s", MSG_LOAD_EXCEPTION, $th->getMessage());
            Log::write("error", $message);

            return ResponseUtil::errorAPI(MSG_LOAD_EXCEPTION, [$th->getMessage()], [], [$th->getCode()]);
        }
    }

    /**
     * CategoriasBrindesController.php::setCategoriasBrindesAPI
     *
     * Insere uma CategoriasBrindes
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-07-28
     *
     * @param string nome Nome da Categoria
     *
     * @return json_encode(App\Model\Entity\CategoriasBrinde) JSON
     */
    public function setCategoriasBrindesAPI()
    {
        $sessaoUsuario = $this->getSessionUserVariables();

        $usuarioLogado = $sessaoUsuario["usuarioLogado"];
        $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"] ?? null;
        $rede = $sessaoUsuario["rede"];

        if (!empty($usuarioAdministrar)) {
            $usuarioLogado = $usuarioAdministrar;
        }

        $nome = null;

        try {
            if ($this->request->is("post")) {
                $data = $this->request->getData();
                $nome = $data["nome"] ?? null;
            }

            if (empty($nome)) {
                throw new Exception(MSG_CATEGORIAS_BRINDES_NOME_EMPTY);
            }

            $categoriaBrinde = new CategoriasBrinde();
            $categoriaBrinde->redes_id = $rede->id;
            $categoriaBrinde->nome = trim($nome);
            $categoriaBrinde->habilitado = true;
            $categoriaBrinde->data = new DateTime('now');
            $categoriaBrinde->audit_user_insert_id = $usuarioLogado->id;
            $categoriaBrinde = $this->CategoriasBrindes->saveUpdate($categoriaBrinde);

            if (!$categoriaBrinde) {
                throw new Exception(implode("\n", $categoriaBrinde->errors()));
            }

            $retorno = ['categoria_brinde' => $categoriaBrinde];

            return ResponseUtil::successAPI(MESSAGE_SAVED_SUCCESS, ['data' => $retorno]);
        } catch (\Throwable $th) {
            $message = sprintf("[%s] %s", MESSAGE_SAVED_EXCEPTION, $th->getMessage());
            Log::write("error", $message);

            return ResponseUtil::errorAPI(MESSAGE_SAVED_EXCEPTION, [$th->getMessage()]);
        }
    }

    /**
     * CategoriasBrindesController.php::updateCategoriasBrindesAPI
     *
     * Atualiza um registro CategoriasBrindes
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-07-28
     *
     * @param int $id Id do Registro
     * @param string $nome Nome
     * @param bool $habilitado Registro habilitado / desabilitado
     *
     * @return json_encode(App\Model\Entity\CategoriasBrinde) JSON
     */
    public function updateCategoriasBrindesAPI()
    {
        $sessaoUsuario = $this->getSessionUserVariables();

        $usuarioLogado = $sessaoUsuario["usuarioLogado"];
        $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"] ?? null;
        $rede = $sessaoUsuario["rede"];

        if (!empty($usuarioAdministrar)) {
            $usuarioLogado = $usuarioAdministrar;
        }

        try {
            $id = 0;
            $nome = null;
            $habilitado = false;

            if ($this->request->is("put")) {
                $data = $this->request->getData();
                $id = $data["id"] ?? 0;
                $nome = $data["nome"] ?? null;
                $habilitado = $data["habilitado"] ?? true;

                if (empty($id)) {
                    throw new Exception(MSG_CATEGORIAS_BRINDES_ID_EMPTY, MSG_CATEGORIAS_BRINDES_ID_EMPTY_CODE);
                }

                if (empty($nome)) {
                    throw new Exception(MSG_CATEGORIAS_BRINDES_NOME_EMPTY, MSG_CATEGORIAS_BRINDES_NOME_EMPTY_CODE);
                }

                $categoriaBrinde = $this->CategoriasBrindes->get($id);

                if (empty($categoriaBrinde)) {
                    throw new Exception(MESSAGE_RECORD_NOT_FOUND);
                }

                if ($categoriaBrinde->redes_id != $rede->id) {
                    throw new Exception(MESSAGE_RECORD_DOES_NOT_BELONG_NETWORK);
                }

                if (empty($nome)) {
                    throw new Exception(MSG_CATEGORIAS_BRINDES_NOME_EMPTY);
                }

                $categoriaBrinde->nome = trim($nome);
                $categoriaBrinde->habilitado = $habilitado;
                $categoriaBrinde = $this->CategoriasBrindes->saveUpdate($categoriaBrinde);

                if (!$categoriaBrinde) {
                    throw new Exception(MESSAGE_SAVED_EXCEPTION, MESSAGE_SAVED_EXCEPTION_CODE);
                }

                $retorno = ['categoria_brinde' => $categoriaBrinde];

                return ResponseUtil::successAPI(MESSAGE_SAVED_SUCCESS, ['data' => $retorno]);
            }
        } catch (\Throwable $th) {
            $message = sprintf("[%s] %s", MESSAGE_SAVED_EXCEPTION, $th->getMessage());
            Log::write("error", $message);
            $errorCodes = $th->getCode();

            return ResponseUtil::errorAPI(MESSAGE_SAVED_EXCEPTION, [$th->getMessage()], [], [$errorCodes]);
        }
    }

    /**
     * CategoriasBrindesController.php::updateStatusCategoriasBrindesAPI
     *
     * Atualiza o estado de habilitado de um CategoriasBrindes
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-07-28
     *
     * @param int $id Id do Registro
     * @param bool $habilitado Registro habilitado / desabilitado
     *
     * @return json_encode(App\Model\Entity\CategoriasBrinde) JSON
     */
    public function updateStatusCategoriasBrindesAPI()
    {
        $sessaoUsuario = $this->getSessionUserVariables();

        $usuarioLogado = $sessaoUsuario["usuarioLogado"];
        $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"] ?? null;
        $rede = $sessaoUsuario["rede"];

        if (!empty($usuarioAdministrar)) {
            $usuarioLogado = $usuarioAdministrar;
        }

        try {
            $id = 0;
            $habilitado = null;

            if ($this->request->is("PUT")) {
                $data = $this->request->getData();

                $id = $data["id"] ?? null;
                $habilitado = $data["habilitado"] ?? null;
            }

            if (empty($id)) {
                throw new Exception(MSG_CATEGORIAS_BRINDES_ID_EMPTY);
            }

            if (!isset($habilitado)) {
                throw new Exception(MSG_CATEGORIAS_BRINDES_HABILITADO_EMPTY);
            }

            $categoriaBrinde = $this->CategoriasBrindes->get($id);

            if (empty($categoriaBrinde)) {
                throw new Exception(MESSAGE_RECORD_NOT_FOUND);
            }

            if ($categoriaBrinde->redes_id != $rede->id) {
                throw new Exception(MESSAGE_RECORD_DOES_NOT_BELONG_NETWORK);
            }

            $success = $this->CategoriasBrindes->updateStatusCategoriasBrindes($categoriaBrinde->id, $habilitado);

            if (!$success) {
                throw new Exception(MESSAGE_DELETE_ERROR);
            }

            return ResponseUtil::successAPI(MESSAGE_SAVED_SUCCESS);
        } catch (\Throwable $th) {
            $message = sprintf("[%s] %s", MESSAGE_SAVED_ERROR, $th->getMessage());
            Log::write("error", $message);

            return ResponseUtil::errorAPI(MESSAGE_SAVED_ERROR, [$th->getMessage()]);
        }
    }

    /**
     * CategoriasBrindesController.php::deleteCategoriasBrindesAPI
     *
     * Remove um CategoriasBrindes
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-07-28
     *
     * @param int $id Id do Registro
     *
     * @return json_encode JSON
     */
    public function deleteCategoriasBrindesAPI()
    {
        $sessaoUsuario = $this->getSessionUserVariables();

        $usuarioLogado = $sessaoUsuario["usuarioLogado"];
        $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"] ?? null;
        $rede = $sessaoUsuario["rede"];

        if (!empty($usuarioAdministrar)) {
            $usuarioLogado = $usuarioAdministrar;
        }

        try {
            $id = 0;

            if ($this->request->is("DELETE")) {
                $data = $this->request->getData();
                $id = $data["id"] ?? null;
            }

            if (empty($id)) {
                throw new Exception(MSG_CATEGORIAS_BRINDES_ID_EMPTY);
            }

            $categoriaBrinde = $this->CategoriasBrindes->get($id);

            if ($categoriaBrinde->redes_id != $rede->id) {
                throw new Exception(MESSAGE_RECORD_DOES_NOT_BELONG_NETWORK);
            }

            $success = $this->CategoriasBrindes->delete($categoriaBrinde);

            if (!$success) {
                throw new Exception(MESSAGE_DELETE_ERROR);
            }

            return ResponseUtil::successAPI(MESSAGE_DELETE_SUCCESS);
        } catch (\Throwable $th) {
            $message = sprintf("[%s] %s", MSG_DELETE_EXCEPTION, $th->getMessage());
            Log::write("error", $message);

            return ResponseUtil::errorAPI(MSG_DELETE_EXCEPTION, [$th->getMessage()]);
        }
    }

    #endregion
}
