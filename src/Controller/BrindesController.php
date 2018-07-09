<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Log\Log;
use Cake\Routing\Router;
use Cake\Mailer\Email;
use Cake\View\Helper\UrlHelper;
use \DateTime;
use App\Custom\RTI\Security;
use App\Custom\RTI\DateTimeUtil;
use App\Custom\RTI\ImageUtil;
use App\Custom\RTI\FilesUtil;
use App\Custom\RTI\DebugUtil;

/**
 * Brindes Controller
 *
 * @property \App\Model\Table\BrindesTable $Brindes
 *
 * @method \App\Model\Entity\Brinde[] paginate($object = null, array $settings = [])
 */
class BrindesController extends AppController
{
    /**
     * ------------------------------------------------------------
     * Campos
     * ------------------------------------------------------------
     */
    protected $user_logged = null;

    /**
     * ------------------------------------------------------------
     * Métodos Comuns
     * ------------------------------------------------------------
     */

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Clientes']
        ];
        $brindes = $this->paginate($this->Brindes);

        $this->set(compact('brindes'));
        $this->set('_serialize', ['brindes']);
    }

    /**
     * View method
     *
     * @param string|null $id Brinde id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $brinde = $this->Brindes->get(
            $id,
            [
                'contain' => ['Clientes']
            ]
        );

        $this->set('brinde', $brinde);
        $this->set('_serialize', ['brinde']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $brinde = $this->Brindes->newEntity();
        if ($this->request->is('post')) {
            $brinde = $this->Brindes->patchEntity($brinde, $this->request->getData());
            if ($this->Brindes->save($brinde)) {
                $this->Flash->success(__('The brinde has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The brinde could not be saved. Please, try again.'));
        }
        $clientes = $this->Brindes->Clientes->find('list', ['limit' => 200]);
        $this->set(compact('brinde', 'clientes'));
        $this->set('_serialize', ['brinde']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Brinde id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $brinde = $this->Brindes->get(
            $id,
            [
                'contain' => []
            ]
        );

        if ($this->request->is(['patch', 'post', 'put'])) {
            $brinde = $this->Brindes->patchEntity($brinde, $this->request->getData());
            if ($this->Brindes->save($brinde)) {
                $this->Flash->success(__('The brinde has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The brinde could not be saved. Please, try again.'));
        }
        $clientes = $this->Brindes->Clientes->find('list', ['limit' => 200]);
        $this->set(compact('brinde', 'clientes'));
        $this->set('_serialize', ['brinde']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Brinde id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $brinde = $this->Brindes->get($id);
        if ($this->Brindes->delete($brinde)) {
            $this->Flash->success(__('The brinde has been deleted.'));
        } else {
            $this->Flash->error(__('The brinde could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Método para brindes da rede (mostra os brindes que a rede possui)
     *
     * @return \Cake\Http\Response|void
     */
    public function brindesMinhaRede($param = null)
    {
        $user_admin = $this->request->session()->read('User.RootLogged');
        $user_managed = $this->request->session()->read('User.ToManage');

        if ($user_admin) {
            $this->user_logged = $user_managed;
        }

        $rede = $this->request->session()->read('Network.Main');

        $clientes_ids = [];

        // pega a matriz da rede

        $redes_has_clientes = $this->RedesHasClientes->findMatrizOfRedesByRedesId($rede->id);
        $unidadesIds = $redes_has_clientes->clientes_id;

        $conditions = [];

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();

            if (strlen($data['parametro']) > 0) {
                if ($data['opcoes'] == 'nome') {
                    array_push($conditions, ['nome like' => '%' . $data['parametro'] . '%']);
                } else {
                    array_push($conditions, ['preco_padrao' => $data['parametro']]);
                }
            }

        }

        array_push($conditions, ['clientes_id ' => $unidadesIds]);

        $brindes = $this->Brindes->findBrindes($conditions);

        $brindes = $this->paginate($brindes, ['limit' => 10]);

        $unidadesIds = $this->Clientes->getClientesListByRedesId($rede["id"])->toArray();

        $this->set(compact(['brindes', 'unidadesIds', 'unidade']));
        $this->set('_serialize', ['brindes', 'clientes_id', 'unidade']);
    }

    /**
     * Metodo par ver os detalhes de um brinde da rede
     *
     * @param string|null $id Brinde id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function verBrindeRede($id = null)
    {

        try {

            $user_admin = $this->request->session()->read('User.RootLogged');
            $user_managed = $this->request->session()->read('User.ToManage');
            $rede = $this->request->session()->read('Network.Main');

            if ($user_admin) {
                $this->user_logged = $user_managed;
            }

            $cliente = $this->security_util->checkUserIsClienteRouteAllowed(
                $this->user_logged,
                $this->Clientes,
                $this->ClientesHasUsuarios,
                array(),
                $rede["id"]
            );

            $brinde = $this->Brindes->get($id);

            if (is_null($brinde) && !isset($brinde)) {
                throw new \Exception("Brinde não encontrado!");

                return $this->redirect(array("controller" => "Brindes", "action" => "brindes_minha_rede", $rede["id"]));
            }

            $brinde["nome_img"] = $brinde["nome_img"] ? __("{0}{1}{2}{3}", Configure::read("appAddress"), "webroot", Configure::read("imageGiftPathRead"), $brinde["nome_img"]) : null;

            $arraySet = array(
                "brinde",
                "cliente"
            );

            $this->set(compact($arraySet));
            $this->set('_serialize', $arraySet);
        } catch (\Exception $e) {
            $this->Flash->error("Houve um erro: " . $e->getMessage());
            return $this->redirect(array("controller" => "Brindes", "action" => "brindes_minha_rede", $rede["id"]));

        }
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function adicionarBrindeRede()
    {
        $editMode = 0;

        try {
            $rede = $this->request->session()->read("Network.Main");
            $user_admin = $this->request->session()->read('User.RootLogged');
            $user_managed = $this->request->session()->read('User.ToManage');

            if ($user_admin) {
                $this->user_logged = $user_managed;
            }

            $clientesId = $this->RedesHasClientes->getClientesIdsFromRedesHasClientes($rede["id"]);

            // verifica se usuário é pelo menos administrador.

            if ($this->user_logged['tipo_perfil'] > Configure::read('profileTypes')['AdminLocalProfileType']) {
                $this->security_util->redirectUserNotAuthorized($this);
            }
            // Verifica permissão do usuário na rede / unidade da rede

            $temAcesso = $this->security_util->checkUserIsClienteRouteAllowed($this->user_logged, $this->Clientes, $this->ClientesHasUsuarios, $clientesId, $rede["id"]);

            // Se não tem acesso, redireciona
            if (!$temAcesso) {
                return $this->security_util->redirectUserNotAuthorized($this, $this->user_logged);
            }

            $brinde = $this->Brindes->newEntity();

            $generoBrindesCliente = $this->GeneroBrindesClientes->getGenerosBrindesClientesVinculados($clientesId);

            if (strlen($brinde->nome_img) > 0) {
                $imagemOriginal = __("{0}{1}", Configure::read("imageGiftPath"), $brinde->nome_img);
            }

            if ($this->request->is('post')) {
                $data = $this->request->getData();
                $brinde = $this->Brindes->patchEntity($brinde, $this->request->getData());

                $brinde->preco_padrao = str_replace(",", "", $this->request->getData()['preco_padrao']);

                if ($this->Brindes->findBrindesByName($brinde['nome'])) {
                    $this->Flash->warning(__('Já existe um registro com o nome {0}', $brinde['nome']));
                } else {

                    $enviouNovaImagem = isset($data["img-upload"]) && strlen($data["img-upload"]) > 0;

                    if ($enviouNovaImagem) {
                        $brinde["nome_img"] = $this->_preparaImagemBrindeParaGravacao($data);
                    }

                    $brinde["clientes_id"] = $clientesId;

                    $brinde = $this->Brindes->save($brinde);
                    if ($brinde) {
                        // habilita brinde para venda em uma rede/loja
                        $clienteHasBrindeHabilitado
                            = $this->ClientesHasBrindesHabilitados->addClienteHasBrindeHabilitado(
                            $clientesId,
                            $brinde->id
                        );

                        /* estoque só deve ser criado nas seguintes situações.
                         * 1 - O Brinde está sendo vinculado a um cadastro de loja
                         *  no sistema (Isto é, se ele não foi anteriormente )
                         * 2 - Não é ilimitado
                         * 3 - Se não houver cadastro anterior
                         */

                        if (!$brinde->ilimitado) {
                            $estoque = $this->ClientesHasBrindesEstoque
                                ->getEstoqueForBrindeId(
                                    $clienteHasBrindeHabilitado->id,
                                    0
                                );

                            if (is_null($estoque)) {
                                // Não tem estoque, criar novo registro vazio
                                $result
                                    = $this->ClientesHasBrindesEstoque->addEstoqueForBrindeId(
                                    $clienteHasBrindeHabilitado->id,
                                    $this->user_logged['id'],
                                    0,
                                    0
                                );
                            }
                        }

                        // brinde habilitado, então cadastra novo preço
                        if ($clienteHasBrindeHabilitado) {
                            $brindesHabilitadosPreco = $this->ClientesHasBrindesHabilitadosPreco->addBrindeHabilitadoPreco($clienteHasBrindeHabilitado->id, $clientesId, $brinde->preco_padrao, Configure::read('giftApprovalStatus')['Allowed']);
                        }

                        if ($brindesHabilitadosPreco) {
                            $this->Flash->success(__(Configure::read('messageSavedSuccess')));

                            return $this->redirect(['action' => 'brindes_minha_rede']);
                        }
                    }
                    $this->Flash->error(__(Configure::read('messageSavedError')));
                }
            }
            $arraySet = array(
                "editMode",
                "brinde",
                "clientesId",
                "generoBrindesCliente"
            );

            $this->set(compact($arraySet));
            $this->set('_serialize', $arraySet);
        } catch (\Exception $e) {
            $this->Flash->error($e->getMessage());
        }
    }

    /**
     * Edit method
     *
     * @param string|null $id Brinde id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function editarBrindeRede($id = null)
    {
        $editMode = 1;

        $brinde = $this->Brindes->get($id);

        $generoBrindesId = $brinde["genero_brindes_id"];

        $imagemOriginal = null;
        $imagemOriginalDisco = null;

        if (strlen($brinde->nome_img) > 0) {
            $imagemOriginal = __("{0}{1}{2}{3}", Configure::read("appAddress"), "webroot", Configure::read("imageGiftPathRead"), $brinde["nome_img"]);
            $imagemOriginalDisco = __("{0}{1}{2}", WWW_ROOT, Configure::read("imageGiftPathRead"), $brinde["nome_img"]);
        }

        $user_admin = $this->request->session()->read('User.RootLogged');
        $user_managed = $this->request->session()->read('User.ToManage');
        $rede = $this->request->session()->read("Network.Main");

        if ($user_admin) {
            $this->user_logged = $user_managed;
        }

        $cliente = $this->security_util->checkUserIsClienteRouteAllowed(
            $this->user_logged,
            $this->Clientes,
            $this->ClientesHasUsuarios,
            array(),
            $rede["id"]
        );

        $generoBrindesCliente = $this->GeneroBrindesClientes->getGenerosBrindesClientesVinculados(array($brinde["clientes_id"]));

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();

            if ($this->Brindes->findBrindesByName($data['nome'], $brinde["id"])) {
                $this->Flash->warning(__('Já existe um registro com o nome {0}', $brinde['nome']));
            } else {
                $enviouNovaImagem = isset($data["img-upload"]) && strlen($data["img-upload"]) > 0;

                $brinde = $this->Brindes->patchEntity($brinde, $data);

                // Preserva o id base de gênero brindes
                $brinde["genero_brindes_id"] = $generoBrindesId;

                $brinde->preco_padrao = str_replace(",", "", $data['preco_padrao']);

                if ($enviouNovaImagem) {
                    $brinde["nome_img"] = $this->_preparaImagemBrindeParaGravacao($data);
                }

                if ($this->Brindes->save($brinde)) {
                    $this->Flash->success(__(Configure::read('messageSavedSuccess')));

                    // Se mandou imagem nova
                    if ($enviouNovaImagem && strlen($imagemOriginalDisco) > 0) {
                        // Apaga o arquivo do disco
                        $deleteStatus = unlink($imagemOriginalDisco);
                        Log::write("info", "Excluiu imagem: {$deleteStatus}");
                    }

                    return $this->redirect(['action' => 'brindes_minha_rede']);
                }
                $this->Flash->error(__(Configure::read('messageSavedError')));
            }

        }

        $arraySet = array(
            "editMode",
            "brinde",
            "imagemOriginal",
            "clientes",
            "generoBrindesCliente"
        );

        $this->set(compact($arraySet));
        $this->set('_serialize', $arraySet);
    }

    /**
     * Ativar brinde na loja
     *
     * @param int $id Id do brinde
     *
     * @return void
     **/
    public function ativarBrinde(int $id)
    {
        $this->_alterarEstadoBrinde($id, true);
    }

    /**
     * Desativar brinde na loja
     *
     * @param int $id Id do brinde
     *
     * @return void
     **/
    public function desativarBrinde($id)
    {
        $this->_alterarEstadoBrinde($id, false);
    }

    /**
     * Altera estado do Brinde
     *
     * @param int     $id     Id do Brinde
     * @param boolean $status Estado de alteração
     *
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     */
    private function _alterarEstadoBrinde($id, $status)
    {
        $this->request->allowMethod(['post']);

        $brinde = $this->Brindes->get($id);
        $brinde->habilitado = $status;

        if ($this->Brindes->save($brinde)) {
            if ($status) {
                $this->Flash->success(__(Configure::read('messageEnableSuccess')));
            } else {
                $this->Flash->success(__(Configure::read('messageDisableSuccess')));
            }
        } else {
            if ($status) {
                $this->Flash->success(__(Configure::read('messageEnableError')));
            } else {
                $this->Flash->success(__(Configure::read('messageDisableError')));
            }
        }

        return $this->redirect(['action' => 'brindes_minha_rede']);
    }

    /**
     * Action para impressao rapida (view de funcionário)
     *
     * @return void
     */
    public function impressaoRapida()
    {
        $urlRedirectConfirmacao = array("controller" => "Brindes", "action" => "impressao_rapida");
        $user_admin = $this->request->session()->read('User.RootLogged');
        $user_managed = $this->request->session()->read('User.ToManage');

        if ($user_admin) {
            $this->user_logged = $user_managed;
        }

        $usuario = $this->Usuarios->newEntity();
        $transportadora = $this->Transportadoras->newEntity();
        $veiculo = $this->Veiculos->newEntity();

        $funcionario = $this->Usuarios->getUsuarioById($this->user_logged['id']);

        $rede = $this->request->session()->read('Network.Main');

        // Pega unidades que tem acesso
        $clientes_ids = [];

        $unidades_ids = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($rede->id, $this->user_logged['id'], false);

        foreach ($unidades_ids as $key => $value) {
            $clientes_ids[] = $key;
        }

        // No caso do funcionário, ele só estará em
        // uma unidade, então pega o cliente que ele estiver

        $cliente = $this->Clientes->getClienteById($clientes_ids[0]);

        $clientes_id = $cliente->id;

        // o estado do funcionário é o local onde se encontra o estabelecimento.
        $estado_funcionario = $cliente->estado;

        $transportadoraPath = "TransportadorasHasUsuarios.Transportadoras.";
        $veiculoPath = "UsuariosHasVeiculos.Veiculos.";

        $arraySet = array(
            "usuario",
            "cliente",
            "clientes_id",
            "funcionario",
            "estado_funcionario",
            "urlRedirectConfirmacao",
            "transportadoraPath",
            "veiculoPath"
        );

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);
    }

    /**
     * BrindesController::_preparaImagemBrindeParaGravacao
     *
     * Prepara imagem do brinde para gravação no diretório
     *
     * @param array $data Contendo informações da imagem enviada
     *
     * @author Gustavo Souza GOnçalves <gustavosouzagoncalves@outlook.com>
     * @date 10/06/2018
     *
     * @return string Nome da imagem para gravação no banco
     */
    public function _preparaImagemBrindeParaGravacao(array $data)
    {
        // Faz tratamento de imagem
        // imagem já está no servidor, deve ser feito apenas o resize e mover ela da pasta temporária

        // obtem dados de redimensionamento
        $height = $data["crop-height"];
        $width = $data["crop-width"];
        $valueX = $data["crop-x1"];
        $valueY = $data["crop-y1"];

        $imagemOrigem = __("{0}{1}", Configure::read("imageGiftPathTemp"), $data["img-upload"]);
        $imagemDestino = __("{0}{1}", Configure::read("imageGiftPath"), $data["img-upload"]);

        $resizeSucesso = ImageUtil::resizeImage($imagemOrigem, 600, 600, $valueX, $valueY, $width, $height, 90);

        // Se imagem foi redimensionada, move e atribui o nome para gravação
        if ($resizeSucesso == 1) {
            rename($imagemOrigem, $imagemDestino);

            $nomeImagem = $data["img-upload"];
        }

        return $nomeImagem;
    }

    /**
     * ------------------------------------------------------------
     * Relatórios de Admin RTI
     * ------------------------------------------------------------
     */

    /**
     * Relatóriod de Brindes de cada Rede
     *
     * @return \Cake\Http\Response|void
     */
    public function relatorioBrindesRedes()
    {
        $redesList = $this->Redes->getRedesList();

        $whereConditions = array();

        $redesArrayIds = array();

        foreach ($redesList as $key => $redeItem) {
            $redesArrayIds[] = $key;
        }

        if ($this->request->is(['post'])) {
            $data = $this->request->getData();

            if (strlen($data['redes_id']) > 0) {
                $redesArrayIds = ['id' => $data['redes_id']];
            }

            if (strlen($data['nome']) > 0) {
                $whereConditions[] = ["nome like '%" . $data['nome'] . "%'"];
            }

            if (strlen($data['ilimitado']) > 0) {
                $whereConditions[] = ["ilimitado" => (bool)$data['ilimitado']];
            }

            if (strlen($data['habilitado']) > 0) {
                $whereConditions[] = ["habilitado" => (bool)$data['habilitado']];
            }

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
                    $whereConditions[] = ['brindes.audit_insert BETWEEN "' . $dataInicial . '" and "' . $dataFinal . '"'];
                }

            } else if (strlen($data['auditInsertInicio']) > 0) {

                if ($dataInicial > $dataHoje) {
                    $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Início'));
                } else {
                    $whereConditions[] = ['brindes.audit_insert >= ' => $dataInicial];
                }

            } else if (strlen($data['auditInsertFim']) > 0) {

                if ($dataFinal > $dataHoje) {
                    $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Fim'));
                } else {
                    $whereConditions[] = ['brindes.audit_insert <= ' => $dataFinal];
                }
            }


        }

         // Monta o Array para apresentar em tela
        $redes = array();

        foreach ($redesArrayIds as $key => $value) {
            $arrayWhereConditions = $whereConditions;

            $redesHasClientesIds = array();

            $usuariosIds = array();

            $rede = $this->Redes->getRedeById((int)$value);

            $redeItem = array();

            $redeItem['id'] = $rede->id;
            $redeItem['nome_rede'] = $rede->nome_rede;
            $redeItem['brindes'] = array();

            $unidades_ids = [];

            // obtem os ids das unidades para saber quais brindes estão disponíveis
            foreach ($rede->redes_has_clientes as $key => $value) {
                $unidades_ids[] = $value->clientes_id;
            }

            $arrayWhereConditions[] = [
                'clientes_id in ' => $unidades_ids
            ];

            $brindes = $this->Brindes->findBrindes(
                $arrayWhereConditions
            );

            $redeItem['brindes'] = $brindes;

            unset($arrayWhereConditions);

            array_push($redes, $redeItem);
        }

        $arraySet = [
            'redesList',
            'redes'
        ];

        $this->set(compact($arraySet));

    }

    /**
     * ------------------------------------------------------------
     * Ajax Methods
     * ------------------------------------------------------------
     */

    /**
     * BrindesController::enviaImagemBrinde
     *
     * Envia imagem de brinde de forma assíncrona
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 28/05/2018
     *
     * @return json_object
     */
    public function enviaImagemBrinde()
    {
        $mensagem = null;
        $status = false;
        $message = __("Erro durante o envio da imagem. Tente novamente!");

        $arquivos = array();
        try {
            if ($this->request->is('post')) {

                $data = $this->request->getData();

                $arquivos = FilesUtil::uploadFiles(Configure::read("imageGiftPathTemp"));

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
     * Encontra todos os brindes de um cliente
     *
     * @return void
     */
    public function findBrindes()
    {
        $result = null;
        $brindes = array();

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();

            $clientes_id = $data['clientes_id'];

            $resultado = $this->ClientesHasBrindesHabilitados->getAllGiftsClienteId(
                $clientes_id,
                array(),
                array(),
                array(),
                array(),
                array()
            );

            $brindesHabilitadosCliente = $resultado["brindes"]["data"];

            $brindesTemp = array();

            foreach ($brindesHabilitadosCliente as $key => $brindeHabilitadoCliente) {
                $brindeHabilitadoCliente["brinde"]["nome_img"] =
                    __(
                    "{0}{1}{2}",
                    Configure::read("webrootAddress"),
                    Configure::read("imageGiftPathRead"),
                    $brindeHabilitadoCliente["brinde"]["nome_img"]
                );
                $brindesTemp[] = $brindeHabilitadoCliente;
            }

            $brindes = $brindesTemp;
            $count = sizeof($brindes);
        }

        $arraySet = [
            'brindes',
            'count'
        ];

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);
    }

    /**
     * ------------------------------------------------------------
     * Métodos Comuns
     * ------------------------------------------------------------
     */

    /**
     * BeforeRender callback
     *
     * @param Event $event objeto de Evento
     *
     * @return void
     */
    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);

        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        }
    }

    /**
     * BeforeFilter callback
     *
     * @param Event $event objeto de Evento
     *
     * @return void
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        if (is_null($this->security)) {
            $this->security = new Security();
        }
    }

    /**
     * Método que é executado na inicialização
     */
    public function initialize()
    {
        parent::initialize();

        $this->user_logged = $this->getUserLogged();
        $this->set('user_logged', $this->getUserLogged());
    }
}
