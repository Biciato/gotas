<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\Database\Exception;
use Cake\ORM\TableRegistry;
use App\Custom\RTI\DebugUtil;

/**
 * TiposBrindesClientes Controller
 *
 *
 * @method \App\Model\Entity\TiposBrindesCliente[] paginate($object = null, array $settings = [])
 */
class TiposBrindesClientesController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $tiposBrindesClientes = $this->paginate($this->TiposBrindesClientes);

        $this->set(compact('tiposBrindesClientes'));
        $this->set('_serialize', ['tiposBrindesClientes']);
    }

    /**
     * View method
     *
     * @param string|null $id Tipos Brindes Cliente id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function tiposBrindesCliente($clientesId = null)
    {
        $tiposBrindesClientes = array();
        $cliente = $this->Clientes->getClienteById($clientesId);

        if (empty($cliente)) {
            throw new \Exception(__(Configure::read("messageRecordClienteNotFound")));
        }

        try {

            $tiposBrindesClientes = $this->TiposBrindesClientes->getTiposBrindesClientesByClientesId($clientesId);

            $tiposBrindesClientes = $this->paginate($tiposBrindesClientes, ["limit" => 10]);

            // DebugUtil::print($tiposBrindesClientes);
        } catch (\Exception $e) {

            $messageString = __("Não foi possível exibir os dados de Tipos de Brindes do Cliente [{0}] Nome Fantasia: {1} / Razão Social:  {2} !", $cliente["id"], $cliente["nome_fantasia"], $cliente["razao_social"]);

            $trace = $e->getTrace();
            $mensagem = array('status' => false, 'message' => $messageString, 'errors' => $trace);
            $messageStringDebug = __("{0} - {1} em: {2}. [Função: {3} / Arquivo: {4} / Linha: {5}]  ", $messageString, $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write("error", $messageStringDebug);
            Log::write("error", $trace);
        }

        // DebugUtil::print($tiposBrindesClientes);
        $arraySet = ["cliente", "tiposBrindesClientes"];
        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);

    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function adicionarTiposBrindesCliente(int $clientesId)
    {
        $cliente = null;
        $cliente = $this->Clientes->getClienteById($clientesId);

        if (empty($cliente)) {
            throw new \Exception(__("{0}{1}"), Configure::read("messageLoadDataWithError"), __(Configure::read("messageRecordClienteNotFound")));
        }

        $tiposBrindesRedesQuery = $this->TiposBrindesClientes->getTiposBrindesClientesDisponiveis($cliente["id"]);

        $tiposBrindesRedes = array();
        foreach ($tiposBrindesRedesQuery as $tipoBrinde) {

            $tipo = array(
                "text" => $tipoBrinde["brinde_necessidades_especiais"] ? __("{0} {1}", $tipoBrinde["nome"], "PNE") : $tipoBrinde["nome"],
                "value" => $tipoBrinde["id"],
                "id" => "tipos_brindes_redes_id",
                "data-tipo-principal" => $tipoBrinde["tipo_principal_codigo_brinde_default"],
                "data-tipo-secundario" => $tipoBrinde["tipo_secundario_codigo_brinde_default"],
                "equipamento_rti" => $tipoBrinde["equipamento_rti"]
            );
            $tiposBrindesRedes[] = $tipo;
        }

        // DebugUtil::print($tiposBrindesRedes);
        try {

            $tiposBrindesCliente = $this->TiposBrindesClientes->newEntity();

            // DebugUtil::print($tiposBrindesRedes);
            if ($this->request->is('post')) {

                $data = $this->request->getData();
                $equipamentoRTI = false;
                foreach ($tiposBrindesRedes as $tipoBrindeRede) {
                    if ($tipoBrindeRede["value"] == $data["tipos_brindes_redes_id"]){
                        $equipamentoRTI = $tipoBrindeRede["equipamento_rti"] ? true : false;
                    }
                }
                // $

                $data["clientes_id"] = $cliente->id;

                // Verifica se o brinde sendo gravado é um SMART shower e o id está diferente do definido pela regra de negócio

                // if ($equipamentoRTI && (is_numeric($data["tipo_principal_codigo_brinde"]) && $data["tipo_principal_codigo_brinde"] <= 4)) {
                //     $this->Flash->error("O brinde selecionado não deve ter um tipo principal menor ou igual à 4, pois estes valores são para SMART Shower!");
                // } else {
                    // Verifica se este cliente não tem um cadastro com a mesma configuração, não pode ter repetido

                    // $whereConditions = array(["clientes_id" => $clientesId, "tipos_brindes_redes_id" => $data["tipos_brindes_redes_id"]]);

                    // $tiposBrindesCheck = $this->TiposBrindesClientes->findTiposBrindesClientes($whereConditions, 1);

                    // if (!empty($tiposBrindesCheck)) {
                    //     $this->Flash->error(__("Já existe um tipo de brinde configurado para este cliente, conforme informações passadas!"));

                    // } else {

                        /**
                         * Agora verifica se o mesmo código primário / secundário já não existe
                         * Cada Tipo de Brinde deve pertencer a uma combinação única
                         */
                        $whereConditions = array(
                            [
                                "clientes_id" => $clientesId,
                                "tipo_principal_codigo_brinde" => (int)$data["tipo_principal_codigo_brinde"],
                            ]
                        );

                        // if (is_numeric($data["tipo_principal_codigo_brinde"]) && $data["tipo_principal_codigo_brinde"] <= 4) {
                        //     $whereConditions[] = ["tipo_secundario_codigo_brinde" => $data["tipo_secundario_codigo_brinde"]];
                        // }

                        // $tiposBrindesCheck = $this->TiposBrindesClientes->findTiposBrindesClientes($whereConditions, 1);

                        // if (!empty($tiposBrindesCheck)) {
                        //     $this->Flash->error(__("Já existe um tipo de brinde com este código de equipamento para este cliente, conforme informações passadas!"));

                        // } else {
                        // Verifica se o brinde que está sendo cadastrado é um banho.
                        // Brindes de banho tem id de 1 a 4. então o campo tipo_secundario_codigo_brinde deve ser 00
                        // Pois esses campos são calculados conforme o tempo do brinde

                            if (is_numeric($data["tipo_principal_codigo_brinde"]) && $data["tipo_principal_codigo_brinde"] <= 4) {
                                $data["tipo_secundario_codigo_brinde"] = "00";
                            }

                            $tiposBrindesClienteSave = $this->TiposBrindesClientes->saveTiposBrindeCliente(
                                $data["tipos_brindes_redes_id"],
                                $data["clientes_id"],
                                $data["tipo_principal_codigo_brinde"],
                                $data["tipo_secundario_codigo_brinde"],
                                $data["habilitado"],
                                0
                            );

                            if ($tiposBrindesClienteSave) {
                                $this->Flash->success(__(Configure::read("messageSavedSuccess")));

                                return $this->redirect(['action' => 'tipos_brindes_cliente', $clientesId]);
                            }
                            $this->Flash->error(__(Configure::read("messageSavedError")));
                    //     }
                    // }
                // }
            }

        } catch (\Exception $e) {

            $messageString = __("Não foi possível gravar um novo Tipo de Brindes para o Cliente [{0}] Nome Fantasia: {1}!", $cliente["id"], $cliente["nome_fantasia"]);

            $trace = $e->getTrace();
            $mensagem = array('status' => false, 'message' => $messageString, 'errors' => $trace);
            $messageStringDebug = __("{0} - {1} . [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $messageString, $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write("error", $messageStringDebug);
            Log::write("error", $trace);
        }

        $arraySet = [
            "cliente",
            "tiposBrindesRedes",
            "tiposBrindesCliente"
        ];

        $this->set(compact($arraySet));
        $this->set('_serialize', $arraySet);
    }

    /**
     * TiposBrindesClientesController::editarTiposBrindesCliente
     *
     * Método de edição de um tipos de brindes
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 06/06/2018
     *
     * @param string|null $id Tipos Brindes Cliente id.
     *
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function editarTiposBrindesCliente($id = null)
    {
        try {
            $tiposBrindesCliente = $this->TiposBrindesClientes->get($id);

            $cliente = $this->Clientes->getClienteById($tiposBrindesCliente["clientes_id"]);

            if ($this->request->is(['patch', 'post', 'put'])) {

                $data = $this->request->getData();
                /**
                 * Verifica se há algum outro tipo de brinde cadastrado para este usuário,
                 * onde o tipo principal e secundário batem com outro mas o id é diferente do que
                 * está sendo modificado.
                 */

                $whereConditions = array(
                    "id != " => $id,
                    "clientes_id != " => $tiposBrindesCliente["clientes_id"],
                    "tipo_principal_codigo_brinde" => $data["tipo_principal_codigo_brinde"],
                    "tipo_secundario_codigo_brinde" => $data["tipo_secundario_codigo_brinde"]
                );

                $tiposBrindesClienteCheck = $this->TiposBrindesClientes->findTiposBrindesClientes($whereConditions, 1);

                if (!empty($tiposBrindesClienteCheck)) {
                    $this->Flash->error(__("Já existe um brinde com a configuração de tipo principal e tipo secundário de código!"));
                } else {

                    $tiposBrindesCliente = $this->TiposBrindesClientes->patchEntity($tiposBrindesCliente, $this->request->getData());
                    if ($this->TiposBrindesClientes->save($tiposBrindesCliente)) {
                        $this->Flash->success(__(Configure::read("messageSavedSuccess")));

                        return $this->redirect(['action' => 'tipos_brindes_cliente', $cliente["id"]]);
                    }
                    $this->Flash->error(__(Configure::read("messageSavedSuccess")));
                }
            }

            $tiposBrindesRedes = array();

            $arraySet = [
                "cliente",
                "tiposBrindesRedes",
                "tiposBrindesCliente"
            ];

            $this->set(compact($arraySet));
            $this->set('_serialize', [$arraySet]);

        } catch (\Exception $e) {

            $messageString = __("Não foi possível gravar um novo Tipo de Brindes para o Cliente [{0}] Nome Fantasia: {1}!", $cliente["id"], $cliente["nome_fantasia"]);

            $trace = $e->getTrace();
            $mensagem = array('status' => false, 'message' => $messageString, 'errors' => $trace);
            $messageStringDebug = __("{0} - {1} . [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $messageString, $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write("error", $messageStringDebug);
        }
    }

    /**
     * Delete method
     *
     * @param string|null $id Tipos Brindes Cliente id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        try {
            $this->request->allowMethod(['post', 'delete']);
            $query = $this->request->query;
            $tiposBrindesClienteId = $query["tipos_brindes_cliente_id"];
            $returnUrl = $query["return_url"];

            $tiposBrindesCliente = $this->TiposBrindesClientes->get($tiposBrindesClienteId);
            if ($this->TiposBrindesClientes->delete($tiposBrindesCliente)) {
                $this->Flash->success(__(Configure::read("messageDeleteSuccess")));
            } else {
                $this->Flash->error(__(Configure::read("messageDeleteError")));
            }

            return $this->redirect($returnUrl);
        } catch (\Exception $e) {

            $messageString = __("Não foi possível remover um Tipo de Brindes!");

            $trace = $e->getTrace();
            $mensagem = array('status' => false, 'message' => $messageString, 'errors' => $trace);
            $messageStringDebug = __("{0} - {1} . [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $messageString, $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write("error", $messageStringDebug);
        }
    }

    /**
     * TiposBrindesClientes::verDetalhes
     *
     * Action de visualizar detalhes
     *
     * @param integer $tiposBrindesClienteId Id
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 06/06/2018
     *
     * @return void
     */
    public function verDetalhes(int $tiposBrindesClienteId)
    {
        $tiposBrindesClientes = $this->TiposBrindesClientes->getTiposBrindesClientesById($tiposBrindesClienteId);

        $cliente = $this->Clientes->getClienteById($tiposBrindesClientes["clientes_id"]);

        // DebugUtil::print($tiposBrindesClientes);

        $arraySet = array(
            "tiposBrindesClientes",
            "cliente"
        );

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);
    }

    /**
     * Altera o estado de um Tipo de Brinde de Cliente
     *
     * @param  $query["tipos_brindes_cliente_id"]
     * @param  $query["clientes_id"]
     * @param  $query["estado"]
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date   2018/06/08
     *
     * @return void
     */
    public function alteraEstadoTiposBrindesCliente()
    {
        try {

            $query = $this->request->query;

            $tiposBrindesClienteId = $query["tipos_brindes_cliente_id"];
            $clientesId = $query["clientes_id"];
            $estado = $query["estado"];

            if ($this->TiposBrindesClientes->updateHabilitadoTiposBrindesCliente($tiposBrindesClienteId, $estado)) {

                $mensagemAviso = $estado ? __(Configure::read("messageEnableSuccess")) : __(Configure::read("messageDisableSuccess"));

                $this->Flash->success(__($mensagemAviso));

                return $this->redirect(array("controller" => "tipos_brindes_clientes", "action" => "tipos_brindes_cliente", $clientesId));
            }

            $mensagemErro = $estado ? __(Configure::read("messageEnableError")) : __(Configure::read("messageDisableError"));

            $this->Flash->error(__($mensagemErro));

            return $this->redirect(array("controller" => "tipos_brindes_clientes", "action" => "tipos_brindes_cliente", $clientesId));
        } catch (\Exception $e) {

            $messageString = __("Não foi possível alterar o estado de habilitado/desabilitado um Tipo de Brindes de Cliente!");

            $trace = $e->getTrace();
            $mensagem = array('status' => false, 'message' => $messageString, 'errors' => $trace);
            $messageStringDebug = __("{0} - {1} . [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $messageString, $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write("error", $messageStringDebug);
        }
    }
}
