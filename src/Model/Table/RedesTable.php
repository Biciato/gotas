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
 * Redes Model
 *
 * @method \App\Model\Entity\Rede get($primaryKey, $options = [])
 * @method \App\Model\Entity\Rede newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Rede[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Rede|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Rede patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Rede[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Rede findOrCreate($search, callable $callback = null, $options = [])
 */
class RedesTable extends GenericTable
{

    /**
     * -------------------------------------------------------------
     * Fields
     * -------------------------------------------------------------
     */
    protected $redes_table = null;

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
    private function _getRedesTable()
    {
        if (is_null($this->redes_table)) {
            $this->_setRedesTable();
        }
        return $this->redes_table;
    }

    /**
     * Method set of Redes table property
     *
     * @return void
     */
    private function _setRedesTable()
    {
        $this->redes_table = TableRegistry::get('Redes');
    }

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     *
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('redes');
        $this->setDisplayField('nome_rede');
        $this->setPrimaryKey('id');

        $this->hasMany(
            'RedesHasClientes',
            [
                'className' => 'RedesHasClientes',
                'foreignKey' => 'redes_id',
                'join' => 'INNER'

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
            ->requirePresence('nome_rede', 'create')
            ->notEmpty('nome_rede');

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
     * -------------------------------------------------------------
     * Methods
     * -------------------------------------------------------------
     */

    /* ------------------------ Create ------------------------ */

    /**
     * Undocumented function
     *
     * @param string $nome_rede
     * @param string $nome_fantasia
     * @param bool   $ativado
     *
     * @return void
     */
    public function addRede(\App\Model\Entity\Rede $rede)
    {
        try {
            return $this->_getRedesTable()->save($rede);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $object = null;

            foreach ($trace as $key => $item_trace) {
                if ($item_trace['class'] == 'Cake\Database\Query') {
                    $object = $item_trace;
                    break;
                }
            }

            $stringError = __("Erro ao adicionar registro: {0}, em {1}", $e->getMessage(), $object['file']);

            Log::write('error', $stringError);

            $error = ['success' => false, 'message' => $stringError];
            return $error;
        }
    }

    /* ------------------------ Read ------------------------ */

    /**
     * RedesTable::findRedesByName
     *
     * Procura Redes por nome
     *
     * @param string $nomeRede Nome da rede
     * @param integer $qteRegistros Qte de Registros
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2018-08-12
     *
     * @return Cake\ORM\Query Registros contendo redes
     */
    public function findRedesByName(string $nomeRede, int $qteRegistros = null)
    {
        try {
            $options = array(
                "conditions" => array(
                    "nome_rede like '%$nomeRede%'"
                )

            );
            $redes = $this->find("all", $options);

            if ($qteRegistros > 0) {
                $redes = $redes->limit($qteRegistros);
            }

            return $redes;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao realizar pesquisa: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write('error', $trace);

        }
    }

    /**
     * RedesTable::getRedesList
     * Retorna uma lista de Redes para Select
     *
     * @param array $whereConditions Lista de condições
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 2018/05/13
     *
     * @return array ['id', 'nome']
     */
    public function getRedesList(array $whereConditions = [])
    {
        try {

            return $this->_getRedesTable()->find('list')
                ->where($whereConditions)
                ->select(['id', 'nome_rede']);
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

            $error = ['success' => false, 'message' => $stringError];
            return $error;
        }
    }

    /**
     * Obtem todas as redes
     *
     * @param string $queryType        Tipo de Query
     * @param array  $where_conditions Condições extras
     *
     * @return \App\Entity\Model\Redes $redes[] Lista de Redes
     */
    public function getAllRedes(string $queryType = null, array $where_conditions = [], bool $withAssociations = true)
    {
        try {

            $conditions = [];

            foreach ($where_conditions as $key => $value) {
                array_push($conditions, [$key => $value]);
            }

            $query = isset($queryType) ? $queryType : 'all';

            $redes = $this->_getRedesTable()->find($query)
                ->where($conditions);

            if ($withAssociations) {
                $redes = $redes->contain(
                    [
                        'RedesHasClientes',
                        'RedesHasClientes.RedesHasClientesAdministradores',
                        'RedesHasClientes.Clientes'
                    ]
                );
            }

            return $redes;
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

            $error = ['success' => false, 'message' => $stringError];
            return $error;
        }
    }

    /**
     * Obtem todas as redes Conforme condições
     *
     * @param array $whereConditions      Condições de where
     * @param array $associations         Lista de Associações
     * @param array $orderConditions      Condições de Ordenação
     * @param array $paginationConditions Condições de paginação
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @data   2018/05/13
     *
     * @return array("count", "data") \App\Entity\Model\Redes $redes[] Lista de Redes
     */
    public function getRedes(array $whereConditions = [], array $selectFields = array(), array $associations = [], array $orderConditions = [], array $paginationConditions = [])
    {
        try {

            $conditions = [];

            foreach ($whereConditions as $key => $value) {
                // array_push($conditions, $value);
                array_push($conditions, [$key => $value]);
            }

            // DebugUtil::printArray($whereConditions);
            // DebugUtil::printArray($conditions);

            $redesQuery = $this->_getRedesTable()->find('all')
                ->where($conditions);

            if (sizeof($selectFields) > 0) {
                $redesQuery = $redesQuery->select($selectFields);
            }

            $redesTodas = $redesQuery->toArray();
            $redesAtual = $redesQuery->toArray();

            // DebugUtil::printArray($redesTodas, false);
            // DebugUtil::printArray($redesAtual, true);

            $retorno = $this->prepareReturnDataPagination($redesTodas, $redesAtual, "redes", $paginationConditions);

            if ($retorno["mensagem"]["status"] == 0) {
                return $retorno;
            }

            // DebugUtil::printArray($retorno);

            // $count = $redes->count();

            if (sizeof($orderConditions) > 0) {
                $redesQuery = $redesQuery->order($orderConditions);
            }

            if (sizeof($paginationConditions) > 0) {
                $redesQuery = $redesQuery->limit($paginationConditions["limit"])
                    ->page($paginationConditions["page"]);
            }

            $redesAtual = $redesQuery->toArray();

            $retorno = $this->prepareReturnDataPagination($redesTodas, $redesAtual, "redes", $paginationConditions);

            return $retorno;
            // Retorna mensagem de que não retornou dados se for page 1. Se for page 2, apenas não exibe.
            if (sizeof($redes->toArray()) == 0) {

                $retorno = array(
                    "count" => 0,
                    "page_count" => 0,
                    "mensagem" => array(
                        "status" => false,
                        "message" => __(""),
                        "errors" => array()
                    ),
                    "data" => array(),
                );
                if ($paginationConditions["page"] == 1) {
                    $retorno = array(
                        "count" => 0,
                        "page_count" => 0,
                        "mensagem" => array(
                            "status" => false,
                            "message" => Configure::read("messageQueryNoDataToReturn"),
                            "errors" => array()
                        ),
                        "data" => array(),
                    );
                } else {
                    $retorno["page_count"] = 0;
                    $retorno["mensagem"] = array(
                        "status" => false,
                        "message" => Configure::read("messageQueryPaginationEnd"),
                        "errors" => array()
                    );
                }
                return $retorno;
            }

            $redes = $redes->contain($associations);

            if (sizeof($orderConditions) > 0) {
                $redes = $redes->order($orderConditions);
            }

            if (sizeof($paginationConditions) > 0) {
                $redes = $redes->limit($paginationConditions["limit"])
                    ->page($paginationConditions["page"]);
            }

            $pageCount = $redes->count();

            $retorno = array(
                "count" => $count,
                "page_count" => sizeof($pageCount),
                "mensagem" => array(
                    "status" => true,
                    "message" => Configure::read("messageLoadDataWithSuccess"),
                    "errors" => array()
                ),
                "data" => $redes->toArray(),
            );

            return $retorno;

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

            $error = ['success' => false, 'message' => $stringError];
            return $error;
        }
    }

    /**
     * Obtêm rede por id
     *
     * @param int $id Id da Rede
     *
     * @return \App\Model\Entity\Rede
     */
    public function getRedeById(int $id, bool $ativado = true)
    {
        try {
            return $this->_getRedesTable()->find('all')
                ->where(
                    [
                        'id' => $id,
                        'ativado' => $ativado
                    ]
                )
                ->contain(
                    [
                        'RedesHasClientes',
                        'RedesHasClientes.RedesHasClientesAdministradores',
                        'RedesHasClientes.Clientes.ClientesHasBrindesHabilitados.Brindes'
                    ]
                )
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

            $error = ['success' => false, 'message' => $stringError];

            throw new \Exception($stringError);

            return $error;
        }
    }

    /* ------------------------ Update ------------------------ */

    /**
     * Troca estado de unidade
     *
     * @param int  $id      Id de RedesHasClientes
     * @param bool $ativado Estado de ativação
     *
     * @return \App\Model\Entity\Clientes $rede
     */
    public function changeStateEnabledRede(int $id, bool $ativado)
    {
        try {

            $rede = $this->_getRedesTable()->find('all')
                ->where(['id' => $id])
                ->contain(['RedesHasClientes'])
                ->first();

            $clientes_ids = [];

            foreach ($rede->redes_has_clientes as $key => $value) {
                array_push($clientes_ids, $value->clientes_id);
            }

            // troca o estado dos registros pertencentes à uma rede
            $clientes_table = TableRegistry::get('Clientes');

            if (sizeof($clientes_ids) > 0) {
                $clientes_table->updateAll(
                    [
                        'ativado' => $ativado
                    ],
                    [
                        'id IN ' => $clientes_ids
                    ]
                );
            }

            $rede->ativado = $ativado;

            return $this->_getRedesTable()->save($rede);

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
     * Atualiza dados de rede
     *
     * @param \App\Model\Entity\Redes $rede Objeto de rede
     *
     * @return \App\Model\Entity\Redes Objeto de redes atualizado
     */
    public function updateRede(\App\Model\Entity\Rede $rede)
    {
        try {

            return $this->_getRedesTable()->save($rede);

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
     * Remove uma rede
     *
     * @param int $id Id da Rede
     *
     * @return boolean
     */
    public function deleteRedesById(int $id)
    {
        try {

            return
                $this->_getRedesTable()->deleteAll(['id' => $id]);

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
