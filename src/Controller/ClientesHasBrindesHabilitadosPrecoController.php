<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Custom\RTI\Security;
use Cake\Core\Configure;
use Cake\Collection\Collection;
use Cake\Event\Event;
use Cake\Log\Log;
use Cake\Routing\Router;
use Cake\Mailer\Email;
use Cake\View\Helper\UrlHelper;
use \DateTime;
use App\Custom\RTI\DateTimeUtil;
use App\Custom\RTI\DebugUtil;
use Cake\I18n\Number;

/**
 * ClientesHasBrindesHabilitadosPreco Controller
 *
 * @property \App\Model\Table\ClientesHasBrindesHabilitadosPrecoTable $ClientesHasBrindesHabilitadosPreco
 *
 * @method \App\Model\Entity\ClientesHasBrindesHabilitadosPreco[] paginate($object = null, array $settings = [])
 */
class ClientesHasBrindesHabilitadosPrecoController extends AppController
{
    /**
     * ------------------------------------------------------------
     * Fields
     * ------------------------------------------------------------
     */
    protected $usuarioLogado = null;


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
            'contain' => ['BrindesHabilitados']
        ];
        $clientes_has_brindes_habilitados_precos = $this->paginate($this->ClientesHasBrindesHabilitadosPreco);

        $this->set(compact('clientes_has_brindes_habilitados_precos'));
        $this->set('_serialize', ['clientes_has_brindes_habilitados_precos']);
    }

    /**
     * View method
     *
     * @param string|null $id Brindes Habilitados Preco id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $clientesHasBrindesHabilitadosPreco
            = $this->ClientesHasBrindesHabilitadosPreco
            ->get($id, ['contain' => ['BrindesHabilitados']]);

        $this->set('clientesHasBrindesHabilitadosPreco', $clientesHasBrindesHabilitadosPreco);
        $this->set('_serialize', ['clientesHasBrindesHabilitadosPreco']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $clientesHasBrindesHabilitadosPreco = $this->ClientesHasBrindesHabilitadosPreco->newEntity();
        if ($this->request->is('post')) {
            $clientesHasBrindesHabilitadosPreco = $this->ClientesHasBrindesHabilitadosPreco->patchEntity($clientesHasBrindesHabilitadosPreco, $this->request->getData());
            if ($this->ClientesHasBrindesHabilitadosPreco->save($clientesHasBrindesHabilitadosPreco)) {
                $this->Flash->success(__('The brindes habilitados preco has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The brindes habilitados preco could not be saved. Please, try again.'));
        }
        $brindesHabilitados = $this->ClientesHasBrindesHabilitadosPreco->BrindesHabilitados->find('list', ['limit' => 200]);
        $this->set(compact('clientesHasBrindesHabilitadosPreco', 'brindesHabilitados'));
        $this->set('_serialize', ['clientesHasBrindesHabilitadosPreco']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Brindes Habilitados Preco id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $clientesHasBrindesHabilitadosPreco = $this->ClientesHasBrindesHabilitadosPreco
            ->get($id, ['contain' => []]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $clientesHasBrindesHabilitadosPreco = $this->ClientesHasBrindesHabilitadosPreco->patchEntity($clientesHasBrindesHabilitadosPreco, $this->request->getData());
            if ($this->ClientesHasBrindesHabilitadosPreco->save($clientesHasBrindesHabilitadosPreco)) {
                $this->Flash->success(__('The brindes habilitados preco has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The brindes habilitados preco could not be saved. Please, try again.'));
        }
        $brindesHabilitados = $this->ClientesHasBrindesHabilitadosPreco->BrindesHabilitados->find('list', ['limit' => 200]);
        $this->set(compact('clientesHasBrindesHabilitadosPreco', 'brindesHabilitados'));
        $this->set('_serialize', ['clientesHasBrindesHabilitadosPreco']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Brindes Habilitados Preco id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $clientesHasBrindesHabilitadosPreco = $this->ClientesHasBrindesHabilitadosPreco->get($id);
        if ($this->ClientesHasBrindesHabilitadosPreco->delete($clientesHasBrindesHabilitadosPreco)) {
            $this->Flash->success(__('The brindes habilitados preco has been deleted.'));
        } else {
            $this->Flash->error(__('The brindes habilitados preco could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * ------------------------------------------------------------
     * Custom Methods
     * ------------------------------------------------------------
     */

    /**
     * Novo preço de brinde
     *
     * @param int $brindes_id Id de Brindes
     *
     * @return \Cake\Http\Response|null Redirects to giftsDetails on success, renders view otherwize
     */
    public function novoPrecoBrinde($brindesId)
    {
        $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
        $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

        if ($usuarioAdministrador) {
            $this->usuarioLogado = $usuarioAdministrar;
        }

        $rede = $this->request->session()->read("Rede.Principal");

        // $cliente = $this->securityUtil->checkUserIsClienteRouteAllowed($this->usuarioLogado, $this->Clientes, $this->ClientesHasUsuarios, array(), $rede["id"]);

        $novoPreco = $this->ClientesHasBrindesHabilitadosPreco->newEntity();

        $brindeHabilitado = $this->ClientesHasBrindesHabilitados->getBrindeHabilitadoById($brindesId);

        // pega último preço autorizado
        $ultimoPrecoAutorizadoGotas = $this->ClientesHasBrindesHabilitadosPreco->getUltimoPrecoBrindeHabilitadoId($brindesId, ['status_autorizacao' => (int)Configure::read('giftApprovalStatus')['Allowed']]);
        $ultimoPrecoAutorizadoVendaAvulsa = $this->ClientesHasBrindesHabilitadosPreco->getUltimoPrecoVendaAvulsaBrindeHabilitadoId($brindesId, (int)Configure::read('giftApprovalStatus')['Allowed']);

        // Pega último preco de venda avulsa autorizado

        $clientesId = $brindeHabilitado->clientes_id;

        $cliente = $this->Clientes->getClienteById($clientesId);


        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();

            $preco = $data["preco"];
            $valorMoedaVenda = $data["valor_moeda_venda"];

            $message = null;
            $errorFound = false;
            if (empty($preco) && empty($valorMoedaVenda)) {
                $message = "É necessário informar valor para Preço (em gotas) ou Preço (Venda avulsa)";
                $errorFound = true;

            } else if (empty($valorMoedaVenda) && (!empty($preco) && ($preco == $ultimoPrecoAutorizadoGotas["preco"]))) {
                $message = "Este preço de gotas já foi utilizado anteriormente. Especifique outro valor para cadastro!";
                $errorFound = true;

            } else if (empty($preco) && (!empty($valorMoedaVenda) && ($valorMoedaVenda == $ultimoPrecoAutorizadoVendaAvulsa["valor_venda_moeda"]))) {
                $message = "Este preço de venda avulsa já foi utilizado anteriormente. Especifique outro valor para cadastro!";
                $errorFound = true;
            }

            // DebugUtil::print($data);
            if ($errorFound) {
                $this->Flash->error($message);

                return $this->redirect(array("action" => "novo_preco_brinde", $brindesId));
            }

            $novoPreco = $this->ClientesHasBrindesHabilitadosPreco->patchEntity($novoPreco, $data);

            // verifica se o brinde tem algum preço aguardando autorização.

            $ultimoPreco = $this->ClientesHasBrindesHabilitadosPreco->getUltimoPrecoBrindeHabilitadoId($brindesId);

            // echo __LINE__;
            // DebugUtil::print($ultimoPreco);

            /**
             * verifica se houve um último preço para atualizar os dados
             * (conferir se realmente vai autorizar a mudança...)
             */

            if ($ultimoPreco) {

                /**
                 * Caso esteja pendente e for alguém com permissão
                 * maior que Administrador Local, não permite continuar
                 */
                if ($ultimoPreco->status_autorizacao == (int)Configure::read('giftApprovalStatus')['AwaitingAuthorization']) {
                    if ($this->usuarioLogado['tipo_perfil'] > Configure::read('profileTypes')['AdminRegionalProfileType']) {

                        $this->Flash->error("Este brinde já possui um preço pendente de autorização. Não será possível cadastrar um novo até que o anterior seja autorizado ou negado!");

                        return $this->redirect(['controller' => 'clientes_has_brindes_habilitados_preco', 'action' => 'novo_preco_brinde', $brindesId]);
                    } else {

                    //caso contrário, atualiza ele para negado
                        $ultimoPreco->status_autorizacao == (int)Configure::read('giftApprovalStatus')['Denied'];

                        $this->ClientesHasBrindesHabilitadosPreco->save($ultimoPreco);
                    }
                }
            }

            // $novoPreco->preco = str_replace(",", "", $this->request->getData()['preco']);
            $novoPreco["preco"] = (float)$data['preco'] != 0 ? (float)$data["preco"] : null;
            $novoPreco["valor_moeda_venda"] = (float)$data['valor_moeda_venda'] != 0 ? (float)$data["valor_moeda_venda"] : null;

            // DebugUtil::print($novoPreco);

            /*
             * Preco deve ser alertado aos Administradores da Rede caso
             * quem alterou tiver um perfil de administrador comum e
             * for fora da matriz.
             */

            $requerAutorizacao = Configure::read('giftApprovalStatus')['Allowed'];

            // DebugUtil::printArray($clientesId);

            if (!($cliente->matriz) && ($brindeHabilitado->brinde->preco_padrao != $novoPreco->preco) && $this->usuarioLogado['tipo_perfil'] == Configure::read('profileTypes')['AdminLocalProfileType']) {
                $requerAutorizacao = (int)Configure::read('giftApprovalStatus')['AwaitingAuthorization'];
            }

            $novoPreco = $this->ClientesHasBrindesHabilitadosPreco->addBrindeHabilitadoPreco(
                $brindesId,
                $clientesId,
                $requerAutorizacao,
                $novoPreco["preco"],
                $novoPreco["valor_moeda_venda"]
            );

            // DebugUtil::print($brindeHabilitado);
            if ($novoPreco) {
                $this->Flash->success(Configure::read('messageSavedSuccess'));

                /**
                 * Se o preço é diferente, envia um e-mail para cada administrador
                 * da rede daquela rede informando à respeito da alteração do preço
                 */
                if ($requerAutorizacao == (int)Configure::read('giftApprovalStatus')['AwaitingAuthorization']) {
                    $matrizId = $brindeHabilitado->brinde->clientes_id;

                    $usuarios = $this->ClientesHasUsuarios->getAllUsersByClienteId($matrizId, (int)Configure::read('profileTypes')['AdminNetworkProfileType']);

                    foreach ($usuarios as $key => $usuario) {
                        $url = Router::url(
                            [
                                'controller' => 'clientes_has_brindes_habilitados',
                                'action' => 'detalhes_brinde',
                                $brindeHabilitado->id
                            ]
                        );

                        $fullUrl = Configure::read('appAddress') . $url;

                        $adminName = $usuario->nome;

                        $content['full_url'] = $fullUrl;
                        $content['admin_name'] = $adminName;

                        $this->emailUtil->sendMail('price_update_gift', $usuario, 'Preço de Brinde Aguardando Autorização', $content);
                    }
                }

                return $this->redirect(['controller' => 'clientesHasBrindesHabilitados', 'action' => 'configurar_brinde', $brindesId]);
            }

            $this->Flash->error(Configure::read('messageSavedError'));
        }

        $ultimoPrecoAutorizadoGotas["preco"] = empty($ultimoPrecoAutorizadoGotas["preco"]) ? 0 : $ultimoPrecoAutorizadoGotas["preco"];

        $arraySet = array('novoPreco', 'brindesId', 'brindeHabilitado', 'clientesId', 'ultimoPrecoAutorizadoGotas', "ultimoPrecoAutorizadoVendaAvulsa");
        $this->set(compact([$arraySet]));
        $this->set('_serialize', [$arraySet]);
    }

    /**
     * Method for Gifts Awaiting Approval
     *
     * @param int $clientes_id Id de cliente
     *
     * @return \Cake\Network\Response|void
     **/
    public function brindesAguardandoAprovacao()
    {
        try {
            $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
            $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

            if ($usuarioAdministrador) {
                $this->usuarioLogado = $usuarioAdministrar;
            }

            $rede = $this->request->session()->read('Rede.Principal');

            $matriz = $this->RedesHasClientes->findMatrizOfRedesByRedesId($rede->id);

            $unidades_ids = [];

            $clientes_ids = [];

                    // Pega unidades que tem acesso

            $unidades_ids = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($rede->id, $this->usuarioLogado['id']);

            foreach ($unidades_ids as $key => $value) {
                            // $clientes_ids[] = $value['clientes_id'];
                $clientes_ids[] = $key;
            }

            $conditions = [];

            $brindes_aguardando_autorizacao = $this->ClientesHasBrindesHabilitadosPreco->getPrecoAwaitingAuthorizationByClientesId($clientes_ids);

            $this->paginate($brindes_aguardando_autorizacao, ['limit' => 10]);

            $this->set(compact(['brindes_aguardando_autorizacao']));
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $this->Flash->error(__("Não foi possível realizar o procedimento. Entre em contato com o suporte. Descrição do erro: {0} em: {1}", $e->getMessage(), $trace[1]));

            Log::write('error', __("Não foi possível realizar o procedimento. Entre em contato com o suporte. Descrição do erro: {0} em: {1}", $e->getMessage(), $trace[1]));
        }
    }

    /**
     * Allow change of gift price
     *
     * @param int $clientes_has_brindes_habilitados_preco_id Id do ClienteHasBrindesHabilitadosPreco
     *
     * @return void
     */
    public function permitirPrecoBrinde($clientes_has_brindes_habilitados_preco_id)
    {
        $this->_alteraPrecoBrindeEstado($clientes_has_brindes_habilitados_preco_id, true);
    }

    /**
     * Deny change of gift price
     *
     * @param int $clientes_has_brindes_habilitados_preco_id Id do ClienteHasBrindesHabilitadosPreco
     *
     * @return void
     **/
    public function negarPrecoBrinde($clientes_has_brindes_habilitados_preco_id)
    {
        $this->_alteraPrecoBrindeEstado($clientes_has_brindes_habilitados_preco_id, false);
    }

    /**
     * Deny change of gift price
     *
     * @param int  $clientes_has_brindes_habilitados_preco_id Id do ClienteHasBrindesHabilitadosPreco
     * @param bool $status                                    (1 Allowed | 2 Denied)
     *
     * @return void
     */
    private function _alteraPrecoBrindeEstado(int $clientes_has_brindes_habilitados_preco_id, bool $status)
    {
        try {
            if ($this->request->is(['post', 'put'])) {
                $preco_autorizar = $this->ClientesHasBrindesHabilitadosPreco->getPrecoBrindeById($clientes_has_brindes_habilitados_preco_id);

                if ($status) {
                    $preco_autorizar->status_autorizacao = Configure::read('giftApprovalStatus')['Allowed'];
                } else {
                    $preco_autorizar->status_autorizacao = Configure::read('giftApprovalStatus')['Denied'];
                }

                if ($this->ClientesHasBrindesHabilitadosPreco->save($preco_autorizar)) {
                    if ($status) {
                        $this->Flash->success(Configure::read('messageAllowGiftPrice'));
                    } else {
                        $this->Flash->success(Configure::read('messageDenyGiftPrice'));
                    }

                    return $this->redirect(['controller' => 'ClientesHasBrindesHabilitadosPreco', 'action' => 'brindesAguardandoAprovacao']);
                }
            }
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $this->Flash->error(__("Não foi possível realizar o procedimento. Entre em contato com o suporte. Descrição do erro: {0} em: {1}", $e->getMessage(), $trace[1]));


            Log::write('error', __("Não foi possível realizar o procedimento. Entre em contato com o suporte. Descrição do erro: {0} em: {1}", $e->getMessage(), $trace[1]));
        }
    }

    /**
     * ------------------------------------------------------------
     * Relatórios Dashboard Admin RTI
     * ------------------------------------------------------------
     */

    /**
     * Exibe a action de Relatorio de Histórico Preços de Brindes Habilitados por Redes
     *
     * @return \Cake\Network\Response|null|void
     */
    public function relatorioHistoricoPrecoBrindesRedes()
    {
        try {
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
                    $whereConditions[] = ["brindes.nome like '%" . $data['nome'] . "%'"];
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

            // este relatório não precisa mostrar os brindes RTI Shower

            // TODO: ajustar
            $whereConditions[] = ["brindes.equipamento_rti_shower" => false];

            foreach ($redesArrayIds as $key => $value) {
                $arrayWhereConditions = $whereConditions;

                $redesHasClientesIds = array();

                $usuariosIds = array();

                $rede = $this->Redes->getRedeById((int)$value);

                $redeItem = array();

                $redeItem['id'] = $rede->id;
                $redeItem['nome_rede'] = $rede->nome_rede;
                $redeItem['brindes'] = array();

                $clientesIds = [];

                // obtem os ids das unidades para saber quais brindes estão disponíveis
                foreach ($rede->redes_has_clientes as $key => $value) {
                    $clientesIds[] = $value->clientes_id;
                }

                $brindesHabilitadosReturn = array();

                $cliente = null;
                foreach ($clientesIds as $key => $clienteId) {

                    $brindesHabilitadosArray = $this->ClientesHasBrindesHabilitados->getBrindesHabilitadosByClienteId(
                        [$clienteId],
                        $arrayWhereConditions
                    )->toArray();

                    if (sizeof($brindesHabilitadosArray) > 0) {
                        $brindesHabilitadosArrayTmp = array();
                        foreach ($brindesHabilitadosArray as $key => $brindeHabilitado) {

                            $brindeHabilitado = $this->ClientesHasBrindesHabilitados->getBrindeHabilitadoById($brindeHabilitado->id);

                            array_push($brindesHabilitadosArrayTmp, $brindeHabilitado);
                        }

                        $brindesHabilitadosArray = $brindesHabilitadosArrayTmp;

                        $brindesHabilitadosReturn[] = $brindesHabilitadosArray;
                    }
                }

                $redeItem['clientesBrindes'] = $brindesHabilitadosReturn;
                unset($arrayWhereConditions);

                array_push($redes, $redeItem);
            }

            $arraySet = [
                'redesList',
                'redes'
            ];

            $this->set(compact($arraySet));

        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao consultar Histórico de Preços: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Exibe a action de Relatorio de Histórico Detalhado de Preços de Brindes Habilitados por Redes
     *
     * @param int $clientesHasBrindesHabilitadoId Id de Brinde Habilitado do Cliente
     * @return \Cake\Network\Response|null|void
     */
    public function relatorioHistoricoPrecoBrindesDetalhado(int $clientesHasBrindesHabilitadoId)
    {
        try {
            $brinde = $this->ClientesHasBrindesHabilitados->getBrindeHabilitadoById($clientesHasBrindesHabilitadoId);

            $qteRegistros = 10;

            $whereConditions = array();

            if (isset($brinde['id'])) {

                if ($this->request->is('post')) {

                    $data = $this->request->getData();

                // status autorização

                    if (strlen($data['statusAutorizacao']) > 0) {
                        $whereConditions[] = ['status_autorizacao' => (int)$data['statusAutorizacao']];
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
                            $whereConditions[] = ['ClientesHasBrindesHabilitadosPreco.audit_insert BETWEEN "' . $dataInicial . '" and "' . $dataFinal . '"'];
                        }

                    } else if (strlen($data['auditInsertInicio']) > 0) {

                        if ($dataInicial > $dataHoje) {
                            $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Início'));
                        } else {
                            $whereConditions[] = ['ClientesHasBrindesHabilitadosPreco.audit_insert >= ' => $dataInicial];
                        }

                    } else if (strlen($data['auditInsertFim']) > 0) {

                        if ($dataFinal > $dataHoje) {
                            $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Fim'));
                        } else {
                            $whereConditions[] = ['ClientesHasBrindesHabilitadosPreco.audit_insert <= ' => $dataFinal];
                        }
                    }
                }

                $rede = $this->RedesHasClientes->getRedesHasClientesByClientesId($brinde->clientes_id)->rede;

                $cliente = $this->Clientes->getClienteById($brinde->clientes_id);

                $historicoPrecoBrinde = $this->ClientesHasBrindesHabilitadosPreco->getAllPrecoForBrindeHabilitadoId($clientesHasBrindesHabilitadoId, $whereConditions, $qteRegistros);
            }

            $arraySet = [
                'brinde',
                'cliente',
                'rede',
                'historicoPrecoBrinde'
            ];

            $this->set(compact($arraySet));
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao consultar Histórico Detalhado de Preços: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * ------------------------------------------------------------
     * Initialize Methods
     * ------------------------------------------------------------
     */

    /**
     * Before render callback.
     *
     * @param \App\Controller\Event\Event $event The beforeRender event.
     * @return \Cake\Network\Response|null|void
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
    }

    /**
     *
     */
    public function initialize()
    {
        parent::initialize();
    }
}
