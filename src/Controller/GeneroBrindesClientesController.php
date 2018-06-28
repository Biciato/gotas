<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\Database\Exception;
use Cake\ORM\TableRegistry;
use App\Custom\RTI\DebugUtil;

/**
 * GeneroBrindesClientes Controller
 *
 *
 * @method \App\Model\Entity\GeneroBrindesCliente[] paginate($object = null, array $settings = [])
 */
class GeneroBrindesClientesController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $generoBrindesClientes = $this->paginate($this->GeneroBrindesClientes);

        $this->set(compact('generoBrindesClientes'));
        $this->set('_serialize', ['generoBrindesClientes']);
    }

    /**
     * View method
     *
     * @param string|null $id Genero Brindes Cliente id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function generosBrindesCliente($clientesId = null)
    {
        try {
            $cliente = $this->Clientes->getClienteById($clientesId);

            if ($cliente) {
                $generoBrindesClientes = $this->GeneroBrindesClientes->getGeneroBrindesClientesByClientesId($clientesId);
            } else {
                $this->Flash->error(__(Configure::read("messageRecordClienteNotFound")));
            }

            $generoBrindesClientes = $this->paginate($generoBrindesClientes, ["limit" => 10]);

            $arraySet = ["cliente", "generoBrindesClientes"];
            $this->set(compact($arraySet));
            $this->set("_serialize", $arraySet);
        } catch (\Exception $e) {

            $messageString = __("Não foi possível exibir os dados de Gênero de Brindes do Cliente [{0}] Nome Fantasia: {1} / Razão Social:  {2} !", $cliente["id"], $cliente["nome_fantasia"], $cliente["razao_social"]);

            $trace = $e->getTrace();
            $mensagem = array('status' => false, 'message' => $messageString, 'errors' => $trace);
            $messageStringDebug = __("{0} - {1} em: {2}. [Função: {3} / Arquivo: {4} / Linha: {5}]  ", $messageString, $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write("error", $messageStringDebug);
        }
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function adicionarGeneroBrindesCliente(int $clientesId)
    {
        $cliente = null;

        try {
            $cliente = $this->Clientes->getClienteById($clientesId);

            $generoBrindesCliente = $this->GeneroBrindesClientes->newEntity();

            $generoBrindes = $this->GeneroBrindesClientes->getGenerosBrindesClientesDisponiveis($cliente["id"]);

            if ($this->request->is('post')) {

                $data = $this->request->getData();

                $data["clientes_id"] = $cliente->id;

                // Verifica se o brinde sendo gravado é um SMART shower e o id está diferente do definido pela regra de negócio

                if ($data["genero_brindes_id"] > 4 && $data["tipo_principal_codigo_brinde"] <= 4) {
                    $this->Flash->error("O brinde selecionado não deve ter um tipo menor ou igual à 4, pois estes valores são para SMART Shower!");
                } else {
                    // Verifica se este cliente não tem um cadastro com a mesma configuração, não pode ter repetido

                    $whereConditions = array(["clientes_id" => $clientesId, "genero_brindes_id" => $data["genero_brindes_id"]]);

                    $generoBrindesCheck = $this->GeneroBrindesClientes->findGeneroBrindesClientes($whereConditions, 1);

                    if (!empty($generoBrindesCheck)) {
                        $this->Flash->error(__("Já existe um gênero de brinde configurado para este cliente, conforme informações passadas!"));

                    } else {

                        /**
                         * Agora verifica se o mesmo código primário / secundário já não existe
                         * Cada Gênero deve pertencer a uma combinação única
                         */
                        $whereConditions = array(
                            [
                                "clientes_id" => $clientesId,
                                "tipo_principal_codigo_brinde" => (int)$data["tipo_principal_codigo_brinde"],
                            ]
                        );

                        if ($data["tipo_principal_codigo_brinde"] <= 4) {
                            $whereConditions[] = ["tipo_secundario_codigo_brinde" => $data["tipo_secundario_codigo_brinde"]];
                        }

                        $generoBrindesCheck = $this->GeneroBrindesClientes->findGeneroBrindesClientes($whereConditions, 1);

                        if (!empty($generoBrindesCheck)) {
                            $this->Flash->error(__("Já existe um gênero de brinde com este código de equipamento para este cliente, conforme informações passadas!"));

                        } else {
                        // Verifica se o brinde que está sendo cadastrado é um banho.
                        // Brindes de banho tem id de 1 a 4. então o campo tipo_secundario_codigo_brinde deve ser 00
                        // Pois esses campos são calculados conforme o tempo do brinde

                            if ($data["tipo_principal_codigo_brinde"] <= 4) {
                                $data["tipo_secundario_codigo_brinde"] = "00";
                            }

                            if ($this->GeneroBrindesClientes->saveGeneroBrindeCliente($data)) {
                                $this->Flash->success(__(Configure::read("messageSavedSuccess")));

                                return $this->redirect(['action' => 'generos_brindes_cliente', $clientesId]);
                            }
                            $this->Flash->error(__(Configure::read("messageSavedError")));
                        }
                    }
                }
            }

            $arraySet = [
                "cliente",
                "generoBrindes",
                "generoBrindesCliente"
            ];

            $this->set(compact($arraySet));
            $this->set('_serialize', $arraySet);
        } catch (\Exception $e) {

            $messageString = __("Não foi possível gravar um novo Gênero de Brindes para o Cliente [{0}] Nome Fantasia: {1}!", $cliente["id"], $cliente["nome_fantasia"]);

            $trace = $e->getTrace();
            $mensagem = array('status' => false, 'message' => $messageString, 'errors' => $trace);
            $messageStringDebug = __("{0} - {1} . [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $messageString, $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write("error", $messageStringDebug);
        }
    }

    /**
     * GeneroBrindesClientesController::editarGeneroBrindesCliente
     *
     * Método de edição de um gênero de brindes
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 06/06/2018
     *
     * @param string|null $id Genero Brindes Cliente id.
     *
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function editarGeneroBrindesCliente($id = null)
    {
        try {
            $generoBrindesCliente = $this->GeneroBrindesClientes->get($id);

            $cliente = $this->Clientes->getClienteById($generoBrindesCliente["clientes_id"]);

            $generoBrindes = $this->GeneroBrindes->find('list');

            if ($this->request->is(['patch', 'post', 'put'])) {

                $data = $this->request->getData();
                /**
                 * Verifica se há algum outro gênero de brinde cadastrado para este usuário,
                 * onde o tipo principal e secundário batem com outro mas o id é diferente do que
                 * está sendo modificado.
                 */

                $whereConditions = array(
                    "id != " => $id,
                    "tipo_principal_codigo_brinde" => $data["tipo_principal_codigo_brinde"],
                    "tipo_secundario_codigo_brinde" => $data["tipo_secundario_codigo_brinde"]
                );

                $generoBrindeClienteCheck = $this->GeneroBrindesClientes->findGeneroBrindesClientes($whereConditions, 1);

                if (!empty($generoBrindeClienteCheck)) {
                    $this->Flash->error(__("Já existe um brinde com a configuração de tipo principal e tipo secundário de código!"));
                } else {

                    $generoBrindesCliente = $this->GeneroBrindesClientes->patchEntity($generoBrindesCliente, $this->request->getData());
                    if ($this->GeneroBrindesClientes->save($generoBrindesCliente)) {
                        $this->Flash->success(__(Configure::read("messageSavedSuccess")));

                        return $this->redirect(['action' => 'generos_brindes_cliente', $cliente["id"]]);
                    }
                    $this->Flash->error(__(Configure::read("messageSavedSuccess")));
                }
            }

            $arraySet = [
                "cliente",
                "generoBrindes",
                "generoBrindesCliente"
            ];

            $this->set(compact($arraySet));
            $this->set('_serialize', [$arraySet]);

        } catch (\Exception $e) {

            $messageString = __("Não foi possível gravar um novo Gênero de Brindes para o Cliente [{0}] Nome Fantasia: {1}!", $cliente["id"], $cliente["nome_fantasia"]);

            $trace = $e->getTrace();
            $mensagem = array('status' => false, 'message' => $messageString, 'errors' => $trace);
            $messageStringDebug = __("{0} - {1} . [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $messageString, $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write("error", $messageStringDebug);
        }
    }

    /**
     * Delete method
     *
     * @param string|null $id Genero Brindes Cliente id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        try {
            $query = $this->request->query;
            $this->request->allowMethod(['post', 'delete']);

            $generoBrindesClienteId = $query["genero_brindes_cliente_id"];

            $returnUrl = $query["return_url"];

            $generoBrindesCliente = $this->GeneroBrindesClientes->get($generoBrindesClienteId);
            if ($this->GeneroBrindesClientes->delete($generoBrindesCliente)) {
                $this->Flash->success(__(Configure::read("messageDeleteSuccess")));
            } else {
                $this->Flash->error(__(Configure::read("messageDeleteError")));
            }

            return $this->redirect($returnUrl);
        } catch (\Exception $e) {

            $messageString = __("Não foi possível remover um Gênero de Brindes!");

            $trace = $e->getTrace();
            $mensagem = array('status' => false, 'message' => $messageString, 'errors' => $trace);
            $messageStringDebug = __("{0} - {1} . [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $messageString, $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write("error", $messageStringDebug);
        }
    }

    /**
     * GeneroBrindesClientes::verDetalhes
     *
     * Action de visualizar detalhes
     *
     * @param integer $generoBrindesClienteId Id
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 06/06/2018
     *
     * @return void
     */
    public function verDetalhes(int $generoBrindesClienteId)
    {
        $generoBrindesClientes = $this->GeneroBrindesClientes->getGeneroBrindesClientesById($generoBrindesClienteId);

        $cliente = $this->Clientes->getClienteById($generoBrindesClientes["clientes_id"]);

        $arraySet = array(
            "generoBrindesClientes",
            "cliente"
        );

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);
    }

    /**
     * Altera o estado de um Gênero de Brinde de Cliente
     *
     * @param  $query["genero_brindes_cliente_id"]
     * @param  $query["clientes_id"]
     * @param  $query["estado"]
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date   2018/06/08
     *
     * @return void
     */
    public function alteraEstadoGeneroBrindesCliente()
    {
        try {

            $query = $this->request->query;

            $generoBrindesClienteId = $query["genero_brindes_cliente_id"];
            $clientesId = $query["clientes_id"];
            $estado = $query["estado"];

            if ($this->GeneroBrindesClientes->updateHabilitadoGeneroBrindeCliente($generoBrindesClienteId, $estado)) {

                $mensagemAviso = $estado ? __(Configure::read("messageEnableSuccess")) : __(Configure::read("messageDisableSuccess"));

                $this->Flash->success(__($mensagemAviso));

                return $this->redirect(array("controller" => "genero_brindes_clientes", "action" => "generos_brindes_cliente", $clientesId));
            }

            $mensagemErro = $estado ? __(Configure::read("messageEnableError")) : __(Configure::read("messageDisableError"));

            $this->Flash->error(__($mensagemErro));

            return $this->redirect(array("controller" => "genero_brindes_clientes", "action" => "generos_brindes_cliente", $clientesId));
        } catch (\Exception $e) {

            $messageString = __("Não foi possível alterar o estado de habilitado/desabilitado um Gênero de Brindes de Cliente!");

            $trace = $e->getTrace();
            $mensagem = array('status' => false, 'message' => $messageString, 'errors' => $trace);
            $messageStringDebug = __("{0} - {1} . [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $messageString, $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write("error", $messageStringDebug);
        }
    }

    /**
     * GeneroBrindesClientesController::getGeneroBrindesClienteAPI
     *
     * Obtem a lista de Gênero de Brindes vinculada a unidades da rede
     *
     * @param int $post["redesId"] Id da rede
     * @param int $post["clientesId"] Id do cliente
     *
     * @explain: Um dos 2 tipos devem ser informados.
     * A pesquisa é feita através de um deles, caso contrário retorna json de erro
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date   28/06/2018
     *
     * @return json object
     */
    public function getGeneroBrindesClienteAPI()
    {
        $messageString = null;
        $status = false;

        $mensagem = array();
        $genero_brindes_cliente = array();

        try {
            if ($this->request->is(['post'])) {
                $data = $this->request->getData();

                $redesId = !empty($data["redes_id"]) && $data["redes_id"] > 0 ? $data["redes_id"] : null;
                $clientesId = !empty($data["clientesId"]) && $data["clientesId"] > 0 ? $data["clientesId"] : null;

                if (($redesId == null) && ($clientesId == null)) {

                    $messageString = __("É necessário informar uma rede ou um posto de atendimento para continuar!");

                } else {

                    $clientesIds = array();
                    if (!empty($redesId)) {
                        // Pesquisa pelo id da rede

                        $redeHasClientesTable = TableRegistry::get("RedesHasClientes");

                        $clientesIds = $redeHasClientesTable->getClientesIdsFromRedesHasClientes($redesId);


                    } else if (!empty($clientesId)) {
                        // Pesquisa pelo id da rede
                        $clientesIds[] = $clientesId;
                    }

                    // Com a lista de Clientes Ids obtida, faz a pesquisa

                    $generoBrindesIds = $this->GeneroBrindesClientes->findGeneroBrindesClienteByClientesIds($clientesIds);


                    // TODO: Erro aqui!
                    // print_r($generoBrindesIds);
                    // DebugUtil::printArray($clientesIds);
                    $generoBrindesQuery = $this->GeneroBrindes->findGeneroBrindesByIds($generoBrindesIds);
                    DebugUtil::printArray($generoBrindesQuery);

                    $genero_brindes = array("count" => $generoBrindesQuery["count"], "data" => $generoBrindesQuery["data"]);
                }
                $mensagem = ['status' => true, 'message' => $messageString];
            }

        } catch (\Exception $e) {
            $messageString = __("Não foi possível obter dados de Redes e Pontuações!");
            $trace = $e->getTrace();
            $mensagem = array('status' => false, 'message' => $messageString, 'errors' => $trace);
            $messageStringDebug = __("{0} - {1} em: {2}. [Função: {3} / Arquivo: {4} / Linha: {5}]  ", $messageString, $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write("error", $messageStringDebug);
        }

        $arraySet = [
            'genero_brindes',
            'mensagem'
        ];

        $this->set(compact($arraySet));
        $this->set('_serialize', $arraySet);
    }

}
