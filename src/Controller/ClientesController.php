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
use App\Custom\RTI\HtmlUtil;
use App\Custom\RTI\ResponseUtil;
use App\Custom\RTI\NumberUtil;
use App\Custom\RTI\StringUtil;
use App\Model\Entity\Cliente;
use App\Model\Entity\Gota;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Http\Client\Request;
use stdClass;
use Throwable;

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
        // Se a pesquisa é feita por alguém que está vinculado à uma rede, fixa o id da rede
        $redesId = !empty($this->rede) ? $this->rede->id : 0;
        $errors = [];
        $errorCodes = [];

        try {
            if ($this->request->is(Request::METHOD_GET)) {
                $getData = $this->request->getQueryParams();

                // Parâmetro de paginação
                $pagination = new stdClass();
                $pagination->start = isset($getData["start"]) ? (int) $getData["start"] : 1;
                $pagination->length = isset($getData["length"]) ? (int) $getData["length"] : 10000;

                $data = $getData["filtros"];

                $redesId = !empty($data["redes_id"]) ? (int) $data["redes_id"] : $redesId;
                $nomeFantasia = !empty($data["nome_fantasia"]) ? $data["nome_fantasia"] : null;
                $razaoSocial = !empty($data["razao_social"]) ? $data["razao_social"] : null;
                $cnpj = !empty($data["cnpj"]) ? preg_replace("/\D/", "", $data["cnpj"]) : null;
                $ativado = isset($data["ativado"]) && strlen($data["ativado"]) > 0 ? (bool) $data["ativado"] : null;

                $clientes = $this->Clientes->getClientes($redesId, $nomeFantasia, $razaoSocial, $cnpj, $ativado);

                $total = $clientes->count();
                // Cálculo da paginação

                if (isset($pagination->start) && isset($pagination->length)) {
                    $clientes = $clientes
                        ->limit($pagination->length)
                        ->page(floor(($pagination->start + $pagination->length) / $pagination->length))->toArray();
                }

                // Faz formatação necessária somente no resultado da paginação
                foreach ($clientes as $cliente) {
                    $cliente->cnpj = preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "$1.$2.$3/$4-$5", $cliente->cnpj);
                }

                $dataTableSource = new stdClass();
                $dataTableSource->draw = $data['draw'];
                $dataTableSource->recordsTotal = $total;
                $dataTableSource->recordsFiltered = $total;
                $dataTableSource->data = $clientes;

                return ResponseUtil::successAPI(MSG_LOAD_DATA_WITH_SUCCESS, ['data_table_source' => $dataTableSource]);
            }
        } catch (\Throwable $th) {
            $errorMessage = $th->getMessage();
            $errorCode = $th->getCode();

            if (count($errors) == 0) {
                $errors[] = $errorMessage;
                $errorCodes[] = $errorCode;
            }

            for ($i = 0; $i < count($errors); $i++) {
                Log::write("error", sprintf("[%s] %s - %s", MSG_LOAD_DATA_WITH_ERROR, $errorCodes[$i], $errors[$i]));
            }

            return ResponseUtil::errorAPI(MSG_LOAD_DATA_WITH_ERROR, $errors, [], $errorCodes);
        }
    }

    /**
     * Altera o estado de habilitado do Estabelecimento
     *
     * @param integer $id Id da unidade
     * @return json_encode $response Resposta
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.2.3
     * @date 2020-05-11
     */
    public function changeStatusAPI(int $id)
    {
        $errors = [];
        $errorCodes = [];

        try {
            $result = $this->Clientes->changeState($id);
            $word = $result->ativado ? "habilitada" : "desabilitada";
            $msg = sprintf("A rede foi %s com sucesso!", $word);

            return ResponseUtil::successAPI(MESSAGE_SAVED_SUCCESS, ['status' => $msg]);
        } catch (Throwable $th) {
            $errorMessage = $th->getMessage();
            $errorCode = $th->getCode();

            if (count($errors) == 0) {
                $errors[] = $errorMessage;
                $errorCodes[] = $errorCode;
            }

            for ($i = 0; $i < count($errors); $i++) {
                Log::write("error", sprintf("[%s] %s - %s", $errorMessage, $errorCodes[$i], $errors[$i]));
            }

            return ResponseUtil::errorAPI($errorMessage, $errors, [], $errorCodes);
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
    public function view($id = null)
    {
        $cliente = $this->Clientes->getClienteById($id);
        $data = new stdClass();
        $data->cliente = $cliente;

        return ResponseUtil::successAPI(MSG_LOAD_DATA_WITH_SUCCESS, ['data' => $data]);
    }

    /**
     * Adiciona uma loja ou posto
     *
     * @author Gustavo Souza Gonçalves
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     */
    public function add()
    {
        $errors = [];
        try {
            $cliente = $this->Clientes->newEntity();
            $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
            $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

            if ($usuarioAdministrador) {
                $this->usuarioLogado = $usuarioAdministrar;
            }

            if ($this->request->is(Request::METHOD_POST)) {
                $data = $this->request->getData();
                $redesId = !empty($data["redes_id"]) ? $data["redes_id"] : 0;
                $rede = $this->Redes->getRedeById($redesId);
                $cliente = $this->Clientes->patchEntity($cliente, $data);

                // Verifica se ja tem um registro antes
                $cnpj = !empty($cliente["cnpj"]) ? NumberUtil::limparFormatacaoNumeros($cliente["cnpj"]) : null;

                if ($cnpj) {
                    $clienteJaExistente = $this->Clientes->getClienteByCNPJ($cnpj);

                    if ($clienteJaExistente) {
                        $message = __("Este CNPJ já está cadastrado! Cliente Cadastrado com o CNPJ: {0}, Nome Fantasia: {1}, Razão Social: {2}", NumberUtil::formatarCNPJ($clienteJaExistente["cnpj"]), $clienteJaExistente["nome_fantasia"], $clienteJaExistente["razao_social"]);

                        throw new Exception($message);
                    }
                }

                $cliente = $this->Clientes->addClient($redesId, $cliente);

                if ($cliente) {
                    $qteTurnos = $data["qte_turnos"];
                    $horarioInicial = $data["turno"];
                    $horarios = $this->calculaTurnos($qteTurnos, $horarioInicial);
                    $this->ClientesHasQuadroHorario->addHorariosCliente($redesId, $cliente["id"], $horarios);

                    // Adiciona bonificação extra sefaz para novo posto
                    $gotas = [];
                    $gota = new Gota();
                    $gota->nome_parametro = GOTAS_BONUS_SEFAZ;
                    $gota->multiplicador_gota = $rede->qte_gotas_bonificacao;
                    $gotas[] = $gota;

                    if ($rede->pontuacao_extra_produto_generico) {
                        $gota = new Gota();
                        $gota->nome_parametro = GOTAS_BONUS_EXTRA_POINTS_SEFAZ;
                        $gota->multiplicador_gota = 1;
                        $gotas[] = $gota;
                    }

                    foreach ($gotas as $gota) {
                        $this->Gotas->saveUpdateExtraPoints([$cliente->id], $gota->multiplicador_gota, $gota->nome_parametro);
                    }

                    // Adiciona Gota de Ajuste de pontos
                    $this->Gotas->saveUpdateGotasAdjustment([$cliente->id]);

                    // Adiciona o funcionário de sistema como o primeiro funcionário da rede
                    // Ele será necessário para funções automáticas de venda de brinde via API
                    $funcionarioSistema = $this->Usuarios->getFuncionarioFicticio();
                    $this->ClientesHasUsuarios->saveClienteHasUsuario($cliente->id, $funcionarioSistema->id, true);

                    $usuarioFicticio = $this->Usuarios->getUsuarioFicticio();
                    $this->ClientesHasUsuarios->saveClienteHasUsuario($cliente->id, $usuarioFicticio->id, true);
                }

                return ResponseUtil::successAPI(MESSAGE_SAVED_SUCCESS);
            }
        } catch (\Throwable $th) {
            $errorMessage = $th->getMessage();
            $errorCode = $th->getCode();

            if (count($errors) == 0) {
                $errors[] = $errorMessage;
                $errorCodes[] = $errorCode;
            }

            for ($i = 0; $i < count($errors); $i++) {
                Log::write("error", sprintf("[%s] %s - %s", $errorMessage, $errorCodes[$i], $errors[$i]));
            }

            return ResponseUtil::errorAPI(MSG_SAVED_EXCEPTION, $errors, [], $errorCodes);
        }
    }

    /**
     * Edit method
     *
     * @param string|null $id Cliente id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $errors = [];

        try {
            $cliente = $this->Clientes->getClienteById($id);
            $redesId = $cliente["redes_has_cliente"]["redes_id"];

            // Monta o quadro de horários
            $quantidadeTurnos = sizeof($cliente["clientes_has_quadro_horarios"]);
            $turnoInicial = null;

            if ($quantidadeTurnos > 0) {
                $turnoInicial = $cliente["clientes_has_quadro_horarios"][0]["horario"]->format("H:i");
            }

            if ($this->request->is([REQUEST::METHOD_POST, Request::METHOD_PUT])) {
                $data = $this->request->getData();

                $cliente = $this->Clientes->patchEntity($cliente, $data);
                $cnpj = !empty($cliente["cnpj"]) ? NumberUtil::limparFormatacaoNumeros($cliente["cnpj"]) : null;

                if ($cnpj) {
                    $clienteJaExistente = $this->Clientes->getClienteByCNPJ($cnpj);

                    if ($clienteJaExistente && $clienteJaExistente["id"] != $cliente["id"]) {
                        $message = __("Este CNPJ já está cadastrado! Cliente Cadastrado com o CNPJ: {0}, Nome Fantasia: {1}, Razão Social: {2}", NumberUtil::formatarCNPJ($clienteJaExistente["cnpj"]), $clienteJaExistente["nome_fantasia"], $clienteJaExistente["razao_social"]);

                        throw new Exception($message);
                    }
                }

                // Atualiza o registro

                $cliente->codigo_equipamento_rti = str_pad($cliente->codigo_equipamento_rti, 3, "0", STR_PAD_LEFT);
                $cliente->cnpj = NumberUtil::limparFormatacaoNumeros($cliente['cnpj']);
                $cliente->tel_fixo = NumberUtil::limparFormatacaoNumeros($cliente['tel_fixo']);
                $cliente->tel_celular = NumberUtil::limparFormatacaoNumeros($cliente['tel_celular']);
                $cliente->tel_fax = NumberUtil::limparFormatacaoNumeros($cliente['tel_fax']);
                $cliente->cep = NumberUtil::limparFormatacaoNumeros($cliente['cep']);
                $this->Clientes->saveUpdate($cliente);

                /** Atualização do quadro de horarios
                 * Se o primeiro horário e a quantidade de turno for diferente,
                 * apaga todos e grava novamente
                 */
                $novaQteTurnos = $data["qte_turnos"];
                $novoTurno = $data["turno"];

                if (($novaQteTurnos != $quantidadeTurnos) || ($novoTurno != $turnoInicial)) {
                    $horarios = $this->calculaTurnos($novaQteTurnos, $novoTurno);
                    $this->ClientesHasQuadroHorario->disableHorariosCliente($cliente->id);
                    $this->ClientesHasQuadroHorario->addHorariosCliente($redesId, $cliente->id, $horarios);
                }

                return ResponseUtil::successAPI(MESSAGE_SAVED_SUCCESS);
            }
        } catch (\Throwable $th) {
            $errorMessage = $th->getMessage();
            $errorCode = $th->getCode();

            if (count($errors) == 0) {
                $errors[] = $errorMessage;
                $errorCodes[] = $errorCode;
            }

            for ($i = 0; $i < count($errors); $i++) {
                Log::write("error", sprintf("[%s] %s - %s", $errorMessage, $errorCodes[$i], $errors[$i]));
            }

            return ResponseUtil::errorAPI(MSG_SAVED_EXCEPTION, $errors, [], $errorCodes);
        }
    }

    /**
     * Delete method
     *
     * @param string|null $id Cliente id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id)
    {
        try {
            $data = $this->request->getData();
            $senhaUsuario = !empty($data["password"]) ? $data["password"] : null;
            $usuario = $this->sessaoUsuario["usuarioLogado"];
            $usuario = $this->Usuarios->getUsuarioById($usuario["id"]);

            if (empty($senhaUsuario)) {
                throw new Exception("Para continuar, informe sua senha!");
            }

            // Testa a senha do usuário
            if (!(new DefaultPasswordHasher)->check($senhaUsuario, $usuario["senha"])) {
                throw new Exception(Configure::read("messageUsuarioSenhaDoesntMatch"));
            }

            $cliente = $this->Clientes->get($id);

            // Faz remoção dos dados associados (Remoção mesmo, sem recuperação)

            // Usuários Has Brindes
            $this->UsuariosHasBrindes->deleteAllUsuariosHasBrindesByClientesIds([$cliente->id]);

            // Remoção de Transaçoes de cupons
            $this->CuponsTransacoes->deleteAllByRedesId($cliente->id);
            // Remoção de Cupons
            $this->Cupons->deleteAllCuponsByClientesIds([$cliente->id]);

            $this->PontuacoesPendentes->deleteAllPontuacoesPendentesByClientesIds([$cliente->id]);
            $this->Pontuacoes->deleteAllPontuacoesByClientesIds([$cliente->id]);
            $this->PontuacoesComprovantes->deleteAllPontuacoesComprovantesByClientesIds([$cliente->id]);

            // brindes
            $this->BrindesEstoque->deleteAllBrindesEstoqueByClientesIds([$cliente->id]);
            $this->BrindesPrecos->deleteAllBrindesPrecosByClientesIds([$cliente->id]);
            $this->Brindes->deleteAllBrindesByClientesIds([$cliente->id]);

            // gotas
            $this->Gotas->deleteAllGotasByClientesIds([$cliente->id]);

            // apagar os usuários que são da rede (Administradores da Rede até funcionários)
            $whereConditions = array();
            $whereConditions[] = ['tipo_perfil >= ' => Configure::read('profileTypes')['AdminNetworkProfileType']];
            $whereConditions[] = ['tipo_perfil <= ' => Configure::read('profileTypes')['WorkerProfileType']];

            // Apaga os funcionários
            $this->Usuarios->deleteAllUsuariosByClienteIds([$cliente->id], $whereConditions);
            $this->ClientesHasUsuarios->deleteAllClientesHasUsuariosByClientesIds([$cliente->id]);
            $this->ClientesHasQuadroHorario->deleteHorariosCliente($cliente->id);

            // Remove o estabelecimento da rede
            $this->RedesHasClientes->deleteRedesHasClientesByClientesIds([$cliente->id]);

            // Enfim, remove o estabelecimento
            $this->Clientes->delete($cliente);

            return ResponseUtil::successAPI(MESSAGE_DELETE_SUCCESS);
        } catch (\Throwable $th) {
            $message = sprintf("[%s] %s", MSG_DELETE_EXCEPTION, $th->getMessage());
            Log::write("error", $message);

            return ResponseUtil::errorAPI(MSG_DELETE_EXCEPTION, [$th->getMessage()]);
        }
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

                if ($this->Clientes->saveUpdate($cliente)) {

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
            $messageString = __("Não foi possível obter dados de Pontos de Atendimento!");

            $messageStringDebug =
                __("{0} - {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $messageString, $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write("error", $messageStringDebug);
        }
    }

    /**
     * Action para Relatório de Ranking de Operações
     *
     * @return void
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.1.6
     * @date 2020-03-04
     */
    public function relBalancoGeral()
    {
        $sessaoUsuario = $this->getSessionUserVariables();
        $usuarioLogado = $sessaoUsuario["usuarioLogado"];
        $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"];
        $cliente = $sessaoUsuario["cliente"];
        $clientesId = !empty($cliente) ? $cliente->id : 0;
        $rede = $sessaoUsuario["rede"];

        if ($usuarioAdministrar) {
            $usuarioLogado = $usuarioAdministrar;
        }

        $arraySet = ["clientesId"];

        $this->set(compact($arraySet));
        $this->set('_serialize', $arraySet);
    }

    /**
     * Action para o Relatório de cliente final
     * @return void
     * @author Vinícius Carvalho de Abreu <vinicius@aigen.com.br>
     * @since 1.1.6
     * @date 2020-03-23
     */
    public function relClienteFinal()
    {
    }

    /**
     * Action para Relatório de Ranking de Operações
     *
     * @return void
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.1.6
     * @date 2020-03-04
     */
    public function relRankingOperacoes()
    {
        $sessaoUsuario = $this->getSessionUserVariables();
        $usuarioLogado = $sessaoUsuario["usuarioLogado"];
        $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"];
        $cliente = $sessaoUsuario["cliente"];
        $clientesId = !empty($cliente) ? $cliente->id : 0;
        $rede = $sessaoUsuario["rede"];

        if ($usuarioAdministrar) {
            $usuarioLogado = $usuarioAdministrar;
        }

        $arraySet = ["clientesId"];

        $this->set(compact($arraySet));
        $this->set('_serialize', $arraySet);
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
     * Dados de Relatório de Balanço Geral de Estabelecimentos
     *
     * Obtem Dados de Relatório de Balanço Geral de Estabelecimentos contendo as seguintes informações:
     *
     * 1 - Quantidade de Gotas de Entrada (Pontos de Produtos ADQUIRIDOS)
     * 2 - Quantidade de Gotas de Saída (Pontos de Brindes USADOS)
     *
     * @param int $redes_id Id da Rede
     * @param DateTime $data_inicio Data Início
     * @param DateTime $data_fim Data Fim
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.1.8
     * @date 2020-03-04
     *
     * @return json_encode (json object|table html|excel)
     */
    public function balancoGeralAPI()
    {
        $sessaoUsuario = $this->getSessionUserVariables();
        $usuarioLogado = $sessaoUsuario["usuarioLogado"];
        $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"];
        $rede = $sessaoUsuario["rede"];
        $cliente = $sessaoUsuario["cliente"];
        $errors = [];
        $errorCodes = [];

        if ($usuarioAdministrar) {
            $usuarioLogado = $usuarioAdministrar;
        }

        try {
            if ($this->request->is(Request::METHOD_GET)) {
                $data = $this->request->getQueryParams();
                $redesId = !empty($data["redes_id"]) ? $data["redes_id"] : null;
                $dataInicio =  !empty($data["data_inicio"]) ? $data["data_inicio"] : null;
                $dataFim =  !empty($data["data_fim"]) ? $data["data_fim"] : null;
                $typeExport = !empty($data["tipo_exportacao"]) ? $data["tipo_exportacao"] : TYPE_EXPORTATION_DATA_OBJECT;
                // Se usuário logado for Adm Rede ou Regional, não precisa estar preso a um posto a pesquisa
                if (in_array($usuarioLogado->tipo_perfil, [PROFILE_TYPE_ADMIN_NETWORK, PROFILE_TYPE_ADMIN_REGIONAL])) {
                    $cliente = null;
                }

                $redesId = !empty($rede) ? $rede->id : $redesId;

                if (empty($redesId)) {
                    // Necessário ter uma
                    $errors[] = MSG_REDES_ID_EMPTY;
                    $errorCodes[] = MSG_REDES_ID_EMPTY_CODE;
                }

                if (empty($dataInicio)) {
                    $errors[] = MSG_DATE_BEGIN_EMPTY;
                    $errorCodes[] = MSG_DATE_BEGIN_EMPTY_CODE;
                }

                if (empty($dataFim)) {
                    $errors[] = MSG_DATE_END_EMPTY;
                    $errorCodes[] = MSG_DATE_END_EMPTY_CODE;
                }

                if (empty($typeExport)) {
                    $errors[] = TYPE_EXPORTATION_DATA_EMPTY;
                    $errorCodes[] = TYPE_EXPORTATION_DATA_EMPTY_CODE;
                }

                if (count($errors) > 0) {
                    throw new Exception(MSG_LOAD_EXCEPTION, MSG_LOAD_EXCEPTION_CODE);
                }

                $redesHasClientesQuery = $this->RedesHasClientes->getRedesHasClientesByRedesId($redesId);
                $redesHasClientes = $redesHasClientesQuery->select(["Clientes.id", "Clientes.nome_fantasia"])->toArray();
                $clientes = array_column($redesHasClientes, "Clientes");

                $dataInicio = new DateTime(sprintf("%s 00:00:00", $dataInicio));
                $dataFim = new DateTime(sprintf("%s 23:59:59", $dataFim));

                /**
                 *  Para cada registro de Cliente, obtem os dados de:
                 *  -> Brindes mais resgatados;
                 *  -> Combustível mais utilizado;
                 *  -> Cliente que mais pontuou;
                 *  -> Funcionário que mais atendeu;
                 */

                $reportData = [];

                foreach ($clientes as $cliente) {
                    $reportItem = new stdClass();

                    // Dados do Estabelecimento
                    $reportItem->cliente = $cliente;
                    // Total entrada
                    $reportItem->entrada = $this->Pontuacoes->getSumPointsNetwork($redesId, $cliente->id, $dataInicio, $dataFim)->first();
                    // Total Saída
                    $reportItem->saida = $this->CuponsTransacoes->getSumTransacoesByTypeOperation($rede->id, $cliente->id, null, null, null, null, TYPE_OPERATION_USE, $dataInicio, $dataFim);

                    $reportData[] = $reportItem;
                }

                $headersReport = new stdClass();
                $headersReport->nome_fantasia = "Estabelecimento";
                $headersReport->entrada = "Pontos de Entrada";
                $headersReport->saida = "Pontos de Saída";

                $dataRows = [];
                $sumInPoints = 0;
                $sumOutPoints = 0;

                foreach ($reportData as $data) {
                    $item = new stdClass();
                    $item->nome_fantasia = $data->cliente->nome_fantasia;
                    $item->entrada = !empty($data->entrada) && !empty($data->entrada->sum) ? $data->entrada->sum : 0;
                    $item->saida = !empty($data->saida) && !empty($data->saida->sum_valor_pago_gotas) ? $data->saida->sum_valor_pago_gotas : 0;
                    $dataRows[] = $item;

                    $sumInPoints += $item->entrada;
                    $sumOutPoints += $item->saida;
                }

                $total = new stdClass();
                $total->entrada = $sumInPoints;
                $total->saida = $sumOutPoints;

                if ($typeExport === TYPE_EXPORTATION_DATA_OBJECT) {
                    $dataToReturn = new stdClass();
                    $dataToReturn->headers = $headersReport;
                    $dataToReturn->rows = $dataRows;
                    $dataToReturn->total = $total;

                    // return ResponseUtil::successAPI(MSG_LOAD_DATA_WITH_SUCCESS, $dataToReturn);
                    return ResponseUtil::successAPI(MSG_LOAD_DATA_WITH_SUCCESS, ["data" => $dataToReturn]);
                } elseif (in_array($typeExport, [TYPE_EXPORTATION_DATA_TABLE, TYPE_EXPORTATION_DATA_EXCEL])) {

                    $rowTotal = new stdClass();
                    $rowTotal->nome_fantasia = "Total:";
                    $rowTotal->entrada = $sumInPoints;
                    $rowTotal->saida = $sumOutPoints;

                    $dataRows[] = $rowTotal;

                    $dataToReturn = new stdClass();
                    $title = sprintf("Balanço Geral: (%s à %s)", $dataInicio->format("d/m/Y"), $dataFim->format("d/m/Y"));
                    $dataToReturn->report = HtmlUtil::generateHTMLTable($title, $headersReport, $dataRows, true);

                    if ($typeExport === TYPE_EXPORTATION_DATA_TABLE) {
                        return ResponseUtil::successAPI(MSG_LOAD_DATA_WITH_SUCCESS, ['data' => $dataToReturn]);
                    } else {
                        $allTables = sprintf("%s", $dataToReturn->report);
                        $excel = HtmlUtil::wrapContentToHtml($allTables);
                        return ResponseUtil::successAPI(MSG_LOAD_DATA_WITH_SUCCESS, ['data' => $excel]);
                    }
                }

                throw new Exception(MSG_LOAD_EXCEPTION, MSG_LOAD_EXCEPTION_CODE);
            }
        } catch (\Throwable $th) {
            $errorMessage = $th->getMessage();
            $errorCode = $th->getCode();

            if (count($errors) == 0) {
                $errors[] = $errorMessage;
                $errorCodes[] = $errorCode;
            }

            for ($i = 0; $i < count($errors); $i++) {
                Log::write("error", sprintf("[%s] %s - %s", MSG_LOAD_DATA_WITH_ERROR, $errorCodes[$i], $errors[$i]));
            }

            return ResponseUtil::errorAPI(MSG_LOAD_DATA_WITH_ERROR, $errors, [], $errorCodes);
        }
    }

    /**
     * ClientesController::getClientesListAPI
     *
     * Obtem lista de Clientes (da Rede informada ou da sessão do usuário)
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
        $redesId = !empty($rede) ? $rede->id : 0;
        $cliente = $sessao["cliente"];
        $clientesIds = [];
        $clientesId = !empty($cliente) && !empty($cliente->id) ? $cliente->id : null;
        $usuarioLogado = $sessao["usuarioLogado"];

        if (!empty($clientesId) && !in_array($usuarioLogado->tipo_perfil, [PROFILE_TYPE_ADMIN_NETWORK, PROFILE_TYPE_ADMIN_REGIONAL])) {
            $clientesIds[] = $clientesId;
        }

        try {
            // Caso o método seja chamado via get
            if ($this->request->is(Request::METHOD_GET)) {
                $data = $this->request->getQueryParams();

                $redesId = !empty($data["redes_id"]) ? $data["redes_id"] : $redesId;
            }

            $selectList = array(
                "Clientes.id",
                "Clientes.nome_fantasia",
                "Clientes.razao_social",
                "Clientes.propaganda_img",
                "Clientes.municipio",
                "Clientes.estado"
            );

            if (empty($redesId)) {
                throw new Exception(MSG_ID_EMPTY);
            }

            $redeHasClientes = $this->RedesHasClientes->getRedesHasClientesByRedesId($redesId, $clientesIds);

            $redeHasClientes = $redeHasClientes->select($selectList);

            // return ResponseUtil::successAPI("", [$redeHasClientes->toArray()]);
            $clientes = [];

            foreach ($redeHasClientes as $redeHasCliente) {
                $clientes[] = $redeHasCliente->Clientes;
            }

            if (count($clientes) == 0) {
                return ResponseUtil::errorAPI(MSG_LOAD_DATA_NOT_FOUND);
            }

            $data = ["data" => ["clientes" => $clientes]];

            return ResponseUtil::successAPI(MSG_LOAD_DATA_WITH_SUCCESS, $data);
        } catch (\Throwable $th) {
            $message = sprintf("[%s] %s", MSG_LOAD_EXCEPTION, $th->getMessage());
            Log::write("error", $message);

            return ResponseUtil::errorAPI(MSG_LOAD_EXCEPTION, [$th->getMessage()]);
        }
    }

    /**
     * Dados de Relatório de Ranking de Operações
     *
     * Obtem Dados de Relatório de Ranking de Operações contendo as seguintes informações:
     *
     * 1 - Brinde mais resgatado;
     * 2 - Combustível mais utilizado;
     * 3 - Cliente mais pontuado;
     * 4 - Funcionário que mais prestou atendimento;
     *
     * @param int $redes_id Id da Rede
     * @param int $clientes_id Id do Estabelecimento
     * @param DateTime $data_inicio Data Início
     * @param DateTime $data_fim Data Fim
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.1.6
     * @date 2020-03-04
     *
     * @return json_encode (json object|table html|excel)
     */
    public function rankingOperacoesAPI()
    {
        // $sessaoUsuario = $this->getSessionUserVariables();
        // $usuarioLogado = $sessaoUsuario["usuarioLogado"];
        // $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"];
        // $rede = $sessaoUsuario["rede"];
        // $cliente = $sessaoUsuario["cliente"];
        $cliente = $this->cliente;
        $errors = [];
        $errorCodes = [];

        // if ($usuarioAdministrar) {
        //     $usuarioLogado = $usuarioAdministrar;
        // }

        try {
            if ($this->request->is(Request::METHOD_GET)) {
                $data = $this->request->getQueryParams();
                $redesId = !empty($data["redes_id"]) ? $data["redes_id"] : null;
                $clientesId = !empty($data["clientes_id"]) ? $data["clientes_id"] : null;
                $dataInicio =  !empty($data["data_inicio"]) ? $data["data_inicio"] : null;
                $dataFim =  !empty($data["data_fim"]) ? $data["data_fim"] : null;
                $typeExport = !empty($data["tipo_exportacao"]) ? $data["tipo_exportacao"] : TYPE_EXPORTATION_DATA_OBJECT;
                $limit = !empty($data["limit"]) ? $data["limit"] : 0;
                // return ResponseUtil::successAPI('', ['data' => $data]);
                // Se usuário logado for Adm Rede ou Regional, não precisa estar preso a um posto a pesquisa
                if (in_array($this->usuarioLogado->tipo_perfil, [PROFILE_TYPE_ADMIN_NETWORK, PROFILE_TYPE_ADMIN_REGIONAL])) {
                    $cliente = null;
                }

                $redesId = !empty($this->rede) ? $this->rede->id : $redesId;
                $clientesId = !empty($cliente) ? $cliente->id : $clientesId;

                if (empty($redesId)) {
                    // Necessário ter uma Rede selecionada
                    $errors[] = MSG_REDES_ID_EMPTY;
                    $errorCodes[] = MSG_REDES_ID_EMPTY_CODE;
                }

                if (empty($dataInicio)) {
                    $errors[] = MSG_DATE_BEGIN_EMPTY;
                    $errorCodes[] = MSG_DATE_BEGIN_EMPTY_CODE;
                }

                if (empty($dataFim)) {
                    $errors[] = MSG_DATE_END_EMPTY;
                    $errorCodes[] = MSG_DATE_END_EMPTY_CODE;
                }

                if (count($errors) > 0) {
                    throw new Exception(MSG_LOAD_EXCEPTION, MSG_LOAD_EXCEPTION_CODE);
                }

                $clientes = [];

                if (empty($clientesId)) {
                    $redesHasClientesQuery = $this->RedesHasClientes->getRedesHasClientesByRedesId($redesId);
                    $redesHasClientes = $redesHasClientesQuery->select(["Clientes.id", "Clientes.nome_fantasia"])->toArray();
                    $clientes = array_column($redesHasClientes, "Clientes");
                } else {
                    $clienteTemp = $this->Clientes->get($clientesId);
                    $cliente = new Cliente();
                    $cliente->id = $clienteTemp->id;
                    $cliente->nome_fantasia = $clienteTemp->nome_fantasia;
                    $clientes[] = $cliente;
                }

                $dataInicio = new DateTime(sprintf("%s 00:00:00", $dataInicio));
                $dataFim = new DateTime(sprintf("%s 23:59:59", $dataFim));

                /**
                 *  Para cada registro de Cliente, obtem os dados de:
                 *  -> Brindes mais resgatados;
                 *  -> Combustível mais utilizado;
                 *  -> Cliente que mais pontuou;
                 *  -> Funcionário que mais atendeu;
                 */

                $reportData = [];

                foreach ($clientes as $cliente) {
                    $reportItem = new stdClass();

                    // Dados do Estabelecimento
                    $reportItem->cliente = $cliente;

                    // Brinde mais resgatado:
                    $reportItem->brindes = $this->CuponsTransacoes->getBestSellerBrindes($redesId, $cliente->id, $dataInicio, $dataFim, $limit);
                    // return ResponseUtil::successAPI('', ['data' => $reportItem]);

                    // Combustível mais utilizado
                    $reportItem->gotas = $this->Pontuacoes->getBestSellerGotas($redesId, $cliente->id, $dataInicio, $dataFim, $limit);

                    // Usuário que mais pontuou
                    $reportItem->usuarios = $this->Pontuacoes->getUserHighestPointsIn($redesId, $cliente->id, $dataInicio, $dataFim, $limit);

                    // Funcionario que mais abasteceu clientes
                    $employeeTopSoldProducts = $this->Pontuacoes->getEmployeeMostSoldGotas($redesId, $cliente->id, $dataInicio, $dataFim, $limit);
                    // Funcionário que mais vendeu Brindes
                    $employeeTopSoldGifts = $this->CuponsTransacoes->getEmployeeMostSoldBrindes($redesId, $cliente->id, $dataInicio, $dataFim, $limit);

                    $reportItem->funcionarios = new stdClass();
                    $reportItem->funcionarios->gotas = $employeeTopSoldProducts;
                    $reportItem->funcionarios->brindes = $employeeTopSoldGifts;

                    $reportData[] = $reportItem;
                }

                $headerReportGifts = new stdClass();
                $headerReportGifts->nome_fantasia = "Estabelecimento";
                $headerReportGifts->brinde_nome = "Brindes Mais Resgatados";
                $headerReportGifts->brinde_qte = "Qte.";

                $headerReportDrops = new stdClass();
                $headerReportDrops->nome_fantasia = "Estabelecimento";
                $headerReportDrops->gota_nome = "Produtos Mais Resgatados";
                $headerReportDrops->gota_qte = "Qte.";

                $headerReportUser = new stdClass();
                $headerReportUser->nome_fantasia = "Estabelecimento";
                $headerReportUser->usuario_nome = "Usuários Mais Pontuados";
                $headerReportUser->usuario_qte = "Qte.";

                $headerReportEmployee = new stdClass();
                $headerReportEmployee->nome_fantasia = "Estabelecimento";
                $headerReportEmployee->funcionario_brindes_nome = "Saída";
                $headerReportEmployee->funcionario_brindes_qte = "Qte.";
                $headerReportEmployee->funcionario_gotas_nome = "Entrada";
                $headerReportEmployee->funcionario_gotas_qte = "Qte.";
                $dataGifts = [];
                $dataDrops = [];
                $dataUser = [];
                $dataEmployee = [];

                $sumBrindeQte = 0;
                $sumGotaQte = 0;
                $sumUsuarioQte = 0;
                $sumFuncionarioBrindes = 0;
                $sumFuncionarioGotas = 0;

                foreach ($reportData as $data) {
                    foreach ($data->brindes as $brinde) {
                        $itemsBrindes = new stdClass();
                        $itemsBrindes->nome_fantasia = $data->cliente->nome_fantasia;
                        $itemsBrindes->brinde_nome = $brinde->nome;
                        $itemsBrindes->brinde_qte = $brinde->count;
                        $sumBrindeQte += $itemsBrindes->brinde_qte;
                        $dataGifts[] = $itemsBrindes;
                    }

                    foreach ($data->gotas as $gota) {
                        $itemsGotas = new stdClass();
                        $itemsGotas->nome_fantasia = $data->cliente->nome_fantasia;
                        $itemsGotas->gota_nome = $gota->nome;
                        $itemsGotas->gota_qte = $gota->sum;
                        $dataDrops[] = $itemsGotas;
                        $sumGotaQte += $gota->sum;
                    }

                    foreach ($data->usuarios as $usuario) {
                        $itemsUsuarios = new stdClass();
                        $itemsUsuarios->nome_fantasia = $data->cliente->nome_fantasia;
                        $itemsUsuarios->usuario_nome = $usuario->nome;
                        $itemsUsuarios->usuario_qte = $usuario->sum;
                        $dataUser[] = $itemsUsuarios;

                        $sumUsuarioQte += $usuario->sum;
                    }

                    $brindesFuncionario = $data->funcionarios->brindes->toArray();
                    $gotasFuncionario = $data->funcionarios->gotas->toArray();
                    $lengthBrindes = count($brindesFuncionario);
                    $lengthGotas = count($gotasFuncionario);
                    for ($index = 0; $index < max($lengthBrindes, $lengthGotas); $index++) {
                        $itemFuncionario = new stdClass();
                        $itemFuncionario->nome_fantasia = $data->cliente->nome_fantasia;
                        $brinde = !empty($brindesFuncionario[$index]) ? $brindesFuncionario[$index] : null;
                        $gota = !empty($gotasFuncionario[$index]) ? $gotasFuncionario[$index] : null;

                        if (!empty($brinde)) {
                            $itemFuncionario->funcionario_brindes_nome = $brindesFuncionario[$index]->funcionarios_nome;
                            $itemFuncionario->funcionario_brindes_qte = $brindesFuncionario[$index]->count;
                            $sumFuncionarioBrindes += $itemFuncionario->funcionario_brindes_qte;
                        } else {
                            $itemFuncionario->funcionario_brindes_nome = "";
                            $itemFuncionario->funcionario_brindes_qte = 0;
                        }

                        if (!empty($gota)) {
                            $itemFuncionario->funcionario_gotas_nome = $gotasFuncionario[$index]->funcionarios_nome;
                            $itemFuncionario->funcionario_gotas_qte = $gotasFuncionario[$index]->count;
                            $sumFuncionarioGotas += $itemFuncionario->funcionario_gotas_qte;
                        } else {
                            $itemFuncionario->funcionario_gotas_nome = "";
                            $itemFuncionario->funcionario_gotas_qte = 0;
                        }

                        $dataEmployee[] = $itemFuncionario;
                    }
                }

                $totalGift = new stdClass();
                $totalGift->sum = $sumBrindeQte;
                $totalDrop = new stdClass();
                $totalDrop->sum = $sumGotaQte;
                $totalUser = new stdClass();
                $totalUser->sum = $sumUsuarioQte;
                $totalEmployee = new stdClass();
                $totalEmployee->sum_brinde = $sumFuncionarioBrindes;
                $totalEmployee->sum_gota = $sumFuncionarioGotas;

                if ($typeExport === TYPE_EXPORTATION_DATA_OBJECT) {
                    $reportGifts = new stdClass();
                    $reportGifts->headers = $headerReportGifts;
                    $reportGifts->rows = $dataGifts;
                    $reportGifts->total = $totalGift;

                    $reportDrops = new stdClass();
                    $reportDrops->headers = $headerReportDrops;
                    $reportDrops->rows = $dataDrops;
                    $reportDrops->total = $totalDrop;

                    $reportUser = new stdClass();
                    $reportUser->headers = $headerReportGifts;
                    $reportUser->rows = $dataUser;
                    $reportUser->total = $totalUser;

                    $reportEmployee = new stdClass();
                    $reportEmployee->headers = $headerReportEmployee;
                    $reportEmployee->rows = $dataEmployee;
                    $reportEmployee->total = $totalEmployee;

                    $dataToReturn = new stdClass();
                    $dataToReturn->brindes = $reportGifts;
                    $dataToReturn->gotas = $reportDrops;
                    $dataToReturn->usuarios = $reportUser;
                    $dataToReturn->funcionarios = $reportEmployee;

                    // return ResponseUtil::successAPI(MSG_LOAD_DATA_WITH_SUCCESS, $dataToReturn);
                    return ResponseUtil::successAPI(MSG_LOAD_DATA_WITH_SUCCESS, ["data" => $dataToReturn]);
                } elseif (in_array($typeExport, [TYPE_EXPORTATION_DATA_TABLE, TYPE_EXPORTATION_DATA_EXCEL])) {

                    $rowTotal = new stdClass();
                    $rowTotal->nome_fantasia = "Total:";
                    $rowTotal->brinde_nome = "";
                    $rowTotal->brinde_qte = $sumBrindeQte;
                    $rowTotal->gota_nome = "";
                    $rowTotal->gota_qte = $sumGotaQte;
                    $rowTotal->usuario_nome = "";
                    $rowTotal->usuario_qte = $sumUsuarioQte;
                    $rowTotal->funcionario_brindes_nome = "";
                    $rowTotal->funcionario_brindes_qte = $sumFuncionarioBrindes;
                    $rowTotal->funcionario_gotas_nome = "";
                    $rowTotal->funcionario_gotas_qte = $sumFuncionarioGotas;

                    $rowTotalGift = new stdClass();
                    $rowTotalGift->nome_fantasia = "Total:";
                    $rowTotalGift->brinde_nome = "";
                    $rowTotalGift->sum = $sumBrindeQte;
                    $rowTotalDrop = new stdClass();
                    $rowTotalDrop->nome_fantasia = "Total:";
                    $rowTotalDrop->gota_nome = "";
                    $rowTotalDrop->sum = $sumGotaQte;
                    $rowTotalUser = new stdClass();
                    $rowTotalUser->nome_fantasia = "Total:";
                    $rowTotalUser->usuario_nome = "";
                    $rowTotalUser->sum = $sumUsuarioQte;
                    $rowTotalEmployee = new stdClass();
                    $rowTotalEmployee->nome_fantasia = "Total:";
                    $rowTotalEmployee->funcionario_brindes_nome = "";
                    $rowTotalEmployee->sum_brinde = $sumFuncionarioBrindes;
                    $rowTotalEmployee->funcionario_gotas_nome = "";
                    $rowTotalEmployee->sum_gota = $sumFuncionarioGotas;

                    $dataGifts[] = $rowTotalGift;
                    $dataDrops[] = $rowTotalDrop;
                    $dataUser[] = $rowTotalUser;
                    $dataEmployee[] = $rowTotalEmployee;

                    $dataToReturn = new stdClass();
                    $titleGift = sprintf("Ranking de Operações: %s (%s à %s)", "Brindes Adquiridos", $dataInicio->format("d/m/Y"), $dataFim->format("d/m/Y"));
                    $titleDrop = sprintf("Ranking de Operações: %s (%s à %s)", "Produtos Vendidos", $dataInicio->format("d/m/Y"), $dataFim->format("d/m/Y"));
                    $titleUser = sprintf("Ranking de Operações: %s (%s à %s)", "Usuário", $dataInicio->format("d/m/Y"), $dataFim->format("d/m/Y"));
                    $titleEmployee = sprintf("Ranking de Operações: %s (%s à %s)", "Funcionário", $dataInicio->format("d/m/Y"), $dataFim->format("d/m/Y"));
                    $dataToReturn->brinde = HtmlUtil::generateHTMLTable($titleGift, $headerReportGifts, $dataGifts, true);
                    $dataToReturn->gota = HtmlUtil::generateHTMLTable($titleDrop, $headerReportDrops, $dataDrops, true);
                    $dataToReturn->usuario = HtmlUtil::generateHTMLTable($titleUser, $headerReportUser, $dataUser, true);
                    $dataToReturn->funcionario = HtmlUtil::generateHTMLTable($titleEmployee, $headerReportEmployee, $dataEmployee, true);

                    if ($typeExport === TYPE_EXPORTATION_DATA_TABLE) {
                        return ResponseUtil::successAPI(MSG_LOAD_DATA_WITH_SUCCESS, ['data' => $dataToReturn]);
                    } else {
                        $allTables = sprintf("%s%s%s%s", $dataToReturn->brinde, $dataToReturn->gota, $dataToReturn->usuario, $dataToReturn->funcionario);
                        $excel = HtmlUtil::wrapContentToHtml($allTables);
                        return ResponseUtil::successAPI(MSG_LOAD_DATA_WITH_SUCCESS, ['data' => $excel]);
                    }
                }

                // return ResponseUtil::successAPI(MSG_LOAD_DATA_WITH_SUCCESS, ['headers' => $titleReportColumns, 'rows' => $reportData]);
            }

            throw new Exception(TYPE_EXPORTATION_DATA_EMPTY, TYPE_EXPORTATION_DATA_EMPTY_CODE);
        } catch (\Throwable $th) {
            $errorMessage = $th->getMessage();
            $errorCode = $th->getCode();

            if (count($errors) == 0) {
                $errors[] = $errorMessage;
                $errorCodes[] = $errorCode;
            }

            for ($i = 0; $i < count($errors); $i++) {
                Log::write("error", sprintf("[%s] %s - %s", MSG_LOAD_DATA_WITH_ERROR, $errorCodes[$i], $errors[$i]));
            }

            return ResponseUtil::errorAPI(MSG_LOAD_DATA_WITH_ERROR, $errors, [], $errorCodes);
        }
    }


    public function clienteFinalAPI()
    {
        $sessaoUsuario = $this->getSessionUserVariables();
        $usuarioLogado = $sessaoUsuario["usuarioLogado"];
        $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"];
        $rede = $sessaoUsuario["rede"];
        $cliente = $sessaoUsuario["cliente"];
        $errors = [];
        $errorCodes = [];

        if ($usuarioAdministrar) {
            $usuarioLogado = $usuarioAdministrar;
        }

        try {
            if ($this->request->is(Request::METHOD_GET)) {
                $data = $this->request->getQueryParams();
                $redesId = !empty($data["redes_id"]) ? $data["redes_id"] : null;
                $clientesId = !empty($data["clientes_id"]) ? $data["clientes_id"] : null;
                $dataInicio =  !empty($data["data_inicio"]) ? $data["data_inicio"] : null;
                $dataFim =  !empty($data["data_fim"]) ? $data["data_fim"] : null;
                $typeExport = !empty($data["tipo_exportacao"]) ? $data["tipo_exportacao"] : TYPE_EXPORTATION_DATA_OBJECT;
                $pesquisarPor = !empty($data['pesquisar_por']) ? $data['pesquisar_por'] : null;
                $termo = !empty($data['termo_pesquisa']) ? $data['termo_pesquisa'] : null;
                $usuario = !empty($data['usuario_selecionado']) ? $data['usuario_selecionado'] : null;
                // Se usuário logado for Adm Rede ou Regional, não precisa estar preso a um posto a pesquisa
                if (in_array($usuarioLogado->tipo_perfil, [PROFILE_TYPE_ADMIN_NETWORK, PROFILE_TYPE_ADMIN_REGIONAL])) {
                    $cliente = null;
                }

                $redesId = !empty($rede) ? $rede->id : $redesId;
                $clientesId = !empty($cliente) ? $cliente->id : $clientesId;

                if (empty($redesId)) {
                    // Necessário ter uma
                    $errors[] = MSG_REDES_ID_EMPTY;
                    $errorCodes[] = MSG_REDES_ID_EMPTY_CODE;
                }

                if (empty($dataInicio)) {
                    $errors[] = MSG_DATE_BEGIN_EMPTY;
                    $errorCodes[] = MSG_DATE_BEGIN_EMPTY_CODE;
                }

                if (empty($dataFim)) {
                    $errors[] = MSG_DATE_END_EMPTY;
                    $errorCodes[] = MSG_DATE_END_EMPTY_CODE;
                }

                if (empty($usuario)) {
                    $errors[] = MSG_USUARIOS_ID_EMPTY;
                    $errorCodes[] = MSG_USUARIOS_ID_EMPTY_CODE;
                }

                if (count($errors) > 0) {
                    throw new Exception(MSG_LOAD_EXCEPTION, MSG_LOAD_EXCEPTION_CODE);
                }

                $dataInicio = new DateTime(sprintf("%s 00:00:00", $dataInicio));
                $dataFim = new DateTime(sprintf("%s 23:59:59", $dataFim));


                $reportData = [];

                $entradas = $this->Pontuacoes->getPontuacoesClienteFinal($redesId, $dataInicio, $dataFim, $clientesId, $usuario);

                $saidas = $this->CuponsTransacoes->getCuponsClienteFinal($redesId, $dataInicio, $dataFim, $clientesId, $usuario);

                $headersRelEntrada = new stdClass();
                $headersRelEntrada->nome_rede = 'Rede';
                $headersRelEntrada->estabelecimento = 'Estabelecimento';
                $headersRelEntrada->usuario = 'Usuário';
                $headersRelEntrada->produto = 'Produto';
                $headersRelEntrada->quantidade_pontos = 'Qtd. Pontos';
                $headersRelEntrada->data = 'Data';

                $dadosEntrada = [];

                $headersRelSaida = new stdClass();
                $headersRelSaida->nome_rede = 'Rede';
                $headersRelSaida->estabelecimento = 'Estabelecimento';
                $headersRelSaida->usuario = 'Usuário';
                $headersRelSaida->brinde = 'Brinde';
                $headersRelSaida->pontos = 'Pontos';
                $headersRelSaida->reais = 'Reais';
                $headersRelSaida->unidades = 'Unidades';
                $headersRelSaida->data_resgate = 'Data resgate';
                $headersRelSaida->data_uso = 'Data uso';

                $dadosSaida = [];

                $totalPontosEntrada = 0;
                $totalPontosSaida = 0;
                $totalReaisSaida = 0;
                $totalQuantidadeSaida = 0;

                foreach ($entradas as $entrada) {
                    $itemEntrada = new stdClass();
                    $itemEntrada->nome_rede = $entrada->cliente->redes_has_cliente->rede->nome_rede;
                    $itemEntrada->estabelecimento = $entrada->cliente->nome_fantasia;
                    $itemEntrada->usuario = $entrada->usuario->nome;
                    $itemEntrada->produto = $entrada->gota->nome_parametro;
                    $itemEntrada->quantidade_pontos = $entrada->quantidade_gotas;
                    $itemEntrada->data = $entrada->data->format('d/m/Y');

                    $totalPontosEntrada += (int) $entrada->quantidade_gotas;

                    $dadosEntrada[] = $itemEntrada;
                }
                foreach ($saidas as $saida) {
                    $itemSaida = new stdClass();
                    $itemSaida->nome_rede = $saida->cliente->redes_has_cliente->rede->nome_rede;
                    $itemSaida->estabelecimento = $saida->cliente->nome_fantasia;
                    $itemSaida->usuario = $saida->cupon->usuario->nome;
                    $itemSaida->brinde = $saida->brinde->nome;
                    $itemSaida->pontos = $saida->cupon->valor_pago_gotas;
                    $itemSaida->reais = $saida->cupon->valor_pago_reais;
                    $itemSaida->unidades = $saida->cupon->quantidade;
                    $itemSaida->data_resgate = $saida->data->format('d/m/Y');

                    $uso = $this->CuponsTransacoes->getCuponRelacionado($saida->cupons_id);

                    $itemSaida->data_uso = (!is_null($uso)) ? $uso->data->format('d/m/Y') : "";

                    $totalPontosSaida += (int) $saida->cupon->valor_pago_gotas;
                    $totalReaisSaida += (int) $saida->cupon->valor_pago_reais;
                    $totalQuantidadeSaida += (int) $saida->cupon->quantidade;

                    $dadosSaida[] = $itemSaida;
                }

                $rowTotalEntrada = new stdClass();
                $rowTotalEntrada->nome_rede = "Total:";
                $rowTotalEntrada->estabelecimento = "";
                $rowTotalEntrada->usuario = "";
                $rowTotalEntrada->produto = "";
                $rowTotalEntrada->quantidade_pontos = $totalPontosEntrada;
                $rowTotalEntrada->data = "";

                $dadosEntrada[] = $rowTotalEntrada;

                $rowTotalSaida = new stdClass();
                $rowTotalSaida->nome_rede = "Total:";
                $rowTotalSaida->estabelecimento = "";
                $rowTotalSaida->usuario = "";
                $rowTotalSaida->brinde = "";
                $rowTotalSaida->pontos = $totalPontosSaida;
                $rowTotalSaida->reais  = $totalReaisSaida;
                $rowTotalSaida->unidades  = $totalQuantidadeSaida;
                $rowTotalSaida->data_resgate = "";
                $rowTotalSaida->data_uso = "";

                $dadosSaida[] = $rowTotalSaida;

                if ($typeExport === TYPE_EXPORTATION_DATA_OBJECT) {
                    $relEntrada = new stdClass();
                    $relEntrada->headers = $headersRelEntrada;
                    $relEntrada->rows = $dadosEntrada;

                    $relSaida = new stdClass();
                    $relSaida->headers = $headersRelSaida;
                    $relSaida->rows = $dadosSaida;

                    $dadosReturn = new stdClass();
                    $tituloEntrada = sprintf("Entradas: %s (%s à %s)", "", $dataInicio->format("d/m/Y"), $dataFim->format("d/m/Y"));
                    $dadosReturn->entrada = HtmlUtil::generateHTMLTable($tituloEntrada, $headersRelEntrada, $dadosEntrada, true);
                    $tituloSaida = sprintf("Saídas: %s (%s à %s)", "", $dataInicio->format("d/m/Y"), $dataFim->format("d/m/Y"));
                    $dadosReturn->saida = HtmlUtil::generateHTMLTable($tituloSaida, $headersRelSaida, $dadosSaida, true);
                    // return ResponseUtil::successAPI(MSG_LOAD_DATA_WITH_SUCCESS, $dadosReturn);
                    return ResponseUtil::successAPI(MSG_LOAD_DATA_WITH_SUCCESS, ["data" => $dadosReturn]);
                } elseif (in_array($typeExport, [TYPE_EXPORTATION_DATA_TABLE, TYPE_EXPORTATION_DATA_EXCEL])) {

                    $dadosReturn = new stdClass();
                    $tituloEntrada = sprintf("Entradas: %s (%s à %s)", "", $dataInicio->format("d/m/Y"), $dataFim->format("d/m/Y"));
                    $tituloSaida = sprintf("Saídas: %s (%s à %s)", "", $dataInicio->format("d/m/Y"), $dataFim->format("d/m/Y"));
                    $dadosReturn->entrada = HtmlUtil::generateHTMLTable($tituloEntrada, $headersRelEntrada, $dadosEntrada, true);
                    $dadosReturn->saida = HtmlUtil::generateHTMLTable($tituloSaida, $headersRelSaida, $dadosSaida, true);

                    if ($typeExport === TYPE_EXPORTATION_DATA_TABLE) {
                        return ResponseUtil::successAPI(MSG_LOAD_DATA_WITH_SUCCESS, ['data' => $dadosReturn]);
                    } else {
                        $allTables = sprintf("%s%s", $dadosReturn->entrada, $dadosReturn->saida);
                        $excel = HtmlUtil::wrapContentToHtml($allTables);
                        return ResponseUtil::successAPI(MSG_LOAD_DATA_WITH_SUCCESS, ['data' => $excel]);
                    }
                }

                // return ResponseUtil::successAPI(MSG_LOAD_DATA_WITH_SUCCESS, ['headers' => $titleReportColumns, 'rows' => $reportData]);
            }

            throw new Exception(TYPE_EXPORTATION_DATA_EMPTY, TYPE_EXPORTATION_DATA_EMPTY_CODE);
        } catch (\Throwable $th) {
            $errorMessage = $th->getMessage();
            $errorCode = $th->getCode();

            if (count($errors) == 0) {
                $errors[] = $errorMessage;
                $errorCodes[] = $errorCode;
            }

            for ($i = 0; $i < count($errors); $i++) {
                Log::write("error", sprintf("[%s] %s - %s", MSG_LOAD_DATA_WITH_ERROR, $errorCodes[$i], $errors[$i]));
            }

            return ResponseUtil::errorAPI(MSG_LOAD_DATA_WITH_ERROR, $errors, [], $errorCodes);
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

                    return ResponseUtil::successAPI(MSG_LOAD_DATA_WITH_SUCCESS, array("cliente" => $posto));
                } else {
                    $errors = array();
                    $errors[] = $usuario["tipo_perfil"] <= PROFILE_TYPE_WORKER ? MSG_USUARIOS_WORKER_NOT_ASSOCIATED_CLIENTE : MSG_USUARIOS_CANT_SEARCH;

                    return ResponseUtil::errorAPI(MSG_LOAD_DATA_NOT_FOUND, $errors);
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
