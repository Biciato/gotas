<?php

namespace App\Model\Table;

use App\Model\Entity\RedesCpfListaNegra;
use Cake\Database\Expression\QueryExpression;
use Cake\Log\Log;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Exception;
use Throwable;

/**
 * RedesCpfListaNegra Model
 *
 * @property \App\Model\Table\RedesTable|\Cake\ORM\Association\BelongsTo $Redes
 * @property \App\Model\Table\UsuariosTable|\Cake\ORM\Association\BelongsTo $Usuarios
 *
 * @method \App\Model\Entity\RedesCpfListaNegra get($primaryKey, $options = [])
 * @method \App\Model\Entity\RedesCpfListaNegra newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\RedesCpfListaNegra[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\RedesCpfListaNegra|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\RedesCpfListaNegra patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\RedesCpfListaNegra[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\RedesCpfListaNegra findOrCreate($search, callable $callback = null, $options = [])
 */
class RedesCpfListaNegraTable extends Table
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

        $this->setTable('redes_cpf_lista_negra');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Redes', [
            'foreignKey' => 'redes_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Usuarios', [
            'foreignKey' => 'audit_user_insert_id',
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
            ->scalar('cpf')
            ->maxLength('cpf', 20)
            ->requirePresence('cpf', 'create')
            ->notEmpty('cpf');

        $validator
            ->boolean('habilitado')
            ->allowEmpty('habilitado');

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
        $rules->add($rules->existsIn(['redes_id'], 'Redes'));
        $rules->add($rules->existsIn(['audit_user_insert_id'], 'Usuarios'));

        return $rules;
    }

    #region Read

    /**
     * Obtem lista de cpf
     *
     * Obtem lista de cpf pela rede e pelo cpf
     *
     * @param integer $redesId Id da Rede
     * @param string $cpf CPF
     * @return \Cake\Orm\Query|\App\Model\Entity\RedesCpfListaNegra[] $list
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.1.8
     * @date 2020-03-12
     */
    public function getCpfsByNetwork(int $redesId, string $cpf = null)
    {
        try {
            $where = function (QueryExpression $exp) use ($redesId, $cpf) {
                $exp->eq("RedesCpfListaNegra.redes_id", $redesId);

                if (!empty($cpf)) {
                    $exp->like("RedesCpfListaNegra.cpf", $cpf);
                }

                return $exp;
            };

            return $this
                ->find("all")
                ->where($where);
        } catch (\Throwable $th) {
            $message = sprintf("[%s] %s", MSG_LOAD_EXCEPTION, $th->getMessage());
            Log::write("error", $message);
            throw new Exception($message, $th->getCode());
        }
    }

    #endregion

    #region Save

    /**
     * Salva um registro
     *
     * @param RedesCpfListaNegra $record Entidade
     * @return RedesCpfListaNegra $record Entidade salva com Id
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.1.8
     */
    public function saveUpdate(RedesCpfListaNegra $record)
    {
        try {
            return $this->save($record);
        } catch (Throwable $th) {
            $message = sprintf("[%s] %s", MSG_SAVED_EXCEPTION, $th->getMessage());
            Log::write("error", $message);
            throw new Exception($message, MSG_SAVED_EXCEPTION_CODE);
        }
    }

    #endregion

}
