<?php
namespace App\Model\Table;

use Cake\Log\Log;
use Cake\Core\Configure;
use Cake\ORM\RulesChecker;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;


/**
 * BrindesPrecos Model
 *
 * @property \App\Model\Table\ClientesHasBrindesHabilitadosTable|\Cake\ORM\Association\BelongsTo $ClientesHasBrindesHabilitados
 *
 * @method \App\Model\Entity\BrindesPrecos get($primaryKey, $options = [])
 * @method \App\Model\Entity\BrindesPrecos newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\BrindesPrecos[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\BrindesPrecos|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\BrindesPrecos patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\BrindesPrecos[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\BrindesPrecos findOrCreate($search, callable $callback = null, $options = [])
 */
class BrindesPrecosTable extends GenericTable
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

        $this->setTable('brindes_precos');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Brindes', [
            'foreignKey' => 'brindes_id',
            'joinType' => 'INNER'
        ]);

        $this->belongsTo('Clientes', [
            'className' => 'Clientes',
            'foreignKey' => 'clientes_id',
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
            ->integer("brindes_id");

        $validator
            ->integer("clientes_id");

        $validator
            ->decimal('preco')
            ->requirePresence("preco", "create")
            ->notEmpty('preco');

        $validator
            ->decimal('valor_moeda_venda')
            ->requirePresence("valor_moeda_venda", "create")
            ->notEmpty('valor_moeda_venda');

        $validator
            ->dateTime('data_preco')
            ->requirePresence('data_preco', 'create')
            ->notEmpty('data_preco');

        $validator
            ->notEmpty('status_autorizacao');

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
        $rules->add($rules->existsIn(['brindes_id'], 'Brindes'));
        $rules->add($rules->existsIn(['clientes_id'], 'Clientes'));

        return $rules;
    }

    /**
     * -------------------------------------------------------------
     * Métodos Validação
     * -------------------------------------------------------------
     */

    /**
     * -------------------------------------------------------------
     * Métodos CRUD
     * -------------------------------------------------------------
     */

    #region Create

    /**
     * BrindesPrecosTable::addBrindePreco
     *
     * Adiciona um preço para brinde habilitado
     *
     * @param int $brindesId
     * @param int $clientesId
     * @param int $usuariosId
     * @param int $precoPadrao
     * @param int $valorMoedaVenda
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 01/09/2017
     *
     * @return (entity\BrindesPrecos) $entity
     **/
    public function addBrindePreco(int $brindesId, int $clientesId, int $usuariosId, string $statusAutorizacao, float $precoPadrao = null, float $valorMoedaVenda = null)
    {
        try {
            $brindeSave = $this->newEntity();

            $brindeSave["brindes_id"] = $brindesId;
            $brindeSave["clientes_id"] = $clientesId;
            $brindeSave["usuarios_id"] = $usuariosId;
            $brindeSave["preco"] = $precoPadrao;
            $brindeSave["valor_moeda_venda"] = $valorMoedaVenda;
            $brindeSave["status_autorizacao"] = $statusAutorizacao;
            $brindeSave["data_preco"] = date('Y-m-d H:i:s');

            return $this->save($brindeSave);
        } catch (\Exception $e) {
            $trace = $e->getTraceAsString();
            $stringError = __("Erro ao inserir registro: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write("error", $trace);
        }
    }

    #region Read

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function getPrecoBrindeById($id)
    {
        try {
            return $this->get($id);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao obter registro: {0} em: {1}", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Obtem todos os preços para o brinde habilitado id
     *
     * @param int $brindesId Id de brinde habilitado
     *
     * @return entity $preco
     **/
    public function getAllPrecoForBrindeHabilitadoId(int $brindesId, array $whereConditions = [], int $qteRegistros = null)
    {
        try {

            $conditionsSql = array();

            foreach ($whereConditions as $key => $value) {
                $conditionsSql[] = $value;
            }

            $conditionsSql[] = ['brindes_id' => $brindesId];

            $data = $this->find('all')
                ->where($conditionsSql)
                ->contain(array("Brindes"));

            if (!is_null($qteRegistros)) {
                $data = $data->limit($qteRegistros);
            }

            return $data;
        } catch (\Exception $e) {
            // @todo ajustar
            $trace = $e->getTraceAsString();
            $stringError = __("Erro ao obter registro: {0} em: {1}", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Obtem último Preço para Brinde Habilitado
     *
     * @param int $brindesId Id de clientes has brindes habilitados
     *
     * @return entity $preco
     **/
    public function getUltimoPrecoBrinde(int $brindesId, string $statusAutorizacao = null)
    {
        try {

            $conditions = array();

            $conditions["brindes_id"] =  $brindesId;
            if (!empty($statusAutorizacao)) {
                $conditions["status_autorizacao"] = $statusAutorizacao;
            }

            return $this->find('all')
                ->where($conditions)
                ->order(array('id' => 'desc'))
                // ->contain(array("Brindes"))
                ->select(array(
                    "id",
                    "preco",
                    "valor_moeda_venda",
                    "status_autorizacao",
                    "data_preco",
                ))->first();
        } catch (\Exception $e) {
            $trace = $e->getTraceAsString();

            $stringError = __("Erro ao obter registro: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write('error', $trace);
        }
    }

    /**
     * Obtem preço aguardando autorização
     *
     * @param array $arrayClientesId Lista de array de ids
     *
     * @return array $preços lista de registros
     */
    public function getPrecoAwaitingAuthorizationByClientesId(array $arrayClientesId = [])
    {
        try {
            return $this->find('all')
                ->where(
                    array(
                        'BrindesPrecos.status_autorizacao' => STATUS_AUTHORIZATION_PRICE_AWAITING,
                        'BrindesPrecos.clientes_id IN' => $arrayClientesId
                    )
                )
                ->order(array('data_preco' => 'desc'))
                ->contain(array('Clientes', 'Brindes'));
        } catch (\Exception $e) {
            $trace = $e->getTraceAsString();

            $stringError = __("Erro ao obter registro: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write('error', $trace);
        }
    }

    #region Update

    /**
     * Define todos os preços de brindes habilitados de um cliente para a matriz
     *
     * @param int $clientes_id Id de Cliente
     * @param int $matriz_id   Id da Matriz
     *
     * @return boolean
     */
    public function setBrindesPrecosToMainCliente(int $clientes_id, int $matriz_id)
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

            $stringError = __("Erro ao buscar registro: {0}, em {1}", $e->getMessage(), $object['file']);

            Log::write('error', $stringError);

            $error = ['result' => false, 'message' => $stringError];
            return $error;
        }
    }

    #region Delete

    /**
     * Apaga todos os preços para brindes habilitados de um cliente
     *
     * @param array $clientes_ids Ids de clientes
     *
     * @return boolean
     */
    public function deleteAllBrindesPrecosByClientesIds(array $clientes_ids)
    {
        try {
            return $this
                ->deleteAll(
                    [
                        'clientes_id in' => $clientes_ids
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

            $stringError = __("Erro ao buscar registro: {0}, em {1}", $e->getMessage(), $object['file']);

            Log::write('error', $stringError);

            $error = ['result' => false, 'message' => $stringError];
            return $error;
        }
    }
}
