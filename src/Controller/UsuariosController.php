<?php

namespace App\Controller;

use stdClass;
use \DateTime;
use \Exception;
use \Throwable;
use App\Controller\AppController;
use App\Custom\RTI\DateTimeUtil;
use App\Custom\RTI\EmailUtil;
use App\Custom\RTI\ExcelUtil;
use App\Custom\RTI\ImageUtil;
use App\Custom\RTI\NumberUtil;
use App\Custom\RTI\ResponseUtil;
use App\Custom\RTI\DebugUtil;
use App\Model\Entity\Usuario;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Http\Client\Request;
use Cake\I18n\Number;
use Cake\Log\Log;
use Cake\Mailer\Email;
use Cake\Network\Exception\UnauthorizedException;
use Cake\Routing\Router;
use Cake\Utility\Security;
use Firebase\JWT\JWT;

/**
 * Usuarios Controller
 *
 * Controller para Usuários
 *
 * @property \App\Model\Table\UsuariosTable $Usuarios
 *
 * @method \App\Model\Entity\Usuario[] paginate($object = null, array $settings = [])
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 2017-08-01
 */
class UsuariosController extends AppController
{
    protected $usuarioLogado = null;

    #region Actions Web

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $perfisUsuariosList = Configure::read("profileTypesTranslatedDevel");
        $tipoPerfil = null;
        $tipoPerfilMin = Configure::read("profileTypes")["AdminDeveloperProfileType"];
        $tipoPerfilMax = Configure::read("profileTypes")["UserProfileType"];
        $nome = null;
        $email = null;
        $cpf = null;
        $docEstrangeiro = null;

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();

            $tipoPerfil = strlen($data["tipo_perfil"]) > 0 ? $data["tipo_perfil"] : null;

            if (!empty($tipoPerfil)) {
                $tipoPerfilMin = $tipoPerfil;
                $tipoPerfilMax = $tipoPerfil;
            }

            $nome = !empty($data["nome"]) ? $data["nome"] : null;
            $email = !empty($data["email"]) ? $data["email"] : null;
            $cpf = !empty($data["cpf"]) ? $this->cleanNumber($data["cpf"]) : null;
            $docEstrangeiro = !empty($data["doc_estrangeiro"]) ? $data["doc_estrangeiro"] : null;
        }

        if (strlen($tipoPerfil) == 0) {
            $tipoPerfilMin = Configure::read('profileTypes')['AdminNetworkProfileType'];
            $tipoPerfilMax = Configure::read('profileTypes')['UserProfileType'];
        } else {
            $tipoPerfilMin = $tipoPerfil;
            $tipoPerfilMax = $tipoPerfil;
        }

        $usuarios = $this->Usuarios->findAllUsuarios(null, array(), $nome, $email, null, $tipoPerfilMin, $tipoPerfilMax, $cpf, $docEstrangeiro, null, 1);

        $usuarios = $this->paginate($usuarios, array('limit' => 10, "order" => array("Usuarios.nome" => "ASC")));

        $unidades_ids = $this->Clientes->find('list')->toArray();

        // DebugUtil::printArray($usuarios);

        $arraySet = array("usuarios", "unidades_ids", "perfisUsuariosList");
        $this->set(compact($arraySet));
        $this->set('_serialize', $arraySet);
    }

    /**
     * View method
     *
     * @param string|null $id Usuario id.
     *
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException
     *  When record not found.
     */
    public function view($id = null)
    {
        $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
        $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

        $rede = $this->request->session()->read("Rede.Grupo");

        if ($usuarioAdministrador) {
            $this->usuarioLogado = $usuarioAdministrar;
            $usuarioLogado = $this->usuarioLogado;
        }

        $usuario = $this->Usuarios->get(
            $id,
            [
                'contain' => []
            ]
        );

        $arraySet = array("usuario", "usuarioLogado", "rede");

        $this->set(compact($arraySet));
        $this->set('_serialize', $arraySet);
    }

    /**
     * Exibe detalhes de usuário (action para administrador)
     *
     * @param string|null $id Usuario id.
     *
     * @return \Cake\Http\Response|void
     *
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function detalhesUsuario($id = null)
    {
        $usuario = $this->Usuarios->get(
            $id,
            [
                'contain' => []
            ]
        );

        $this->set('usuario', $usuario);
        $this->set('_serialize', ['usuario']);
    }

    /**
     * Método para editar dados de usuário (modo de administrador)
     *
     * @param string|null $id Usuario id.
     *
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     *
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function editarUsuario($id = null)
    {
        try {
            $arraySet = array('usuario', 'usuarioLogadoTipoPerfil', "usuarioLogado", "senhaObrigatoriaEdicao");
            $sessaoUsuario = $this->getSessionUserVariables();
            $usuarioAdministrador = $sessaoUsuario["usuarioAdministrador"];
            $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"];
            $usuarioLogadoTipoPerfil = PROFILE_TYPE_USER;
            $usuarioLogado = $sessaoUsuario["usuarioLogado"];

            if ($usuarioAdministrador) {
                $this->usuarioLogado = $usuarioAdministrar;
                $usuarioLogado = $usuarioAdministrar;
            }

            $senhaObrigatoriaEdicao = $usuarioLogado["tipo_perfil"] >= PROFILE_TYPE_ADMIN_NETWORK && $usuarioLogado["tipo_perfil"] <= PROFILE_TYPE_MANAGER;
            $rede = $this->request->session()->read('Rede.Grupo');
            $usuario = $this->Usuarios->get($id, array('contain' => []));

            $usuario["sexo"] = isset($usuario["sexo"]) ? $usuario["sexo"] : -1;
            if ($this->request->is(['post', 'put'])) {
                $data = $this->request->getData();
                $senhaUsuario = $data["senha"];

                if ($senhaObrigatoriaEdicao) {
                    $result = ((new DefaultPasswordHasher)->check($senhaUsuario, $usuario->senha));

                    if (!$result) {
                        $this->Flash->error(MSG_USUARIOS_PASSWORD_INCORRECT);
                        $this->set(compact($arraySet));
                        $this->set('_serialize', $arraySet);
                        return;
                    }

                    // Apenas garante que não vai atualizar a senha do usuário
                    unset($data["senha"]);
                }

                $usuario = $this->Usuarios->patchEntity($usuario, $data, ['validate' => 'EditUsuarioInfo']);
                $errors = $usuario->errors();
                $usuario = $this->Usuarios->save($usuario);

                if ($usuario) {
                    $this->Flash->success(__(Configure::read('messageSavedSuccess')));
                    $url = Router::url(['controller' => 'Usuarios', 'action' => 'meus_clientes']);

                    return $this->response = $this->response->withLocation($url);
                }

                $this->Flash->error(__(Configure::read('messageSavedError')));

                // exibe os erros logo acima identificados
                foreach ($errors as $key => $error) {
                    $key = key($error);
                    $this->Flash->error(__("{0}", $error[$key]));
                }
            }
            $this->set(compact($arraySet));
            $this->set('_serialize', $arraySet);
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao editar dados de usuário: {0} em: {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Exibe action de perfil do usuário
     *
     * @return \Cake\Http\Response|void
     */
    public function meuPerfil()
    {
        $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
        $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

        if ($usuarioAdministrador) {
            $this->usuarioLogado = $usuarioAdministrar;
        }

        $usuario = $this->Usuarios->get($this->usuarioLogado['id']);

        $this->set('usuario', $usuario);
        $this->set('_serialize', ['usuario']);
    }

    /**
     * Adiciona Conta de usuário
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function registrar()
    {
        $usuario = $this->Usuarios->newEntity();
        $transportadora = $this->Transportadoras->newEntity();
        $veiculo = $this->Veiculos->newEntity();

        $sessaoUsuario = $this->getSessionUserVariables();
        $usuarioAdministrador = $sessaoUsuario["usuarioAdministrador"];
        $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"];
        $usuarioLogado = $sessaoUsuario["usuarioLogado"];

        $arraySet = array(
            "usuario",
            "usuarioLogado",
            // "transportadoraPath",
            // "veiculoPath",
            "usuarioLogado",
            "veiculo",
            "transportadora"
        );

        if ($usuarioAdministrador) {
            $this->usuarioLogado = $usuarioAdministrar;
            $usuarioLogado = $usuarioAdministrar;
        }

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();

            if (!empty($data["senha"])) {
                $data["senha"] = str_replace("?", "", $data["senha"]);
            }

            if (!empty($data["confirm_senha"])) {
                $data["confirm_senha"] = str_replace("?", "", $data["confirm_senha"]);
            }

            // DebugUtil::printArray($data);

            $cpf = preg_replace("/\D/", "", $data["cpf"]);

            $usuarioCheck = $this->Usuarios->getUsuarioByCPF($cpf);
            $usuarioData = $data;
            // só atribui o id pois o resto é tudo informação nova
            // $usuarioData["id"] = $usuarioCheck->id;
            // $usuarioData = $this->Usuarios->patchEntity($usuarioCheck, $usuarioData);



            $cliente = null;

            if (isset($this->usuarioLogado)) {
                $cliente_has_usuario =
                    $this->ClientesHasUsuarios->findClienteHasUsuario(
                        array('ClientesHasUsuarios.usuarios_id' => $this->usuarioLogado['id'])
                    );

                $cliente_id = isset($cliente_has_usuario) ? $cliente_has_usuario->clientes_id : null;
                $clienteAdministrar = $this->request->session()->read('Rede.PontoAtendimento');
                $transportadoraData = !empty($usuarioData['TransportadorasHasUsuarios']['Transportadoras']) ? $usuarioData['TransportadorasHasUsuarios']['Transportadoras']  : null;
                $veiculosData = !empty($usuarioData['UsuariosHasVeiculos']['Veiculos']) ? $usuarioData['UsuariosHasVeiculos']['Veiculos']  : null;
            }

            unset($usuarioData['TransportadorasHasUsuarios']);
            unset($usuarioData['UsuariosHasVeiculos']);
            unset($usuarioData['transportadora']);

            if ($usuarioData['doc_invalido'] == true) {
                $nomeDoc = strlen($usuarioData['cpf']) > 0 ? $usuarioData['cpf'] : $usuarioData['doc_estrangeiro'];
                $nomeDoc = $this->cleanNumberAndLetters($nomeDoc);
                $nomeDoc = $nomeDoc . '.jpg';
                $currentPath = Configure::read('temporaryDocumentUserPath');
                $newPath = Configure::read('documentUserPath');

                $fotoDocumento = $this->moveDocumentPermanently($currentPath . $nomeDoc, $newPath, null, '.jpg');

                $usuarioData['foto_documento'] = $fotoDocumento;
                $usuarioData['aguardando_aprovacao'] = true;
                $usuarioData['data_limite_aprovacao'] = date('Y-m-d H:i:s', strtotime('+7 days'));
            }

            if (isset($this->usuarioLogado)) {
                if (strlen($veiculosData["placa"]) > 0) {
                    $veiculoDataBase = $this->Veiculos->getVeiculoByPlaca($veiculosData['placa']);

                    $veiculoDataBase = $veiculoDataBase["veiculo"];
                    if ($veiculosData) {
                        if ($veiculoDataBase) {
                            $veiculo = $veiculoDataBase;
                        } else {
                            $veiculo = $this->Veiculos->patchEntity($veiculo, $veiculosData);
                            $veiculo = $this->Veiculos->save($veiculo);
                        }
                    }
                }

                if (strlen($transportadoraData['cnpj']) > 0 || strlen($transportadoraData['nome_fantasia']) > 0 || strlen($transportadoraData['razao_social']) > 0) {
                    $transportadoraDataBase = $this->Transportadoras->findTransportadoraByCNPJ($transportadoraData['cnpj']);

                    if ($transportadoraDataBase) {
                        $transportadora = $transportadoraDataBase;
                    } else {
                        $transportadora = $this->Transportadoras->patchEntity($transportadora, $transportadoraData);

                        $transportadora = $this->Transportadoras->save($transportadora);
                    }
                }
            }

            $usuarioData["cpf"] = preg_replace("/\D/", "", $usuarioData["cpf"]);

            // Se e-mail não informado e cpf informado, copia informação para o campo em questão
            if (empty($usuarioData["email"]) && !empty($usuarioData["cpf"])) {
                $usuarioData["email"] = $usuarioData["cpf"];
            }

            $passwordEncrypt = $this->cryptUtil->encrypt($usuarioData['senha']);

            $updateClientesHasUsuarios = false;

            if (!empty($usuarioCheck)) {
                $usuario->id = $usuarioCheck->id;
                $usuario->conta_ativa = true;
                $updateClientesHasUsuarios = true;

                $this->Usuarios->validator('Default')->remove('cpf');
            }

            if (!empty($usuarioData["doc_estrangeiro"]) && strlen($usuarioData['doc_estrangeiro']) > 0) {
                $usuario = $this->Usuarios->patchEntity($usuario, $usuarioData, [
                    'validate' => 'CadastroEstrangeiro'
                ]);
            } else {
                $usuario = $this->Usuarios->patchEntity($usuario, $usuarioData, ['validate' => 'Default']);
            }

            if (!empty($usuarioData["doc_estrangeiro"]) && strlen($usuarioData['doc_estrangeiro']) == 0 && strlen($usuarioData['cpf']) == 0) {
                $this->Flash->error(__("Deve ser informado o CPF ou Documentação Estrangeira do novo usuário!"));

                $this->set(compact(['usuario']));
                $this->set("_serialize", ['usuario']);

                return;
            }
            $errors = $usuario->errors();

            // Valida o e-mail se tiver fornecido um @
            if (strpos($usuario->email, "@") !== false) {
                // validação de email
                $validacaoEmail = EmailUtil::validateEmail($usuario->email);

                if (!$validacaoEmail["status"]) {
                    $this->Flash->error(sprintf("ERRO: %s", $validacaoEmail["message"]));
                    $this->set(compact($arraySet));
                    $this->set('_serialize', $arraySet);

                    return;
                }
            }

            // DebugUtil::printArray($usuarioData);
            $usuario = $this->Usuarios->save($usuario);

            if ($usuario) {
                // guarda uma senha criptografada de forma diferente no DB (para acesso externo)
                $this->UsuariosEncrypted->setUsuarioEncryptedPassword($usuario['id'], $passwordEncrypt);

                // Ativa todos os vínculos aos quais ainda não estão ativados
                $this->ClientesHasUsuarios->updateClientesHasUsuario(null, $usuario->id, true);

                if (isset($this->usuarioLogado)) {
                    if ($transportadora) {
                        $this->TransportadorasHasUsuarios->addTransportadoraHasUsuario($transportadora->id, $usuario->id);
                    }

                    if ($veiculo) {
                        $this->UsuariosHasVeiculos->addUsuarioHasVeiculo($veiculo->id, $usuario->id);
                    }

                    if (isset($cliente_id)) {
                        $usuarioInsercaoId = !empty($usuarioLogado) ? $usuarioLogado->id : 0;
                        $this->ClientesHasUsuarios->saveClienteHasUsuario($cliente_id, $usuario->id, true, $usuarioInsercaoId);
                    }
                }

                $this->Flash->success(__('O usuário foi criado com sucesso.'));

                if (isset($this->usuarioLogado)) {

                    if ($this->usuarioLogado['tipo_perfil'] == (int) Configure::read('profileTypes')['AdminDeveloperProfileType']) {
                        return $this->redirect(['action' => 'index']);
                    } elseif ($this->usuarioLogado['tipo_perfil'] >= (int) Configure::read('profileTypes')['AdminDeveloperProfileType'] && $this->usuarioLogado['tipo_perfil'] <= (int) Configure::read('profileTypes')['ManagerProfileType']) {
                        return $this->redirect(['action' => 'meus_clientes']);
                    } else {
                        return $this->redirect(['controller' => 'pages', 'action' => 'index']);
                    }
                } else {
                    return $this->redirect("/Pages/instalaMobile");
                }
            } else {
                $this->Flash->error(__('O usuário não pode ser registrado.'));

                foreach ($errors as $key => $error) {
                    $this->Flash->error($key . ": " . implode(",", $error));
                }
            }
        }

        $usuarioLogadoTipoPerfil = (int) Configure::read('profileTypes')['UserProfileType'];


        $this->set(compact($arraySet));
        $this->set('_serialize', $arraySet);
    }

    /**
     * Edit method
     *
     * @param string|null $id Usuario id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function editar($id = null)
    {
        try {
            $sessaoUsuario = $this->getSessionUserVariables();
            $usuarioLogado = $sessaoUsuario["usuarioLogado"];
            $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"];

            if ($usuarioAdministrar) {
                $this->usuarioLogado = $usuarioAdministrar;
                $usuarioLogado = $usuarioAdministrar;
            }

            $usuario = $this->Usuarios->getUsuarioById($id);

            $rede = null;
            $redes = array();
            $redesConditions = array();
            $clientesHasUsuariosWhere = array();
            $cliente = null;

            if ($usuario->tipo_perfil != (int) Configure::read('profileTypes')['AdminDeveloperProfileType']) {

                $clientesHasUsuariosWhere[] = array('ClientesHasUsuarios.usuarios_id' => $id);
                // @todo gustavosg Testar tipo_perfil

                $clientesHasUsuariosQuery = $this->ClientesHasUsuarios->findClienteHasUsuario($clientesHasUsuariosWhere);

                if (!empty($clientesHasUsuariosQuery)) {
                    // tenho o cliente alocado, pegar agora a rede que ele está
                    $clienteHasUsuario = $clientesHasUsuariosQuery;
                    $cliente = $clienteHasUsuario->cliente;

                    $redeHasCliente = $this->RedesHasClientes->getRedesHasClientesByClientesId($clienteHasUsuario->clientes_id);

                    $rede = $redeHasCliente->rede;
                }
            }
            // pegar a rede a qual se encontra o usuário
            if (isset($rede)) {
                $redesConditions[] = ['id' => $rede["id"]];
            }

            if ($this->usuarioLogado['tipo_perfil'] == Configure::read('profileTypes')['AdminDeveloperProfileType']) {
                $redes = $this->Redes->getRedesList($rede["id"]);
            } elseif ($this->usuarioLogado['tipo_perfil'] == Configure::read('profileTypes')['AdminNetworkProfileType']) {
                // pega o Id de cliente que o usuário se encontra
                // AdminLocalProfileType

                // TODO: terminar de ajustar
                $clienteId = $this->RedesHasClientesAdministradores->getRedesHasClientesAdministradorByUsuariosId($this->usuarioLogado['id']);
            }

            if ($this->request->is(['post', 'put'])) {

                if ($this->usuarioLogado['tipo_perfil'] == Configure::read('profileTypes')['AdminDeveloperProfileType']) {
                    $this->Usuarios->validator('EditUsuarioInfo')->remove('cpf');
                }

                // verifica se o usuário está na mesma rede,
                // e/ou se o perfil é o mesmo. caso contrário, atualiza

                $usuario_compare = $this->request->getData();

                if ($usuario->tipo_perfil != (int) Configure::read('profileTypes')['AdminDeveloperProfileType']) {
                    if (!empty($clienteHasUsuario)) {
                        if (
                            $clienteHasUsuario->clientes_id != $usuario_compare['clientes_id']
                            || $clienteHasUsuario->tipo_perfil != $usuario_compare['tipo_perfil']
                        ) {
                            $this->ClientesHasUsuarios->updateClienteHasUsuarioRelationship($clienteHasUsuario->id, (int) $usuario_compare['clientes_id'], $usuario_compare['id'], (int) $usuario_compare['tipo_perfil']);
                        }
                    }
                }

                $usuario = $this->Usuarios->patchEntity($usuario, $this->request->getData(), ['validate' => 'EditUsuarioInfo']);

                unset($usuario['senha']);

                if ($this->Usuarios->save($usuario)) {
                    $this->Flash->success(__('O usuário foi gravado com sucesso.'));

                    if ($this->usuarioLogado['tipo_perfil'] == Configure::read('profileTypes')['AdminDeveloperProfileType']) {
                        $url = Router::url(['controller' => 'Usuarios', 'action' => 'index']);

                        return $this->response = $this->response->withLocation($url);
                    } else {
                        $url = Router::url(['controller' => 'Usuarios', 'action' => 'meu_perfil']);

                        return $this->response = $this->response->withLocation($url);
                    }
                } else {
                    $this->Flash->error(__('O usuário não pode ser atualizado.'));
                }
            }
            $usuarioLogadoTipoPerfil = $this->usuarioLogado['tipo_perfil'];

            $arraySet = array('usuario', 'usuarioLogadoTipoPerfil', 'rede', 'redes', 'redesId', 'cliente');
            $this->set(compact($arraySet));
            $this->set('_serialize', $arraySet);
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao realizar edição de dados de usuário: {0} em: {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Delete method
     *
     * @param string|null $id Usuario id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete()
    {
        $data = $this->request->query();
        $usuario_id = $data['usuario_id'];
        $return_url = $data['return_url'];

        $this->request->allowMethod(['post', 'delete']);
        $usuario = $this->Usuarios->get($usuario_id);

        // se o usuário a ser removido é usuário, remove todos os registros à ele vinculados
        if ($usuario->tipo_perfil == (int) Configure::read('profileTypes')['UserProfileType']) {

            $this->UsuariosHasBrindes->deleteAllUsuariosHasBrindesByUsuariosId($usuario->id);
            $this->UsuariosHasVeiculos->deleteAllUsuariosHasVeiculosByUsuariosId($usuario->id);
            $this->TransportadorasHasUsuarios->deleteAllTransportadorasHasUsuariosByUsuariosId($usuario->id);
            $this->PontuacoesPendentes->deleteAllPontuacoesPendentesByUsuariosId($usuario->id);
            $this->Pontuacoes->deleteAllPontuacoesByUsuariosId($usuario->id);
            $this->PontuacoesComprovantes->deleteAllPontuacoesComprovantesByUsuariosId($usuario->id);
            $this->Cupons->deleteAllCuponsByUsuariosId($usuario->id);

            $this->ClientesHasUsuarios->deleteAllClientesHasUsuariosByUsuariosId($usuario->id);

            $this->UsuariosEncrypted->deleteUsuariosEncryptedByUsuariosId($usuario->id);

            $this->Usuarios->delete($usuario);

            $this->Flash->success(__(Configure::read('messageDeleteSuccess')));
            return $this->redirect($return_url);
        } elseif ($usuario->tipo_perfil == (int) Configure::read('profileTypes')['AdminDeveloperProfileType']) {

            // admin rti / desenvolvedor não há problema
            // em ser removido, pois não realizam atendimento

            if ($this->Usuarios->delete($usuario)) {
                $this->Flash->success(__(Configure::read('messageDeleteSuccess')));
                return $this->redirect($return_url);
            }
        } else {

            // se não for usuário final, deve-se pesquisar um outro funcionário da mesma unidade
            // se não houver, informar que não é possível remover o único funcionário da rede, que o melhor a se fazer é desabilitá-lo

            // verifica se é o único funcionário da unidade é o único cadastrado

            // primeiro, descobre o código da unidade

            $unidade_usuario = $this->ClientesHasUsuarios->findClienteHasUsuario(
                array(
                    'ClientesHasUsuarios.usuarios_id' => $usuario->id,
                    // @todo gustavosg Testar tipo_perfil
                    //  'ClientesHasUsuarios.tipo_perfil' => $usuario->tipo_perfil
                )
            );

            // com o código da unidade, verifica se há outro usuário vinculado

            $clientes_has_usuarios = $this->ClientesHasUsuarios->findClienteHasUsuario(
                [
                    'ClientesHasUsuarios.clientes_id' => $unidade_usuario->clientes_id,
                    // @todo gustavosg Testar tipo_perfil
                    // 'ClientesHasUsuarios.tipo_perfil <' => (int)Configure::read('profileTypes')['UserProfileType'],
                    'ClientesHasUsuarios.usuarios_id != ' => $usuario->id
                ]
            );

            if (isset($clientes_has_usuarios)) {

                $usuario_destino_id = $clientes_has_usuarios->usuarios_id;

                // remove / atualiza todas as tabelas necessárias

                $this->UsuariosHasBrindes->deleteAllUsuariosHasBrindesByUsuariosId($usuario->id);
                $this->UsuariosHasVeiculos->deleteAllUsuariosHasVeiculosByUsuariosId($usuario->id);
                $this->TransportadorasHasUsuarios->deleteAllTransportadorasHasUsuariosByUsuariosId($usuario->id);
                $this->PontuacoesPendentes->updateAllPontuacoesPendentes(['funcionarios_id' => $usuario_destino_id], ['funcionarios_id' => $usuario->id]);
                $this->Pontuacoes->updateAllPontuacoes(['funcionarios_id' => $usuario_destino_id], ['funcionarios_id' => $usuario->id]);
                $this->PontuacoesComprovantes->updateAllPontuacoesComprovantes(['funcionarios_id' => $usuario_destino_id], ['funcionarios_id' => $usuario->id]);

                // realiza a exclusão do funcionário
                $this->Cupons->deleteAllCuponsByUsuariosId($usuario->id);
                $this->ClientesHasUsuarios->deleteAllClientesHasUsuariosByUsuariosId($usuario->id);
                $this->UsuariosEncrypted->deleteUsuariosEncryptedByUsuariosId($usuario->id);
                $this->Usuarios->delete($usuario);
                $this->Flash->success(__(Configure::read('messageDeleteSuccess')));

                return $this->redirect($return_url);
            } else {
                // não há outro usuário, então deve-se verificar se há alguma informação vinculada à ele. se não houver, pode remover.

                $found = false;

                $clientes_has_brindes_estoque = $this->ClientesHasBrindesEstoque->find('all')->where(['usuarios_id' => $usuario->id])->toArray();

                $found = sizeof($clientes_has_brindes_estoque) > 0 ? true : false;

                if (!$found) {
                    $cupons = $this->Cupons->find('all')->where(['usuarios_id' => $usuario->id])->toArray();

                    $found = sizeof($cupons) > 0 ? true : false;
                }

                if (!$found) {
                    $pontuacoes = $this->Pontuacoes->find('all')->where(['funcionarios_id' => $usuario->id])->toArray();

                    $found = sizeof($pontuacoes) > 0 ? true : false;
                }

                if (!$found) {
                    $pontuacoes_comprovantes = $this->PontuacoesComprovantes->find('all')->where(['funcionarios_id' => $usuario->id])->toArray();

                    $found = sizeof($pontuacoes_comprovantes) > 0 ? true : false;
                }

                if (!$found) {
                    $pontuacoes_pendentes = $this->PontuacoesPendentes->find('all')->where(['funcionarios_id' => $usuario->id])->toArray();

                    $found = sizeof($pontuacoes_pendentes) > 0 ? true : false;
                }

                if ($found) {
                    // isso significa que não é permitido remover o
                    // usuário em questão, pois não tem como migrar os dados cadastrados
                    $this->Flash->error(__("O usuário não pode ser deletado, pois é o único da unidade. Os dados dos clientes ficarão 'órfãos', e por isto, a operação não é permitida."));

                    return $this->redirect(['action' => $return_url]);
                } else {
                    // realiza a remoção do usuário

                    $this->UsuariosHasBrindes->deleteAllUsuariosHasBrindesByUsuariosId($usuario->id);
                    $this->UsuariosHasVeiculos->deleteAllUsuariosHasVeiculosByUsuariosId($usuario->id);
                    $this->TransportadorasHasUsuarios->deleteAllTransportadorasHasUsuariosByUsuariosId($usuario->id);
                    $this->PontuacoesPendentes->deleteAllPontuacoesPendentesByUsuariosId($usuario->id);
                    $this->Pontuacoes->deleteAllPontuacoesByUsuariosId($usuario->id);
                    $this->PontuacoesComprovantes->deleteAllPontuacoesComprovantesByUsuariosId($usuario->id);
                    $this->Cupons->deleteAllCuponsByUsuariosId($usuario->id);

                    $this->UsuariosEncrypted->deleteUsuariosEncryptedByUsuariosId($usuario->id);

                    $this->ClientesHasUsuarios->deleteAllClientesHasUsuariosByUsuariosId($usuario->id);

                    $this->Usuarios->delete($usuario);

                    $this->Flash->success(__(Configure::read('messageDeleteSuccess')));
                    return $this->redirect($return_url);
                }
            }
        }
    }

    /**
     * Adiciona conta de usuário (cliente final, usado por um funcionário, via Web)
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function adicionarConta(int $redes_id = null)
    {
        $sessaoUsuario = $this->getSessionUserVariables();

        $usuarioAdministrador = $sessaoUsuario["usuarioAdministrador"];
        $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"];
        $rede = $sessaoUsuario["rede"];
        $cliente = $sessaoUsuario["cliente"];
        $transportadoraNomeProcura = 'TransportadorasHasUsuarios_Transportadoras_';
        $veiculosNomeProcura = 'UsuariosHasVeiculos_Veiculos_';
        $usuarioLogado = $sessaoUsuario["usuarioLogado"];

        if ($usuarioAdministrador) {
            $this->usuarioLogado = $usuarioAdministrar;
            $usuarioLogado = $usuarioAdministrar;
        }

        $arraySet = array('usuario', 'rede', 'redes', 'redes_id', 'usuarioLogadoTipoPerfil', "transportadora", "veiculo");
        $usuario = $this->Usuarios->newEntity();
        $transportadora = $this->Usuarios->TransportadorasHasUsuarios->newEntity();
        $veiculo = $this->Usuarios->UsuariosHasVeiculos->newEntity();
        $redes = array();
        $redes_conditions = array();

        // Pega unidades que tem acesso
        $clientes_ids = [];

        $unidades_ids = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($rede->id, $this->usuarioLogado['id'], false);

        foreach ($unidades_ids as $key => $value) {
            $clientes_ids[] = $key;
        }

        if (isset($redes_id)) {
            $redes_conditions[] = ['id' => $redes_id];
        }

        if ($this->usuarioLogado['tipo_perfil'] == PROFILE_TYPE_ADMIN_DEVELOPER) {

            if (is_null($redes_id) && isset($rede)) {
                $redes_id = $rede->id;
            }

            if (isset($redes_id)) {
                $rede = $this->Redes->getRedeById($redes_id);
            }

            $redes = $this->Redes->getRedesList($redes_id);
        }

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $usuarioData = $data;
            $usuarioData["cpf"] = preg_replace("/\D/", "", $usuarioData["cpf"]);

            // guarda qual é a unidade que está sendo cadastrada
            $clientes_id = $cliente["id"];
            $tipo_perfil = $data['tipo_perfil'];
            $transportadoraData = array();
            $veiculosData = array();

            // Remove caracter de formatação para mínimo de dígitos do jQuery Mask
            $usuarioData["senha"] = str_replace("?", "", $usuarioData["senha"]);
            $usuarioData["confirm_senha"] = str_replace("?", "", $usuarioData["confirm_senha"]);

            foreach ($usuarioData as $key => $value) {
                if (substr($key, 0, strlen($transportadoraNomeProcura)) == $transportadoraNomeProcura) {
                    $newKey = substr($key, strlen($transportadoraNomeProcura));
                    $transportadoraData[$newKey] = $value;
                    unset($usuarioData[$key]);
                }

                if (substr($key, 0, strlen($veiculosNomeProcura)) == $veiculosNomeProcura) {
                    $newKey = substr($key, strlen($veiculosNomeProcura));
                    $veiculosData[$newKey] = $value;
                    unset($usuarioData[$key]);
                }
            }

            unset($usuarioData['transportadora']);

            if ($usuarioData['doc_invalido'] == true) {
                if (isset($usuarioData['cpf'])) {
                    $nomeDoc = strlen($usuarioData['cpf']) > 0 ? $usuarioData['cpf'] : $usuarioData['doc_estrangeiro'];
                    $nomeDoc = $this->cleanNumberAndLetters($nomeDoc);
                } else {
                    $nomeDoc = $usuarioData['doc_estrangeiro'];
                }

                $nomeDoc = $nomeDoc . '.jpg';

                $currentPath = Configure::read('temporaryDocumentUserPath');
                $newPath = Configure::read('documentUserPath');

                $fotoDocumento = $this->moveDocumentPermanently($currentPath . $nomeDoc, $newPath, null, '.jpg');

                $usuarioData['foto_documento'] = $fotoDocumento;
                $usuarioData['aguardando_aprovacao'] = true;
                $usuarioData['data_limite_aprovacao'] = date('Y-m-d H:i:s', strtotime('+7 days'));
            }

            if (isset($this->usuarioLogado)) {
                if (!empty($veiculosData["placa"])) {
                    $veiculoDataBase = $this->Veiculos->getVeiculoByPlaca($veiculosData['placa']);
                    $veiculoDataBase = $veiculoDataBase["veiculo"];

                    // DebugUtil::print($veiculoDataBase);

                    if ($veiculoDataBase) {
                        $veiculo = $veiculoDataBase;
                    } elseif (isset($veiculosData)) {
                        $veiculo = $this->Veiculos->patchEntity($veiculo, $veiculosData);
                        $veiculo = $this->Veiculos->save($veiculo);
                    }
                }

                if (!empty($transportadoraData["cnpj"])) {
                    if (strlen($transportadoraData['cnpj']) > 0 || strlen($transportadoraData['nome_fantasia']) > 0 || strlen($transportadoraData['razao_social']) > 0) {
                        $transportadoraDataBase = $this->Transportadoras->findTransportadoraByCNPJ($transportadoraData['cnpj']);

                        if ($transportadoraDataBase) {
                            $transportadora = $transportadoraDataBase;
                        } else {
                            $transportadora = $this->Transportadoras->patchEntity($transportadora, $transportadoraData);
                            $transportadora = $this->Transportadoras->save($transportadora);
                        }
                    }
                }
            }

            // Verifica se o usuário já estava registrado neste CPF, se estiver, é atualização.
            $userCheck = $this->Usuarios->getUsuarioByCPF($usuarioData["cpf"]);

            if (!empty($userCheck)) {
                $usuario->id = $userCheck->id;
            }

            // $this->Usuarios->validator('Default')->remove("cpf");

            if (!empty($usuarioData["doc_estrangeiro"]) &&  strlen($usuarioData['doc_estrangeiro']) > 0) {
                $usuario = $this->Usuarios->patchEntity($usuario, $usuarioData, ['validate' => 'CadastroEstrangeiro']);

                // assegura que em um cadastro de estrangeiro, não tenha CPF vinculado.
                $usuario['cpf'] = null;
            } else {

                if (!empty($userCheck)) {
                    // $this->Usuarios->validator('Default')->remove("cpf", "unique");
                }

                $usuario = $this->Usuarios->patchEntity($usuario, $usuarioData);
            }

            // Se não informou senha, a senha padrão será 123456
            if (empty($usuario->senha)) {
                $usuario->senha = "123456";
                $usuario->confirm_senha = "123456";
            }

            $usuario->conta_ativa = true;
            // $passwordEncrypt = $this->cryptUtil->encrypt($usuarioData['senha']);
            $usuario = $this->Usuarios->formatUsuario(0, $usuario);
            $errors = $usuario->errors();

            /**
             * Validação de email
             * Nota: conforme nova regra, só irá acontecer a validação se for informado um login contendo @
             */
            if (!empty($usuario->email) && strpos($usuario->email, "@") !== false) {
                $validacaoEmail = EmailUtil::validateEmail($usuario->email);

                if (!$validacaoEmail["status"]) {
                    $this->Flash->error(sprintf("ERRO: %s", $validacaoEmail["message"]));
                    $this->set(compact($arraySet));
                    $this->set('_serialize', $arraySet);

                    return;
                }
            }

            // Copia o CPF SE o e-mail estiver vazio, para ser usado no campo de login
            if (empty($usuario->email)) {
                $usuario->email = preg_replace("/\D/", "", $usuario->cpf);
            }

            // DebugUtil::printArray($usuario);
            // return ResponseUtil::successAPI('', $errors);

            if ($usuario = $this->Usuarios->save($usuario)) {
                // guarda uma senha criptografada de forma diferente no DB (para acesso externo)
                // $this->UsuariosEncrypted->setUsuarioEncryptedPassword($usuario['id'], $passwordEncrypt);

                // Ativa o usuário em todos os postos se tiver gravado o registro
                $this->ClientesHasUsuarios->updateClientesHasUsuario(null, $usuario->id, true);

                if ($transportadora) {
                    $this->TransportadorasHasUsuarios->addTransportadoraHasUsuario($transportadora->id, $usuario->id);
                }

                if (isset($veiculo["id"])) {
                    $this->UsuariosHasVeiculos->addUsuarioHasVeiculo($veiculo->id, $usuario->id);
                }

                // a vinculação só será feita se não for um Admin RTI
                if ($tipo_perfil != Configure::read('profileTypes')['AdminDeveloperProfileType']) {

                    if ($tipo_perfil == Configure::read('profileTypes')['AdminNetworkProfileType']) {

                        // Se usuário for administrador geral da rede, guarda na tabela de redes_has_clientes_administradores

                        // ele ficará alocado na matriz
                        if ($clientes_id == "") {
                            $redes_has_cliente = $this->RedesHasClientes->findMatrizOfRedesByRedesId($rede->id);

                            $clientes_id = $redes_has_cliente->clientes_id;
                        }

                        $redes_has_cliente = $this->RedesHasClientes->getRedesHasClientesByClientesId($clientes_id);

                        $result = $this->RedesHasClientesAdministradores->addRedesHasClientesAdministradores(
                            $redes_has_cliente->id,
                            $usuario->id
                        );
                    }

                    /**
                     * Agora vincula o usuário ao cliente. Se for cliente final,
                     * será considerado um 'consumidor', caso contrário,
                     * será considerado equipe
                     */

                    $usuarioInsercaoId = !empty($usuarioLogado) ? $usuarioLogado->id : 0;
                    $this->ClientesHasUsuarios->saveClienteHasUsuario($clientes_id, $usuario->id, true, $usuarioInsercaoId);
                }

                $this->Flash->success(__('O usuário foi salvo.'));

                // se o id da rede está definido, volta para o cadastro da rede
                // caso contrário, volta para o índex de administrador

                // se quem está fazendo o cadastro é administrador da rede até gerente
                if (
                    $this->usuarioLogado['tipo_perfil'] >= (Configure::read('profileTypes')['AdminNetworkProfileType'])
                    && $this->usuarioLogado['tipo_perfil'] <= Configure::read('profileTypes')['ManagerProfileType']
                ) {

                    // se cadastrou um usuário, retorna à meus clientes,
                    // caso contrário, retorna à usuários da rede
                    if ($usuario['tipo_perfil'] == Configure::read('profileTypes')['UserProfileType']) {
                        return $this->redirect(['action' => 'meus_clientes']);
                    } else {
                        return $this->redirect(['action' => 'usuarios_rede', $redes_id]);
                    }
                } else {
                    // se é administrador rti
                    if ($this->usuarioLogado['tipo_perfil'] == Configure::read('profileTypes')['AdminDeveloperProfileType']) {
                        // se está mexendo em um cadastro de rede, redireciona para os usuários daquela rede
                        if (isset($redes_id)) {
                            return $this->redirect(['action' => 'usuarios_rede', $redes_id]);
                        }
                        return $this->redirect(['action' => 'index']);
                    } else {
                        return $this->redirect(['controller' => 'pages', 'action' => 'index']);
                    }
                }
            }

            $this->Flash->error(__('O usuário não pode ser registrado. '));

            // exibe os erros logo acima identificados
            foreach ($errors as $key => $error) {
                $key = key($error);
                $this->Flash->error(__("{0}", $error[$key]));
            }
        }

        // na verdade, o perfil deverá ser 6, pois no momento do cadastro do funcionário
        $usuarioLogadoTipoPerfil = $usuarioLogado->tipo_perfil;


        $this->set(compact($arraySet));
        $this->set('_serialize', $arraySet);

        $this->set('transportadoraPath', $transportadoraNomeProcura);
        $this->set('veiculoPath', $veiculosNomeProcura);
    }

    /**
     * Adiciona conta de usuário (cliente final, usado por um funcionário)
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function adicionarOperador(int $redesId = null)
    {
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

        $usuario = $this->Usuarios->newEntity();
        $unidadesRede = array();
        $unidadeRedeId = 0;

        if (empty($rede) && !empty($redesId)) {
            $rede = $this->Redes->getRedeById($redesId);
        }

        if (!empty($rede)) {

            $unidadesList = $this->RedesHasClientes->getRedesHasClientesByRedesId($redesId);
            $unidades = array();

            foreach ($unidadesList as $key => $value) {
                $unidades[$value["clientes_id"]] = $value["cliente"]["nome_fantasia_municipio_estado"];
            }

            $unidadesRede = $unidades;
        }

        $redesId = $rede["id"];
        $usuarioLogadoTipoPerfil = $usuarioLogado['tipo_perfil'];

        // Verifica se tem posto cadastrado para esta rede, se não tiver, avisa ao operador que pode ocorrer inconsistências
        if (count($unidadesRede) == 0 && !empty($rede)) {
            $this->Flash->warning("Atenção! Não há postos cadastrados para esta rede! Cadastre previamente para evitar inconsistências!");
        }

        if ($this->usuarioLogado['tipo_perfil'] == PROFILE_TYPE_ADMIN_DEVELOPER) {

            if (is_null($redesId) && isset($rede)) {
                $redesId = $rede["id"];
            }

            if (isset($redesId) && empty($rede)) {
                $rede = $this->Redes->getRedeById($redesId);
            }
        }

        if ($usuarioLogado["tipo_perfil"] >= PROFILE_TYPE_ADMIN_NETWORK && $usuarioLogado["tipo_perfil"] <= PROFILE_TYPE_ADMIN_REGIONAL) {
            $unidadesRede = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($redesId, $usuarioLogado["id"]);
        }

        $redes = $this->Redes->getRedesList($redesId);

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            // guarda qual é a unidade que está sendo cadastrada
            $clientes_id = (int) $data['clientes_id'];

            // Remove caracter de formatação para mínimo de dígitos do jQuery Mask
            $data["senha"] = str_replace("?", "", $data["senha"]);
            $data["confirm_senha"] = str_replace("?", "", $data["confirm_senha"]);

            if (empty($redesId) && in_array($data["tipo_perfil"], [PROFILE_TYPE_ADMIN_NETWORK, PROFILE_TYPE_WORKER])) {
                $redesId = $data["redes_id"];
                $rede = $this->Redes->getRedeById($redesId);
            }

            $usuarioData = $data;
            $tipoPerfil = $data['tipo_perfil'];
            $cliente = null;

            // Se quem está cadastrando é um  Administrador Comum >= Funcionário, pega o local onde o Funcionário está e vincula ao mesmo lugar.

            if ($usuarioLogado['tipo_perfil'] >= PROFILE_TYPE_ADMIN_LOCAL && $usuarioLogado['tipo_perfil'] <= PROFILE_TYPE_WORKER) {
                $cliente = $this->request->session()->read('Rede.PontoAtendimento');
                $data['clientes_id'] = $cliente["id"];
                $clientes_id = $cliente["id"];
            }

            // Se o tipo de perfil não for Administrador de Rede Regional ao menos, o Usuário deve estar vinculado à uma unidade!
            // Regional já está em algum lugar, pois antes ele foi um Administrador Comum!

            if ($usuarioData['tipo_perfil'] > PROFILE_TYPE_ADMIN_REGIONAL && strlen($data['clientes_id']) == 0) {
                $this->Flash->error(Configure::read('messageUsuarioRegistrationClienteNotNull'));

                $usuario = $this->Usuarios->patchEntity($usuario, $usuarioData);

                $arraySet = array(
                    'usuario',
                    'rede',
                    'redes',
                    'redesId',
                    'usuarioLogadoTipoPerfil',
                    'usuarioLogado',
                    "unidadesRede",
                    "unidadeRede",
                    "unidadeRedeId",
                );

                $this->set(compact($arraySet));
                $this->set('_serialize', $arraySet);

                // return $this->redirect(array("controller" => "usuarios", "action" => "adicionarOperador", $redesId));
                return;
            }

            $usuario = $this->Usuarios->patchEntity($usuario, $usuarioData);

            if ($usuario->tipo_perfil === PROFILE_TYPE_WORKER) {
                $this->Usuarios->validator('Default')->remove("telefone");
            }

            $passwordEncrypt = $this->cryptUtil->encrypt($usuarioData['senha']);
            // $usuario = $this->Usuarios->formatUsuario(0, $usuario);
            $errors = $usuario->errors();

            if ($usuarioSave = $this->Usuarios->save($usuario)) {
                // guarda uma senha criptografada de forma diferente no DB (para acesso externo)
                $this->UsuariosEncrypted->setUsuarioEncryptedPassword($usuarioSave['id'], $passwordEncrypt);

                // a vinculação só será feita se não for um Admin RTI
                if ($tipoPerfil != PROFILE_TYPE_ADMIN_DEVELOPER) {

                    if ($tipoPerfil == PROFILE_TYPE_ADMIN_NETWORK) {

                        // Se usuário for administrador geral da rede, guarda na tabela de redes_has_clientes_administradores

                        // ele ficará alocado na matriz
                        if ($clientes_id == "") {
                            $redes_has_cliente = $this->RedesHasClientes->findMatrizOfRedesByRedesId($rede->id);

                            if (!empty($redes_has_cliente)) {
                                $clientes_id = $redes_has_cliente->clientes_id;
                            }
                        } else {
                            $redes_has_cliente = $this->RedesHasClientes->getRedesHasClientesByClientesId($clientes_id);
                        }

                        // Só pode ser guardado o relacionamento se já tiver algum posto
                        if (empty($redes_has_cliente)) {
                            Log::warning(sprintf("Administrador sendo cadastrado sem posto vinculado e cadastrado previamente! Rede: [%s / %s] - Usuário: [%s / %s].", $rede->id, $rede->nome_rede, $usuarioSave->id, $usuarioSave->nome));
                        } else {
                            $result = $this->RedesHasClientesAdministradores->addRedesHasClientesAdministradores(
                                $redes_has_cliente->id,
                                $usuarioSave->id
                            );
                        }
                    }

                    /**
                     * Agora vincula o usuário ao cliente. Se for cliente final,
                     * será considerado um 'consumidor', caso contrário,
                     * será considerado equipe
                     */

                    // Define qual foi o usuário que cadastrou o novo funcionário
                    $usuarioInsercaoId = !empty($usuarioLogado) ? $usuarioLogado->id : 0;

                    // Só vincula se o posto tiver sido selecionado
                    if (!empty($clientes_id)) {
                        $this->ClientesHasUsuarios->saveClienteHasUsuario($clientes_id, $usuarioSave["id"], true, $usuarioInsercaoId);
                    }
                }

                $this->Flash->success(__('O usuário foi salvo.'));

                // se cadastrou um usuário, retorna à meus clientes,
                // caso contrário, retorna à usuários da rede
                if ($usuarioSave['tipo_perfil'] == Configure::read('profileTypes')['UserProfileType']) {
                    return $this->redirect(['action' => 'meus_clientes']);
                } elseif ($usuarioSave['tipo_perfil'] == Configure::read('profileTypes')['AdminDeveloperProfileType']) {
                    return $this->redirect(['action' => 'index']);
                } else {
                    if (isset($redesId)) {
                        return $this->redirect(['action' => 'usuarios_rede', $redesId]);
                    }
                    return $this->redirect(['action' => 'index']);
                }
            }

            $this->Flash->error(__('O usuário não pode ser registrado. '));

            // exibe os erros logo acima identificados
            foreach ($errors as $key => $error) {
                $key = key($error);
                $this->Flash->error(__("{0}", $error[$key]));
            }
        }

        // DebugUtil::print($redes->toArray());
        $arraySet = array(
            'usuario',
            'rede',
            'redes',
            'redesId',
            'usuarioLogadoTipoPerfil',
            "unidadesRede",
            "unidadeRedeId",
            'usuarioLogado'
        );

        $this->set(compact($arraySet));
        $this->set('_serialize', $arraySet);
    }

    /**
     * Adiciona conta de usuário (cliente final, usado por um funcionário)
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function editarOperador(int $usuarios_id = null)
    {
        $sessaoUsuario = $this->getSessionUserVariables();

        $origemMeuPerfilAnterior = $this->request->session()->read("Origem.MeuPerfil");

        $origemMeuPerfil = strpos($_SERVER["HTTP_REFERER"], "perfil") !== false;

        if (!$origemMeuPerfilAnterior) {
            if ($origemMeuPerfil)
                $this->request->session()->write("Origem.MeuPerfil", true);
            else
                $this->request->session()->delete("Origem.MeuPerfil");
        }

        // DebugUtil::printArray($sessaoUsuario);
        $usuarioAdministrador = $sessaoUsuario["usuarioAdministrador"];
        $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"];
        $usuarioLogado = $this->usuarioLogado;

        if ($usuarioAdministrador) {
            $this->usuarioLogado = $usuarioAdministrar;
            $usuarioLogado = $usuarioAdministrar;
        }

        $usuario = $this->Usuarios->getUsuarioById($usuarios_id);
        $usuario["cliente_has_usuario"] = $this->ClientesHasUsuarios->getVinculoClientesUsuario($usuarios_id, true);
        $rede = $sessaoUsuario["rede"];

        $clienteHasUsuario = $this->ClientesHasUsuarios->findClienteHasUsuario(
            [
                'ClientesHasUsuarios.usuarios_id' => $usuarios_id,
                // 'ClientesHasUsuarios.tipo_perfil <= ' => Configure::read('profileTypes')['WorkerProfileType']
            ]
        );

        $clientesId = $clienteHasUsuario["clientes_id"];

        // se a rede estiver nula, procura pela rede através do clientes_has_usuarios

        if (!isset($redesId)) {
            $redes_has_cliente = $this->RedesHasClientes->getRedesHasClientesByClientesId($clienteHasUsuario["clientes_id"]);

            $rede = $this->Redes->getAllRedes('all', ['id' => $redes_has_cliente->redes_id])->first();
        }

        $clienteAdministrar = $this->request->session()->read('Rede.PontoAtendimento');

        $redesId = $rede["id"];

        $redes = $this->Redes->getRedesList($redesId);

        $unidadesRede = array();
        $unidadeRedeId = 0;
        if ($this->usuarioLogado["tipo_perfil"] >= PROFILE_TYPE_ADMIN_NETWORK && $this->usuarioLogado["tipo_perfil"] <= PROFILE_TYPE_ADMIN_REGIONAL) {
            $unidadesRede = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($redesId, $usuarioLogado["id"]);
        } else {
            // $unidadesQuery = $this->RedesHasClientes->getRedesHasClientesByRedesId($redesId);
            // $unidadesQuery = $unidadesQuery->toArray();
            // foreach ($unidadesQuery as $unidade) {
            //     $unidadesRede[] = $unidade["cliente"];
            // }
            $unidadesRede = $this->Clientes->getClientesListByRedesId($redesId);
        }
        // Como estamos editando, o usuário já tem vinculo
        $unidadeRede = $this->ClientesHasUsuarios->getVinculoClienteUsuario($redesId, $usuario["id"]);

        $unidadeRedeId = $unidadeRede["clientes_id"];

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();
            $usuarioData = $data;

            // guarda qual é a unidade que está sendo cadastrada
            $clientesId = (int) $data['clientes_id'];
            $tipo_perfil = empty($data["tipo_perfil"]) ? $usuario["tipo_perfil"] : $data['tipo_perfil'];
            $usuario = $this->Usuarios->patchEntity($usuario, $usuarioData);
            // $usuario = $this->Usuarios->formatUsuario($usuario['id'], $usuario);
            $errors = $usuario->errors();

            if ($usuario = $this->Usuarios->save($usuario)) {
                // a vinculação só será feita se não for um Admin RTI
                if ($tipo_perfil != Configure::read('profileTypes')['AdminDeveloperProfileType']) {

                    if ($tipo_perfil == Configure::read('profileTypes')['AdminNetworkProfileType']) {

                        // Se usuário for administrador geral da rede, guarda na tabela de redes_has_clientes_administradores

                        // ele ficará alocado na matriz
                        if ($clientesId == "") {
                            $redes_has_cliente = $this->RedesHasClientes->findMatrizOfRedesByRedesId($rede->id);

                            $clientesId = $redes_has_cliente->clientes_id;
                        } else {
                            $redes_has_cliente = $this->RedesHasClientes->getRedesHasClientesByClientesId($clientesId);
                        }

                        $result = $this->RedesHasClientesAdministradores->addRedesHasClientesAdministradores(
                            $redes_has_cliente->id,
                            $usuario->id
                        );
                    }

                    /**
                     * Agora vincula o usuário ao cliente. Se for cliente final,
                     * será considerado um 'consumidor', caso contrário,
                     * será considerado equipe
                     */

                    /**
                     * Se o operador não for adm de rede, nem regional, atualiza o vínculo.
                     */

                    if ($usuario["tipo_perfil"] >= (int) Configure::read('profileTypes')['AdminNetworkProfileType']) {
                        $this->ClientesHasUsuarios->updateClienteHasUsuarioRelationship($clienteHasUsuario["id"], $clientesId, $usuario["id"]);
                    }
                }

                $origemMeuPerfil = $this->request->session()->read("Origem.MeuPerfil");

                if ($origemMeuPerfil) {
                    $this->request->session()->delete("Origem.MeuPerfil");
                    $this->Flash->success(__('Cadastro atualizado!'));

                    return $this->redirect(["controller" => "Pages", 'action' => 'index']);
                } else {
                    $this->Flash->success(__('O usuário foi salvo.'));

                    return $this->redirect(['action' => 'usuarios_rede', $redesId]);
                }
            }

            $this->Flash->error(__('O usuário não pode ser registrado. '));

            // exibe os erros logo acima identificados
            foreach ($errors as $key => $error) {
                $key = key($error);
                $this->Flash->error(__("{0}", $error[$key]));
            }
        }

        $usuarioLogadoTipoPerfil = $usuarioLogado['tipo_perfil'];
        $arraySet = array(
            "origemMeuPerfil",
            'usuario',
            'rede',
            'redes',
            'redesId',
            'clientesId',
            "unidadesRede",
            "unidadeRedeId",
            'usuarioLogadoTipoPerfil',
            "usuarioLogado"
        );
        $this->set(compact($arraySet));
        $this->set('_serialize', $arraySet);
    }


    /**
     * Exibe todos os usuários aguardando aprovação do documento
     *
     * @return \Cake\Http\Response|void
     * @author
     **/
    public function usuariosAguardandoAprovacao()
    {

        $conditions = [];

        $entire_network = false;

        array_push($conditions, array('tipo_perfil > ' => Configure::read('profileTypes')['AdminDeveloperProfileType']));

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();

            if (!empty($data["nome"])) {
                $conditions[] = array("nome like '%$nome%'");
            }
            if (!empty($data["email"])) {
                $conditions[] = array("email like '%$email%'");
            }
            if (!empty($data['cpf'])) {
                $cpf = $this->cleanNumber($data['parametro']);
                $conditions[] = array("cpf" => $cpf);
            }
            if (!empty($data['doc_estrangeiro'])) {
                $docEstrangeiro = $data["doc_estrangeiro"];
                $conditions[] = array("doc_estrangeiro" => $docEstrangeiro);
            }
        }

        $usuarios = $this->Usuarios->findUsuariosAwaitingApproval();
        $usuarios = $usuarios->where($conditions);
        $usuarios = $this->paginate($usuarios, ['limit' => 10, 'order' => ['tipo_perfil' => 'ASC']]);

        $this->set('usuarios', $usuarios);
    }

    /**
     * Aprova usuário com documentação imprecisa
     *
     * @param int $userId Id de usuário
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function aprovarDocumentoUsuario(int $userId = null)
    {
        try {
            $usuario = $this->Usuarios->getUsuarioById($userId);

            $usuario->foto_documento = Configure::read('documentUserPathRead') . $usuario->foto_documento;

            if ($this->request->is(['post', 'put'])) {
                $usuario = $this->Usuarios->getUsuarioById($userId);
                $usuario->aguardando_aprovacao = false;
                $usuario->data_limite_aprovacao = null;

                if ($this->Usuarios->save($usuario)) {
                    $this->Flash->success(__("O usuário {0} foi autorizado.", $usuario->nome));

                    $this->redirect(['action' => 'usuarios_aguardando_aprovacao']);
                } else {
                    $this->Flash->error(__("Não foi possível autorizar o usuário, tente novamente."));
                }
            }

            $this->set('usuario', $usuario);
        } catch (\Exception $e) {
        }
    }

    /**
     * Action de Esqueci minha Senha
     *
     * @return \Cake\Http\Response|void
     * @author Gustavo Souza Gonçalves
     */
    public function esqueciMinhaSenha()
    {
        $message = [];

        if ($this->request->is('post')) {
            $usuario = $this->Usuarios->getUsuarioByEmail($this->request->data['email']);
            if (is_null($usuario)) {
                $messageString = __(Configure::read("messageEmailNotFound"));
                $message[] = ['status' => false, 'message' => $messageString];

                $this->Flash->error($messageString);
            } else {
                // gera o token que o usuário irá utilizar para recuperar a senha

                $token = bin2hex(openssl_random_pseudo_bytes(32));
                $url = Router::url(['controller' => 'usuarios', 'action' => 'resetar_minha_senha', $token], true);
                $timeout = time() + DAY;

                if ($this->Usuarios->setUsuarioTokenPasswordRequest($usuario->id, $token, $timeout)) {
                    $this->_sendResetEmail($url, $usuario);

                    $this->Flash->success(__("Email enviado para {0} com o token de resetar a senha.", $usuario->email));

                    return $this->redirect(['action' => 'login']);
                } else {
                    $this->Flash->error('Houve um erro ao solicitar o token de resetar a senha.');
                }
            }
        }

        $arraySet = [
            'message',
        ];

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);
    }

    /**
     * Login method
     *
     * @return void
     */
    public function login()
    {
        $recoverAccount = null;
        $email = '';
        $message = '';
        $status = null;

        if ($this->request->is('post')) {
            $data = $this->request->getData();

            $retornoLogin = $this->checkLoginUser($data["email"], $data["senha"], LOGIN_WEB);

            $recoverAccount = !empty($retornoLogin["recoverAccount"]) ? $retornoLogin["recoverAccount"] : null;
            $email = !empty($data["email"]) ? $data["email"] : null;
            $message = !empty($retornoLogin["message"]) ? $retornoLogin["message"] : null;
            $status = isset($retornoLogin["status"]) ? $retornoLogin["status"] : null;

            if (empty($retornoLogin["usuario"])) {
                $this->Flash->error(__($message));
                // return;
            }
        }

        $arraySet = ['recoverAccount', 'email', 'message'];
        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);

        if (isset($status) && ($status == false) && !$this->request->is(Request::METHOD_POST)) {
            return $this->redirect(['controller' => 'pages', 'action' => 'display']);
        } else if (isset($status) && $status == true) {
            return $this->redirect(['controller' => 'pages', 'action' => 'display']);
        } else {
            return;
        }
    }

    /**
     * Action de reativar conta
     *
     * @return \Cake\Http\Response|void
     * @author
     **/
    public function reativarConta($email = null)
    {
        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();

            $this->loadNecessaryModels(['Veiculos', 'UsuariosHasVeiculos']);

            $usuario = $this->Usuarios->getUsuarioByEmail($email);

            if ($usuario['tipo_perfil'] < Configure::read('profileTypes')['UserProfileType']) {
                $this->Flash->error(Configure::read("messageDenyErrorPrivileges"));

                return $this->redirect(['controller' => 'usuarios', 'action' => 'reativar_conta']);
            }

            $usuariosComCarros = $this->Veiculos->getUsuariosByVeiculo($data['placa']);

            $placaEncontrada = false;

            foreach ($usuariosComCarros as $value) {
                foreach ($value->usuarios_has_veiculos as $veiculos) {
                    if ($veiculos['usuarios_id'] == $usuario['id']) {
                        $placaEncontrada = true;
                        break;
                    }
                }
            }

            $dataNascimentoConfere = ($usuario['data_nasc']->format('Y-m-d') == $data['data_nasc']);

            if ($placaEncontrada && $dataNascimentoConfere) {
                $this->Usuarios->reativarConta($usuario['id']);

                $this->Flash->success('Conta recuperada com sucesso. Para continuar, realize o login');

                return $this->redirect(['action' => 'login']);
            } else {
                $this->Flash->error('Informações não conferem. Tente novamente.');
            }
        }
    }

    /**
     * Action de Resetar senha
     *
     * @return \Cake\Http\Response|void
     */
    public function resetarMinhaSenha($tokenSenha = null)
    {
        if ($tokenSenha) {
            $usuario = $this->Usuarios->findUsuarioAwaitingPasswordReset($tokenSenha, time());
            if ($usuario) {
                if ($this->request->is(["POST", "PUT"])) {

                    $data = $this->request->getData();
                    if (!empty($data)) {
                        // Limpa campos de requisição de token
                        $data['token_senha'] = null;
                        $data['data_expiracao_senha'] = null;
                        $data['tipo_perfil'] = $usuario->tipo_perfil;
                        $data["conta_bloqueada"] = 0;
                        $data["tentativas_login"] = 0;

                        $usuario = $this->Usuarios->patchEntity($usuario, $data);
                        $usuarioSave = $this->Usuarios->save($usuario);
                        if ($usuarioSave) {
                            $this->Flash->set(__('Sua senha foi atualizada.'));
                            return $this->redirect(array('action' => 'login'));
                        } else {
                            $this->Flash->error(__('A senha não pode ser atualizada. Tente novamente.'));

                            $errorsSave = $usuario->errors();

                            foreach ($errorsSave as  $errorList) {
                                foreach ($errorList as $error) {
                                    $this->Flash->error($error);
                                }
                            }
                        }
                    }
                }
            } else {
                $this->Flash->error('Token inválido ou expirado. Solicite novamente o reset da senha.');
                $this->redirect(['action' => 'esqueci_minha_senha']);
            }
            unset($usuario->password);
            $this->set(compact('usuario'));
        } else {
            $this->redirect('/');
        }
    }

    /**
     * Action de Trocar senha
     *
     * @param int $id
     * @return \Cake\Http\Response|void
     */
    public function alterarSenha($id = null)
    {
        $arraySet = ["id", "usuario", "usuarioLogado"];

        $sessaoUsuario = $this->getSessionUserVariables();
        $usuarioAdministrador = $sessaoUsuario["usuarioAdministrador"];
        $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"];
        $usuarioLogado = $sessaoUsuario["usuarioLogado"];

        if ($usuarioAdministrar) {
            $this->usuarioLogado = $usuarioAdministrar;
            $usuarioLogado = $usuarioAdministrar;
        }

        if (empty($id)) {
            return $this->redirect(array("controller" => "pages", "action" => "display"));
        }

        $usuario = $this->Usuarios->get($id);

        if ($this->request->is('get')) {
            $usuario->senha = null;
        }

        try {
            if ($this->request->is(['post', 'put'])) {
                $usuario = $this->Usuarios->getUsuarioById($id);

                if ($usuario) {
                    if (!empty($this->request->getData())) {
                        // $passwordEncrypt = $this->cryptUtil->encrypt($this->request->getData()['senha']);

                        // Limpa campos de requisição de token
                        $this->request->data['token_senha'] = null;
                        $this->request->data['data_expiracao_senha'] = null;
                        $this->request->data['tipo_perfil'] = $usuario['tipo_perfil'];

                        $data = $this->request->getData();
                        $senhaAntiga = $data["senha_antiga"] ?? null;
                        $senha = str_replace("?", "", $data["senha"]);
                        $confirmSenha = str_replace("?", "", $data["confirm_senha"] ?? null);
                        $senhaAntigaConfere = false;
                        $errors = array();

                        if ($usuarioLogado->tipo_perfil <= PROFILE_TYPE_ADMIN_LOCAL) {
                            $senhaAntigaConfere = true;
                        } else {
                            $senhaAntigaConfere = ((new DefaultPasswordHasher)->check($senhaAntiga, $usuario->senha));
                        }

                        if (!$senhaAntigaConfere) {
                            $errors[] = MSG_USUARIOS_OLD_PASSWORD_DOESNT_MATCH;
                        }

                        if ($senha != $confirmSenha) {
                            $errors[] = MSG_USUARIOS_PASSWORD_UPDATE_ERROR;
                        }

                        if (count($errors) > 0) {
                            foreach ($errors as $error) {
                                $this->Flash->error($error);
                            }
                        } else {
                            $usuario = $this->Usuarios->patchEntity($usuario, $this->request->data);
                            $usuarioSave = $this->Usuarios->save($usuario);

                            if ($usuarioSave) {
                                $this->Flash->success(__('A senha foi atualizada.'));

                                // atualiza a senha criptografada de forma diferente no DB (para acesso externo)
                                // $this->UsuariosEncrypted->setUsuarioEncryptedPassword($usuario['id'], $passwordEncrypt);

                                if ($this->usuarioLogado['tipo_perfil'] == (int) Configure::read('profileTypes')['AdminDeveloperProfileType']) {
                                    return $this->redirect(array('action' => 'index'));
                                } elseif ($this->usuarioLogado['tipo_perfil'] >= (int) Configure::read('profileTypes')['AdminNetworkProfileType'] && $this->usuarioLogado['tipo_perfil'] <= (int) Configure::read('profileTypes')['WorkerProfileType']) {
                                    return $this->redirect(['controller' => 'pages', 'action' => 'index']);
                                } else {
                                    return $this->redirect(['controller' => 'usuarios', 'action' => 'meu_perfil']);
                                }
                            } else {
                                $this->Flash->error(__('A senha não pode ser atualizada. Tente novamente.'));

                                $errors = $usuario->errors();

                                foreach ($errors as $key => $error) {
                                    foreach ($error as $key => $errorItem) {
                                        $this->Flash->error($errorItem);
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $this->Flash->error('A senha não pode ser atualizada. Foi informada corretamente?');
                    $this->redirect(['action' => 'alterar_senha']);
                }
                unset($usuario->password);
            }
            $this->set("_serialize", $arraySet);
            $this->set(compact($arraySet));
        } catch (\Exception $e) {
            $stringError = __("Erro ao realizar procedimento de troca de senha: {0} em: {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * UsuariosController::alterarSenhaAPI
     *
     * Alteração de Senha via API
     *
     * @param string senha_antiga Senha Antiga
     * @param string nova_senha Nova Senha
     * @param string confirm_senha Confirm Senha
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-05-22
     *
     * @return void
     */
    public function alterarSenhaAPI()
    {
        try {
            if ($this->request->is(array("post"))) {
                $usuario = $this->Auth->user();

                if (empty($usuario)) {
                    $errors = array(MSG_USUARIOS_NOT_AUTHENTICATED);
                    return ResponseUtil::errorAPI(MESSAGE_GENERIC_ERROR, $errors);
                }

                $usuario = $this->Usuarios->getUsuarioById($usuario["id"]);
                $usuarioSave = $usuario;

                $data = $this->request->getData();
                $errors = array();

                $senhaAntiga = !empty($data["senha_antiga"]) ? $data["senha_antiga"] : null;
                $novaSenha = !empty($data["nova_senha"]) ? $data["nova_senha"] : null;
                $confirmSenha = !empty($data["confirm_senha"]) ? $data["confirm_senha"] : null;

                $senhaValida = false;
                if ((new DefaultPasswordHasher)->check($senhaAntiga, $usuario["senha"])) {
                    $senhaValida = true;
                }

                if (!$senhaValida) {
                    $errors[] = MSG_USUARIOS_PASSWORD_INCORRECT;
                }

                if ($novaSenha != $confirmSenha) {
                    $errors[] = MSG_USUARIOS_PASSWORD_UPDATE_ERROR;
                }

                $tamanhoSenha = 8;
                if ($usuario["tipo_perfil"] == PROFILE_TYPE_USER) {
                    $tamanhoSenha = 6;
                }

                if (strlen($novaSenha) != $tamanhoSenha) {
                    $errors[] = sprintf(MSG_USUARIOS_PASSWORD_LENGTH, $tamanhoSenha);
                }

                if (count($errors) > 0) {
                    return ResponseUtil::errorAPI(MESSAGE_GENERIC_ERROR, $errors);
                }

                $usuarioSave["senha"] = $novaSenha;
                $usuarioSave["confirm_senha"] = $confirmSenha;

                $usuario = $this->Usuarios->patchEntity($usuario, (array) $usuarioSave, array('validate' => 'Default'));
                $usuario = $this->Usuarios->save($usuario);

                if ($usuario) {
                    $this->UsuariosTokens->deleteTokensUsuario($usuario->id);
                    $usuario = [
                        'id' => $usuario['id'],
                        'token' => JWT::encode(
                            [
                                'id' => $usuario['id'],
                                'sub' => $usuario['id'],
                                'exp' => time()
                            ],
                            Security::salt()
                        )
                    ];
                    $this->Auth->logout();

                    return ResponseUtil::successAPI(MSG_USUARIOS_PASSWORD_UPDATED, array("usuario" => $usuario));
                }
                return ResponseUtil::errorAPI(MSG_USUARIOS_PASSWORD_UPDATE_ERROR);
            }
        } catch (\Exception $e) {
            $trace = $e->getTraceAsString();
            $stringError = __("Erro ao realizar procedimento de troca de senha: {0} em: {1} ", $e->getMessage());

            Log::write('error', $stringError);
            Log::write("error", $trace);
        }
    }

    /**
     * Exibe os usuários de uma rede
     *
     *
     * @return \Cake\Http\Response|void
     */
    public function usuariosRede(int $redesId = null)
    {
        $sessaoUsuario = $this->getSessionUserVariables();
        $rede = $sessaoUsuario["rede"];
        $cliente = $sessaoUsuario["cliente"];
        $usuarioAdministrador = $sessaoUsuario["usuarioAdministrador"];
        $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"];

        if ($usuarioAdministrador) {
            $this->usuarioLogado = $usuarioAdministrar;
            $usuarioLogado = $this->usuarioLogado;
        }

        if (!empty($rede)) {
            $redesId = $rede["id"];
        }

        $clientesIds = array();
        $conditions = array();

        // se for developer / rti / rede, mostra todas as unidades da rede
        $unidadesIds = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($redesId, $this->usuarioLogado['id']);

        // DebugUtil::printArray($unidadesIds);

        if (!is_null($unidadesIds)) {
            foreach ($unidadesIds as $key => $value) {
                $clientesIds[] = $key;
            }
        }

        $unidadesId = sizeof($clientesIds) == 1 ? $clientesIds[0] : 0;

        $nome = null;
        $cpf = null;
        $docEstrangeiro = null;
        $filtrarUnidade = null;
        $tipoPerfil = null;
        $tipoPerfilMin = null;
        $tipoPerfilMax = null;
        $email = null;

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();

            $tipoPerfil = strlen($data["tipo_perfil"]) > 0 ? $data["tipo_perfil"] : null;
            $nome = !empty($data["nome"]) ? $data["nome"] : "";
            $docEstrangeiro = !empty($data["doc_estrangeiro"]) ? $data["doc_estrangeiro"] : null;
            $filtrarUnidade = !empty($data["filtrar_unidade"]) ? $data["filtrar_unidade"] : null;
            $cpf = !empty($data["cpf"]) ? $this->cleanNumber($data["cpf"]) : "";

            if (!empty($filtrarUnidade)) {
                $clientesIds = [];
                $clientesIds[] = (int) $data['filtrar_unidade'];
            }
        }

        if (strlen($tipoPerfil) == 0) {
            $tipoPerfilMin = Configure::read('profileTypes')['AdminNetworkProfileType'];
            $tipoPerfilMax = Configure::read('profileTypes')['WorkerProfileType'];
        } else {
            $tipoPerfilMin = $tipoPerfil;
            $tipoPerfilMax = $tipoPerfil;
        }

        if (sizeof($clientesIds) == 0) {
            $clientesIds[] = 0;
        }

        $usuarios = $this->Usuarios->findAllUsuarios($redesId, $clientesIds, $nome, $email, null, $tipoPerfilMin, $tipoPerfilMax, $cpf, $docEstrangeiro, null, true);

        $usuarios = $this->paginate($usuarios, array('limit' => 10));

        // DebugUtil::printArray($usuarios->toArray());

        // $arraySet = array('usuarios', 'unidadesIds', "unidadesId", 'redesId', 'usuarioLogado');
        $arraySet = array('usuarios', 'unidadesIds', "unidadesId", 'redesId');

        $this->set(compact($arraySet));
        $this->set('_serialize', $arraySet);
    }

    /**
     * Exibe os administradores de uma rede
     *
     * @param int $redes_id Id da rede
     *
     * @return \Cake\Http\Response|void
     */
    public function atribuirAdminRegionalComum(int $redes_id = null)
    {
        $rede = $this->request->session()->read('Rede.Grupo');

        if (empty($rede)) {
            $rede = $this->Redes->getRedeById($redes_id);
        }

        $redes_id = $rede["id"];
        $cliente = $this->request->session()->read('Rede.PontoAtendimento');
        $clienteAdministrar = $this->request->session()->read('Rede.PontoAtendimento');

        $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
        $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

        if ($usuarioAdministrador) {
            $this->usuarioLogado = $usuarioAdministrar;
            $usuarioLogado = $this->usuarioLogado;
        }

        $conditions = [];

        // define que só poderá buscar administradores e administradores regionais para esta tela

        array_push($conditions, ['usuarios.tipo_perfil >= ' => Configure::read('profileTypes')['AdminRegionalProfileType']]);
        array_push($conditions, ['usuarios.tipo_perfil <= ' => Configure::read('profileTypes')['AdminLocalProfileType']]);

        $entire_network = false;

        $clientesIds = [];

        $unidades_ids = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($redes_id, $this->usuarioLogado['id']);

        if (!is_null($unidades_ids)) {
            foreach ($unidades_ids as $key => $value) {
                $clientesIds[] = $key;
            }
        }

        $nome = null;
        $cpf = null;
        $docEstrangeiro = null;
        $tipoPerfil = null;

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();

            $tipoPerfil = !empty($data["tipo_perfil"]) ? $data["tipo_perfil"] : null;
            $nome = !empty($data["nome"]) ? $data["nome"] : "";
            $docEstrangeiro = !empty($data["doc_estrangeiro"]) ? $data["doc_estrangeiro"] : "";
            $filtrarUnidade = !empty($data["filtrar_unidade"]) ? $data["filtrar_unidade"] : "";
            $cpf = !empty($data["cpf"]) ? $this->cleanNumber($data["cpf"]) : "";

            if ($data['filtrar_unidade'] != "") {
                $clientesIds = [];
                $clientesIds[] = (int) $data['filtrar_unidade'];
            }
        }

        if (sizeof($clientesIds) == 0) {
            $clientesIds[] = 0;
        }

        // TODO: ver se é necessário ajustar ou fixar tipo de perfil
        $usuarios = $this->Usuarios->findFuncionariosRede(
            $redes_id,
            $clientesIds,
            $nome,
            $cpf,
            $docEstrangeiro,
            Configure::read("profileTypes")["AdminRegionalProfileType"],
            Configure::read("profileTypes")["AdminLocalProfileType"]
        );

        $usuarios = $this->paginate($usuarios, ['limit' => 10]);

        $arraySet = array('usuarios', 'unidades_ids', 'rede', 'redes_id', "usuarioLogado");
        $this->set(compact($arraySet));
        $this->set('_serialize', $arraySet);
    }

    /**
     * Método para listar todos os usuários de uma rede
     *
     * @return \Cake\Http\Response|void
     **/
    public function meusClientes()
    {
        $sessaoUsuario = $this->getSessionUserVariables();
        $usuarioAdministrador = $sessaoUsuario["usuarioAdministrador"];
        $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"];
        $rede = $sessaoUsuario["rede"];

        if ($usuarioAdministrador) {
            $this->usuarioLogado = $usuarioAdministrar;
            $usuarioLogado = $usuarioAdministrar;
        }

        // pega id de todos os clientes que estão ligados à uma rede

        $redes_has_clientes_query = $this->RedesHasClientes->getRedesHasClientesByRedesId($rede->id);

        $clientesIds = [];

        $unidades_ids = $this->ClientesHasUsuarios->getClientesFilterAllowedByUsuariosId($rede["id"], $this->usuarioLogado['id']);

        $unidades_ids = $unidades_ids->toArray();

        foreach ($redes_has_clientes_query as $key => $value) {
            $clientesIds[] = $value['clientes_id'];
        }
        $conditions = [];

        $conditions[] = ['clientes_id IN ' => $clientesIds];

        $nome = null;
        $email = null;
        $cpf = null;
        $docEstrangeiro = null;
        $tipoPerfil = Configure::read("profileTypes")["UserProfileType"];

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();

            $nome = !empty($data["nome"]) ? $data["nome"] : "";
            $email = !empty($data["email"]) ? $data["email"] : "";
            $docEstrangeiro = !empty($data["doc_estrangeiro"]) ? $data["doc_estrangeiro"] : "";
            $filtrarUnidade = !empty($data["filtrar_unidade"]) ? $data["filtrar_unidade"] : "";
            $cpf = !empty($data["cpf"]) ? $this->cleanNumber($data["cpf"]) : "";

            if ($filtrarUnidade != "") {
                $clientesIds = [];
                $clientesIds[] = (int) $data['filtrar_unidade'];
            }
        }

        if (sizeof($clientesIds) == 0) {
            $clientesIds[] = 0;
        }

        $usuarios = $this->Usuarios->findAllUsuarios($rede["id"], $clientesIds, $nome, $email, null, $tipoPerfil, $tipoPerfil, $cpf, $docEstrangeiro, null, true);

        $usuarios = $this->paginate($usuarios, array('limit' => 10, 'order' => array("Usuarios.nome" => "ASC")));

        $arraySet = array("usuarios");
        $this->set(compact($arraySet));
        $this->set('_serialize', $arraySet);
    }

    /**
     * Exibe a view para gerenciamento de um determinado usuário
     *
     * @return \Cake\Http\Response|void
     */
    public function administrarUsuario()
    {
        try {
            // verificar se quem está acessando é de fato um administrador RTI

            if (!$this->securityUtil->checkUserIsAuthorized($this->getUserLogged(), 'AdminDeveloperProfileType', 'AdminDeveloperProfileType')) {
                $this->securityUtil->redirectUserNotAuthorized($this);
            }

            $conditions = [];

            // condições básicas do sistema
            // só pode gerenciar de administradores de redes à cliente-final

            $nome = null;
            $email = null;
            $cpf = null;
            $docEstrangeiro = null;
            $tipoPerfil = null;
            $tipoPerfilMin = null;
            $tipoPerfilMax = null;
            $clientesIds = array();
            $redesId = null;

            $redesList = $this->Redes->getRedesList();

            $perfisUsuariosList = Configure::read("profileTypesTranslatedAdminToWorker");

            $queryPost = $this->request->session()->read("QueryConditions.AdministrarUsuario");

            if ($this->request->is(['post', 'put'])) {
                $data = $this->request->getData();

                // DebugUtil::print($data);
                $tipoPerfil = strlen($data["tipo_perfil"]) > 0 ? $data["tipo_perfil"] : null;
                $redesId = !empty($data["redes_id"]) ? $data["redes_id"] : null;
                $nome = !empty($data["nome"]) ? $data["nome"] : "";
                $email = !empty($data["email"]) ? $data["email"] : "";
                $docEstrangeiro = !empty($data["doc_estrangeiro"]) ? $data["doc_estrangeiro"] : "";
                $filtrarUnidade = !empty($data["filtrar_unidade"]) ? $data["filtrar_unidade"] : "";
                $cpf = !empty($data["cpf"]) ? $this->cleanNumber($data["cpf"]) : "";

                $queryPost["tipoPerfil"] = $tipoPerfil;
                $queryPost["nome"] = $nome;
                $queryPost["email"] = $email;
                $queryPost["docEstrangeiro"] = $docEstrangeiro;
                $queryPost["filtrarUnidade"] = $filtrarUnidade;
                $queryPost["cpf"] = $cpf;
                $queryPost["redesId"] = $redesId;

                $this->request->session()->write("QueryConditions.AdministrarUsuario", $queryPost);
            } else {
                // Obtem os dados cacheados para consulta
                $tipoPerfil = $queryPost["tipoPerfil"];
                $nome = $queryPost["nome"];
                $email = $queryPost["email"];
                $docEstrangeiro = $queryPost["docEstrangeiro"];
                $filtrarUnidade = $queryPost["filtrarUnidade"];
                $cpf = $queryPost["cpf"];
                $redesId = $queryPost["redesId"];
            }

            if (strlen($tipoPerfil) == 0) {
                $tipoPerfilMin = Configure::read('profileTypes')['AdminNetworkProfileType'];
                $tipoPerfilMax = Configure::read('profileTypes')['WorkerProfileType'];
            } else {
                $tipoPerfilMin = $tipoPerfil;
                $tipoPerfilMax = $tipoPerfil;
            }

            if (!empty($redesId)) {
                $clientesIds = $this->RedesHasClientes->getClientesIdsFromRedesHasClientes($redesId);
            }

            $usuarios = $this->Usuarios->findAllUsuarios(null, $clientesIds, $nome, $email, null, $tipoPerfilMin, $tipoPerfilMax, $cpf, $docEstrangeiro, 1, 1);

            // DebugUtil::printArray($usuarios->toArray());
            $usuarios = $this->paginate($usuarios, ['limit' => 10, 'order' => ['Clientes.matriz_id' => 'ASC', "Usuarios.nome" => "ASC", "Clientes.nome_fantasia" => "ASC"]]);

            // DebugUtil::printArray($usuarios->toArray());

            $arraySet = array("usuarios", "perfisUsuariosList", "redesList", "redesId");

            $this->set(compact($arraySet));
            $this->set('_serialize', $arraySet);
        } catch (\Exception $e) {

            $stringError = __("Erro: {0} ", $e->getMessage());

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Inicia o gerenciamento de um determinado usuário
     *
     * @return \Cake\Http\Response|void
     */
    public function iniciarAdministracaoUsuario()
    {
        // verifica se o usuário é um administrador RTI / developer

        if (!$this->securityUtil->checkUserIsAuthorized($this->getUserLogged(), 'AdminDeveloperProfileType', 'AdminDeveloperProfileType')) {
            $this->securityUtil->redirectUserNotAuthorized($this);
        }

        $query = $this->request->query;
        $usuarioAdministrador = $this->getUserLogged();
        $usuarioAdministrar = $this->Usuarios->getUsuarioById($query['usuariosId']);

        // Pegar o Id do cliente que foi passado via query
        $cliente = $this->Clientes->getClienteById($query["clientesId"]);

        // pega qual é a rede que o usuário está vinculado

        /**
         * Se o usuário for do tipo Usuário comum, não tem problema ele ainda não estar vinculado
         * pois quando fizer um abastecimento o script vai vincular.
         * Se for Níveis acimas, aí tem problema.
         */

        $clienteHasUsuario = $this->ClientesHasUsuarios->findClienteHasUsuario(
            [
                'ClientesHasUsuarios.usuarios_id' => $usuarioAdministrar["id"],
                'ClientesHasUsuarios.clientes_id' => $cliente["id"]
            ]
        );

        if (empty($clienteHasUsuario) && $usuarioAdministrar["tipo_perfil"] == Configure::read("profileTypes")["UserProfileType"]) {
            $this->Flash->error("Este usuário não pode ser administrado pois não possui vinculo ainda à uma rede / ponto de atendimento!");

            return $this->redirect(['controller' => 'usuarios', 'action' => 'administrarUsuario']);
        }

        $redeHasCliente = $this->RedesHasClientes->getRedesHasClientesByClientesId(
            $clienteHasUsuario["clientes_id"]
        );

        $rede = $redeHasCliente->rede;

        $this->request->session()->write('Usuario.UsuarioLogado', $usuarioAdministrar);
        $this->request->session()->write('Rede.Grupo', $rede);
        $this->request->session()->write('Rede.PontoAtendimento', $cliente);

        $this->request->session()->write("Usuario.AdministradorLogado", $usuarioAdministrador);
        $this->request->session()->write("Usuario.Administrar", $usuarioAdministrar);

        return $this->redirect(['controller' => 'pages', 'action' => 'display']);
    }

    /**
     * Finaliza o gerenciamento de um determinado usuário
     *
     * @return \Cake\Http\Response|void
     */
    public function finalizarAdministracaoUsuario()
    {
        $this->request->session()->delete("Usuario.AdministradorLogado");
        $this->request->session()->delete("Usuario.Administrar");
        $this->request->session()->delete("Usuario.UsuarioLogado");
        $this->request->session()->delete('Rede.Grupo');
        $this->request->session()->delete('Rede.PontoAtendimento');
        $this->request->session()->write("Usuario.UsuarioLogado", $this->Auth->user());

        return $this->redirect(['controller' => 'pages', 'action' => 'display']);
    }

    /**
     * Logoff method
     *
     * @return void
     */
    public function logout()
    {
        // limpa as informações de session
        $this->clearCredentials();

        $usuarioAdministrar = null;
        if (isset($usuarioAdministrador)) {
            $usuarioAdministrador = $usuarioLogado;
            $usuarioLogado = $this->request->session()->read('Usuario.Administrar');
        }

        return $this->redirect($this->Auth->logout());
    }

    #endregion

    #region REST Services

    /**
     * Action de Esqueci minha Senha
     *
     * @return \Cake\Http\Response|void
     * @author Gustavo Souza Gonçalves
     */
    public function esqueciMinhaSenhaAPI()
    {
        $mensagem = [];

        if ($this->request->is('post')) {
            $usuario = $this->Usuarios->getUsuarioByEmail($this->request->data['email']);
            if (is_null($usuario)) {
                $messageString = Configure::read("messageEmailNotFound");
                $mensagem[] = ['status' => false, 'message' => $messageString];
            } else {
                // gera o token que o usuário irá utilizar para recuperar a senha

                $token = bin2hex(openssl_random_pseudo_bytes(32));
                $url = Router::url(['controller' => 'usuarios', 'action' => 'resetar_minha_senha', $token], true);
                $timeout = time() + DAY;

                if ($this->Usuarios->setUsuarioTokenPasswordRequest($usuario->id, $token, $timeout)) {
                    $this->_sendResetEmail($url, $usuario);

                    $messageString = __("Email  enviado para {0} com o token de resetar a senha.", $usuario->email);

                    $mensagem[] = ['status' => true, 'message' => $messageString];
                } else {

                    $messageString = __('Houve um erro ao solicitar o token de resetar a senha.');
                    $mensagem[] = ['status' => false, 'message' => $messageString];
                }
            }
        }

        $arraySet = [
            'mensagem',
        ];

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);
    }

    /**
     * UsuariosController::getUsuarioAPI
     *
     * Obtem dados de usuário
     *
     * @params $data["id"] Id de Usuário
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 18/09/2018
     *
     * @return Object Model\Entity\Usuario
     */
    public function getUsuarioByIdAPI()
    {
        $id = null;

        if ($this->request->is("post")) {
            $data = $this->request->getData();

            $id = $data["id"];
        }

        if (empty($id)) {
            ResponseUtil::error("Id do usuário não informado!", Configure::read("messageWarningDefault"));
        }

        $usuario = $this->Usuarios->getUsuarioById($id);

        if (empty($usuario)) {
            ResponseUtil::error(Configure::read("messageLoadDataNotFound"), Configure::read("messageWarningDefault"));
        }

        ResponseUtil::success($usuario);
    }

    /**
     * UsuariosController::getUsuarioByDocEstrangeiroAPI
     *
     * Serviço REST para obter usuários contendo documento estrangeiro informado
     *
     * @param $data["doc_estrangeiro"] Documento Estrangeiro
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-03-13
     *
     * @return json_encode
     */
    public function getUsuarioByDocEstrangeiroAPI()
    {
        if ($this->request->is("post")) {
            $data = $this->request->getData();

            $documentoEstrangeiro = !empty($data["doc_estrangeiro"]) ? $data["doc_estrangeiro"] : null;

            if (empty($documentoEstrangeiro)) {
                ResponseUtil::error(MESSAGE_GENERIC_COMPLETED_ERROR, MESSAGE_GENERIC_ERROR, array(MSG_USUARIOS_DOC_ESTRANGEIRO_SEARCH_EMPTY));
            }
            $usuario = $this->Usuarios->getUsuarioByDocumentoEstrangeiro($documentoEstrangeiro);

            if ($usuario) {
                ResponseUtil::error("", "Aviso!", array(MSG_USUARIOS_DOC_ESTRANGEIRO_ALREADY_EXISTS));
            }
            ResponseUtil::success(0);
        }
    }

    /**
     * Obtem lista de funcionários de uma determinada rede/cliente
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-09-18
     *
     * @return json_encode
     */
    public function getFuncionariosListAPI()
    {
        $sessaoUsuario = $this->getSessionUserVariables();
        $usuarioLogado = $sessaoUsuario["usuarioLogado"];
        $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"];

        if ($usuarioAdministrar) {
            $usuarioLogado = $usuarioAdministrar;
        }

        $rede = $sessaoUsuario["rede"];
        $errors = [];
        $errorCodes = [];

        try {
            if ($this->request->is(Request::METHOD_GET)) {

                $data = $this->request->getQueryParams();
                $redesId = !empty($data["redes_id"]) ? $data["redes_id"] : null;
                $clientesId = !empty($data["clientes_id"]) ? $data["clientes_id"] : null;
                $tipoPerfil = !empty($data["tipo_perfil"]) ? $data["tipo_perfil"] : null;

                if ($usuarioLogado->tipo_perfil != PROFILE_TYPE_ADMIN_DEVELOPER) {
                    $redesId = $sessaoUsuario["rede"]["id"];
                }

                // Se o usuário trabalha na rede, ele tem que ter vínculo, então não se muda a seleção
                if ($usuarioLogado->tipo_perfil >= PROFILE_TYPE_ADMIN_NETWORK && $usuarioLogado->tipo_perfil <= PROFILE_TYPE_WORKER) {
                    $redesId = $rede->id;
                }

                // se não tiver especificado id da rede ou do cliente, retorna erro

                if (empty($redesId) && $usuarioLogado->tipo_perfil == PROFILE_TYPE_ADMIN_DEVELOPER) {
                    $errors[] = MSG_REDES_FILTER_REQUIRED;
                    $errorCodes[] = MSG_REDES_FILTER_REQUIRED_CODE;
                }

                if (empty($clientesId) && !in_array($usuarioLogado->tipo_perfil, [PROFILE_TYPE_ADMIN_NETWORK, PROFILE_TYPE_ADMIN_REGIONAL])) {
                    $errors[] = MSG_CLIENTES_FILTER_REQUIRED;
                    $errorCodes[] = MSG_CLIENTES_FILTER_REQUIRED_CODE;
                }

                if (count($errors) > 0) {
                    throw new Exception(MSG_LOAD_EXCEPTION, MSG_LOAD_EXCEPTION_CODE);
                }

                $tipoPerfis = [];

                if (!empty($tipoPerfil)) {
                    $tipoPerfis = $tipoPerfil;
                } else {
                    $tipoPerfis = [PROFILE_TYPE_WORKER, PROFILE_TYPE_DUMMY_WORKER];
                }

                $clientesIds = empty($clientesId) ? [] : [$clientesId];

                // Modificar este serviço para aceitar uma lista de arrays para tipo_perfil
                $usuariosList = $this->ClientesHasUsuarios->getFuncionariosRede($redesId, $clientesIds, null, $tipoPerfis);

                if ($usuariosList) {
                    $usuariosList = $usuariosList->toArray();
                    $data = ["usuarios" => $usuariosList];

                    return ResponseUtil::successAPI(MSG_LOAD_DATA_WITH_SUCCESS, ["data" => $data]);
                }
            }
        } catch (\Throwable $th) {
            $errorMessage = $th->getMessage();
            $errorCode = $th->getCode();

            if (count($errors) == 0) {
                $errors[] = $errorMessage;
                $errorCodes[] = $errorCode;
            }

            for ($i = 0; $i < count($errors); $i++) {
                Log::write("error", sprintf("[%s] %s - %s", MESSAGE_LOAD_DATA_WITH_ERROR, $errorCodes[$i], $errors[$i]));
            }

            return ResponseUtil::errorAPI(MESSAGE_LOAD_DATA_WITH_ERROR, $errors, [], $errorCodes);
        }
    }

    /**
     * UsuariosController::getUsuariosAssiduosAPI
     *
     * Obtem dados de usuários fidelizados
     *
     * @param array $data Dados de Post
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 20/09/2018
     *
     * @return json_encode
     */
    public function getUsuariosAssiduosAPI()
    {
        $rede = $this->request->session()->read("Network.Main");

        $mediaAssiduidadeClientes = $rede["media_assiduidade_clientes"];

        $redesId = $rede["id"];

        $data = array();
        if ($this->request->is("post")) {
            $data = $this->request->getData();

            $usuarios = $this->_consultaUsuariosAssiduos($data, $redesId, $mediaAssiduidadeClientes);
        }

        if (sizeof($usuarios) > 0) {
            ResponseUtil::success($usuarios);
        } else {
            ResponseUtil::error(Configure::read("messageLoadDataNotFound"), Configure::read("messageWarningDefault"));
        }
    }

    /**
     * UsuariosController::generateExcelUsuariosAssiduosAPI
     *
     * Gera relatório de usuários fidelizados pela rede
     *
     * @param array $data Dados de Post
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 20/09/2018
     *
     * @return json_encode Dados de excel em json_encode
     */
    public function generateExcelUsuariosAssiduosAPI()
    {
        $rede = $this->request->session()->read("Network.Main");

        $mediaAssiduidadeClientes = $rede["media_assiduidade_clientes"];

        $redesId = $rede["id"];

        $filtrarPorUsuario = false;

        $data = array();
        if ($this->request->is("post")) {
            $data = $this->request->getData();

            $filtrarPorUsuario = !empty($data["filtrarPorUsuario"]) ? $data["filtrarPorUsuario"] : false;

            $usuarios = $this->_consultaUsuariosAssiduos($data, $redesId, $mediaAssiduidadeClientes);
        }

        if (sizeof($usuarios) == 0) {
            ResponseUtil::error(Configure::read("messageLoadDataNotFound"), Configure::read("messageWarningDefault"));
        }

        $usuariosArray = array();
        $usuarioTemp = array();

        $titulo = "";

        // Log::write("info", $usuarios);
        // die();
        if ($filtrarPorUsuario) {

            $cabecalho = array(
                "Ano",
                "Mes",
                "Status Assiduidade",
                "Media Assiduidade"
            );

            $nomeUsuario = $usuarios[0]["nome"];
            $cpf = $usuarios[0]["cpf"];
            $documentoEstrangeiro = $usuarios[0]["docEstrangeiro"];
            $documento = !empty($cpf) ? $cpf : $documentoEstrangeiro;
            $titulo = sprintf("%s: %s (%s)", "Relatório de Usuários Assíduos", $nomeUsuario, $documento);

            foreach ($usuarios as $usuario) {
                $usuarioTemp["ano"] = $usuario["ano"];
                $usuarioTemp["mes"] = $usuario["mes"];
                $usuarioTemp["statusAssiduidade"] = $usuario["statusAssiduidade"] == 1 ? "Regular" : "Irregular";
                $usuarioTemp["mediaAssiduidade"] = $usuario["mediaAssiduidade"];

                $usuariosArray[] = $usuarioTemp;
            }
        } else {

            $cabecalho = array(
                "Usuário",
                "CPF",
                "Documento Estrangeiro",
                "Conta Ativa",
                "Status Assiduidade",
                "Total Assiduidade",
                "Media Assiduidade",
                "Gotas Adquiridas",
                "Gotas Utilizadas",
                "Gotas Expiradas",
                "Saldo Atual",
                "Total Moeda Compra Brindes (R$)"
            );

            $titulo = "Relatório de Usuários Assíduos";

            foreach ($usuarios as $usuario) {
                $usuarioTemp["nome"] = $usuario["nome"];
                $usuarioTemp["cpf"] = $usuario["cpf"];
                $usuarioTemp["docEstrangeiro"] = $usuario["docEstrangeiro"];
                $usuarioTemp["statusConta"] = $usuario["statusConta"];
                $usuarioTemp["statusAssiduidade"] = $usuario["statusAssiduidade"] ? "Regular" : "Irregular";
                $usuarioTemp["totalAssiduidade"] = $usuario["totalAssiduidade"];
                $usuarioTemp["mediaAssiduidade"] = $usuario["mediaAssiduidade"];
                $usuarioTemp["gotasAdquiridas"] = $usuario["gotasAdquiridas"];
                $usuarioTemp["gotasUtilizadas"] = $usuario["gotasUtilizadas"];
                $usuarioTemp["gotasExpiradas"] = $usuario["gotasExpiradas"];
                $usuarioTemp["saldoAtual"] = $usuario["saldoAtual"];
                $usuarioTemp["totalMoedaCompraBrindes"] = $usuario["totalMoedaCompraBrindes"];

                $usuariosArray[] = $usuarioTemp;
            }
        }

        $usuarios = $usuariosArray;

        $excel = ExcelUtil::generateExcel($titulo, $cabecalho, $usuarios);

        ResponseUtil::success($excel);
    }


    /**
     * Obtem dados de usuários fidelizados
     *
     * @return void
     */
    public function getUsuariosFidelizadosAPI()
    {
        $rede = $this->request->session()->read("Network.Main");
        $redesId = $rede["id"];

        $data = array();
        if ($this->request->is("post")) {
            $data = $this->request->getData();

            $usuarios = $this->_consultaUsuariosFidelizados($data, $redesId);
        }

        if (sizeof($usuarios) > 0) {
            ResponseUtil::success($usuarios);
        } else {
            ResponseUtil::error(Configure::read("messageLoadDataNotFound"), Configure::read("messageWarningDefault"));
        }
    }

    /**
     * @filesource src\Controller\UsuariosController.php::getUsuariosFidelizadosRedeAPI
     * Obtem os Usuários Fidelizados pela Rede / Posto(s) da Rede
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-09-25
     *
     * @return json_encode
     */
    public function getUsuariosFidelizadosRedeAPI()
    {
        $sessao = $this->getSessionUserVariables();
        $usuarioLogado = $sessao["usuarioLogado"];
        $rede = $sessao["rede"];
        $cliente = $sessao["cliente"];

        if ($this->request->is(Request::METHOD_GET)) {
            $data = $this->request->getQueryParams();

            $redesId = !empty($data["redes_id"]) ? $data["redes_id"] : $rede->id;
            $clientesId = !empty($data["clientes_id"]) ? $data["clientes_id"] : $cliente->id;
            $funcionariosId = !empty($data["funcionarios_id"]) ? $data["funcionarios_id"] : null;
            $dataInicio = !empty($data["data_inicio"]) ? $data["data_inicio"] : null;
            $dataFim = !empty($data["data_fim"]) ? $data["data_fim"] : null;
            $tipoRelatorio = !empty($data["tipo_relatorio"]) ? $data["tipo_relatorio"] : REPORT_TYPE_SYNTHETIC;

            $errors = [];
            $errorCodes = [];

            #region Validação de parametros preenchidos

            try {
                if (empty($redesId)) {
                    $errors[] = MSG_REDES_FILTER_REQUIRED;
                    $errorCodes[] = MSG_REDES_FILTER_REQUIRED_CODE;
                }

                if (empty($clientesId) && empty($redesId)) {
                    $errors[] = MSG_CLIENTES_FILTER_REQUIRED;
                    $errorCodes[] = MSG_CLIENTES_FILTER_REQUIRED_CODE;
                }

                if (empty($dataInicio)) {
                    $errors[] = MSG_DATE_BEGIN_EMPTY;
                    $errorCodes[] = MSG_DATE_BEGIN_EMPTY_CODE;
                }

                if (empty($dataFim)) {
                    $errors[] = MSG_DATE_END_EMPTY;
                    $errorCodes[] = MSG_DATE_END_EMPTY_CODE;
                }

                if (empty($tipoRelatorio)) {
                    $errors[] = MSG_REPORT_TYPE_EMPTY;
                    $errorCodes[] = MSG_REPORT_TYPE_EMPTY_CODE;
                }

                $dataInicio = new DateTime(sprintf("%s 00:00:00", $dataInicio));
                $dataFim = new DateTime(sprintf("%s 23:59:59", $dataFim));

                $dataDiferenca = $dataFim->diff($dataInicio);

                if ($tipoRelatorio == REPORT_TYPE_ANALYTICAL) {
                    // Máximo de tempo será 1 mês
                    if ($dataDiferenca->m >= 1) {
                        $errors[] = sprintf(MSG_MAX_FILTER_TIME_MONTH, "1");
                        $errorCodes[] = sprintf(MSG_MAX_FILTER_TIME_MONTH_CODE, "1");
                    }
                } else {
                    // Máximo de tempo será 1 ano
                    if ($dataDiferenca->y >= 1) {
                        $errors[] = MSG_MAX_FILTER_TIME_ONE_YEAR;
                        $errorCodes[] = MSG_MAX_FILTER_TIME_ONE_YEAR_CODE;
                    }
                }

                if (!$dataDiferenca->invert) {
                    // Se a data fim for maior que a data início, erro.
                    $errors[] = MSG_DATE_BEGIN_GREATER_THAN_DATE_END;
                    $errorCodes[] = MSG_DATE_BEGIN_GREATER_THAN_DATE_END_CODE;
                }

                if (count($errors) > 0) {
                    throw new Exception(MESSAGE_GENERIC_EXCEPTION, MESSAGE_GENERIC_EXCEPTION_CODE);
                }
            } catch (\Throwable $th) {
                $code = $th->getCode();
                $message = $th->getMessage();
                $length = count($errors);

                if ($length == 0) {
                    $errorCodes[] = MESSAGE_GENERIC_EXCEPTION_CODE;
                    $errors[] = MESSAGE_GENERIC_EXCEPTION;
                    $length = count($errors);
                }

                for ($i = 0; $i < $length; $i++) {
                    Log::write("error", sprintf("[%s] %s: %s.", MESSAGE_GENERIC_EXCEPTION, $errors[$i], $errorCodes[$i]));
                }

                return ResponseUtil::errorAPI(MESSAGE_GENERIC_EXCEPTION, $errors, [], $errorCodes);
            }

            #endregion

            // Obtem a lista de funcionários e faz o agrupamento
            try {
                $clientes = [];
                $clientesTemp = [];

                if (empty($clientesId)) {
                    $clientes = $this->RedesHasClientes->getRedesHasClientesByRedesId($redesId);
                } else {
                    $cliente = $this->Clientes->get($clientesId);
                    $clientes[] = $cliente;
                }

                foreach ($clientes as $cliente) {
                    $funcionariosTemp = $this->ClientesHasUsuarios->getFuncionariosRede($redesId, [$cliente->id], $funcionariosId, [PROFILE_TYPE_WORKER, PROFILE_TYPE_DUMMY_WORKER]);
                    $data = new stdClass();
                    $data = $cliente;
                    $data->funcionarios = $funcionariosTemp->toArray();
                    $clientesTemp[] = $data;
                }

                $clientes = $clientesTemp;
            } catch (Throwable $th) {
                $code = $th->getCode();
                $message = $th->getMessage();

                Log::write("error", sprintf("[%s] - %s: %s.", MSG_LOAD_EXCEPTION, $code, $message));

                return ResponseUtil::errorAPI(MSG_LOAD_EXCEPTION, [$message], [], [$code]);
            }

            /**
             * Com a lista de funcionários, verifica quais foram os clientes cadastrados pelos funcionários
             * dentro daquela rede / posto
             */
            $dataRetorno = [];
            $totalUsuarios = 0;

            try {
                foreach ($clientes as $cliente) {
                    foreach ($cliente->funcionarios as $funcionario) {
                        $funcionario = $funcionario->usuario;
                        $queryUsuarios = $this->ClientesHasUsuarios->getUsuariosCadastradosFuncionarios($redesId, $cliente->id, $funcionario->id, $dataInicio, $dataFim);
                        if ($tipoRelatorio == REPORT_TYPE_ANALYTICAL) {
                            $usuarios = $queryUsuarios->toArray();
                            $funcionario->clientes_has_usuarios = $usuarios;
                        }
                        $count = $queryUsuarios->count();
                        $totalUsuarios += $count;
                        $funcionario->clientes_has_usuarios_soma = $count;
                    }
                    // $cliente["clientes_has_usuarios"] = $usuarios;
                }
            } catch (\Throwable $th) {
                $code = $th->getCode();
                $message = $th->getMessage();

                Log::write("error", sprintf("[%s] - %s: %s.", MSG_LOAD_EXCEPTION, $code, $message));

                return ResponseUtil::errorAPI(MSG_LOAD_EXCEPTION, [$message], [], [$code]);
            }

            $dataRetorno = new stdClass();
            $dataRetorno->clientes = $clientes;
            $dataRetorno->clientes_has_usuarios_total = $totalUsuarios;

            return ResponseUtil::successAPI(MSG_LOAD_DATA_WITH_SUCCESS, ['data' => $dataRetorno]);
        }
    }

    /**
     * UsuariosController::generateExcelUsuariosFidelizadosAPI
     *
     * Gera relatório de usuários fidelizados pela rede
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 12/09/2018
     *
     * @return json_encode Dados de excel em json_encode
     */
    public function generateExcelUsuariosFidelizadosAPI()
    {
        $rede = $this->request->session()->read("Network.Main");
        $redesId = $rede["id"];

        $data = array();
        if ($this->request->is("post")) {
            $data = $this->request->getData();

            $usuarios = $this->_consultaUsuariosFidelizados($data, $redesId);
        }

        $cabecalho = array(
            "Usuário",
            "CPF",
            "Documento Estrangeiro",
            "Conta Ativa",
            "Gotas Adquiridas",
            "Gotas Utilizadas",
            "Gotas Expiradas",
            "Saldo Atual",
            "Brindes Vendidos (R$)",
            "Data Cadastro na Rede",
        );

        if (sizeof($usuarios) == 0) {
            ResponseUtil::error(Configure::read("messageLoadDataNotFound"), Configure::read("messageWarningDefault"));
        }

        $usuariosArray = array();
        $usuarioTemp = array();
        foreach ($usuarios as $usuario) {
            $usuarioTemp["nome"] = $usuario["nome"];
            $usuarioTemp["cpf"] = $usuario["cpf"];
            $usuarioTemp["docEstrangeiro"] = $usuario["docEstrangeiro"];
            $usuarioTemp["contaAtiva"] = $usuario["contaAtiva"] == 1 ? "Sim" : "Nâo";
            $usuarioTemp["gotasAdquiridas"] = $usuario["gotasAdquiridas"];
            $usuarioTemp["gotasUtilizadas"] = $usuario["gotasUtilizadas"];
            $usuarioTemp["gotasExpiradas"] = $usuario["gotasExpiradas"];
            $usuarioTemp["saldoAtual"] = $usuario["saldoAtual"];
            $usuarioTemp["totalMoedaAdquirida"] = $usuario["totalMoedaAdquirida"];
            $usuarioTemp["dataVinculo"] = $usuario["dataVinculo"];

            $usuariosArray[] = $usuarioTemp;
        }

        $usuarios = $usuariosArray;

        $excel = ExcelUtil::generateExcel("Relatório de Usuários Fidelizados", $cabecalho, $usuarios);

        ResponseUtil::success($excel);
    }

    /**
     * Obtêm token de autenticação
     *
     * @return void
     */
    public function loginAPI()
    {
        $usuario = null;
        $cliente = null;

        if ($this->request->is("post")) {
            $data = $this->request->getData();

            Log::write("info", sprintf("Info de %s: %s - %s: %s", Request::METHOD_POST, __CLASS__, __METHOD__, print_r($data, true)));

            $email = !empty($data["email"]) ? $data["email"] : null;
            $senha = !empty($data["senha"]) ? $data["senha"] : null;
            $redesId = !empty($data["redes_id"]) ? $data["redes_id"] : null;
            $cnpj = !empty($data["cnpj"]) ? $data["cnpj"] : null;

            if (empty($email) || empty($senha)) {
                // Retorna mensagem de erro se campos estiverem vazios
                $message = empty($message) ? MSG_USUARIOS_LOGIN_PASSWORD_INCORRECT : $message;

                return ResponseUtil::errorAPI($message);
            }

            // Se chegar já sem formatação, verifica

            $format = "/(\d{3}).(\d{3}).(\d{3})-(\d{2})/";

            $match = preg_match($format, $email);

            $cnpj = preg_replace("/\D/", "", $cnpj);

            if (is_numeric($email) || $match) {
                $cpf = NumberUtil::limparFormatacaoNumeros($email);

                if (strlen($cpf) != CPF_LENGTH) {
                    $error = array();
                    $error[] = MSG_USUARIOS_CPF_LENGTH_INVALID;

                    return ResponseUtil::errorAPI(MESSAGE_GENERIC_ERROR, $error);
                }

                $usuario = $this->Usuarios->getUsuarioByCPF($cpf);

                if (empty($usuario)) {
                    return ResponseUtil::errorAPI(MSG_USUARIOS_LOGIN_PASSWORD_INCORRECT);
                }

                $email = !empty($usuario["email"]) ? $usuario->email : $usuario->cpf;
                $this->request->data["email"] = $email;
            }

            $retornoLogin = $this->checkLoginUser($email, $senha, LOGIN_API, $redesId, $cnpj);

            $recoverAccount = !empty($retornoLogin["recoverAccount"]) ? $retornoLogin["recoverAccount"] : null;
            $usuario = !empty($retornoLogin["usuario"]) ? $retornoLogin["usuario"] : null;
            $email = !empty($data["email"]) ? $data["email"] : null;
            $message = !empty($retornoLogin["message"]) ? $retornoLogin["message"] : null;
            $status = isset($retornoLogin["status"]) ? $retornoLogin["status"] : null;
            $cliente = isset($retornoLogin["cliente"]) ? $retornoLogin["cliente"] : null;
        }

        if (!$usuario) {
            $this->Auth->logout();
            $this->clearCredentials();

            $message = empty($message) ? MSG_USUARIOS_LOGIN_PASSWORD_INCORRECT : $message;

            return ResponseUtil::errorAPI($message, $retornoLogin['errors'], [], $retornoLogin["errorCodes"]);
        }

        $mensagem = array(
            'status' => true,
            'message' => Configure::read('messageUsuarioLoggedInSuccessfully')
        );

        $listaPermissoes = array();
        if ($usuario["tipo_perfil"] >= PROFILE_TYPE_MANAGER && $usuario->tipo_perfil <= PROFILE_TYPE_WORKER) {
            $listaPermissoes = [
                [
                    "funcao" => "VALIDAR_BRINDE",
                    "status" => 1
                ],
                [
                    "funcao" => "CADASTRAR_USUARIO",
                    "status" => 1
                ],
                [
                    "funcao" => "PONTUAR_USUARIO",
                    "status" => 1
                ],
                [
                    "funcao" => "CONFIG_HARDWARE_APP_POSTO",
                    "status" => $usuario->tipo_perfil == PROFILE_TYPE_WORKER ? 0 : 1
                ]
            ];
        }

        $usuario["lista_permissoes"] = $listaPermissoes;

        return ResponseUtil::successAPI(MSG_USUARIOS_LOGGED_IN_SUCCESSFULLY, array("usuario" => $usuario, "cliente" => $cliente));
    }

    /**
     * Obtêm token de autenticação
     *
     * @return void
     */
    public function logoutAPI()
    {
        $usuario = $this->Auth->user();

        if (!$usuario) {
            throw new UnauthorizedException('Usuário ou senha inválidos');
        }

        $mensagem = [
            'status' => true,
            'message' => Configure::read('messageUsuarioLoggedOutSuccessfully')
        ];


        $usuario = [
            'id' => $usuario['id'],
            'token' => JWT::encode(
                [
                    'id' => $usuario['id'],
                    'sub' => $usuario['id'],
                    'exp' => time() + 1
                ],
                Security::salt()
            )
        ];

        $this->Auth->logout();
        $this->clearCredentials();

        $arraySet = [
            'mensagem'
        ];

        $this->set(compact($arraySet));
        $this->set('_serialize', $arraySet);
    }

    /**
     * UsuariosController::meuPerfilAPI
     *
     * Retorna os dados de perfil do usuário logado
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 2018/05/09
     *
     * @return json object
     */
    public function meuPerfilAPI()
    {
        $mensagem = array();

        $status = false;
        $message = null;
        $errors = array();

        try {
            $usuario = $this->Usuarios->getUsuarioById($this->Auth->user()["id"]);

            $mensagem = ['status' => true, 'message' => $message, 'errors' => $errors];
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $messageString = __("Erro ao obter dados de usuário!");

            $errors = $trace;
            $mensagem = ['status' => false, 'message' => $messageString, 'errors' => $errors];

            $messageStringDebug = __("{0} - {1} . [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $messageString, $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write("error", $messageStringDebug);
            Log::write("error", $trace);
        }

        $arraySet = [
            "mensagem",
            "usuario"
        ];

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);
    }

    /**
     * Adiciona Conta de usuário
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function registrarAPI()
    {
        $usuario = $this->Usuarios->newEntity();
        $sessaoUsuario = $this->getSessionUserVariables();
        $usuarioLogado = $sessaoUsuario["usuarioLogado"] ?? null;
        $cliente = $sessaoUsuario["cliente"] ?? null;
        $mensagem = array();
        $usuarioRegistrado = null;
        $errors = array();

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();

            Log::write("info", sprintf("Info Service REST: %s - %s.", __CLASS__, __METHOD__));
            Log::write("info", $data);

            $tipoPerfil = isset($data["tipo_perfil"]) ? $data["tipo_perfil"] : null;

            // validação de cpf
            if (isset($data["cpf"])) {
                $result = NumberUtil::validarCPF($data["cpf"]);

                if (!$result["status"]) {
                    $mensagem["status"] = false;
                    $mensagem["message"] = __($result["message"], $data["cpf"]);
                    $arraySet = ["mensagem"];

                    $this->set(compact($arraySet));
                    $this->set("_serialize", $arraySet);

                    return;
                }
            }

            $usuario = $this->Usuarios->getUsuarioByCPF($data["cpf"]);

            if (!$usuario) {
                $usuario = $this->Usuarios->newEntity();
            }

            // Se o usuário já possui conta ativa, significa que já não é mais pendente ou novo, então retorna e nega cadastro
            if ($usuario->conta_ativa) {
                $errors[] = MSG_USUARIOS_CPF_ALREADY_EXISTS;
                $errorCodes[] = MSG_USUARIOS_CPF_ALREADY_EXISTS_CODE;

                return ResponseUtil::errorAPI(MESSAGE_SAVED_EXCEPTION, $errors, [], $errorCodes);
            }

            if ((isset($tipoPerfil) && $tipoPerfil >= Configure::read("profileTypes")["DummyWorkerProfileType"]) || !$usuario->conta_ativa) {
                // Funcionário ou usuário fictício não precisa de validação de cpf

                $this->Usuarios->validator()->remove('cpf');
                if (!isset($usuario->tipo_perfil)) {
                    $data['tipo_perfil'] = (int) PROFILE_TYPE_USER;
                }
            } else {
                $data['tipo_perfil'] = (int) PROFILE_TYPE_USER;
            }
            $data["doc_invalido"] = false;

            // validação de cpf

            $canContinue = false;

            // Remove os campos da inserção que não são permitidos ao fazer insert

            if (isset($data["token_senha"])) {
                unset($data["token_senha"]);
            }

            if (isset($data["data_expiracao_token"])) {
                unset($data["data_expiracao_token"]);
            }

            if (isset($data["conta_ativa"])) {
                unset($data["conta_ativa"]);
            }

            if (isset($data["conta_bloqueada"])) {
                unset($data["conta_bloqueada"]);
            }

            if (isset($data["tentativas_login"])) {
                unset($data["tentativas_login"]);
            }

            if (isset($data["ultima_tentativa_login"])) {
                unset($data["ultima_tentativa_login"]);
            }

            if (empty($data['telefone'])) {
                $this->Usuarios->validator('Default')->notEmpty('telefone', "O campo TELEFONE precisa ser informado!");
            }

            if (!isset($data["cpf"]) && $tipoPerfil < (int) Configure::read("DummyWorkerProfileType")) {
                $errors[] = array("CPF" => "CPF Deve ser informado!");
                $canContinue = false;
            } else {

                // Valida se o usuário em questão não é ficticio
                if ($tipoPerfil < (int) PROFILE_TYPE_DUMMY_WORKER) {

                    $result = NumberUtil::validarCPF($data["cpf"]);

                    if (!$result["status"]) {
                        $mensagem["status"] = false;
                        $mensagem["message"] = __($result["message"], $data["cpf"]);
                        $arraySet = ["mensagem"];

                        $this->set(compact($arraySet));
                        $this->set("_serialize", $arraySet);

                        return;
                    }
                }


                $canContinue = true;
            }

            $email = !empty($data["email"]) ? $data["email"] : null;
            if (empty($email)) {
                // $errors[] = array("email" => "Email deve ser informado!");
                // $canContinue = false;
                // Email pode ser vazio, neste caso, deve ser copiado o campo de cpf

                $data["email"] = preg_replace("/\D/", "", $data["cpf"]);
            } else {
                $resultado = EmailUtil::validateEmail($data["email"]);

                if (!$resultado["status"]) {
                    $mensagem = [
                        "status" => $resultado["status"],
                        "message" => __($resultado["message"], $data["email"])
                    ];

                    $arraySet = ["mensagem"];

                    $this->set(compact($arraySet));
                    $this->set("_serialize", $arraySet);

                    return;
                }
                $canContinue = true;
            }

            // Faz o tratamento do envio da imagem ao servidor, se especificado

            if (isset($data["foto"])) {

                $foto = $data["foto"];

                $nomeImagem = $foto["image_name"];
                $base64Imagem = $foto["value"];
                $extensao = $foto["extension"];

                $resultado = ImageUtil::generateImageFromBase64(
                    $base64Imagem,
                    Configure::read("temporaryDocumentUserPath") . $nomeImagem . "." . $extensao,
                    Configure::read("temporaryDocumentUserPath")
                );

                // Move o arquivo gerado
                $fotoPerfil = $this->moveDocumentPermanently(
                    Configure::read("temporaryDocumentUserPath") . $nomeImagem . "." . $extensao,
                    Configure::read("documentUserPath"),
                    null,
                    $extensao
                );

                // Remove o array do item de gravação e passa a imagem
                unset($data["foto"]);

                $data["foto_perfil"] = $fotoPerfil;
            }

            $usuarioData = $data;
            $email = !empty($usuarioData["email"]) ? $usuarioData["email"] : null;

            // verifica se o usuário já está registrado

            $usuarioJaExiste = $this->Usuarios->getUsuarioByEmail($email);

            if ($canContinue) {

                // verifica se usuário já existe no sistema
                if ($usuarioJaExiste) {
                    $mensagem = [
                        'status' => false,
                        'message' => "Usuário " . $usuarioData['email'] . " já existe no sistema!"
                    ];
                } else {
                    // senão, grava no banco
                    // Caso não seja informado senha de usuário no momento do cadastro, a senha padrão é 123456
                    $usuarioData["senha"] = !empty($usuarioData["senha"]) ? $usuarioData["senha"] : 123456;
                    $usuarioData["confirm_senha"] = !empty($usuarioData["confirm_senha"]) ? $usuarioData["confirm_senha"] : 123456;
                    $usuarioData["necessidades_especiais"] = isset($usuarioData["necessidades_especiais"]) ? (int) $usuarioData["necessidades_especiais"] : 0;

                    // Desativado no momento pois não temos certeza que será desenvolvido o aplicativo desktop
                    // if (!empty($senha)) {
                    //     $passwordEncrypt = $this->cryptUtil->encrypt($usuarioData['senha']);
                    // }

                    if (!empty($usuario) && $usuario->id > 0) {
                        $usuarioData["tipo_perfil"] = $usuario->tipo_perfil;
                        $usuarioData["id"] = $usuario->id;
                    }

                    $usuarioData["conta_ativa"] = true;
                    $usuario = $this->Usuarios->patchEntity($usuario, $usuarioData);

                    foreach ($usuario->errors() as $key => $erro) {
                        $errors[] = $erro;
                    }

                    $usuario = $this->Usuarios->save($usuario);

                    if ($usuario) {
                        // Se usuarioLogado, significa que foi registrado através de um funcionário
                        // Faz vinculação
                        if (!empty($usuarioLogado)) {
                            $this->ClientesHasUsuarios->saveClienteHasUsuario($cliente->id, $usuario->id, 1, $usuarioLogado->id);
                        } else {
                            // Ativa o usuário em todos os postos se tiver gravado o registro (e não for usuário logado)
                            $this->ClientesHasUsuarios->updateClientesHasUsuario(null, $usuario->id, true);
                        }

                        // Realiza login de autenticação
                        $usuario = [
                            'id' => $usuario->id,
                            'token' => JWT::encode(
                                [
                                    'sub' => $usuario->id,
                                    'exp' => time() + 604800
                                ],
                                Security::salt()
                            )
                        ];

                        $mensagem = array(
                            "status" => 1,
                            "message" => "Usuário registrado com sucesso!",
                            "errors" => $errors
                        );
                    } else {
                        $mensagem = [
                            'status' => false,
                            'message' => __("{0}, {1}", Configure::read('messageGenericCompletedError'), Configure::read('messageGenericCheckFields')),
                            'errors' => $errors
                        ];
                    }
                }
            } else {
                $mensagem = [
                    'status' => false,
                    'message' => __("{0}, {1}", Configure::read('messageGenericCompletedError'), Configure::read('messageGenericCheckFields')),
                    'errors' => $errors
                ];
            }
        }

        $arraySet = array('usuario', 'mensagem');

        $this->set(compact($arraySet));
        $this->set('_serialize', $arraySet);
    }

    /**
     * UsuariosController::setPerfilAPI
     *
     * Atualiza o cadastro do usuário logado via API.
     *
     * @param int      $data["tipo_perfil"]            Tipo de Perfil. Somente leitura
     * @param string   $data["nome"]                   Nome Completo
     * @param date     $data["data_nasc"]              Data de Nascimento. Formato DDDD/MM/YY
     * @param bool     $data["sexo"]                   Masculino / Feminino (1 / 0)
     * @param bool     $data["necessidades_especiais"] Necessidades Especiais :
     * @param string   $data["cpf"]                    Cpf : CPF do Usuário.
     * @param string   $data["foto_documento"]         Foto Documento : URL da carteira de identidade / Doc. Estrangeiro.
     * @param string   $data["doc_estrangeiro"]        Doc Estrangeiro : Documento Estrangeiro.
     * @param string   $data["email"]                  Email : Email de cadastro. Somente leitura.
     * @param string   $data["senha"]                  Senha : Somente leitura neste serviço
     * @param string   $data["telefone"]               Telefone . 10 dígitos para TEL, 11 para CEL
     * @param string   $data["endereco"]               Endereco
     * @param string   $data["endereco_numero"]        Endereco Numero . Vazio = S/N
     * @param string   $data["endereco_complemento"]   Endereco Complemento .
     * @param string   $data["bairro"]                 Bairro
     * @param string   $data["municipio"]              Municipio
     * @param string   $data["estado"]                 Estado
     * @param string   $data["pais"]                   Pais
     * @param string   $data["cep"]                    Cep. Formato 99999999
     * @param string   $data["token_senha"]            Token Senha : Token de Senha. Utilizado para reset de senha. Somente leitura
     * @param datetime $data["data_expiracao_token"]   Data Expiracao Token. Somente Leitura
     * @param bool     $data["conta_ativa"]            Conta Ativa : 1 = Ativo. 0 = Desativada. Somente leitura aqui. Somente Leitura
     * @param bool     $data["conta_bloqueada"]        Conta Bloqueada : [1 = Bloqueada/0 = Desbloqueada]. Somente leitura. Somente Leitura
     * @param int      $data["tentativas_login"]       Tentativas Login : Somente Leitura.
     * @param datetime $data["ultima_tentativa_login"] Ultima Tentativa Login : Somente Leitura
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 2018/05/10
     *
     * @return json object
     */
    public function setPerfilAPI()
    {
        $mensagem = array();

        $status = false;
        $message = null;
        $errors = array();

        try {
            $usuario = $this->Usuarios->getUsuarioById($this->Auth->user()["id"]);

            if ($this->request->is("post")) {

                $data = $this->request->getData();

                Log::write("info", sprintf("Info de %s: %s - %s: %s", Request::METHOD_POST, __CLASS__, __METHOD__, print_r($data, true)));

                // DebugUtil::printArray($data);

                // validação de cpf
                if (isset($data["cpf"])) {
                    $result = NumberUtil::validarCPF($data["cpf"]);

                    if (!$result["status"]) {
                        $mensagem["status"] = false;
                        $mensagem["message"] = __($result["message"], $data["cpf"]);
                        $arraySet = ["mensagem"];

                        $this->set(compact($arraySet));
                        $this->set("_serialize", $arraySet);

                        return;
                    }
                }

                // validação de e-mail

                if (isset($data["email"])) {
                    $resultado = EmailUtil::validateEmail($data["email"]);

                    if (!$resultado["status"]) {
                        $mensagem = [
                            "status" => $resultado["status"],
                            "message" => __($resultado["message"], $data["email"])
                        ];

                        $arraySet = ["mensagem"];

                        $this->set(compact($arraySet));
                        $this->set("_serialize", $arraySet);

                        return;
                    }
                }

                // Faz o tratamento do envio da imagem ao servidor, se especificado

                if (isset($data["foto"])) {

                    $foto = $data["foto"];

                    $nomeImagem = $foto["image_name"];
                    $base64Imagem = $foto["value"];
                    $extensao = $foto["extension"];

                    $resultado = ImageUtil::generateImageFromBase64(
                        $base64Imagem,
                        Configure::read("temporaryDocumentUserPath") . $nomeImagem . "." . $extensao,
                        Configure::read("temporaryDocumentUserPath")
                    );

                    // Move o arquivo gerado
                    $fotoPerfil = $this->moveDocumentPermanently(
                        Configure::read("temporaryDocumentUserPath") . $nomeImagem . "." . $extensao,
                        Configure::read("documentUserPath"),
                        null,
                        $extensao
                    );

                    // Remove o array do item de gravação e passa a imagem
                    unset($data["foto"]);

                    $data["foto_perfil"] = $fotoPerfil;
                }

                // Remove os campos da atualizacao que não são permitidos fazer update

                if (isset($data["tipo_perfil"])) {
                    $data["tipo_perfil"] = $usuario["tipo_perfil"];
                }

                if (isset($data["senha"])) {
                    unset($data["senha"]);
                }

                if (isset($data["token_senha"])) {
                    unset($data["token_senha"]);
                }

                if (isset($data["data_expiracao_token"])) {
                    unset($data["data_expiracao_token"]);
                }

                if (isset($data["conta_ativa"])) {
                    unset($data["conta_ativa"]);
                }

                if (isset($data["conta_bloqueada"])) {
                    unset($data["conta_bloqueada"]);
                }

                if (isset($data["tentativas_login"])) {
                    unset($data["tentativas_login"]);
                }

                if (isset($data["ultima_tentativa_login"])) {
                    unset($data["ultima_tentativa_login"]);
                }

                // $senha = !empty($data["senha"]) ? $data["senha"] : null;
                // $confirmSenha = !empty($data["confirm_senha"]) ? $data["confirm_senha"] : null;

                // if (!empty($senha) && strlen($senha) < 6) {
                //     $errors[] = "Senha deve ter no mínimo 6 dígitos";
                // }

                // if (!empty($senha) && ($senha !== $confirmSenha)) {
                //     $errors[] = "Campos de Senha e Confirmação de Senha não conferem!";
                // }

                // if (count($errors) == 0) {
                // Faz o patch da entidade
                $usuario = $this->Usuarios->patchEntity($usuario, $data, ['validate' => 'EditUsuarioInfo']);

                $errors = $usuario->errors();
                // }


                // Gravação
                if (count($errors) == 0) {
                    $usuario = $this->Usuarios->save($usuario);
                } else {
                    $usuario = null;
                }

                // Atualização com sucesso, retorna mensagem
                if ($usuario) {
                    $status = true;
                    $message = Configure::read("messageSavedSuccess");
                } else {
                    // Atualização com erro, retorna mensagem de erro.
                    $status = false;
                    $message = __("{0} Confira as informações e tente novamente.", Configure::read("messageSavedError"));
                }
            }
            $mensagem = ['status' => $status, 'message' => $message, 'errors' => $errors];
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $messageString = __("Erro ao atualizar dados de usuário!");

            $errors = $trace;
            $mensagem = ['status' => false, 'message' => $messageString, 'errors' => $errors];

            $messageStringDebug = __("{0} - {1} em: {2}. [Função: {3} / Arquivo: {4} / Linha: {5}]  ", $messageString, $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write("error", $messageStringDebug);
        }

        $arraySet = [
            "mensagem",
            "usuario"
        ];

        $this->set(compact($arraySet));
        $this->set("_serialize", $arraySet);
    }

    /**
     * src\Controller\UsuariosController.php::validarAtualizacaoPerfilAPI
     *
     * Valida a atualização de perfil do usuário
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-09-18
     *
     * @return json_encode JSON
     */
    public function validarAtualizacaoPerfilAPI()
    {
        try {
            if ($this->request->is("GET")) {
                $usuario = $this->Auth->user();

                // Verifica se a data de atualização foi informada. Caso contrário, verifica pela data de inserção
                $ultimaAtualizacao = !empty($usuario["audit_update"]) ? $usuario["audit_update"] : $usuario["audit_insert"];
                $ultimaAtualizacao = date_create($ultimaAtualizacao);
                $ultimaAtualizacao = date_format($ultimaAtualizacao, "Y-m-d");

                $dataLimite = date("Y-m-d");

                $dataSubtracao = date_create($dataLimite);
                $dataSubtracao = date_sub($dataSubtracao, date_interval_create_from_date_string("30 days"));
                $dataSubtracao = date_format($dataSubtracao, "Y-m-d");

                $validacao = strtotime($ultimaAtualizacao) >= strtotime($dataSubtracao);
                $infoUltimaAtualizacao = array("ultima_atualizacao" => $ultimaAtualizacao);

                if ($validacao) {
                    ResponseUtil::successAPI(MSG_USUARIOS_PROFILE_ON_DATE, $infoUltimaAtualizacao);
                } else {
                    ResponseUtil::errorAPI(MESSAGE_GENERIC_ERROR, array(MSG_USUARIOS_PROFILE_OUT_DATE), $infoUltimaAtualizacao);
                }
            }
        } catch (\Exception $e) {
            $trace = $e->getTraceAsString();
            $messageString = __("Erro ao validar perfil de usuário!");

            $messageStringDebug = __("{0} - {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $messageString, $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write("error", $messageStringDebug);
            Log::write("error", $trace);
        }
    }

    #endregion

    #region Métodos Comuns

    /**
     * Verifica se usuario está travado e qual tipo
     *
     * @return object conteúdo informando se conta está bloqueada
     * @author
     */
    private function checkUsuarioIsLocked($usuario = null)
    {
        try {
            $message = '';

            /**
             * 0 = nenhuma
             * 1 = inativo
             * 2 = bloqueado
             * 3 = muitas tentativas
             */
            $statusUsuario = 0;

            if (is_null($usuario)) {
                $message = MSG_USUARIOS_LOGIN_PASSWORD_INCORRECT;
                $statusUsuario = 1;

                return array('message' => $message, 'actionNeeded' => $statusUsuario);
            }

            // verifica se é uma conta sem ser usuário.
            // se não for, verifica se a rede a qual ele se encontra está desativada

            if ($usuario['tipo_perfil'] >= PROFILE_TYPE_ADMIN_NETWORK && $usuario['tipo_perfil'] <= PROFILE_TYPE_USER) {
                // pega o vínculo do usuário com a rede

                $clienteHasUsuario = $this->ClientesHasUsuarios->findClienteHasUsuario(
                    array(
                        'ClientesHasUsuarios.usuarios_id' => $usuario['id']
                    )
                );
                $cliente = null;

                // ele pode retornar vários (Caso de Admin Regional, então, pegar o primeiro
                if ($usuario["tipo_perfil"] <= PROFILE_TYPE_WORKER) {
                    $cliente = $clienteHasUsuario["cliente"];

                    // verifica se a unidade está ativa. Se está, a rede também está
                    if (!$cliente["ativado"]) {
                        $message = __("A unidade/rede à qual esta conta está vinculada está desativada. O acesso não é permitido.");
                        $statusUsuario = 2;

                        return array('message' => $message, 'actionNeeded' => $statusUsuario);
                    }
                }

                if ($usuario['conta_ativa'] == 0) {
                    if ($usuario['tipo_perfil'] <= PROFILE_TYPE_USER) {
                        $message = __("A conta encontra-se desativada. Somente seu administrador poderá reativá-la.");
                        $statusUsuario = 2;
                    } else {
                        $message = __("A conta encontra-se desativada. Para reativar, será necessário confirmar alguns dados.");
                        $statusUsuario = 1;
                    }

                    return array('message' => $message, 'actionNeeded' => $statusUsuario);
                } elseif ($usuario['conta_bloqueada'] == true) {
                    $message = __("Sua conta encontra-se bloqueada no momento. Ela pode ter sido bloqueada por um administrador. Entre em contato com sua rede de atendimento.");
                    $statusUsuario = 2;

                    return array('message' => $message, 'actionNeeded' => $statusUsuario);
                } else {
                    $tentativasLogin = $usuario['tentativas_login'];
                    $ultimaTentativaLogin = $usuario['ultima_tentativa_login'];

                    if (!is_null($tentativasLogin) && !is_null($ultimaTentativaLogin)) {
                        $format = 'Y-m-d H:i:s';
                        $fromTime = strtotime($ultimaTentativaLogin->format($format));
                        $toTime = strtotime(date($format));

                        $diff = round(abs($fromTime - $toTime) / 60, 0);

                        if ($tentativasLogin >= 15 && ($diff < 10)) {
                            $message = __('Você já tentou realizar 5 tentativas, é necessário aguardar mais {0} minutos antes da próxima tentativa!', (10 - (int) $diff));

                            $statusUsuario = 3;
                            return array('message' => $message, 'actionNeeded' => $statusUsuario, "status" => $statusUsuario);
                        }
                    }
                }
            }

            return array('message' => $message, 'actionNeeded' => $statusUsuario);
        } catch (\Exception $e) {
            $stringError = __("Erro ao buscar registro: " . $e->getMessage());

            Log::write('error', $stringError);
            Log::write('error', $e->getTraceAsString());
        }
    }

    /**
     * Executa processo de validação de Usuário
     *
     * @param string $email Email do usuário
     * @param string $senha Senha informada
     * @param string $tipoLogin Se Login WEB ou API
     * @param integer $redesIdPost Id da Rede
     * @param string $cnpj CNPJ do Estabelecimento (Se funcionário automático)
     *
     * @return void
     */
    private function checkLoginUser(string $email, string $senha, string $tipoLogin, int $redesIdPost = null, string $cnpj = null)
    {
        $credenciais = array("email" => $email, "senha" => $senha);

        // Obtem o usuário para gravar a falha de login ou reset das tentativas
        $usuarioEmail = $this->Usuarios->getUsuarioByEmail($email);
        $usuarioCPF = $this->Usuarios->getUsuarioByCPF($email);

        $usuario = !empty($usuarioEmail) ? $usuarioEmail : $usuarioCPF;
        $result = $this->checkUsuarioIsLocked($usuario);
        $cliente = null;
        $errors = [];
        $errorCodes = [];

        if ($result['actionNeeded'] == 0) {
            $user = $this->Auth->identify();
            // $errorDebug = $this->Auth->errors();

            if ($user) {
                $user = new Usuario($user);

                // Só autentica JWT se logou
                $user["token"] = JWT::encode(
                    array(
                        'id' => $user['id'],
                        'sub' => $user['id'],
                        'email' => $usuario->email,
                        'exp' => time() + TIME_EXPIRATION_TOKEN_SECONDS
                    ),
                    Security::getSalt()
                );
            }

            if ($user) {

                // Usuário logou, verifica se o mesmo é funcionário e de rede que tem APP_PERSONALIZADO
                if ($usuario->tipo_perfil >= PROFILE_TYPE_ADMIN_NETWORK && $usuario->tipo_perfil <= PROFILE_TYPE_WORKER) {

                    $postoFuncionario = $this->ClientesHasUsuarios->getVinculoClientesUsuario($user["id"], true);
                    $cliente = null;
                    $rede = null;

                    // DebugUtil::printArray($postoFuncionario->cliente);
                    $this->request->session()->delete('Rede.PontoAtendimento');
                    $this->request->session()->delete('Rede.Grupo');

                    if (!empty($postoFuncionario)) {
                        $cliente = $postoFuncionario->cliente;
                        // verifica qual rede o usuário se encontra
                        $redeHasCliente = $this->RedesHasClientes->getRedesHasClientesByClientesId($cliente["id"]);
                        $rede = $redeHasCliente->rede;

                        // Log::write("info", "cliente");
                        // Log::write("info", $cliente);
                        // Mas se for local ou gerente ou funcionário, é a que ele tem acesso mesmo.
                        $this->request->session()->write('Rede.PontoAtendimento', $cliente);
                        $this->request->session()->write('Rede.Grupo', $rede);

                        if ($tipoLogin == LOGIN_API) {
                            $message = null;
                            $errors = [];
                            $errorCodes = [];

                            if (!empty($redesIdPost) && $rede->id != $redesIdPost) {
                                $message = MSG_USUARIOS_WORKER_BELONGS_ANOTHER_APP;
                                $errors[] = MSG_USUARIOS_WORKER_BELONGS_ANOTHER_APP;
                                $errorCodes[] = MSG_USUARIOS_WORKER_BELONGS_ANOTHER_APP_CODE;
                            } elseif ((empty($redesIdPost) && $rede->app_personalizado)) {
                                $message = MSG_USUARIOS_WORKER_BELONGS_CUSTOM_APP;
                                $errors[] = MSG_USUARIOS_WORKER_BELONGS_CUSTOM_APP;
                                $errorCodes[] = MSG_USUARIOS_WORKER_BELONGS_CUSTOM_APP_CODE;
                            } elseif (!empty($redesIdPost) && !$rede->app_personalizado) {
                                $message = MSG_USUARIOS_WORKER_BELONGS_GENERIC_APP;
                                $errors[] = MSG_USUARIOS_WORKER_BELONGS_GENERIC_APP;
                                $errorCodes[] = MSG_USUARIOS_WORKER_BELONGS_GENERIC_APP_CODE;
                            }

                            if (!empty($message)) {
                                return array(
                                    "usuario" => null,
                                    "status" => false,
                                    "message" => $message,
                                    "errors" => $errors,
                                    "errorCodes" => $errorCodes,
                                    "recoverAccount" => null
                                );
                            }
                        }

                        $this->request->session()->write("Usuario.UsuarioLogado", $user);
                    }
                } else if ($user->tipo_perfil === PROFILE_TYPE_DUMMY_WORKER) {

                    // if (empty($cnpj)) {
                    //     $error[] = "Funcionário automático, necessário especificar CNPJ de estabelecimento!";
                    //     $errorCodes[] = 0;
                    //     return array(
                    //         "usuario" => null,
                    //         "status" => false,
                    //         "message" => "Houve um erro!",
                    //         "errors" => $error,
                    //         "errorCodes" => $errorCodes,
                    //         "recoverAccount" => null,
                    //         "cliente" => null
                    //     );
                    // }

                    if (!empty($cnpj)) {
                        $cliente = $this->Clientes->getClienteByCNPJ($cnpj);
                    }
                    // $this->request->session()->delete('Rede.PontoAtendimento');
                    // $this->request->session()->delete('Rede.Grupo');
                }


                $this->Auth->setUser($user);

                // Reset de tentativas de login
                $usuario->tentatias_login = 0;
                $usuario->ultima_tentativa_login = null;
                $this->Usuarios->save($usuario);

                // Grava token gerado
                $this->UsuariosTokens->setToken($user["id"], $tipoLogin, $user["token"]);

                return array(
                    "usuario" => $user,
                    "status" => true,
                    "message" => "",
                    "recoverAccount" => !empty($recoverAccount) ? $recoverAccount : null,
                    "cliente" => $cliente
                );
            } elseif (!empty($usuario)) {
                // se não logou
                if (is_null($usuario->ultima_tentativa_login)) {
                    $usuario->ultima_tentativa_login = new \DateTime('now');
                }

                $fromTime = strtotime($usuario->ultima_tentativa_login->format('Y-m-d H:i:s'));
                $toTime = strtotime(date('Y-m-d H:i:s'));
                $diff = round(abs($fromTime - $toTime) / 60, 0);

                if ($usuario->tentativas_login >= 5 && ($diff < 10)) {
                    $message = __('Você já tentou realizar 5 tentativas de autenticação, é necessário aguardar mais {0} minutos antes da próxima tentativa!', (10 - (int) $diff));
                } else {
                    // Grava falha de tentativa de login
                    if ($usuario->tentativas_login >= 5) {
                        $usuario->tentativas_login = 0;
                    } else {
                        $usuario->ultima_tentativa_login = new DateTime("now");
                    }

                    $usuario->tentativas_login = $usuario->tentativas_login + 1;
                    $this->Usuarios->save($usuario);
                }

                $status = false;
                $message = MSG_USUARIOS_LOGIN_PASSWORD_INCORRECT;
                $errors = [MSG_USUARIOS_LOGIN_PASSWORD_INCORRECT];
                $errorCodes = [MSG_USUARIOS_LOGIN_PASSWORD_INCORRECT_CODE];
                $usuario = null;
            }
        } else {
            $message = $result['message'];
            $recoverAccount = $result['actionNeeded'];
            $status = isset($result["status"]) ? $result["status"] : true;
            $usuario = null;
        }

        /**
         * 0 = nenhuma
         * 1 = inativo
         * 2 = bloqueado
         * 3 = muitas tentativas
         */

        return array(
            "usuario" => $usuario,
            "status" => $status,
            "message" => $message,
            "recoverAccount" => !empty($recoverAccount) ? $recoverAccount : null,
            "errors" => $errors,
            "errorCodes" => $errorCodes,
            "cliente" => $cliente

        );
    }

    /**
     * Helper que envia e-mail ao usuário para resetar a senha
     *
     * @param string $url     Url de envio do link
     * @param object $usuario Entidade Usuário
     *
     * @return \Cake\Http\Response|void
     */
    private function _sendResetEmail($url, $usuario)
    {
        $email = new Email();
        $email->template('resetpw');
        $email->emailFormat('both');
        $email->to($usuario->email, $usuario->nome);
        $email->subject('Reset your password');
        $email->viewVars(['url' => $url, 'username' => $usuario->nome]);
        if ($email->send()) {
            $this->Flash->success(__('Verifique seu email pela requisição de reset de senha'));
        } else {
            $this->Flash->error(__('Erro ao enviar email :') . $email->smtpError);
        }
    }

    /**
     * BeforeRender callback
     *
     * @param Event $event
     *
     * @return \Cake\Http\Response|void
     */
    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);

        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        }
    }

    /**
     * Before render callback.
     *
     * @param \App\Controller\Event\Event $event The beforeRender event.
     *
     * @return \Cake\Network\Response|null|void
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
    }

    /**
     * Initialize function
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');

        // Permitir aos usuários se registrarem e efetuar login e logout.

        $this->Auth->allow(
            array(
                "registrar",
                "registrarAPI",
                "esqueciMinhaSenhaAPI",
                "loginAPI",
                "login",
                "logout",
                "esqueciMinhaSenha",
                "reativarConta",
                "resetarMinhaSenha",
                "getUsuarioByCPF",
                "getUsuarioByEmail",
                "uploadDocumentTemporaly",
                "testAPI",
                "getUsuarioByDocEstrangeiroAPI"
            )
        );
    }

    #endregion

    /**
     * ------------------------------------------------------------
     * Métodos comuns para usuários gerenciadores
     * ------------------------------------------------------------
     */

    /**
     * Action para habilitar usuário
     *
     * @return \Cake\Http\Response|void
     */
    public function habilitarUsuario()
    {
        $query = $this->request->query;

        $result = $this->_alteraContaAtivaUsuario((int) $query['usuarios_id'], true);

        if ($result) {
            $this->Flash->success(Configure::read('messageEnableSuccess'));

            return $this->redirect($query['return_url']);
        }
    }

    /**
     * Action para desabilitar usuário
     *
     * @return \Cake\Http\Response|void
     */
    public function desabilitarUsuario()
    {
        $query = $this->request->query;

        $result = $this->_alteraContaAtivaUsuario((int) $query['usuarios_id'], false);

        if ($result) {
            $this->Flash->success(Configure::read('messageDisableSuccess'));

            return $this->redirect($query['return_url']);
        }
    }

    /**
     * Altera estado de conta ativa de usuário
     *
     * @param int  $usuarios_id Id de usuário
     * @param bool $status      Estado da conta
     *
     * @return \Cake\Http\Response|void
     */
    private function _alteraContaAtivaUsuario(int $usuarios_id, bool $status)
    {
        return $this->Usuarios->changeAccountEnabledByUsuarioId($usuarios_id, $status);
    }

    /**
     * ------------------------------------------------------------
     * Métodos para Funcionários (Dashboard de Funcionário)
     * ------------------------------------------------------------
     */

    /**
     * Abre action para pesquisa de cliente (para abrir tela de alteração de cadastro,
     * dados de veículos e transportadoras)
     *
     * @return void
     */
    public function pesquisarClienteAlterarDados()
    {
        $sessaoUsuario = $this->getSessionUserVariables();

        $usuarioAdministrador = $sessaoUsuario["usuarioAdministrador"];
        $usuarioAdministrar = $sessaoUsuario["usuarioAdministrar"];
        $rede = $sessaoUsuario["rede"];
        $cliente = $sessaoUsuario["cliente"];

        if ($usuarioAdministrador) {
            $this->usuarioLogado = $usuarioAdministrar;
            $usuarioLogado = $usuarioAdministrar;
        }

        $arraySet = array("usuarioLogado");

        $this->set(compact($arraySet));
        $this->set($arraySet);
    }

    /**
     * Método para editar dados de usuário (modo de administrador)
     *
     * @param string|null $id Usuario id.
     *
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     *
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function editarCadastroUsuarioFinal($id = null)
    {
        try {

            $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
            $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

            if ($usuarioAdministrador) {
                $this->usuarioLogado = $usuarioAdministrar;
            }

            $rede = $this->request->session()->read('Rede.Grupo');

            $usuario = $this->Usuarios->get(
                $id,
                [
                    'contain' => []
                ]
            );

            if ($this->request->is(['post', 'put'])) {
                $usuario = $this->Usuarios->patchEntity($usuario, $this->request->getData(), ['validate' => 'EditUsuarioInfo']);

                $errors = $usuario->errors();

                $usuario = $this->Usuarios->save($usuario);
                if ($usuario) {
                    $this->Flash->success(__(Configure::read('messageSavedSuccess')));

                    $url = Router::url(['controller' => 'Pages', 'action' => 'display']);
                    return $this->response = $this->response->withLocation($url);
                }
                $this->Flash->error(__(Configure::read('messageSavedError')));

                // exibe os erros logo acima identificados
                foreach ($errors as $key => $error) {
                    $key = key($error);
                    $this->Flash->error(__("{0}", $error[$key]));
                }
            }

            $usuarioLogadoTipoPerfil = (int) Configure::read('profileTypes')['UserProfileType'];

            $this->set(compact(['usuario', 'usuarioLogadoTipoPerfil']));
            $this->set('_serialize', ['usuario', 'usuarioLogadoTipoPerfil']);
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao editar dados de usuário: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * ------------------------------------------------------------
     * Métodos comuns para todos os usuários
     * ------------------------------------------------------------
     */

    public function relatorios()
    {
        # code...
    }

    /**
     * ------------------------------------------------------------
     * Relatórios (Dashboard de Admin RTI)
     * ------------------------------------------------------------
     */

    /**
     * Relatório de Equipe de cada Rede
     *
     * @return \Cake\Http\Response|void
     */
    public function relatorioEquipeRedes()
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

            if (strlen($data['tipo_perfil']) > 0) {
                $whereConditions[] = ["tipo_perfil " => $data['tipo_perfil']];
            }

            if (strlen($data['sexo']) > 0) {
                $whereConditions[] = ['sexo' => (bool) $data['sexo']];
            }

            if (strlen($data['conta_ativa']) > 0) {
                $whereConditions[] = ['conta_ativa' => (bool) $data['conta_ativa']];
            }

            if (strlen($data['conta_bloqueada']) > 0) {
                $whereConditions[] = ['conta_bloqueada' => (bool) $data['conta_bloqueada']];
            }

            $dataHoje = DateTimeUtil::convertDateToUTC((new DateTime('now'))->format('Y-m-d H:i:s'));
            $dataInicial = strlen($data['auditInsertInicio']) > 0 ? DateTimeUtil::convertDateToUTC($data['auditInsertInicio'], 'd/m/Y') : null;
            $dataFinal = strlen($data['auditInsertFim']) > 0 ? DateTimeUtil::convertDateToUTC($data['auditInsertFim'], 'd/m/Y') : null;

            // Data de Criação Início e Fim
            if (strlen($data['auditInsertInicio']) > 0 && strlen($data['auditInsertFim']) > 0) {

                if ($dataInicial > $dataFinal) {
                    $this->Flash->error(__(Configure::read('messageDateRangeInvalid')));
                } elseif ($dataInicial > $dataHoje) {
                    $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid', 'Data de Início')));
                } else {
                    $whereConditions[] = ['usuarios.audit_insert BETWEEN "' . $dataInicial . '" and "' . $dataFinal . '"'];
                }
            } elseif (strlen($data['auditInsertInicio']) > 0) {

                if ($dataInicial > $dataHoje) {
                    $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Início'));
                } else {
                    $whereConditions[] = ['usuarios.audit_insert >= ' => $dataInicial];
                }
            } elseif (strlen($data['auditInsertFim']) > 0) {

                if ($dataFinal > $dataHoje) {
                    $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Fim'));
                } else {
                    $whereConditions[] = ['usuarios.audit_insert <= ' => $dataFinal];
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
            $redeItem['usuarios'] = array();

            $unidades_ids = [];

            // obtem os ids das unidades para saber quais brindes estão disponíveis
            foreach ($rede->redes_has_clientes as $key => $value) {
                $unidades_ids[] = $value->clientes_id;
            }

            // TODO: Se for usar mesmo serviço, será necessário criar novos campos de assinatura

            $usuarios = [];

            if (count($unidades_ids) > 0) {
                $usuarios = $this->Usuarios->findFuncionariosRede(
                    $rede->id,
                    $unidades_ids
                );
                $usuarios = $usuarios->toArray();
            }

            $redeItem['usuarios'] = $usuarios;

            unset($arrayWhereConditions);

            $redes[] = $redeItem;
        }

        // DebugUtil::printArray($redes);

        $arraySet = [
            'redesList',
            'redes'
        ];

        $this->set(compact($arraySet));
    }

    /**
     * Relatório de Usuários Cadastrados
     *
     * @return \Cake\Http\Response|void
     */
    public function relatorioUsuariosCadastrados()
    {
        $whereConditions = array();

        $dataInicial = date('d/m/Y', strtotime('-30 days'));
        $dataFinal = date('d/m/Y');

        $whereConditions[] = ["tipo_perfil " => Configure::read('profileTypes')['UserProfileType']];

        if ($this->request->is(['post'])) {
            $data = $this->request->getData();

            if ($data['opcoes'] == 'cpf') {
                $value = $this->cleanNumber($data['parametro']);
            } else {
                $value = $data['parametro'];
            }

            $whereConditions[] = [
                "usuarios." . $data['opcoes'] . ' like' => '%' . $value . '%'
            ];

            if (strlen($data['sexo']) > 0) {
                $whereConditions[] = ['usuarios.sexo' => (bool) $data['sexo']];
            }

            if (strlen($data['conta_ativa']) > 0) {
                $whereConditions[] = ['usuarios.conta_ativa' => (bool) $data['conta_ativa']];
            }

            if (strlen($data['conta_bloqueada']) > 0) {
                $whereConditions[] = ['usuarios.conta_bloqueada' => (bool) $data['conta_bloqueada']];
            }

            $dataHoje = DateTimeUtil::convertDateToUTC((new DateTime('now'))->format('Y-m-d H:i:s'));

            // Data de Nascimento Inicio e Fim

            $dataInicialNascimento = strlen($data['dataNascimentoInicio']) > 0 ? DateTimeUtil::convertDateToUTC($data['dataNascimentoInicio'], 'd/m/Y') : null;
            $dataFinalNascimento = strlen($data['dataNascimentoFim']) > 0 ? DateTimeUtil::convertDateToUTC($data['dataNascimentoFim'], 'd/m/Y') : null;

            if (strlen($data['dataNascimentoInicio']) > 0 && strlen($data['dataNascimentoFim']) > 0) {

                if ($dataInicialNascimento > $dataFinalNascimento) {
                    $this->Flash->error(__(Configure::read('messageDateRangeInvalid')));
                } elseif ($dataInicial > $dataHoje) {
                    $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid', 'Data de Início')));
                } else {
                    $whereConditions[] = ['usuarios.data_nasc BETWEEN "' . $dataInicialNascimento . '" and "' . $dataFinalNascimento . '"'];
                }
            } elseif (strlen($data['dataNascimentoInicio']) > 0) {

                if ($dataInicialNascimento > $dataHoje) {
                    $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Início'));
                } else {
                    $whereConditions[] = ['usuarios.data_nasc >= ' => $dataInicialNascimento];
                }
            } elseif (strlen($data['dataNascimentoFim']) > 0) {

                if ($dataFinalNascimento > $dataHoje) {
                    $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Fim'));
                } else {
                    $whereConditions[] = ['usuarios.data_nasc <= ' => $dataFinalNascimento];
                }
            }
            // Data de Criação Início e Fim

            $dataInicialInsercao = strlen($data['auditInsertInicio']) > 0 ? DateTimeUtil::convertDateToUTC($data['auditInsertInicio'], 'd/m/Y') : null;
            $dataFinalInsercao = strlen($data['auditInsertFim']) > 0 ? DateTimeUtil::convertDateToUTC($data['auditInsertFim'], 'd/m/Y') : null;

            if (strlen($data['auditInsertInicio']) > 0 && strlen($data['auditInsertFim']) > 0) {

                if ($dataInicialInsercao > $dataFinalInsercao) {
                    $this->Flash->error(__(Configure::read('messageDateRangeInvalid')));
                } elseif ($dataInicialInsercao > $dataHoje) {
                    $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid', 'Data de Início')));
                } else {
                    $whereConditions[] = ['usuarios.audit_insert BETWEEN "' . $dataInicialInsercao . '" and "' . $dataFinalInsercao . '"'];
                }
            } elseif (strlen($data['auditInsertInicio']) > 0) {

                if ($dataInicialInsercao > $dataHoje) {
                    $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Início'));
                } else {
                    $whereConditions[] = ['usuarios.audit_insert >= ' => $dataInicialInsercao];
                }
            } elseif (strlen($data['auditInsertFim']) > 0) {

                if ($dataFinalInsercao > $dataHoje) {
                    $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Fim'));
                } else {
                    $whereConditions[] = ['usuarios.audit_insert <= ' => $dataFinalInsercao];
                }
            }
        }

        // @todo Conferir se este relatório está sendo usado
        // $usuarios = $this->Usuarios->findAllUsuarios($whereConditions);

        $arraySet = [
            'dataInicial',
            'dataFinal',
            'usuarios',
        ];

        $this->set(compact($arraySet));
    }

    /**
     * Relatório de Usuários Cadastrados pelos funcionários
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-09-25
     *
     * @return \Cake\Http\Response|void
     */
    public function relatorioUsuariosCadastradosFuncionarios()
    {
        $sessao = $this->getSessionUserVariables();
        $usuarioLogado = $sessao["usuarioLogado"];
        $cliente = $sessao["cliente"];

        $clientesId = !empty($cliente) ? $cliente->id : 0;

        $arraySet = ["clientesId"];

        $this->set(compact($arraySet));
    }

    /**
     * Relatório de Usuários Cadastrados por Rede
     *
     * @return \Cake\Http\Response|void
     */
    public function relatorioUsuariosRedes()
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

            $whereConditions[] = ["tipo_perfil" => Configure::read('profileTypes')['UserProfileType']];

            if ($this->request->is(['post'])) {
                $data = $this->request->getData();

                if (strlen($data['redes_id']) == 0) {
                    $this->Flash->error('É necessário selecionar uma rede para filtrar!');

                    return $this->redirect([
                        'controller' => 'Usuarios',
                        'action' => 'relatorioUsuariosRedes'
                    ]);
                }

                if (strlen($data['redes_id']) > 0) {
                    $redesArrayIds = ['id' => $data['redes_id']];
                }

                if (strlen($data['nome']) > 0) {
                    $whereConditions[] = ["nome like '%" . $data['nome'] . "%'"];
                }

                if (strlen($data['sexo']) > 0) {
                    $whereConditions[] = ['sexo' => (bool) $data['sexo']];
                }

                if (strlen($data['conta_ativa']) > 0) {
                    $whereConditions[] = ['conta_ativa' => (bool) $data['conta_ativa']];
                }

                if (strlen($data['conta_bloqueada']) > 0) {
                    $whereConditions[] = ['conta_bloqueada' => (bool) $data['conta_bloqueada']];
                }

                $qteRegistros = (int) $data['qte_registros'];

                $dataHoje = DateTimeUtil::convertDateToUTC((new DateTime('now'))->format('Y-m-d H:i:s'));
                $dataInicial = strlen($data['auditInsertInicio']) > 0 ? DateTimeUtil::convertDateToUTC($data['auditInsertInicio'], 'd/m/Y') : null;
                $dataFinal = strlen($data['auditInsertFim']) > 0 ? DateTimeUtil::convertDateToUTC($data['auditInsertFim'], 'd/m/Y') : null;

                // Data de Nascimento Inicio e Fim

                $dataInicialNascimento = strlen($data['dataNascimentoInicio']) > 0 ? DateTimeUtil::convertDateToUTC($data['dataNascimentoInicio'], 'd/m/Y') : null;
                $dataFinalNascimento = strlen($data['dataNascimentoFim']) > 0 ? DateTimeUtil::convertDateToUTC($data['dataNascimentoFim'], 'd/m/Y') : null;

                if (strlen($data['dataNascimentoInicio']) > 0 && strlen($data['dataNascimentoFim']) > 0) {

                    if ($dataInicialNascimento > $dataFinalNascimento) {
                        $this->Flash->error(__(Configure::read('messageDateRangeInvalid')));
                    } elseif ($dataInicial > $dataHoje) {
                        $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid', 'Data de Início')));
                    } else {
                        $whereConditions[] = ['usuarios.data_nasc BETWEEN "' . $dataInicialNascimento . '" and "' . $dataFinalNascimento . '"'];
                    }
                } elseif (strlen($data['dataNascimentoInicio']) > 0) {

                    if ($dataInicialNascimento > $dataHoje) {
                        $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Início'));
                    } else {
                        $whereConditions[] = ['usuarios.data_nasc >= ' => $dataInicialNascimento];
                    }
                } elseif (strlen($data['dataNascimentoFim']) > 0) {

                    if ($dataFinalNascimento > $dataHoje) {
                        $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Fim'));
                    } else {
                        $whereConditions[] = ['usuarios.data_nasc <= ' => $dataFinalNascimento];
                    }
                }
                // Data de Criação Início e Fim

                $dataInicialInsercao = strlen($data['auditInsertInicio']) > 0 ? DateTimeUtil::convertDateToUTC($data['auditInsertInicio'], 'd/m/Y') : null;
                $dataFinalInsercao = strlen($data['auditInsertFim']) > 0 ? DateTimeUtil::convertDateToUTC($data['auditInsertFim'], 'd/m/Y') : null;

                if (strlen($data['auditInsertInicio']) > 0 && strlen($data['auditInsertFim']) > 0) {

                    if ($dataInicialInsercao > $dataFinalInsercao) {
                        $this->Flash->error(__(Configure::read('messageDateRangeInvalid')));
                    } elseif ($dataInicial > $dataHoje) {
                        $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid', 'Data de Início')));
                    } else {
                        $whereConditions[] = ['usuarios.audit_insert BETWEEN "' . $dataInicialInsercao . '" and "' . $dataFinalInsercao . '"'];
                    }
                } elseif (strlen($data['auditInsertInicio']) > 0) {

                    if ($dataInicialInsercao > $dataHoje) {
                        $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Início'));
                    } else {
                        $whereConditions[] = ['usuarios.audit_insert >= ' => $dataInicialInsercao];
                    }
                } elseif (strlen($data['auditInsertFim']) > 0) {

                    if ($dataFinalInsercao > $dataHoje) {
                        $this->Flash->error(__(Configure::read('messageDateTodayHigherInvalid'), 'Data de Fim'));
                    } else {
                        $whereConditions[] = ['usuarios.audit_insert <= ' => $dataFinalInsercao];
                    }
                }

                // Monta o Array para apresentar em tela

                foreach ($redesArrayIds as $key => $value) {
                    $usuariosConditions = $whereConditions;

                    $redesHasClientesIds = array();

                    $usuariosIds = array();

                    $rede = $this->Redes->getRedeById((int) $value);

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

                    $usuarios = $this->Usuarios->findAllUsuariosByRede($rede->id, $usuariosConditions);

                    if ($qteRegistros > 0) {
                        $redeItem['usuarios'] = $usuarios->limit($qteRegistros);
                    } else {
                        $redeItem['usuarios'] = $usuarios;
                    }

                    unset($arrayWhereConditions);

                    array_push($redes, $redeItem);
                }
            }

            $arraySet = [
                'redesList',
                'redes'
            ];

            $this->set(compact($arraySet));
        } catch (\Exception $e) {
            $trace = $e->getTraceAsString();

            $stringError = __("Erro ao exibir relatório de usuário: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write("error", $trace);

            $this->Flash->error($stringError);
        }
    }

    /**
     * ------------------------------------------------------------
     * REST Methods
     * ------------------------------------------------------------
     */

    /**
     * Consulta Usuários assíduos
     *
     * @param array $data Dados de Post
     * @param integer $redesId
     * @return void
     */
    private function _consultaUsuariosAssiduos(array $data = array(), int $redesId = null, float $mediaAssiduidadeClientes = null)
    {
        if (!empty($data["redesId"]) && $data["redesId"] > 0) {
            $redesId = (int) $data["redesId"];
        }

        $clientesIds = !empty($data["clientesIds"]) ? $data["clientesIds"] : null;
        $usuariosId = !empty($data["usuariosId"]) ? $data["usuariosId"] : null;
        $nome = !empty($data["nome"]) ? $data["nome"] : null;
        $cpf = !empty($data["cpf"]) ? $data["cpf"] : null;
        $placa = !empty($data["placa"]) ? $data["placa"] : null;
        $documentoEstrangeiro = !empty($data["documentoEstrangeiro"]) ? $data["documentoEstrangeiro"] : null;
        $status = isset($data["status"]) && strlen($data["status"]) > 0 ? $data["status"] : null;
        $assiduidade = isset($data["assiduidade"]) && strlen($data["assiduidade"]) > 0 ? $data["assiduidade"] : null;
        $agrupamento = !empty($data["agrupamento"]) ? $data["agrupamento"] : null;
        $dataInicio = !empty($data["dataInicio"]) ? $data["dataInicio"] : null;
        $dataFim = !empty($data["dataFim"]) ? $data["dataFim"] : null;


        // ResponseUtil::success($agrupamento);
        if (gettype($clientesIds) == "integer") {
            $clientesIds = array($clientesIds);
        }

        if (is_null($clientesIds) && sizeof($clientesIds) == 0) {
            $clientesIds = $this->RedesHasClientes->getClientesIdsFromRedesHasClientes($redesId);
        }

        return $this->Usuarios->getUsuariosAssiduosClientes(
            $redesId,
            $clientesIds,
            $usuariosId,
            $nome,
            $cpf,
            $placa,
            $documentoEstrangeiro,
            $status,
            $assiduidade,
            $mediaAssiduidadeClientes,
            $agrupamento,
            $dataInicio,
            $dataFim
        );
    }

    /**
     * UsuariosController::_consultaUsuariosFidelizados
     *
     * Consulta Usuários Fidelizados conforme requisição
     *
     * @param array $data $Array de Post
     * @param int $redesId Id da Rede
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 12/09/2018
     *
     * @return array $dados
     */
    private function _consultaUsuariosFidelizados(array $data, int $redesId)
    {
        if (!empty($data["redesId"]) && $data["redesId"] > 0) {
            $redesId = (int) $data["redesId"];
        }

        $clientesIds = !empty($data["clientesIds"]) ? $data["clientesIds"] : null;
        $nome = !empty($data["nome"]) ? $data["nome"] : null;
        $cpf = !empty($data["cpf"]) ? $data["cpf"] : null;
        $veiculo = !empty($data["veiculo"]) ? $data["veiculo"] : null;
        $documentoEstrangeiro = !empty($data["documentoEstrangeiro"]) ? $data["documentoEstrangeiro"] : null;
        $status = isset($data["status"]) && strlen($data["status"]) > 0 ? $data["status"] : null;
        $dataInicio = !empty($data["dataInicio"]) ? $data["dataInicio"] : null;
        $dataFim = !empty($data["dataFim"]) ? $data["dataFim"] : null;

        if (gettype($clientesIds) == "integer") {
            $clientesIds = array($clientesIds);
        }

        if (!is_null($clientesIds) && sizeof($clientesIds) == 0) {
            $clientesIds = $this->RedesHasClientes->getClientesIdsFromRedesHasClientes($redesId);
        }

        return $this->ClientesHasUsuarios->getUsuariosFidelizadosClientes(
            $clientesIds,
            $nome,
            $cpf,
            $veiculo,
            $documentoEstrangeiro,
            $status,
            $dataInicio,
            $dataFim
        );
    }


    // /**
    //  * Função apenas para teste de acesso e benchmark
    //  *
    //  * @return void
    //  */
    // public function testAPI()
    // {
    //     $value = $this->Cupons->find()->toArray();
    //     // $value = bin2hex(openssl_random_pseudo_bytes(10));

    //     $arraySet = array("value");

    //     $this->set(compact($arraySet));
    //     $this->set("_serialize", $arraySet);
    // }



    #region  Ajax Methods

    /**
     * Envia documento do cliente para autorização posterior
     *
     * @return \Cake\Http\Response|void
     **/
    public function uploadDocumentTemporary()
    {
        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();

            ImageUtil::generateImageFromBase64(
                $data['image'],
                Configure::read('temporaryDocumentUserPath') . $data['imageName'] . '.jpg',
                Configure::read('temporaryDocumentUserPath')
            );

            $img = $data;

            $arraySet = ['img'];

            $this->set(compact($arraySet));
            $this->set("_serialize", $arraySet);
        }
    }

    /**
     * Encontra usuário pelo cpf
     *
     * @return entity\usuario $user
     *
     **/
    public function getUsuarioByCPF()
    {
        try {
            if ($this->request->is(['post', 'put', 'ajax'])) {
                $data = $this->request->getData();

                // DebugUtil::printArray($data);

                $usuariosId = empty($data["id"]) ? null : $data["id"];
                $cpf = empty($data["cpf"]) ? null : $data["cpf"];

                if (empty($cpf)) {
                    return ResponseUtil::errorAPI(MSG_USUARIOS_CPF_EMPTY, array());
                }

                $result = NumberUtil::validarCPF($data["cpf"]);

                if ($result["status"] == 0) {
                    return ResponseUtil::errorAPI($result["message"], array());
                }

                $user = $this->Usuarios->getUsuarioByCPF($data['cpf']);

                if (!empty($user) && !$user->conta_ativa) {
                    $user = null;
                }

                // Se id informado, verifico se o usuário que está informado é o mesmo da consulta.
                // Se for, não pode retornar registro, pois se retornar, significa que há outro usuário com este id
                if (!empty($usuariosId)) {

                    if ($user !== null && $user->id == $data['id'] || !$user->conta_ativa) {
                        $user = null;
                    }
                }

                if (!empty($user)) {
                    $user['data_nasc'] = !empty($user) && !empty($user['data_nasc']) ? $user["data_nasc"]->format('d/m/Y') : null;
                }
            }
            $return = array("user" => $user);
            return ResponseUtil::successAPI(MSG_LOAD_DATA_WITH_SUCCESS, $return);
            // $arraySet = ['user', "mensagem"];

            $this->set(compact($arraySet));
            $this->set("_serialize", $arraySet);
        } catch (\Exception $e) {
            Log::write('debug', $e->getMessage());
        }
    }

    /**
     * Encontra usuario pelo e-mail
     *
     * @return entity\usuario $user
     * @author Gustavo Souza Gonçalves
     **/
    public function getUsuarioByEmail()
    {
        try {
            if ($this->request->is(['post', 'put'])) {
                $data = $this->request->getData();
                // ResponseUtil::successAPI($data);

                $id = !empty($data["id"]) ? $data["id"] : null;
                $email = !empty($data["email"]) ? $data["email"] : null;
                // Validação de email com base no perfil que está sendo criado
                // Se não informou o campo, considera que é usuário final (pois está vindo do Mobile, e o Mobile não informa isto)
                $tipoPerfil = !empty($data["tipo_perfil"]) ? $data["tipo_perfil"] : PROFILE_TYPE_USER;
                $restricaoCampos = !empty($data["restricao_campos"]) ? $data["restricao_campos"] : false;

                if (empty($email)) {
                    return ResponseUtil::errorAPI(MESSAGE_GENERIC_ERROR, [MSG_USUARIOS_EMAIL_EMPTY], [], []);
                }

                $user = $this->Usuarios->getUsuarioByEmail($data['email']);

                if (!empty($user)) {
                    if (!in_array(
                        $user->tipo_perfil,
                        [
                            PROFILE_TYPE_ADMIN_NETWORK,
                            PROFILE_TYPE_ADMIN_REGIONAL,
                            PROFILE_TYPE_ADMIN_LOCAL,
                            PROFILE_TYPE_MANAGER,
                            PROFILE_TYPE_WORKER
                        ]
                    )) {

                        if (in_array($tipoPerfil, [PROFILE_TYPE_ADMIN_DEVELOPER, PROFILE_TYPE_USER, PROFILE_TYPE_DUMMY_USER])) {
                            $validacaoEmail = EmailUtil::validateEmail($email);

                            if (!$validacaoEmail["status"]) {
                                return ResponseUtil::errorAPI(MESSAGE_GENERIC_ERROR, array($validacaoEmail["message"]), [], [$validacaoEmail["code"]]);
                            }
                        }
                    }
                }


                if ($data['id'] != 0 && !empty($user)) {
                    // Se na busca retornou o mesmo id de usuário ou a conta não está ativa, consta como se não existisse
                    if ($user->id == $data['id'] || !$user->conta_ativa) {
                        $user = null;
                    }
                }

                $arraySet = ['user'];

                $this->set(compact($arraySet));
                $this->set("_serialize", $arraySet);
            }
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao obter usuário por e-mail: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);

            $arraySet = ['stringError'];

            $this->set(compact($arraySet));
            $this->set("_serialize", $arraySet);
        }
    }

    /**
     * Encontra usuário por parâmetro
     * Este método só retorna perfis do tipo Usuários
     *
     * @return json object
     * @author Gustavo Souza Gonçalves
     */
    public function findUsuario()
    {
        try {
            $result = null;

            if ($this->request->is(['post', 'put'])) {
                $data = $this->request->getData();
                $usuarios = array();

                $criaUsuarioCPFPesquisa = !empty($data["cria_usuario_cpf_pesquisa"]) ? $data["cria_usuario_cpf_pesquisa"] : false;
                $clientesId = !empty($data["clientes_id"]) ? $data["clientes_id"] : null;

                if (strlen($data['parametro']) >= 3) {

                    $rede = $this->request->session()->read('Rede.Grupo');
                    $restringirUsuariosRede = isset($data["restrict_query"]) ? $data["restrict_query"] : false;
                    $veiculoEncontrado = null;

                    if ($data['opcao'] == 'nome') {
                        // Pesquisa por Nome

                        if ($restringirUsuariosRede) {
                            $usuarios = $this->Usuarios->getUsuariosByName($data['parametro'], $rede["id"], array(), false, array())->toArray();
                        } else {
                            $usuarios = $this->Usuarios->getUsuariosByName($data['parametro'], null, array(), false, array())->toArray();
                        }

                        // DebugUtil::printArray($usuarios, false);
                    } elseif ($data['opcao'] == 'doc_estrangeiro') {
                        // Pesquisa por Documento Estrangeiro

                        if ($restringirUsuariosRede) {
                            $usuarios = $this->Usuarios->getUsuariosByDocumentoEstrangeiro($data['parametro'], $rede['id'], array(), false, array())->toArray();
                        } else {
                            $usuarios = $this->Usuarios->getUsuariosByDocumentoEstrangeiro($data['parametro'], null, array(), false, array())->toArray();
                        }
                    } elseif ($data['opcao'] == 'cpf' && !isset($user)) {
                        $cpf = preg_replace("(\W)", "", $data["parametro"]);
                        // Pesquisa por CPF
                        if ($restringirUsuariosRede) {
                            $usuario = $this->Usuarios->getUsuarioByCPF($cpf, $rede["id"], array(), false, array());
                        } else {
                            $usuario = $this->Usuarios->getUsuarioByCPF($cpf, null, array(), false, array());
                        }

                        if ($criaUsuarioCPFPesquisa) {
                            // Validação
                            $cpfValido = NumberUtil::validarCPF($cpf);

                            if (!$cpfValido["status"]) {
                                // return ResponseUtil::errorAPI(MESSAGE_OPERATION_FAILURE_DURING_PROCESSING, array($cpfValido["message"]));

                                // $message = MESSAGE_OPERATION_FAILURE_DURING_PROCESSING;
                                $message = __($cpfValido["message"], $data["parametro"]);
                                $error = 1;
                                $count = 0;
                                $usuarios = null;
                                $veiculoEncontrado = 0;

                                $arraySet = [
                                    "error",
                                    "count",
                                    "message",
                                    "usuarios",
                                    "veiculoEncontrado"
                                ];

                                $this->set(compact($arraySet));
                                $this->set("_serialize", $arraySet);

                                return;
                            }

                            // Criação
                            // Se usuário não encontrado, cadastra para futuro acesso

                            if (empty($usuario)) {
                                $usuario = $this->Usuarios->addUsuarioAguardandoAtivacao($cpf);

                                // Se usuário cadastrado, vincula ele ao ponto de atendimento (cliente)
                                if ($usuario) {
                                    $this->ClientesHasUsuarios->saveClienteHasUsuario($clientesId, $usuario["id"], 0);
                                }
                            }
                        }

                        $usuarios[] = $usuario;
                    } else {
                        // Pesquisa por Placas

                        if ($restringirUsuariosRede) {
                            $retorno = $this->Veiculos->getUsuariosClienteByVeiculo($data['parametro'], $rede["id"], array(), false);
                        } else {
                            $retorno = $this->Veiculos->getUsuariosClienteByVeiculo($data['parametro'], null, array(), false);
                        }

                        $veiculoEncontrado = $retorno["veiculo"];
                        $usuarios = $retorno["usuarios"];

                        // print_r($data);
                        // echo PHP_EOL;
                        // echo __LINE__;
                        // echo PHP_EOL;
                        // print_r($retorno);
                    }

                    $usuariosTemp = array();

                    foreach ($usuarios as $key => $value) {
                        if (!empty($value)) {
                            $pontuacoes = $this->Pontuacoes->getSumPontuacoesOfUsuario($value['id'], $rede["id"], array());

                            $saldo = $pontuacoes["resumo_gotas"]["saldo"];

                            if (!empty($saldo) && $saldo > 0) {
                                $saldo = floor($saldo);
                            }

                            $value["pontuacoes"] = $saldo;
                            $value['data_nasc'] = !empty($value['data_nasc']) ? $value["data_nasc"]->format('d/m/Y') : null;

                            $usuariosTemp[] = $value;
                        }
                    }

                    $usuarios = $usuariosTemp;

                    $error = false;
                    $count = sizeof($usuarios);
                    $message = "";
                } else {
                    $error = true;
                    $message = "O argumento de pesquisa deve ser maior que 3 caracteres!";
                }

                $arraySet = [
                    "error",
                    "count",
                    "message",
                    "usuarios",
                    "veiculoEncontrado"
                ];

                $this->set(compact($arraySet));
                $this->set("_serialize", $arraySet);
            }
        } catch (\Exception $e) {
            $trace = $e->getTraceAsString();
            $messageString = __("Erro ao pesquisar usuário!");

            $errors = $trace;
            $mensagem = ['status' => false, 'message' => $messageString, 'errors' => $errors];

            $messageStringDebug = __("{0} - {1} . [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $messageString, $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }
    }

    /**
     * Encontra usuário por Id
     *
     * @return \Cake\Http\Response|void
     */
    public function findUsuarioById()
    {
        try {
            $result = null;

            $user = null;
            $count = null;

            if ($this->request->is(['post'])) {
                $data = $this->request->getData();

                // verifica cliente (Usuário deve estar vinculado à rede, seja matriz ou filial)

                $usuariosId = isset($data["usuarios_id"]) ? (int) $data["usuarios_id"] : null;

                if (!empty($usuariosId)) {
                    $clientes_ids = $this->Clientes->getIdsMatrizFiliaisByClienteId($data['clientes_id']);

                    $cliente_has_usuario = $this->ClientesHasUsuarios->findClienteHasUsuarioInsideNetwork($data['usuarios_id'], $clientes_ids);

                    // achou usuário, retorna o objeto
                    if ($cliente_has_usuario->usuario) {
                        // consulta de pontuação, se encontrou usuário

                        $cliente_has_usuario->usuario['data_nasc'] = $cliente_has_usuario->usuario['data_nasc']->format('d/m/Y');


                        $pontuacoes
                            = Number::precision(
                                $this->Pontuacoes->getSumPontuacoesOfUsuario(
                                    $usuariosId,
                                    null,
                                    $clientes_ids
                                ),
                                2
                            );
                        $saldo = $pontuacoes["resumo_gotas"]["saldo"];

                        if (!empty($saldo) && $saldo > 0) {
                            $saldo = floor($saldo);
                        }

                        $value["pontuacoes"] = $saldo;


                        $cliente_has_usuario->usuario['pontuacoes']
                            = $pontuacoes;

                        // $result = json_encode(['user' => $cliente_has_usuario->usuario, 'count' => 1]);
                        $user = $cliente_has_usuario->usuario;
                        $count = 1;
                    }
                }
            }

            $arraySet = [
                'user',
                'count'
            ];

            $this->set(compact($arraySet));
            $this->set("_serialize", $arraySet);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao procurar usuário[{0}] ", $e->getMessage());

            Log::write('error', $stringError);
            Log::write('error', $trace);
        }
    }

    #endregion
}
