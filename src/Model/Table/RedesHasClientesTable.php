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
use App\Custom\RTI\DebugUtil;

/**
 * RedesHasClientes Model
 *
 * @property \App\Model\Table\RedesHasClientesTable|\Cake\ORM\Association\BelongsTo $Redes
 * @property \App\Model\Table\ClientesTable|\Cake\ORM\Association\BelongsTo $Clientes
 *
 * @method \App\Model\Entity\RedesHasCliente get($primaryKey, $options = [])
 * @method \App\Model\Entity\RedesHasCliente newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\RedesHasCliente[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\RedesHasCliente|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\RedesHasCliente patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\RedesHasCliente[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\RedesHasCliente findOrCreate($search, callable $callback = null, $options = [])
 */
class RedesHasClientesTable extends GenericTable
{
    /**
     * -------------------------------------------------------------
     * Fields
     * -------------------------------------------------------------
     */
    protected $redes_has_clientes_table = null;

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
    private function _getRedesHasClientesTable()
    {
        if (is_null($this->redes_has_clientes_table)) {
            $this->_setRedesHasClientesTable();
        }
        return $this->redes_has_clientes_table;
    }

    /**
     * Method set of Redes table property
     *
     * @return void
     */
    private function _setRedesHasClientesTable()
    {
        $this->redes_has_clientes_table = TableRegistry::get('Redes_Has_Clientes');
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

        $this->setTable('redes_has_clientes');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo(
            'Redes',
            array(
                "className" => "Redes",
                'foreignKey' => 'redes_id',
                'joinType' => 'LEFT'
            )
        );

        $this->hasMany(
            'RedesHasClientesAdministradores',
            [
                'foreignKey' => 'redes_has_clientes_id',
                'joinTyp' => 'INNER'
            ]
        );

        $this->belongsTo(
            'Clientes',
            [
                'foreignKey' => 'clientes_id',
                'joinType' => 'INNER'
            ]
        );

        $this->belongsToMany(
            'ClientesHasUsuarios',
            [
                "className" => "ClientesHasUsuarios",
                'foreignKey' => 'clientes_id',
                'joinType' => 'INNER'
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
            ->boolean('ativado')
            ->allowEmpty('ativado');

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
        $rules->add($rules->existsIn(['clientes_id'], 'Clientes'));

        return $rules;
    }

    #region Create

    #endregion

    #region Read

    /**
     * Localiza a matriz de uma rede
     *
     * @param int $redes_id Id da Rede
     *
     * @return \App\Model\Entity\RedesHasCliente $rede_has_cliente
     */
    public function findMatrizOfRedesByRedesId(int $redes_id)
    {
        try {
            return $this->_getRedesHasClientesTable()->find('all')
                ->where(
                    array(
                        'redes_has_clientes.redes_id' => $redes_id,
                        'clientes.matriz' => true
                    )
                )
                ->contain(array('Redes', 'Clientes'))
                ->first();

        } catch (\Exception $e) {
            // TODO: Corrigir catch
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
     * Obtem os clientes ids através da pesquisa feita
     *
     * @param integer $redesId Id de Rede
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 25/06/2018
     *
     * @return \App\Model\Entity\RedesHasClientes[] $redes_has_clientes[]
     */
    public function getClientesIdsFromRedesHasClientes(int $redesId)
    {
        try {
            // pega o id da rede que pertence a unidade
            $clientesIdsQuery = $this->find('all')
                ->where(array('redes_id' => $redesId))
                ->select(array('clientes_id'));

            $clientesIds = array();

            foreach ($clientesIdsQuery as $item) {
                $clientesIds[] = $item["clientes_id"];
            }

            return $clientesIds;

        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao obter ids de Clientes de Rede: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Obtem todos os clientes e a rede pelo id da rede
     *
     * @param int $redes_id Id de Redes
     *
     * @return \App\Model\Entity\RedesHasClientes $redes_has_clientes[] Array
     */
    public function getRedesHasClientesById(int $id)
    {
        try {
            // return $this->_getRedesHasClientesTable()->find('all')
            //     ->where(['redes_has_clientes.id' => $id])
            return $this->find('all')
                ->where(['RedesHasClientes.id' => $id])
                ->contain(['Redes', 'Clientes'])
                ->first();

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
            Log::write('error', $trace);

            return ['success' => false, 'message' => $stringError];
        }
    }

    /**
     * RedesHasClientesTable::getAllRedesHasClientesIdsByRedesId
     *
     * Obtem todos os ids de clientes através de um id de rede
     *
     * @param integer $redesId Id da rede
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 2018/05/13
     *
     * @return \App\Model\Table\RedesHasClientesTable[]
     */
    public function getAllRedesHasClientesIdsByRedesId(int $redesId)
    {
        try {

            $result = $this->_getRedesHasClientesTable()->find('all')
                ->where(
                    [
                        'redes_id' => $redesId,
                    ]
                );

            return $result;

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
     * Obtem os ids de todas as unidades de uma rede através do id de um cliente
     *
     * @param int $clientes_id Id de clientes
     *
     * @return \App\Model\Entity\RedesHasClientes $redes_has_clientes[] Array
     */
    public function getAllRedesHasClientesIdsByClientesId(int $clientes_id)
    {
        try {

            // pega o id da rede que pertence a unidade
            $redes_id = $this->_getRedesHasClientesTable()->find('all')
                ->where(['clientes_id' => $clientes_id])
                ->first()
                ->redes_id;

            // pega todos os ids de unidades que pertencem à rede

            $unidades_ids = $this->_getRedesHasClientesTable()->find('all')
                ->where(['redes_id' => $redes_id])
                ->select(['clientes_id']);

            $unidades_ids = $this->retrieveColumnsQueryAsArray($unidades_ids, ['clientes_id']);

            return $unidades_ids;

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
     * Obtem o vinculo de rede pelo id do cliente
     *
     * @param int $clientes_id Id de clientes
     *
     * @return \App\Model\Entity\RedesHasClientes $redes_has_clientes[] Array
     */
    public function getRedesHasClientesByClientesId(int $clientes_id)
    {
        try {
            return $this->_getRedesHasClientesTable()->find('all')
                ->where(['clientes_id' => $clientes_id])
                ->contain(['Redes', 'Clientes'])->first();

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
     * Obtem o vinculo de rede pelo id do cliente
     *
     * @param array $clientes_ids Ids de clientes
     *
     * @return \App\Model\Entity\RedesHasClientes $redes_has_clientes[] Array
     */
    public function getRedesHasClientesByClientesIds(array $clientes_ids)
    {
        try {
            return $this->_getRedesHasClientesTable()->find('all')
                ->where(['clientes_id in' => $clientes_ids])
                ->distinct(['redes_id'])
                ->select(['redes_id']);

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
     * Obtem todos os clientes e a rede pelo id da rede
     *
     * @param int   $redesId     Id de Redes
     * @param array $clientesIds Ids de clientes
     *
     * @return \App\Model\Entity\RedesHasClientes $redes_has_clientes[] Array
     */
    public function getRedesHasClientesByRedesId(int $redesId, array $clientesIds = [])
    {
        try {

            // $whereCondition = [];

            $whereCondition = array('redes_id' => $redesId);

            if (isset($clientesIds) && sizeof($clientesIds) > 0) {
                $whereCondition[] = array('clientes_id in ' => $clientesIds);
            }

            return $this->find('all')
                ->where($whereCondition)
                ->contain(['Redes', 'Clientes']);

        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao obter registro: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);

            return ['success' => false, 'message' => $stringError];
        }
    }

    /**
     * Obtem todos os clientes e a rede pelo id da rede
     *
     * @param int   $redes_id     Id de Redes
     * @param array $clientes_ids Ids de clientes
     *
     * @return \App\Model\Entity\RedesHasClientes $redes_has_clientes[] Array
     */
    public function getClientesFromRedesIdAndParams(int $redes_id, string $nomeFantasia = null, string $razaoSocial = null, string $cnpj = null)
    {
        try {

            $whereCondition = array();

            $whereCondition[] = array('redes_id' => $redes_id);

            if (!empty($nomeFantasia)) {
                $whereCondition[] = array("Clientes.nome_fantasia like '%{$nomeFantasia}%'");
            }

            if (!empty($razaoSocial)) {
                $whereCondition[] = array("Clientes.razao_social like '%{$razaoSocial}%'");
            }

            if (!empty($cnpj)) {
                $whereCondition[] = array("Clientes.cnpj like '%{$cnpj}%'");
            }

            $redesHasClientes = $this->_getRedesHasClientesTable()->find('all')
                ->where($whereCondition)
                ->contain(['Redes', 'Clientes']);

            // if (sizeof($selectList) > 0) {
            //     $redesHasClientes = $redesHasClientes->select($selectList);
            // }

            return $redesHasClientes;

        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao obter registro: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);

            return ['success' => false, 'message' => $stringError];
        }
    }

    #endregion

    #region Update

    #endregion

    #region Delete

    /**
     * Remove uma unidade da rede
     *
     * @param array $clientes_ids Ids de clientes
     *
     * @return void
     */
    public function deleteRedesHasClientesByClientesIds(array $clientes_ids)
    {
        try {

            return
                $this->_getRedesHasClientesTable()->deleteAll(['clientes_id in' => $clientes_ids]);

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

    #endregion

}
