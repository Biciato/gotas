<?php

namespace App\Controller;

use App\Controller\AppController;
use App\Model\Entity;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Event\Event;
use App\Custom\RTI\Security;
use Aura\Intl\Exception;
use \DateTime;
use App\Custom\RTI\DateTimeUtil;
use App\Custom\RTI\FilesUtil;
use App\Custom\RTI\ImageUtil;
use App\Custom\RTI\DebugUtil;
use App\Custom\RTI\ResponseUtil;
use App\Custom\RTI\NumberUtil;
use App\Custom\RTI\StringUtil;

/**
 * Clientes Controller
 *
 * @property \App\Model\Table\ClientesTable $Clientes
 *
 * @method \App\Model\Entity\Cliente[] paginate($object = null, array $settings = [])
 */
class ClientesController extends AppController
{
    protected $usuarioLogado = null;
    protected $security = null;


    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $conditions = [];

        if ($this->request->is('post')) {

            $data = $this->request->getData();

            if (sizeof($data) > 0) {
                if ($data['opcoes'] == 'cnpj') {
                    $value = $this->cleanNumber($data['parametro']);
                } else {
                    $value = $data['parametro'];
                }

                array_push($conditions, [$data['opcoes'] . ' like ' => '%' . $value . '%']);
            }
        }

        $clientes = $this->Clientes->getAllClientes($conditions);
        $this->paginate($clientes, ['limit' => 10]);


        // $clientes = $this->paginate($this->Clientes->find()->where(['matriz_id IS' => null]));

        $this->set(compact('clientes'));
        $this->set('_serialize', ['clientes']);
    }

    /**
     * Ativa um registro
     *
     * @return boolean
     */
    public function ativar()
    {
        $query = $this->request->query();

        $this->_alteraEstadoCliente((int) $query['clientes_id'], true, $query['return_url']);
    }

    /**
     * Desativa um registro
     *
     * @return boolean
     */
    public function desativar()
    {
        $query = $this->request->query();

        $this->_alteraEstadoCliente((int) $query['clientes_id'], false, $query['return_url']);
    }

    /**
     * Undocumented function
     *
     * @param int $clientes_id
     * @param bool $estado
     * @return void
     */
    private function _alteraEstadoCliente(int $clientes_id, bool $estado, array $return_url)
    {
        try {

            $this->request->allowMethod(['post']);

            $result = $this->Clientes->changeStateEnabledCliente($clientes_id, $estado);
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
            $stringError = __("Erro ao realizar procedimento de alteração de estado de cliente: {0} em: {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Exibe o cadastro da rede
     *
     * TODO: CONFERIR se realmente não é usado
     * @deprecated 1.0?
     */
    public function dadosMinhaRede()
    {
        die("Tela não mais em uso!");
        $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
        $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

        if ($usuarioAdministrador) {
            $this->usuarioLogado = $usuarioAdministrar;
        }

        $cliente = $this->Clientes->getClienteMatrizLinkedToUsuario($this->usuarioLogado);

        $clienteAdministrar = $this->request->session()->read('Rede.PontoAtendimento');

        if (!is_null($clienteAdministrar)) {
            $cliente = $clienteAdministrar;
        }

        $filiais = $this->Clientes->getClienteFiliais($cliente['id']);

        $conditions = [];

        if ($this->request->is('post')) {
            $data = $this->request->getData();

            if (sizeof($data) > 0) {
                if ($data['opcoes'] == 'cnpj') {
                    $value = $this->cleanNumber($data['parametro']);
                } else {
                    $value = $data['parametro'];
                }

                array_push($conditions, [$data['opcoes'] . ' like ' => '%' . $value . '%']);
            }
        }

        $filiais = $this->Clientes->getClienteFiliais($cliente->id, $conditions);

        $this->paginate($filiais, ['limit' => 10]);

        $this->set('filiais', $filiais);

        $this->set(compact('cliente'));
        $this->set('_serialize', ['cliente']);
    }

    /**
     * View method
     *
     * @param string|null $id Cliente id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function verDetalhes($id = null)
    {
        $cliente = $this->Clientes->getClienteById($id);

        $this->set('cliente', $cliente);
        $this->set('_serialize', ['cliente']);
    }

    /**
     * Adiciona uma loja ou posto
     * @param int $redes_id Id de rede
     * @author Gustavo Souza Gonçalves
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     */
    public function adicionar(int $redes_id = null)
    {
        $arraySet = array('cliente', 'clientes', 'rede', "redesId", 'usuarioLogado');

        $cliente = $this->Clientes->newEntity();
        $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
        $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

        if ($usuarioAdministrador) {
            $this->usuarioLogado = $usuarioAdministrar;
        }

        $usuarioLogado = $this->usuarioLogado;
        $rede = $this->Redes->getRedeById($redes_id);

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $cliente = $this->Clientes->patchEntity($cliente, $data);

            // Verifica se ja tem um registro antes
            $cnpj = !empty($cliente["cnpj"]) ? NumberUtil::limparFormatacaoNumeros($cliente["cnpj"]) : null;

            if ($cnpj) {
                $clienteJaExistente = $this->Clientes->getClienteByCNPJ($cnpj);

                if ($clienteJaExistente) {
                    $message = __("Este CNPJ já está cadastrado! Cliente Cadastrado com o CNPJ: {0}, Nome Fantasia: {1}, Razão Social: {2}", NumberUtil::formatarCNPJ($clienteJaExistente["cnpj"]), $clienteJaExistente["nome_fantasia"], $clienteJaExistente["razao_social"]);
                    
                    $this->Flash->error($message);
                    $this->set(compact($arraySet));
                    $this->set('_serialize', $arraySet);
                    return;
                }
            }

            if ($cliente = $this->Clientes->addClient($redes_id, $cliente)) {
                $qteTurnos = $data["quantidade_turnos"];
                $horarioInicial = $data["horario"];
                $horarios = $this->calculaTurnos($qteTurnos, $horarioInicial);
                $this->ClientesHasQuadroHorario->addHorariosCliente($redes_id, $cliente["id"], $horarios);

                // Adiciona bonificação extra sefaz para novo posto
                $this->Gotas->saveUpdateBonificacaoExtraSefaz([$cliente->id], $rede->qte_gotas_bonificacao);

                $this->Flash->success(__("Registro gravado com sucesso."));

                return $this->redirect(
                    [
                        'controller' => 'redes',
                        'action' => 'ver_detalhes',
                        $rede->id
                    ]
                );
            }

            $this->Flash->error(__("Não foi possível gravar o registro!"));
        }
        $clientes = $this->Clientes->find('list', ['limit' => 200]);

        $this->set(compact($arraySet));
        $this->set('_serialize', $arraySet);
    }

    /**
     * Edit method
     *
     * @param string|null $id Cliente id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function editar($id = null)
    {
        try {
            $arraySet = array('cliente', "redesId");

            $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
            $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

            if ($usuarioAdministrador) {
                $this->usuarioLogado = $usuarioAdministrar;
            }

            $cliente = $this->Clientes->getClienteById($id);
            $redesId = $cliente["rede_has_cliente"]["redes_id"];

            // Monta o quadro de horários
            $quantidadeTurnos = sizeof($cliente["clientes_has_quadro_horarios"]);
            $turnoInicial = null;

            if ($quantidadeTurnos > 0) {
                $turnoInicial = $cliente["clientes_has_quadro_horarios"][0]["horario"]->format("H:i");
            }

            $cliente["quantidade_turnos"] = $quantidadeTurnos;
            $cliente["horario"] = $turnoInicial;

            if ($this->request->is(['patch', 'post', 'put'])) {
                $data = $this->request->getData();

                // DebugUtil::print($data);
                $cliente = $this->Clientes->patchEntity($cliente, $data);

                // $cliente = $this->Clientes->patchEntity($cliente, $this->request->getData());

                $cnpj = !empty($cliente["cnpj"]) ? NumberUtil::limparFormatacaoNumeros($cliente["cnpj"]) : null;

                if ($cnpj) {
                    $clienteJaExistente = $this->Clientes->getClienteByCNPJ($cnpj);

                    if ($clienteJaExistente && $clienteJaExistente["id"] != $cliente["id"]) {

                        $message = __("Este CNPJ já está cadastrado! Cliente Cadastrado com o CNPJ: {0}, Nome Fantasia: {1}, Razão Social: {2}", NumberUtil::formatarCNPJ($clienteJaExistente["cnpj"]), $clienteJaExistente["nome_fantasia"], $clienteJaExistente["razao_social"]);
                        $this->Flash->error($message);

                        $this->set(compact($arraySet));
                        $this->set('_serialize', $arraySet);
                        return;
                    }
                }


                if ($this->Clientes->updateClient($cliente)) {

                    /** Atualização do quadro de horarios
                     * Se o primeiro horário e a quantidade de turno for diferente,
                     * apaga todos e grava novamente
                     */

                    $novaQteTurnos = $data["quantidade_turnos"];
                    $novoTurno = $data["horario"];

                    if (($novaQteTurnos != $quantidadeTurnos) || ($novoTurno != $turnoInicial)) {

                        $horarios = $this->calculaTurnos($novaQteTurnos, $novoTurno);

                        $resultDisable = $this->ClientesHasQuadroHorario->disableHorariosCliente($cliente["id"]);

                        $status = $this->ClientesHasQuadroHorario->addHorariosCliente($redesId, $cliente["id"], $horarios);
                    }

                    $this->Flash->success(__('O registro foi atualizado com sucesso.'));

                    return $this->redirect(
                        [
                            'controller' => 'redes',
                            'action' => 'ver_detalhes',
                            $cliente->rede_has_cliente->redes_id
                        ]
                    );
                }
                $this->Flash->error(__('O registro não pode ser atualizado.'));

                $this->Flash->error($result['message']);
            }

            $this->set(compact($arraySet));
            $this->set('_serialize', $arraySet);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao obter cupom fiscal para consulta: {0} em: {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Delete method
     *
     * @param string|null $id Cliente id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete()
    {
        $data = $this->request->query();
        $cliente_id = $data['cliente_id'];
        $return_url = $data['return_url'];

        $this->request->allowMethod(['post', 'delete']);
        $cliente = $this->Clientes->get($cliente_id);
        if ($this->Clientes->delete($cliente)) {
            $this->Flash->success(__('O registro {0} foi removido com sucesso.', $cliente->nome_fantasia));
        } else {
            $this->Flash->error(__('The cliente could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Gerencia um colaborador
     *
     * @param int $id Id do colaborador (Funcionário)
     *
     * @deprecated 1.0 / Não é mais usado
     * @return void
     */
    public function gerenciarColaborador(int $id = null)
    {
        // TODO: conferir se é usado
        try {
            $profileTypes = Configure::read('profileTypes');

            $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
            $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

            if ($usuarioAdministrador) {
                $this->usuarioLogado = $usuarioAdministrar;
            }

            $cliente = $this->Clientes->getClienteMatrizLinkedToUsuario($this->usuarioLogado);

            $loja = $this->Clientes->getClienteById($id);

            if ($cliente['id'] != $loja['id']) {
                $cliente = $loja;
            }

            $conditions = [];

            if ($this->request->is(['post', 'put'])) {
                $data = $this->request->getData();

                if (isset($data['opcoes'])) {
                    if ($data['opcoes'] == 'cpf') {
                        $value = $this->cleanNumber($data['parametro']);
                    } else {
                        $value = $data['parametro'];
                    }

                    array_push(
                        $conditions,
                        [
                            'Usuarios.' . $data['opcoes'] . ' like' => '%' . $value . '%'
                        ]
                    );
                }

                $params = $this->request->query;

                if (sizeof($params) > 0) {
                    $matrizId = $params['matriz_id'];
                    $clienteId = $params['cliente_id'];
                    $usuarioId = $params['usuario_id'];
                    $action = $params['action'];

                    $usuarioToManage = $this->Usuarios->getUsuarioById($usuarioId);

                    if ($action == 'add') {
                        $this->ClientesHasUsuarios->addNewClienteHasUsuario($matrizId, $clienteId, $usuarioId);
                        $this->Flash->success("Usuário atribuído com sucesso.");

                        Log::write(
                            'info',
                            __(
                                "Usuário [({0}) - {1}] adicionou operador [({2}) ({3})] como colaborador na empresa [({4})({5})]",
                                $this->usuarioLogado['id'],
                                $this->usuarioLogado['nome'],
                                $usuarioToManage->id,
                                $usuarioToManage->nome,
                                $cliente->id,
                                $cliente->razao_social
                            )
                        );
                    } elseif ($action == 'remove') {
                        $this->ClientesHasUsuarios->removeClienteHasUsuario($matrizId, $clienteId, $usuarioId);
                        $this->Flash->success("Usuário removido com sucesso.");

                        Log::write(
                            'info',
                            __(
                                "Usuário [({0}) - {1}] removeu operador [({2}) ({3})] como colaborador na empresa [({4})({5})]",
                                $this->usuarioLogado['id'],
                                $this->usuarioLogado['nome'],
                                $usuarioToManage->id,
                                $usuarioToManage->nome,
                                $cliente->id,
                                $cliente->razao_social
                            )
                        );
                    }

                    $this->redirect(['action' => 'gerenciar_colaborador', $cliente->id]);
                }
            }
            $usuarios
                = $this->Usuarios->getUsuariosAssociatedWithClient(
                    $cliente,
                    $profileTypes['ManagerProfileType'],
                    $profileTypes['WorkerProfileType'],
                    $conditions
                );

            $this->paginate(
                $usuarios,
                [
                    'limit' => 10
                ]
            );

            $this->set('usuarioLogado', $this->usuarioLogado);
            $this->set('cliente', $cliente);
            $this->set('usuarios', $usuarios);
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $message = __(
                "Não foi possível realizar o procedimento. Entre em contato com o suporte. Descrição do erro: {0} em: {1}",
                $e->getMessage(),
                $trace[1]
            );
            $this->Flash->error($message);

            Log::write('error', $message);
        }
    }

    /**
     * Exibe os dados de um cliente (local de abastecimento) para um usuário
     *
     * @param integer $pontuacao_comprovante_id Id de Comprovante da Pontuação (nele tem todos os dados)
     *
     * @return void
     */
    public function dadosClienteAtendimentoUsuario(int $pontuacao_comprovante_id)
    {
        $pontuacao_comprovante = $this->PontuacoesComprovantes->getCouponById($pontuacao_comprovante_id);

        $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
        $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

        if ($usuarioAdministrador) {
            $this->usuarioLogado = $usuarioAdministrar;
        }

        $usuarioLogado = $this->usuarioLogado;

        $cliente = $this->Clientes->getClienteById($pontuacao_comprovante->cliente->id);
        $usuarios_id = $pontuacao_comprovante->usuarios_id;

        $arraySet = ['cliente', 'usuarioLogado', 'usuarios_id'];

        $this->set(compact($arraySet));
        $this->set('_serialize', $arraySet);
    }

    /**
     * Configura propaganda para a unidade de atendimento
     *
     * @return void
     */
    public function configurarPropaganda(int $clientesId = null)
    {
        try {

            $sessaoUsuario = $this->getSessionUserVariables();

            $usuarioAdministrador = $sessaoUsuario["usuarioAdministrador"];
            $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"];

            if ($usuarioAdministrador) {
                $this->usuarioLogado = $usuarioAdministrar;
                $usuarioLogado = $usuarioAdministrar;
            }

            // Se usuário não tem acesso, redireciona
            if (!$this->securityUtil->checkUserIsAuthorized($this->usuarioLogado, "AdminNetworkProfileType", "AdminRegionalProfileType")) {
                $this->securityUtil->redirectUserNotAuthorized($this);
            }

            $clientesId = empty($clientesId) ? $sessaoUsuario["cliente"]["id"] : $clientesId;
            $cliente = $this->Clientes->getClienteById($clientesId);
            $imagem = sprintf("/%s%s%s", PATH_WEBROOT, PATH_IMAGES_CLIENTES, $cliente->propaganda_img);
            // $imagem = __("{0}{1}{2}", Configure::read("webrootAddress"), Configure::read("imageClientPathRead"), $cliente["propaganda_img"]);
            $imagemExistente = !empty($cliente->propaganda_img);
            $imagemOriginal = null;

            if (strlen($cliente["propaganda_img"]) > 0) {
                // O caminho tem que ser pelo cliente, pois a mesma imagem será usada para todas as unidades
                $imagemOriginal = __("{0}{1}", Configure::read("imageClientPath"), $cliente["propaganda_img"]);
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
                    $propagandaImg = StringUtil::gerarNomeArquivoAleatorio();
                    $propagandaImg = $propagandaImg["fileName"];

                    // Verifica se já tem este nome gerado na base
                    while (!empty($idClientePropaganda = $this->Clientes->getClienteByImage($propagandaImg))) {
                        $propagandaImg = StringUtil::gerarNomeArquivoAleatorio();
                    }

                    $imagemOrigem = __("{0}{1}", PATH_IMAGES_CLIENTES_TEMP, $data["img-upload"]);

                    $imagemDestino = __("{0}{1}", PATH_IMAGES_CLIENTES, $propagandaImg);
                    // $resizeSucesso = ImageUtil::resizeImage($imagemOrigem, 600, 600, $valueX, $valueY, $width, $height, 90);
                    $resizeSucesso = ImageUtil::resizeImage($imagemOrigem, $width, $height, $valueX, $valueY, $width, $height, 90);

                    // Se imagem foi redimensionada, move e atribui o nome para gravação
                    if ($resizeSucesso == 1) {
                        rename($imagemOrigem, $imagemDestino);
                        $data["propaganda_img"] = $propagandaImg;

                        $trocaImagem = 1;
                    }
                }

                $cliente = $this->Redes->patchEntity($cliente, $data);

                if ($this->Clientes->updateClient($cliente)) {

                    $this->Flash->success(MESSAGE_SAVED_SUCCESS);

                    if (
                        $this->usuarioLogado["tipo_perfil"] >= PROFILE_TYPE_ADMIN_DEVELOPER
                        && $this->usuarioLogado["tipo_perfil"] <= PROFILE_TYPE_ADMIN_REGIONAL
                    ) {
                        return $this->redirect(array("controller" => "RedesHasClientes", 'action' => 'propagandaEscolhaUnidades'));
                    } else if ($this->usuarioLogado["tipo_perfil"] >= PROFILE_TYPE_ADMIN_LOCAL) {
                        return $this->redirect(array("controller" => "Pages", 'action' => 'display'));
                    }
                }
                $this->Flash->error(__(Configure::read('messageSavedError')));
            }

            $propaganda = $cliente;
            $arraySet = array("cliente", "imagem", "usuarioLogado", "imagemExistente", "propaganda");

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
     * ClientesController::calculaTurnos
     *
     * Calcula os turnos cadastrados para a unidade
     *
     * @param int $qteTurnos Quantidade de turnos
     * @param string $horaMinutoTurno Hora e Minuto do Turno
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 31/12/2018
     *
     * @return array
     */
    private function calculaTurnos($qteTurnos, $horaMinutoTurno)
    {
        $horaMinutoTurno = explode(":", $horaMinutoTurno);
        $hora = intval($horaMinutoTurno[0]);
        $minuto = intval($horaMinutoTurno[1]);
        $divisao = 24 / $qteTurnos;
        $turnos = array();
        $horaTemp = $hora;

        for ($i = 0; $i < $qteTurnos; $i++) {
            $turno = array();

            // $turno["id"] = $i;
            $turno["hora"] = strlen($horaTemp) == 1 ? "0" . $horaTemp : $horaTemp;
            $turno["minuto"] = strlen($minuto) == 1 ? "0" . $minuto : $minuto;

            $horaTurno = $horaTemp + $divisao;

            $horaTurno = $horaTurno > 23 ? $horaTurno - 24 : $horaTurno;

            $horaTemp = $horaTurno;
            $turnos[] = $turno;
        }

        return $turnos;
    }

    /**
     * ------------------------------------------------------------
     * Métodos REST
     * ------------------------------------------------------------
     */

    /**
     * ClientesController::getClientesListAPI
     *
     * Obtem lista de Clientes
     *
     * @param int $redesId Id da Rede (opcional)
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2018-09-10
     *
     * @return json_encode
     */
    public function getClientesListAPI()
    {
        $sessao = $this->getSessionUserVariables();
        $rede = $sessao["rede"];
        $redesId = $rede["id"];

        // Caso o método seja chamado via post
        if ($this->request->is("post")) {
            $data = $this->request->getData();

            if (!empty($data["redesId"])) {
                $redesId = $data["redesId"];
            }
        }

        $selectList = array(
            "Clientes.id",
            "Clientes.nome_fantasia",
            "Clientes.razao_social",
            "Clientes.propaganda_img"
        );

        // @todo Gustavo, se a redesId for nulo, não pode retornar ninguém!
        $redeHasClientes = $this->RedesHasClientes->getRedesHasClientesByRedesId($redesId, array(), $selectList);

        $clientes = array();

        foreach ($redeHasClientes as $redeHasCliente) {
            $clientes[] = $redeHasCliente["cliente"];
        }

        if (sizeof($clientes) > 0) {
            ResponseUtil::success($clientes);
        } else {
            ResponseUtil::error(Configure::read("messageLoadDataNotFound"), Configure::read("messageWarningDefault"));
        }
    }

    public function getPostoFuncionarioAPI()
    {
        try {
            if ($this->request->is("GET")) {
                $usuario = $this->Auth->user();

                if ($usuario["tipo_perfil"] <= PROFILE_TYPE_WORKER) {
                    $posto = $this->ClientesHasUsuarios->findClienteHasUsuario(array("usuarios_id" => $usuario["id"]));
                } else {
                    $posto = null;
                }

                if (!empty($posto) && !empty($posto["cliente"])) {
                    $posto = $posto["cliente"];

                    return ResponseUtil::successAPI(MESSAGE_LOAD_DATA_WITH_SUCCESS, array("cliente" => $posto));
                } else {
                    $errors = array();
                    $errors[] = $usuario["tipo_perfil"] <= PROFILE_TYPE_WORKER ? MESSAGE_USUARIO_WORKER_NOT_ASSOCIATED_CLIENTE : MESSAGE_USUARIO_CANT_SEARCH;

                    return ResponseUtil::errorAPI(MESSAGE_LOAD_DATA_NOT_FOUND, $errors);
                }
            }
        } catch (\Exception $e) {
            $trace = $e->getTraceAsString();
            $messageString = __("Erro ao obter dados do posto!");

            $messageStringDebug = __("{0} - {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $messageString, $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write("error", $messageStringDebug);
            Log::write("error", $trace);
        }
    }

    /**
     * ------------------------------------------------------------
     * Métodos AJAX
     * ------------------------------------------------------------
     */

    /**
     * ClientesController::enviaImagemPropaganda
     *
     * Envia imagem de forma assíncrona
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 06/08/2018
     *
     * @return json_object
     */
    public function enviaImagemPropagandaAPI()
    {
        $message = __("Erro durante o envio da imagem. Tente novamente!");

        $arquivos = array();
        try {
            if ($this->request->is('post')) {
                $data = $this->request->getData();

                $arquivos = FilesUtil::uploadFiles(PATH_IMAGES_CLIENTES_TEMP);

                return ResponseUtil::successAPI("", $arquivos);
            }
        } catch (\Exception $e) {
            $message = sprintf("[%s] %s", MESSAGE_GENERIC_EXCEPTION, $e->getMessage());
            Log::write("error", $message);
            $errors = array();
            $errors[] = $e->getMessage();

            return ResponseUtil::errorAPI(MESSAGE_GENERIC_ERROR, $errors);
        }
    }

    /**
     * ------------------------------------------------------------
     * Relatórios - Administrativo RTI
     * ------------------------------------------------------------
     */

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

        $this->Auth->allow(['enviaImagemPropaganda']);
    }

    /**
     *
     */
    public function initialize()
    {
        parent::initialize();
    }
}
