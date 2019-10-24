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
 * RedesHasClientesAdministradores Model
 *
 * @property \App\Model\Table\RedesHasClientesTable|\Cake\ORM\Association\BelongsTo $RedesHasClientes
 * @property \App\Model\Table\UsuariosTable|\Cake\ORM\Association\BelongsTo $Usuarios
 *
 * @method \App\Model\Entity\RedesHasClientesAdministradore get($primaryKey, $options = [])
 * @method \App\Model\Entity\RedesHasClientesAdministradore newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\RedesHasClientesAdministradore[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\RedesHasClientesAdministradore|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\RedesHasClientesAdministradore patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\RedesHasClientesAdministradore[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\RedesHasClientesAdministradore findOrCreate($search, callable $callback = null, $options = [])
 */
class RedesHasClientesAdministradoresTable extends GenericTable
{

    /**
     * -------------------------------------------------------------
     * Fields
     * -------------------------------------------------------------
     */
    protected $redes_has_clientes_administradores_table = null;

    /**
     * -------------------------------------------------------------
     * Properties
     * -------------------------------------------------------------
     */

    /**
     * Method get of Redes table property
     *
     * @return Cake\ORM\Table Table object
     */
    private function _getRedesHasClientesAdministradoresTable()
    {
        if (is_null($this->redes_has_clientes_administradores_table)) {
            $this->_setRedesHasClientesAdministradoresTable();
        }
        return $this->redes_has_clientes_administradores_table;
    }

    /**
     * Method set of Redes table property
     *
     * @return void
     */
    private function _setRedesHasClientesAdministradoresTable()
    {
        $this->redes_has_clientes_administradores_table = TableRegistry::get('Redes_Has_Clientes_Administradores');
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

        $this->setTable('redes_has_clientes_administradores');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('RedesHasClientes', [
            'foreignKey' => 'redes_has_clientes_id',
            'joinType' => 'INNER'
        ]);
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
        $rules->add($rules->existsIn(['redes_has_clientes_id'], 'RedesHasClientes'));
        $rules->add($rules->existsIn(['usuarios_id'], 'Usuarios'));

        return $rules;
    }

    #region Create

    /**
     * Grava novo administrador de uma rede
     *
     * @param int $redesHasClientesId Id de Redes Has Usuários
     * @param int $usuariosId           Id de Usuário
     *
     * @return \App\Model\Entity\RedesHasClientesAdministradores $redes_has_clientes_administradores
     */
    public function addRedesHasClientesAdministradores(int $redesHasClientesId, int $usuariosId)
    {
        try {
            $redesHasClientesAdministradores = $this->newEntity();
            $redesHasClientesAdministradores["redes_has_clientes_id"] = $redesHasClientesId;
            $redesHasClientesAdministradores["usuarios_id"] = $usuariosId;

            return $this->save($redesHasClientesAdministradores);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $object = null;

            foreach ($trace as $key => $item_trace) {
                if ($item_trace['class'] == 'Cake\Database\Query') {
                    $object = $item_trace;
                    break;
                }
            }

            $stringError = __("Erro ao gravar registro: {0}, em {1}", $e->getMessage(), $object['file']);

            Log::write('error', $stringError);

            return ['success' => false, 'message' => $stringError];
        }
    }

    #region Read

    /**
     * Obtem o vínculo de ligação de um usuário com a rede pelo id de usuário
     *
     * @param int $redes_id Id de Redes
     *
     * @return \App\Model\Entity\RedesHasClientes $redes_has_clientes[] Array
     */
    public function getRedesHasClientesAdministradorByUsuariosId(int $usuarios_id)
    {
        try {
            return $this->find('all')
                ->where(['usuarios_id' => $usuarios_id])
                ->contain(['RedesHasClientes.Redes', 'RedesHasClientes.Clientes'])->first();

        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $object = null;

            foreach ($trace as $key => $item_trace) {
                if ($item_trace['class'] == 'Cake\Database\Query') {
                    $object = $item_trace;
                    break;
                }
            }

            $stringError = __("Erro ao obter registro: {0}, em {1}", $e->getMessage(), $object['file']);

            Log::write('error', $stringError);

            return ['success' => false, 'message' => $stringError];
        }
    }

    #region Update



    #region Delete


    /**
     * Remove registros de Administradores
     *
     * @param array $redes_has_cliente_ids Redes has Clientes Ids
     *
     * @return void
     */
    public function deleteAllRedesHasClientesAdministradoresByClientesIds(array $redes_has_cliente_ids)
    {
        try {

            return $this
                ->deleteAll(['redes_has_clientes_id in' => $redes_has_cliente_ids]);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $object = null;

            foreach ($trace as $key => $item_trace) {
                if ($item_trace['class'] == 'Cake\Database\Query') {
                    $object = $item_trace;
                    break;
                }
            }

            $stringError = __("Erro ao remover registro: {0}, em {1}", $e->getMessage(), $object['file']);

            Log::write('error', $stringError);

            $error = ['result' => false, 'message' => $stringError];
            return $error;
        }
    }
}
