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
 * UsuariosHasBrindes Model
 *
 * @property \App\Model\Table\UsuariosTable|\Cake\ORM\Association\BelongsTo $Usuarios
 * @property \App\Model\Table\ClientesHasBrindesHabilitadosTable|\Cake\ORM\Association\BelongsTo $ClientesHasBrindesHabilitados
 *
 * @method \App\Model\Entity\UsuariosHasBrinde get($primaryKey, $options = [])
 * @method \App\Model\Entity\UsuariosHasBrinde newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\UsuariosHasBrinde[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\UsuariosHasBrinde|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\UsuariosHasBrinde patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\UsuariosHasBrinde[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\UsuariosHasBrinde findOrCreate($search, callable $callback = null, $options = [])
 */
class UsuariosHasBrindesTable extends GenericTable
{
    /**
     * -------------------------------------------------------------
     * Fields
     * -------------------------------------------------------------
     */
    protected $usuariosHasBrindesTable = null;

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
    private function _getUsuariosHasBrindesTable()
    {
        if (is_null($this->usuariosHasBrindesTable)) {
            $this->_setUsuariosHasBrindesTable();
        }
        return $this->usuariosHasBrindesTable;
    }

    /**
     * Method set of brinde table property
     * 
     * @return void
     */
    private function _setUsuariosHasBrindesTable()
    {
        $this->usuariosHasBrindesTable = TableRegistry::get('UsuariosHasBrindes');
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

        $this->setTable('usuarios_has_brindes');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Usuarios', [
            'foreignKey' => 'usuarios_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('ClientesHasBrindesHabilitados', [
            'foreignKey' => 'clientes_has_brindes_habilitados_id',
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
            ->numeric('preco')
            ->requirePresence('preco', 'create')
            ->notEmpty('preco');

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
        $rules->add($rules->existsIn(['usuarios_id'], 'Usuarios'));
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
     * Add a new Brinde for a Usuario
     *
     *
     * @param int $usuarios_id
     * @param int $brindes_habilitados_id
     * @param int $cupons_id
     * @param float $quantidade
     * @param float $preco
     * @return \App\Model\Entity\UsuariosHasBrinde $usuarioHasBrinde
     * @author
     **/
    public function addUsuarioHasBrindes(int $usuarios_id, int $brindes_habilitados_id, float $quantidade, float $preco, int $cupons_id = null)
    {
        try {
            $brinde_usuario = $this->_getUsuariosHasBrindesTable()->newEntity();

            $brinde_usuario->usuarios_id = $usuarios_id;
            $brinde_usuario->clientes_has_brindes_habilitados_id = $brindes_habilitados_id;
            $brinde_usuario->quantidade = (int)$quantidade;
            $brinde_usuario->preco = $preco * $quantidade;
            $brinde_usuario->data = date('Y-m-d H:i:s');

            
            $brinde_usuario->cupons_id = $cupons_id;

            return $this->_getUsuariosHasBrindesTable()->save($brinde_usuario);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao atualizar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }
    /* ------------------------ Read ------------------------ */

    /**
     * Obtem detalhes de brinde de usuário pelo Id
     *
     * @param integer $usuarios_has_brindes_id Id do brinde de usuário
     * @return \App\Model\Entity\UsuariosHasBrindes
     */
    public function getUsuariosHasBrindesById(int $usuarios_has_brindes_id)
    {
        try {
            return $this->_getUsuariosHasBrindesTable()
                ->find('all')
                ->contain(['ClientesHasBrindesHabilitados.Brindes'])
                ->where(['UsuariosHasBrindes.id' => $usuarios_has_brindes_id])
                ->first();
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao atualizar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Obtem todos os brindes de usuários conforme condições
     *
     * @param array $where_conditions Condições de pesquisa
     * @param array $order_conditions Condições de ordem
     * 
     * @return \App\Model\Entity\UsuariosHasBrindes[]
     */
    public function getAllUsuariosHasBrindes(array $where_conditions = [], array $order_conditions = [])
    {
        try {
            return $this->_getUsuariosHasBrindesTable()
                ->find('all')
                ->contain(
                    [
                        'ClientesHasBrindesHabilitados.Brindes'
                    ]
                )
                ->where($where_conditions)
                ->order($order_conditions);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao atualizar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }
    
    /* ------------------------ Update ------------------------ */

    /* ------------------------ Delete ------------------------ */

    /**
     * Apaga todas as gotas de um cliente
     *
     * @param array $clientes_ids Ids de clientes
     *
     * @return boolean
     */
    public function deleteAllUsuariosHasBrindesByClientesIds(array $clientes_ids)
    {
        try {

            // pegar id de brindes que estão vinculados em um cliente

            $brindes_clientes_ids = $this->_getUsuariosHasBrindesTable()->ClientesHasBrindesHabilitados->find('all')
                ->where(['clientes_id in' => $clientes_ids])
                ->select(['id']);

            $clientes_has_brindes_habilitados_ids = [];

            foreach ($brindes_clientes_ids as $key => $value) {
                array_push($clientes_has_brindes_habilitados_ids, $value['id']);
            }

            if (sizeof($clientes_has_brindes_habilitados_ids) > 0) {
                return $this->_getUsuariosHasBrindesTable()
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

    /**
     * Apaga todas as gotas de um usuário
     *
     * @param int $usuarios_id Id de usuario
     *
     * @return boolean
     */
    public function deleteAllUsuariosHasBrindesByUsuariosId(int $usuarios_id)
    {
        try {
            return $this->deleteAll(['usuarios_id' => $usuarios_id]);
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
