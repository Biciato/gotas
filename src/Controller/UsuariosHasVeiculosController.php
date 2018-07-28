<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Log\Log;
use App\Custom\RTI\DebugUtil;
use App\Model\Entity\Veiculo;
use App\Model\Table\VeiculosTable;

/**
 * UsuariosHasVeiculos Controller
 *
 * @property \App\Model\Table\UsuariosHasVeiculosTable $UsuariosHasVeiculos
 *
 * @method \App\Model\Entity\UsuariosHasVeiculo[] paginate($object = null, array $settings = [])
 */
class UsuariosHasVeiculosController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Usuarios', 'Veiculos']
        ];
        $usuariosHasVeiculos = $this->paginate($this->UsuariosHasVeiculos);

        $this->set(compact('usuariosHasVeiculos'));
        $this->set('_serialize', ['usuariosHasVeiculos']);
    }

    /**
     * View method
     *
     * @param string|null $id Usuarios Has Veiculo id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $usuariosHasVeiculo = $this->UsuariosHasVeiculos->get($id, [
            'contain' => ['Usuarios', 'Veiculos']
        ]);

        $this->set('usuariosHasVeiculo', $usuariosHasVeiculo);
        $this->set('_serialize', ['usuariosHasVeiculo']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $usuariosHasVeiculo = $this->UsuariosHasVeiculos->newEntity();
        if ($this->request->is('post')) {
            $usuariosHasVeiculo = $this->UsuariosHasVeiculos->patchEntity($usuariosHasVeiculo, $this->request->getData());
            if ($this->UsuariosHasVeiculos->save($usuariosHasVeiculo)) {
                $this->Flash->success(__('The usuarios has veiculo has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The usuarios has veiculo could not be saved. Please, try again.'));
        }
        $usuarios = $this->UsuariosHasVeiculos->Usuarios->find('list', ['limit' => 200]);
        $veiculos = $this->UsuariosHasVeiculos->Veiculos->find('list', ['limit' => 200]);
        $this->set(compact('usuariosHasVeiculo', 'usuarios', 'veiculos'));
        $this->set('_serialize', ['usuariosHasVeiculo']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Usuarios Has Veiculo id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $usuariosHasVeiculo = $this->UsuariosHasVeiculos->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $usuariosHasVeiculo = $this->UsuariosHasVeiculos->patchEntity($usuariosHasVeiculo, $this->request->getData());
            if ($this->UsuariosHasVeiculos->save($usuariosHasVeiculo)) {
                $this->Flash->success(__('The usuarios has veiculo has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The usuarios has veiculo could not be saved. Please, try again.'));
        }
        $usuarios = $this->UsuariosHasVeiculos->Usuarios->find('list', ['limit' => 200]);
        $veiculos = $this->UsuariosHasVeiculos->Veiculos->find('list', ['limit' => 200]);
        $this->set(compact('usuariosHasVeiculo', 'usuarios', 'veiculos'));
        $this->set('_serialize', ['usuariosHasVeiculo']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Usuarios Has Veiculo id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $usuariosHasVeiculo = $this->UsuariosHasVeiculos->get($id);
        if ($this->UsuariosHasVeiculos->delete($usuariosHasVeiculo)) {
            $this->Flash->success(__(Configure::read('messageDeleteSuccess')));
        } else {
            $this->Flash->error(__(Configure::read('messageDeleteError')));
        }

        return $this->redirect($this->referer());
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
    public function deleteVeiculoUsuarioFinal(int $usuario_has_veiculo_id)
    {
        try {
            $this->request->allowMethod(['post', 'delete']);

            $usuario_has_veiculo = $this->UsuariosHasVeiculos->getUsuariosHasVeiculosById($usuario_has_veiculo_id);

            if ($this->UsuariosHasVeiculos->delete($usuario_has_veiculo)) {
                $this->Flash->success(__(Configure::read('messageDeleteSuccess')));
            } else {
                $this->Flash->error(__(Configure::read('messageDeleteError')));
            }

            return $this->redirect(
                [
                    'controller' => 'Veiculos',
                    'action' => 'veiculos_usuario_final', $usuario_has_veiculo->usuarios_id
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
     * --------------------------------------------------------------
     * Serviços REST
     * --------------------------------------------------------------
     */

    /**
     * UsuariosHasVeiculosController::getVeiculosUsuarioAPI
     *
     * Serviço REST que retorna a lista de veículos que o usuário logado possui
     *
     * @param $data["placa"]      Placa do veículo
     * @param $data["modelo"]     Modelo do veículo
     * @param $data["fabricante"] Fabricante do veículo
     * @param $data["ano"]        Ano do veículo
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 2018/04/29
     *
     * @return json Objeto JSON
     */
    public function getVeiculosUsuarioAPI()
    {
        try {
            // Dados de mensagem
            $mensagem = array();
            $message = null;
            $status = true;

            if ($this->request->is('post')) {
                $usuario = $this->Auth->user();

                $data = $this->request->getData();

                $placa = isset($data["placa"]) ? $data["placa"] : null;
                $modelo = isset($data["modelo"]) ? $data["modelo"] : null;
                $fabricante = isset($data["fabricante"]) ? $data["fabricante"] : null;
                $ano = isset($data["ano"]) ? $data["ano"] : null;

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

                $resultado = $this->UsuariosHasVeiculos->getVeiculosUsuario(
                    $this->Auth->user()["id"],
                    $placa,
                    $modelo,
                    $fabricante,
                    $ano,
                    $orderConditions,
                    $paginationConditions
                );

                $mensagem = $resultado["mensagem"];
                $veiculos = $resultado["veiculos"];

                $arraySet = array("mensagem", "veiculos");

                $this->set(compact($arraySet));
                $this->set("_serialize", $arraySet);
            }
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $messageString = __("Não foi possível obter dados de veículos do usuário!");

            $mensagem = ['status' => false, 'message' => $messageString, 'errors' => $trace];

            $messageStringDebug =
                $stringError = __(
                "{0} - {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ",
                $messageString,
                $e->getMessage(),
                __FUNCTION__,
                __FILE__,
                __LINE__
            );

            Log::write("error", $messageStringDebug);
            Log::write("error", $trace);
        }
    }

    /**
     * UsuariosHasVeiculosController::setVeiculoUsuario
     *
     * Serviço REST que grava um veículo para o usuário logado
     *
     * @param $data["placa"]      Placa do veículo
     * @param $data["modelo"]     Modelo do veículo
     * @param $data["fabricante"] Fabricante do veículo
     * @param $data["ano"]        Ano do veículo
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 2018/04/29
     *
     * @return json Objeto JSON
     */
    public function setVeiculosUsuarioAPI()
    {
        // Dados de mensagem
        $mensagem = array();
        $message = Configure::read("messageSavedSuccess");
        $status = false;
        $errors = array();

        try {
            if ($this->request->is('post')) {
                $usuario = $this->Auth->user();

                $data = $this->request->getData();

                $whereConditions = array();

                $veiculo = null;

                $veiculosId = isset($data["id"]) && $data["id"] > 0 ? $data["id"] : null;
                $placa = isset($data["placa"]) ? $data["placa"] : null;

                // Se id do veiculo foi informado, faz pesquisa pelo id primeiro.

                if (isset($veiculosId)) {
                    $veiculo = $this->Veiculos->findVeiculos(["id" => $data["id"]])->first();
                }

                // Se não achou o veículo, faz pesquisa pela placa.

                if (!$veiculo) {
                    $resultado = $this->Veiculos->getVeiculoByPlaca($data["placa"]);
                    $veiculo = $resultado["veiculo"];
                }

                /**
                 * Se tiver encontrado o Veículo pela placa, verifica se há vínculo.
                 * Se tiver, validar se já está cadastrado ao Usuário.
                 * - Se tiver, mensagem de erro informando que não há necessidade.
                 * - Se não tiver, cadastra
                 */

                if ($veiculo) {
                    $whereConditions = array();

                    $whereConditions[] = ["veiculos_id" => $veiculo->id];
                    $whereConditions[] = ["usuarios_id" => $usuario["id"]];

                    // Antes de gravar, verifica se o usuário já não possui um veículo em seu cadastro
                    $usuarioHasVeiculosCheck = $this->UsuariosHasVeiculos->findUsuariosHasVeiculos($whereConditions)->first();

                    if (is_null($usuarioHasVeiculosCheck)) {
                        // grava novo registro
                        $usuarioHasVeiculoSave
                            = $this->UsuariosHasVeiculos->addUsuarioHasVeiculo($veiculo->id, $usuario["id"]);

                        if ($usuarioHasVeiculoSave) {
                            $status = true;
                            $message = __(Configure::read("messageSavedSuccess"));
                        }
                    }
                    // Já existe registro, não vincular novamente
                    else {
                        $status = false;
                        $message = __("Veículo já se encontra em seu perfil, não é necessário realizar novo cadastro!");
                    }
                }
                // Não encontrou veículo cadastrado no sistema

                // Gravação de dados utilizando cadastro de veículos
                else {

                    // Validações

                    /**
                     * Caso não seja informado id do veículo, será informado placa.
                     * Então verifica se já um veículo com os dados cadastrados
                     * Se tiver, apenas vincula
                     */

                    $veiculoSave = $this->Veiculos->newEntity();
                    $veiculoSave = $this->Veiculos->patchEntity($veiculoSave, $data);
                    $errors = $veiculoSave->errors();

                    $veiculoSave = $this->Veiculos->save($veiculoSave);

                    $usuarioHasVeiculoSave = false;

                    // se salvou corretamente, vincula
                    if ($veiculoSave) {

                        // grava novo registro
                        $usuarioHasVeiculoSave
                            = $this->UsuariosHasVeiculos->addUsuarioHasVeiculo($veiculoSave->id, $usuario['id']);

                        if ($usuarioHasVeiculoSave) {
                            $status = true;
                            $message = __("Veículo cadastrado com sucesso!");
                        }
                    }

                    // se não salvou, exibe todas as mensagens de erro com validação
                    if ($veiculoSave != true && $usuarioHasVeiculoSave != true) {
                        $status = false;
                        $message = __("Houve erros durante o procedimento, confira se todos os campos estão preenchidos!");
                    }
                }
            }
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $messageString = __("Não foi possível gravar veículo para o usuário!");

            $mensagem = ['status' => false, 'message' => $messageString, 'errors' => $trace];

            $messageStringDebug = __("{0} - {1} em: {2}. [Função: {3} / Arquivo: {4} / Linha: {5}]  ", $messageString, $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write("error", $messageStringDebug);
        }

        $mensagem = ["status" => $status, "message" => $message, "errors" => $errors];

        $arraySet = ["mensagem"];

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);
    }

    /**
     * UsuariosHasVeiculos::updateVeiculosUsuarioAPI
     *
     * Atualiza dados de veículo.
     *
     * @param $data["id"]         Id do veículo
     * @param $data["placa"]      Placa do veículo
     * @param $data["modelo"]     Modelo do veículo
     * @param $data["fabricante"] Fabricante do veículo
     * @param $data["ano"]        Ano do veículo
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 2018/05/05
     *
     * @return json Objeto JSON
     */
    public function updateVeiculosUsuarioAPI()
    {
        $mensagem = array();

        $status = false;
        $message = null;
        $errors = array();

        try {

            if ($this->request->is('post')) {
                $data = $this->request->getData();
                $usuario = $this->Auth->user();

                $veiculosId = isset($data["id"]) && strlen($data["id"]) > 0 ? $data["id"] : null;
                $placa = isset($data["placa"]) && strlen($data["placa"]) > 0 ? $data["placa"] : null;
                $modelo = isset($data["modelo"]) && strlen($data["modelo"]) > 0 ? $data["modelo"] : null;
                $fabricante = isset($data["fabricante"]) && strlen($data["fabricante"]) > 0 ? $data["fabricante"] : null;
                $ano = isset($data["ano"]) && strlen($data["ano"]) > 0 ? $data["ano"] : null;

                $veiculo = null;

                // Verifica se os parâmetros estão informados, se não, não é possível atualizar

                $arrayCheck = array("placa", "modelo", "fabricante", "ano");
                $arrayErrors = array();

                foreach ($arrayCheck as $item) {
                    if (empty($data[$item])) {
                        $arrayErrors = __("O campo {0} precisa estar preenchido para continuar!", strtoupper($item));
                    }
                }

                if (sizeof($arrayErrors) > 0) {
                    $mensagem = array(
                        "status" => 0,
                        "message" => Configure::read("messageOperationFailureDuringProcessing"),
                        "errors" => $arrayErrors
                    );

                    $veiculo = array(
                        "data" => array(),
                        "count" => 0,
                        "page_count" => 0
                    );
                    $arraySet = array("mensagem", "veiculo");

                    $this->set(compact($arraySet));
                    $this->set("_serialize", $arraySet);
                    return;
                }

                // Localiza pelo Id se fornecido
                if (isset($veiculosId)) {
                    $veiculo = $this->Veiculos->getVeiculoById($veiculosId);
                }
                // Localiza registro pela placa se não achou pelo id.
                if (!$veiculo && isset($placa)) {
                    $resultado = $this->Veiculos->getVeiculoByPlaca($placa);
                    $veiculo = $resultado["veiculo"];
                }

                // Se não achou, retorna erro pois não existe

                if (empty($veiculo)) {
                    $mensagem = array(
                        "status" => 0,
                        "message" => Configure::read("messageOperationFailureDuringProcessing"),
                        "errors" => array("Veiculo não existe no sistema, não sendo possível atualizar o mesmo!")
                    );

                    $veiculo = array(
                        "data" => array(),
                        "count" => 0,
                        "page_count" => 0
                    );
                    $arraySet = array("mensagem", "veiculo");

                    $this->set(compact($arraySet));
                    $this->set("_serialize", $arraySet);
                    return;
                }

                // Realiza update dos dados
                else {
                    $placa = $veiculo["placa"];

                    $veiculo = $this->Veiculos->saveUpdateVeiculo(
                        $veiculo["id"],
                        $placa,
                        $modelo,
                        $fabricante,
                        $ano
                    );

                    $mensagem = array(
                        "status" => $veiculo? 1 : 0,
                        "mensagem" => $veiculo ? Configure::read("messageProcessingCompleted") : Configure::read("messageOperationFailureDuringProcessing"),
                        "errors" => $veiculo ? array() : $veiculo->errors()
                    );

                    $arraySet = ["mensagem", "veiculo"];

                    $this->set(compact($arraySet));
                    $this->set("_serialize", $arraySet);

                    return;
                }
            }
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $messageString = __("Não foi possível gravar veículo!");

            $mensagem = ['status' => false, 'message' => $messageString, 'errors' => $trace];

            $messageStringDebug = __("{0} - {1} em: {2}. [Função: {3} / Arquivo: {4} / Linha: {5}]  ", $messageString, $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write("error", $messageStringDebug);
        }

        $mensagem = ["status" => $status, "message" => $message, "errors" => $errors];

        $arraySet = ["mensagem", "veiculo"];

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);
    }

    /**
     * UsuariosHasVeiculos::deleteVeiculosUsuarioAPI
     *
     * Remove o vínculo de um veículo com um funcionário
     *
     * @param $data["id"] Id do Veículo

     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 2018/05/04
     *
     * @return json Objeto JSON
     */
    public function deleteVeiculosUsuarioAPI()
    {
        $mensagem = array();
        $errors = array();
        $message = null;
        $status = false;

        try {
            if ($this->request->is("post")) {
                $usuario = $this->Auth->user();
                $data = $this->request->getData();

                $veiculosId = isset($data["id"]) && strlen($data["id"]) > 0 ? $data["id"] : null;

                if (is_null($veiculosId)) {
                    $mensagem = array(
                        "status" => 0,
                        "message" => Configure::read("messageDeleteError"),
                        "errors" => array("É necessário especificar o Veículo a ser removido do cadastro!")
                    );

                    $arraySet = array("mensagem");
                    $this->set(compact($arraySet));
                    $this->set("_serialize", $arraySet);

                    return;
                } else {
                    $deleteConditions = array();

                    $deleteConditions[] = [
                        'veiculos_id' => $veiculosId,
                        "usuarios_id" => $usuario["id"]
                    ];

                    $resultado = $this->UsuariosHasVeiculos->deleteUsuariosHasVeiculos($deleteConditions);

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
                            "errors" => array(
                                __("Usuário não possui o veículo em seu cadastro.")
                            )
                        );
                    }
                }
            }
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $messageString = __("Não foi possível remover veículo do usuário!");

            $mensagem = array(
                'status' => false,
                'message' => $messageString,
                'errors' => $trace
            );

            $messageStringDebug = __(
                "{0} - {1}. [Função: {3} / Arquivo: {4} / Linha: {5}]  ",
                $messageString,
                $e->getMessage(),
                __FUNCTION__,
                __FILE__,
                __LINE__
            );

            Log::write("error", $messageStringDebug);

            Log::write("error", $trace);
        }

        $arraySet = ["mensagem"];

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);
    }

    /**
     * ------------------------------------------------------------
     * Relatórios (Dashboard de Admin RTI)
     * ------------------------------------------------------------
     */

    /**
     * Exibe Action de Relatório Detalhado das Veiculos
     * de Usuários de cada Rede
     *
     * @param int $veiculoHasUsuarioId
     * @return void
     */
    public function relatorioVeiculosUsuariosDetalhado(int $veiculoHasUsuarioId)
    {
        try {

            $veiculoHasUsuarios = $this->UsuariosHasVeiculos->findUsuariosHasVeiculosByVeiculosId($veiculoHasUsuarioId)->toArray();

            $veiculo = $this->Veiculos->get($veiculoHasUsuarios[0]->veiculos_id);

            $arraySet = [
                'veiculoHasUsuarios',
                'veiculo'
            ];

            $this->set(compact([$arraySet]));

        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao exibir relatório de veiculos detalhado: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }
}
