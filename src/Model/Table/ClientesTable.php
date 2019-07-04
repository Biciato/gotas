<?php

/**
 * TODO: fazer header
 *
 */

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
 * Clientes Model
 *
 * @property \App\Model\Table\MatrizsTable|\Cake\ORM\Association\BelongsTo $Matrizs
 *
 * @method \App\Model\Entity\Cliente get($primaryKey, $options = [])
 * @method \App\Model\Entity\Cliente newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Cliente[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Cliente|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Cliente patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Cliente[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Cliente findOrCreate($search, callable $callback = null, $options = [])
 */
class ClientesTable extends GenericTable
{
    /**
     * -----------------------------------------------------
     * Fields
     * -----------------------------------------------------
     */
    protected $clienteTable = null;

    /**
     * -----------------------------------------------------
     * Properties
     * -----------------------------------------------------
     */

    /**
     * Method get of client table property
     * @return Cake\ORM\Table Table object
     */
    private function _getClientesTable()
    {
        if (is_null($this->clienteTable)) {
            $this->_setClienteTable();
        }
        return $this->clienteTable;
    }

    /**
     * Method set of client table property
     * @return void
     */
    private function _setClienteTable()
    {
        $this->clienteTable = TableRegistry::get('Clientes');
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

        $this->setTable('clientes');
        $this->setDisplayField('razao_social');
        $this->setPrimaryKey('id');

        $this->belongsTo(
            'RedesHasClientes',
            [
                'className' => 'RedesHasClientes',
                'foreignKey' => 'id',
                'joinType' => 'left'
            ]
        );

        $this->hasOne(
            'RedeHasCliente',
            [
                'className' => 'RedesHasClientes',
                'foreignKey' => 'clientes_id',
                'joinType' => 'left'
            ]
        );

        $this->hasMany(
            'ClientesHasUsuarios',
            [
                'className' => 'ClientesHasUsuarios',
                'foreignKey' => 'clientes_id',
                'joinType' => 'LEFT'
            ]
        );

        $this->hasMany(
            'Gotas',
            [
                'className' => 'Gotas',
                'foreignKey' => 'clientes_id',
                'join' => 'INNER'
            ]
        );

        // $this->hasMany(
        //     'ClientesHasBrindesHabilitados',
        //     [
        //         'className' => 'ClientesHasBrindesHabilitados',
        //         'foreignKey' => 'clientes_id',
        //         'join' => 'INNER'
        //     ]
        // );

        $this->hasMany(
            "Brindes",
            array(
                "className" => "Brindes",
                "foreignKey" => "clientes_id",
                "join" => "LEFT"
            )
        );

        $this->hasMany(
            "ClientesHasQuadroHorarios",
            array(
                "className" => "ClientesHasQuadroHorario",
                "foreignKey" => "clientes_id",
                "join" => "LEFT"
            )
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
            ->integer('matriz')
            ->notEmpty('matriz');

        $validator
            ->integer('tipo_unidade')
            ->requirePresence('tipo_unidade', 'create')
            ->notEmpty('tipo_unidade');

        $validator
            ->allowEmpty('codigo_equipamento_rti');

        $validator
            ->allowEmpty('nome_fantasia');

        $validator
            ->requirePresence('razao_social', 'create')
            ->notEmpty('razao_social');

        $validator
            ->notEmpty('cnpj');

        $validator
            ->allowEmpty('endereco');

        $validator
            ->allowEmpty('endereco_numero');

        $validator
            ->allowEmpty('endereco_complemento');

        $validator
            ->allowEmpty('bairro');

        $validator
            ->allowEmpty('municipio');

        $validator
            ->requirePresence('estado')
            ->notEmpty('estado', 'Necessário informar um estado para realizar a importação de dados da SEFAZ');

        $validator
            ->requirePresence('pais')
            ->allowEmpty('pais');

        $validator
            ->allowEmpty('cep', 'Se o usuário não souber o CEP do local, utilize o CEP da cidade.');

        $validator
            ->allowEmpty('latitude');

        $validator
            ->allowEmpty('longitude');

        $validator
            ->allowEmpty('tel_fixo');

        $validator
            ->allowEmpty('tel_fax');

        $validator
            ->allowEmpty('tel_celular');

        $validator
            ->dateTime('audit_insert')
            ->allowEmpty('audit_insert');

        $validator
            ->dateTime('audit_update')
            ->allowEmpty('audit_update');

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

        return $rules;
    }

    #region Create

    /**
     * Adiciona nova unidade para uma rede
     *
     * @param int                      $redes_id Id da rede
     * @param App\Model\Entity\Cliente $cliente  Entidade Cliente
     *
     * @return bool         $cliente   Registro salvo
     */
    public function addClient(int $redes_id, \App\Model\Entity\Cliente $cliente)
    {
        try {

            $redesHasClientes = $this
                ->RedeHasCliente->find('all')
                ->where(
                    [
                        'redes_id' => $redes_id
                    ]
                )->toArray();

            // verifica se tem alguma empresa cadastrada.
            // Se não tiver, esta fica sendo a matriz
            $cliente["matriz"] = sizeof($redesHasClientes) == 0;

            // $cliente["matriz"] = $cliente["matriz"];
            // $cliente["ativado"] = $cliente["ativado"];
            // $cliente['tipo_unidade'] = $cliente['tipo_unidade'];
            // $cliente['nome_fantasia'] = $cliente['nome_fantasia'];
            // $cliente['razao_social'] = $cliente['razao_social'];
            // $cliente['endereco'] = $cliente['endereco'];
            // $cliente['endereco_numero'] = $cliente['endereco_numero'];
            // $cliente['endereco_complemento'] = $cliente['endereco_complemento'];
            // $cliente['bairro'] = $cliente['bairro'];
            // $cliente['municipio'] = $cliente['municipio'];
            // $cliente['estado'] = $cliente['estado'];
            $cliente['cnpj'] = $this->cleanNumber($cliente['cnpj']);
            $cliente['tel_fixo'] = $this->cleanNumber($cliente['tel_fixo']);
            $cliente['tel_celular'] = $this->cleanNumber($cliente['tel_celular']);
            $cliente['tel_fax'] = $this->cleanNumber($cliente['tel_fax']);
            $cliente['cep'] = $this->cleanNumber($cliente['cep']);

            $cliente = $this->save($cliente);

            // salvou o cliente
            if ($cliente) {
                $redesHasCliente = $this->RedeHasCliente->newEntity();

                $redesHasCliente["redes_id"] = $redes_id;
                $redesHasCliente["clientes_id"] = $cliente->id;

                $result = $this->RedeHasCliente->save($redesHasCliente);
            }

            return $result;
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao inserir novo registro: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);

            Log::write("error", $trace);
        }
    }

    #endregion

    #region Read

    /**
     * Obtem todos os clientes
     *
     * @param array $where_conditions condições
     *
     * @return void
     */
    public function getAllClientes(array $where_conditions = array())
    {
        try {
            $conditions = [];

            foreach ($where_conditions as $key => $condition) {
                $conditions[$key] = $condition;
            }

            return $this->find('all')
                ->where($conditions)
                ->order(
                    [
                        'Clientes.id' => 'asc'
                    ]
                )
                ->contain(['ClientesHasUsuarios']);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Obtem todos os clientes
     * Utilizado para Serviços REST
     *
     * @param array $whereConditions Condições de where
     * @param array $usuariosId Id de usuário (Se informado, irá pesquisar pontuações)
     * @param array $orderConditions Condições de ordenação
     * @param array $paginationConditions Condições de Paginação
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 2018-05-13
     *
     * @return array ("count", "data")
     */
    public function getClientes(array $whereConditions = array(), int $usuariosId = null, array $orderConditions = array(), array $paginationConditions = array())
    {
        try {
            $redesHasClientesTable = TableRegistry::get("RedesHasClientes");

            $clientesQuery = $this->_getClientesTable()->find('all')
                ->where($whereConditions);

            $clientesQuery = $clientesQuery->contain(array("RedesHasClientes.Redes"));

            // DebugUtil::print($clientesQuery);

            $count = $clientesQuery->count();

            $clientesId = 0;

            $resumo_gotas = array(
                'total_gotas_adquiridas' => 0,
                'total_gotas_utilizadas' => 0,
                'total_gotas_expiradas' => 0,
                'saldo' => 0
            );

            if (sizeof($clientesQuery->toArray()) > 0) {

                $clientesId = $clientesQuery->first()["id"];

                $redesHasClientesQuery = $redesHasClientesTable->getRedesHasClientesByClientesId($clientesId);

                $pontuacoesTable = TableRegistry::get("Pontuacoes");

                if (!empty($redesHasClientesQuery)) {

                    $redesId = $redesHasClientesQuery->toArray()["redes_id"];

                    $pontuacoesTable = TableRegistry::get("Pontuacoes");

                    $pontuacoesUsuarioRetorno = $pontuacoesTable->getSumPontuacoesOfUsuario($usuariosId, $redesId);

                    $resumo_gotas = $pontuacoesUsuarioRetorno["resumo_gotas"];
                }
            }

            $clientesTodos = $clientesQuery->toArray();
            $clientesAtual = $clientesQuery->toArray();

            $retorno = ResponseUtil::prepareReturnDataPagination($clientesTodos, $clientesAtual, "clientes", $paginationConditions);

            if ($retorno["mensagem"]["status"] == 0) {
                return $retorno;
            }

            if (sizeof($orderConditions) > 0) {
                $clientesQuery = $clientesQuery->order($orderConditions);
            }

            if (sizeof($paginationConditions) > 0) {
                $clientesQuery = $clientesQuery->limit($paginationConditions["limit"])
                    ->page($paginationConditions["page"]);
            }

            $clientesAtual = $clientesQuery->toArray();

            $clientesAtualTemp = array();

            foreach ($clientesAtual as $key => $cliente) {
                $clienteTemp = $cliente;
                $redesId = $cliente->redes_has_cliente->redes_id;
                $clienteTemp["resumo_gotas_cliente"] = $pontuacoesTable->getSumPontuacoesOfUsuario($usuariosId, $redesId, array());

                $clientesAtualTemp[] = $clienteTemp;
            }

            $clientesAtual = $clientesAtualTemp;

            $retorno = ResponseUtil::prepareReturnDataPagination($clientesTodos, $clientesAtual, "clientes", $paginationConditions);

            $retorno["resumo_gotas"] = $resumo_gotas;

            return $retorno;
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringExplode = implode(";", $trace);

            $stringError = __("Erro ao realizar pesquisa de clientes: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4} / Errors: ]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__, $stringExplode);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * ClientesTable::getClientesFromRelationshipRedesUsuarios
     *
     * Obtem todos os clientes que estão interligados à uma rede e um administrador regional
     *
     * @param integer $redesId id da Rede
     * @param integer $usuariosId Id do usuário
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2018/08/05
     *
     * @return App\Model\Entity\Clientes
     */
    public function getClientesFromRelationshipRedesUsuarios(int $redesId, int $usuariosId)
    {
        try {
            $options = array(
                "RHC" => array(
                    "table" => "redes_has_clientes",
                    "alias" => "RHC",
                    "type" => "inner",
                    "foreignKey" => null,
                    "conditions" => array("RHC.clientes_id = Clientes.id")
                ),
                "CHU" => array(
                    "table" => "clientes_has_usuarios",
                    "alias" => "CHU",
                    "type" => "inner",
                    "foreignKey" => "clientes_id",
                    "conditions" => array("RHC.clientes_id = CHU.clientes_id")
                ),
                "U" => array(
                    "table" => "usuarios",
                    "alias" => "U",
                    "type" => "LEFT",
                    "foreignKey" => null,
                    "conditions" => array("CHU.usuarios_id = U.id", "U.id" => $usuariosId)
                )
            );
            $clientes = $this->find("all")
                ->join($options)
                ->where(
                    array(
                        "RHC.redes_id" => $redesId,
                        "CHU.usuarios_id" => $usuariosId,
                    )
                )
                ->select(
                    array(
                        "id",
                        "nome_fantasia",
                        "razao_social"
                    )
                );


            return $clientes->toArray();
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registro: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write('error', $trace);

            return $stringError;
        }
    }

    /**
     * Pega todos os ids de uma rede
     *
     * @return void
     * @author
     *
     * @deprecated 1.0.0
     **/
    public function getAllIdsCliente($clientes_id)
    {
        try {
            $cliente = $this->get($clientes_id);

            $matriz = null;

            // cliente não é matriz?
            if (!is_null($cliente->matriz_id)) {
                // pega quem é matriz
                $matriz = $this->_getClientesTable()->find('all')
                    ->where(
                        [
                            'matriz_id' => $cliente->matriz_id
                        ]
                    )->first();
            } else {
                $matriz = $cliente;
            }

            // Após ter a matriz, retorna lista de ids dos clientes para passar em pontuações

            $array_ids = $this->_getClientesTable()->find('all')
                ->where(['matriz_id' => $matriz->id])
                ->select(['id'])->toArray();

            if (!is_null($cliente->matriz_id)) {
                $array_ids[] = ['id' => $cliente->matriz_id];
            } else {
                $array_ids[] = ['id' => $cliente->id];
            }

            return $array_ids;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao obter registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Obtem cliente pelo id
     *
     * @param int $clientes_id Id de Cliente
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 01/08/2017
     *
     * @return entity $cliente
     *
     **/
    public function getClienteById($clientes_id, $selectFields = array())
    {
        try {

            $cliente = $this
                ->find('all')
                ->where(
                    [
                        'Clientes.id' => $clientes_id
                    ]
                )->contain(['RedeHasCliente', 'RedeHasCliente.Redes', "ClientesHasQuadroHorarios"]);

            if (sizeof($selectFields) > 0) {
                $cliente = $cliente->select($selectFields);
            }

            $cliente = $cliente->first();

            return $cliente;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return ['success' => 'false', 'message' => $stringError];
        }
    }

    /**
     * Obtem cliente pelo id com pontuações do usuário
     *
     * @param int $clientes_id Id de Cliente
     * @param int $usuariosId Id de usuário para pesquisa de pontos
     * @param array $selectFields Campos que serão obtidos do retorno
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 17/07/2018
     *
     * @return entity $cliente
     *
     **/
    public function getClienteByIdWithPoints(int $clientesId, int $usuariosId, array $selectFields = array())
    {
        try {

            $cliente = null;
            $resumo_gotas = array(
                'total_gotas_adquiridas' => 0,
                'total_gotas_utilizadas' => 0,
                'total_gotas_expiradas' => 0,
                'saldo' => 0
            );
            $redesHasClientesTable = TableRegistry::get("RedesHasClientes");

            $redesHasClientesQuery = $redesHasClientesTable->getRedesHasClientesByClientesId($clientesId);

            if (!empty($redesHasClientesQuery)) {

                $redesId = $redesHasClientesQuery->toArray()["redes_id"];

                $pontuacoesTable = TableRegistry::get("Pontuacoes");

                $pontuacoesUsuarioRetorno = $pontuacoesTable->getSumPontuacoesOfUsuario($usuariosId, $redesId);

                $resumo_gotas = $pontuacoesUsuarioRetorno["resumo_gotas"];

                $cliente = $this->_getClientesTable()
                    ->find('all')
                    ->where(
                        [
                            'Clientes.id' => $clientesId
                        ]
                    );

                $cliente = $cliente->contain(array("RedesHasClientes.Redes"));

                if (sizeof($selectFields) > 0) {
                    $cliente = $cliente->select($selectFields);
                }

                $cliente = $cliente->first();
            }

            $mensagem = array(
                "status" => empty($cliente) ? 0 : 1,
                "message" => empty($cliente) ? Configure::read("messageLoadDataWithError") : Configure::read("messageLoadDataWithSuccess"),
                "errors" => empty($cliente) ? array("A consulta não retornou dados!") : array()
            );

            return array("mensagem" => $mensagem, "cliente" => $cliente, "resumo_gotas" => $resumo_gotas);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registro: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write('error', $trace);

            return $stringError;
        }
    }

    /**
     * Clientes::getClienteByCNPJ
     *
     * Obtem cliente a partir de seu CNPJ
     *
     * @param string $cnpj CNPJ
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date   2018/05/07
     *
     * @return \App\Model\Entity\Cliente $cliente
     *
     **/
    public function getClienteByCNPJ(string $cnpj)
    {
        return $this
            ->find('all')
            ->where(array('Clientes.cnpj' => $cnpj))
            ->contain("RedeHasCliente.Redes")
            ->select($this)
            ->select(array("RedeHasCliente.id"))
            ->select($this->RedeHasCliente->Redes)
            ->first();
    }

    /**
     * ClientesTable::getClientesListByRedesId
     *
     * Obtem lista de clientes através de id de rede
     *
     * @param integer $redesId Id da rede
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date   22/06/2018
     *
     * @return @method \App\Model\Entity\Cliente[] Lista de Clientes por Id e Nome Fantasia
     */
    public function getClientesListByRedesId(int $redesId)
    {
        try {

            $redesHasClientesTable = TableRegistry::get("RedesHasClientes");

            $clientesIds = array();

            $redeHasClientesQuery = $redesHasClientesTable->getAllRedesHasClientesIdsByRedesId($redesId);

            foreach ($redeHasClientesQuery as $key => $redeHasCliente) {
                $clientesIds[] = $redeHasCliente["clientes_id"];
            }

            $clientesIds = sizeof($clientesIds) > 0 ? $clientesIds : array(0);

            return $this->find('list')
                ->where(["id in " => $clientesIds]);
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao realizar pesquisa de clientes: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Clientes::getClientesCNPJByEstado
     *
     * Obtem todos os CNPJ cadastrado pelo estado
     *
     * @param string $estado Estado
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date   2018/05/07
     *
     * @return array[] CNPJ's
     *
     **/
    public function getClientesCNPJByEstado(string $estado)
    {
        try {
            return $this->_getClientesTable()
                ->find('all')
                ->where(
                    [
                        'estado' => $estado
                    ]
                )->select(["cnpj"]);
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao realizar pesquisa de CNPJ's de clientes: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Obtem ids de matriz e filiais pelo Cliente ID
     *
     * @param int $clientes_id Id do Cliente
     *
     * @return array Array de Ids
     **/
    public function getIdsMatrizFiliaisByClienteId($clientes_id)
    {
        try {
            $arrayIds = $this->_getClientesTable()->RedesHasClientes->getAllRedesHasClientesIdsByClientesId($clientes_id);

            $array = [];

            // preciso somente dos ids
            foreach ($arrayIds as $key => $value) {
                array_push($array, $value['clientes_id']);
            }

            $arrayIds = $array;

            return $arrayIds;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao atualizar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Retorna Entidade Cliente associado à usuário
     *
     * @return Entity\Cliente $cliente
     * @param Cake\Auth\Storage $usuarioLogado
     * @author Gustavo Souza Gonçalves
     **/
    public function getClienteMatrizLinkedToUsuario($usuarioLogado = null)
    {
        /**
         *  TODO: validar todos os serviços que usam este método
         *
         *  Primeiro problema...
         * Se usuário não estiver na matriz, o registro retornará nulo.
         * Segundo...
         * Em casos como Adm Redes ou Regional, eles possuem mais de uma unidade.
         *
         * Solução:
         * Criar um objeto de session que irá guardar a lista de clientes.
         *
         *
         */
        $matriz = $this->find('all')
            ->where(
                array(
                    "Usuarios.id" => $usuarioLogado["id"],
                    "Cliente.matriz" => 1
                )
            )
            ->contain(
                array(
                    "ClientesHasUsuarios.Cliente",
                    "ClientesHasUsuarios.Usuarios",
                )
            )
            // ->join(
            //     [
            //         'ClientesHasUsuarios' =>
            //             [
            //             'table' => 'clientes_has_usuarios',
            //             'alias' => 'chu',
            //             'type' => 'inner',
            //             'conditions' =>
            //                 [
            //                 'chu.clientes_id = Clientes.id',
            //                 'chu.usuarios_id' => $usuarioLogado['id']
            //             ]

            //         ]
            //     ]
            // )
            ->select([
                'id',
                "matriz",
                'tipo_unidade',
                'codigo_equipamento_rti',
                'nome_fantasia',
                'razao_social',
                'cnpj',
                'tel_fixo',
                'tel_fax',
                'tel_celular',
                'endereco',
                'endereco_numero',
                'endereco_complemento',
                'bairro',
                'municipio',
                'estado',
                'cep',
                'audit_insert',
                'audit_update',
                'ClientesHasUsuarios.id',
                'ClientesHasUsuarios.clientes_id',
                'ClientesHasUsuarios.usuarios_id'
                // 'chu.id',
                // 'chu.clientes_id',
                // 'chu.usuarios_id'
            ]);

        if (sizeof($matriz->toArray()) > 0) {
            return $matriz->first();
        } else {
            return null;
        }
    }

    /**
     * Retorna filiais associadas com matriz
     *
     * @param int $clientes_id Id de cliente
     *
     * @return void
     **/
    public function getClienteFiliais(int $clientes_id, array $where_conditions = array())
    {
        try {
            $conditions = [];

            foreach ($where_conditions as $key => $condition) {
                array_push($conditions, $condition);
            }
            array_push($conditions, ['Clientes.matriz_id' => $clientes_id]);

            return $this->_getClientesTable()
                ->find('all')
                ->where([$conditions])
                ->contain(['ClientesHasUsuarios']);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Retorna filiais associadas com matriz
     *
     * @param int $cliente_id Id de cliente
     *
     * @return void
     **/
    public function getClienteFiliaisIds(int $cliente_id = null)
    {

        try {
            $ids = $this->_getClientesTable()->find('all')
                ->where(['matriz_id' => $cliente_id])
                ->select(['id']);

            $returnIds = [];
            foreach ($ids as $key => $id) {
                array_push($returnIds, $id->id);
            }

            return $returnIds;
        } catch (\Exception $e) { }
    }

    /**
     * Obtêm registro de cliente matriz ligada ao administrador
     * Se usuário não for da matriz, retorna null
     *
     * @return object|null $cliente
     * @author
     **/
    public function getClienteMatrizLinkedToAdmin($usuarioLogado)
    {
        $cliente = $this->_getClientesTable()->find('all')
            ->matching(
                'ClientesHasUsuarios',
                function ($q) use ($usuarioLogado) {
                    return $q->where(['ClientesHasUsuarios.matriz_id' => $usuarioLogado['matriz_id']]);
                }
            )
            ->first();

        return $cliente;
    }

    #endregion

    #region Update

    /**
     * Troca estado de unidade
     *
     * @param int  $id      Id de RedesHasClientes
     * @param bool $ativado Estado de ativação
     *
     * @return \App\Model\Entity\Clientes $cliente
     */
    public function changeStateEnabledCliente(int $id, bool $ativado)
    {
        try {

            $cliente = $this->_getClientesTable()->find('all')
                ->where(['id' => $id])
                ->first();

            $cliente["ativado"] = $ativado;

            return $this->_getClientesTable()->save($cliente);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $object = null;

            foreach ($trace as $key => $item_trace) {
                if ($item_trace['class'] == 'Cake\Database\Query') {
                    $object = $item_trace;
                    break;
                }
            }

            $stringError = __("Erro ao obter registro: {0}, em {1}", $e->getMessage(), $object['file']);

            Log::write('error', $stringError);

            return ['success' => false, 'message' => $stringError];
        }
    }

    /**
     * Update entity in BD
     *
     * @param entity $cliente
     * @return void
     */
    public function updateClient($cliente)
    {
        try {
            $cliente['cnpj'] = $this->cleanNumber($cliente['cnpj']);
            $cliente['tel_fixo'] = $this->cleanNumber($cliente['tel_fixo']);
            $cliente['tel_celular'] = $this->cleanNumber($cliente['tel_celular']);
            $cliente['tel_fax'] = $this->cleanNumber($cliente['tel_fax']);
            $cliente['cep'] = $this->cleanNumber($cliente['cep']);
            $clienteToUpdate = $cliente;
            // $clienteToUpdate = $this->formatClient($cliente);

            return $this->save($clienteToUpdate);
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao inserir novo registro: {0} em: {1}", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            return ['success' => false, 'message' => $stringError];
        }
    }

    #endregion

    #region Delete

    /**
     * Remove todos os clientes
     *
     * @param array $clientes_ids Ids de clientes
     *
     * @return void
     */
    public function deleteClientesByIds(array $clientes_ids)
    {
        try {

            return
                $this->_getClientesTable()->deleteAll(['id in' => $clientes_ids]);
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

    #endregion


    /* ------------------------ Others ------------------------ */

    /**
     * Prepara cliente para atualização no BD
     * @param entity $cliente
     * @return Entity\Cliente
     */
    public function formatClient($cliente)
    {
        echo __LINE__;
        DebugUtil::print($cliente);
        $cliente["matriz"] = $cliente["matriz"];
        $cliente["ativado"] = $cliente["ativado"];
        $cliente['tipo_unidade'] = $cliente['tipo_unidade'];
        $cliente['nome_fantasia'] = $cliente['nome_fantasia'];
        $cliente['razao_social'] = $cliente['razao_social'];
        $cliente['endereco'] = $cliente['endereco'];
        $cliente['endereco_numero'] = $cliente['endereco_numero'];
        $cliente['endereco_complemento'] = $cliente['endereco_complemento'];
        $cliente['bairro'] = $cliente['bairro'];
        $cliente['municipio'] = $cliente['municipio'];
        $cliente['estado'] = $cliente['estado'];
        $cliente['cnpj'] = $this->cleanNumber($cliente['cnpj']);
        $cliente['tel_fixo'] = $this->cleanNumber($cliente['tel_fixo']);
        $cliente['tel_celular'] = $this->cleanNumber($cliente['tel_celular']);
        $cliente['tel_fax'] = $this->cleanNumber($cliente['tel_fax']);
        $cliente['cep'] = $this->cleanNumber($cliente['cep']);

        // if (isset($cliente['matriz_id'])) {
        //     $cliente['matriz_id'] = $cliente['matriz_id'];
        // }

        return $cliente;
    }

    /**
     * Remove todos os caracteres não numéricos para guardar sem formatação no BD
     * @param String CNPJ
     */
    public function cleanNumber($value)
    {
        return preg_replace('/[^0-9]/', "", $value);
    }
}
