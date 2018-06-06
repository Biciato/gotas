<?php
namespace App\Model\Table;

use ArrayObject;
use Cake\Event\Event;
use Cake\Log\Log;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Core\Configure;

/**
 * ClientesHasBrindesHabilitadosPreco Model
 *
 * @property \App\Model\Table\ClientesHasBrindesHabilitadosTable|\Cake\ORM\Association\BelongsTo $ClientesHasBrindesHabilitados
 *
 * @method \App\Model\Entity\ClientesHasBrindesHabilitadosPreco get($primaryKey, $options = [])
 * @method \App\Model\Entity\ClientesHasBrindesHabilitadosPreco newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ClientesHasBrindesHabilitadosPreco[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ClientesHasBrindesHabilitadosPreco|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ClientesHasBrindesHabilitadosPreco patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ClientesHasBrindesHabilitadosPreco[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ClientesHasBrindesHabilitadosPreco findOrCreate($search, callable $callback = null, $options = [])
 */
class ClientesHasBrindesHabilitadosPrecoTable extends GenericTable
{

    /**
     * -------------------------------------------------------------
     * Fields
     * -------------------------------------------------------------
     */
    protected $clientesHasBrindesHabilitadosPrecoTable = null;

    /**
     * -------------------------------------------------------------
     * Properties
     * -------------------------------------------------------------
     */

    /**
     * Method get of brinde table property
     * 
     * @return (Cake\ORM\Table) Table object
     */
    private function _getClientesHasBrindesHabilitadosPrecoTable()
    {
        if (is_null($this->clientesHasBrindesHabilitadosPrecoTable)) {
            $this->_setClientesHasBrindesHabilitadosPrecoTable();
        }
        return $this->clientesHasBrindesHabilitadosPrecoTable;
    }

    /**
     * Method set of brinde table property
     * 
     * @return void
     */
    private function _setClientesHasBrindesHabilitadosPrecoTable()
    {
        $this->clientesHasBrindesHabilitadosPrecoTable = TableRegistry::get('ClientesHasBrindesHabilitadosPreco');
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

        $this->setTable('clientes_has_brindes_habilitados_preco');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('ClientesHasBrindesHabilitados', [
            'foreignKey' => 'clientes_has_brindes_habilitados_id',
            'joinType' => 'INNER'
        ]);

        $this->belongsTo('Clientes', [
            'className' => 'Clientes',
            'foreignKey' => 'clientes_id',
            'joinType' => 'INNER'
        ]);
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
            ->decimal('preco')
            ->requirePresence('preco', 'create')
            ->notEmpty('preco');

        $validator
            ->dateTime('data_preco')
            ->requirePresence('data_preco', 'create')
            ->notEmpty('data_preco');

        $validator
            ->integer('status_autorizacao');

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
        $rules->add($rules->existsIn(['clientes_has_brindes_habilitados_id'], 'ClientesHasBrindesHabilitados'));
        $rules->add($rules->existsIn(['clientes_id'], 'Clientes'));

        return $rules;
    }

    /**
     * -------------------------------------------------------------
     * Methods
     * -------------------------------------------------------------
     */


    /* ------------------------ Create ------------------------ */

    /**
     * Add a Preço for a BrindeHabilitado
     *
     * @param int $clientesHasBrindesHabilitadosId
     * @param int $clientes_id
     * @param int $preco_padrao
     * 
     * @return (entity\ClientesHasBrindesHabilitadosPreco) $entity
     **/
    public function addBrindeHabilitadoPreco($clientesHasBrindesHabilitadosId, $clientes_id, $preco_padrao, $status_autorizacao)
    {
        try {
            $brindeHabilitadoPreco = $this->_getClientesHasBrindesHabilitadosPrecoTable()->newEntity();

            $brindeHabilitadoPreco->clientes_has_brindes_habilitados_id = $clientesHasBrindesHabilitadosId;
            $brindeHabilitadoPreco->clientes_id = $clientes_id;
            $brindeHabilitadoPreco->preco = $preco_padrao;
            $brindeHabilitadoPreco->status_autorizacao = $status_autorizacao;
            $brindeHabilitadoPreco->data_preco = date('Y-m-d H:i:s');

            return $this->_getClientesHasBrindesHabilitadosPrecoTable()->save($brindeHabilitadoPreco);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao inserir registro: {0} em: {1}", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }
    
    /* ------------------------ Read ------------------------ */

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function getPrecoBrindeById($id)
    {
        try {
            return $this->_getClientesHasBrindesHabilitadosPrecoTable()->get($id);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao obter registro: {0} em: {1}", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Obtem todos os preços para o brinde habilitado id
     *
     * @param int $clientesHasBrindesHabilitadosId Id de brinde habilitado
     *
     * @return entity $preco
     **/
    public function getAllPrecoForBrindeHabilitadoId(int $clientesHasBrindesHabilitadosId, array $whereConditions = [], int $qteRegistros = null)
    {
        try {

            $conditionsSql = array();

            foreach ($whereConditions as $key => $value) {
                $conditionsSql[] = $value;
            }

            $conditionsSql[] = ['clientes_has_brindes_habilitados_id' => $clientesHasBrindesHabilitadosId];

            $data = $this->_getClientesHasBrindesHabilitadosPrecoTable()->find('all')
                ->where($conditionsSql)
                ->contain(['ClientesHasBrindesHabilitados']);


            if (!is_null($qteRegistros)) {
                $data = $data->limit($qteRegistros);
            }

            return $data;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao obter registro: {0} em: {1}", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Obtem último Preço para Brinde Habilitado
     *
     * @param int $clientesHasBrindesHabilitadosId Id de clientes has brindes habilitados
     *
     * @return entity $preco
     **/
    public function getLastPrecoForBrindeHabilitadoId(int $clientesHasBrindesHabilitadosId, array $where_conditions = [])
    {
        try {

            $conditions = [];

            foreach ($where_conditions as $key => $value) {
                array_push($conditions, $value);
            }

            array_push($conditions, ['clientes_has_brindes_habilitados_id' => $clientesHasBrindesHabilitadosId]);

            return $this->_getClientesHasBrindesHabilitadosPrecoTable()->find('all')
                ->where($conditions)
                ->order(['ClientesHasBrindesHabilitadosPreco.id' => 'desc'])
                ->contain(['ClientesHasBrindesHabilitados'])->first();
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao obter registro: {0} em: {1}", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Obtem preço aguardando autorização
     *
     * @param array $array_clientes_id Lista de array de ids
     *
     * @return array $preços lista de registros
     */
    public function getPrecoAwaitingAuthorizationByClientesId(array $array_clientes_id = [])
    {
        try {
            return $this->_getClientesHasBrindesHabilitadosPrecoTable()->find('all')
                ->where(
                    [
                        'status_autorizacao' => (int)Configure::read('giftApprovalStatus')['AwaitingAuthorization'],
                        'ClientesHasBrindesHabilitadosPreco.clientes_id IN' => $array_clientes_id
                    ]
                )
                ->order(
                    [
                        'data_preco' => 'desc'
                    ]
                )
                ->contain(
                    [
                        'Clientes', 'ClientesHasBrindesHabilitados', 'ClientesHasBrindesHabilitados.Brindes'
                    ]
                );
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao obter registro: {0} em: {1}", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
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
    public function setClientesHasBrindesHabilitadosPrecoToMainCliente(int $clientes_id, int $matriz_id)
    {
        try {
            return $this->updateAll(
                [
                    'clientes_id' => $matriz_id
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
    public function deleteAllClientesHasBrindesHabilitadosPrecoByClientesIds(array $clientes_ids)
    {
        try {
            return $this->_getClientesHasBrindesHabilitadosPrecoTable()
                ->deleteAll(
                    [
                        'clientes_id in' => $clientes_ids
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


}
