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

    public function getTopBrindesNacionalAPI()
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

            if ($this->request->is("get")) {
                $data = $this->request->getData();

                $brindesId = $data["brindes_id"] ?? null;
            }

            $topBrindesNacional = $this->TopBrindes->getTopBrindes($rede->id, null, null, null, TOP_BRINDES_TYPE_NATIONAL);

            return ResponseUtil::successAPI(MESSAGE_SAVED_SUCCESS, ['top_brindes' => $topBrindesNacional]);
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
     * @param $data['brindes_id'] Id de Brinde 
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

            if (empty($brindesId)) {
                throw new Exception(MESSAGE_TOP_BRINDES_BRINDE_ID_NOT_EMPTY);
            }

            $brinde = $this->Brindes->get($brindesId);
            $topBrindesNacionalQte = $this->TopBrindes->countTopBrindes($rede->id, null, TOP_BRINDES_TYPE_NATIONAL);

            if ($topBrindesNacionalQte >= MESSAGE_TOP_BRINDES_MAX) {
                throw new Exception(MESSAGE_TOP_BRINDES_MAX_DEFINED);
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

    #endregion
}
