<?php
namespace App\Model\Table;

use ArrayObject;
use Cake\Event\Event;
use Cake\Log\Log;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use App\Custom\RTI\DebugUtil;

/**
 * Brindes Model
 *
 * @property \App\Model\Table\ClientesTable|\Cake\ORM\Association\BelongsTo $Clientes
 *
 * @method \App\Model\Entity\Brinde get($primaryKey, $options = [])
 * @method \App\Model\Entity\Brinde newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Brinde[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Brinde|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Brinde patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Brinde[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Brinde findOrCreate($search, callable $callback = null, $options = [])
 */
class BrindesTable extends GenericTable
{

    /**
     * -------------------------------------------------------------
     * Fields
     * -------------------------------------------------------------
     */
    protected $brindeTable = null;

    /**
     * -------------------------------------------------------------
     * Properties
     * -------------------------------------------------------------
     */

    /**
     * Method get of brinde table property
     *
     * @return [Cake\ORM\Table] Table object
     */
    private function _getBrindeTable()
    {
        if (is_null($this->brindeTable)) {
            $this->_setBrindeTable();
        }
        return $this->brindeTable;
    }

    /**
     * Method set of brinde table property
     *
     * @return void
     */
    private function _setBrindeTable()
    {
        $this->brindeTable = TableRegistry::get('Brindes');
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

        $this->setTable('brindes');
        $this->setDisplayField('nome');
        $this->setPrimaryKey('id');

        // relacionamento de brindes com matriz
        $this->belongsTo('Clientes', [
            'foreignKey' => 'clientes_id',
            'joinType' => 'INNER'
        ]);

        $this->hasMany(
            'ClientesHasBrindesHabilitados',
            [
                'foreignKey' => 'brindes_id',
                'joinType' => 'INNER'
            ]
        );

        $this->hasMany(
            'BrindesNaoHabilitados',
            [
                'className' => 'ClientesHasBrindesHabilitados',
                'foreignKey' => 'clientes_id',
                'strategy' => 'select'
            ]
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
            ->requirePresence('nome', 'create')
            ->notEmpty('nome');

        $validator
            ->boolean('equipamento_rti_shower');

        $validator
            ->integer("tipos_brindes_redes_id")
            ->requirePresence("tipos_brindes_redes_id", true);

        $validator
            ->integer('ilimitado')
            ->requirePresence('ilimitado', 'create')
            ->notEmpty('ilimitado');

        $validator
            ->decimal('preco_padrao')
            ->requirePresence('preco_padrao', 'create')
            ->notEmpty('preco_padrao');

        $validator
            ->decimal('valor_moeda_venda_padrao')
            ->requirePresence('valor_moeda_venda_padrao', 'create')
            ->allowEmpty('valor_moeda_venda_padrao');

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

        return $rules;
    }

    /**
     * -------------------------------------------------------------
     * Methods
     * -------------------------------------------------------------
     */

    /* ------------------------ Create ------------------------ */

    /* ------------------------ Read ------------------------ */


    /**
     * Procura brindes conforme filtros
     *
     * @param array $where_parameters Parametros de pesquisa
     * @param boolean $useContain Usar contain
     * @param array $containConditions Condições do contain
     * @param array $selectFields Campos de seleção
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     *
     * @date 01/07/2017

     * @return \App\Model\Entity\Brindes $brinde
     */
    public function findBrindes(array $where_parameters = [], bool $useContain = true, array $containConditions = array(), array $selectFields = array())
    {
        try {

            if (sizeof($containConditions) == 0 && $useContain) {
                $containConditions = array("Clientes");
            }

            $brindes = $this->find('all')
                ->where($where_parameters);

            if (sizeof($containConditions) == 0 && $useContain) {
                $brindes = $brindes->contain('Clientes');
            } else if ($useContain) {
                $brindes = $brindes->contain($containConditions);
            }

            if (sizeof($selectFields) > 0) {
                $brindes = $brindes->select($selectFields);
            }

            return $brindes;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao gravar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * BrindesTable::getBrindesIds
     *
     * Obtem Id de brindes conforme condições
     *
     * @param integer $id
     * @param integer $clientesIds
     * @param integer $tiposBrindesRedesId
     * @param string $nome
     * @param integer $tempoRtiShower
     * @param boolean $ilimitado
     * @param boolean $habilitado
     * @param float $precoPadrao
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 26/07/2018
     *
     * @return array $resultado
     */
    public function getBrindesIds(
        int $id = null,
        array $clientesIds = array(),
        int $tiposBrindesRedesId = null,
        string $nome = "",
        int $tempoRtiShower = null,
        bool $ilimitado = null,
        bool $habilitado = null,
        float $precoPadrao = null
    ) {

        try {
            $whereConditions = array();

            if (!empty($id)) {
                $whereConditions[] = array("id" => $id);
            }
            if (sizeof($clientesIds) > 0) {
                $whereConditions[] = array("clientes_id IN " => $clientesIds);
            }
            if (!empty($tiposBrindesRedesId)) {
                $whereConditions[] = array("tipos_brindes_redes_id" => $tiposBrindesRedesId);
            }
            if (!empty($nome)) {
                $whereConditions[] = array("nome like '%{$nome}%'");
            }
            if (!empty($tempoRtiShower)) {
                $whereConditions[] = array("tempo_rti_shower" => $tempoRtiShower);
            }
            if (!empty($ilimitado)) {
                $whereConditions[] = array("ilimitado" => $ilimitado);
            }
            if (!empty($habilitado)) {
                $whereConditions[] = array("habilitado" => $habilitado);
            }
            if (!empty($precoPadrao)) {
                $whereConditions[] = array("preco_padrao" => $precoPadrao);
            }

            $brindesQuery = $this->_getBrindeTable()->find("all")
                ->where($whereConditions)
                ->select(["id"]);

            $brindesIds = array();

            foreach ($brindesQuery as $brinde) {
                $brindesIds[] = $brinde["id"];
            }

            return $brindesIds;
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao obter ids de brindes: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write('error', $trace);
        }

    }

    /**
     * Find Brindes by Id
     *
     * @param int $clientes_id Id de Clientes
     *
     * @return \App\Model\Entity\Brindes $brinde
     **/
    public function getBrindesById($brindes_id)
    {
        try {
            return $this->get($brindes_id);

        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            // $this->Flash->error($stringError);
        }
    }

    /**
     * Find Brindes by Nome
     *
     * @param [string] $nome
     * @return \App\Model\Entity\Brindes $brinde
     * @author
     **/
    public function findBrindesByName($nome, $id = null)
    {
        try {

            $whereConditions = array();

            $whereConditions[] = array('nome' => $nome);

            if (!empty($id)) {
                $whereConditions[] = array("Brindes.id != " => $id);
            }

            return $this->_getBrindeTable()->find('all')
                ->where($whereConditions)
                ->contain('Clientes')
                ->first();

        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao gravar registro: " . $e->getMessage());

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Obtem lista de brindes através de array de clientes
     *
     * @param array $clientes_ids Ids de clientes
     *
     * @return App\Model\Entity\Brinde $brindes[]
     */
    public function getBrindesByClientes(array $clientes_ids)
    {
        try {

            return $this->_getBrindeTable()->find('all')
                ->where(['clientes_id in ' => $clientes_ids]);

        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao gravar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Obtêm brindes que não estão habilitados
     *
     * @param int   $clientes_id      Id da unidade que irá ativar os brindes
     * @param int   $matriz_id        Id da unidade principal
     * @param array $where_conditions Condições de Pesquisa extra
     *
     * @return App\Model\Entity\Brinde $brindes[]
     **/
    public function getBrindesHabilitarByClienteId(int $clientes_id, int $matriz_id, array $where_conditions = [])
    {
        try {

            $matriz_conditions = [];
            $clientes_conditions = [];

            foreach ($where_conditions as $key => $value) {
                array_push($matriz_conditions, $value);
            }

            array_push($matriz_conditions, ['Brindes.clientes_id' => $matriz_id]);

            foreach ($where_conditions as $key => $value) {
                array_push($clientes_conditions, $value);
            }

            array_push($clientes_conditions, ['ClientesHasBrindesHabilitados.clientes_id' => $clientes_id]);

            $brindes = $this->_getBrindeTable()->find('all')->where($matriz_conditions);

            $brindes_cliente = $this->_getBrindeTable()->ClientesHasBrindesHabilitados->find('all')->where($clientes_conditions)->contain(['Brindes']);

            $arrayToReturn = $brindes->toArray();

            // preciso percorrer item a item para ver quais items já estão habilitados
            foreach ($brindes as $key => $brinde) {

                foreach ($brindes_cliente as $key => $clienteBrinde) {

                    if ($brinde['id'] == $clienteBrinde['brindes_id']) {
                        $index = array_search($brinde, $arrayToReturn);
                        unset($arrayToReturn[$index]);
                    }
                }
            }

            return $arrayToReturn;

        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao gravar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /* ------------------------ Update ------------------------ */

    /**
     * Define todos os preços de brindes habilitados de um cliente para a matriz
     *
     * @param int $clientes_id Id de Cliente
     * @param int $matriz_id   Id da Matriz
     *
     * @return boolean
     */
    public function setBrindesToMainCliente(int $clientes_id, int $matriz_id)
    {
        try {
            return $this->updateAll(
                [
                    'clientes_id' => $matriz_id,
                    'habilitado' => false
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


    /* ------------------------ Delete ------------------------ */

    /**
     * Apaga todos os brindes de um cliente
     *
     * @param array $clientes_ids Ids de clientes
     *
     * @return boolean
     */
    public function deleteAllBrindesByClientesIds(array $clientes_ids)
    {

        try {
            return $this->_getBrindeTable()
                ->deleteAll(['clientes_id in' => $clientes_ids]);
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
