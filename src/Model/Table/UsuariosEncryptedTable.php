<?php
namespace App\Model\Table;

use ArrayObject;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Log\Log;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * UsuariosEncrypted Model
 *
 * @property \App\Model\Table\UsuariosTable|\Cake\ORM\Association\BelongsTo $Usuarios
 *
 * @method \App\Model\Entity\UsuariosEncrypted get($primaryKey, $options = [])
 * @method \App\Model\Entity\UsuariosEncrypted newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\UsuariosEncrypted[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\UsuariosEncrypted|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\UsuariosEncrypted patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\UsuariosEncrypted[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\UsuariosEncrypted findOrCreate($search, callable $callback = null, $options = [])
 */
class UsuariosEncryptedTable extends Table
{

    /**
     * -------------------------------------------------------------
     * Fields
     * -------------------------------------------------------------
     */
    protected $pontuacoesTable = null;

    /**
     * -------------------------------------------------------------
     * Properties
     * -------------------------------------------------------------
     */

    /**
     * Method get of pontuacoes table property
     *
     * @return Cake\ORM\Table Table object
     */
    private function _getUsuariosEncryptedTable()
    {
        if (is_null($this->pontuacoesTable)) {
            $this->_setUsuariosEncryptedTable();
        }
        return $this->pontuacoesTable;
    }

    /**
     * Method set of pontuacoes table property
     *
     * @return void
     */
    private function _setUsuariosEncryptedTable()
    {
        $this->pontuacoesTable = TableRegistry::get('UsuariosEncrypted');
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

        $this->setTable('usuarios_encrypted');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Usuarios', [
            'foreignKey' => 'usuarios_id'
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
            ->requirePresence('secret_encrypted', 'create')
            ->notEmpty('secret_encrypted');

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


    /* ------------------------ Create ------------------------ */

    /**
     * Insere novo registro de senha criptografada do usuário
     *
     * @param int    $usuarios_id      Id do Usuário
     * @param string $secret_encrypted Senha criptografada
     * 
     * @return bool Registro inserido
     */
    public function addUsuarioEncryptedPassword(int $usuarios_id, string $secret_encrypted)
    {
        try {
            $usuario_encrypted = $this->_getUsuariosEncryptedTable()->newEntity();

            $usuario_encrypted->usuarios_id = $usuarios_id;
            $usuario_encrypted->secret_encrypted = $secret_encrypted;

            return $this->_getUsuariosEncryptedTable()->save($usuario_encrypted);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao atualizar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /* ------------------------ Read ------------------------ */

    /**
     * Obtêm registro de usuário criptografado pelo Id
     *
     * @param int $usuarios_id Id de Usuário
     * 
     * @return UsuarioEncrypted usuário com senha criptografada
     */
    public function getUsuarioEncryptedById(int $usuarios_id)
    {
        try {
            return $this->_getUsuariosEncryptedTable()->find('all')
                ->where(['usuarios_id' => $usuarios_id])
                ->first();
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao atualizar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }
    /* ------------------------ Update ------------------------ */

    /**
     * Atualiza senha criptografada do usuário
     *
     * @param int    $usuarios_id      Id do Usuário
     * @param string $secret_encrypted Senha criptografada
     * 
     * @return bool Registro modificado
     */
    public function setUsuarioEncryptedPassword(int $usuarios_id, string $secret_encrypted)
    {
        try {
            $usuario_encrypted = $this->getUsuarioEncryptedById($usuarios_id);

            if (is_null($usuario_encrypted)) {
                return $this->addUsuarioEncryptedPassword($usuarios_id, $secret_encrypted);
            } else {
                return $this->updateUsuarioEncryptedPassword($usuario_encrypted, $secret_encrypted);
            }
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao atualizar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Atualiza senha criptografada do usuário
     *
     * @param UsuariosEncrypted $usuario_encrypted Usuário
     * @param string            $secret_encrypted  Senha criptografada
     * 
     * @return bool Registro atualizado
     */
    public function updateUsuarioEncryptedPassword(\App\Model\Entity\UsuariosEncrypted $usuario_encrypted, string $secret_encrypted)
    {
        try {
            $usuario_encrypted->secret_encrypted = $secret_encrypted;

            return $this->_getUsuariosEncryptedTable()->save($usuario_encrypted);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao atualizar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /* ------------------------ Delete ------------------------ */

    /**
     * Deleta todos os registros por um Usuário Id
     *
     * @param int $usuarios_id Id de Usuário
     * 
     * @return void
     */
    public function deleteUsuariosEncryptedByUsuariosId(int $usuarios_id)
    {
        try {
            return $this->deleteAll(
                [
                    'usuarios_id' => $usuarios_id
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

            $stringError = __("Erro ao remover registro: {0}, em {1}", $e->getMessage(), $object['file']);

            Log::write('error', $stringError);

            $error = ['result' => false, 'message' => $stringError];
            return $error;
        }
    }
}
