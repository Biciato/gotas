<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Log\Log;
use App\Model\Entity;

/**
 * TransportadorasHasUsuarios Model
 *
 * @property \App\Model\Table\TransportadorasTable|\Cake\ORM\Association\BelongsTo $Transportadoras
 * @property \App\Model\Table\UsuariosTable|\Cake\ORM\Association\BelongsTo $Usuarios
 *
 * @method \App\Model\Entity\TransportadorasHasUsuario get($primaryKey, $options = [])
 * @method \App\Model\Entity\TransportadorasHasUsuario newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\TransportadorasHasUsuario[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\TransportadorasHasUsuario|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\TransportadorasHasUsuario patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\TransportadorasHasUsuario[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\TransportadorasHasUsuario findOrCreate($search, callable $callback = null, $options = [])
 */
class TransportadorasHasUsuariosTable extends GenericTable
{
    /**
     * -------------------------------------------------------------
     * Fields
     * -------------------------------------------------------------
     */

    protected $transportadorasHasUsuariosQuery = null;

    protected $transportadorasHasUsuariosTable = null;

    /**
     * -------------------------------------------------------------
     * Properties
     * -------------------------------------------------------------
     */

    /**
     * Method get of client table property
     * @return (Cake\ORM\Table) Table object
     */
    private function getTransportadorasHasUsuariosTable()
    {
        if (is_null($this->transportadorasHasUsuariosTable)) {
            $this->setTransportadorasHasUsuariosTable();
        }
        return $this->transportadorasHasUsuariosTable;
    }

    /**
     * Method set of client table property
     * @return void
     */
    private function setTransportadorasHasUsuariosTable()
    {
        $this->transportadorasHasUsuariosTable = TableRegistry::get('TransportadorasHasUsuarios');
    }

    /**
     * Method get of client query property
     * @return (Cake\ORM\Table) Table object
     **/
    private function getTransportadorasHasUsuariosQuery()
    {
        if (is_null($this->transportadorasHasUsuariosQuery)) {
            $this->setTransportadorasHasUsuariosQuery();
        }
        return $this->transportadorasHasUsuariosQuery;
    }

    /**
     * Method set of client query property
     * @return void
     */
    private function setTransportadorasHasUsuariosQuery()
    {
        $this->transportadorasHasUsuariosQuery = $this->getTransportadorasHasUsuariosTable()->query();
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

        $this->setTable('transportadoras_has_usuarios');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Transportadoras', [
            'foreignKey' => 'transportadoras_id',
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
        $rules->add($rules->existsIn(['transportadoras_id'], 'Transportadoras'));
        $rules->add($rules->existsIn(['usuarios_id'], 'Usuarios'));

        return $rules;
    }

    /* -------------------------- Create -------------------------- */

    /**
     * Adiciona novo Usuário em transportadoras
     *
     * @param int $transportadoras_id Id de Transportadora
     * @param int $usuarios_id        Id de usuário
     *
     * @return void \App\Entity\Model\TransportadoraHasUsuario
     */
    public function addTransportadoraHasUsuario($transportadoras_id = null, $usuarios_id = null)
    {
        try {

            $transportadoraHasUsuario = $this->getTransportadorasHasUsuariosTable()->newEntity();

            $transportadoraHasUsuario->transportadoras_id = (int)$transportadoras_id;
            $transportadoraHasUsuario->usuarios_id = (int)$usuarios_id;

            return $this->getTransportadorasHasUsuariosTable()->save($transportadoraHasUsuario);

        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $this->log("Erro ao inserir novo registro: " . $e->getMessage() . ", em: " . $trace[1], 'error');
        }
    }

    /* -------------------------- Read -------------------------- */

    /**
     * Procura todas as transportadoras por um array de condição
     *
     * @param array $where_conditions Array de condição
     *
     * @return \App\Model\Entity\TransportadorasHasUsuario[] $array Lista de TransportadorasHasUsuarios
     */
    public function findTransportadorasHasUsuarios(array $where_conditions)
    {
        try {
            return $this->getTransportadorasHasUsuariosTable()
                ->find('all')
                ->where($where_conditions);

        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao realizar pesquisa de TransportadorasHasUsuarios: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Procura vínculo de transportadoras com usuários pelo id
     *
     * @param array $where_conditions Array de condição
     *
     * @return \App\Model\Entity\TransportadorasHasUsuario[] $array Lista de TransportadorasHasUsuarios
     */
    public function findTransportadorasHasUsuariosByTransportadorasId(int $id)
    {
        try {
            return $this->getTransportadorasHasUsuariosTable()
            ->find('all')
            ->where(['transportadoras_id' => $id])
            ->contain(['Transportadoras', 'Usuarios']);
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao realizar pesquisa de TransportadorasHasUsuarios: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Procura vínculo de transportadoras com usuários pelo id
     *
     * @param array $where_conditions Array de condição
     *
     * @return \App\Model\Entity\TransportadorasHasUsuario[] $array Lista de TransportadorasHasUsuarios
     */
    public function getTransportadorasHasUsuariosById(int $id)
    {
        try {
            return $this->getTransportadorasHasUsuariosTable()
                ->find('all')
                ->where(['id' => $id])
                ->first();

        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao realizar pesquisa de TransportadorasHasUsuarios: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Procura todas as transportadoras por Usuários Id
     *
     * @param int $usuarios_id
     *
     * @return \App\Model\Entity\TransportadorasHasUsuario[] $array Lista de TransportadorasHasUsuarios
     */
    public function findTransportadorasHasUsuariosByUsuariosId(int $usuarios_id)
    {
        try {
            return $this->getTransportadorasHasUsuariosTable()
                ->find('all')
                ->where(['usuarios_id' => $usuarios_id])
                ->contain(['Transportadoras', 'Usuarios']);

        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao realizar pesquisa de TransportadorasHasUsuarios: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /* -------------------------- Delete -------------------------- */

    /**
     * Remove todos os vínculos de transportadora com usuário
     *
     * @param integer $transportadoras_id Id de Transportadora
     *
     * @return void
     */
    public function deleteAllTransportadorasHasUsuarios(array $deleteConditions)
    {
        try {
            return $this->deleteAll($deleteConditions);
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao realizar remoção de vínculo de Transportadora: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Remove todos os vínculos de transportadora com usuário
     *
     * @param integer $transportadoras_id Id de Transportadora
     *
     * @return void
     */
    public function deleteAllTransportadorasHasUsuariosByTransportadorasId(int $transportadoras_id)
    {
        try {
            return $this->deleteAll(['transportadoras_id' => $transportadoras_id]);
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao realizar remoção de vínculo de Transportadora: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Remove todos os vínculos de transportadora com usuário
     *
     * @param integer $usuarios_id Id de Usuário
     *
     * @return void
     */
    public function deleteAllTransportadorasHasUsuariosByUsuariosId(int $usuarios_id)
    {
        try {
            return $this->deleteAll(['usuarios_id' => $usuarios_id]);
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao realizar remoção de vínculo de Transportadora: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);

            return $stringError;
        }
    }
}
