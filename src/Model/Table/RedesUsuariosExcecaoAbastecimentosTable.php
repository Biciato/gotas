<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * RedesUsuariosExcecaoAbastecimentos Model
 *
 * @property \App\Model\Table\RedesTable|\Cake\ORM\Association\BelongsTo $Redes
 * @property \App\Model\Table\UsuariosTable|\Cake\ORM\Association\BelongsTo $Usuarios
 * @property \App\Model\Table\UsuariosTable|\Cake\ORM\Association\BelongsTo $Usuarios
 *
 * @method \App\Model\Entity\RedesUsuariosExcecaoAbastecimento get($primaryKey, $options = [])
 * @method \App\Model\Entity\RedesUsuariosExcecaoAbastecimento newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\RedesUsuariosExcecaoAbastecimento[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\RedesUsuariosExcecaoAbastecimento|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\RedesUsuariosExcecaoAbastecimento patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\RedesUsuariosExcecaoAbastecimento[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\RedesUsuariosExcecaoAbastecimento findOrCreate($search, callable $callback = null, $options = [])
 */
class RedesUsuariosExcecaoAbastecimentosTable extends GenericTable
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

        $this->setTable('redes_usuarios_excecao_abastecimentos');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo(
            "Rede",
            array(
                "className" => "Redes",
                "joinType" => Query::JOIN_TYPE_INNER,
                "foreignKey" => "redes_id"
            )
        );
        $this->belongsToMany('Redes', [
            'foreignKey' => 'redes_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsToMany('Usuarios', [
            'foreignKey' => 'adm_rede_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo(
            'Usuario',
            array(
                "className" => "Usuarios",
                'foreignKey' => 'usuarios_id',
                'joinType' => 'INNER'
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
            ->allowEmpty('quantidade_dia');

        $validator
            ->dateTime('validade')
            ->allowEmpty('validade');

        $validator
            ->boolean('habilitado')
            ->allowEmpty('habilitado');

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
        $rules->add($rules->existsIn(['redes_id'], 'Redes'));
        $rules->add($rules->existsIn(['adm_rede_id'], 'Usuarios'));
        $rules->add($rules->existsIn(['usuarios_id'], 'Usuarios'));

        return $rules;
    }

    #region Read

    /**
     * Obtem os Usuários que possuem Exceção
     *
     * @param integer $redesId
     * @param string $name
     * @param string $email
     * @param string $cpf
     * @return void
     */
    public function findUsuariosExcecaoAbastecimentos(int $redesId, string $name = null, string $email = null, string $cpf = null)
    {
        # code...
    }
    #endregion

    #region Save

    #endregion

    #region Delete

    #endregion
}
