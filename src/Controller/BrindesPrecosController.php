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
 * BrindesPrecos Controller
 *
 * @property \App\Model\Table\BrindesPrecosTable $BrindesPrecos
 *
 * @method \App\Model\Entity\BrindesPrecos[] paginate($object = null, array $settings = [])
 */
class BrindesPrecosController extends AppController
{
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
        $clientes_has_brindes_habilitados_precos = $this->paginate($this->BrindesPrecos);

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
        $BrindesPrecos
            = $this->BrindesPrecos
            ->get($id, ['contain' => ['BrindesHabilitados']]);

        $this->set('BrindesPrecos', $BrindesPrecos);
        $this->set('_serialize', ['BrindesPrecos']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $BrindesPrecos = $this->BrindesPrecos->newEntity();
        if ($this->request->is('post')) {
            $BrindesPrecos = $this->BrindesPrecos->patchEntity($BrindesPrecos, $this->request->getData());
            if ($this->BrindesPrecos->save($BrindesPrecos)) {
                $this->Flash->success(__('The brindes habilitados preco has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The brindes habilitados preco could not be saved. Please, try again.'));
        }
        $brindesHabilitados = $this->BrindesPrecos->BrindesHabilitados->find('list', ['limit' => 200]);
        $this->set(compact('BrindesPrecos', 'brindesHabilitados'));
        $this->set('_serialize', ['BrindesPrecos']);
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
        $BrindesPrecos = $this->BrindesPrecos
            ->get($id, ['contain' => []]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $BrindesPrecos = $this->BrindesPrecos->patchEntity($BrindesPrecos, $this->request->getData());
            if ($this->BrindesPrecos->save($BrindesPrecos)) {
                $this->Flash->success(__('The brindes habilitados preco has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The brindes habilitados preco could not be saved. Please, try again.'));
        }
        $brindesHabilitados = $this->BrindesPrecos->BrindesHabilitados->find('list', ['limit' => 200]);
        $this->set(compact('BrindesPrecos', 'brindesHabilitados'));
        $this->set('_serialize', ['BrindesPrecos']);
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
        $BrindesPrecos = $this->BrindesPrecos->get($id);
        if ($this->BrindesPrecos->delete($BrindesPrecos)) {
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
    public function atualizarPreco($brindesId)
    {
        $arraySet = array( 'brindesId', 'brinde', 'clientesId', "redesId",'novoPreco', "tipoVenda", "ultimoPreco");

        $sessaoUsuario = $this->getSessionUserVariables();

        $usuarioAdministrador = $sessaoUsuario["usuarioAdministrador"];
        $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"];
        $usuarioLogado = $sessaoUsuario["usuarioLogado"];
        if ($usuarioAdministrador) {
            $this->usuarioLogado = $usuarioAdministrar;
        }

        $cliente = $sessaoUsuario["cliente"];
        $rede = $sessaoUsuario["rede"];

        $brinde = $this->Brindes->getBrindeById($brindesId);

        $error = false;
        $errors = array();
        if (empty($brinde)) {
            $errors[] = "Brinde não existe!";
        }

        if (!$brinde["habilitado"]) {
            $errors[] = "Brinde desabilitado, não é possível atualizar preço!";
        }

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->Flash->error($error);
            }

            return $this->redirect(sprintf("/brindes/view/%s", $brindesId));
        }

        $clientesId = $brinde["clientes_id"];

        if (empty($rede)) {
            $redeHasCliente = $this->RedesHasClientes->getRedesHasClientesByClientesId($clientesId);
            $cliente = $redeHasCliente["cliente"];
            $rede = $redeHasCliente["rede"];
        }
        $redesId = $rede["id"];

        $cliente = $this->securityUtil->checkUserIsClienteRouteAllowed($this->usuarioLogado, $this->Clientes, $this->ClientesHasUsuarios, array(), $rede["id"]);
        $tipoVenda = $brinde["tipo_venda"];

        $novoPreco = $this->BrindesPrecos->newEntity();

        // Pega último preco autorizado
        $ultimoPreco = $this->BrindesPrecos->getUltimoPrecoBrinde($brindesId, STATUS_AUTHORIZATION_PRICE_AUTHORIZED);

        if ($this->request->is(array('post', 'put'))) {
            $data = $this->request->getData();
            $preco = $data["preco"];
            $valorMoedaVenda = $data["valor_moeda_venda"];
            $errors = array();

            // Se desconto, preco_padrao e valor_moeda_venda_padrao devem estar preenchidos
            if (($tipoVenda == TYPE_SELL_DISCOUNT_TEXT) && (empty($preco) || empty($valorMoedaVenda))) {
                $errors[] = "Preço Padrão ou Preço em Reais devem ser informados!";
            }
            // se é Opcional mas preco_padrao ou valor_moeda_venda_padrao estão vazios
            if (($tipoVenda == TYPE_SELL_CURRENCY_OR_POINTS_TEXT) && (empty($preco) && empty($valorMoedaVenda))) {
                $errors[] = "Preço Padrão e Preço em Reais devem ser informados!";
            }
            if (empty($preco) && empty($valorMoedaVenda)) {
                $errors[] = "É necessário informar valor para Preço (em gotas) ou Preço (Venda avulsa)";
            }
            if (empty($valorMoedaVenda) && (!empty($preco) && ($preco == $ultimoPrecoAutorizadoGotas["preco"]))) {
                $errors[] = "Este preço de gotas já foi utilizado anteriormente. Especifique outro valor para cadastro!";
            }
            if (empty($preco) && (!empty($valorMoedaVenda) && ($valorMoedaVenda == $ultimoPreco["valor_venda_moeda"]))) {
                $message = "Este preço de venda avulsa já foi utilizado anteriormente. Especifique outro valor para cadastro!";
            }

            if (count($errors) > 0) {

                foreach ($errors as $error) {
                    $this->Flash->error($error);
                }

                $this->set(compact($arraySet));
                $this->set('_serialize', $arraySet);

                return;
            }

            $novoPreco = $this->BrindesPrecos->patchEntity($novoPreco, $data);

            // verifica se o brinde tem algum preço aguardando autorização.

            $ultimoPreco = $this->BrindesPrecos->getUltimoPrecoBrinde($brindesId);

            /**
             * verifica se houve um último preço para atualizar os dados
             * (conferir se realmente vai autorizar a mudança...)
             */

            if ($ultimoPreco) {

                /**
                 * Caso esteja pendente e for alguém com permissão
                 * maior que Administrador Local, não permite continuar
                 */
                if ($ultimoPreco["status_autorizacao"] == STATUS_AUTHORIZATION_PRICE_AWAITING) {
                    if ($this->usuarioLogado['tipo_perfil'] > PROFILE_TYPE_ADMIN_REGIONAL) {

                        $this->Flash->error("Este brinde já possui um preço pendente de autorização. Não será possível cadastrar um novo até que o anterior seja autorizado ou negado!");

                        return $this->redirect(['controller' => 'brindesPrecos', 'action' => 'atualizarPreco', $brindesId]);
                    } else {

                        //caso contrário, atualiza ele para negado
                        $ultimoPreco["status_autorizacao"] == STATUS_AUTHORIZATION_PRICE_DENIED;

                        $this->BrindesPrecos->save($ultimoPreco);
                    }
                }
            }

            // $novoPreco->preco = str_replace(",", "", $this->request->getData()['preco']);
            $novoPreco["preco"] = (float)$data['preco'] != 0 ? (float)$data["preco"] : null;
            $novoPreco["valor_moeda_venda"] = (float)$data['valor_moeda_venda'] != 0 ? (float)$data["valor_moeda_venda"] : null;

            $novoPreco = $this->BrindesPrecos->addBrindePreco(
                $brindesId,
                $clientesId,
                $this->usuarioLogado["id"],
                STATUS_AUTHORIZATION_PRICE_AUTHORIZED,
                $novoPreco["preco"],
                $novoPreco["valor_moeda_venda"]
            );

            // DebugUtil::print($brindeHabilitado);
            if ($novoPreco) {
                $this->Flash->success(Configure::read('messageSavedSuccess'));

                return $this->redirect(['controller' => 'brindes', 'action' => 'view', $brindesId]);

                // @warning Desativado por enquanto, ver com Samuel como ficará no futuro

                /**
                * Preco deve ser alertado aos Administradores da Rede caso
                * quem alterou tiver um perfil de administrador comum e
                * for fora da matriz.
                */
                /**
                 * Se o preço é diferente, envia um e-mail para cada administrador
                 * da rede daquela rede informando à respeito da alteração do preço
                 */
                // if ($requerAutorizacao == STATUS_AUTHORIZATION_PRICE_AWAITING) {
                //     $matrizId = $brinde->clientes_id;

                //     $usuarios = $this->ClientesHasUsuarios->getAllUsersByClienteId($matrizId, PROFILE_TYPE_ADMIN_NETWORK);

                //     foreach ($usuarios as $key => $usuario) {
                //         $url = Router::url(
                //             [
                //                 'controller' => 'clientes_has_brindes_habilitados',
                //                 'action' => 'detalhes_brinde',
                //                 $brindeHabilitado->id
                //             ]
                //         );

                //         $fullUrl = Configure::read('appAddress') . $url;

                //         $adminName = $usuario->nome;

                //         $content['full_url'] = $fullUrl;
                //         $content['admin_name'] = $adminName;

                //         $this->emailUtil->sendMail('price_update_gift', $usuario, 'Preço de Brinde Aguardando Autorização', $content);
                //     }
                // }

            }

            $this->Flash->error(Configure::read('messageSavedError'));
        }

        $ultimoPrecoAutorizadoGotas["preco"] = empty($ultimoPrecoAutorizadoGotas["preco"]) ? 0 : $ultimoPrecoAutorizadoGotas["preco"];

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

            $rede = $this->request->session()->read('Rede.Grupo');

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

            $brindes_aguardando_autorizacao = $this->BrindesPrecos->getPrecoAwaitingAuthorizationByClientesId($clientes_ids);

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
                $preco_autorizar = $this->BrindesPrecos->getPrecoBrindeById($clientes_has_brindes_habilitados_preco_id);

                if ($status) {
                    $preco_autorizar->status_autorizacao = STATUS_AUTHORIZATION_PRICE_AUTHORIZED;
                } else {
                    $preco_autorizar->status_autorizacao = STATUS_AUTHORIZATION_PRICE_DENIED;
                }

                if ($this->BrindesPrecos->save($preco_autorizar)) {
                    if ($status) {
                        $this->Flash->success(Configure::read('messageAllowGiftPrice'));
                    } else {
                        $this->Flash->success(Configure::read('messageDenyGiftPrice'));
                    }

                    return $this->redirect(['controller' => 'BrindesPrecos', 'action' => 'brindesAguardandoAprovacao']);
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
                            $whereConditions[] = ['BrindesPrecos.audit_insert BETWEEN "' . $dataInicial . '" and "' . $dataFinal . '"'];
                        }
                    } else if (strlen($data['auditInsertInicio']) > 0) {

                        if ($dataInicial > $dataHoje) {
                            $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Início'));
                        } else {
                            $whereConditions[] = ['BrindesPrecos.audit_insert >= ' => $dataInicial];
                        }
                    } else if (strlen($data['auditInsertFim']) > 0) {

                        if ($dataFinal > $dataHoje) {
                            $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Fim'));
                        } else {
                            $whereConditions[] = ['BrindesPrecos.audit_insert <= ' => $dataFinal];
                        }
                    }
                }

                $rede = $this->RedesHasClientes->getRedesHasClientesByClientesId($brinde->clientes_id)->rede;

                $cliente = $this->Clientes->getClienteById($brinde->clientes_id);

                $historicoPrecoBrinde = $this->BrindesPrecos->getAllPrecoForBrindeHabilitadoId($clientesHasBrindesHabilitadoId, $whereConditions, $qteRegistros);
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
