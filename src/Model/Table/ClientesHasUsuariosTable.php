<?php
namespace App\Model\Table;

use ArrayObject;
use App\View\Helper;
use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use App\Custom\RTI\DebugUtil;
use App\Custom\RTI\ResponseUtil;

/**
 * ClientesHasUsuarios Model
 *
 * @property \App\Model\Table\ClientesTable|\Cake\ORM\Association\BelongsTo $Matriz
 * @property \App\Model\Table\ClientesTable|\Cake\ORM\Association\BelongsTo $Clientes
 * @property \App\Model\Table\UsuariosTable|\Cake\ORM\Association\BelongsTo $Usuarios
 *
 * @method \App\Model\Entity\ClientesHasUsuario get($primaryKey, $options = [])
 * @method \App\Model\Entity\ClientesHasUsuario newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ClientesHasUsuario[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ClientesHasUsuario|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ClientesHasUsuario patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ClientesHasUsuario[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ClientesHasUsuario findOrCreate($search, callable $callback = null, $options = [])
 */
class ClientesHasUsuariosTable extends Table
{

    /**
     * -------------------------------------------------------------
     * Fields
     * -------------------------------------------------------------
     */

    protected $clienteHasUsuarioQuery = null;

    protected $clienteHasUsuarioTable = null;

    /**
     * -------------------------------------------------------------
     * Properties
     * -------------------------------------------------------------
     */

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        // parent::initialize($config);

        $this->setTable('clientes_has_usuarios');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo(
            'Cliente',
            array(
                'className' => 'Clientes',
                'foreignKey' => 'clientes_id',
                'joinType' => 'LEFT'
            )
        );

        $this->hasMany(
            'Clientes',
            [
                'className' => 'Clientes',
                'foreignKey' => 'id',
                'joinType' => 'LEFT'
            ]
        );

        // $this->belongsTo(
        //     'Clientes',
        //     [
        //         'className' => 'Clientes',
        //         'foreignKey' => 'clientes_id',
        //         'join' => 'INNER'
        //     ]
        // );

        $this->belongsTo(
            'Usuario',
            [
                'className' => 'Usuarios',
                'foreignKey' => 'usuarios_id',
                'joinType' => 'INNER'
            ]
        );

        $this->belongsTo(
            'Usuarios',
            [
                'className' => 'Usuarios',
                'foreignKey' => 'usuarios_id',
                'where' => [
                    'usuarios_id = Usuarios.id',
                ],
                'joinType' => 'LEFT'
            ]
        );

        $this->belongsTo(
            'RedesHasClientes',
            [
                'className' => 'RedesHasClientes',
                'foreignKey' => 'clientes_id',
                'joinType' => 'INNER'
            ]
        );
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->allowEmpty('id', 'create');

        $validator
            ->integer('clientes_id');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['clientes_id'], 'Clientes'));
        $rules->add($rules->existsIn(['usuarios_id'], 'Usuarios'));

        return $rules;
    }

    /**
     * -------------------------------------------------------------
     * Methods
     * -------------------------------------------------------------
     */


    #region Read

    /**
     * Obtem um relacionamento cliente has usuario
     *
     * @param array $where_conditions Condições de pesquisa
     *
     * @return Cake\ORM\Query
     **/
    public function findClienteHasUsuario(array $where_conditions)
    {
        try {
            return $this->find('all')
                ->where($where_conditions)
                ->contain(['Cliente', 'Usuario']);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);
        }
    }

    /**
     * Procura por um usuário dentro de uma rede de lojas
     *
     * @param int   $usuariosId       Id do usuário
     * @param array $array_clientes_id Array de Id de clientes
     *
     * @return void
     */
    public function findClienteHasUsuarioInsideNetwork(int $usuariosId, array $array_clientes_id)
    {
        try {
            $data = $this->find('all')
                ->where(
                    [
                        'ClientesHasUsuarios.clientes_id in' => $array_clientes_id,
                        'ClientesHasUsuarios.usuarios_id' => $usuariosId
                    ]
                )
                ->join(['Usuarios'])
                // ->contain(['Usuarios'])
                ->first();

            return $data;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * ClientesHasUsuarios::getUsuariosFidelizadosClientes
     *
     * Obtem clientes fidelizados de postos/rede
     *
     * @param array $clientesIds
     * @param string $nome
     * @param string $cpf
     * @param string $veiculo
     * @param string $documentoEstrangeiro
     * @param int $status
     * @param string $dataInicial
     * @param string $dataFinal
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 13/09/2018
     *
     * @return array
     */
    public function getUsuariosFidelizadosClientes(
        array $clientesIds = array(),
        string $nome = null,
        string $cpf = null,
        string $veiculo = null,
        string $documentoEstrangeiro = null,
        int $status = null,
        string $dataInicial = null,
        string $dataFinal = null
    ) {

        $whereConditions = array();

        if (!empty($nome)) {
            $whereConditions[] = array("Usuario.nome like '%$nome%'");
        }

        if (!empty($cpf)) {
            $whereConditions[] = array("Usuario.cpf like '%$cpf%'");
        }

        if (!empty($veiculo)) {
            $whereConditions[] = array("Veiculos.placa like '%$veiculo%'");
        }

        if (!empty($documentoEstrangeiro)) {
            $whereConditions[] = array("Usuario.doc_estrangeiro like '%$documentoEstrangeiro%'");
        }

        if (strlen($status) > 0) {
            $whereConditions[] = array("Usuario.conta_ativa" => $status);
        }

        if (!empty($dataInicial)) {
            $whereConditions[] = array("DATE_FORMAT(ClientesHasUsuarios.audit_insert, '%Y-%m-%d') >= '$dataInicial'");
        }

        if (!empty($dataFinal)) {
            $whereConditions[] = array("DATE_FORMAT(ClientesHasUsuarios.audit_insert, '%Y-%m-%d') <= '$dataFinal'");
        }

        // ResponseUtil::success($whereConditions);
        $whereConditions[] = array("ClientesHasUsuarios.clientes_id in " => $clientesIds);

        // Obtem os ids de usuarios
        $usuariosCliente = $this->find()
            ->select(array(
                "ClientesHasUsuarios.usuarios_id",
                "ClientesHasUsuarios.audit_insert",
                "Usuario.id",
                "Usuario.nome",
                "Usuario.cpf",
                "Usuario.doc_estrangeiro",
                "Usuario.conta_ativa"
            ))
            ->where($whereConditions)
            ->contain("Usuario.UsuariosHasVeiculos.Veiculos")
            ->order(array("ClientesHasUsuarios.audit_insert" => "ASC"))
            ->toArray();

        $usuarios = array();
        $usuariosIds = array();
        $usuariosTable = TableRegistry::get("Usuarios");
        $pontuacoesTable = TableRegistry::get("Pontuacoes");

        // ResponseUtil::success($usuariosCliente);
        if (count($usuariosCliente) > 0) {

            foreach ($usuariosCliente as $clienteHasUsuario) {
                if (!in_array($clienteHasUsuario["usuario"]["id"], $usuariosIds)) {
                    $usuariosIds[] = $clienteHasUsuario["usuario"]["id"];
                    $usuario["id"] = $clienteHasUsuario["usuarios_id"];
                    $usuario["dataVinculo"] = $clienteHasUsuario["audit_insert"];
                    $usuario["nome"] = $clienteHasUsuario["usuario"]["nome"];
                    $usuario["cpf"] = $clienteHasUsuario["usuario"]["cpf"];
                    $usuario["docEstrangeiro"] = $clienteHasUsuario["usuario"]["doc_estrangeiro"];
                    $saldoAtual = $pontuacoesTable->getSumPontuacoesOfUsuario($usuario["id"], null, $clientesIds);
                    $usuario["gotasAdquiridas"] = $saldoAtual["resumo_gotas"]["total_gotas_adquiridas"];
                    $usuario["gotasUtilizadas"] = $saldoAtual["resumo_gotas"]["total_gotas_utilizadas"];
                    $usuario["gotasExpiradas"] = $saldoAtual["resumo_gotas"]["total_gotas_expiradas"];
                    $usuario["saldoAtual"] = $saldoAtual["resumo_gotas"]["saldo"];
                    $usuario["brindesVendidosReais"] = 0;
                    $usuario["contaAtiva"] = $clienteHasUsuario["usuario"]["conta_ativa"];

                    $usuarios[] = $usuario;
                }
            }
        }

        return $usuarios;
    }

    /**
     * Pega id de unidades que usuário pode filtrar
     *
     * @param int  $redesId         Id da Rede
     * @param int  $usuariosId      Id de Usuário
     * @param bool $descartarMatriz Retira ou Inclui matriz
     *
     * @return void
     */
    public function getClientesFilterAllowedByUsuariosId(int $redesId, int $usuariosId, bool $descartarMatriz = false)
    {
        try {

            $clientesIds = $this->RedesHasClientes->getClientesIdsFromRedesHasClientes($redesId);

            $usuario = $this->Usuarios->find('all')
                ->where(['id' => $usuariosId])->first();

            // Rede não tem cliente
            if (count($clientesIds) == 0) {
                return null;
            }

            if ($usuario["tipo_perfil"] <= PROFILE_TYPE_ADMIN_NETWORK) {
                // se for admin rti ou admin rede, pega o id de todas as unidades

                if ($descartarMatriz) {
                    $clientes = $this->Clientes->find('list')
                        ->where(['id in' => $clientesIds, 'matriz' => false]);
                } else {
                    $clientes = $this->Clientes->find('list')
                        ->where(['id in' => $clientesIds]);
                }
            } else if ($usuario["tipo_perfil"] <= PROFILE_TYPE_ADMIN_LOCAL) {

                // se usuário tem permissão de admin regional ou de local, pega quais as unidades tem acesso

                // pega os id's aos quais ele tem permissão de admin

                $clientesHasUsuariosList = $this
                    ->find('all')
                    ->where(
                        [
                            'clientes_id in ' => $clientesIds,
                            'usuarios_id' => $usuario["id"],
                            // 'Usuarios.tipo_perfil IN ' => [
                            //     (int)Configure::read('profileTypes')['AdminRegionalProfileType'],
                            //     (int)Configure::read('profileTypes')['AdminLocalProfileType']
                            // ]
                        ]
                    )->select(array("clientes_id"))
                    ;

                $clientesIds = [];
                foreach ($clientesHasUsuariosList as $key => $value) {
                    $clientesIds[] = $value['clientes_id'];
                }

                if ($descartarMatriz) {

                    $clientes = $this->Clientes
                        ->find('list')
                        ->where(
                            [
                                'id IN ' => $clientesIds,
                                'matriz' => false
                            ]
                        );
                } else {
                    $clientes = $this->Clientes
                        ->find('list')
                        ->where(['id IN ' => $clientesIds]);
                }
            } else {

                // pega os id's aos quais ele tem permissão de admin

                $clientesHasUsuariosList = $this
                    ->find('all')
                    ->where(
                        array(
                            'clientes_id in ' => $clientesIds,
                            'usuarios_id' => $usuario["id"]
                        )
                    )
                    ->select(array("clientes_id"));

                $clientesIds = [];
                foreach ($clientesHasUsuariosList as $value) {
                    $clientesIds[] = $value['clientes_id'];
                }

                $whereConditions = array("id IN " => $clientesIds);

                if ($descartarMatriz) {
                    $whereConditions[] = array("matriz" => false);
                }

                $clientes = $this->Clientes
                    ->find('list')
                    ->where($whereConditions);
            }

            return $clientes->select(array("id", "razao_social"));
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registro: " . $e->getMessage() . ".");

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Obtem todos os vínculos de um usuário pela rede
     *
     * @param integer $redesId    Id da Rede
     * @param integer $usuariosId Id do usuário
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 01/10/2017
     *
     * @return \App\Model\Entity\ClientesHasUsuarios
     */
    public function getVinculoClienteUsuario(int $redesId, int $usuariosId)
    {
        $conditions = array(
            "Redes.id" => $redesId,
            "ClientesHasUsuarios.usuarios_id" => $usuariosId
        );
        $usuario = $this->find("all")
            ->where($conditions)
            ->contain("RedesHasClientes.Redes")
            ->select(
                array(
                    "id",
                    "clientes_id",
                    "usuarios_id",
                    "conta_ativa"
                )
            )->first();

        return $usuario;
    }

    /**
     * Obtêm todos os usuários de um cliente através do
     * Id de cliente e tipo de perfil
     *
     * @param int $clientesId  Id do cliente
     * @param int $tipoPerfil  Tipo de perfil procurado
     *
     * @return \App\Model\Entity\ClientesHasUsuarios
     */
    public function getAllUsersByClienteId(int $clientesId, int $tipoPerfil = null, bool $ativo = null)
    {
        try {

            $whereConditions = array('ClientesHasUsuarios.clientes_id' => $clientesId);

            // if (!is_null($tipoPerfil)) {
            //     $whereConditions[] = ['ClientesHasUsuarios.tipo_perfil' => $tipoPerfil];
            // }

            if (!is_null($ativo)) {
                $whereConditions[] = array("ClientesHasUsuarios.conta_ativa" => $ativo);
            }

            $clientesHasUsuarios = $this->find('all')
                ->where($whereConditions);

            $usuariosIds = [];

            foreach ($clientesHasUsuarios as $key => $value) {
                $usuariosIds[] = $value['usuarios_id'];
            }

            $result = null;

            if (count($usuariosIds) > 0) {
                $whereUsuarios = array("id IN " => $usuariosIds);

                if (!is_null($tipoPerfil)) {
                    $whereUsuarios["tipo_perfil"]  = $tipoPerfil;
                }

                $result = $this
                    ->Usuarios
                    ->find('all')
                    ->where($whereUsuarios);
            }

            return $result;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * ClientesHasUsuariosTable::getAllClientesIdsAllowedFromRedesIdUsuariosId
     *
     * Obtem todos os vínculos de um usuário em uma rede específica
     *
     * @param integer $redesId
     * @param integer $usuariosId
     * @param integer $tipoPerfil
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 11/10/2018
     *
     * @return array Lista de seleção
     */
    public function getAllClientesIdsAllowedFromRedesIdUsuariosId(int $redesId, int $usuariosId, int $tipoPerfil)
    {
        try {
            $query = $this->find("all")
                ->contain(array(
                    "RedesHasClientes",
                    "Clientes",
                    "Usuarios"
                ))
                ->where(
                    array(
                        "RedesHasClientes.redes_id" => $redesId,
                        "Usuarios.id" => $usuariosId,
                        "Usuarios.tipo_perfil" => $tipoPerfil
                    )
                )
                ->select(
                    array(
                        "ClientesHasUsuarios.clientes_id",
                        "Clientes.nome_fantasia"
                    )
                )
                ->toArray();

            $items = array();

            foreach ($query as $key => $value) {
                $items[$value["clientes_id"]] = $value["cliente"]["nome_fantasia"];
            }

            return $items;
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao obter registros: {0}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
        }
    }

    /**
     * Obtêm todos os clientes através de um usuário e tipo de perfil
     *
     * @param int $usuariosId Id de usuário
     * @param int $tipoPerfil Tipo de perfil procurado
     *
     * @return object ClientesHasUsuarios
     */
    public function getAllClientesIdsByUsuariosId(int $usuariosId, int $tipoPerfil = null)
    {
        try {

            $whereConditions = array();

            $whereConditions[] = [
                'ClientesHasUsuarios.usuarios_id' => $usuariosId
            ];

            // if (!is_null($tipoPerfil)) {
            //     $whereConditions[] = ['ClientesHasUsuarios.tipo_perfil' => $tipoPerfil];
            // }
            if (!is_null($tipoPerfil)) {
                // @todo gustavosg Testar tipo_perfil
                $whereConditions[] = ['Usuarios.tipo_perfil' => $tipoPerfil];
            }

            $clientesHasUsuarios = $this->find('all')
                ->where($whereConditions);

            $clientesIds = [];

            foreach ($clientesHasUsuarios as $key => $value) {
                $clientesIds[] = $value['clientes_id'];
            }

            return $clientesIds;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Obtem o vínculo de clientes a um usuário
     *
     * @param integer $usuariosId Id de Usuário
     * @param boolean $filtrarPrimeiro Se deve trazer somente o primeiro registro
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 19/10/2018
     *
     * @return array $ids de clientes
     */
    public function getVinculoClientesUsuario(int $usuariosId, bool $filtrarPrimeiro = true)
    {
        try {
            $whereConditions = array(
                "usuarios_id" => $usuariosId
            );
            $clientesUsuarios = $this->find("all")
                ->where($whereConditions)
                ->contain("Cliente");
            // ->order(
            //     array("tipo_perfil" => "ASC")
            // );


            if ($filtrarPrimeiro) {
                $clientesUsuarios = $clientesUsuarios->first();
                return $clientesUsuarios;
            } else {
                return $clientesUsuarios->toArray();
            }
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao obter vínculo de clientes ao usuário: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write('error', $trace);
        }
    }

    #endregion

    #region  Create

    /**
     * Adiciona novo Usuário em cliente
     *
     * @param int $usuariosId Id do cliente
     * @param int $clientesId Id do usuário
     * @param int $contaAtiva Conta do usuário Ativa
     *
     * @return \App\Model\Entity\ClienteHasUsuario
     */
    public function saveClienteHasUsuario(int $clientesId, int $usuariosId, bool $contaAtiva = true)
    {
        // @todo gustavosg: Ajustar todas as ocorrências
        try {

            $whereConditions = array(
                'usuarios_id' => $usuariosId,
                'clientes_id' => $clientesId
                // "conta_ativa" => $contaAtiva
            );

            if (strlen($tipoPerfil) > 0) {
                $whereConditions[] = array('tipo_perfil' => $tipoPerfil);
            }

            $clientesHasUsuario = $this->find('all')->where($whereConditions)->first();

            if (!$clientesHasUsuario) {
                $clientesHasUsuario = $this->newEntity();
            }

            $clientesHasUsuario["clientes_id"] = (int)$clientesId;
            $clientesHasUsuario["usuarios_id"] = (int)$usuariosId;
            // $clientesHasUsuario["tipo_perfil"] = (int)$tipoPerfil;
            $clientesHasUsuario["conta_ativa"] = (int)$contaAtiva;

            return $this->save($clientesHasUsuario);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $object = null;

            foreach ($trace as $key => $item_trace) {
                if ($item_trace['class'] == 'Cake\Database\Query') {
                    $object = $item_trace;
                    break;
                }
            }

            $stringError = __("Erro ao inserir registro: {0}, em {1}", $e->getMessage(), $object['file']);

            Log::write('error', $stringError);

            $error = ['success' => false, 'message' => $stringError];
            return $error;
        }
    }

    #endregion

    #region  Update

    /**
     * Atualiza o relacionamento de cliente e usuário
     *
     * @param int $id Id do registro
     * @param int $clientesId Id do cliente
     * @param int $usuariosId Id do Usuário
     * @param int $tipoPerfil Tipo de Perfil
     *
     * @return \App\Entity\Model\ClientesHasUsuario
     */
    public function updateClienteHasUsuarioRelationship(int $id, int $clientesId, int $usuariosId, int $tipoPerfil)
    {
        try {

            return $this->updateAll(
                [
                    'clientes_id' => $clientesId,
                    'usuarios_id' => $usuariosId,
                    'tipo_perfil' => $tipoPerfil
                ],
                [
                    'id' => $id
                ]
            );
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $object = null;

            foreach ($trace as $key => $item_trace) {
                if ($item_trace['class'] == 'Cake\Database\Query') {
                    $object = $item_trace;
                    break;
                }
            }

            $stringError = __("Erro ao buscar registro: {0}, em {1}", $e->getMessage(), $object['file']);

            Log::write('error', $stringError);

            $error = ['result' => false, 'message' => $stringError];
            return $error;
        }
    }

    /**
     * Atualiza o relacionamento de cliente e usuário
     *
     * @param array $update_array Argumentos que serão atualizados
     * @param array $select_array Argumentos de condição
     *
     * @return \App\Entity\Model\ClientesHasUsuario
     */
    public function updateClientesHasUsuarioRelationship(array $update_array, array $select_array)
    {
        try {
            return $this->updateAll($update_array, $select_array);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $object = null;

            foreach ($trace as $key => $item_trace) {
                if ($item_trace['class'] == 'Cake\Database\Query') {
                    $object = $item_trace;
                    break;
                }
            }

            $stringError = __("Erro ao buscar registro: {0}, em {1}", $e->getMessage(), $object['file']);

            Log::write('error', $stringError);

            $error = ['result' => false, 'message' => $stringError];
            return $error;
        }
    }


    /**
     * Define usuário informado para uma matriz (migração)
     *
     * @param int $clientesId Id de Cliente
     * @param int $matriz_id   Id da Matriz
     *
     * @return boolean
     */
    public function setClientesHasUsuariosToMainCliente(int $clientesId, int $matriz_id)
    {
        try {
            return $this->updateAll(
                [
                    'clientes_id' => $matriz_id
                ],
                [
                    'clientes_id' => $clientesId
                ]
            );
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $object = null;

            foreach ($trace as $key => $item_trace) {
                if ($item_trace['class'] == 'Cake\Database\Query') {
                    $object = $item_trace;
                    break;
                }
            }

            $stringError = __("Erro ao buscar registro: {0}, em {1}", $e->getMessage(), $object['file']);

            Log::write('error', $stringError);

            $error = ['result' => false, 'message' => $stringError];
            return $error;
        }
    }

    public function updateContaAtivaUsuario(int $id = null, int $clientesId, int $usuariosId, bool $contaAtiva)
    {
        $usuarioCliente = null;

        if (!empty($id)) {
            $usuarioCliente = $this->getById($id);
        } else {
            $usuarioCliente = $this->find("all")->where(
                array(
                    "clientes_id" => $clientesId,
                    "usuarios_id" => $usuariosId
                )
            )->first();
        }

        if (empty($usuarioCliente)) {
            throw new \Exception("Este usuário não possui vínculo com o Posto de Atendimento, não sendo possível alterar o status da conta!");
        }

        $usuarioCliente["conta_ativa"] = $contaAtiva;

        return $this->saveClienteHasUsuario($usuarioCliente["clientes_id"], $usuarioCliente["usuarios_id"], $contaAtiva);
    }

    #endregion

    #region  Delete

    /**
     * Apaga todos os vínculos de um usuário à um cliente
     *
     * @param array $clientesIds Ids de clientes
     *
     * @return boolean
     */
    public function deleteAllClientesHasUsuariosByClientesIds(array $clientesIds)
    {
        try {

            return $this->deleteAll(array('clientes_id in' => $clientesIds));
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $object = null;

            foreach ($trace as $key => $item_trace) {
                if ($item_trace['class'] == 'Cake\Database\Query') {
                    $object = $item_trace;
                    break;
                }
            }

            $stringError = __("Erro ao buscar registro: {0}, em {1}", $e->getMessage(), $object['file']);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Apaga todos os vínculos de um usuário à um cliente
     *
     * @param int $usuariosId Id de usuário
     *
     * @return boolean
     */
    public function deleteAllClientesHasUsuariosByUsuariosId(int $usuariosId)
    {
        try {

            return $this->deleteAll(['usuarios_id' => $usuariosId]);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $object = null;

            foreach ($trace as $key => $item_trace) {
                if ($item_trace['class'] == 'Cake\Database\Query') {
                    $object = $item_trace;
                    break;
                }
            }

            $stringError = __("Erro ao buscar registro: {0}, em {1}", $e->getMessage(), $object['file']);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Remove Administrador de cliente
     *
     * @param int $matrizId    Id da matriz
     * @param int $clientesId Id do cliente
     * @param int $usuariosId Id do usuário
     *
     * @return boolean
     */
    public function removeAdministratorOfClienteHasUsuario($clientesId, $usuariosId)
    {
        try {
            $clientesHasUsuario = $this->find()
                ->where(
                    array(
                        'clientes_id' => $clientesId,
                        'usuarios_id' => $usuariosId
                    )
                )
                ->first();

            $result = $clienteHasUsuarioTable->delete($clientesHasUsuario);

            if ($result) {
                // se adicionou o registro, atualiza o perfil do admin

                $usuario
                    = $this->Usuarios->find('all')
                    ->where(['id' => $usuariosId])
                    ->first();

                $usuario->matriz_id = null;

                return $this->Usuarios->save($usuario);
            } else {
                return false;
            }
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $object = null;

            foreach ($trace as $key => $item_trace) {
                if ($item_trace['class'] == 'Cake\Database\Query') {
                    $object = $item_trace;
                    break;
                }
            }

            $stringError = __("Erro ao remover registro: {0}, em {1}", $e->getMessage(), $object['file']);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Remove Usuário / Funcionário em cliente
     *
     * @param int $id Id do registro
     *
     * @return boolean
     */
    public function removeClienteHasUsuario($id)
    {
        try {
            $cliente_has_usuario = $this
                ->find('all')
                ->where(['id' => $id])
                ->first();


            return $this->delete($cliente_has_usuario);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $object = null;

            foreach ($trace as $key => $item_trace) {
                if ($item_trace['class'] == 'Cake\Database\Query') {
                    $object = $item_trace;
                    break;
                }
            }

            $stringError = __("Erro ao remover registro: {0}, em {1}", $e->getMessage(), $object['file']);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    #endregion
}
