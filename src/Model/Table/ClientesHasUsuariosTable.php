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
     * Method get of client table property
     *
     * @return Cake\ORM\Table Table object
     */
    private function _getClienteHasUsuarioTable()
    {
        if (is_null($this->clienteHasUsuarioTable)) {
            $this->_setClienteHasUsuarioTable();
        }
        return $this->clienteHasUsuarioTable;
    }

    /**
     * Method set of client table property
     *
     * @return void
     */
    private function _setClienteHasUsuarioTable()
    {
        $this->clienteHasUsuarioTable = TableRegistry::get('ClientesHasUsuarios');
    }

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

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

        // $this->hasMany(
        //     'Usuarios',
        //     [
        //         'className' => 'Usuarios',
        //         'foreignKey' => 'usuarios_id',
        //         // 'where' => [
        //         //     'usuarios_id = Usuarios.id',
        //         // ],
        //         'joinType' => 'INNER'
        //     ]
        // );

        $this->belongsTo(
            "Usuarios",
            array(
                "className" => "Usuarios",
                "foreignKey" => "usuarios_id",
                "joinType" => "INNER"
            )
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
     * Verifica se usuário está vinculado à cliente (rede / posto)
     *
     * @param array $where_conditions Condições de pesquisa
     *
     * @return void
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
            $data = $this->_getClienteHasUsuarioTable()->find('all')
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

            if (sizeof($clientesIds) == 0) {
                return null;
            }

            if ($usuario["tipo_perfil"] <= Configure::read('profileTypes')['AdminNetworkProfileType']) {
                // se for admin rti ou admin rede, pega o id de todas as unidades

                if ($descartarMatriz) {
                    $clientes = $this->Clientes->find('list')
                        ->where(['id in' => $clientesIds, 'matriz' => false]);
                } else {
                    $clientes = $this->Clientes->find('list')
                        ->where(['id in' => $clientesIds]);
                }

            } else if ($usuario->tipo_perfil <= Configure::read('profileTypes')['AdminLocalProfileType']) {

                // se usuário tem permissão de admin regional ou de local, pega quais as unidades tem acesso

                // pega os id's aos quais ele tem permissão de admin

                $clientesHasUsuariosList = $this
                    ->find('all')
                    ->where(
                        [
                            'clientes_id in ' => $clientesIds,
                            'usuarios_id' => $usuario->id,
                            'tipo_perfil IN ' => [
                                (int)Configure::read('profileTypes')['AdminRegionalProfileType'],
                                (int)Configure::read('profileTypes')['AdminLocalProfileType']
                            ]
                        ]
                    );

                $clientesIds = [];
                foreach ($clientesHasUsuariosList as $key => $value) {
                    $clientesIds[] = $value['clientes_id'];
                }

                if ($descartarMatriz) {

                    $clientes = $this->_getClienteHasUsuarioTable()->Clientes
                        ->find('list')
                        ->where(
                            [
                                'id IN ' => $clientesIds,
                                'matriz' => false
                            ]
                        );
                } else {
                    $clientes = $this->_getClienteHasUsuarioTable()->Clientes
                        ->find('list')
                        ->where(['id IN ' => $clientesIds]);
                }

            } else {

                // pega os id's aos quais ele tem permissão de admin

                $clientesHasUsuariosList = $this
                    ->find('all')
                    ->where(
                        [
                            'clientes_id in ' => $clientesIds,
                            'usuarios_id' => $usuario["id"],
                            'tipo_perfil' => $usuario["tipo_perfil"]
                        ]
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
                    "tipo_perfil",
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
     * @param int $tipo_perfil  Tipo de perfil procurado
     *
     * @return \App\Model\Entity\ClientesHasUsuarios
     */
    public function getAllUsersByClienteId(int $clientesId, int $tipo_perfil = null)
    {
        try {

            $whereConditions = array();

            $whereConditions[] = [
                'ClientesHasUsuarios.clientes_id' => $clientesId
            ];

            if (!is_null($tipo_perfil)) {
                $whereConditions[] = ['ClientesHasUsuarios.tipo_perfil' => $tipo_perfil];
            }

            $clientes_has_usuarios = $this->_getClienteHasUsuarioTable()->find('all')
                ->where($whereConditions);

            $usuariosIds = [];

            foreach ($clientes_has_usuarios as $key => $value) {
                $usuariosIds[] = $value['usuarios_id'];
            }

            $result = null;

            if (sizeof($usuariosIds) > 0) {
                $result = $this->_getClienteHasUsuarioTable()
                    ->Usuarios
                    ->find('all')
                    ->where(['id IN ' => $usuariosIds]);
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
     * @param integer $tipo_perfil
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 11/10/2018
     *
     * @return array Lista de seleção
     */
    public function getAllClientesIdsAllowedFromRedesIdUsuariosId(int $redesId, int $usuariosId, int $tipo_perfil)
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
                        "Usuarios.tipo_perfil" => $tipo_perfil
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
     * @param int $tipo_perfil Tipo de perfil procurado
     *
     * @return object ClientesHasUsuarios
     */
    public function getAllClientesIdsByUsuariosId(int $usuariosId, int $tipo_perfil = null)
    {
        try {

            $whereConditions = array();

            $whereConditions[] = [
                'ClientesHasUsuarios.usuarios_id' => $usuariosId
            ];

            if (!is_null($tipo_perfil)) {
                $whereConditions[] = ['ClientesHasUsuarios.tipo_perfil' => $tipo_perfil];
            }

            $clientes_has_usuarios = $this->_getClienteHasUsuarioTable()->find('all')
                ->where($whereConditions);

            $clientesIds = [];

            foreach ($clientes_has_usuarios as $key => $value) {
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
                ->contain("Cliente")
                ->order(array("tipo_perfil" => "ASC"));

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
     * @param int $tipoPerfil Tipo do perfil
     * @param int $contaAtiva Conta do usuário Ativa
     *
     * @return \App\Model\Entity\ClienteHasUsuario
     */
    public function saveClienteHasUsuario(int $clientesId, int $usuariosId, int $tipoPerfil, bool $contaAtiva = true)
    {
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
            $clientesHasUsuario["tipo_perfil"] = (int)$tipoPerfil;
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
     * @param int $id          Id do registro
     * @param int $clientesId Id do cliente
     * @param int $usuariosId Id do Usuário
     * @param int $tipo_perfil Tipo de Perfil
     *
     * @return \App\Entity\Model\ClientesHasUsuario
     */
    public function updateClienteHasUsuarioRelationship(int $id, int $clientesId, int $usuariosId, int $tipo_perfil)
    {
        try {

            return $this->updateAll(
                [
                    'clientes_id' => $clientesId,
                    'usuarios_id' => $usuariosId,
                    'tipo_perfil' => $tipo_perfil
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

        return $this->saveClienteHasUsuario($usuarioCliente["clientes_id"], $usuarioCliente["usuarios_id"], $usuarioCliente["tipo_perfil"], $contaAtiva);

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

            return $this->_getClienteHasUsuarioTable()
                ->deleteAll(['clientes_id in' => $clientesIds]);
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

            return $this->_getClienteHasUsuarioTable()
                ->deleteAll(['usuarios_id' => $usuariosId]);
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
            $clientesHasUsuario = $this->_getClienteHasUsuarioTable()
                ->find()
                ->where(
                    [
                        'clientes_id' => $clientesId,
                        'usuarios_id' => $usuariosId
                    ]
                )
                ->first();

            $result = $clienteHasUsuarioTable->delete($clientesHasUsuario);

            if ($result) {
                // se adicionou o registro, atualiza o perfil do admin

                $usuario
                    = $this->_getClienteHasUsuarioTable()->Usuarios->find('all')
                    ->where(['id' => $usuariosId])
                    ->first();

                $usuario->matriz_id = null;

                return $this->_getClienteHasUsuarioTable()->Usuarios->save($usuario);
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
            $cliente_has_usuario = $this->_getClienteHasUsuarioTable()
                ->find('all')
                ->where(['id' => $id])
                ->first();


            return $this->_getClienteHasUsuarioTable()->delete($cliente_has_usuario);
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
