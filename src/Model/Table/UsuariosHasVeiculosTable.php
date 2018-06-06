<?php
namespace App\Model\Table;

use ArrayObject;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * UsuariosHasVeiculos Model
 *
 * @property \App\Model\Table\UsuariosTable|\Cake\ORM\Association\BelongsTo $Usuarios
 * @property \App\Model\Table\VeiculosTable|\Cake\ORM\Association\BelongsTo $Veiculos
 *
 * @method \App\Model\Entity\UsuariosHasVeiculo get($primaryKey, $options = [])
 * @method \App\Model\Entity\UsuariosHasVeiculo newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\UsuariosHasVeiculo[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\UsuariosHasVeiculo|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\UsuariosHasVeiculo patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\UsuariosHasVeiculo[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\UsuariosHasVeiculo findOrCreate($search, callable $callback = null, $options = [])
 */
class UsuariosHasVeiculosTable extends GenericTable
{

    /**
     * -------------------------------------------------------------
     * Fields
     * -------------------------------------------------------------
     */

    protected $usuarioHasVeiculosQuery = null;
    protected $usuarioHasVeiculosTable = null;

    /**
     * -------------------------------------------------------------
     * Properties
     * -------------------------------------------------------------
     */

    /**
     * Method get of user has vehicles table property
     * @return (Cake\ORM\Table) Table object
     */
    private function getUsuarioHasVeiculosTable()
    {
        if (is_null($this->usuarioHasVeiculosTable)) {
            $this->setUsuarioHasVeiculosTable();
        }
        return $this->usuarioHasVeiculosTable;
    }

    /**
     * Method set of user has vehicles table property
     * @return void
     */
    private function setUsuarioHasVeiculosTable()
    {
        $this->usuarioHasVeiculosTable = TableRegistry::get('UsuariosHasVeiculos');
    }

    /**
     * Method get of user has vehicles query property
     * @return (Cake\ORM\Table) Table object
     **/
    private function getUsuarioHasVeiculosQuery()
    {
        if (is_null($this->usuarioHasVeiculosQuery)) {
            $this->setUsuarioHasVeiculosQuery();
        }
        return $this->usuarioHasVeiculosQuery;
    }

    /**
     * Method set of user has vehicles query property
     * @return void
     */
    private function setUsuarioHasVeiculosQuery()
    {
        $this->usuarioHasVeiculosQuery = $this->getUsuarioHasVeiculosTable()->query();
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

        $this->setTable('usuarios_has_veiculos');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Usuarios', [
            'foreignKey' => 'usuarios_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Veiculos', [
            'foreignKey' => 'veiculos_id',
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
        $rules->add($rules->existsIn(['usuarios_id'], 'Usuarios'));
        $rules->add($rules->existsIn(['veiculos_id'], 'Veiculos'));

        return $rules;
    }


    /* -------------------------- Create -------------------------- */

    /**
     * Vincula veículo à usuário
     *
     * @param int $veiculos_id id de veículo
     * @param int $usuarios_id id de usuário
     *
     * @return boolean registro vinculado
     **/
    public function addUsuarioHasVeiculo($veiculos_id = null, $usuarios_id = null)
    {
        try {
            $usuarioHasVeiculo = $this->getUsuarioHasVeiculosTable()->newEntity();

            $usuarioHasVeiculo->veiculos_id = (int)$veiculos_id;
            $usuarioHasVeiculo->usuarios_id = (int)$usuarios_id;

            return $this->getUsuarioHasVeiculosTable()->save($usuarioHasVeiculo);

        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $this->log("Erro ao inserir novo registro: " . $e->getMessage() . ", em: " . $trace[1]);
        }
    }

    /* -------------------------- Read -------------------------- */

    /**
     * Localiza se usuário tem veiculo vinculado
     *
     * @param array $whereConditions Array de condição
     *
     * @return \App\Model\Entity\UsuariosHasVeiculo $usuario_has_veiculo
     */
    public function findUsuariosHasVeiculos(array $whereConditions = [])
    {
        try {
            return $this->getUsuarioHasVeiculosTable()
                ->find('all')
                ->where($whereConditions);

        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao realizar pesquisa de UsuariosHasVeiculos: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

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
    public function findUsuariosHasVeiculosByVeiculosId(int $id)
    {
        try {
            return $this->getUsuarioHasVeiculosTable()
                ->find('all')
                ->where(['veiculos_id' => $id])
                ->contain(['Veiculos', 'Usuarios']);
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao realizar pesquisa de TransportadorasHasUsuarios: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Obtem veículos pelo id do usuário
     *
     * @param int $usuarios_id Id de Usuário
     *
     * @return array $veiculos
     */
    public function getUsuariosHasVeiculosById(int $usuario_has_veiculo_id)
    {
        try {
            $vehicles = $this->getUsuarioHasVeiculosQuery()
                ->find('all')
                ->where(['id' => $usuario_has_veiculo_id])
                ->first();

            return $vehicles;
        } catch (\Exception $e) {

        }
    }

    /**
     * Obtem veículos pelo id do usuário
     *
     * @param int $usuarios_id Id de Usuário
     *
     * @return array $veiculos
     */
    public function getVeiculoByUsuarioId(int $usuarios_id)
    {
        try {
            $vehicles = $this->getUsuarioHasVeiculosQuery()
                ->find('all')
                ->contain(['Usuarios', 'Veiculos'])
                ->where(['usuarios_id' => $usuarios_id]);

            return $vehicles;
        } catch (\Exception $e) {
        }
    }

    /* -------------------------- Delete -------------------------- */

    /**
     * Remove todos os vínculos de usuário com veículos
     *
     * @param integer $usuarios_id Id de Usuário
     *
     * @return void
     */
    public function deleteAllUsuariosHasVeiculosByUsuariosId(int $usuarios_id)
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

            $stringError = __("Erro ao obter registro: {0}, em {1}", $e->getMessage(), $object['file']);

            Log::write('error', $stringError);

            return ['success' => false, 'message' => $stringError];
        }
    }

    /**
     * Undocumented function
     *
     * @param array $deleteConditions
     * @return void
     */
    public function deleteUsuariosHasVeiculos(array $deleteConditions)
    {
        try {

            return $this->getUsuarioHasVeiculosTable()
                ->deleteAll($deleteConditions);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $messageString = __("Não foi possível obter transportadora!");
            $stringError = __("Erro ao buscar registro: {0} - {1} em: {2}. [Função: {3} / Arquivo: {4} / Linha: {5}]  ", $messageString, $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
        }
    }
}
