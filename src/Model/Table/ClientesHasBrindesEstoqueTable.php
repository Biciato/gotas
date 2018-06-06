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

/**
 * ClientesHasBrindesEstoque Model
 *
 * @property \App\Model\Table\BrindesTable|\Cake\ORM\Association\BelongsTo $Brindes
 * @property \App\Model\Table\UsuariosTable|\Cake\ORM\Association\BelongsTo $Usuarios
 *
 * @method \App\Model\Entity\BrindesEstoque get($primaryKey, $options = [])
 * @method \App\Model\Entity\BrindesEstoque newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\BrindesEstoque[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\BrindesEstoque|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\BrindesEstoque patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\BrindesEstoque[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\BrindesEstoque findOrCreate($search, callable $callback = null, $options = [])
 */
class ClientesHasBrindesEstoqueTable extends GenericTable
{

    /**
     * -------------------------------------------------------------
     * Fields
     * -------------------------------------------------------------
     */
    protected $clientesHasBrindesEstoqueTable = null;

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
    private function _getClientesHasBrindesEstoqueTable()
    {
        if (is_null($this->clientesHasBrindesEstoqueTable)) {
            $this->_setClientesHasBrindesEstoqueTable();
        }
        return $this->clientesHasBrindesEstoqueTable;
    }

    /**
     * Method set of brinde table property
     *
     * @return void
     */
    private function _setClientesHasBrindesEstoqueTable()
    {
        $this->clientesHasBrindesEstoqueTable = TableRegistry::get('ClientesHasBrindesEstoque');
    }

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     *
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('clientes_has_brindes_estoque');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo(
            'Brindes',
            [
                'foreignKey' => 'brindes_id',
                'joinType' => 'INNER'
            ]
        );
        $this->belongsTo(
            'Usuarios',
            [
                'foreignKey' => 'usuarios_id'
            ]
        );

        $this->belongsTo(
            'ClientesHasBrindesHabilitados',
            [
                'className' => 'ClientesHasBrindesHabilitados',
                'foreignKey' => 'id',
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
            ->integer('quantidade')
            ->requirePresence('quantidade', 'create')
            ->notEmpty('quantidade');

        $validator
            ->integer('tipo_operacao')
            ->requirePresence('tipo_operacao', 'create')
            ->notEmpty('tipo_operacao');

        $validator
            ->dateTime('data')
            ->requirePresence('data', 'create')
            ->notEmpty('data');

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
        // $rules->add($rules->existsIn(['brindes_id'], 'Brindes'));
        // $rules->add($rules->existsIn(['usuarios_id'], 'Usuarios'));

        return $rules;
    }

    /**
     * -------------------------------------------------------------
     * Methods
     * -------------------------------------------------------------
     */

    /* ------------------------ Create ------------------------ */

    /**
     * Add Estoque for Brinde Id
     *
     * @param int $clientes_has_brindes_habilitados_id
     * @param int $usuarios_id
     * @param int $quantidade
     * @param int $tipo_operacao (0: Entrada estoque, 1: Saída tipo Brinde, 2: Saída tipo Venda, 3: Devolução)
     * @param int $id clientesHasBrindesEstoqueId
     *
     * @return void
     **/
    public function addEstoqueForBrindeId($clientes_has_brindes_habilitados_id, $usuarios_id, $quantidade, $tipo_operacao, $id = null)
    {
        try {
            $estoque = null;

            if (is_null($id)) {
                $estoque = $this->_getClientesHasBrindesEstoqueTable()->newEntity();
            } else {
                $estoque = $this->_getClientesHasBrindesEstoqueTable()->get($id);
            }

            $estoque->clientes_has_brindes_habilitados_id = $clientes_has_brindes_habilitados_id;
            $estoque->usuarios_id = $usuarios_id;
            $estoque->quantidade = $quantidade;
            $estoque->tipo_operacao = $tipo_operacao;

            return $this->_getClientesHasBrindesEstoqueTable()->save($estoque);

        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registros: {0} em: {1}", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /* ------------------------ Read ------------------------ */

    /**
     * Get one Brinde Habilitado For Cliente using Brindes Id
     *
     * @param int $clientes_has_brindes_habilitados_id Id de Brinde Habilitado
     * 
     * @return (entity\ClientesHasBrindesHabilitados) $entity
     **/
    public function getEstoqueForBrindeId($clientes_has_brindes_habilitados_id, $quantidade = null, array $whereConditions = [], int $qteRegistros = 10)
    {
        try {

            $conditions = [];

            array_push($conditions, ['clientes_has_brindes_habilitados_id' => $clientes_has_brindes_habilitados_id]);

            foreach ($whereConditions as $key => $value) {
                $conditions[] = $value;
            }

            if (!is_null($quantidade)) {
                array_push($conditions, ['quantidade' => $quantidade]);
            }

            $estoque = $this->_getClientesHasBrindesEstoqueTable()->find('all')
                ->contain(['Usuarios'])
                ->where($conditions);


                echo ($quantidade);
            if (!is_null($quantidade)) {
                $estoque = $estoque->first();
            }

            if (!is_null($estoque)) {
                if (!is_null($qteRegistros)) {
                    $estoque = $estoque->limit($qteRegistros);
                }
            }

            return $estoque;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registros: {0} em: {1}", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Get total sum of brindes stock
     *
     * @param int $clientes_has_brindes_habilitados_id

     * @return int sum of brinde stock
     **/
    public function getEstoqueAtualForBrindeId($clientes_has_brindes_habilitados_id)
    {
        try {
            $query = $this->_getClientesHasBrindesEstoqueTable()->find();

            $queryResult = $query->select(['sum' => $query->func()->sum('quantidade')])
                ->where(['clientes_has_brindes_habilitados_id' => $clientes_has_brindes_habilitados_id])->first();

            return $queryResult['sum'];

        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registros: {0} em: {1}", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Verifica se tem estoque para o brinde
     *
     * @param int $clientes_has_brindes_habilitados_id Id do Brinde Habilitado
     * @param int $checkout_ammount                    Quantidade de saída
     *
     * @return array
     */
    public function checkBrindeHasEstoqueByBrindesHabilitadosId(int $clientes_has_brindes_habilitados_id, int $checkout_ammount)
    {
        $left = $this->getEstoqueAtualForBrindeId($clientes_has_brindes_habilitados_id);

        return ['enough' => $left > $checkout_ammount, 'left' => $left];

    }

    /* ------------------------ Update ------------------------ */

    /* ------------------------ Delete ------------------------ */

    /**
     * Apaga todas as gotas de clientes
     *
     * @param array $clientes_ids Ids de clientes
     *
     * @return boolean
     */
    public function deleteAllClientesHasBrindesEstoqueByClientesIds(array $clientes_ids)
    {
        try {

            $clientes_has_brindes_habilitados_id = $this->_getClientesHasBrindesEstoqueTable()->ClientesHasBrindesHabilitados->find('all')
                ->where(['clientes_id in' => $clientes_ids])->select(['id']);

            $clientes_has_brindes_habilitados_ids = [];

            foreach ($clientes_has_brindes_habilitados_id as $key => $value) {
                array_push($clientes_has_brindes_habilitados_ids, $value['id']);
            }

            if (sizeof($clientes_has_brindes_habilitados_ids) > 0) {
                return $this->_getClientesHasBrindesEstoqueTable()
                    ->deleteAll(['clientes_has_brindes_habilitados_id in' => $clientes_has_brindes_habilitados_ids]);
            } else {
                return true;
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

            $stringError = __("Erro ao buscar registro: {0}, em {1}", $e->getMessage(), $object['file']);

            Log::write('error', $stringError);

            $error = ['result' => false, 'message' => $stringError];
            return $error;
        }
    }
}
