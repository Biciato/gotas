<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Custom\RTI\Security;
use App\Model\Entity;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\Routing\Router;
use \DateTime;
use App\Custom\RTI\DateTimeUtil;
use App\Custom\RTI\ImageUtil;
use App\Custom\RTI\FilesUtil;
use App\Custom\RTI\DebugUtil;

/**
 * Redes Controller
 *
 * @property \App\Model\Table\RedesTable $Redes
 *
 * @method \App\Model\Entity\Rede[] paginate($object = null, array $settings = [])
 */
class RedesController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        try {
            $conditions = [];

            if ($this->request->is(['post', 'put'])) {
                $data = $this->request->getData();

                array_push(
                    $conditions,
                    [
                        $data['opcoes'] . ' like' => '%' . $data['parametro'] . '%'
                    ]
                );
            }

            $redes = $this->Redes->getAllRedes('all', $conditions);

            $this->paginate($redes, ['limit' => 10]);

            $this->set(compact('redes'));
            $this->set('_serialize', ['redes']);
        } catch (\Exception $e) {

        }
    }

    /**
     * View method
     *
     * @param string|null $id Rede id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function verDetalhes($id = null)
    {
        try {

            $rede = $this->Redes->getRedeById($id);

            $imagem = strlen($rede->nome_img) > 0 ? Configure::read('imageNetworkPathRead') . $rede->nome_img : null;

            $redes_has_clientes = $this->RedesHasClientes->getRedesHasClientesByRedesId($id);

            $this->paginate($redes_has_clientes, ['limit' => 10]);

            $this->set(compact('rede', 'redes_has_clientes', 'imagem'));
            $this->set('_serialize', ['rede', 'redes_has_clientes', 'imagem']);

        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $message = __("Erro ao exibir detalhes de Rede : {0} em: {1} ", $e->getMessage(), $trace[1]);
            Log::write('error', $message);

            $this->Flash->error($message);

            return $this->redirect(
                [
                    'controller' => 'redes', 'action' => 'ver_detalhes', $id
                ]
            );
        }
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function adicionarRede()
    {
        try {
            $rede = $this->Redes->newEntity();
            if ($this->request->is('post')) {

                $data = $this->request->getData();

                if (strlen($data['crop-height']) > 0) {

                    // imagem já está no servidor, deve ser feito apenas o resize e mover ela da pasta temporária

                    // obtem dados de redimensionamento

                    $height = $data["crop-height"];
                    $width = $data["crop-width"];
                    $valueX = $data["crop-x1"];
                    $valueY = $data["crop-y1"];

                    $imagemOrigem = __("{0}{1}", Configure::read("imageNetworkPathTemp"), $data["img-upload"]);

                    $imagemDestino = __("{0}{1}", Configure::read("imageNetworkPath"), $data["img-upload"]);
                    $resizeSucesso = ImageUtil::resizeImage($imagemOrigem, 600, 600, $valueX, $valueY, $width, $height, 90);

                    // Se imagem foi redimensionada, move e atribui o nome para gravação
                    if ($resizeSucesso) {

                        rename($imagemOrigem, $imagemDestino);

                        $data["nome_img"] = $data["img-upload"];
                    }
                }

                $rede = $this->Redes->patchEntity($rede, $data);

                if ($this->Redes->addRede($rede)) {
                    $this->Flash->success(__(Configure::read('messageSavedSuccess')));

                    return $this->redirect(['action' => 'index']);
                }
                $this->Flash->error(__(Configure::read('messageSavedError')));
            }

            $this->set(compact('rede'));
            $this->set('_serialize', ['rede']);

        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $message = __("Erro ao adicionar nova rede: {0} em: {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $message);

            $this->Flash->error($message);

        }
    }

    /**
     * Edit method
     *
     * @param string|null $id Rede id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function editar($id = null)
    {
        try {

            $rede = $this->Redes->getRedeById($id);

            $imagemOriginal = null;

            if (strlen($rede->nome_img) > 0) {
                $imagemOriginal = __("{0}{1}", Configure::read("imageNetworkPath"), $rede->nome_img);
            }

            if ($this->request->is(['post', 'put'])) {

                $data = $this->request->getData();

                $trocaImagem = 0;

                if (strlen($data['crop-height']) > 0) {

                    // imagem já está no servidor, deve ser feito apenas o resize e mover ela da pasta temporária

                    // obtem dados de redimensionamento

                    $height = $data["crop-height"];
                    $width = $data["crop-width"];
                    $valueX = $data["crop-x1"];
                    $valueY = $data["crop-y1"];

                    $imagemOrigem = __("{0}{1}", Configure::read("imageNetworkPathTemp"), $data["img-upload"]);

                    $imagemDestino = __("{0}{1}", Configure::read("imageNetworkPath"), $data["img-upload"]);
                    // $resizeSucesso = ImageUtil::resizeImage($imagemOrigem, 600, 600, $valueX, $valueY, $width, $height, 90);
                    $resizeSucesso = ImageUtil::resizeImage($imagemOrigem, $width, $height, $valueX, $valueY, $width, $height, 90);

                    // Se imagem foi redimensionada, move e atribui o nome para gravação
                    if ($resizeSucesso == 1) {

                        rename($imagemOrigem, $imagemDestino);

                        $data["nome_img"] = $data["img-upload"];

                        $trocaImagem = 1;
                    }
                }

                $rede = $this->Redes->patchEntity($rede, $data);

                debug($rede->errors());
                if ($this->Redes->updateRede($rede)) {

                    if ($trocaImagem == 1 && !is_null($imagemOriginal)) {
                        unlink($imagemOriginal);
                    }

                    $this->Flash->success(__(Configure::read('messageSavedSuccess')));

                    return $this->redirect(['action' => 'index']);
                }
                $this->Flash->error(__(Configure::read('messageSavedError')));
            }

            $this->set(compact('rede', 'imagem'));
            $this->set('_serialize', ['rede', 'imagem']);

        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $message = __("Erro ao adicionar nova rede: {0} em: {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $message);

            $this->Flash->error($message);

        }
    }

    /**
     * Delete method
     *
     * @param string|null $id Rede id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete()
    {
        $query = $this->request->query;

        try {
            $this->request->allowMethod(['post', 'delete']);

            $rede_id = $query['rede_id'];

            $rede = $this->Redes->getRedeById($rede_id);

            $clientes_ids = [];
            $redes_has_clientes_ids = [];

            foreach ($rede->redes_has_clientes as $key => $rede_has_cliente) {
                $redes_has_clientes_ids[] = $rede_has_cliente->id;
                $clientes_ids[] = $rede_has_cliente->clientes_id;
            }

            if (sizeof($clientes_ids) > 0) {

                $this->PontuacoesPendentes
                    ->deleteAllPontuacoesPendentesByClienteIds($clientes_ids);
                $this->Pontuacoes->deleteAllPontuacoesByClientesIds($clientes_ids);
                $this->PontuacoesComprovantes->deleteAllPontuacoesComprovantesByClientesIds($clientes_ids);


            // gotas

                $this->Gotas->deleteAllGotasByClientesIds($clientes_ids);
                $this->Cupons->deleteAllCuponsByClientesIds($clientes_ids);

            // brindes

                $this->UsuariosHasBrindes->deleteAllUsuariosHasBrindesByClientesIds($clientes_ids);
                $this->ClientesHasBrindesEstoque->deleteAllClientesHasBrindesEstoqueByClientesIds($clientes_ids);
                $this->ClientesHasBrindesHabilitadosPreco->deleteAllClientesHasBrindesHabilitadosPrecoByClientesIds($clientes_ids);
                $this->ClientesHasBrindesHabilitados->deleteAllClientesHasBrindesHabilitadosByClientesIds($clientes_ids);
                $this->Brindes->deleteAllBrindesByClientesIds($clientes_ids);

            // apagar os usuários que são da rede (Administradores da Rede até funcionários)

                $whereConditions = [];

                $whereConditions[] = ['tipo_perfil >= ' => Configure::read('profileTypes')['AdminNetworkProfileType']];
                $whereConditions[] = ['tipo_perfil <= ' => Configure::read('profileTypes')['WorkerProfileType']];

                $this->Usuarios->deleteAllUsuariosByClienteIds(
                    $clientes_ids,
                    $whereConditions
                );

                // Não apaga os usuários que estão vinculados, mas remove o vínculo

                $this->ClientesHasUsuarios->deleteAllClientesHasUsuariosByClientesIds($clientes_ids);
            }

            if (sizeof($redes_has_clientes_ids) > 0) {
                // Remove os Administradores da Rede
                $this->RedesHasClientesAdministradores->deleteAllRedesHasClientesAdministradoresByClientesIds($redes_has_clientes_ids);
            }

            if (sizeof($clientes_ids) > 0) {

                // Remove a unidade de rede
                $this->RedesHasClientes->deleteRedesHasClientesByClientesIds($clientes_ids);

                $this->Clientes->deleteClientesByIds($clientes_ids);
            }

            // remove a rede
            $this->Redes->deleteRedesById($rede->id);

            return $this->redirect($query['return_url']);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao remover rede: {0} em: {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Ativa um registro
     *
     * @return boolean
     */
    public function ativar()
    {
        $query = $this->request->query();

        $this->_alteraEstadoRede((int)$query['rede_id'], true, $query['return_url']);
    }

    /**
     * Desativa um registro
     *
     * @return boolean
     */
    public function desativar()
    {
        $query = $this->request->query();

        $this->_alteraEstadoRede((int)$query['rede_id'], false, $query['return_url']);
    }

    /**
     * Altera estado de uma rede
     *
     * @param int   $rede_id    Id da Rede
     * @param bool  $estado     Estado
     * @param array $return_url Url de Retorno
     *
     * @return void
     */
    private function _alteraEstadoRede(int $rede_id, bool $estado, array $return_url)
    {
        try {

            $this->request->allowMethod(['post']);

            $result = $this->Redes->changeStateEnabledRede($rede_id, $estado);

            if ($result) {
                if ($estado) {
                    $this->Flash->success(__(Configure::read('messageEnableSuccess')));
                } else {
                    $this->Flash->success(__(Configure::read('messageDisableSuccess')));
                }
            } else {
                if ($estado) {
                    $this->Flash->success(__(Configure::read('messageEnableError')));
                } else {
                    $this->Flash->success(__(Configure::read('messageDisableError')));
                }
            }

            return $this->redirect($return_url);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao realizar procedimento de alteração de estado de cliente: {0} em: {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Configura propaganda para a rede do administrtador
     *
     * @return void
     */
    public function configurarPropaganda()
    {
        try {
            $user_admin = $this->request->session()->read('User.RootLogged');
            $user_managed = $this->request->session()->read('User.ToManage');

            if ($user_admin) {
                $this->user_logged = $user_managed;
            }

            // Se usuário não tem acesso, redireciona
            if (!$this->security_util->checkUserIsAuthorized($this->user_logged, "AdminNetworkProfileType", "AdminRegionalProfileType")) {
                $this->security_util->redirectUserNotAuthorized($this);
            }
            $rede = $this->request->session()->read('Network.Main');

            $rede = $this->Redes->getRedeById($rede["id"]);

            $imagem = __("{0}{1}{2}", Configure::read("webrootAddress"), Configure::read("imageClientPathRead") , $rede["propaganda_img"]);

            $imagemOriginal = null;

            if (strlen($rede["propaganda_img"]) > 0) {
                // O caminho tem que ser pelo cliente, pois a mesma imagem será usada para todas as unidades
                $imagemOriginal = __("{0}{1}", Configure::read("imageClientPath"), $rede["propaganda_img"]);
            }

            if ($this->request->is(['post', 'put'])) {

                $data = $this->request->getData();

                $trocaImagem = 0;

                if (strlen($data['crop-height']) > 0) {

                    // imagem já está no servidor, deve ser feito apenas o resize e mover ela da pasta temporária
                    // obtem dados de redimensionamento

                    $height = $data["crop-height"];
                    $width = $data["crop-width"];
                    $valueX = $data["crop-x1"];
                    $valueY = $data["crop-y1"];

                    $propagandaLink = $data["propaganda_link"];
                    $propagandaImg = $data["img-upload"];

                    $imagemOrigem = __("{0}{1}", Configure::read("imageClientPathTemp"), $data["img-upload"]);

                    $imagemDestino = __("{0}{1}", Configure::read("imageClientPath"), $data["img-upload"]);
                    $resizeSucesso = ImageUtil::resizeImage($imagemOrigem, 600, 600, $valueX, $valueY, $width, $height, 90);

                    // Se imagem foi redimensionada, move e atribui o nome para gravação
                    if ($resizeSucesso == 1) {
                        rename($imagemOrigem, $imagemDestino);
                        $data["propaganda_img"] = $data["img-upload"];

                        $trocaImagem = 1;
                    }
                }

                $rede = $this->Redes->patchEntity($rede, $data);

                if ($this->Redes->updateRede($rede)) {

                    if ($trocaImagem == 1 && !is_null($imagemOriginal)) {
                        unlink($imagemOriginal);
                    }

                    // atualiza todas as unidades de atendimento
                    $clientesIds = $this->RedesHasClientes->getClientesIdsFromRedesHasClientes($rede["id"]);

                    $arrayUpdate = array();
                    foreach ($clientesIds as $cliente) {
                        $itemUpdate = array(
                            "propaganda_link" => $propagandaLink,
                            "propaganda_img" => $propagandaImg,
                        );

                        $this->Clientes->updateAll($itemUpdate, array("id" => $cliente));
                    }

                    $this->Flash->success(__(Configure::read('messageSavedSuccess')));

                    return $this->redirect(
                        array(
                            "controller" => "RedesHasClientes", 'action' => 'propagandaEscolhaUnidades'
                        )
                    );
                }
                $this->Flash->error(__(Configure::read('messageSavedError')));
            }

            $arraySet = array(
                "rede",
                "imagem"
            );

            $this->set(compact($arraySet));
            $this->set("_serialize", $arraySet);

        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $messageString = __("Não foi possível obter dados de Pontos de Atendimento!");

            $messageStringDebug =
                __("{0} - {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $messageString, $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write("error", $messageStringDebug);
            Log::write("error", $trace);
        }
    }

    /**
     * ------------------------------------------------------------
     * Métodos para dashboard de cliente final
     * ------------------------------------------------------------
     */

    /**
     * Exibe as unidades de uma rede para o cliente realizar o resgate de um brinde
     *
     * @param integer $redes_id Id da rede
     *
     * @return void
     */
    public function escolherUnidadeRede(int $redes_id)
    {
        $rede = $this->Redes->getRedeById($redes_id);

        $unidadesIds = [];

        $this->set(compact('rede'));
        $this->set('_serialize', ['rede']);
    }

    /**
     * ------------------------------------------------------------
     * Relatórios de Admin RTI
     * ------------------------------------------------------------
     */

    /**
     * Exibe action de Relatório de Redes
     *
     * @return \Cake\Http\Response|void
     */
    public function relatorioRedes()
    {
        $redes = $this->Redes->getAllRedes('all');

        $qteRegistros = 10;

        if ($this->request->is(['post'])) {
            $data = $this->request->getData();

            $whereConditions = array();

            // Nome da Rede
            if (strlen($data['nome_rede']) > 0) {
                $whereConditions[] = [$data['opcoes'] . ' like "%' . $data["parametro"] . '%"'];
            }

            // Registros Ativados no Sistema?
            if (strlen($data['ativado']) > 0) {
                $whereConditions[] = ['ativado' => $data['ativado']];
            }

            // Consumo Gotas de Funcionários?
            if (strlen($data['permite_consumo_gotas_funcionarios']) > 0) {
                $whereConditions[] = ['permite_consumo_gotas_funcionarios' => $data['permite_consumo_gotas_funcionarios']];
            }

            // Qte. de Registros
            $qteRegistros = $data['qteRegistros'];

            $dataHoje = DateTimeUtil::convertDateToUTC((new DateTime('now'))->format('Y-m-d H:i:s'));
            $dataInicial = strlen($data['auditInsertInicio']) > 0 ? DateTimeUtil::convertDateToUTC($data['auditInsertInicio'], 'd/m/Y') : null;
            $dataFinal = strlen($data['auditInsertFim']) > 0 ? DateTimeUtil::convertDateToUTC($data['auditInsertFim'], 'd/m/Y') : null;

            // Data de Criação Início e Fim
            if (strlen($data['auditInsertInicio']) > 0 && strlen($data['auditInsertFim']) > 0) {

                if ($dataInicial > $dataFinal) {
                    $this->Flash->error(__(Configure::read('messageDateRangeInvalid')));
                } else if ($dataInicial > $dataHoje) {
                    $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid', 'Data de Início')));
                } else {
                    $whereConditions[] = ['audit_insert BETWEEN "' . $dataInicial . '" AND "' . $dataFinal . '"'];

                }

            } else if (strlen($data['auditInsertInicio']) > 0) {

                if ($dataInicial > $dataHoje) {
                    $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Início'));
                } else {
                    $whereConditions[] = ['audit_insert >= ' => $dataInicial];
                }

            } else if (strlen($data['auditInsertFim']) > 0) {
                if ($dataFinal > $dataHoje) {
                    $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Fim'));
                } else {
                    $whereConditions[] = ['audit_insert <= ' => $dataFinal];
                }
            }


            if (sizeof($whereConditions) > 0) {
                $redes = $redes->where($whereConditions);
            }
        }

        $redes = $this->paginate($redes, ['limit' => $qteRegistros]);

        $array_set = [
            'redes'
        ];

        $this->set(compact([$array_set]));

        $this->set('_serialize', [$array_set]);
    }

    /**
     * ------------------------------------------------------------------
     * Métodos JSON
     * ------------------------------------------------------------------
     */

    /**
     * RedesController::enviaImagemRede
     *
     * Envia imagem de rede de forma assíncrona
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 26/05/2018
     *
     * @return json_object
     */
    public function enviaImagemRede()
    {
        $mensagem = null;
        $status = false;
        $message = __("Erro durante o envio da imagem. Tente novamente!");

        $arquivos = array();
        try {
            if ($this->request->is('post')) {

                $data = $this->request->getData();

                $arquivos = FilesUtil::uploadFiles(Configure::read("imageNetworkPathTemp"));

                $status = true;
                $message = __("Envio concluído com sucesso!");
            }
        } catch (\Exception $e) {
            $messageString = __("Não foi possível enviar imagem de rede!");
            $trace = $e->getTrace();
            $mensagem = array('status' => false, 'message' => $messageString, 'errors' => $trace);
            $messageStringDebug = __("{0} - {1} em: {2}. [Função: {3} / Arquivo: {4} / Linha: {5}]  ", $messageString, $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write("error", $messageStringDebug);
        }

        $mensagem = array("status" => true, "message" => null);

        $result = array("mensagem" => $mensagem, "arquivos" => $arquivos);

        // echo json_encode($result);
        $arraySet = array(
            "arquivos",
            "mensagem"
        );

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);
    }

    /**
     * ------------------------------------------------------------------
     * Métodos de API
     * ------------------------------------------------------------------
     */

    /**
     * RedesController::getRedesAPI
     *
     * Obtem as redes que o usuário possui vínculo
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date   13/05/2018
     *
     * @return json object
     */
    public function getRedesAPI()
    {
        $messageString = null;

        $mensagem = [];

        try {
            if ($this->request->is(['post', 'put'])) {
                $data = $this->request->getData();

                $nomeRede = isset($data["nome_rede"]) && strlen($data["nome_rede"]) > 0 ? $data["nome_rede"] : null;

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

                $usuario = $this->Auth->user();

                // id do usuário
                $usuariosId = $usuario['id'];

                // localiza quais as unidades o usuário tem pontuacao

                $unidadesIdsQuery = $this->PontuacoesComprovantes->getAllClientesIdFromCoupons(['usuarios_id' => $usuariosId]);

                $unidadesIds = [];

                foreach ($unidadesIdsQuery->toArray() as $key => $value) {
                    $unidadesIds[] = $value->clientes_id;
                }

                if (count($unidadesIds) == 0) {
                    $status = 0;
                    $messageString = Configure::read("messageLoadDataWithError");
                    $errors = array("Para utilizar seus pontos é necessário primeiramente realizar um abastecimento em algum Posto credenciado ao sistema!");

                    $mensagem = array(
                        "status" => $status,
                        "message" => $messageString,
                        "errors" => $errors
                    );

                    $arraySet = array("mensagem", "redes");
                    $this->set(compact($arraySet));

                    $this->set("_serialize", $arraySet);
                }

                // obtem o id de redes através dos ids de clientes, de forma distinta

                $redes_array = [];
                if (sizeof($unidadesIds) > 0) {
                    $redes_array = $this->RedesHasClientes->getRedesHasClientesByClientesIds($unidadesIds);

                    $redes_array = $redes_array->toArray();
                }


                $redes_ids = [];

                foreach ($redes_array as $key => $value) {
                    $redes_ids[] = $value->redes_id;
                }

                /* agora tenho o id das redes que o usuário está vinculado.
                 * Pegar informações de cada rede, total de
                 * pontos acumulados, e brindes fornecidos
                 */

                $redes = [];

                if (count($redes_ids) == 0) {
                    $status = 0;
                    $messageString = Configure::read("messageLoadDataWithError");
                    $errors = array("Para utilizar seus pontos é necessário primeiramente realizar um abastecimento em algum Posto credenciado ao sistema!");
                } else {

                    $redesQueryResult = $this->Redes->getRedes(
                        array("Redes.id in " => $redes_ids, "Redes.nome_rede like '%{$nomeRede}%'"),
                        array(
                            "id",
                            "nome_rede",
                            "nome_img",
                            "ativado",
                            "propaganda_img",
                            "propaganda_link"
                        ),
                        array(),
                        $orderConditions,
                        $paginationConditions
                    );

                    // DebugUtil::printArray($redesQueryResult);

                    $redesData = $redesQueryResult["redes"]["data"];

                    $redes = array();

                    // $redes["count"] = $redesQueryResult["count"];

                    foreach ($redesData as $key => $rede) {

                        // Obtem todos os ids de clientes para consultar, no agrupamento da rede, o total de pontos do usuário logado

                        $clientesIdsQuery = $this->RedesHasClientes->getAllRedesHasClientesIdsByRedesId($rede->id);

                        $clientesIds = array();

                        foreach ($clientesIdsQuery as $key => $clientesIdsItem) {
                            $clientesIds[] = $clientesIdsItem->cliente_id;
                        }
                        $soma_pontos = $this->Pontuacoes->getSumPontuacoesOfUsuario($usuariosId, $rede["id"], $unidadesIds);

                        $rede['soma_pontos'] = $soma_pontos;

                        $redes["data"][] = $rede;
                    }

                    if (sizeof($redesData) == 0) {
                        $redes["data"] = array();
                    }

                    $redes["count"] = $redesQueryResult["redes"]["count"];
                    $redes["page_count"] = $redesQueryResult["redes"]["page_count"];
                    $mensagem = $redesQueryResult["mensagem"];
                }

            }

        } catch (\Exception $e) {
            $messageString = __("Não foi possível obter dados de Redes e Pontuações!");
            $trace = $e->getTrace();
            $mensagem = array('status' => 0, 'message' => $messageString, 'errors' => $trace);
            $messageStringDebug = __("{0} - {1} em: {2}. [Função: {3} / Arquivo: {4} / Linha: {5}]  ", $messageString, $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write("error", $messageStringDebug);
        }

        $arraySet = [
            'redes',
            'mensagem'
        ];

        $this->set(compact($arraySet));
        $this->set('_serialize', $arraySet);
    }

    /**
     * ------------------------------------------------------------
     * Métodos Comuns
     * ------------------------------------------------------------
     */

    /**
     * BeforeRender callback
     *
     * @param Event $event Evento
     *
     * @return \Cake\Http\Response|void
     */
    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);

        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        }
    }

    /**
     * Before render callback.
     *
     * @param \App\Controller\Event\Event $event The beforeRender event.
     *
     * @return \Cake\Network\Response|null|void
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        $this->Auth->allow(['getNetworkDetails', 'enviaImagemRede']);
    }

    /**
     * Initialize function
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->user_logged = $this->getUserLogged();
        $this->set('user_logged', $this->getUserLogged());
    }

    /**
     * ------------------------------------------------------------
     * Ajax Methods
     * ------------------------------------------------------------
     */

    /**
     * Obtem detalhes da rede
     *
     * @return void
     */
    public function getNetworkDetails()
    {
        if ($this->request->is('post')) {

            $data = $this->request->getData();

            $rede = $this->Redes->getRedeById($data['redes_id']);

            $rede->nome_img = strlen($rede->nome_img) > 0 ? Configure::read('imageNetworkPathRead') . $rede->nome_img : null;

            $unidadesIds = [];

            // obtem os ids das unidades para saber quais brindes estão disponíveis
            foreach ($rede->redes_has_clientes as $key => $value) {
                $unidadesIds[] = $value->clientes_id;
            }

            $brindes = $this->Brindes->getBrindesByClientes($unidadesIds);

            $rede['brindes'] = $brindes;

            $arraySet = ['rede'];

            $this->set(compact($arraySet));
            $this->set("_serialize", $arraySet);
        }
    }
}
