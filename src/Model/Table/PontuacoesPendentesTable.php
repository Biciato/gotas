<?php
namespace App\Model\Table;

use ArrayObject;
use App\View\Helper;
use App\Controller\AppController;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * PontuacoesPendentes Model
 *
 * @property \App\Model\Table\ClientesTable|\Cake\ORM\Association\BelongsTo $Clientes
 * @property \App\Model\Table\UsuariosTable|\Cake\ORM\Association\BelongsTo $Usuarios
 * @property \App\Model\Table\UsuariosTable|\Cake\ORM\Association\BelongsTo $Usuarios
 *
 * @method \App\Model\Entity\PontuacoesPendente get($primaryKey, $options = [])
 * @method \App\Model\Entity\PontuacoesPendente newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\PontuacoesPendente[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\PontuacoesPendente|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\PontuacoesPendente patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\PontuacoesPendente[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\PontuacoesPendente findOrCreate($search, callable $callback = null, $options = [])
 */
class PontuacoesPendentesTable extends GenericTable
{

    /**
     * -------------------------------------------------------------
     * Fields
     * -------------------------------------------------------------
     */
    protected $pontuacoesPendentesTable = null;

    protected $pontuacoesPendentesQuery = null;


    /**
     * -------------------------------------------------------------
     * Properties
     * -------------------------------------------------------------
     */

    /**
     * Method get of pontuacoesPendentes table property
     *
     * @return Cake\ORM\Table Table object
     */
    private function _getPontuacoesPendentesTable()
    {
        if (is_null($this->pontuacoesPendentesTable)) {
            $this->_setPontuacoesPendentesTable();
        }
        return $this->pontuacoesPendentesTable;
    }

    /**
     * Method set of pontuacoesPendentes table property
     *
     * @return void
     */
    private function _setPontuacoesPendentesTable()
    {
        $this->pontuacoesPendentesTable = TableRegistry::get('PontuacoesPendentes');
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

        $this->setTable('pontuacoes_pendentes');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Clientes', [
            'foreignKey' => 'clientes_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Usuarios', [
            'foreignKey' => 'usuarios_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Funcionarios', [
            'className' => 'Usuarios',
            'foreignKey' => 'funcionarios_id',
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
            ->allowEmpty('conteudo');

        $validator
            ->notEmpty('chave_nfe');

        $validator
            ->notEmpty('estado_nfe');

        $validator
            ->dateTime('data')
            ->requirePresence('data', 'create')
            ->notEmpty('data');

        $validator
            ->integer('registro_processado')
            ->notEmpty('registro_processado');

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
        $rules->add($rules->existsIn(['usuarios_id'], 'Usuarios'));
        $rules->add($rules->existsIn(['funcionarios_id'], 'Usuarios'));

        return $rules;
    }

    /**
     * -------------------------------------------------------------
     * Methods
     * -------------------------------------------------------------
     */

    /* ------------------------ Create ------------------------ */

    /**
     * Cria registro de pontuacao pendente
     *
     * @param int    $clientes_id     Id do cliente
     * @param int    $usuarios_id     Id do usuário
     * @param int    $funcionarios_id Id do usuário funcionário
     * @param string $conteudo        Conteúdo da url
     * @param string $chave_nfe       Chave do Cupom Fiscal Eletrônico
     * @param string $estado_nfe      Estado do Cupom Fiscal Eletrônico
     *
     * @return object $entidade
     */
    public function createPontuacaoPendenteAwaitingProcessing($clientes_id, $usuarios_id, $funcionarios_id, $conteudo, $chave_nfe, $estado_nfe)
    {
        try {
            $pontuacao_pendente = $this->_getPontuacoesPendentesTable()->newEntity();

            $pontuacao_pendente['clientes_id'] = $clientes_id;
            $pontuacao_pendente['usuarios_id'] = $usuarios_id;
            $pontuacao_pendente['funcionarios_id'] = $funcionarios_id;
            $pontuacao_pendente['conteudo'] = $conteudo;
            $pontuacao_pendente['chave_nfe'] = $chave_nfe;
            $pontuacao_pendente['estado_nfe'] = $estado_nfe;
            $pontuacao_pendente['registro_processado'] = false;

            $pontuacao_pendente['data'] = date('Y-m-d H:i:s');

            $pontuacao_pendente = $this->save($pontuacao_pendente);

            return $pontuacao_pendente;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao editar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }
    /* ------------------------ Read ------------------------ */

    /**
     * Obtem todas as pontuacoes Pendentes por Id de Cliente
     *
     * @param int $clientes_id Id de Cliente
     *
     * @return \App\Model\Entity\PontuacoesPendentes $array[]
     *  lista de pontuacoes pendentes
     */
    public function getAllPontuacoesPendentesByClienteId(int $clientes_id)
    {
        try {
            return $this->_getPontuacoesPendentesTable()->find('all')
                ->where(['clientes_id' => $clientes_id]);
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
     * Obtêm cupom aguardando processamento pelo id
     *
     * @param int $id
     * @return object
     */
    public function getPontuacaoPendenteById(int $id)
    {
        try {
            return $this->_getPontuacoesPendentesTable()->get($id);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao editar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Procura por pontuação pendente aguardando processamento
     *
     * @param string $chave_nfe  Chave da NFE
     * @param string $estado_nfe Estado de Processamento da NFE
     *
     * @return object $pontuacao_pendente
     */
    public function findPontuacaoPendenteAwaitingProcessing(string $chave_nfe, string $estado_nfe)
    {
        try {
            $pontuacao_pendente = $this->_getPontuacoesPendentesTable()->find('all')
                ->where(
                    [
                        'chave_nfe' => $chave_nfe,
                        'estado_nfe' => $estado_nfe
                    ]
                )->first();

            return $pontuacao_pendente;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao editar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Procura por todos os cupons aguardando processamento
     *
     * @return array $pontuacao_pendentes
     */
    public function findAllPontuacoesPendentesAwaitingProcessing()
    {
        try {
            $pontuacao_pendente = $this->_getPontuacoesPendentesTable()->find('all')
                ->where(
                    [
                        'registro_processado' => false,
                    ]
                );

            return $pontuacao_pendente;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao editar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }
    /* ------------------------ Update ------------------------ */

    /**
     * Atualiza pontuacao pendente para 'processado'
     *
     * @param int $id
     * @param int $pontuacao_comprovante_id
     * @return object $pontuacao_pendente
     */
    public function setPontuacaoPendenteProcessed(int $id, int $pontuacao_comprovante_id = null)
    {
        try {
            $pontuacao_pendente = $this->_getPontuacoesPendentesTable()->get($id);

            $pontuacao_pendente->registro_processado = true;
            $pontuacao_pendente->pontuacao_comprovante_id = $pontuacao_comprovante_id;

            return $this->_getPontuacoesPendentesTable()->save($pontuacao_pendente);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao editar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Remove todas as pontuacoes Pendentes por Id de Cliente
     *
     * @param int $clientes_id Id de Cliente
     * @param int $matriz_id   Id da Matriz
     *
     * @return \App\Model\Entity\PontuacoesPendentes $array[]
     *  lista de pontuacoes pendentes
     */
    public function setPontuacoesPendentesToMainCliente(int $clientes_id, int $matriz_id)
    {
        try {
            return $this->updateAll(
                [
                    'clientes_id' => $matriz_id
                ],
                [
                    'clientes_id' => $clientes_id
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

            $stringError = __("Erro ao obter registro: {0}, em {1}", $e->getMessage(), $object['file']);

            Log::write('error', $stringError);

            return ['success' => false, 'message' => $stringError];
        }
    }

    /**
     * Atualiza todas as pontuacoes pendentes conforme argumentos
     *
     * @param array $fields     Campos contendo atualização
     * @param array $conditions Condições
     *
     * @return bool
     */
    public function updateAllPontuacoesPendentes(array $fields, array $conditions)
    {
        try {
            return $this->updateAll($fields, $conditions);
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

    /* ------------------------ Delete ------------------------ */

    /**
     * Remove todas as pontuacoes Pendentes por Id de Cliente
     *
     * @param array $clientes_id Ids de Clientes
     *
     * @return \App\Model\Entity\PontuacoesPendentes $array[]
     *  lista de pontuacoes pendentes
     */
    public function deleteAllPontuacoesPendentesByClienteIds(array $clientes_ids)
    {
        try {
            return $this->deleteAll(['clientes_id in' => $clientes_ids]);
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
     * Remove todas as pontuacoes Pendentes por Id de usuário
     *
     * @param int $usuarios_id Id de Usuário
     *
     * @return \App\Model\Entity\PontuacoesPendentes $array[]
     *  lista de pontuacoes pendentes
     */
    public function deleteAllPontuacoesPendentesByUsuariosId(int $usuarios_id)
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
}
