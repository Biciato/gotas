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
    public function novoPrecoBrinde($brindes_id)
    {
        $user_admin = $this->request->session()->read('User.RootLogged');
        $user_managed = $this->request->session()->read('User.ToManage');

        if ($user_admin) {
            $this->user_logged = $user_managed;
        }

        $cliente = $this->security_util->checkUserIsClienteRouteAllowed($this->user_logged, $this->Clientes, $this->ClientesHasUsuarios);

        $novo_preco = $this->ClientesHasBrindesHabilitadosPreco->newEntity();

        $brinde_habilitado = $this->ClientesHasBrindesHabilitados->getBrindeHabilitadoByBrindeId($brindes_id);

        // pega último preço autorizado
        $ultimo_preco_autorizado = $this->ClientesHasBrindesHabilitadosPreco->getLastPrecoForBrindeHabilitadoId($brindes_id, ['status_autorizacao' => (int)Configure::read('giftApprovalStatus')['Allowed']]);

        $clientes_id = $brinde_habilitado->clientes_id;

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();

            $novo_preco = $this->ClientesHasBrindesHabilitadosPreco->patchEntity($novo_preco, $data);

            // verifica se o último brinde autorizado
            // tem valor igual ao valor que o usuário informou

            $preco_comparacao = str_replace(",", "", $this->request->getData()['preco']);

            if ($ultimo_preco_autorizado->preco == $preco_comparacao) {

                $this->Flash->error("O valor informado é o mesmo do preço atual, não será criado um novo preço!");

                return $this->redirect(['controller' => 'clientes_has_brindes_habilitados_preco', 'action' => 'novo_preco_brinde', $brindes_id]);
            }

            // verifica se o brinde tem algum preço aguardando autorização.

            $ultimo_preco = $this->ClientesHasBrindesHabilitadosPreco->getLastPrecoForBrindeHabilitadoId($brindes_id);

            /**
             * verifica se houve um último preço para atualizar os dados
             * (conferir se realmente vai autorizar a mudança...)
             */

            if ($ultimo_preco) {

                /**
                 * Caso esteja pendente e for alguém com permissão
                 * maior que Administrador Local, não permite continuar
                 */
                if ($ultimo_preco->status_autorizacao == (int)Configure::read('giftApprovalStatus')['AwaitingAuthorization']) {
                    if ($this->user_logged['tipo_perfil'] > Configure::read('profileTypes')['AdminRegionalProfileType']) {

                        $this->Flash->error("Este brinde já possui um preço pendente de autorização. Não será possível cadastrar um novo até que o anterior seja autorizado ou negado!");

                        return $this->redirect(['controller' => 'clientes_has_brindes_habilitados_preco', 'action' => 'novo_preco_brinde', $brindes_id]);
                    } else {

                    //caso contrário, atualiza ele para negado
                        $ultimo_preco->status_autorizacao == (int)Configure::read('giftApprovalStatus')['Denied'];

                        $this->ClientesHasBrindesHabilitadosPreco->save($ultimo_preco);
                    }
                }
            }

            $novo_preco->preco = str_replace(",", "", $this->request->getData()['preco']);

            /*
             * Preco deve ser alertado aos Administradores da Rede caso
             * quem alterou tiver um perfil de administrador comum e
             * for fora da matriz.
             */

            $requer_autorizacao = Configure::read('giftApprovalStatus')['Allowed'];

            if (!($cliente->matriz) && ($brinde_habilitado->brinde->preco_padrao != $novo_preco->preco) && $this->user_logged['tipo_perfil'] == Configure::read('profileTypes')['AdminLocalProfileType']) {
                $requer_autorizacao = (int)Configure::read('giftApprovalStatus')['AwaitingAuthorization'];
            }

            $novo_preco = $this->ClientesHasBrindesHabilitadosPreco->addBrindeHabilitadoPreco($brindes_id, $cliente->id, $novo_preco['preco'], $requer_autorizacao);

            if ($novo_preco) {
                $this->Flash->success(Configure::read('messageSavedSuccess'));

                /**
                 * Se o preço é diferente, envia um e-mail para cada administrador
                 * da rede daquela rede informando à respeito da alteração do preço
                 */
                if ($requer_autorizacao == (int)Configure::read('giftApprovalStatus')['AwaitingAuthorization']) {
                    $matriz_id = $brinde_habilitado->brinde->clientes_id;

                    $usuarios = $this->ClientesHasUsuarios->getAllUsersByClienteId($matriz_id, (int)Configure::read('profileTypes')['AdminNetworkProfileType']);

                    foreach ($usuarios as $key => $usuario) {
                        $url = Router::url(
                            [
                                'controller' => 'clientes_has_brindes_habilitados',
                                'action' => 'detalhes_brinde',
                                $brinde_habilitado->id
                            ]
                        );

                        $full_url = Configure::read('appAddress') . $url;

                        $admin_name = $usuario->nome;

                        $content['full_url'] = $full_url;
                        $content['admin_name'] = $admin_name;

                        $this->email_util->sendMail('price_update_gift', $usuario, 'Preço de Brinde Aguardando Autorização', $content);
                    }
                }

                return $this->redirect(['controller' => 'clientesHasBrindesHabilitados', 'action' => 'configurar_brinde', $brindes_id]);
            }

            $this->Flash->error(Configure::read('messageSavedError'));
        }

        $array_set = ['novo_preco', 'brindes_id', 'brinde_habilitado', 'clientes_id', 'ultimo_preco_autorizado'];
        $this->set(compact([$array_set]));
        $this->set('_serialize', [$array_set]);
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
            $user_admin = $this->request->session()->read('User.RootLogged');
            $user_managed = $this->request->session()->read('User.ToManage');

            if ($user_admin) {
                $this->user_logged = $user_managed;
            }

            $rede = $this->request->session()->read('Network.Main');

            $matriz = $this->RedesHasClientes->findMatrizOfRedesByRedesId($rede->id);

            $unidades_ids = [];

            $clientes_ids = [];

                    // Pega unidades que tem acesso

            $unidades_ids = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($rede->id, $this->user_logged['id']);

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
