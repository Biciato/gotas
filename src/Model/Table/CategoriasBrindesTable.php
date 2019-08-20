<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Exception;
use App\Model\Entity\CategoriasBrinde;
use Cake\Log\Log;

/**
 * CategoriasBrindes Model
 *
 * @property \App\Model\Table\RedesTable|\Cake\ORM\Association\BelongsTo $Redes
 * @property \App\Model\Table\UsuariosTable|\Cake\ORM\Association\BelongsTo $Usuarios
 *
 * @method \App\Model\Entity\CategoriasBrinde get($primaryKey, $options = [])
 * @method \App\Model\Entity\CategoriasBrinde newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\CategoriasBrinde[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CategoriasBrinde|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CategoriasBrinde patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CategoriasBrinde[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\CategoriasBrinde findOrCreate($search, callable $callback = null, $options = [])
 */
class CategoriasBrindesTable extends GenericTable
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

        $this->setTable('categorias_brindes');
        $this->setDisplayField('nome');
        $this->setPrimaryKey('id');

        $this->belongsTo('Rede', [
            "className" => "Redes",
            'foreignKey' => 'redes_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('UsuarioCadastro', [
            "className" => "Usuarios",
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
            ->scalar('nome')
            ->maxLength('nome', 30)
            ->requirePresence('nome', 'create')
            ->notEmpty('nome');

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
        $rules->add($rules->existsIn(['redes_id'], 'Rede'));
        $rules->add($rules->existsIn(['audit_user_insert_id'], 'UsuarioCadastro'));

        return $rules;
    }

    #region Read

    public function getCategoriasBrindesList(int $redesId)
    {
        try {
            $where = [];
            $where[] = ["redes_id" => $redesId];
            $where[] = ["habilitado" => 1];

            return $this->find("list")->where($where);
        } catch (\Throwable $th) {
            $message = sprintf("[%s] %s", MESSAGE_LOAD_EXCEPTION, $th->getMessage());
            Log::write("error", $message);
            throw new Exception($message);
        }
    }

    public function getCategoriasBrindes(int $redesId, string $nome = null, bool $habilitado = null)
    {
        try {
            $where = [];
            $where[] = ["redes_id" => $redesId];
            $where[] = ["nome LIKE" => "%{$nome}%"];

            if (!empty($habilitado)) {
                $where[] = ["habilitado" => $habilitado];
            }

            return $this->find("all")->where($where);
        } catch (\Throwable $th) {
            $message = sprintf("[%s] %s", MESSAGE_LOAD_EXCEPTION, $th->getMessage());
            Log::write("error", $message);
            throw new Exception($message);
        }
    }
    #endregion

    #region Save

    public function saveUpdate(CategoriasBrinde $categoriasBrinde)
    {
        try {
            return $this->save($categoriasBrinde);
        } catch (Exception $e) {
            $message = sprintf("[%s] %s", MESSAGE_SAVED_ERROR, $e->getMessage());
            Log::write("error", $message);
            throw new Exception($message);
        }
    }

    public function updateStatusCategoriasBrindes(int $id, bool $habilitado)
    {
        try {
            return $this->updateAll(["habilitado" => $habilitado], ["id" => $id]);
        } catch (Exception $e) {
            $message = sprintf("[%s] %s", MESSAGE_SAVED_ERROR, $e->getMessage());
            Log::write("error", $message);
            throw new Exception($message);
        }
    }
    #endregion

    #region Delete

    #endregion


}
