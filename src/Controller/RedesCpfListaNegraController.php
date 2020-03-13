<?php

namespace App\Controller;

use App\Controller\AppController;
use App\Custom\RTI\NumberUtil;
use App\Custom\RTI\ResponseUtil;
use App\Model\Entity\RedesCpfListaNegra;
use Cake\Http\Client\Request;
use Cake\Log\Log;
use Exception;

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
     * Index Web method
     *
     * @return \Cake\Http\Response|void
     */
    public function redesCpfListaNegra()
    {
        $redesId = !empty($this->rede) ? $this->rede->id : 0;

        // Se não for adm devel e não tiver uma rede definida, não tem acesso
        if ($this->usuarioLogado->tipo_perfil !== PROFILE_TYPE_ADMIN_DEVELOPER && empty($redesId)) {
            if ($this->request->getHeader("IsMobile") === 1) {
                return ResponseUtil::errorAPI(USER_NOT_ALLOWED_TO_EXECUTE_FUNCTION);
            }

            $this->Flash->error(USER_NOT_ALLOWED_TO_EXECUTE_FUNCTION);
            return $this->redirect("/");
        }
    }

    /**
     * List method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $redesId = !empty($this->rede) ? $this->rede->id : 0;
        $errors = [];
        $errorCodes = [];

        // Se não for adm devel e não tiver uma rede definida, não tem acesso
        if ($this->usuarioLogado->tipo_perfil !== PROFILE_TYPE_ADMIN_DEVELOPER && empty($redesId)) {
            return ResponseUtil::errorAPI(USER_NOT_ALLOWED_TO_EXECUTE_FUNCTION);
        }

        try {
            if ($this->request->is(Request::METHOD_GET)) {
                $data = $this->request->getQueryParams();
                $redesId = !empty($data["redes_id"]) ? $data["redes_id"] : $redesId;
                $cpf = !empty($data["cpf"]) ? preg_replace('/[^0-9]/', "", $data["cpf"]) : null;

                if (empty($redesId)) {
                    throw new Exception(MSG_REDES_ID_EMPTY, MSG_REDES_ID_EMPTY_CODE);
                }

                $redesCpfListaNegra = $this->RedesCpfListaNegra->getCpfsByNetwork($redesId, $cpf);

                return ResponseUtil::successAPI(MSG_LOAD_DATA_WITH_SUCCESS, ['data' => $redesCpfListaNegra]);
            }
        } catch (\Throwable $th) {
            $errorMessage = $th->getMessage();
            $errorCode = $th->getCode();

            if (count($errors) == 0) {
                $errors[] = $errorMessage;
                $errorCodes[] = $errorCode;
            }

            for ($i = 0; $i < count($errors); $i++) {
                Log::write("error", sprintf("[%s] %s - %s", MSG_LOAD_DATA_WITH_ERROR, $errorCodes[$i], $errors[$i]));
            }

            return ResponseUtil::errorAPI(MSG_LOAD_DATA_WITH_ERROR, $errors, [], $errorCodes);
        }
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
        if ($this->request->is(Request::METHOD_POST)) {
            $postData = $this->request->getData();

            $cpfIsValid = NumberUtil::validarCPF($postData["cpf"]);

            if (!$cpfIsValid["status"]) {
                throw new Exception($cpfIsValid["message"]);
            }

            $redesCpfListaNegra = new RedesCpfListaNegra();
            $redesCpfListaNegra->cpf = $postData["cpf"];

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
