<?php
namespace App\Controller;

use \DateTime;
use App\Controller\AppController;
use App\Custom\RTI\Security;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Log\Log;
use Cake\Mailer\Email;
use Cake\Routing\Router;
use Cake\View\Helper\UrlHelper;
use App\Custom\RTI\DebugUtil;
/**
 * TransportadorasHasUsuarios Controller
 *
 * @property \App\Model\Table\TransportadorasHasUsuariosTable $TransportadorasHasUsuarios
 *
 * @method \App\Model\Entity\TransportadorasHasUsuario[] paginate($object = null, array $settings = [])
 */
class TransportadorasHasUsuariosController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Transportadoras', 'Usuarios']
        ];
        $transportadorasHasUsuarios = $this->paginate($this->TransportadorasHasUsuarios);

        $this->set(compact('transportadorasHasUsuarios'));
        $this->set('_serialize', ['transportadorasHasUsuarios']);
    }

    /**
     * View method
     *
     * @param string|null $id Transportadoras Has Usuario id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $transportadorasHasUsuario = $this->TransportadorasHasUsuarios->get($id, [
            'contain' => ['Transportadoras', 'Usuarios']
        ]);

        $this->set('transportadorasHasUsuario', $transportadorasHasUsuario);
        $this->set('_serialize', ['transportadorasHasUsuario']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $transportadorasHasUsuario = $this->TransportadorasHasUsuarios->newEntity();
        if ($this->request->is('post')) {
            $transportadorasHasUsuario = $this->TransportadorasHasUsuarios->patchEntity($transportadorasHasUsuario, $this->request->getData());
            if ($this->TransportadorasHasUsuarios->save($transportadorasHasUsuario)) {
                $this->Flash->success(__('The transportadoras has usuario has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The transportadoras has usuario could not be saved. Please, try again.'));
        }
        $transportadoras = $this->TransportadorasHasUsuarios->Transportadoras->find('list', ['limit' => 200]);
        $usuarios = $this->TransportadorasHasUsuarios->Usuarios->find('list', ['limit' => 200]);
        $this->set(compact('transportadorasHasUsuario', 'transportadoras', 'usuarios'));
        $this->set('_serialize', ['transportadorasHasUsuario']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Transportadoras Has Usuario id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $transportadorasHasUsuario = $this->TransportadorasHasUsuarios->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $transportadorasHasUsuario = $this->TransportadorasHasUsuarios->patchEntity($transportadorasHasUsuario, $this->request->getData());
            if ($this->TransportadorasHasUsuarios->save($transportadorasHasUsuario)) {
                $this->Flash->success(__('The transportadoras has usuario has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The transportadoras has usuario could not be saved. Please, try again.'));
        }
        $transportadoras = $this->TransportadorasHasUsuarios->Transportadoras->find('list', ['limit' => 200]);
        $usuarios = $this->TransportadorasHasUsuarios->Usuarios->find('list', ['limit' => 200]);
        $this->set(compact('transportadorasHasUsuario', 'transportadoras', 'usuarios'));
        $this->set('_serialize', ['transportadorasHasUsuario']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Transportadoras Has Usuario id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $transportadorasHasUsuario = $this->TransportadorasHasUsuarios->get($id);
        if ($this->TransportadorasHasUsuarios->delete($transportadorasHasUsuario)) {
            $this->Flash->success(__('The transportadoras has usuario has been deleted.'));
        } else {
            $this->Flash->error(__('The transportadoras has usuario could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * --------------------------------------------------------------
     * Métodos para Dashboard de Funcionário
     * --------------------------------------------------------------
     */

    /**
     * Remove o vínculo de um veículo com um cliente final
     * Usado pela dashboard de funcionário
     *
     * @param int $usuario_has_veiculo_id
     */
    public function deleteTransportadoraUsuarioFinal(int $transportadora_has_usuario_id)
    {
        try {
            $this->request->allowMethod(['post', 'delete']);

            $transportadora_has_usuario = $this->TransportadorasHasUsuarios->getTransportadorasHasUsuariosById($transportadora_has_usuario_id);

            if ($this->TransportadorasHasUsuarios->delete($transportadora_has_usuario)) {
                $this->Flash->success(__(Configure::read('messageDeleteSuccess')));
            } else {
                $this->Flash->error(__(Configure::read('messageDeleteError')));
            }

            return $this->redirect(
                [
                    'controller' => 'Transportadoras',
                    'action' => 'transportadoras_usuario_final', $transportadora_has_usuario->usuarios_id
                ]
            );
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao remover veículos para usuário: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * ------------------------------------------------------------
     * Relatórios (Dashboard de Admin RTI)
     * ------------------------------------------------------------
     */

    /**
     * Exibe Action de Relatório Detalhado das Transportadoras
     * de Usuários de cada Rede
     *
     * @param int $transportadoraHasUsuarioId
     * @return void
     */
    public function relatorioTransportadorasUsuariosDetalhado(int $transportadoraHasUsuarioId)
    {
        try {

            $transportadoraHasUsuarios = $this->TransportadorasHasUsuarios->findTransportadorasHasUsuariosByTransportadorasId($transportadoraHasUsuarioId)->toArray();

            $transportadora = $this->Transportadoras->get($transportadoraHasUsuarios[0]->transportadoras_id);

            $arraySet = [
                'transportadoraHasUsuarios',
                'transportadora'
            ];

            $this->set(compact($arraySet));

        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao exibir relatório de transportadoras detalhado: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * ------------------------------------------------------------
     * Serviços REST
     * ------------------------------------------------------------
     */

    /**
     * TransportadorasHasUsuarios::getTransportadorasUsuarioAPI
     *
     * Obtem todas as transportadoras que um usuário oferece serviço
     *
     * @param @data["cnpj"]          CNPJ
     * @param @data["nome_fantasia"] Nome Fantasia
     * @param @data["razao_social"]  Razão Social
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 2018/05/02
     *
     * @return json Objeto JSON
     */
    public function getTransportadorasUsuarioAPI()
    {
        $mensagem = array();

        $status = true;
        $message = __(Configure::read("messageLoadDataWithSuccess"));

        try {
            if ($this->request->is('post')) {
                $data = $this->request->getData();

                $whereConditions = array();

                $cnpj = isset($data["cnpj"]) && strlen($data["cnpj"]) > 0 ? $data["cnpj"] : null;
                $nomeFantasia = isset($data["nome_fantasia"]) && strlen($data["nome_fantasia"]) > 0 ? $data["nome_fantasia"] : null;
                $razaoSocial = isset($data["razao_social"]) && strlen($data["razao_social"]) > 0 ? $data["razao_social"] : null;

                // Filtra pelo usuário logado e pelas transportadoras cadastradas
                $usuariosId = $this->Auth->user()["id"];

                $orderConditions = array();
                $paginationConditions = array();

                if (isset($data["order_by"])) {
                    $orderConditions = $data["order_by"];
                }

                if (isset($data["pagination"])) {
                    $paginationConditions = $data["pagination"];

                    if ($paginationConditions["page"] < 1) {
                        $paginationConditions["page"] = 1;
                    }
                }

                $retorno = $this->TransportadorasHasUsuarios->getTransportadorasHasUsuarios(
                    $usuariosId,
                    $cnpj,
                    $nomeFantasia,
                    $razaoSocial,
                    $orderConditions,
                    $paginationConditions
                );

                $mensagem = $retorno["mensagem"];
                $transportadoras = $retorno["transportadoras"];

                $arraySet = array("mensagem", "transportadoras");

                $this->set(compact($arraySet));
                $this->set("_serialize", $arraySet);

                return;
            }

        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $messageString = __("Não foi possível obter dados de transportadoras do usuário!");

            $mensagem = ['status' => false, 'message' => $messageString, 'errors' => $trace];

            $messageStringDebug =
                $stringError = __("{0} - {1}. [Função: {3} / Arquivo: {4} / Linha: {5}]  ", $messageString, $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write("error", $messageStringDebug);
            Log::write("error", $trace);
        }

        $mensagem = ["status" => $status, "message" => $message];
        $arraySet = ["mensagem", "transportadoras"];

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);
    }

    /**
     * TransportadorasHasUsuarios::setTransportadorasUsuarioAPI
     *
     * Vincula nova transportadora ao cadastro do Usuário final.
     * Cria novo registro se a transportadora não existir.
     *
     * @param $data["id"]                   Id da Transportadora para vínculo
     * @param $data["nome_fantasia"]        Nome Fantasia
     * @param $data["razao_social"]         Razao Social
     * @param $data["cnpj"]                 CNPJ
     * @param $data["cep"]                  CEP
     * @param $data["endereco"]             Endereco
     * @param $data["endereco_numero"]      Endereco Numero
     * @param $data["endereco_complemento"] Endereco Complemento
     * @param $data["bairro"]               Bairro
     * @param $data["municipio"]            Municipio
     * @param $data["estado"]               Estado
     * @param $data["pais"]                 Pais
     * @param $data["tel_fixo"]             Tel Fixo
     * @param $data["tel_celular"]          Tel Celular
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 2018/05/02
     *
     * @return json Objeto JSON
     */
    public function setTransportadorasUsuarioAPI()
    {
        $mensagem = array();

        $status = true;
        $message = __(Configure::read("messageSavedSuccess"));
        $errors = array();

        try {

            if ($this->request->is('post')) {
                $data = $this->request->getData();
                $usuario = $this->Auth->user();

                $transportadorasId = isset($data["id"]) && strlen($data["id"]) > 0 ? $data["id"] : null;
                $cnpj = isset($data["cnpj"]) && strlen($data["cnpj"]) > 0 ? $data["cnpj"] : null;

                $transportadora = null;

                $canContinue = true;

                /**
                 *  Verifica se o Id ou cnpj foi fornecido. se não tiver sido fornecido,
                 * se não foi fornecido, informa que sem o CNPJ não é possível cadastrar nova transportadora
                 */
                if (empty($transportadorasId) && empty($cnpj)) {
                    $mensagem = array(
                        "status" => 0,
                        "message" => Configure::read("messageOperationFailureDuringProcessing"),
                        "errors" => array(
                            "Para realizar o cadastro de uma nova transportadora, o campo de CNPJ deve ser informado!"
                        )
                    );

                    $canContinue = false;
                }

                if ($canContinue) {

                    // Localiza pelo Id se fornecido
                    if (isset($transportadorasId) && strlen($transportadorasId) > 0) {
                        $transportadora = $this->Transportadoras->getTransportadoraById($transportadorasId);
                    }
                    // Localiza registro pelo CNPJ
                    if (!$transportadora && (isset($cnpj) && strlen($cnpj) > 0)) {
                        $transportadora = $this->Transportadoras->findTransportadoraByCNPJ($cnpj);
                    }

                    // Registro não encontrado, cria o mesmo
                    if (is_null($transportadora)) {

                        $transportadora = $this->Transportadoras->createUpdateTransportadora($data);

                        $errors = $transportadora->errors();

                        if (!$transportadora) {
                            $mensagem = array(
                                "status" => false,
                                "message" => Configure::read("messageOperationFailureDuringProcessing"),
                                "errors" => $errors,
                            );

                            $canContinue = false;
                        }
                    }

                    if ($canContinue) {

                        // Encontrou registro de transportadora
                        // Primeiro verifica se já há registro de transportadora para o usuário em questão

                        $whereTransportadorasUsuarios = array();
                        $whereTransportadorasUsuarios[] = ["transportadoras_id" => $transportadora["id"]];
                        $whereTransportadorasUsuarios[] = ["usuarios_id" => $usuario["id"]];

                        $transportadoraHasUsuarioCheck = $this->TransportadorasHasUsuarios->findTransportadorasHasUsuarios($whereTransportadorasUsuarios)->first();

                        // Não existe registro, cria o mesmo
                        if (is_null($transportadoraHasUsuarioCheck)) {

                            $transportadoraSave = $this->TransportadorasHasUsuarios->addTransportadoraHasUsuario($transportadora["id"], $usuario["id"]);

                            $errors = $transportadoraSave->errors();

                            if ($transportadoraSave) {
                                $mensagem = array(
                                    "status" => 1,
                                    "message" => __(Configure::read("messageSavedSuccess")),
                                    "errors" => array()
                                );
                            } else {
                                // Apaga o registro pois não foi feito vinculo
                                $this->TransportadorasHasUsuarios->deleteTransportadoraHasUsuario($transportadora["id"], $usuario["id"]);

                                $mensagem = array(
                                    "status" => 0,
                                    "message" => __(Configure::read("messageSavedError")),
                                    "errors" => $errors
                                );
                            }
                        }
                        // Existe o registro, retorna mensagem de erro
                        else {
                            $mensagem = array(
                                "status" => 0,
                                "message" => __(Configure::read("messageSavedError")),
                                "errors" => array("Transportadora já se encontra em seu perfil, não é necessário realizar novo cadastro!")
                            );
                        }
                    }
                }

                // Chegou até aqui, retorna resultado
                $arraySet = array("mensagem");

                $this->set(compact($arraySet));
                $this->set("_serialize", $arraySet);

                return;
            }

        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $messageString = __("Não foi possível gravar transportadora para o usuário!");

            $mensagem = ['status' => false, 'message' => $messageString, 'errors' => $trace];

            $messageStringDebug = __("{0} - {1}. [Função: {3} / Arquivo: {4} / Linha: {5}]  ", $messageString, $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write("error", $messageStringDebug);
            Log::write("error", $trace);
        }

        $mensagem = ["status" => $status, "message" => $message, "errors" => $errors];

        $arraySet = ["mensagem"];

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);
    }

    /**
     * TransportadorasHasUsuarios::updateTransportadorasUsuarioAPI
     *
     * Atualiza dados de transportadora.
     *
     * @param $data["id"]                   Id da Transportadora à ser atualizada
     * @param $data["nome_fantasia"]        Nome Fantasia
     * @param $data["razao_social"]         Razao Social
     * @param $data["cnpj"]                 CNPJ
     * @param $data["cep"]                  CEP
     * @param $data["endereco"]             Endereco
     * @param $data["endereco_numero"]      Endereco Numero
     * @param $data["endereco_complemento"] Endereco Complemento
     * @param $data["bairro"]               Bairro
     * @param $data["municipio"]            Municipio
     * @param $data["estado"]               Estado
     * @param $data["pais"]                 Pais
     * @param $data["tel_fixo"]             Tel Fixo
     * @param $data["tel_celular"]          Tel Celular
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 2018/05/05
     *
     * @return json Objeto JSON
     */
    public function updateTransportadorasUsuarioAPI()
    {
        $mensagem = array();

        $status = false;
        $message = null;
        $errors = array();

        try {

            if ($this->request->is('post')) {
                $data = $this->request->getData();
                $usuario = $this->Auth->user();

                $transportadorasId = isset($data["id"]) && strlen($data["id"]) > 0 ? $data["id"] : null;
                $cnpj = isset($data["cnpj"]) && strlen($data["cnpj"]) > 0 ? $data["cnpj"] : null;

                $transportadora = null;

                // Localiza pelo Id se fornecido
                if (isset($transportadorasId) && strlen($transportadorasId) > 0) {
                    $transportadora = $this->Transportadoras->getTransportadoraById($transportadorasId);
                }
                // Localiza registro pelo CNPJ
                if (!$transportadora && (isset($cnpj) && strlen($cnpj) > 0)) {
                    $transportadora = $this->Transportadoras->findTransportadoraByCNPJ($cnpj);
                }

                // Registro não encontrado, retorna mensagem de erro
                if (is_null($transportadora)) {

                    $mensagem = array(
                        "status" => 0,
                        "message" => __(Configure::read("messageOperationFailureDuringProcessing")),
                        "errors" => array(Configure::read("messageRecordNotFound"))
                    );
                }
                // Realiza update dos dados
                else {
                    $data["cnpj"] = $transportadora["cnpj"];

                    $transportadora = $this->Transportadoras->patchEntity($transportadora, $data);

                    $errors = $transportadora->errors();

                    if (!$errors) {
                        $transportadora = $this->Transportadoras->createUpdateTransportadora($data);

                        $mensagem = array(
                            "status" => 1,
                            "message" => __(Configure::read("messageSavedSuccess")),
                            "errors" => array()
                        );
                    } else {
                        $mensagem = array(
                            "status" => 0,
                            "message" => __(Configure::read("messageSavedError")),
                            "errors" => $errors
                        );
                    }
                }

                $arraySet = array(
                    "mensagem",
                    "transportadora"
                );

                $this->set(compact($arraySet));
                $this->set("_serialize", $arraySet);

                return;
            }
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $messageString = __("Não foi possível gravar transportadora para o usuário!");

            $mensagem = ['status' => false, 'message' => $messageString, 'errors' => $trace];

            $messageStringDebug = __("{0} - {1}. [Função: {3} / Arquivo: {4} / Linha: {5}]  ", $messageString, $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write("error", $messageStringDebug);
            Log::write("error", $trace);
        }

        $mensagem = array(
            "status" => $status,
            "message" => $message,
            "errors" => $errors
        );

        $arraySet = array(
            "mensagem",
            "transportadora"
        );

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);
    }

    /**
     * TransportadorasHasUsuarios::deleteTransportadorasUsuarioAPI
     *
     * Remove transportadora ao cadastro do Usuário final.
     *
     * @param $data["id"] Id da Transportadora para vínculo
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 2018/05/05
     *
     * @return json Objeto JSON
     */
    public function deleteTransportadorasUsuarioAPI()
    {
        $mensagem = array();

        $status = false;
        $message = null;
        $errors = array();

        try {

            if ($this->request->is('post')) {
                $data = $this->request->getData();
                $usuario = $this->Auth->user();

                $transportadorasId = isset($data["id"]) && strlen($data["id"]) > 0 ? $data["id"] : null;

                if (is_null($transportadorasId)) {
                    $mensagem = array(
                        "status" => false,
                        "messageOperationFailureDuringProcessing" => "É necessário especificar a Transportadora a ser removida do cadastro!",
                        "errors" => "É necessário especificar a Transportadora a ser removida do cadastro!",
                    );
                } else {
                    $deleteConditions = array();

                    $deleteConditions[] = [
                        "transportadoras_id" => $transportadorasId,
                        "usuarios_id" => $usuario["id"]
                    ];

                    $resultado = $this->TransportadorasHasUsuarios->deleteAll($deleteConditions);

                    if ($resultado == 1) {
                        $mensagem = array(

                            "status" => 1,
                            "message" => __(Configure::read("messageDeleteSuccess")),
                            "errors" => array()
                        );
                    } else {
                        $mensagem = array(
                            "status" => 0,
                            "message" => Configure::read("messageDeleteError"),
                            "errors" => array(__("Usuário não possui a transportadora em seu cadastro!")),
                        );
                    }
                }

                $arraySet = array("mensagem");

                $this->set(compact($arraySet));
                $this->set("_serialize", $arraySet);

                return;
            }
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $messageString = __("Não foi possível gravar transportadora para o usuário!");

            $mensagem = ['status' => false, 'message' => $messageString, 'errors' => $trace];

            $messageStringDebug = __("{0} - {1} em: {2}. [Função: {3} / Arquivo: {4} / Linha: {5}]  ", $messageString, $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write("error", $messageStringDebug);
        }

        $mensagem = ["status" => $status, "message" => $message, "errors" => $errors];

        $arraySet = ["mensagem"];

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);
    }
}
