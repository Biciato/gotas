<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * UsuariosTokens Model
 *
 * @property \App\Model\Table\UsuariosTable|\Cake\ORM\Association\BelongsTo $Usuarios
 *
 * @method \App\Model\Entity\UsuariosToken get($primaryKey, $options = [])
 * @method \App\Model\Entity\UsuariosToken newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\UsuariosToken[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\UsuariosToken|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\UsuariosToken patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\UsuariosToken[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\UsuariosToken findOrCreate($search, callable $callback = null, $options = [])
 */
class UsuariosTokensTable extends Table
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

        $this->setTable('usuarios_tokens');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Usuarios', [
            'foreignKey' => 'usuarios_id',
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
            ->scalar('tipo')
            ->allowEmpty('tipo');

        $validator
            ->scalar('token')
            ->maxLength('token', 200)
            ->allowEmpty('token');

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

        return $rules;
    }

    public function getTokenUsuario(int $usuariosId)
    {
        $where = array();

        $where["usuarios_id"] = $usuariosId;
        $tokens = $this->find("all")
            ->where($where)
            ->toArray();
        return $tokens;
    }

    public function getTokens()
    {
        $tokens = $this->find("all")->toArray();
        return $tokens;
    }
}
