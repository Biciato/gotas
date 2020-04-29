<?php

namespace App\Controller;

use App\Controller\AppController;
use App\Custom\RTI\NumberUtil;
use App\Custom\RTI\ResponseUtil;
use App\Model\Entity\RedesCpfListaNegra;
use Cake\Http\Client\Request;
use Cake\Log\Log;
use DateTime;
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
     * List method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $redesId = !empty($this->rede) ? $this->rede->id : 0;
        $errors = [];
        $errorCodes = [];

        // Se a solicitação não for ajax e não ter o header, é pq é view via web, e verifica se o usuário tem acesso ou não
        if (!$this->request->is('ajax') && empty($this->request->getHeader("IsMobile"))) {
            // Se não for adm devel e não tiver uma rede definida, não tem acesso
            if ($this->usuarioLogado->tipo_perfil !== PROFILE_TYPE_ADMIN_DEVELOPER && empty($redesId)) {
                if ($this->request->getHeader("IsMobile") === 1) {
                    return ResponseUtil::errorAPI(USER_NOT_ALLOWED_TO_EXECUTE_FUNCTION);
                }

                $this->Flash->error(USER_NOT_ALLOWED_TO_EXECUTE_FUNCTION);
                return $this->redirect("/");
            }

            return;
        }

        // Se não for adm devel e não tiver uma rede definida, não tem acesso
        if (($this->usuarioLogado->tipo_perfil !== PROFILE_TYPE_ADMIN_DEVELOPER && empty($redesId)) || $this->usuarioLogado->tipo_perfil > PROFILE_TYPE_ADMIN_NETWORK) {
            if ($this->request->getHeader("IsMobile") == true) {
                return ResponseUtil::errorAPI(USER_NOT_ALLOWED_TO_EXECUTE_FUNCTION);
            }

            $this->Flash->error(USER_NOT_ALLOWED_TO_EXECUTE_FUNCTION);
            return $this->redirect("/");
        }

        try {
            if ($this->request->is(Request::METHOD_GET)) {
                $data = $this->request->getQueryParams();
                $redesId = !empty($data["redes_id"]) ? $data["redes_id"] : $redesId;

                if (empty($redesId)) {
                    throw new Exception(MSG_REDES_ID_EMPTY, MSG_REDES_ID_EMPTY_CODE);
                }

                $redesCpfListaNegra = $this->RedesCpfListaNegra->getCpfsByNetwork($redesId);

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
        throw new Exception("Not yet implemented!");
    }

    /**
     * Action de adicionar registro
     *
     * @param $post["redes_id"]
     * @param $post["cpf"] CPF do usuário
     * @return json_encode $response success|fail Resposta
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.1.8
     * @date 2020-03-13
     */
    public function add()
    {
        $errors = [];
        $errorCodes = [];

        try {
            if ($this->request->is(Request::METHOD_POST)) {
                $postData = $this->request->getData();

                $redesId = $postData["redes_id"];
                // Considera a rede que está definida na sessão do usuário
                $redesId = !empty($this->rede) ? $this->rede->id : $redesId;
                $cpfIsValid = NumberUtil::validarCPF($postData["cpf"]);
                $cpf = NumberUtil::limparFormatacaoNumeros($postData["cpf"]);

                if (!$cpfIsValid["status"]) {
                    $errors[] = $cpfIsValid["message"];
                    $errorCodes[] = 0;
                }

                if (empty($redesId)) {
                    $errors[] = MSG_REDES_ID_EMPTY;
                    $errorCodes[] = MSG_REDES_ID_EMPTY_CODE;
                }

                // Verifica se registro já existe

                if (!empty($cpf)) {
                    $recordCheck = $this->RedesCpfListaNegra->getCpfInNetwork($redesId, $cpf);

                    if (!empty($recordCheck)) {
                        $errors[] = MSG_RECORD_ALREADY_EXISTS;
                        $errorCodes[] = MSG_RECORD_ALREADY_EXISTS_CODE;
                    }
                }

                // Se rede não informada ou cpf não válido, retorna exception
                if (count($errors) > 0) {
                    throw new Exception(MSG_SAVED_EXCEPTION, MSG_SAVED_EXCEPTION_CODE);
                }

                $redesCpfListaNegra = new RedesCpfListaNegra();
                $redesCpfListaNegra->audit_user_insert_id = !empty($this->usuarioAdministrar) ? $this->usuarioAdministrar->id : $this->usuarioLogado->id;
                $redesCpfListaNegra->cpf = NumberUtil::limparFormatacaoNumeros($postData["cpf"]);
                $redesCpfListaNegra->data = new DateTime('now');
                $redesCpfListaNegra->redes_id = $redesId;
                $record = $this->RedesCpfListaNegra->saveUpdate($redesCpfListaNegra);

                if ($record) {
                    return ResponseUtil::successAPI(MESSAGE_SAVED_SUCCESS);
                }

                throw new Exception(MSG_SAVED_EXCEPTION, MSG_SAVED_EXCEPTION_CODE);
            }
        } catch (\Throwable $th) {
            $errorMessage = $th->getMessage();
            $errorCode = $th->getCode();

            if (count($errors) == 0) {
                $errors[] = $errorMessage;
                $errorCodes[] = $errorCode;
            }

            for ($i = 0; $i < count($errors); $i++) {
                Log::write("error", sprintf("[%s] %s - %s", $errorMessage, $errorCodes[$i], $errors[$i]));
            }

            return ResponseUtil::errorAPI($errorMessage, $errors, [], $errorCodes);
        }
    }

    /**
     * Delete method
     *
     * @param string|null $id Redes Cpf Lista Negra id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id)
    {
        $this->request->allowMethod(['post', 'delete']);

        try {
            if ($this->request->is("delete")) {

                Log::write("info", sprintf("Info de %s: %s - %s.", Request::METHOD_DELETE, __CLASS__, __METHOD__));
                Log::write("info", "Id sendo removido: " . $id);
            }

            $redesCpfListaNegra = $this->RedesCpfListaNegra->get($id);
            $userValidation = !empty($this->usuarioAdministrar) ? $this->usuarioAdministrar : $this->usuarioLogado;

            if ($userValidation->tipo_perfil !== PROFILE_TYPE_ADMIN_DEVELOPER && $this->rede->id !== $redesCpfListaNegra->redes_id) {
                throw new Exception(MESSAGE_RECORD_DOES_NOT_BELONG_NETWORK);
            }

            $success = $this->RedesCpfListaNegra->delete($redesCpfListaNegra);

            if (!$success) {
                throw new Exception(MESSAGE_CONTACT_SUPPORT);
            }

            return ResponseUtil::successAPI(MESSAGE_DELETE_SUCCESS);
        } catch (\Throwable $th) {
            $message = sprintf("[%s] %s", MSG_DELETE_EXCEPTION, $th->getMessage());
            Log::write("error", $message);

            return ResponseUtil::errorAPI(MSG_DELETE_EXCEPTION, [$th->getMessage()]);
        }
    }
}
