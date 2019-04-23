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
use App\Custom\RTI\ResponseUtil;

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
    public function index($clientesId)
    {
        $arraySet = array("redesId", "clientesId", "brindes", "usuario", "dataPost");
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

        if (empty($redesId)) {
            $rede = $this->RedesHasClientes->getRedesHasClientesByClientesId($clientesId);
            $redesId = $rede["redes_id"];
        }

        $nome = null;
        $codigoPrimario = 0;
        $tempoUsoBrindeMin = null;
        $tempoUsoBrindeMax = null;
        $ilimitado = null;
        $tipoEquipamento = null;
        $tipoCodigoBarras = null;
        $precoPadraoMin = null;
        $precoPadraoMax = null;
        $valorMoedaVendaPadraoMin = null;
        $valorMoedaVendaPadraoMax = null;

        if ($this->request->is('post')) {
            $dataPost = $this->request->getData();

            $nome = !empty($dataPost["nome"]) ? $dataPost["nome"] : null;
            $codigoPrimario = !empty($dataPost["codigo_primario"]) ? $dataPost["codigo_primario"] : 0;
            $tempoUsoBrindeMin = !empty($dataPost["tempo_uso_brinde_min"]) ? $dataPost["tempo_uso_brinde_min"] : null;
            $tempoUsoBrindeMax = !empty($dataPost["tempo_uso_brinde_max"]) ? $dataPost["tempo_uso_brinde_max"] : null;
            $ilimitado = !empty($dataPost["ilimitado"]) ? $dataPost["ilimitado"] : null;
            $tipoEquipamento = !empty($dataPost["tipo_equipamento"]) ? $dataPost["tipo_equipamento"] : null;
            $tipoCodigoBarras = !empty($dataPost["tipo_codigo_barras"]) ? $dataPost["tipo_codigo_barras"] : null;
            $precoPadraoMin = !empty($dataPost["preco_padrao_min"]) ? $dataPost["preco_padrao_min"] : null;
            $precoPadraoMax = !empty($dataPost["preco_padrao_max"]) ? $dataPost["preco_padrao_max"] : null;
            $valorMoedaVendaPadraoMin = !empty($dataPost["valor_moeda_venda_padrao_min"]) ? $dataPost["valor_moeda_venda_padrao_min"] : null;
            $valorMoedaVendaPadraoMax = !empty($dataPost["valor_moeda_venda_padrao_max"]) ? $dataPost["valor_moeda_venda_padrao_max"] : null;
        }

        $brindes = $this->Brindes->findBrindes(0, $clientesId, $nome, $codigoPrimario, $tempoUsoBrindeMin, $tempoUsoBrindeMax, $ilimitado, $tipoEquipamento, $tipoCodigoBarras, $precoPadraoMin, $precoPadraoMax, $valorMoedaVendaPadraoMin, $valorMoedaVendaPadraoMax);
        $brindes = $this->paginate($brindes, array("limit" => 10));
        $this->set(compact($arraySet));
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
    public function adicionar($clientesId)
    {
        $arraySet = array("editMode", "brinde", "clientesId", "textoCodigoSecundario");
        $editMode = 0;
        $sessaoUsuario = $this->getSessionUserVariables();
        $usuarioAdministrador = $sessaoUsuario["usuarioAdministrador"];
        $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"];
        $usuarioLogado = $sessaoUsuario["usuarioLogado"];
        $cliente = $sessaoUsuario["cliente"];

        if ($usuarioAdministrar) {
            $this->usuarioLogado = $usuarioAdministrar;
        }

        $textoCodigoSecundario = $this->usuarioLogado["tipo_perfil"] == PROFILE_TYPE_ADMIN_DEVELOPER ? "Tempo / Cód. Secundário*" : "Tempo (min.)";

        $rede = $sessaoUsuario["rede"];
        $brinde = $this->Brindes->newEntity();

        try {
            // verifica se usuário é pelo menos administrador.

            if ($this->usuarioLogado['tipo_perfil'] > PROFILE_TYPE_ADMIN_LOCAL) {
                $this->securityUtil->redirectUserNotAuthorized($this);
            }
            // Verifica permissão do usuário na rede / unidade da rede

            if ($usuarioLogado["tipo_perfil"] > PROFILE_TYPE_ADMIN_DEVELOPER) {
                $temAcesso = $this->securityUtil->checkUserIsClienteRouteAllowed($this->usuarioLogado, $this->Clientes, $this->ClientesHasUsuarios, $clientesId, $rede["id"]);

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

                // Trata tipo de equipamento

                if ($this->usuarioLogado["tipo_perfil"] !=  PROFILE_TYPE_ADMIN_DEVELOPER) {
                    $brinde["tipo_equipamento"] = TYPE_EQUIPMENT_PRODUCT_SERVICES;
                }

                // Se desconto, preco_padrao e valor_moeda_venda_padrao devem estar preenchidos
                if (($data['tipo_venda'] == TYPE_SELL_DISCOUNT_TEXT) && (empty($data['preco_padrao']) || empty($data['valor_moeda_venda_padrao']))) {
                    $errors[] = "Preço Padrão ou Preço em Reais devem ser informados!";
                }
                // se é Opcional mas preco_padrao ou valor_moeda_venda_padrao estão vazios
                if (($data['tipo_venda'] == TYPE_SELL_CURRENCY_OR_POINTS_TEXT) && (empty($data['preco_padrao']) && empty($data['valor_moeda_venda_padrao']))) {
                    $errors[] = "Preço Padrão e Preço em Reais devem ser informados!";
                }

                // Se o brinde for do tipo SMART SHOWER, é ilimitado
                $tipoEquipamento = !empty($data["tipo_equipamento"]) ? $data["tipo_equipamento"] : null;
                $codigoPrimario = !empty($data["codigo_primario"]) ? $data["codigo_primario"] : 0;

                if (empty($tipoEquipamento)) {
                    $errors[] = MESSAGE_TYPE_EQUIPMENT_EMPTY;
                }

                if ($tipoEquipamento == TYPE_EQUIPMENT_RTI && empty($codigoPrimario)) {
                    $errors[] = MESSAGE_TYPE_EQUIPMENT_RTI_PRIMARY_CODE_EMPTY;
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
                    $brinde["ilimitado"] = 1;
                } else {
                    $brinde["ilimitado"] = $data["ilimitado"];
                }

                $brinde["preco_padrao"] = (float)$data['preco_padrao'];

                // DebugUtil::printArray($data);

                // Procura o brinde NA UNIDADE e Verifica se tem o mesmo nome,
                $brindeCheck = $this->Brindes->findBrindes(0, $clientesId, $data["nome"], $data["codigo_primario"], $data["tempo_uso_brinde"], $data["tempo_uso_brinde"], $data["ilimitado"], $data["tipo_equipamento"], $data["tipo_codigo_barras"]);
                if ($brindeCheck->first()) {
                    $this->Flash->warning(__('Já existe um registro com o nome {0}', $brinde['nome']));
                } else {
                    $enviouNovaImagem = isset($data["img-upload"]) && strlen($data["img-upload"]) > 0;

                    if ($enviouNovaImagem) {
                        $brinde["nome_img"] = $this->_preparaImagemBrindeParaGravacao($data);
                    }

                    $brinde["clientes_id"] = $clientesId;

                    $brinde = $this->Brindes->patchEntity($brinde, $data);
                    $brinde = $this->Brindes->saveBrinde($brinde);
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
                                = $this->BrindesEstoque->addEstoque($brinde["id"], $this->usuarioLogado["id"], 0, 0, TYPE_OPERATION_INITIALIZE, null, null, 0, null);
                        }

                        // cadastra novo preço
                        $brindePrecoSave = $this->BrindesPrecos->addBrindePreco(
                            $brinde["id"],
                            $clientesId,
                            STATUS_AUTHORIZATION_PRICE_AUTHORIZED,
                            $brinde["preco_padrao"],
                            $brinde["valor_moeda_venda_padrao"]
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
            $this->Flash->error($e->getMessage());

            $messageString = __("Não foi possível gravar um novo Brinde!");

            $trace = $e->getTrace();
            $mensagem = array('status' => false, 'message' => $messageString, 'errors' => $trace);
            $messageStringDebug = __("{0} - {1} . [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $messageString, $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write("error", $messageStringDebug);
            Log::write("error", $trace);
        }
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
     *
     * @deprecated 1.0 esta action não será mais utilizada.
     */
    public function brindesMinhaRede($param = null)
    {
        $sessaoUsuario = $this->getSessionUserVariables();
        $usuarioAdministrador = $sessaoUsuario["usuarioAdministrador"];
        $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"];
        $usuarioLogado = $sessaoUsuario["usuarioLogado"];
        $cliente = $sessaoUsuario["cliente"];
        $rede = $sessaoUsuario["rede"];

        $temAcesso = $this->securityUtil->checkUserIsClienteRouteAllowed($this->usuarioLogado, $this->Clientes, $this->ClientesHasUsuarios, array(), $rede["id"]);

        // Se não tem acesso, redireciona
        if (!$temAcesso) {
            return $this->securityUtil->redirectUserNotAuthorized($this, $this->usuarioLogado);
        }

        $clientes_ids = [];

        // pega a matriz da rede

        $redesHasClientes = $this->RedesHasClientes->findMatrizOfRedesByRedesId($rede->id);
        $unidadesIds = $redesHasClientes["clientes_id"];

        $conditions = [];

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();

            $nome = !empty($data["nome"]) ? $data["nome"] : null;
            $ilimitado = strlen($data["ilimitado"]) > 0 ? $data["ilimitado"] : null;
            $precoPadrao = !empty($data["preco_padrao"]) ? $data["preco_padrao"] : null;
            $valorMoedaVendaPadrao = strlen($data["valor_moeda_venda_padrao"]) > 0 ? $data["valor_moeda_venda_padrao"] : null;
            $habilitado = strlen($data["habilitado"]) > 0 ? $data["habilitado"] : null;

            $conditions[] = array("nome like '%{$nome}%'");

            if (strlen($ilimitado) > 0) {
                $conditions[] = array("ilimitado" => $ilimitado);
            }

            if ($precoPadrao > 0) {
                $conditions[] = array("preco_padrao" => (float)$precoPadrao);
            }

            if (strlen($valorMoedaVendaPadrao) > 0) {
                if ($valorMoedaVendaPadrao == "0,00") {
                    $conditions[] = array(
                        "OR" => array(
                            "valor_moeda_venda_padrao IS NULL",
                            "valor_moeda_venda_padrao " => $valorMoedaVendaPadrao
                        )
                    );
                } else {
                    $conditions[] = array("valor_moeda_venda_padrao" => $valorMoedaVendaPadrao);
                }
            }

            if (strlen($habilitado) > 0) {
                $conditions[] = array("habilitado" => $habilitado);
            }
        }

        array_push($conditions, ['clientes_id ' => $unidadesIds]);

        // $brindes = $this->Brindes->findBrindes($conditions);
        $brindes = $this->paginate($brindes, ['limit' => 10]);
        $unidadesIds = $this->Clientes->getClientesListByRedesId($rede["id"])->toArray();

        $arraySet = array('brindes', 'unidadesIds', 'cliente', "rede");
        $this->set(compact($arraySet));
        $this->set('_serialize', $arraySet);
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
     * Edit method
     *
     * @param string|null $id Brinde id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function editarBrindeRede($id = null)
    {
        $arraySet = array(
            "editMode",
            "brinde",
            "imagemOriginal",
            "clientes",
            "tiposBrindesCliente"
        );
        $brinde = $this->Brindes->get($id);

        $brinde["preco_padrao"] = number_format($brinde["preco_padrao"], 2);
        $brinde["valor_moeda_venda_padrao"] = number_format($brinde["valor_moeda_venda_padrao"], 2);

        $editMode = 1;
        $sessaoUsuario = $this->getSessionUserVariables();
        $usuarioAdministrador = $sessaoUsuario["usuarioAdministrador"];
        $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"];
        $usuarioLogado = $sessaoUsuario["usuarioLogado"];
        $cliente = $sessaoUsuario["cliente"];
        $rede = $sessaoUsuario["rede"];
        $tiposBrindesRedesId = $brinde["tipos_brindes_redes_id"];
        $imagemOriginal = null;
        $imagemOriginalDisco = null;

        if (strlen($brinde["nome_img"]) > 0) {
            $imagemOriginal = __("{0}{1}{2}{3}", Configure::read("appAddress"), "webroot", Configure::read("imageGiftPathRead"), $brinde["nome_img"]);
            $imagemOriginalDisco = __("{0}{1}{2}", WWW_ROOT, Configure::read("imageGiftPathRead"), $brinde["nome_img"]);
        }

        if ($usuarioAdministrador) {
            $this->usuarioLogado = $usuarioAdministrar;
        }

        $tiposBrindesCliente = $this->TiposBrindesClientes->getTiposBrindesHabilitadosCliente(array($cliente["id"]));
        $tiposBrindesCliente = $tiposBrindesCliente->toArray();
        $tiposBrindesClienteTemp = array();

        foreach ($tiposBrindesCliente as $tipoBrinde) {
            $tipo = array(
                "text" => $tipoBrinde["brinde_necessidades_especiais"] ? __("{0} {1}", $tipoBrinde["nome"], "PNE") : $tipoBrinde["nome"],
                "value" => $tipoBrinde["id"],
                "id" => "tipos_brindes_redes_id",
                "data-obrigatorio" => $tipoBrinde["obrigatorio"],
                "data-tipo-principal-codigo-brinde" => $tipoBrinde["tipo_principal_codigo_brinde_default"],
                "data-tipo-secundario-codigo-brinde" => $tipoBrinde["tipo_secundario_codigo_brinde_default"],
            );
            $tiposBrindesClienteTemp[] = $tipo;
        }
        $tiposBrindesCliente = $tiposBrindesClienteTemp;

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();

            $errors = array();

            // Se desconto, preco_padrao e valor_moeda_venda_padrao devem estar preenchidos
            if (($data['tipo_venda'] == TYPE_SELL_DISCOUNT_TEXT) && (empty($data['preco_padrao']) || empty($data['valor_moeda_venda_padrao']))) {
                $errors[] = "Preço Padrão ou Preço em Reais devem ser informados!";
            }
            // se é Opcional mas preco_padrao ou valor_moeda_venda_padrao estão vazios
            if (($data['tipo_venda'] == TYPE_SELL_CURRENCY_OR_POINTS_TEXT) && (empty($data['preco_padrao']) && empty($data['valor_moeda_venda_padrao']))) {
                $errors[] = "Preço Padrão e Preço em Reais devem ser informados!";
            }

            $tiposBrindesRedesId = !empty($data["tipos_brindes_redes_id"]) ? $data["tipos_brindes_redes_id"] : null;

            if (empty($tiposBrindesRedesId)) {
                $errors[] = "É necessário selecionar um tipo de brinde!";
            }

            if (count($errors) > 0) {

                foreach ($errors as $error) {
                    $this->Flash->error($error);
                }

                $this->set(compact($arraySet));
                $this->set('_serialize', $arraySet);

                return;
            }

            $brindeCheck = $this->Brindes->findBrindesByConditions(
                $rede["id"],
                array(),
                null,
                $brinde['nome'],
                $brinde["tipos_brindes_redes_id"],
                $brinde["tempo_uso_brinde"],
                $brinde["ilimitado"]
            );

            if ($brindeCheck["id"] != $id) {
                $this->Flash->warning(__('Já existe um registro com o nome {0}', $brinde['nome']));
            } else {
                // Se o brinde for do tipo SMART SHOWER, é ilimitado

                $tipoBrindeRede = $this->TiposBrindesRedes->getTiposBrindesRedeById($tiposBrindesRedesId);
                if ($tipoBrindeRede["tipo_principal_codigo_brinde_default"] >= 1 && $tipoBrindeRede["tipo_principal_codigo_brinde_default"] <= 4) {
                    $brinde["ilimitado"] = 1;
                } else {
                    $brinde["ilimitado"] = $data["ilimitado"];
                }

                $enviouNovaImagem = isset($data["img-upload"]) && strlen($data["img-upload"]) > 0;

                $brinde = $this->Brindes->patchEntity($brinde, $data);

                // Preserva o id base de tipos brindes
                $brinde["tipos_brindes_redes_id"] = $tiposBrindesRedesId;
                $brinde["preco_padrao"] = (float)$data['preco_padrao'];

                if ($enviouNovaImagem) {
                    $brinde["nome_img"] = $this->_preparaImagemBrindeParaGravacao($data);
                }

                if ($this->Brindes->saveBrinde($brinde)) {
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

        // TODO: NÃO MUDAR!
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
            $tipoPagamento = !empty($data['tipo_pagamento']) ? $data['tipo_pagamento'] : null;

            if (empty($tipoPagamento)) {
                ResponseUtil::errorAPI(MESSAGE_GENERIC_ERROR, array("Erro! Transação não definida!"));
            }

            $resultado = $this->ClientesHasBrindesHabilitados->getBrindesPorClienteId(
                $clientes_id,
                array(),
                array(),
                0,
                0,
                array(),
                array(),
                array(),
                $tipoPagamento,
                array(TYPE_SELL_CURRENCY_OR_POINTS_TEXT, TYPE_SELL_DISCOUNT_TEXT, TYPE_SELL_FREE_TEXT)
            );

            $brindesHabilitadosCliente = $resultado["brindes"]["data"];

            $brindesTemp = array();

            foreach ($brindesHabilitadosCliente as $brindeHabilitadoCliente) {
                $brindeHabilitadoCliente["brinde"]["nome_img"] =
                    __(
                        "{0}{1}{2}",
                        Configure::read("webrootAddress"),
                        Configure::read("imageGiftPathRead"),
                        $brindeHabilitadoCliente["brinde"]["nome_img"]
                    );

                $nome = $brindeHabilitadoCliente["brinde"]["nome"];
                $isBrindeShower = $brindeHabilitadoCliente["tipos_brindes_redes_id"] >= 1 && $brindeHabilitadoCliente["tipos_brindes_redes_id"] <= 4;

                if ($isBrindeShower) {
                    $nome = __("{0} ({1} minutos)", $nome, $brindeHabilitadoCliente["brinde"]["tempo_uso_brinde"]);
                }

                if ($brindeHabilitadoCliente["tipos_brindes_cliente"]["tipos_brindes_rede"]["brinde_necessidades_especiais"]) {
                    $brindeHabilitadoCliente["brinde"]["nome_brinde_detalhado"] = $nome . " (PNE)";
                } else {
                    $brindeHabilitadoCliente["brinde"]["nome_brinde_detalhado"] = $nome;
                }

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

        return;
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
    }
}
