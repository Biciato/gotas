<?php

namespace App\Controller;

#region References

use \DateTime;
use \Exception;
use App\Controller\AppController;
use App\Custom\RTI\Security;
use App\Custom\RTI\DateTimeUtil;
use App\Custom\RTI\ImageUtil;
use App\Custom\RTI\FilesUtil;
use App\Custom\RTI\DebugUtil;
use App\Custom\RTI\ResponseUtil;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Log\Log;

#endregion

/**
 * Controller para Brindes
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 2017-08-01
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
    protected $usuarioLogado = null;

    #region Actions

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index($clientesId = null)
    {
        try {
            $arraySet = [
                "categoriasBrindesList",
                "redesId",
                "clientesId",
                "brindes",
                "usuario",
                "dataPost"
            ];
            $sessaoUsuario = $this->getSessionUserVariables();
            $usuarioAdministrador = $sessaoUsuario["usuarioAdministrador"];
            $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"];
            $usuarioLogado = $sessaoUsuario["usuarioLogado"];
            $dataPost = array();

            if ($usuarioAdministrar) {
                $this->usuarioLogado = $usuarioAdministrar;
            }

            $cliente = $sessaoUsuario["cliente"];

            $rede = $sessaoUsuario["rede"];
            $redesId = $rede["id"];

            if (empty($clientesId) && empty($cliente)) {
                $this->Flash->error(RULE_CLIENTES_NEED_TO_INFORM);
                return $this->redirect("/");
            }

            if (empty($redesId)) {
                if (empty($clientesId)) {
                    $clientesId = $cliente["id"];
                }

                $rede = $this->RedesHasClientes->getRedesHasClientesByClientesId($clientesId);
                $redesId = $rede["redes_id"];
            }

            $categoriasBrindesList = $this->CategoriasBrindes->getCategoriasBrindesList($rede->id);

            $categoriasBrindesId = null;
            $nome = null;
            $codigoPrimario = 0;
            $tempoUsoBrindeMin = null;
            $tempoUsoBrindeMax = null;
            $ilimitado = null;
            $tipoEquipamento = null;
            $tipoVenda = null;
            $tipoCodigoBarras = null;
            $precoPadraoMin = null;
            $precoPadraoMax = null;
            $valorMoedaVendaPadraoMin = null;
            $valorMoedaVendaPadraoMax = null;

            if ($this->request->is('post')) {
                $dataPost = $this->request->getData();

                $categoriasBrindesId = !empty($dataPost["categorias_brindes_id"]) ? $dataPost["categorias_brindes_id"] : null;
                $nome = !empty($dataPost["nome"]) ? $dataPost["nome"] : null;
                $codigoPrimario = !empty($dataPost["codigo_primario"]) ? $dataPost["codigo_primario"] : 0;
                $tempoUsoBrindeMin = !empty($dataPost["tempo_uso_brinde_min"]) ? $dataPost["tempo_uso_brinde_min"] : null;
                $tempoUsoBrindeMax = !empty($dataPost["tempo_uso_brinde_max"]) ? $dataPost["tempo_uso_brinde_max"] : null;
                $ilimitado = !empty($dataPost["ilimitado"]) ? $dataPost["ilimitado"] : null;
                $tipoEquipamento = !empty($dataPost["tipo_equipamento"]) ? $dataPost["tipo_equipamento"] : null;
                $tipoVenda = !empty($dataPost["tipo_venda"]) ? $dataPost["tipo_venda"] : null;
                $tipoCodigoBarras = !empty($dataPost["tipo_codigo_barras"]) ? $dataPost["tipo_codigo_barras"] : null;
                $precoPadraoMin = !empty($dataPost["preco_padrao_min"]) ? $dataPost["preco_padrao_min"] : null;
                $precoPadraoMax = !empty($dataPost["preco_padrao_max"]) ? $dataPost["preco_padrao_max"] : null;
                $valorMoedaVendaPadraoMin = !empty($dataPost["valor_moeda_venda_padrao_min"]) ? $dataPost["valor_moeda_venda_padrao_min"] : null;
                $valorMoedaVendaPadraoMax = !empty($dataPost["valor_moeda_venda_padrao_max"]) ? $dataPost["valor_moeda_venda_padrao_max"] : null;
            }

            if (empty($tipoVenda)) {
                $tipoVenda = array(TYPE_SELL_FREE_TEXT, TYPE_SELL_DISCOUNT_TEXT, TYPE_SELL_CURRENCY_OR_POINTS_TEXT);
            } else {
                $tipoVenda = array($tipoVenda);
            }

            $brindes = $this->Brindes->findBrindes(0, $clientesId, $categoriasBrindesId, $nome, $codigoPrimario, $tempoUsoBrindeMin, $tempoUsoBrindeMax, $ilimitado, $tipoEquipamento, $tipoVenda, $tipoCodigoBarras, $precoPadraoMin, $precoPadraoMax, $valorMoedaVendaPadraoMin, $valorMoedaVendaPadraoMax);

            // DebugUtil::printArray($brindes);
            $brindes = $this->Paginate($brindes, array("limit" => 10));
            $this->set(compact($arraySet));
        } catch (\Exception $e) {
            $trace = $e->getTraceAsString();
            $messageString = __("Erro ao exibir lista de brindes!");
            $messageStringDebug = __("{0} - {1} . [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $messageString, $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
            $messageStringUser = sprintf("%s - %s", $messageString, $e->getMessage());

            Log::write("error", $messageStringDebug);
            Log::write("error", $trace);

            throw new Exception($messageStringUser);
        }
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
        $arraySet = array("brinde", "clientesId", "redesId", "textoCodigoSecundario", "editMode", "precoAtualBrinde", "imagem");
        $brinde = $this->Brindes->getBrindeById($id);
        $sessaoUsuario = $this->getSessionUserVariables();
        $usuarioAdministrador = $sessaoUsuario["usuarioAdministrador"];
        $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"];
        $usuarioLogado = $sessaoUsuario["usuarioLogado"];

        if ($usuarioAdministrar) {
            $this->usuarioLogado = $usuarioAdministrar;
        }

        $cliente = $sessaoUsuario["cliente"];
        $rede = $sessaoUsuario["rede"];
        $redesId = $rede["id"];
        $clientesId = $brinde["clientes_id"];

        if (empty($redesId)) {
            $rede = $this->RedesHasClientes->getRedesHasClientesByClientesId($clientesId);
            $redesId = $rede["redes_id"];
        }

        $imagem = sprintf("%s%s", PATH_IMAGES_READ_BRINDES, $brinde["nome_img"]);

        $clientesId = $brinde["clientes_id"];
        $redeHasCliente = $this->RedesHasClientes->getRedesHasClientesByClientesId($clientesId);
        $redesId = $redeHasCliente["rede"]["redes_id"];
        $editMode = 1;
        $textoCodigoSecundario = $this->usuarioLogado["tipo_perfil"] == PROFILE_TYPE_ADMIN_DEVELOPER ? "Tempo / Cód. Secundário*" : "Tempo (min.)";
        // $precoAtualBrinde = $this->BrindesPrecos->getUltimoPrecoBrinde($brinde["id"], STATUS_AUTHORIZATION_PRICE_AUTHORIZED);
        $precoAtualBrinde = $brinde["preco_atual"];
        // $brinde["estoque"] = $this->BrindesEstoque->getActualStockForBrindesEstoque($id);

        $this->set(compact($arraySet));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function adicionar($clientesId)
    {
        $arraySet = [
            "editMode",
            "brinde",
            "clientesId",
            "permiteCadastrarBrindeRede",
            "redesId",
            "textoCodigoSecundario",
            "categoriasBrindesList"
        ];
        $editMode = 0;
        $sessaoUsuario = $this->getSessionUserVariables();
        $usuarioAdministrador = $sessaoUsuario["usuarioAdministrador"];
        $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"];
        $usuarioLogado = $sessaoUsuario["usuarioLogado"];
        $cliente = $sessaoUsuario["cliente"];
        $rede = $sessaoUsuario["rede"];

        if ($usuarioAdministrar) {
            $this->usuarioLogado = $usuarioAdministrar;
            $usuarioLogado = $usuarioAdministrar;
        }

        if (empty($cliente)) {
            $cliente = $this->Clientes->get($clientesId);
            $redeHasCliente = $this->RedesHasClientes->getRedesHasClientesByClientesId($cliente->id);
            $rede = $redeHasCliente->rede;
        }

        /**
         * Se o posto (clientes) for matriz e o usuário logado for <= PROFILE_TYPE_ADMIN_NETWORK,
         * permite cadastrar brinde rede
         */
        $permiteCadastrarBrindeRede = $usuarioLogado->tipo_perfil <= PROFILE_TYPE_ADMIN_NETWORK && $cliente->matriz;

        $textoCodigoSecundario = $this->usuarioLogado["tipo_perfil"] == PROFILE_TYPE_ADMIN_DEVELOPER ? "Tempo / Cód. Secundário*" : "Tempo (min.)";
        $categoriasBrindesList = $this->CategoriasBrindes->getCategoriasBrindesList($rede->id);

        if (empty($rede)) {
            $redeHasCliente = $this->RedesHasClientes->getRedesHasClientesByClientesId($clientesId);
            $rede = $redeHasCliente["rede"];
        }

        $redesId = $rede["id"];

        $brinde = $this->Brindes->newEntity();

        try {
            // verifica se usuário é pelo menos administrador.

            if ($this->usuarioLogado['tipo_perfil'] > PROFILE_TYPE_ADMIN_LOCAL) {
                $this->securityUtil->redirectUserNotAuthorized($this);
            }
            // Verifica permissão do usuário na rede / unidade da rede

            if ($usuarioLogado["tipo_perfil"] > PROFILE_TYPE_ADMIN_DEVELOPER) {
                $temAcesso = $this->securityUtil->checkUserIsClienteRouteAllowed($this->usuarioLogado, $this->Clientes, $this->ClientesHasUsuarios, array($clientesId), $rede["id"]);

                // Se não tem acesso, redireciona
                if (!$temAcesso) {
                    return $this->securityUtil->redirectUserNotAuthorized($this, $this->usuarioLogado);
                }
            }

            if (strlen($brinde->nome_img) > 0) {
                $imagemOriginal = __("{0}{1}", Configure::read("imageGiftPath"), $brinde->nome_img);
            }

            if ($this->request->is('post')) {
                $data = $this->request->getData();
                $errors = array();
                $data["clientes_id"] = $clientesId;

                $nome = !empty($data["nome"]) ? $data["nome"] : null;
                $categoriasBrindesId = !empty($data["categorias_brindes_id"]) ? $data["categorias_brindes_id"] : null;
                $tipoCodigoBarras = !empty($data["tipo_codigo_barras"]) ? $data["tipo_codigo_barras"] : null;
                // Se o brinde for do tipo SMART SHOWER, é ilimitado
                $tipoEquipamento = !empty($data["tipo_equipamento"]) ? $data["tipo_equipamento"] : null;
                $tipoVenda = !empty($data["tipo_venda"]) ? $data["tipo_venda"] : null;
                $codigoPrimario = !empty($data["codigo_primario"]) ? $data["codigo_primario"] : 0;
                $tempoUsoBrinde = !empty($data["tempo_uso_brinde"]) ? $data["tempo_uso_brinde"] : 0;
                $brindeRede = !empty($data["brinde_rede"]) ? $data["brinde_rede"] : 0;
                $ilimitado = !empty($data["ilimitado"]) ? $data["ilimitado"] : false;
                $habilitado = !empty($data["habilitado"]) ? $data["habilitado"] : true;
                $tipoVenda = !empty($data["tipo_venda"]) ? $data["tipo_venda"] : $brinde["tipo_venda"];
                $precoPadrao = !empty($data["preco_padrao"]) ? (float) $data["preco_padrao"] : 0;
                $valorMoedaVendaPadrao = !empty($data["valor_moeda_venda_padrao"]) ? (float) $data["valor_moeda_venda_padrao"] : 0;
                $nomeImg = !empty($data["nome_img"]) ? $data["nome_img"] : null;

                // Trata tipo de equipamento

                if ($this->usuarioLogado["tipo_perfil"] !=  PROFILE_TYPE_ADMIN_DEVELOPER) {
                    $brinde["tipo_equipamento"] = TYPE_EQUIPMENT_PRODUCT_SERVICES;
                }

                // Se desconto, preco_padrao e valor_moeda_venda_padrao devem estar preenchidos
                if (($data['tipo_venda'] == TYPE_SELL_DISCOUNT_TEXT) && (empty($precoPadrao) || empty($valorMoedaVendaPadrao))) {
                    $errors[] = "Preço Padrão ou Preço em Reais devem ser informados!";
                }
                // se é Opcional mas preco_padrao ou valor_moeda_venda_padrao estão vazios
                if (($data['tipo_venda'] == TYPE_SELL_CURRENCY_OR_POINTS_TEXT) && (empty($precoPadrao) && empty($valorMoedaVendaPadrao))) {
                    $errors[] = "Preço Padrão e Preço em Reais devem ser informados!";
                }

                if (empty($tipoEquipamento)) {
                    $errors[] = MSG_BRINDES_TYPE_EQUIPMENT_EMPTY;
                }

                if ($tipoEquipamento == TYPE_EQUIPMENT_RTI && empty($codigoPrimario)) {
                    $errors[] = MSG_BRINDES_TYPE_EQUIPMENT_RTI_PRIMARY_CODE_EMPTY;
                }

                if (count($errors) > 0) {

                    foreach ($errors as $error) {
                        $this->Flash->error($error);
                    }

                    $this->set(compact($arraySet));
                    $this->set('_serialize', $arraySet);

                    return;
                }

                if ($codigoPrimario > 0 && $codigoPrimario < 5) {
                    $ilimitado = 1;
                }

                $precoPadrao = (float) $precoPadrao;

                // Procura o brinde NA UNIDADE e Verifica se tem o mesmo nome,
                $brindeCheck = $this->Brindes->findBrindes(0, $clientesId, $categoriasBrindesId, $nome, $codigoPrimario, $tempoUsoBrinde, $tempoUsoBrinde, $ilimitado, $tipoEquipamento, array($tipoVenda), $tipoCodigoBarras);
                if ($brindeCheck->first()) {
                    $this->Flash->warning(__('Já existe um registro com o nome {0}', $brinde['nome']));
                } else {
                    $enviouNovaImagem = isset($data["img-upload"]) && strlen($data["img-upload"]) > 0;

                    if ($enviouNovaImagem) {
                        $nomeImg = $this->_preparaImagemBrindeParaGravacao($data);
                    }

                    $brinde->clientes_id = $clientesId;
                    $brinde->categorias_brindes_id = $categoriasBrindesId;
                    $brinde->nome = $nome;
                    $brinde->codigo_primario = $codigoPrimario;
                    $brinde->tempo_uso_brinde = $tempoUsoBrinde;
                    $brinde->brinde_rede = $brindeRede;
                    $brinde->ilimitado = $brindeRede ? true : $ilimitado;
                    $brinde->habilitado = $habilitado;
                    $brinde->tipo_equipamento = $tipoEquipamento;
                    $brinde->tipo_venda = $tipoVenda;
                    $brinde->tipo_codigo_barras = $tipoCodigoBarras;
                    $brinde->preco_padrao = $precoPadrao;
                    $brinde->valor_moeda_venda_padrao = $valorMoedaVendaPadrao;
                    $brinde->nome_img = $nomeImg;

                    $brinde = $this->Brindes->saveUpdate($brinde);

                    $errors = $brinde->errors();

                    if ($brinde) {
                        /* estoque só deve ser criado nas seguintes situações.
                         * 1 - O Brinde está sendo vinculado a um cadastro de loja
                         *  no sistema (Isto é, se ele não foi anteriormente )
                         * 2 - Não é ilimitado
                         * 3 - Se não houver cadastro anterior
                         */

                        if (!$brinde["ilimitado"]) {
                            // Brinde Novo, cadastra estoque
                            $result
                                = $this->BrindesEstoque->addBrindeEstoque($brinde["id"], $this->usuarioLogado["id"], 0, 0, TYPE_OPERATION_INITIALIZE, null, null, 0, null);
                        }

                        // cadastra novo preço
                        $brindePrecoSave = $this->BrindesPrecos->addBrindePreco(
                            $brinde["id"],
                            $clientesId,
                            $this->usuarioLogado["id"],
                            STATUS_AUTHORIZATION_PRICE_AUTHORIZED,
                            $precoPadrao,
                            $valorMoedaVendaPadrao
                        );
                    }

                    if ($brindePrecoSave) {
                        $this->Flash->success(__(Configure::read('messageSavedSuccess')));

                        return $this->redirect(['action' => 'index', $clientesId]);
                    }
                }
                $this->Flash->error(__(Configure::read('messageSavedError')));
            }

            $this->set(compact($arraySet));
            $this->set('_serialize', $arraySet);
        } catch (\Exception $e) {
            $trace = $e->getTraceAsString();
            $messageString = __("Não foi possível gravar um novo Brinde!");
            $messageStringDebug = __("{0} - {1} . [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $messageString, $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            $this->Flash->error($e->getMessage());
            Log::write("error", $messageStringDebug);
            Log::write("error", $trace);
        }
    }

    /**
     * Action de Adicionar
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function editar($id)
    {
        $arraySet = [
            "editMode",
            "brinde",
            "clientesId",
            "permiteCadastrarBrindeRede",
            "redesId",
            "textoCodigoSecundario",
            "imagemOriginal",
            "imagemExibicao",
            "categoriasBrindesList"
        ];

        $editMode = 1;
        $sessaoUsuario = $this->getSessionUserVariables();
        $usuarioAdministrador = $sessaoUsuario["usuarioAdministrador"];
        $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"];
        $usuarioLogado = $sessaoUsuario["usuarioLogado"];
        $cliente = $sessaoUsuario["cliente"];
        $rede = $sessaoUsuario["rede"];

        if ($usuarioAdministrar) {
            $this->usuarioLogado = $usuarioAdministrar;
        }

        $brinde = $this->Brindes->getBrindeById($id);

        if (empty($cliente)) {
            $cliente = $this->Clientes->get($brinde->clientes_id);
            $redeHasCliente = $this->RedesHasClientes->getRedesHasClientesByClientesId($cliente->id);
            $rede = $redeHasCliente->rede;
        }

        /**
         * Se o posto (clientes) for matriz e o usuário logado for <= PROFILE_TYPE_ADMIN_NETWORK,
         * permite cadastrar brinde rede
         */
        $permiteCadastrarBrindeRede = $usuarioLogado->tipo_perfil <= PROFILE_TYPE_ADMIN_NETWORK && $cliente->matriz;

        $imagemExibicao = !empty($brinde->nome_img) ? sprintf("%s%s", PATH_IMAGES_READ_BRINDES, $brinde->nome_img) : null;
        $imagemOriginal = !empty($brinde->nome_img) ? sprintf("%s%s", PATH_IMAGES_BRINDES, $brinde->nome_img) : null;
        $categoriasBrindesList = $this->CategoriasBrindes->getCategoriasBrindesList($rede->id);

        try {
            if (empty($brinde)) {
                throw new Exception(MESSAGE_RECORD_NOT_FOUND);
            }
        } catch (\Throwable $th) {

            $trace = $th->getTraceAsString();
            $messageString = __($th->getMessage());
            $messageStringDebug = __("{0} - {1} . [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $messageString, $th->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            $this->Flash->error($messageString);
            Log::write("error", $messageStringDebug);
            Log::write("error", $trace);

            return $this->redirect("/");
        }

        $clientesId = $brinde["clientes_id"];

        $textoCodigoSecundario = $this->usuarioLogado["tipo_perfil"] == PROFILE_TYPE_ADMIN_DEVELOPER ? "Tempo / Cód. Secundário*" : "Tempo (min.)";

        $rede = $sessaoUsuario["rede"];

        if (empty($rede)) {
            $redeHasCliente = $this->RedesHasClientes->getRedesHasClientesByClientesId($clientesId);
            $rede = $redeHasCliente["rede"];
        }

        $redesId = $rede["id"];

        try {
            // verifica se usuário é pelo menos administrador.

            if ($this->usuarioLogado['tipo_perfil'] > PROFILE_TYPE_ADMIN_LOCAL) {
                $this->securityUtil->redirectUserNotAuthorized($this);
            }
            // Verifica permissão do usuário na rede / unidade da rede

            if ($usuarioLogado["tipo_perfil"] > PROFILE_TYPE_ADMIN_DEVELOPER) {
                $temAcesso = $this->securityUtil->checkUserIsClienteRouteAllowed($this->usuarioLogado, $this->Clientes, $this->ClientesHasUsuarios, array($clientesId), $rede["id"]);

                // Se não tem acesso, redireciona
                if (!$temAcesso) {
                    return $this->securityUtil->redirectUserNotAuthorized($this, $this->usuarioLogado);
                }
            }

            if (strlen($brinde->nome_img) > 0) {
                $imagemOriginal = __("{0}{1}", Configure::read("imageGiftPath"), $brinde->nome_img);
            }

            if ($this->request->is(array('post', 'put'))) {
                $data = $this->request->getData();

                $errors = array();

                $nome = !empty($data["nome"]) ? $data["nome"] : null;
                $categoriasBrindesId = !empty($data["categorias_brindes_id"]) ? $data["categorias_brindes_id"] : null;
                $tipoCodigoBarras = !empty($data["tipo_codigo_barras"]) ? $data["tipo_codigo_barras"] : null;
                $tipoEquipamento = !empty($data["tipo_equipamento"]) ? $data["tipo_equipamento"] : null;
                $tipoVenda = !empty($data["tipo_venda"]) ? $data["tipo_venda"] : $brinde["tipo_venda"];
                $codigoPrimario = !empty($data["codigo_primario"]) ? $data["codigo_primario"] : 0;
                $tempoUsoBrinde = !empty($data["tempo_uso_brinde"]) ? $data["tempo_uso_brinde"] : 0;
                $brindeRede = !empty($data["brinde_rede"]) ? $data["brinde_rede"] : 0;
                $ilimitado = !empty($data["ilimitado"]) ? $data["ilimitado"] : false;
                $habilitado = !empty($data["habilitado"]) ? $data["habilitado"] : true;
                $precoPadrao = !empty($data["preco_padrao"]) ? (float) $data["preco_padrao"] : 0;
                $valorMoedaVendaPadrao = !empty($data["valor_moeda_venda_padrao"]) ? (float) $data["valor_moeda_venda_padrao"] : 0;
                $nomeImg = !empty($data["nome_img"]) ? $data["nome_img"] : null;


                // Se desconto, preco_padrao e valor_moeda_venda_padrao devem estar preenchidos
                if (($tipoVenda == TYPE_SELL_DISCOUNT_TEXT) && (empty($precoPadrao) || empty($valorMoedaVendaPadrao))) {
                    $errors[] = "Preço Padrão ou Preço em Reais devem ser informados!";
                }
                // se é Opcional mas preco_padrao ou valor_moeda_venda_padrao estão vazios
                if (($tipoVenda == TYPE_SELL_CURRENCY_OR_POINTS_TEXT) && (empty($precoPadrao) && empty($valorMoedaVendaPadrao))) {
                    $errors[] = "Preço Padrão e Preço em Reais devem ser informados!";
                }

                // Se o brinde for do tipo SMART SHOWER, é ilimitado
                $tipoEquipamento = !empty($data["tipo_equipamento"]) ? $data["tipo_equipamento"] : null;
                $codigoPrimario = !empty($data["codigo_primario"]) ? $data["codigo_primario"] : 0;

                if (!in_array($tipoEquipamento, array(TYPE_EQUIPMENT_PRODUCT_SERVICES, TYPE_EQUIPMENT_RTI))) {
                    $errors[] = MSG_BRINDES_TYPE_EQUIPMENT_INCORRECT;
                }

                if (empty($tipoEquipamento)) {
                    $errors[] = MSG_BRINDES_TYPE_EQUIPMENT_EMPTY;
                }

                if ($tipoEquipamento == TYPE_EQUIPMENT_RTI && empty($codigoPrimario)) {
                    $errors[] = MSG_BRINDES_TYPE_EQUIPMENT_RTI_PRIMARY_CODE_EMPTY;
                }

                if (count($errors) > 0) {

                    foreach ($errors as $error) {
                        $this->Flash->error($error);
                    }

                    $this->set(compact($arraySet));
                    $this->set('_serialize', $arraySet);

                    return;
                }

                if ($codigoPrimario > 0 && $codigoPrimario < 5) {
                    $ilimitado = 1;
                }

                // Procura o brinde NA UNIDADE e Verifica se tem o mesmo nome, mas com outro Id
                $brindeCheck = $this->Brindes->findBrindes(0, $clientesId, $categoriasBrindesId, $nome, $codigoPrimario, $tempoUsoBrinde, $tempoUsoBrinde, $ilimitado, $tipoEquipamento, array($tipoVenda), $tipoCodigoBarras);
                $brindeCheck = $brindeCheck->first();
                if ($brindeCheck && $brindeCheck["id"] != $brinde["id"]) {
                    $this->Flash->warning(__('Já existe um registro com o nome {0}', $brinde['nome']));
                } else {
                    $enviouNovaImagem = isset($data["img-upload"]) && strlen($data["img-upload"]) > 0;

                    if ($enviouNovaImagem) {
                        if (!empty($brinde["nome_img"])) {
                            $imagemRemover = __("{0}{1}", PATH_IMAGES_BRINDES,  $brinde["nome_img"]);
                            if (file_exists($imagemOriginal)) {
                                unlink($imagemOriginal);
                            }
                        }
                        $nomeImg = $this->_preparaImagemBrindeParaGravacao($data);
                    }

                    $brinde["clientes_id"] = $clientesId;

                    // $brinde = $this->Brindes->patchEntity($brinde, $data);

                    $brinde->clientes_id = $clientesId;
                    $brinde->categorias_brindes_id = $categoriasBrindesId;
                    $brinde->nome = $nome;
                    $brinde->codigo_primario = $codigoPrimario;
                    $brinde->tempo_uso_brinde = $tempoUsoBrinde;
                    $brinde->brinde_rede = $brindeRede;
                    $brinde->ilimitado = $brindeRede ? true : $ilimitado;
                    $brinde->habilitado = $habilitado;
                    $brinde->tipo_equipamento = $tipoEquipamento;
                    $brinde->tipo_venda = $tipoVenda;
                    $brinde->tipo_codigo_barras = $tipoCodigoBarras;
                    $brinde->preco_padrao = $precoPadrao;
                    $brinde->valor_moeda_venda_padrao = $valorMoedaVendaPadrao;
                    $brinde->nome_img = $enviouNovaImagem ? $nomeImg : $brinde->nome_img;

                    $brinde = $this->Brindes->saveUpdate($brinde);

                    $errors = $brinde->errors();

                    if ($brinde) {
                        $this->Flash->success(MESSAGE_SAVED_SUCCESS);

                        return $this->redirect(['action' => 'index', $clientesId]);
                    }
                }
                $this->Flash->error(MESSAGE_SAVED_ERROR);
            }

            $this->set(compact($arraySet));
            $this->set('_serialize', $arraySet);
        } catch (\Exception $e) {
            $trace = $e->getTraceAsString();
            $messageString = __("Não foi possível gravar um novo Brinde!");
            $messageStringDebug = __("{0} - {1} . [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $messageString, $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            $this->Flash->error($e->getMessage());
            Log::write("error", $messageStringDebug);
            Log::write("error", $trace);
        }
    }

    /**
     * Action de remover
     *
     * @param string|null $id Brinde id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $brinde = $this->Brindes->getBrindeById($id);
        if ($this->Brindes->deleteBrinde($id)) {
            $this->Flash->success(MESSAGE_DELETE_SUCCESS);
        } else {
            $this->Flash->error(MESSAGE_DELETE_ERROR);
        }

        return $this->redirect(sprintf("/Brindes/index/%s", $brinde["clientes_id"]));
    }

    /**
     * BrindesController::alterarEstadoBrinde
     *
     * Altera estado do Brinde
     *
     * @param $id Id brinde
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-04-26
     *
     * @return void
     */
    public function alterarEstadoBrinde()
    {
        if ($this->request->is(array("post"))) {
            $data = $this->request->query;
            $brindesId = !empty($data["id"]) ? $data["id"] : null;

            $errors = array();
            if (empty($brindesId)) {
                $errors[] = MSG_ID_EMPTY;
            }

            if (count($errors) > 0) {
                foreach ($errors as  $error) {
                    $this->Flash->error($error);

                    return $this->redirect("/Pages/display");
                }
            }

            $brindeSave = $this->Brindes->updateEnabledStatusBrinde($brindesId);

            if ($brindeSave) {
                $this->Flash->success(MESSAGE_SAVED_SUCCESS);
            } else {
                $this->Flash->error(sprintf("%s %s", MESSAGE_SAVED_ERROR, $brindeSave));
            }
            return $this->redirect(sprintf("Brindes/index/%s", $brindeSave["clientes_id"]));
        }
    }

    /**
     * Método para brindes da rede (mostra os brindes que a rede possui)
     *
     * @return \Cake\Http\Response|void
     */
    public function escolherPostoConfigurarBrinde($param = null)
    {
        $sessaoUsuario = $this->getSessionUserVariables();
        $usuarioAdministrador = $sessaoUsuario["usuarioAdministrador"];
        $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"];
        $usuarioLogado = $sessaoUsuario["usuarioLogado"];
        $cliente = $sessaoUsuario["cliente"];
        $rede = $sessaoUsuario["rede"];

        $temAcesso = $this->securityUtil->checkUserIsClienteRouteAllowed($this->usuarioLogado, $this->Clientes, $this->ClientesHasUsuarios, array(), $rede["id"]);

        if ($usuarioAdministrar) {
            $this->usuarioLogado = $usuarioAdministrar;
            $usuarioLogado = $usuarioAdministrar;
        }

        // Se não tem acesso, redireciona
        if (!$temAcesso) {
            return $this->securityUtil->redirectUserNotAuthorized($this, $this->usuarioLogado);
        }

        $clientesIds = array();

        if ($usuarioLogado["tipo_perfil"] == PROFILE_TYPE_ADMIN_REGIONAL) {
            $redesHasClientesAdmin = $this->RedesHasClientesAdministradores->getRedesHasClientesAdministradorByUsuariosId($usuarioLogado["id"]);

            $redesHasClientesAdmin = $redesHasClientesAdmin->toArray();
            foreach ($redesHasClientesAdmin["redes_has_cliente"]["clientes"] as $cliente) {
                $clientesIds[] = $cliente["id"];
            }
        } elseif ($usuarioLogado["tipo_perfil"] > PROFILE_TYPE_ADMIN_REGIONAL) {
            $clientesIds[] = $cliente["id"];
        }

        $nomeFantasia = null;
        $razaoSocial = null;
        $cnpj = null;

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();

            $nomeFantasia = !empty($data["nome_fantasia"]) ? $data["nome_fantasia"] : null;
            $razaoSocial = !empty($data["razao_social"]) ? $data["razao_social"] : null;
            $cnpj = !empty($data["cnpj"]) ? $data["cnpj"] : null;
        }

        $redesHasClientes = $this->RedesHasClientes->findRedesHasClientes($rede["id"], $clientesIds, $nomeFantasia, $razaoSocial, $cnpj);

        $clientes = array();
        foreach ($redesHasClientes as $redeHasCliente) {
            $clientes[] = $redeHasCliente["cliente"];
        }

        // $clientes = $this->paginate($clientes, ['limit' => 10]);

        $arraySet = array('brindes', 'unidadesIds', 'cliente', "rede", "clientes");
        $this->set(compact($arraySet));
        $this->set('_serialize', $arraySet);
    }

    /**
     * Metodo par ver os detalhes de um brinde da rede
     *
     * @deprecated 1.0
     *
     * @param string|null $id Brinde id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function verBrindeRede($id = null)
    {

        try {

            $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
            $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');
            $rede = $this->request->session()->read('Rede.Grupo');

            if ($usuarioAdministrador) {
                $this->usuarioLogado = $usuarioAdministrar;
            }

            $cliente = $this->securityUtil->checkUserIsClienteRouteAllowed(
                $this->usuarioLogado,
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
     * Action para impressao rapida (view de funcionário)
     *
     * @return void
     */
    public function resgateBrinde()
    {
        $urlRedirectConfirmacao = array("controller" => "Brindes", "action" => "resgate_brinde");
        $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
        $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

        if ($usuarioAdministrador) {
            $this->usuarioLogado = $usuarioAdministrar;
        }

        $usuarioLogado = $this->usuarioLogado;

        $usuario = $this->Usuarios->newEntity();
        $transportadora = $this->Transportadoras->newEntity();
        $veiculo = $this->Veiculos->newEntity();

        $funcionario = $this->Usuarios->getUsuarioById($this->usuarioLogado['id']);

        $rede = $this->request->session()->read('Rede.Grupo');

        if (empty($rede)) {
            // Melhorar sistemática
            $this->Flash->error("Você não tem permissão para visualizar esta tela!");
            return $this->redirect(array("controller" => "Pages", "action" => "display"));
        }
        // Pega unidades que tem acesso
        $clientes_ids = [];

        $unidades_ids = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($rede["id"], $this->usuarioLogado['id'], false);

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
            "usuarioLogado",
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

        // TODO: NÃO MUDAR! O Crop Width e Height são fixos para a API
        $resizeSucesso = ImageUtil::resizeImage($imagemOrigem, 600, 600, $valueX, $valueY, $width, $height, 90);

        // Se imagem foi redimensionada, move e atribui o nome para gravação
        if ($resizeSucesso == 1) {
            rename($imagemOrigem, $imagemDestino);

            $nomeImagem = $data["img-upload"];
        }

        return $nomeImagem;
    }

    #region Action de Relatórios

    /**
     * Relatóriod de Brindes de cada Rede
     *
     * @return \Cake\Http\Response|void
     *
     * @deprecated 1.0
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
                $whereConditions[] = ["ilimitado" => (bool) $data['ilimitado']];
            }

            if (strlen($data['habilitado']) > 0) {
                $whereConditions[] = ["habilitado" => (bool) $data['habilitado']];
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

            $rede = $this->Redes->getRedeById((int) $value);

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

            $redeItem['brindes'] = $brindes;
            unset($arrayWhereConditions);

            $redes[] = $redeItem;
        }

        $arraySet = [
            'redesList',
            'redes'
        ];

        $this->set(compact($arraySet));
    }

    #endregion

    #region Ajax Methods

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

        $mensagem = array("status" => 1, "message" => null);

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
            $clientesId = !empty($data["clientes_id"]) ? (int) $data['clientes_id'] : null;
            $tipoPagamento = !empty($data['tipo_pagamento']) ? $data['tipo_pagamento'] : null;
            $tipoVenda = !empty($data['tipo_venda']) ? explode(",", $data['tipo_venda']) : null;
            // $tipoVenda = !empty($data['tipo_venda']) ? $data['tipo_venda'] : null;
            $desconto = !empty($data['desconto']) ? $data["desconto"] : false;

            if (empty($clientesId)) {
                ResponseUtil::errorAPI(MESSAGE_GENERIC_ERROR, array(MSG_BRINDES_CLIENTES_ID_REQUIRED));
            }

            if (empty($tipoVenda)) {
                ResponseUtil::errorAPI(MESSAGE_GENERIC_ERROR, array("Erro! Tipo de Venda não definida!"));
            }

            if (!in_array($tipoPagamento, array(TYPE_PAYMENT_POINTS, TYPE_PAYMENT_MONEY))) {
                ResponseUtil::errorAPI(MESSAGE_GENERIC_ERROR, array("Erro! Tipo de Operacao não definida!"));
            }

            $brindes = $this->Brindes->getBrindesSell($clientesId, $tipoVenda, $tipoPagamento);

            $count = sizeof($brindes);
        }

        $arraySet = array('brindes', 'count');

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);

        return;
    }

    #endregion

    #region REST Services

    /**
     * Brindes::getBrindesUnidadesParaTopBrindesAPI
     *
     * Obtêm lista de brindes conforme parâmetros informados
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-09-16
     *
     * @param int $redesId Id da Rede (Opcional)
     * @param $clientes_id Id do Cliente (Posto)
     *
     * @return json_encode Brindes
     */
    public function getBrindesListAPI()
    {
        $sessaoUsuario = $this->getSessionUserVariables();

        $usuario = $sessaoUsuario["usuarioLogado"];
        $rede = $sessaoUsuario["rede"];
        // $cliente

        try {
            if ($this->request->is("GET")) {
                $data = $this->request->getQueryParams();
                $redesId = 0;
                $brindes = null;

                if ($usuario->tipo_perfil == PROFILE_TYPE_ADMIN_DEVELOPER) {
                    // Só permite especificar a rede se for RTI/Desenvolvedor
                    $redesId = !empty($data["redes_id"]) ? $data["redes_id"] : $rede->id;
                } else {
                    $redesId = $rede->id;
                }

                $clientesId = !empty($data["clientes_id"]) ? $data["clientes_id"] : null;

                $brindes = $this->Brindes->getList($redesId, $clientesId, 0);
                $brindes = ["brindes" => $brindes->toArray()];

                return ResponseUtil::successAPI(MSG_LOAD_DATA_WITH_SUCCESS, ["data" => $brindes]);
            }
        } catch (\Throwable $th) {
            $message = sprintf("[%s] %s", MESSAGE_LOAD_EXCEPTION, $th->getMessage());
            Log::write("error", $message);

            return ResponseUtil::errorAPI(MESSAGE_LOAD_EXCEPTION, [$th->getMessage()]);
        }
    }

    /**
     * Brindes::getBrindesUnidadeAPI
     *
     * Obtem todos os Brindes de uma Unidade
     *
     * @param $clientes_id Id da unidade que deseja adquirir o brinde
     * @param $tipos_brindes_redes_id Id do tipo de brinde da rede que deseja filtrar
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 23/04/2018
     *
     * @return json Brindes Habilitados
     */
    public function getBrindesUnidadeAPI()
    {
        $mensagem = array();
        $brindes = null;
        $count = 0;
        $redesId = 0;

        try {
            if ($this->request->is(['post'])) {
                $data = $this->request->getData();
                // $tipoPagamento = !empty($data["tipo_pagamento"]) ? $data["tipo_pagamento"] : TYPE_PAYMENT_POINTS;
                // cliente api no momento só compra via gotas, pois precisa da interação humana para recebimento de dinheiro
                $tipoPagamento = TYPE_PAYMENT_POINTS;
                $clientesId = isset($data['clientes_id']) ? $data['clientes_id'] : null;
                $categoriasBrindesId = $data['categorias_brindes_id'] ?? null;
                $nome = !empty($data["nome"]) ? $data["nome"] : null;
                $categoriasBrindesId = $data["categorias_brindes_id"] ?? null;
                $tiposVenda = [TYPE_SELL_CURRENCY_OR_POINTS_TEXT, TYPE_SELL_FREE_TEXT];

                $precoMin = isset($data["preco_min"]) ? (float) $data["preco_min"] : null;
                $precoMax = isset($data["preco_max"]) ? (float) $data["preco_max"] : null;

                if (empty($clientesId)) {
                    $mensagem = array(
                        "status" => 0,
                        "message" => Configure::read("messageOperationFailureDuringProcessing"),
                        "errors" => array("É necessário informar um Ponto de Atendimento para obter os brindes do Ponto de Atendimento!")
                    );

                    $arraySet = [
                        "mensagem"
                    ];

                    $this->set(compact($arraySet));
                    $this->set("_serialize", $arraySet);

                    return;
                }

                $whereConditionsBrindes = array();

                if (!empty($nome)) {
                    $whereConditionsBrindes[] = array("Brindes.nome like '%{$data["nome"]}%'");
                }

                $orderConditions = array();
                $pagination = array();

                if (isset($data["order_by"])) {
                    $orderConditions = $data["order_by"];
                }

                if (isset($data["pagination"])) {
                    $pagination = $data["pagination"];

                    if ($pagination["page"] < 1) {
                        $pagination["page"] = 1;
                    }
                }

                $orderPrecoArray = array();

                foreach ($orderConditions as $key => $value) {
                    if (strpos($key, "preco_atual.") !== false) {
                        $keyProperty = explode(".", $key);
                        $orderPrecoArray[$keyProperty[1]] = $value;
                        unset($orderConditions[$key]);
                    }
                }

                $resultado = $this->Brindes->findBrindes(
                    $redesId,
                    $clientesId,
                    $categoriasBrindesId,
                    $nome,
                    null,
                    null,
                    null,
                    null,
                    null,
                    $tiposVenda,
                    null,
                    null,
                    null,
                    null,
                    null,
                    0,
                    $orderConditions
                );

                $resultado = $resultado->where(["Brindes.habilitado" => 1]);

                $todosBrindes = $resultado;
                $brindesAtuais = $resultado;
                if (count($pagination) > 0) {
                    $brindesAtuais = $brindesAtuais->limit($pagination["limit"])->page($pagination["page"]);
                }

                $todosBrindes = $todosBrindes->toArray();
                $brindesAtuais = $brindesAtuais->toArray();
                $resultado = ResponseUtil::prepareReturnDataPagination($todosBrindes, $brindesAtuais, "brindes", $pagination);
                $mensagem = $resultado["mensagem"];
                $brindes = $resultado["brindes"];
                $brindesSort = $brindes["data"];

                if (count($orderPrecoArray) > 0) {
                    // @TODO isto carece de ajustes no futuro, pois a ordenação deve vir do banco. No momento, funciona.
                    usort($brindesSort, function ($a, $b) use ($orderPrecoArray) {
                        $key = key($orderPrecoArray);

                        if (strtoupper($orderPrecoArray[$key]) == "ASC") {
                            // return $a[$key] > $b[$key];
                            return $a["preco_atual"][$key] > $b["preco_atual"][$key];
                        } else {
                            // return $a[$key] < $b[$key];
                            return $a["preco_atual"][$key] < $b["preco_atual"][$key];
                        }
                    });
                }

                $brindes["data"] = $brindesSort;
            }
        } catch (\Exception $e) {
            $trace = $e->getTraceAsString();

            $messageString = __("Não foi possível obter dados de brindes da unidade selecionada!");
            $messageStringDebug = __("{0} - {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $messageString, $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write("error", $messageStringDebug);
            Log::write("error", $trace);

            $mensagem = array('status' => false, 'message' => $messageString, 'errors' => $trace);
        }

        $arraySet = array(
            'mensagem',
            'brindes',
        );

        $this->set(compact($arraySet));
        $this->set('_serialize', $arraySet);
    }

    /**
     * Brindes::getBrindesUnidadesParaTopBrindesAPI
     *
     * Obtem os brindes disponíveis para Top Brindes
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-08-08
     *
     * @param $clientes_id Id do Cliente (Posto)
     *
     * @return json_encode Brindes
     */
    public function getBrindesUnidadesParaTopBrindesAPI()
    {
        $sessaoUsuario = $this->getSessionUserVariables();

        $rede = $sessaoUsuario["rede"];

        try {
            $clientesId = 0;

            if ($this->request->is("GET")) {
                $data = $this->request->getQueryParams();

                $clientesId = $data["clientes_id"] ?? 0;
            }

            if (empty($clientesId)) {
                throw new Exception(MESSAGE_TOP_BRINDES_CLIENTES_ID_NOT_EMPTY);
            }

            $topBrindesAtuais = $this->TopBrindes->getTopBrindes($rede->id, $clientesId);
            $topBrindesAtuais = $topBrindesAtuais->select("Brinde.id");
            $idsTopBrindes = [];

            foreach ($topBrindesAtuais as $topBrinde) {
                $idsTopBrindes[] = $topBrinde->Brinde->id;
            }

            $brindesPosto = $this->Brindes->getBrindesPostoNotIn($clientesId, $idsTopBrindes);
            $brindesPosto = $brindesPosto->toArray();

            foreach ($brindesPosto as $brinde) {
                $estoqueAtual = $this->BrindesEstoque->getActualStockForBrindesEstoque($brinde->id);
                $brinde->status_estoque = ($estoqueAtual["estoque_atual"] <= 0 && !$brinde->ilimitado) ? "Esgotado" : "Normal";
            }

            $data = ["brindes" => $brindesPosto];

            return ResponseUtil::successAPI(MSG_LOAD_DATA_WITH_SUCCESS, ['data' => $data]);
        } catch (\Throwable $th) {
            $message = sprintf("[%s] %s", MESSAGE_LOAD_EXCEPTION, $th->getMessage());
            Log::write("error", $message);

            return ResponseUtil::errorAPI(MESSAGE_LOAD_EXCEPTION, [$th->getMessage()]);
        }
    }

    #endregion

    #region Métodos Comuns

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
    }

    #endregion
}
