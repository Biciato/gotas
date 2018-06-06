<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\Database\Exception;
use Cake\ORM\TableRegistry;

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
                    echo 'oi';
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
                $generoBrindesCliente = $this->GeneroBrindesClientes->patchEntity($generoBrindesCliente, $this->request->getData());
                if ($this->GeneroBrindesClientes->save($generoBrindesCliente)) {
                    $this->Flash->success(__('The genero brindes cliente has been saved.'));

                    return $this->redirect(['action' => 'index']);
                }
                $this->Flash->error(__('The genero brindes cliente could not be saved. Please, try again.'));
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
        $this->request->allowMethod(['post', 'delete']);
        $generoBrindesCliente = $this->GeneroBrindesClientes->get($id);
        if ($this->GeneroBrindesClientes->delete($generoBrindesCliente)) {
            $this->Flash->success(__('The genero brindes cliente has been deleted.'));
        } else {
            $this->Flash->error(__('The genero brindes cliente could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Métodos Internos
     */


}
