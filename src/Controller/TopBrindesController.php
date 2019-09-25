<?php

namespace App\Controller;

use App\Controller\AppController;
use Exception;
use App\Model\Entity\TopBrindes;
use DateTime;
use App\Custom\RTI\ResponseUtil;
use Cake\Log\Log;

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

    #region REST Services

    /**
     * TopBrindesController::deleteTopBrindesAPI
     *
     * Remove um Top Brinde e reoordena posições dos registros restantes
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-08-14
     *
     * @param $id Id do registro Top Brinde
     *
     * @return json_encode $response success|fail Resposta
     */
    public function deleteTopBrindesAPI()
    {
        $sessaoUsuario = $this->getSessionUserVariables();
        $rede = $sessaoUsuario["rede"];
        $usuarioLogado = $sessaoUsuario["usuarioLogado"];
        $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"];

        if ($usuarioAdministrar) {
            $usuarioLogado = $usuarioAdministrar;
        }

        try {
            $id = 0;

            if ($this->request->is("delete")) {
                $data = $this->request->getData();

                $id = $data["id"] ?? null;
            }

            if (empty($id)) {
                throw new Exception(MESSAGE_ID_EMPTY);
            }

            $topBrinde = $this->TopBrindes->get($id);

            if ($topBrinde->redes_id != $rede->id) {
                throw new Exception(MESSAGE_RECORD_DOES_NOT_BELONG_NETWORK);
            }

            $success = $this->TopBrindes->delete($topBrinde);

            if (!$success) {
                throw new Exception(MESSAGE_CONTACT_SUPPORT);
            }

            /**
             * Reordena os brindes pós remoção.
             * Primeiro é necessário identificar qual o tipo do brinde,
             * para depois identificar quais brindes devem ser reajustados.
             */
            $redesId = $topBrinde->redes_id;
            $clientesId = null;
            $tipo = null;
            if ($topBrinde->tipo == TOP_BRINDES_TYPE_NATIONAL) {
                $tipo = TOP_BRINDES_TYPE_NATIONAL;
            } else {
                $clientesId = $topBrinde->clientes_id;
                $tipo = TOP_BRINDES_TYPE_LOCAL;
            }

            $topBrindes = $this->TopBrindes->getTopBrindes($redesId, $clientesId, null, null, $tipo);
            $posicao = 1;

            // Grava novas posições
            foreach ($topBrindes as $key => $topBrinde) {
                $topBrinde = new TopBrindes($topBrinde->toArray());
                $topBrinde->posicao = $posicao;
                $this->TopBrindes->saveUpdate($topBrinde);
                $posicao++;
            }

            return ResponseUtil::successAPI(MESSAGE_DELETE_SUCCESS);
        } catch (\Throwable $th) {
            $message = sprintf("[%s] %s", MESSAGE_DELETE_EXCEPTION, $th->getMessage());
            Log::write("error", $message);

            return ResponseUtil::errorAPI(MESSAGE_DELETE_EXCEPTION, [$th->getMessage()]);
        }
    }

    /**
     * TopBrindesController::getTopBrindesNacionalAPI
     *
     * Obtem Top Brindes Nacional
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-08-04
     *
     * @param $redes_id Id da Rede (se usuário for PROFILE_TYPE_USER)
     *
     * @return json_encode $response success|fail Resposta
     */
    public function getTopBrindesNacionalAPI()
    {
        $sessaoUsuario = $this->getSessionUserVariables();
        $rede = $sessaoUsuario["rede"] ?? null;
        $usuarioLogado = $sessaoUsuario["usuarioLogado"];

        try {

            if ($this->request->is("get")) {
                $redesId = 0;

                $redesId = $this->request->getQuery("redes_id");

                // Se for funcionário, está vinculado à uma rede.
                if ($usuarioLogado->tipo_perfil > PROFILE_TYPE_ADMIN_DEVELOPER && $usuarioLogado->tipo_perfil <= PROFILE_TYPE_WORKER) {
                    $redesId = $rede->id;
                }

                // Se não for funcionário E a Id de redes não for informada, retorna erro
                if (empty($redesId)) {
                    throw new Exception(MESSAGE_TOP_BRINDES_REDES_ID_NOT_EMPTY);
                }

                // Se a rede não tiver a configuração de app_personalizado, throw error
                $rede = $this->Redes->getRedeById($redesId);

                if (!$rede->app_personalizado) {
                    throw new Exception(MESSAGE_NETWORK_CUSTOM_APP_NOT_CONFIGURED);
                }

                $topBrindesNacional = $this->TopBrindes->getTopBrindes($redesId, null, null, null, TOP_BRINDES_TYPE_NATIONAL);

                // Verifica se o brinde está esgotado ou não
                foreach ($topBrindesNacional as $topBrinde) {
                    $estoqueAtual = $this->BrindesEstoque->getActualStockForBrindesEstoque($topBrinde->brinde->id);
                    $topBrinde->brinde->status_estoque = ($estoqueAtual["estoque_atual"] <= 0 && !$topBrinde->brinde->ilimitado) ? "Esgotado" : "Normal";
                }

                $data = ['top_brindes' => $topBrindesNacional];
                return ResponseUtil::successAPI(MSG_LOAD_DATA_WITH_SUCCESS, ["data" => $data]);
            }
        } catch (\Throwable $th) {
            $message = sprintf("[%s] %s", MESSAGE_LOAD_EXCEPTION, $th->getMessage());
            Log::write("error", $message);

            return ResponseUtil::errorAPI(MESSAGE_LOAD_EXCEPTION, [$th->getMessage()]);
        }
    }

    /**
     * TopBrindesController::getTopBrindesPostoAPI
     *
     * Obtem Top Brindes Nacional
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-08-04
     *
     * @return json_encode $response success|fail Resposta
     */
    public function getTopBrindesPostoAPI()
    {
        $sessaoUsuario = $this->getSessionUserVariables();
        $rede = $sessaoUsuario["rede"];
        $usuarioLogado = $sessaoUsuario["usuarioLogado"];

        try {
            $clientesId = 0;

            if ($this->request->is("get")) {
                $redesId = 0;

                $data = $this->request->getQueryParams();
                $redesId = $data["redes_id"] ?? 0;
                $clientesId = $data["clientes_id"] ?? 0;

                // Se for funcionário, está vinculado à uma rede.
                if ($usuarioLogado->tipo_perfil > PROFILE_TYPE_ADMIN_DEVELOPER && $usuarioLogado->tipo_perfil <= PROFILE_TYPE_WORKER) {
                    $redesId = $rede->id;
                }

                // Se não for funcionário E a Id de redes não for informada, retorna erro
                if (empty($redesId)) {
                    throw new Exception(MESSAGE_TOP_BRINDES_REDES_ID_NOT_EMPTY);
                }

                // Se a rede não tiver a configuração de app_personalizado, throw error
                $rede = $this->Redes->getRedeById($redesId);

                if (!$rede->app_personalizado) {
                    throw new Exception(MESSAGE_NETWORK_CUSTOM_APP_NOT_CONFIGURED);
                }

                if (empty($clientesId)) {
                    throw new Exception(MESSAGE_TOP_BRINDES_CLIENTES_ID_NOT_EMPTY);
                }

                $topBrindes = $this->TopBrindes->getTopBrindes($rede->id, $clientesId, null, null, TOP_BRINDES_TYPE_LOCAL);

                // Verifica se o brinde está esgotado ou não
                foreach ($topBrindes as $topBrinde) {
                    $estoqueAtual = $this->BrindesEstoque->getActualStockForBrindesEstoque($topBrinde->brinde->id);
                    $topBrinde->brinde->status_estoque = ($estoqueAtual["estoque_atual"] <= 0 && !$topBrinde->brinde->ilimitado) ? "Esgotado" : "Normal";
                }

                $data = ['top_brindes' => $topBrindes];
                return ResponseUtil::successAPI(MSG_LOAD_DATA_WITH_SUCCESS, ['data' => $data]);
            }
        } catch (\Throwable $th) {
            $message = sprintf("[%s] %s", MESSAGE_LOAD_EXCEPTION, $th->getMessage());
            Log::write("error", $message);

            return ResponseUtil::errorAPI(MESSAGE_LOAD_EXCEPTION, [$th->getMessage()]);
        }
    }

    /**
     * TopBrindesController::setTopBrindeNacionalAPI
     *
     * Define um brinde como Nacional
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-08-04
     *
     * @param $brindes_id Id de Brinde
     *
     * @return json_encode $response success|fail Resposta
     */
    public function setTopBrindeNacionalAPI()
    {
        $sessaoUsuario = $this->getSessionUserVariables();
        $rede = $sessaoUsuario["rede"];
        $usuarioLogado = $sessaoUsuario["usuarioLogado"];
        $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"];

        if ($usuarioAdministrar) {
            $usuarioLogado = $usuarioAdministrar;
        }

        try {
            $brindesId = 0;

            if ($this->request->is("post")) {
                $data = $this->request->getData();

                $brindesId = $data["brindes_id"] ?? null;
            }

            if ($usuarioLogado->tipo_perfil > PROFILE_TYPE_ADMIN_NETWORK) {
                throw new Exception(USER_NOT_ALLOWED_TO_EXECUTE_FUNCTION);
            }

            if (empty($brindesId)) {
                throw new Exception(MESSAGE_TOP_BRINDES_BRINDE_ID_NOT_EMPTY);
            }

            $brinde = $this->Brindes->get($brindesId);
            $topBrindesNacionalQte = $this->TopBrindes->countTopBrindes($rede->id, null, TOP_BRINDES_TYPE_NATIONAL);

            if ($topBrindesNacionalQte >= MESSAGE_TOP_BRINDES_MAX) {
                throw new Exception(MESSAGE_TOP_BRINDES_MAX_DEFINED);
            }

            // Verifica se o brinde em questão já está definido. Só atribui se não estiver
            $topBrindeCheck = $this->TopBrindes->getTopBrindes($rede->id, null, $brinde->id, null, TOP_BRINDES_TYPE_NATIONAL)->first();

            if (!empty($topBrindeCheck)) {
                throw new Exception("Este brinde já foi definido como Top Brindes Nacional!");
            }

            $topNacional = new TopBrindes();
            $topNacional->redes_id = $rede->id;
            $topNacional->clientes_id = $brinde->clientes_id;
            $topNacional->brindes_id = $brinde->id;
            $topNacional->posicao = $topBrindesNacionalQte + 1;
            $topNacional->tipo = TOP_BRINDES_TYPE_NATIONAL;
            $topNacional->data = new DateTime('now');
            $topNacional->audit_user_insert_id = $usuarioLogado->id;
            $topNacional = $this->TopBrindes->saveUpdate($topNacional);

            if (!$topNacional && count($topNacional->errors()) > 0) {
                throw new Exception(implode("\n", $topNacional->errors));
            }

            return ResponseUtil::successAPI(MESSAGE_SAVED_SUCCESS);
        } catch (\Throwable $th) {
            $message = sprintf("[%s] %s", MESSAGE_SAVED_EXCEPTION, $th->getMessage());
            Log::write("error", $message);

            return ResponseUtil::errorAPI(MESSAGE_SAVED_EXCEPTION, [$th->getMessage()]);
        }
    }

    /**
     * TopBrindesController::setTopBrindePostoAPI
     *
     * Define um brinde como Top Brinde Posto
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-08-12
     *
     * @param $brindes_id Id de Brinde
     *
     * @return json_encode $response success|fail Resposta
     */
    public function setTopBrindePostoAPI()
    {
        $sessaoUsuario = $this->getSessionUserVariables();
        $rede = $sessaoUsuario["rede"];
        $usuarioLogado = $sessaoUsuario["usuarioLogado"];
        $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"];
        $errors = [];

        if ($usuarioAdministrar) {
            $usuarioLogado = $usuarioAdministrar;
        }

        try {
            $brindesId = 0;

            if ($this->request->is("post")) {
                $data = $this->request->getData();
                $brindesId = $data["brindes_id"] ?? null;

                if (empty($brindesId)) {
                    $errors[] = MESSAGE_TOP_BRINDES_BRINDE_ID_NOT_EMPTY;
                }

                if (count($errors) > 0) {
                    throw new Exception(MESSAGE_SAVED_EXCEPTION);
                }

                $brinde = $this->Brindes->get($brindesId);
                $topBrindesPostoQte = $this->TopBrindes->countTopBrindes($rede->id, null, TOP_BRINDES_TYPE_LOCAL);

                if ($topBrindesPostoQte >= MESSAGE_TOP_BRINDES_MAX) {
                    $errors[] = MESSAGE_TOP_BRINDES_MAX_DEFINED;
                }

                if (count($errors) > 0) {
                    throw new Exception(MESSAGE_SAVED_EXCEPTION);
                }

                // Verifica se o brinde em questão já está definido. Só atribui se não estiver
                $topBrindeCheck = $this->TopBrindes->getTopBrindes($rede->id, $brinde->clientes_id, $brinde->id, null, TOP_BRINDES_TYPE_LOCAL)->first();

                if (!empty($topBrindeCheck)) {
                    throw new Exception("Este brinde já foi definido como Top Brindes Nacional!");
                }

                $topBrinde = new TopBrindes();
                $topBrinde->redes_id = $rede->id;
                $topBrinde->clientes_id = $brinde->clientes_id;
                $topBrinde->brindes_id = $brinde->id;
                $topBrinde->posicao = $topBrindesPostoQte + 1;
                $topBrinde->tipo = TOP_BRINDES_TYPE_LOCAL;
                $topBrinde->data = new DateTime('now');
                $topBrinde->audit_user_insert_id = $usuarioLogado->id;
                $topBrinde = $this->TopBrindes->saveUpdate($topBrinde);

                if (!$topBrinde && count($topBrinde->errors()) > 0) {
                    throw new Exception(implode("\n", $topBrinde->errors));
                }

                return ResponseUtil::successAPI(MESSAGE_SAVED_SUCCESS);
            }
        } catch (\Throwable $th) {
            $message = sprintf("[%s] %s", MESSAGE_SAVED_EXCEPTION, $th->getMessage());
            Log::write("error", $message);

            return ResponseUtil::errorAPI(MESSAGE_SAVED_EXCEPTION, $errors);
        }
    }

    /**
     * TopBrindesController::setPosicoesTopBrindesAPI
     *
     * Define as posições dos top brindes nacionais
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-08-07
     *
     * @return json_encode $response success|fail Resposta
     */
    public function setPosicoesTopBrindesAPI()
    {
        try {
            $topBrindes = [];

            if ($this->request->is("put")) {
                $data = $this->request->getData();
                $topBrindes = $data["top_brindes"] ?? [];
            }

            if (count($topBrindes) == 0) {
                throw new Exception(MESSAGE_TOP_BRINDES_ITEMS_REQUIRED);
            }

            $topBrindesReajustarList = [];

            foreach ($topBrindes as $brindeReajuste) {
                $topBrinde = $this->TopBrindes->get($brindeReajuste["id"]);

                if ($topBrinde->posicao != $brindeReajuste["posicao"]) {
                    $topBrinde->posicao = $brindeReajuste["posicao"];
                    $topBrinde = new TopBrindes($topBrinde->toArray());
                    $topBrindesReajustarList[] = $topBrinde;
                }
            }

            foreach ($topBrindesReajustarList as $topBrindesSave) {
                $this->TopBrindes->saveUpdate($topBrindesSave);
            }

            return ResponseUtil::successAPI(MESSAGE_SAVED_SUCCESS);
        } catch (\Throwable $th) {
            $message = sprintf("[%s] %s", MESSAGE_SAVED_EXCEPTION, $th->getMessage());
            Log::write("error", $message);

            return ResponseUtil::errorAPI(MESSAGE_SAVED_EXCEPTION, [$th->getMessage()]);
        }
    }

    #endregion
}
