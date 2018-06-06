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

/**
 * Gotas Model
 *
 * @property \App\Model\Table\GotasTable|\Cake\ORM\Association\BelongsTo $Gotas
 *
 * @method \App\Model\Entity\Gota get($primaryKey, $options = [])
 * @method \App\Model\Entity\Gota newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Gota[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Gota|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Gota patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Gota[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Gota findOrCreate($search, callable $callback = null, $options = [])
 */
class GotasTable extends GenericTable
{

    /**
     * -----------------------------------------------------
     * Fields
     * -----------------------------------------------------
     */
    protected $gotasTable = null;

    /**
     * -----------------------------------------------------
     * Properties
     * -----------------------------------------------------
     */

    /**
     * Method get of client table property
     * @return (Cake\ORM\Table) Table object
     */
    private function _getGotasTable()
    {
        if (is_null($this->gotasTable)) {
            $this->_setGotasTable();
        }
        return $this->gotasTable;
    }

    /**
     * Method set of client table property
     * @return void
     */
    private function _setGotasTable()
    {
        $this->gotasTable = TableRegistry::get('Gotas');
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

        $this->setTable('gotas');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo(
            'Clientes',
            [
                'foreignKey' => 'clientes_id',
                'joinType' => 'INNER'
            ]
        );
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     *
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('nome_parametro')
            ->notEmpty('nome_parametro');

        $validator
            ->decimal('multiplicador_gota')
            ->requirePresence('multiplicador_gota', 'create')
            ->notEmpty('multiplicador_gota');

        $validator
            ->integer('habilitado');

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
        // $rules->add($rules->existsIn(['gotas_id'], 'Gotas'));

        return $rules;
    }

    /* ------------------------ Create ------------------------ */

    /**
     * Cria uma nova gota
     *
     * @param int    $clientes_id        Id de Cliente
     * @param string $nome_parametro     Nome da gota
     * @param float  $multiplicador_gota Multiplicador da gota
     *
     * @return boolean Registro gravado
     */
    public function createGota(int $clientes_id, string $nome_parametro, float $multiplicador_gota)
    {
        try {
            $gota = $this->_getGotasTable()->newEntity();

            $gota->clientes_id = $clientes_id;
            $gota->nome_parametro = $nome_parametro;
            $gota->multiplicador_gota = $multiplicador_gota;

            return $this->_getGotasTable()->save($gota);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /* ------------------------ Read ------------------------ */

    /**
     * Encontra todas as 'gotas' de clientes
     *
     * @param array $clientesIds     Id de Cliente
     * @param array $whereConditions Condições de pesquisa
     *
     * @return (entity\Gotas)[] $gotas
     **/
    public function findGotasByClientesId(array $clientesIds = [], array $whereConditions = [])
    {
        try {
            $conditionsSql = array();

            $conditionsSql[] = [
                'clientes_id IN ' => $clientesIds
            ];

            foreach ($whereConditions as $key => $value) {
                $conditionsSql[] = $value;
            }

            return $this->_getGotasTable()
                ->find('all')
                ->where($conditionsSql)
                ->contain(['Clientes']);

        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Encontra todas as 'gotas' de um cliente
     *
     * @param int $clientes_id Id de Cliente
     *
     * @return (entity\Gotas)[] $gotas
     **/
    public function findGotasEnabledByClientesId(int $clientes_id)
    {
        try {
            return $this->_getGotasTable()->find('all')
                ->where(
                    [
                        'clientes_id' => $clientes_id,
                        'habilitado' => true,
                    ]
                );
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Obtêm todas as gotas para a rede especificada pelo Id
     *
     * @param array $clientesIds     Ids de Cliente
     * @param array $where_conditions Condições de pesquisa
     *
     * @return array $clientes Lista de clientes com gotas
     */
    public function getAllGotasWithClientes(array $clientesIds, array $where_conditions = array())
    {
        try {
            // obtem todos os registros

            $conditions = [];

            array_push(
                $conditions,
                [
                    'id in ' => $clientesIds
                ]
            );

            foreach ($where_conditions as $key => $condition) {
                array_push($conditions, $condition);
            }

            $query = $this->_getGotasTable()->Clientes->find('all')
                ->where($conditions)
                ->contain(['Gotas']);

            return $query;
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

            $error = [false, $stringError];
            return $error;
        }
    }

    /**
     * Obtêm gota pelo ID
     *
     * @param int $id Id da Gota
     *
     * @return object $gota Gota
     */
    public function getGotaById(int $id)
    {
        try {
            return $this->_getGotasTable()->find('all')
                ->where(
                    [
                        'id' => $id
                    ]
                )->first();
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

            $error = [false, $stringError];
            return $error;
        }
    }

    /**
     * Obtem gota por id e clientes Id
     *
     * @param int $id          Id da gota
     * @param int $clientes_id Id de clientes
     *
     * @return (entity\Gotas) $gota
     */
    public function getGotaClienteById(int $id, int $clientes_id)
    {
        try {
            return $this->_getGotasTable()->find('all')
                ->where(
                    [
                        'id' => $id,
                        'clientes_id' => $clientes_id
                    ]
                )->first();
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

            $error = [false, $stringError];
            return $error;
        }
    }

    /**
     * Obtem gota de cliente pelo nome
     *
     * @param int    $clientes_id    Id de cliente
     * @param string $nome_parametro Nome do parâmetro
     *
     * @return (entity\Gotas) $gota
     */
    public function getGotaClienteByName(int $clientes_id, string $nome_parametro)
    {
        try {
            return $this->_getGotasTable()->find('all')
                ->where(
                    [
                        'clientes_id' => $clientes_id,
                        'nome_parametro' => $nome_parametro
                    ]
                )->first();
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

            $error = [false, $stringError];
            return $error;
        }
    }

    /* ------------------------ Update ------------------------ */

    /**
     * Define todas as gotas de um cliente para a matriz
     *
     * @param int $clientes_id Id de Cliente
     * @param int $matriz_id   Id da Matriz
     *
     * @return boolean
     */
    public function setGotasToMainCliente(int $clientes_id, int $matriz_id)
    {
        try {
            return $this->updateAll(
                [
                    'clientes_id' => $matriz_id,
                    'habilitado' => false,
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

    /**
     * Habilita/Desabilita uma gota
     *
     * @param int  $id     Id da Gota
     * @param bool $status Estado de habilitado
     *
     * @return bool registro atualizado
     */
    public function updateStatusGota(int $id, bool $status)
    {
        try {
            $gotas = $this->_getGotasTable()->query();

            $success[0] = $gotas->update()
                ->set(['habilitado' => $status])
                ->where(['id' => $id])
                ->execute();

            return $success;
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

            $error = [false, $stringError];
            return $error;
        }
    }

    /* ------------------------ Delete ------------------------ */

    /**
     * Apaga todas as gotas de um cliente
     *
     * @param array $clientesIds Ids de clientes
     *
     * @return boolean
     */
    public function deleteAllGotasByClientesIds(array $clientesIds)
    {
        try {
            return $this->_getGotasTable()
                ->deleteAll(
                    [
                        'clientes_id in' => $clientesIds
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
