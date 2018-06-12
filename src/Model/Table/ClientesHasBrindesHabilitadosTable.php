<?php
namespace App\Model\Table;

use ArrayObject;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Log\Log;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * ClientesHasBrindesHabilitados Model
 *
 * @property \App\Model\Table\ClientesTable|\Cake\ORM\Association\BelongsTo $Clientes
 * @property \App\Model\Table\BrindesHabilitadosTable|\Cake\ORM\Association\BelongsTo $BrindesHabilitados
 *
 * @method \App\Model\Entity\ClientesHasBrindesHabilitado get($primaryKey, $options = [])
 * @method \App\Model\Entity\ClientesHasBrindesHabilitado newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ClientesHasBrindesHabilitado[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ClientesHasBrindesHabilitado|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ClientesHasBrindesHabilitado patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ClientesHasBrindesHabilitado[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ClientesHasBrindesHabilitado findOrCreate($search, callable $callback = null, $options = [])
 */
class ClientesHasBrindesHabilitadosTable extends GenericTable
{

    /**
     * -------------------------------------------------------------
     * Fields
     * -------------------------------------------------------------
     */
    protected $clientesHasBrindesHabilitadosTable = null;

    protected $clientesHasBrindesHabilitadosQuery = null;


    /**
     * -------------------------------------------------------------
     * Properties
     * -------------------------------------------------------------
     */

    /**
     * Method get of brinde table property
     *
     * @return Cake\ORM\Table Table object
     */
    private function _getClientesHasBrindesHabilitadosTable()
    {
        if (is_null($this->clientesHasBrindesHabilitadosTable)) {
            $this->_setClientesHasBrindesHabilitadosTable();
        }
        return $this->clientesHasBrindesHabilitadosTable;
    }

    /**
     * Method set of brinde table property
     * @return void
     */
    private function _setClientesHasBrindesHabilitadosTable()
    {
        $this->clientesHasBrindesHabilitadosTable = TableRegistry::get('ClientesHasBrindesHabilitados');
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

        $this->setTable('clientes_has_brindes_habilitados');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Clientes', [
            'foreignKey' => 'clientes_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('ClientesHasBrindesHabilitados', [
            'foreignKey' => 'clientes_has_brindes_habilitados_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Brindes', [
            'foreignKey' => 'brindes_id',
            'joinType' => 'INNER'
        ]);

        $this->hasOne(
            'BrindeHabilitadoPrecoAtual',
            [
                'className' => 'ClientesHasBrindesHabilitadosPreco',
                'foreignKey' => 'clientes_has_brindes_habilitados_id',
                // 'joinType' => 'inner',
                'strategy' => 'select',
                'conditions' => ['status_autorizacao' => 1],
                'order' => ['id' => 'desc']
            ]
        );

                    //    $this->hasOne('BrindeHabilitadoPrecoAtual', [
                    //    'className' => 'ClientesHasBrindesHabilitadosPreco',
                    //    'foreignKey' => 'clientes_has_brindes_habilitados_id',
                    //    'strategy' => 'select',
                    //    'sort' => ['data_preco' => 'desc'],
                    //    'conditions' => ['status_autorizacao' => 1]
                    //    ]);

        $this->hasMany(
            'BrindesHabilitadosUltimosPrecos',
            [
                'className' => 'ClientesHasBrindesHabilitadosPreco',
                'foreignKey' => 'clientes_has_brindes_habilitados_id',
                'joinType' => 'INNER',
                'sort' => ['data_preco' => 'desc']
            ]
        );

        $this->hasOne(
            'BrindesEstoqueAtual',
            [
                'className' => 'ClientesHasBrindesEstoque',
                'foreignKey' => 'clientes_has_brindes_habilitados_id',
                'joinType' => 'INNER'
            ]
        );

        $this->hasMany(
            'Pontuacoes',
            [
                'foreignKey' => 'clientes_has_brindes_habilitados_id',
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
            ->notEmpty('brindes_id', 'create');

        $validator
            ->notEmpty('clientes_id', 'create');

        $validator
            ->notEmpty('habilitado', 'create');

        $validator
            ->notEmpty('tipo_codigo_barras', 'create');

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
        $rules->add($rules->existsIn(['clientes_has_brindes_habilitados_id'], 'ClientesHasBrindesHabilitados'));

        return $rules;
    }

    /**
     * -------------------------------------------------------------
     * Methods
     * -------------------------------------------------------------
     */

     /* ------------------------ Create ------------------------ */

    /**
     * Add a BrindeHabilitado for a Cliente
     *
     * @param int $clientes_id
     * @param int $brindes_habilitados_id
     * @return entity\ClienteHasBrindesHabilitados $entity
     * @author
     **/
    public function addClienteHasBrindeHabilitado($clientes_id, $brindes_id)
    {
        try {
            $clienteHasBrindeHabilitado = $this->_getClientesHasBrindesHabilitadosTable()->newEntity();

            $clienteHasBrindeHabilitado->clientes_id = $clientes_id;
            $clienteHasBrindeHabilitado->brindes_id = $brindes_id;
            $clienteHasBrindeHabilitado->habilitado = true;

            return $this->_getClientesHasBrindesHabilitadosTable()->save($clienteHasBrindeHabilitado);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao inserir registro: {0} em: {1}", $e->getMessage(), $trace);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }


    /* ------------------------ Read ------------------------ */

    /**
     * Obtêm um Brinde Habilitado para Cliente através do Brindes Id
     *
     * @param int $id id do brinde
     *
     * @return entity\ClientesHasBrindesHabilitados $entity
     **/
    public function getBrindeHabilitadoById($id)
    {
        try {
            $brinde = $this->_getClientesHasBrindesHabilitadosTable()->find('all')->where(['ClientesHasBrindesHabilitados.id' => $id])
                ->contain(['Clientes', 'Brindes', 'BrindeHabilitadoPrecoAtual'])
                ->first();

            // Cálculo de estoque do item
            $queryEntrada = $this->_getClientesHasBrindesHabilitadosTable()->BrindesEstoqueAtual->find();
            $querySaidaBrinde = $this->_getClientesHasBrindesHabilitadosTable()->BrindesEstoqueAtual->find();
            $querySaidaVenda = $this->_getClientesHasBrindesHabilitadosTable()->BrindesEstoqueAtual->find();
            $queryDevolucao = $this->_getClientesHasBrindesHabilitadosTable()->BrindesEstoqueAtual->find();

            $resultEntrada = $queryEntrada->select([
                'sum' => $queryEntrada->func()->sum('quantidade')
            ])->where(['tipo_operacao' => 0, 'clientes_has_brindes_habilitados_id' => $id])->first();

            $resultSaidaBrinde = $querySaidaBrinde->select([
                'sum' => $querySaidaBrinde->func()->sum('quantidade')
            ])->where(['tipo_operacao' => 1, 'clientes_has_brindes_habilitados_id' => $id])->first();

            $resultSaidaVenda = $querySaidaVenda->select([
                'sum' => $querySaidaVenda->func()->sum('quantidade')
            ])->where(['tipo_operacao' => 2, 'clientes_has_brindes_habilitados_id' => $id])->first();

            $resultDevolucao = $queryDevolucao->select([
                'sum' => $queryDevolucao->func()->sum('quantidade')
            ])->where(['tipo_operacao' => 3, 'clientes_has_brindes_habilitados_id' => $id])->first();


            $entrada = is_null($resultEntrada['sum']) ? 0 : $resultEntrada['sum'];
            $saidaBrinde = is_null($resultSaidaBrinde['sum']) ? 0 : $resultSaidaBrinde['sum'];
            $saidaVenda = is_null($resultSaidaVenda['sum']) ? 0 : $resultSaidaVenda['sum'];
            $devolucao = is_null($resultDevolucao['sum']) ? 0 : $resultDevolucao['sum'];

            $estoque = [($entrada + $devolucao) - ($saidaBrinde + $saidaVenda)];

            $brinde['estoque'] = $estoque;

            return $brinde;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registros: {0} em: {1}", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Obtêm brinde habilitado
     *
     * @param array $where_conditions Condições de pesquisa
     *
     * @return App\Model\Entity\ClientesHasBrindesHabilitado
     **/
    public function getBrindeHabilitadoByBrindeId(array $where_conditions = [])
    {
        try {
            $brinde = $this->_getClientesHasBrindesHabilitadosTable()->find('all')->where(
                $where_conditions
            )
                ->contain(['Brindes'])->first();

            return $brinde;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registros: {0} em: {1}", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Obtêm todos os brindes de um cliente conforme tipo
     *
     * @param int  $clientes_id            Id de CLiente
     * @param bool $equipamento_rti_shower Se é Smart Shower ou não
     *
     * @return App\Model\Entity\ClientesHasBrindesHabilitado
     */
    public function getAllGiftsClienteId(int $clientes_id, bool $equipamento_rti_shower = null)
    {
        try {
            $whereConditions = [];

            if (!is_null($equipamento_rti_shower)) {
                $whereConditions[] = ['Brindes.equipamento_rti_shower' => $equipamento_rti_shower];
            }

            $whereConditions[] = ['ClientesHasBrindesHabilitados.habilitado' => true];
            $whereConditions[] = ['ClientesHasBrindesHabilitados.clientes_id' => $clientes_id];

            $brindes = $this->_getClientesHasBrindesHabilitadosTable()->find('all')
                ->where($whereConditions)->contain(['Brindes', 'Clientes']);

            $brinde_habilitado_preco_table = TableRegistry::get('ClientesHasBrindesHabilitadosPreco');


            $brindes_array = [];
            foreach ($brindes as $key => $value) {
                $value['brinde_habilitado_preco_atual'] = $brinde_habilitado_preco_table->find('all')->where(
                    [
                        'status_autorizacao' => (int)Configure::read('giftApprovalStatus')['Allowed'],
                        'clientes_has_brindes_habilitados_id' => $value->id

                    ]
                )->order(['id' => 'DESC'])
                    ->first();

                $brindes_array[] = $value;
            }

            return $brindes_array;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registros: {0} em: {1}", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Obtem todos os brindes habilitados para cliente usando o Clientes Id
     *
     * @param array $clientes_ids     Ids de cliente
     * @param array $where_conditions Condições Extras
     *
     * @return entity\ClientesHasBrindesHabilitados $entity
     */
    public function getBrindesHabilitadosByClienteId(array $clientes_ids, array $where_conditions = [])
    {
        try {
            $conditions = [];

            array_push($conditions, ['Brindes.habilitado' => true]);

            foreach ($where_conditions as $key => $value) {
                array_push($conditions, $value);
            }

            $brindes_cliente = $this->_getClientesHasBrindesHabilitadosTable()->find('all')
                ->where($where_conditions)
                ->contain(
                    [
                        'Brindes' =>
                            [
                            'strategy' => 'join',
                            'queryBuilder' => function ($q) use ($clientes_ids) {
                                return $q->where(
                                    [
                                        'ClientesHasBrindesHabilitados.clientes_id IN ' => $clientes_ids,
                                    ]
                                );
                            }
                        ],
                        'Clientes',
                        'BrindeHabilitadoPrecoAtual'
                    ]
                )
                ->order(['ClientesHasBrindesHabilitados.id' => 'asc']);

            return $brindes_cliente;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registros: {0} em: {1}", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Obtem todos os brindes habilitados (ou não) para cliente usando o Clientes Id
     *
     * @param array $clientes_ids     Ids de cliente
     * @param array $where_conditions Condições Extras
     *
     * @return entity\ClientesHasBrindesHabilitados $entity
     */
    public function getTodosBrindesByClienteId(array $clientes_ids, array $where_conditions = [])
    {
        try {
            $conditions = [];

            // obtem todas as unidades que estão vinculada à aquela unidade

            $redeHasClienteTable = TableRegistry::get("RedesHasClientes");

            // Cliente que quero aplicar a configuração
            $clienteAplicarConfiguracaoId = $clientes_ids[0];

            $clientesIdsQuery = $redeHasClienteTable->getAllRedesHasClientesIdsByClientesId($clienteAplicarConfiguracaoId);
            $clientesIds = array();

            foreach ($clientesIdsQuery as $key => $value) {
                $clientesIds[] = $value["clientes_id"];
            }

            /**
             * Obtem os brindes daquela rede
             */
            $brindeTable = TableRegistry::get("Brindes");
            $brindesRede = $brindeTable->find('all')
                ->where(array(
                    "habilitado" => 1,
                    "clientes_id in " => $clientesIds
                ))->toArray();

            // Obtem os ids dos brindes da rede

            $brindesRedeIds = array();

            foreach ($brindesRede as $key => $brinde) {
                $brindesRedeIds[] = $brinde["id"];
            }

            /**
             * Brindes não configurados será iterado e aquele que constar na lista será removido.
             * em contrapartida, aquele removido será adicionado em Brindes Configurados Ids
             */
            $brindesConfiguradosIds = array();
            $brindesNaoConfiguradosIds = $brindesRedeIds;

            // Obtem todos os brindes que estão configurados para aquela unidade.

            $brindesAtribuidos = $this->_getClientesHasBrindesHabilitadosTable()->find("all")
                ->where(
                    array(
                        "brindes_id in " => $brindesRedeIds,
                        "clientes_id" => $clienteAplicarConfiguracaoId
                    )
                )->toArray();

            foreach ($brindesAtribuidos as $key => $brindeAtribuido) {
                if (in_array($brindeAtribuido["brindes_id"], $brindesNaoConfiguradosIds)) {
                    $brindesConfiguradosIds[] = $brindeAtribuido["brindes_id"];
                }
            }

            $brindesNaoAtribuidos = $brindeTable->find('all')
                ->where(
                    array(
                        "id not in " => $brindesConfiguradosIds,
                        "clientes_id in " => $clientesIds,
                        "clientes_id != " => $clienteAplicarConfiguracaoId
                    )
                )->toArray();

            $brindesNaoVinculados = array();

            $brindesVinculados = array();

            foreach ($brindesRede as $key => $brinde) {
                foreach ($brindesConfiguradosIds as $key => $brindeConfiguradoId) {
                    if ($brinde["id"] == $brindeConfiguradoId) {
                        $clienteBrindeHabilitado = $this->_getClientesHasBrindesHabilitadosTable()->find('all')
                            ->where(
                                array(
                                    "brindes_id" => $brinde["id"],
                                    "clientes_id" => $clienteAplicarConfiguracaoId
                                )
                            )->first();

                        $item = $brinde;
                        $item["brindeVinculado"] = $clienteBrindeHabilitado;
                        $item["atribuido"] = 1;
                        $brindesVinculados[] = $item;
                    }
                }
            }

            foreach ($brindesNaoAtribuidos as $key => $brinde) {
                foreach ($brindesNaoConfiguradosIds as $key => $brindeNaoConfiguradoId) {
                    if ($brinde["id"] == $brindeNaoConfiguradoId) {
                        $item = $brinde;
                        $item["brindeVinculado"] = null;
                        $item["atribuido"] = 0;
                        $brindesNaoVinculados[] = $item;
                    }
                }
            }
            return array_merge($brindesVinculados, $brindesNaoVinculados);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registros: {0} em: {1}", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /* ------------------------ Update ------------------------ */

    /**
     * Define todos os preços de brindes habilitados de um cliente para a matriz
     *
     * @param int $clientes_id Id de Cliente
     * @param int $matriz_id   Id da Matriz
     *
     * @return boolean
     */
    public function setClientesHasBrindesHabilitadosToMainCliente(int $clientes_id, int $matriz_id)
    {
        try {
            return $this->updateAll(
                [
                    'clientes_id' => $matriz_id,
                    'habilitado' => false
                ],
                [
                    'clientes_id' => $clientes_id
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

    /* ------------------------ Delete ------------------------ */

    /**
     * Apaga todos os preços para brindes habilitados de um cliente
     *
     * @param array $clientes_ids Ids de clientes
     *
     * @return boolean
     */
    public function deleteAllClientesHasBrindesHabilitadosByClientesIds(array $clientes_ids)
    {

        try {
            return $this->_getClientesHasBrindesHabilitadosTable()
                ->deleteAll(['clientes_id in' => $clientes_ids]);
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
}
