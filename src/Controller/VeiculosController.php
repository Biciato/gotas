<?php
namespace App\Controller;

use \DateTime;
use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Routing\Router;
use App\Custom\RTI\Security;
use Cake\Event\Event;
use Cake\Log\Log;
use Cake\Mailer\Email;
use Cake\View\Helper\UrlHelper;
use App\Custom\RTI\DateTimeUtil;

/**
 * Veiculos Controller
 *
 * @property \App\Model\Table\VeiculosTable $Veiculos
 *
 * @method \App\Model\Entity\Veiculo[] paginate($object = null, array $settings = [])
 */
class VeiculosController extends AppController
{

    protected $usuarioLogado = null;

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $whereConditions = [];

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();

            $placa = !empty($data["placa"]) ? strtoupper($data["placa"]) : null;
            $modelo = !empty($data["modelo"]) ? $data["modelo"] : null;
            $fabricante = !empty($data["fabricante"]) ? $data["fabricante"] : null;
            $ano = !empty($data["ano"]) ? $data["ano"] : null;

            $whereConditions = array(
                "placa like '%{$placa}%'",
                "modelo like '%{$modelo}%'",
                "fabricante like '%{$fabricante}%'"
            );

            if (!empty($ano)) {
                $whereConditions[] = array("ano" => $ano);
            }
        }

        $veiculos = $this->Veiculos->findVeiculos($whereConditions);

        $veiculos = $this->paginate($this->Veiculos, ['limit' => 10]);

        $this->set(compact('veiculos'));
        $this->set('_serialize', ['veiculos']);
    }

    /**
     * View method
     *
     * @param string|null $id Veiculo id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $veiculo = $this->Veiculos->get($id, [
            'contain' => []
        ]);

        $this->set('veiculo', $veiculo);
        $this->set('_serialize', ['veiculo']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $veiculo = $this->Veiculos->newEntity();
        if ($this->request->is('post')) {
            $veiculo = $this->Veiculos->patchEntity($veiculo, $this->request->getData());
            if ($this->Veiculos->save($veiculo)) {
                $this->Flash->success(__('The veiculo has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The veiculo could not be saved. Please, try again.'));
        }
        $this->set(compact('veiculo'));
        $this->set('_serialize', ['veiculo']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Veiculo id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $veiculo = $this->Veiculos->get($id, [
            'contain' => ["UsuariosHasVeiculos.Usuarios"]
        ]);

        $usuario = $veiculo["usuarios_has_veiculos"][0]["usuario"];

        if ($this->request->is(['patch', 'post', 'put'])) {
            $veiculo = $this->Veiculos->patchEntity($veiculo, $this->request->getData());
            if ($this->Veiculos->save($veiculo)) {
                $this->Flash->success(__('The veiculo has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The veiculo could not be saved. Please, try again.'));
        }

        $arraySet = array("veiculo", "usuario");

        $this->set(compact($arraySet));
        $this->set('_serialize', $arraySet);
    }

    /**
     * Delete method
     *
     * @param string|null $id Veiculo id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete()
    {
        $query = $this->request->query;

        $veiculos_id = $query['veiculos_id'];
        $return_url = $query['return_url'];
        $this->request->allowMethod(['post', 'delete']);
        $veiculo = $this->Veiculos->get($veiculos_id);
        if ($this->Veiculos->delete($veiculo)) {
            $this->Flash->success(__(Configure::read('messageDeleteSuccess')));
        } else {
            $this->Flash->error(__(Configure::read('messageDeleteError')));
        }

        return $this->redirect(['action' => $return_url]);
    }

    /**
     * Método Add para adicionar veículo de usuário logado
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function adicionarVeiculo(int $usuarios_id = null)
    {
        try {
            // se o usuário que estiver cadastrando for um cliente final
            // o id será nulo, senão será o funcionário

            $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
            $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

            if ($usuarioAdministrador) {
                $this->usuarioLogado = $usuarioAdministrar;
                $usuarioLogado = $usuarioAdministrar;
            }

            if (!is_null($usuarios_id)) {
                $usuario = $this->Usuarios->getUsuarioById($usuarios_id);
            } else {
                $usuario = $this->usuarioLogado;
            }

            $veiculo = $this->Veiculos->newEntity();

            if ($this->request->is('post')) {
                $data = $this->request->getData();

                $veiculo = $this->Veiculos->getVeiculoByPlaca($data['placa']);

                $veiculo = $veiculo["veiculo"];
                // Se não achou o veículo, significa que é novo registro
                if (!$veiculo) {
                    $veiculo = $this->Veiculos->saveUpdateVeiculo(
                        null,
                        $data['placa'],
                        $data['modelo'],
                        $data['fabricante'],
                        $data['ano']
                    );
                }

                // debug($veiculo);
                // Com o Veículo do Banco de Dados (ou novo) insere e faz vínculo
                if ($veiculo) {
                    $usuario_has_veiculo = $this->UsuariosHasVeiculos->addUsuarioHasVeiculo($veiculo->id, $usuario->id);

                    if ($usuario_has_veiculo) {
                        $this->Flash->success(__('O registro foi inserido.'));

                        if (is_null($usuarios_id)) {
                            return $this->redirect(['action' => 'meus_veiculos']);
                        } else {
                            return $this->redirect(['action' => 'veiculos_usuario', $usuario->id]);
                        }
                    }
                }
                $this->Flash->error(__('O veículo não pode ser gravado.'));
            }
            $this->set(compact('veiculo', 'usuario', 'usuarioLogado'));
            $this->set('_serialize', ['veiculo', 'usuario', 'usuarioLogado']);
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao adicionar veículos para usuário: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Action para listar veículos de um usuário
     *
     * @return void
     **/
    public function meusVeiculos()
    {
        $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
        $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

        if ($usuarioAdministrador) {
            $usuarioLogado = $usuarioAdministrar;
        }

        $usuario = $this->Usuarios->getUsuarioById($this->usuarioLogado['id']);

        $usuariosHasVeiculos
            = $this->UsuariosHasVeiculos->getVeiculoByUsuarioId($usuario->id);

        $this->paginate($usuariosHasVeiculos);

        if ($this->request->is(['post', 'put'])) {
            $param = $this->request->getData();

            if (strlen($param['placa'] > 0)) {
                $usuariosHasVeiculos = $usuariosHasVeiculos->where(['placa' => $param['placa']]);
            }
        }

        $arraySet = [
            'usuario',
            'usuarioLogado',
            'usuariosHasVeiculos'
        ];

        $this->set(compact($arraySet));
        $this->set('_serialize', $arraySet);
    }

    /**
     * Action para listar veículos de um usuário
     *
     * @param int $usuarios_id Id de Usuários
     *
     * @return void
     */
    public function veiculosUsuario(int $usuarios_id)
    {
        $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
        $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');
        $rede = $this->request->session()->read("Rede.Grupo");

        if ($usuarioAdministrador) {
            $usuarioLogado = $usuarioAdministrar;
        }

        $usuario = $this->Usuarios->getUsuarioById($usuarios_id);

        $usuariosHasVeiculos
            = $this->UsuariosHasVeiculos->getVeiculoByUsuarioId($usuarios_id);

        if ($this->request->is(['post', 'put'])) {
            $param = $this->request->getData();

            if (strlen($param['placa'] > 0)) {
                $usuariosHasVeiculos = $usuariosHasVeiculos->where(
                    [
                        'placa' => $param['placa']
                    ]
                );
            }
        }

        $this->paginate($usuariosHasVeiculos);

        $arraySet = array(
            'usuariosHasVeiculos',
            'usuarios_id',
            'usuario',
            'usuarioLogado',
            "rede"
        );

        $this->set(compact($arraySet));
        $this->set('_serialize', $arraySet);
    }


    /**
     * ------------------------------------------------------------
     * Métodos para Dashboard de Funcionário
     * ------------------------------------------------------------
     */

    /**
     * Action para listar veículos de um usuário
     * (pela dashboard de um Funcionário)
     *
     * @param int $usuarios_id Id de Usuários
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function veiculosUsuarioFinal(int $usuarios_id)
    {
        try {

            $usuario = $this->Usuarios->getUsuarioById($usuarios_id);

            $usuariosHasVeiculos
                = $this->UsuariosHasVeiculos->getVeiculoByUsuarioId($usuarios_id);

            if ($this->request->is(['post', 'put'])) {
                $param = $this->request->getData();

                if (strlen($param['placa'] > 0)) {
                    $usuariosHasVeiculos = $usuariosHasVeiculos->where(
                        [
                            'placa' => $param['placa']
                        ]
                    );
                }
            }

            $this->paginate($usuariosHasVeiculos);

            $this->set(compact(['usuariosHasVeiculos', 'usuarios_id', 'usuario']));
            $this->set('_serialize', ['usuariosHasVeiculos', 'usuarios_id', 'usuario']);
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao exibir veículos de usuário: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Método Add para adicionar veículo de usuário logado
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function adicionarVeiculoUsuarioFinal(int $usuarios_id = null)
    {
        try {
            // se o usuário que estiver cadastrando for um cliente final
            // o id será nulo, senão será o funcionário

            $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
            $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

            if ($usuarioAdministrador) {
                $this->usuarioLogado = $usuarioAdministrar;
            }

            if (!is_null($usuarios_id)) {
                $usuario = $this->Usuarios->getUsuarioById($usuarios_id);
            } else {
                $usuario = $this->usuarioLogado;
            }

            $veiculo = $this->Veiculos->newEntity();

            if ($this->request->is('post')) {
                $data = $this->request->getData();

                $veiculo = $this->Veiculos->getVeiculoByPlaca($data['placa']);

                $veiculo = $veiculo["veiculo"];
                // Se não achou o veículo, significa que é novo registro
                if (!$veiculo) {
                    $veiculo = $this->Veiculos->saveUpdateVeiculo(
                        null,
                        $data['placa'],
                        $data['modelo'],
                        $data['fabricante'],
                        $data['ano']
                    );
                }

                // Com o Veículo do Banco de Dados (ou novo) insere e faz vínculo
                if ($veiculo) {

                    $usuario_has_veiculo = $this->UsuariosHasVeiculos->findUsuariosHasVeiculos(
                        [
                            'usuarios_id' => $usuarios_id,
                            'veiculos_id' => $veiculo->id
                        ]
                    )->first();

                    // se cliente ja tem o veículo vinculado, dá mensagem de erro
                    // pois ele não pode ter dois registros do mesmo item
                    if ($usuario_has_veiculo) {
                        $this->Flash->error(__(Configure::read('messageVeiculoAlreadyLinked')));

                        $url = Router::url(['controller' => 'Veiculos', 'action' => 'adicionarVeiculoUsuarioFinal', $usuarios_id]);
                        return $this->response = $this->response->withLocation($url);
                    }

                    $usuario_has_veiculo = $this->UsuariosHasVeiculos->addUsuarioHasVeiculo($veiculo->id, $usuario->id);

                    if ($usuario_has_veiculo) {
                        $this->Flash->success(__(Configure::read('messageSavedSuccess')));

                        $url = Router::url(['controller' => 'Veiculos', 'action' => 'veiculos_usuario_final', $usuarios_id]);
                        return $this->response = $this->response->withLocation($url);
                    }
                }
                $this->Flash->error(__('O veículo não pode ser gravado.'));
            }

            $array_set = [
                'veiculo',
                'usuario',
                'usuarioLogado',
                'usuarios_id'
            ];

            $this->set(compact($array_set));
            $this->set('_serialize', [$array_set]);
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
     * Exibe Action de Relatório dos Veículos de Usuários de cada Rede
     *
     * @return void
     */
    public function relatorioVeiculosUsuariosRedes()
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
                $valorParametro = $data['parametro'];

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
                $veiculosConditions = $whereConditions;

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

                    $veiculosHasUsuariosIdsQuery = $this->UsuariosHasVeiculos->findUsuariosHasVeiculos(
                        [
                            'usuarios_id in ' => $usuariosIds
                        ]
                    );

                    $veiculosHasUsuariosIds = array();
                    foreach ($veiculosHasUsuariosIdsQuery as $key => $veiculoHasUsuario) {
                        $veiculosHasUsuariosIds[] = $veiculoHasUsuario->veiculos_id;
                    }


                    if (sizeof($veiculosHasUsuariosIds) > 0) {

                        $veiculosConditions[] = [
                            'id in' => $veiculosHasUsuariosIds
                        ];
                        $veiculos = $this->Veiculos->findVeiculos(
                            $veiculosConditions
                        )->toArray();
                        $redeItem['veiculos'] = $veiculos;
                    }
                }

                if (isset($redeItem['veiculos']) && sizeof($redeItem['veiculos']) > 0) {
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

            $stringError = __("Erro ao exibir relatório de veículos: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
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
     * Ajax Methods
     * ------------------------------------------------------------
     */

    /**
     * Busca veículo por placa
     *
     * @return (entity\veiculos) $veiculo
     **/
    public function getVeiculoByIdAPI()
    {
        $mensagem = array();

        try {
            if ($this->request->is(['post', 'put'])) {
                $data = $this->request->getData();


                $veiculosId = empty($data["id"]) ? null : $data["id"];

                if (empty($veiculosId)) {
                    $mensagem = array(
                        "status" => 0,
                        "message" => Configure::read("messageLoadDataWithError"),
                        "errors" => array(Configure::read("messageVeiculoIdEmpty"))
                    );
                    $veiculo = array(
                        "data" => null
                    );

                    $arraySet = array("mensagem", "veiculo");
                    $this->set(compact($arraySet));
                    $this->set("_serialize", $arraySet);

                    return;
                }

                $veiculo = $this->Veiculos->getVeiculoById($veiculosId);

                if (empty($veiculo)) {
                    $mensagem = array(
                        "status" => 0,
                        "message" => Configure::read("messageLoadDataWithError"),
                        "errors" => array(Configure::read("messageRecordNotFound"))
                    );
                } else {
                    $mensagem = array(
                        "status" => 1,
                        "message" => Configure::read("messageLoadDataWithSuccess"),
                        "errors" => array()
                    );
                }

                $arraySet = array("mensagem", "veiculo");
                $this->set(compact($arraySet));
                $this->set("_serialize", $arraySet);

                return;
            }
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $messageString = __("Não foi possível obter dados de cupons do usuário!");

            $mensagem = ['status' => false, 'message' => $messageString, 'errors' => $trace];
        }

        $arraySet = ['veiculo', 'mensagem'];

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);
    }

    /**
     * Busca veículo por placa
     *
     * @return (entity\veiculos) $veiculo
     **/
    public function getVeiculoByPlacaAPI()
    {
        $mensagem = [];

        try {
            if ($this->request->is(['post', 'put'])) {
                $data = $this->request->getData();

                $resultado = $this->Veiculos->getVeiculoByPlaca($data['placa']);
                $mensagem = $resultado["mensagem"];
                $veiculo = $resultado["veiculo"];
            }
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $messageString = __("Não foi possível obter dados de cupons do usuário!");

            $mensagem = ['status' => false, 'message' => $messageString, 'errors' => $trace];
        }

        $arraySet = ['veiculo', 'mensagem'];

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);
    }

}
