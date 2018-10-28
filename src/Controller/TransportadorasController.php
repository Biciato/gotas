<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Custom\RTI\Security;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Log\Log;
use Cake\Mailer\Email;
use Cake\Routing\Router;
use Cake\View\Helper\UrlHelper;
use App\Custom\RTI\DateTimeUtil;
use \DateTime;
use App\Custom\RTI\DebugUtil;

/**
 * Transportadoras Controller
 *
 * @property \App\Model\Table\TransportadorasTable $Transportadoras
 *
 * @method \App\Model\Entity\Transportadora[] paginate($object = null, array $settings = [])
 */
class TransportadorasController extends AppController
{

    protected $usuarioLogado = null;

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
        $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

        if ($usuarioAdministrador) {
            $this->usuarioLogado = $usuarioAdministrar;
        }

        if (!$this->security_util->checkUserIsAuthorized($this->usuarioLogado, 'AdminDeveloperProfileType')) {
            $this->Flash->error(Configure::read("messageNotAuthorized"));
        }

        $whereConditions = array();

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();

            $nomeFantasia = !empty($data["nome_fantasia"]) ? $data["nome_fantasia"] : null;
            $razaoSocial = !empty($data["razao_social"]) ? $data["razao_social"] : null;
            $cnpj = !empty($data["cnpj"]) ? $this->cleanNumber($data["cnpj"]) : null;

            $whereConditions = array(
                "nome_fantasia like '%{$nomeFantasia}%'",
                "razao_social like '%{$razaoSocial}%'",
                "cnpj like '%{$cnpj}%'"
            );
        }

        $transportadoras = $this->Transportadoras->findTransportadoras($whereConditions);

        $this->paginate($transportadoras, ['limit' => 10]);

        $this->set(compact('transportadoras'));
        $this->set('_serialize', ['transportadoras']);
    }

    /**
     * View method
     *
     * @param string|null $id Transportadora id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $transportadora = $this->Transportadoras->get($id, [
            'contain' => []
        ]);

        $this->set('transportadora', $transportadora);
        $this->set('_serialize', ['transportadora']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $transportadora = $this->Transportadoras->newEntity();
        // debug($transportadora);
        if ($this->request->is('post')) {
            $transportadora = $this->Transportadoras->patchEntity($transportadora, $this->request->getData());
            if ($this->Transportadoras->save($transportadora)) {
                $this->Flash->success(__(Configure::read('messageSavedSuccess')));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__(Configure::read('messageSavedError')));
        }
        $this->set(compact('transportadora'));
        $this->set('_serialize', ['transportadora']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Transportadora id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $transportadora = $this->Transportadoras->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $transportadora = $this->Transportadoras->patchEntity($transportadora, $this->request->getData());
            if ($this->Transportadoras->save($transportadora)) {
                $this->Flash->success(__('The transportadora has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The transportadora could not be saved. Please, try again.'));
        }
        $this->set(compact('transportadora'));
        $this->set('_serialize', ['transportadora']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Transportadora id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete()
    {
        $query = $this->request->query;

        $transportadora_id = $query['transportadora_id'];
        $return_url = $query['return_url'];

        $this->request->allowMethod(['post', 'delete']);
        $transportadora = $this->Transportadoras->get($transportadora_id);

        $this->TransportadorasHasUsuarios->deleteAllTransportadorasHasUsuariosByTransportadorasId($transportadora->id);

        if ($this->Transportadoras->delete($transportadora)) {
            $this->Flash->success(__(Configure::read('messageDeleteSuccess')));
        } else {
            $this->Flash->error(__(Configure::read('messageDeleteError')));
        }

        return $this->redirect(['action' => $return_url]);
    }

    /**
     * ------------------------------------------------------------
     * Métodos para Dashboard de Funcionário
     * ------------------------------------------------------------
     */

    /**
     * Action para listar transportadoras de um usuário
     * (pela dashboard de um Funcionário)
     *
     * @param int $usuarios_id Id de Usuários
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function transportadorasUsuario(int $usuarios_id)
    {
        try {
            $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
            $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

            $rede = $this->request->session()->read("Rede.Principal");

            if ($usuarioAdministrador) {
                $this->usuarioLogado = $usuarioAdministrar;
                $usuarioLogado = $this->usuarioLogado;
            }

            $usuario = $this->Usuarios->getUsuarioById($usuarios_id);

            $transportadora_has_usuario = $this
                ->TransportadorasHasUsuarios
                ->findTransportadorasHasUsuariosByUsuariosId($usuarios_id);

            if ($this->request->is(['post', 'put'])) {
                $data = $this->request->getData();

                $nomeFantasia = !empty($data["nome_fantasia"]) ? $data["nome_fantasia"] : null;
                $razaoSocial = !empty($data["razao_social"]) ? $data["razao_social"] : null;
                $cnpj = !empty($data["cnpj"]) ? $this->cleanNumber($data["cnpj"]) : null;

                $whereConditions = array(
                    "nome_fantasia like '%{$nomeFantasia}%'",
                    "razao_social like '%{$razaoSocial}%'",
                    "cnpj like '%{$cnpj}%'"
                );

                if (sizeof($whereConditions) > 0) {
                    $transportadora_has_usuario = $transportadora_has_usuario->where(
                        $whereConditions
                    );
                }
            }

            $transportadora_has_usuario = $this->paginate($transportadora_has_usuario);

            $arraySet = [
                'transportadora_has_usuario',
                'usuarios_id',
                'usuario',
                "usuarioLogado",
                "rede"
            ];

            $this->set(compact($arraySet));
            $this->set('_serialize', $arraySet);

        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao exibir transportadoras de usuário: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
        }
    }

    /**
     * Undocumented function
     *
     * @param integer $usuarios_id
     * @return void
     */
    public function adicionarTransportadoraUsuarioFinal(int $usuarios_id)
    {
        try {
            $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
            $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');
            $rede = $this->request->session()->read('Rede.Principal');

            if ($usuarioAdministrador) {
                $this->usuarioLogado = $usuarioAdministrar;
            }

            if (!is_null($usuarios_id)) {
                $usuario = $this->Usuarios->getUsuarioById($usuarios_id);
            } else {
                $usuario = $this->usuarioLogado;
            }

            $transportadora = $this->Transportadoras->newEntity();

            if ($this->request->is('post')) {
                $data = $this->request->getData();

                $transportadora = $this->Transportadoras->findTransportadoraByCNPJ($data['cnpj']);

                // Se não achou o veículo, significa que é novo registro
                if (!$transportadora) {
                    $transportadora = $this->Transportadoras->createUpdateTransportadora(
                        $data
                    );
                }

                // Com a Transportadora do Banco de Dados (ou novo) insere e faz vínculo
                if ($transportadora) {

                    $transportadora_has_usuario = $this->TransportadorasHasUsuarios->findTransportadorasHasUsuarios(
                        [
                            'usuarios_id' => $usuarios_id,
                            'transportadoras_id' => $transportadora->id
                        ]
                    )->first();

                    // se cliente ja tem a transportadora vinculada, dá mensagem de erro
                    // pois ele não pode ter dois registros do mesmo item
                    if ($transportadora_has_usuario) {
                        $this->Flash->error(__(Configure::read('messageTransporterAlreadyLinked')));

                        $url = Router::url(['controller' => 'Transportadoras', 'action' => 'adicionar_transportadora_usuario_final', $usuarios_id]);
                        return $this->response = $this->response->withLocation($url);
                    }

                    $transportadora_has_usuario = $this->TransportadorasHasUsuarios->addTransportadoraHasUsuario($transportadora->id, $usuario->id);

                    if ($transportadora_has_usuario) {
                        $this->Flash->success(__(Configure::read('messageSavedSuccess')));

                        $url = Router::url(['controller' => 'Transportadoras', 'action' => 'transportadorasUsuario', $usuarios_id]);
                        return $this->response = $this->response->withLocation($url);
                    }
                }
                $this->Flash->error(Configure::read('messageSavedError'));
            }

            $arraySet = [
                'transportadora',
                'usuario',
                'usuarioLogado',
                'usuarios_id',
                "rede"
            ];

            $this->set(compact($arraySet));
            $this->set('_serialize', [$arraySet]);
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao adicionar veículos para usuário: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * ------------------------------------------------------------
     * Relatórios (Dashboard de Admin RTI)
     * ------------------------------------------------------------
     */

    /**
     * Exibe Action de Relatório das Transportadoras de Usuários de cada Rede
     *
     * @return void
     */
    public function relatorioTransportadorasUsuariosRedes()
    {
        try {
            $redesList = $this->Redes->getRedesList();

            $whereConditions = array();

            $redesArrayIds = array();

            $redes = array();

            $qteRegistros = 10;

            foreach ($redesList as $key => $redeItem) {
                $redesArrayIds[] = $key;
            }

            if ($this->request->is(['post'])) {
                $data = $this->request->getData();

                if (strlen($data['redes_id']) > 0) {
                    $redesArrayIds = ['id' => $data['redes_id']];
                }

                $valorParametro = null;
                if ($data['opcoes'] == 'cnpj') {
                    $valorParametro = $this->cleanNumber($data['parametro']);
                } else {
                    $valorParametro = $data['parametro'];
                }

                $whereConditions[] =
                    [
                    $data['opcoes'] . ' like' => '%' . $valorParametro . '%'
                ];

                $qteRegistros = (int)$data['qte_registros'];

                $dataHoje = DateTimeUtil::convertDateToUTC((new DateTime('now'))->format('Y-m-d H:i:s'));

                // Data de Criação Início e Fim

                $dataInicialInsercao = strlen($data['auditInsertInicio']) > 0 ? DateTimeUtil::convertDateToUTC($data['auditInsertInicio'], 'd/m/Y') : null;
                $dataFinalInsercao = strlen($data['auditInsertFim']) > 0 ? DateTimeUtil::convertDateToUTC($data['auditInsertFim'], 'd/m/Y') : null;

                if (strlen($data['auditInsertInicio']) > 0 && strlen($data['auditInsertFim']) > 0) {

                    if ($dataInicialInsercao > $dataFinalInsercao) {
                        $this->Flash->error(__(Configure::read('messageDateRangeInvalid')));
                    } else if ($dataInicialInsercao > $dataHoje) {
                        $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid', 'Data de Início')));
                    } else {
                        $whereConditions[] = ['audit_insert BETWEEN "' . $dataInicialInsercao . '" and "' . $dataFinalInsercao . '"'];
                    }

                } else if (strlen($data['auditInsertInicio']) > 0) {

                    if ($dataInicialInsercao > $dataHoje) {
                        $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Início'));
                    } else {
                        $whereConditions[] = ['audit_insert >= ' => $dataInicialInsercao];
                    }

                } else if (strlen($data['auditInsertFim']) > 0) {

                    if ($dataFinalInsercao > $dataHoje) {
                        $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Fim'));
                    } else {
                        $whereConditions[] = ['audit_insert <= ' => $dataFinalInsercao];
                    }
                }

            // Monta o Array para apresentar em tela
            }

            foreach ($redesArrayIds as $key => $value) {
                $transportadorasConditions = $whereConditions;

                $redesHasClientesIds = array();

                $usuariosIds = array();

                $rede = $this->Redes->getRedeById((int)$value);

                $redeItem = array();

                $redeItem['id'] = $rede->id;
                $redeItem['nome_rede'] = $rede->nome_rede;
                $redeItem['usuarios'] = array();

                $unidades_ids = [];

                    // obtem os ids das unidades para saber quais brindes estão disponíveis
                foreach ($rede->redes_has_clientes as $key => $value) {
                    $unidades_ids[] = $value->clientes_id;
                }

                $redesConditions = [];

                $redesConditions[] = ['id' => $rede->id];

                $usuariosIdsQuery = $this->Usuarios->findAllUsuariosByRede($rede->id);

                $usuariosIdsArray = array();
                if (!is_null($usuariosIdsQuery)) {
                    $usuariosIdsArray = $usuariosIdsQuery->select(['id'])->toArray();
                }

                if (sizeof($usuariosIdsArray) > 0) {
                    $usuariosIds = array();

                    foreach ($usuariosIdsArray as $key => $usuario) {
                        $usuariosIds[] = $usuario->id;
                    }

                    $transportadorasHasUsuariosIdsQuery = $this->TransportadorasHasUsuarios->findTransportadorasHasUsuarios(
                        [
                            'usuarios_id in ' => $usuariosIds
                        ]
                    );


                    $transportadorasHasUsuariosIds = array();
                    foreach ($transportadorasHasUsuariosIdsQuery as $key => $transportadoraHasUsuario) {
                        $transportadorasHasUsuariosIds[] = $transportadoraHasUsuario->transportadoras_id;
                    }


                    if (sizeof($transportadorasHasUsuariosIds) > 0) {

                        $transportadorasConditions[] = [
                            'id in' => $transportadorasHasUsuariosIds
                        ];
                        $transportadoras = $this->Transportadoras->findTransportadoras(
                            $transportadorasConditions
                        )->toArray();
                        $redeItem['transportadoras'] = $transportadoras;
                    }
                }

                if (isset($redeItem['transportadoras']) && sizeof($redeItem['transportadoras']) > 0) {
                    array_push($redes, $redeItem);
                }
            }

            $arraySet = [
                'redesList',
                'redes'
            ];

            $this->set(compact($arraySet));

        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao exibir relatório de transportadoras: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * ------------------------------------------------------------
     * Métodos Comuns
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

        // $this->Auth->allow(['add', 'findTransportadoraByCNPJ']);
    }

    /**
     * Initialize function
     */
    public function initialize()
    {
        parent::initialize();

    }


    /**
     * ------------------------------------------------------------
     * AJAX Methods
     * ------------------------------------------------------------
     */

    /**
     * TransportadorasController::getTransportadoraByCNPJ
     *
     * Busca transportadora por CNPJ
     *
     * @param array $data["cnpj"] CNPJ de pesquisa
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 01/04/2018
     *
     * @return (json_encode) $result
     **/
    public function getTransportadoraByCNPJ()
    {
        // Dados de mensagem
        $mensagem = array();
        $message = null;
        $status = 1;
        $errors = array();

        try {
            if ($this->request->is('post')) {
                $data = $this->request->getData();
                $transportadora = $this->Transportadoras->findTransportadoraByCNPJ($data['cnpj']);

                $mensagem = array(
                    "status" => 1,
                    "message" => __(Configure::read("messageLoadDataWithSuccess")),
                    "errors" => array(),
                );

                if (!$transportadora) {
                    $mensagem = array(
                        "status" => 0,
                        "message" => __(Configure::read("messageLoadDataWithError")),
                        "errors" => array("Não foi encontrado Transportadora conforme CNPJ informado!"),
                    );
                }

                $arraySet = array("transportadora", "mensagem");

                $this->set(compact($arraySet));
                $this->set("_serialize", $arraySet);

                return;
            }
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $errors = $trace;
            $status = false;
            $messageString = __("Não foi possível obter dados de Transportadora pelo CNPJ informado!");

            $messageStringDebug = __("{0} - {1} em: {2}. [Função: {3} / Arquivo: {4} / Linha: {5}]  ", $messageString, $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            $mensagem = ['status' => false, 'message' => $messageString, 'errors' => $trace];

            Log::write("error", $messageStringDebug);
        }

        $mensagem = array("status" => $status, "message" => $message, "errors" => $errors);

        $arraySet = array("transportadora", "mensagem");

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);
    }
}
