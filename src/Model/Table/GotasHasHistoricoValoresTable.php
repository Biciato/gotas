<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * GotasHasHistoricoValores Model
 *
 * @property \App\Model\Table\ClientesTable|\Cake\ORM\Association\BelongsTo $Clientes
 * @property \App\Model\Table\GotasTable|\Cake\ORM\Association\BelongsTo $Gotas
 *
 * @method \App\Model\Entity\GotasHasHistoricoValores get($primaryKey, $options = [])
 * @method \App\Model\Entity\GotasHasHistoricoValores newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\GotasHasHistoricoValores[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\GotasHasHistoricoValores|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\GotasHasHistoricoValores patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\GotasHasHistoricoValores[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\GotasHasHistoricoValores findOrCreate($search, callable $callback = null, $options = [])
 */
class GotasHasHistoricoValoresTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('gotas_has_historico_valores');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Clientes', [
            'foreignKey' => 'clientes_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Gotas', [
            'foreignKey' => 'gotas_id',
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
        $rules->add($rules->existsIn(['clientes_id'], 'Clientes'));
        $rules->add($rules->existsIn(['gotas_id'], 'Gotas'));

        return $rules;
    }
}
