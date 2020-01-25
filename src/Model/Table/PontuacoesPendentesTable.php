<?php

namespace App\Model\Table;

use ArrayObject;
use App\View\Helper;
use App\Controller\AppController;
use App\Model\Entity\PontuacoesPendente;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Exception;
use Throwable;

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
            'joinType' => Query::JOIN_TYPE_LEFT
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

    #region Create

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
            $pontuacao_pendente = $this->newEntity();

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
        }
    }
    #region Read

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
            return $this->find('all')
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
            return $this->get($id);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao editar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);
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
            $pontuacao_pendente = $this
                ->find('all')
                ->where(
                    [
                        'PontuacoesPendentes.chave_nfe' => $chave_nfe,
                        'PontuacoesPendentes.estado_nfe' => $estado_nfe
                    ]
                )->contain(["Clientes"])
                ->first();

            return $pontuacao_pendente;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao editar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);
        }
    }

    /**
     * Procura por todos os cupons aguardando processamento
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2017-09-01
     *
     * @return array $pontuacao_pendentes
     */
    public function findAllPontuacoesPendentesAwaitingProcessing()
    {
        try {
            $pontuacao_pendente = $this->find('all')
                ->where(
                    array(
                        'registro_processado' => false,
                    )
                );

            return $pontuacao_pendente;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao editar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);
        }
    }

    #endregion

    #region Save/Update

    /**
     * src\Model\Table\PontuacoesPendentesTable.php::saveUpdate
     *
     * Insere/Atualiza registro PontuacoesPendente
     *
     * @param PontuacoesPendente $pontuacoesPendente Objeto
     * @return \App\Model\Entity\PontuacoesPendente $PontuacoesPendente Objeto
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-10-08
     */
    public function saveUpdate(PontuacoesPendente $pontuacoesPendente)
    {
        try {
            return $this->save($pontuacoesPendente);
        } catch (Throwable $th) {
            $message = sprintf("[%s] %s: %s", MESSAGE_SAVED_ERROR, $th->getCode(), $th->getMessage());
            Log::write("error", $message);
            throw new Exception($message, $th->getCode());
        }
    }

    /**
     * Atualiza pontuacao pendente para 'processado'
     *
     * @param int $id
     * @param int $pontuacaoComprovanteId
     * @return object $pontuacao_pendente
     */
    public function setPontuacaoPendenteProcessed(int $id, int $pontuacaoComprovanteId = null)
    {
        try {
            $pontuacao_pendente = $this->get($id);

            $pontuacao_pendente["registro_processado"] = 1;
            $pontuacao_pendente["pontuacoes_comprovantes_id"] = $pontuacaoComprovanteId;

            return $this->save($pontuacao_pendente);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao editar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);
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

    #endregion

    #region Delete

    /**
     * Remove todas as pontuacoes Pendentes por Id de Cliente
     *
     * @param array $clientes_id Ids de Clientes
     *
     * @return \App\Model\Entity\PontuacoesPendentes $array[]
     *  lista de pontuacoes pendentes
     */
    public function deleteAllPontuacoesPendentesByRedesId(array $redesId)
    {
        try {
            $redesClientesTable = TableRegistry::get("RedesHasClientes");
            $clientesIds = $redesClientesTable->getClientesIdsFromRedesHasClientes($redesId);

            if (sizeof($clientesIds) > 0) {
                return $this->deleteAll(array('clientes_id in' => $clientesIds));
            } else {
                return null;
            }
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $object = null;

            $stringError = __("Erro ao remover registro: {0}", $e->getMessage());

            Log::write('error', $stringError);

            return ['success' => false, 'message' => $stringError];
        }
    }

    /**
     * Remove todas as pontuacoes Pendentes por Id de Cliente
     *
     * @param array $clientes_id Ids de Clientes
     *
     * @return \App\Model\Entity\PontuacoesPendentes $array[]
     *  lista de pontuacoes pendentes
     */
    public function deleteAllPontuacoesPendentesByClientesIds(array $clientesIds)
    {
        try {
            return $this->deleteAll(array('clientes_id in' => $clientesIds));
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $object = null;

            $stringError = __("Erro ao remover registro: {0}", $e->getMessage());

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

    #endregion
}
