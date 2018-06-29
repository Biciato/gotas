<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;
use App\Custom\RTI\DebugUtil;

/**
 * GeneroBrindes Controller
 *
 * @property \App\Model\Table\GeneroBrindesTable $GeneroBrindes
 *
 * @method \App\Model\Entity\GeneroBrinde[] paginate($object = null, array $settings = [])
 */
class GeneroBrindesController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $qteRegistros = 999;
        $whereConditions = array();

        if ($this->request->is("post")) {
            $data = $this->request->getData();

            // Nome do gênero
            if ((!empty($data["nome"]) && isset($data["nome"])) && strlen($data["nome"]) > 0) {
                $whereConditions[] = ["nome like '%" . $data["nome"] . "%'"];
            }

            /**
             * Se é equipamento RTI (Leitora)
             * Se for: Lógica da RTI
             * Se não for: Lógica padrão Developer
             *
             */
            if ((!empty($data["equipamento_rti"]) && isset($data["equipamento_rti"]))) {
                $whereConditions[] = ["equipamento_rti" => $data["equipamento_rti"]];
            }

            // Brindes Necessidades Especiais
            if (!empty($data["brinde_necessidades_especiais"]) && isset($data["brinde_necessidades_especiais"])) {
                $whereConditions[] = ["brinde_necessidades_especiais" => $data["brinde_necessidades_especiais"]];
            }

            // Habilitado
            if (!empty($data["habilitado"]) && isset($data["habilitado"])) {
                $whereConditions[] = ["habilitado" => $data["habilitado"]];
            }

            // Atribuir automaticamente
            if (!empty($data["atribuir_automatico"]) && isset($data["atribuir_automatico"])) {
                $whereConditions[] = ["atribuir_automatico" => $data["atribuir_automatico"]];
            }

             // Qte. de Registros
            $qteRegistros = $data['qteRegistros'];
        }

        $generoBrindes = $this->GeneroBrindes->findGeneroBrindes($whereConditions);

        $generoBrindes = $this->paginate($generoBrindes, ["limit" => $qteRegistros]);

        $this->set(compact('generoBrindes'));
        $this->set('_serialize', ['generoBrindes']);
    }

    /**
     * View method
     *
     * @param string|null $id Genero Brinde id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function verDetalhes($id = null)
    {
        // $generoBrinde = $this->GeneroBrindes->get($id, [
        //     'contain' => ['Clientes']
        // ]);
        $generoBrinde = $this->GeneroBrindes->get($id);

        $this->set('generoBrinde', $generoBrinde);
        $this->set('_serialize', ['generoBrinde']);
    }

    /**
     * GeneroBrindesController::adicionarGeneroBrinde
     *
     * Método de adicionar Gênero de Brinde
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 31/05/2018
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function adicionarGeneroBrinde()
    {
        try {
            $generoBrinde = $this->GeneroBrindes->newEntity();

            if ($this->request->is('post')) {

                $data = $this->request->getData();

                // Verifica se é automático ou não. Se não for automático, não precisa guardar o tipo

                if (!$data["atribuir_automatico"]) {
                    $data["tipo_principal_codigo_brinde_default"] = null;
                    $data["tipo_secundario_codigo_brinde_default"] = null;
                }

                // Valida se o tipo é menor que 4 pois este já é default SMART Shower
                if ($data["atribuir_automatico"] && $data["tipo_principal_codigo_brinde_default"] <= 4) {
                    $this->Flash->error(__("O Tipo Principal de Código Brinde é reservado de 1 a 4 para SMART Shower, selecione outro valor para continuar!"));
                } else {

                    /**
                     * Valida se há outro gênero com mesmo nome
                     * e se também é brinde de Nec. Especiais
                     */
                    $whereConditions = array();

                    $whereConditions[] = [
                        "nome" => $data["nome"],
                        "equipamento_rti" => $data["equipamento_rti"],
                        "brinde_necessidades_especiais" => $data["brinde_necessidades_especiais"],
                        "atribuir_automatico" => $data["atribuir_automatico"],
                    ];

                    $generoBrindeEncontrado = $this->GeneroBrindes->findGeneroBrindes($whereConditions, 1);

                    // se for mesmas condições, impede
                    if ($generoBrindeEncontrado) {
                        $this->Flash->error(Configure::read("messageRecordExistsSameCharacteristics"));

                        $arraySet = [
                            "generoBrinde"
                        ];

                        $this->set(compact($arraySet));
                        $this->set('_serialize', $arraySet);

                        return;
                    }

                    $generoBrinde = $this->GeneroBrindes->patchEntity($generoBrinde, $data);
                    if ($this->GeneroBrindes->saveGeneroBrindes($generoBrinde->toArray())) {
                        $this->Flash->success(__(Configure::read("messageSavedSuccess")));

                        return $this->redirect(['action' => 'index']);
                    }
                    $this->Flash->error(__(Configure::read("messageSavedError")));

                    Log::write("error", $generoBrinde);

                }

            }
            $arraySet = [
                "generoBrinde"
            ];

            $this->set(compact($arraySet));
            $this->set('_serialize', $arraySet);
        } catch (\Exception $e) {
            $messageString = __("Não foi possível adicionar um Gênero de Brindes!");

            $trace = $e->getTrace();
            $mensagem = array('status' => false, 'message' => $messageString, 'errors' => $trace);
            $messageStringDebug = __("{0} - {1} . [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $messageString, $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            $this->Flash->error($mensagem);
            Log::write("error", $messageStringDebug);
            Log::write("error", $trace);
        }
    }

    /**
     * GeneroBrindesController::editarGeneroBrinde
     *
     * Método de editar Gênero de Brinde
     *
     * @param string|null $id Genero Brinde id.
     *
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 31/05/2018
     */
    public function editarGeneroBrinde($id = null)
    {
        try {


            $generoBrinde = $this->GeneroBrindes->get($id, [
                'contain' => ['Clientes']
            ]);
            if ($this->request->is(['patch', 'post', 'put'])) {

                $data = $this->request->getData();

            // Valida se o tipo é menor que 4 pois este já é default SMART Shower
                if ($data["tipo_principal_codigo_brinde_default"] <= 4) {
                    $this->Flash->error(__("O Tipo Principal de Código Brinde é reservado de 1 a 4 para SMART Shower, selecione outro valor para continuar!"));
                } else {

                    /**
                     * Valida se há outro gênero com mesmo nome
                     * e se também é brinde de Nec. Especiais
                     */

                    $whereConditions = array();

                    $whereConditions[] = [
                        "id != " => $id,
                        "nome" => $data["nome"],
                        "equipamento_rti" => $data["equipamento_rti"],
                        "brinde_necessidades_especiais" => $data["brinde_necessidades_especiais"],
                        "atribuir_automatico" => $data["atribuir_automatico"],
                    ];

                    $generoBrindeEncontrado = $this->GeneroBrindes->findGeneroBrindes($whereConditions, 1);

                // se for mesmas condições, impede
                    if ($generoBrindeEncontrado) {
                        $this->Flash->error(Configure::read("messageRecordExistsSameCharacteristics"));

                        $arraySet = [
                            "generoBrinde"
                        ];

                        $this->set(compact($arraySet));
                        $this->set('_serialize', $arraySet);

                        return;
                    }

                    $generoBrinde = $this->GeneroBrindes->patchEntity($generoBrinde, $data);
                    if ($this->GeneroBrindes->saveGeneroBrindes($generoBrinde->toArray())) {
                        $this->Flash->success(__(Configure::read("messageSavedSuccess")));

                        return $this->redirect(['action' => 'index']);
                    }
                    $this->Flash->error(__(Configure::read("messageSavedError")));
                }
            }

            $arraySet = [
                "generoBrinde"
            ];

            $this->set(compact($arraySet));
            $this->set('_serialize', $arraySet);
        } catch (\Exception $e) {
            $messageString = __("Não foi possível editar um Gênero de Brindes!");

            $trace = $e->getTrace();
            $mensagem = array('status' => false, 'message' => $messageString, 'errors' => $trace);
            $messageStringDebug = __("{0} - {1} . [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $messageString, $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            $this->Flash->error($mensagem);
            Log::write("error", $messageStringDebug);
            Log::write("error", $trace);
        }
    }

    /**
     * GeneroBrindesController::delete
     *
     * Método de remover Gênero de Brinde
     *
     * @param string|null $id Genero Brinde id.
     *
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 31/05/2018
     */
    public function delete($id = null)
    {
        try {
            $this->request->allowMethod(['post', 'delete']);

            $data = $this->request->query();

            $cliente_id = $data['genero_brindes_id'];
            $return_url = $data['return_url'];

            $generoBrinde = $this->GeneroBrindes->get($id);

            if ($this->GeneroBrindes->delete($generoBrinde)) {
                $this->Flash->success(__(Configure::read("messageDeleteSuccess")));
            } else {
                $this->Flash->error(__(Configure::read("messageDeleteError")));
            }

            return $this->redirect(['action' => 'index']);
        } catch (\Exception $e) {
            $messageString = __("Não foi possível deletar um Gênero de Brindes!");

            $trace = $e->getTrace();
            $mensagem = array('status' => false, 'message' => $messageString, 'errors' => $trace);
            $messageStringDebug = __("{0} - {1} . [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $messageString, $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            $this->Flash->error($mensagem);
            Log::write("error", $messageStringDebug);
            Log::write("error", $trace);

            return $this->redirect(['action' => 'index']);

        }
    }

    /**
     * --------------------------------------------------------------------------------
     * Métodos de Serviços REST
     * --------------------------------------------------------------------------------
     */

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

                $whereConditions = array();
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

                if (($redesId == null) && ($clientesId == null)) {

                    $messageString = __("É necessário informar uma rede ou um posto de atendimento para continuar!");

                } else {

                    $clientesIds = array();
                    if (!empty($redesId)) {
                        // Pesquisa pelo id da rede
                        $clientesIds = $this->RedesHasClientes->getClientesIdsFromRedesHasClientes($redesId);
                    } else if (!empty($clientesId)) {
                        // Pesquisa pelo id da rede
                        $clientesIds[] = $clientesId;
                    }
                    // Com a lista de Clientes Ids obtida, faz a pesquisa

                    $generoBrindesIds = $this->GeneroBrindesClientes->findGeneroBrindesClienteByClientesIds($clientesIds);

                    $generoBrindesQuery = $this->GeneroBrindes->findGeneroBrindesByIds($generoBrindesIds, $orderConditions, $paginationConditions);

                    $genero_brindes = array("count" => $generoBrindesQuery["count"], "data" => $generoBrindesQuery["data"]->toArray());
                }
                $mensagem = ['status' => true, 'message' => $messageString];
            }

        } catch (\Exception $e) {
            $messageString = __("Não foi possível obter dados de Gênero de Brindes do Cliente!");
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
