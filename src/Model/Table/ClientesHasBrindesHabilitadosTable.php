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
use App\Custom\RTI\DebugUtil;

/**
 * ClientesHasBrindesHabilitados Model
 *
 * @property \App\Model\Table\ClientesTable|\Cake\ORM\Association\BelongsTo $Clientes
 * @property \App\Model\Table\BrindesHabilitadosTable|\Cake\ORM\Association\BelongsTo $BrindesHabilitados
 *
 * @method \App\Model\Entity\ClientesHasBrindesHabilitado get($primaryKey, $options = [])
 * @method \App\Model\Entity\ClientesHasBrindesHabilitado newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ClientesHasBrindesHabilitado[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ClientesHasBrindesHabilitado|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ClientesHasBrindesHabilitado patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ClientesHasBrindesHabilitado[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ClientesHasBrindesHabilitado findOrCreate($search, callable $callback = null, $options = [])
 */
class ClientesHasBrindesHabilitadosTable extends GenericTable
{

    /**
     * -------------------------------------------------------------
     * Fields
     * -------------------------------------------------------------
     */
    protected $clientesHasBrindesHabilitadosTable = null;

    protected $clientesHasBrindesHabilitadosQuery = null;


    /**
     * -------------------------------------------------------------
     * Properties
     * -------------------------------------------------------------
     */

    /**
     * Method get of brinde table property
     *
     * @return Cake\ORM\Table Table object
     */
    private function _getClientesHasBrindesHabilitadosTable()
    {
        if (is_null($this->clientesHasBrindesHabilitadosTable)) {
            $this->_setClientesHasBrindesHabilitadosTable();
        }
        return $this->clientesHasBrindesHabilitadosTable;
    }

    /**
     * Method set of brinde table property
     * @return void
     */
    private function _setClientesHasBrindesHabilitadosTable()
    {
        $this->clientesHasBrindesHabilitadosTable = TableRegistry::get('ClientesHasBrindesHabilitados');
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

        $this->setTable('clientes_has_brindes_habilitados');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Clientes', [
            'foreignKey' => 'clientes_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('ClientesHasBrindesHabilitados', [
            'foreignKey' => 'clientes_has_brindes_habilitados_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Brindes', [
            'foreignKey' => 'brindes_id',
            'joinType' => 'INNER'
        ]);

        $this->hasOne(
            'BrindeHabilitadoPrecoAtual',
            [
                'className' => 'ClientesHasBrindesHabilitadosPreco',
                'foreignKey' => 'clientes_has_brindes_habilitados_id',
                // 'joinType' => 'inner',
                'strategy' => 'select',
                'conditions' =>
                    array(
                    'status_autorizacao' => 1,
                    "preco IS NOT NULL"
                ),
                'order' => ['id' => 'desc']
            ]
        );




                    //    $this->hasOne('BrindeHabilitadoPrecoAtual', [
                    //    'className' => 'ClientesHasBrindesHabilitadosPreco',
                    //    'foreignKey' => 'clientes_has_brindes_habilitados_id',
                    //    'strategy' => 'select',
                    //    'sort' => ['data_preco' => 'desc'],
                    //    'conditions' => ['status_autorizacao' => 1]
                    //    ]);

        $this->hasMany(
            'BrindesHabilitadosUltimosPrecos',
            [
                'className' => 'ClientesHasBrindesHabilitadosPreco',
                'foreignKey' => 'clientes_has_brindes_habilitados_id',
                'joinType' => 'INNER',
                'sort' => ['data_preco' => 'desc']
            ]
        );

        $this->hasOne(
            'BrindesEstoqueAtual',
            [
                'className' => 'ClientesHasBrindesEstoque',
                'foreignKey' => 'clientes_has_brindes_habilitados_id',
                'joinType' => 'INNER'
            ]
        );

        $this->hasMany(
            'Pontuacoes',
            [
                'foreignKey' => 'clientes_has_brindes_habilitados_id',
                'joinType' => 'INNER'
            ]
        );

        $this->belongsTo(
            "TiposBrindesClientes",
            array(
                "className" => "TiposBrindesClientes",
                "foreignKey" => "tipos_brindes_clientes_id",
                "joinType" => "LEFT"
            )
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
            ->notEmpty('brindes_id', 'create');

        $validator
            ->notEmpty('clientes_id', 'create');

        $validator
            ->notEmpty('habilitado', 'create');

        $validator
            ->notEmpty('tipo_codigo_barras', 'create');

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
        $rules->add($rules->existsIn(['clientes_has_brindes_habilitados_id'], 'ClientesHasBrindesHabilitados'));

        return $rules;
    }

    /**
     * -------------------------------------------------------------
     * Methods
     * -------------------------------------------------------------
     */

    #region Create

    /**
     * Add a BrindeHabilitado for a Cliente
     *
     * @param int $clientes_id
     * @param int $brindes_habilitados_id
     * @return entity\ClienteHasBrindesHabilitados $entity
     * @author
     **/
    public function addClienteHasBrindeHabilitado($clientesId, $brindesId, $tiposBrindesClientesId)
    {
        try {
            $clienteHasBrindeHabilitado = $this->_getClientesHasBrindesHabilitadosTable()->newEntity();

            $clienteHasBrindeHabilitado->clientes_id = $clientesId;
            $clienteHasBrindeHabilitado->brindes_id = $brindesId;
            $clienteHasBrindeHabilitado->tipos_brindes_clientes_id = $tiposBrindesClientesId;
            $clienteHasBrindeHabilitado->habilitado = 1;

            return $this->_getClientesHasBrindesHabilitadosTable()->save($clienteHasBrindeHabilitado);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao inserir registro: {0} em: {1}", $e->getMessage(), $trace);

            Log::write('error', $stringError);
        }
    }

    #endregion

    #region Read

    /**
     * ClientesHasBrindesHabilitadosTable::findClientesHasBrindesHabilitados
     *
     * Realiza pesquisa genérica
     *
     * @param array $whereConditions Condições de pesquisa
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 06/07/2018
     *
     * @return \App\Model\Entity\ClientesHasBrindesHabilitado[] $brindesHabilitados
     */
    public function findClientesHasBrindesHabilitados(array $whereConditions)
    {
        try {

            $data = $this->_getClientesHasBrindesHabilitadosTable()
                ->find('all')
                ->where($whereConditions);

            return $data;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao realizar busca: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write("error", $trace);

            $error = array('status' => false, 'message' => $stringError, "errors" => array());
            return $error;
        }
    }

    /**
     * Obtêm um Brinde Habilitado para Cliente através do Brindes Id
     *
     * @param int $id id do brinde
     *
     * @return entity\ClientesHasBrindesHabilitados $entity
     **/
    public function getBrindeHabilitadoById($id)
    {
        try {
            $brinde = $this->_getClientesHasBrindesHabilitadosTable()->find('all')->where(['ClientesHasBrindesHabilitados.id' => $id])
                ->contain(
                    array(
                        'Clientes',
                        'Brindes',
                        'BrindeHabilitadoPrecoAtual',
                        "TiposBrindesClientes"
                    )
                )
                ->first();

            // Cálculo de estoque do item
            $queryEntrada = $this->_getClientesHasBrindesHabilitadosTable()->BrindesEstoqueAtual->find();
            $querySaidaBrinde = $this->_getClientesHasBrindesHabilitadosTable()->BrindesEstoqueAtual->find();
            $querySaidaVenda = $this->_getClientesHasBrindesHabilitadosTable()->BrindesEstoqueAtual->find();
            $queryDevolucao = $this->_getClientesHasBrindesHabilitadosTable()->BrindesEstoqueAtual->find();

            $resultEntrada = $queryEntrada->select([
                'sum' => $queryEntrada->func()->sum('quantidade')
            ])->where(['tipo_operacao' => 0, 'clientes_has_brindes_habilitados_id' => $id])->first();

            $resultSaidaBrinde = $querySaidaBrinde->select([
                'sum' => $querySaidaBrinde->func()->sum('quantidade')
            ])->where(['tipo_operacao' => 1, 'clientes_has_brindes_habilitados_id' => $id])->first();

            $resultSaidaVenda = $querySaidaVenda->select([
                'sum' => $querySaidaVenda->func()->sum('quantidade')
            ])->where(['tipo_operacao' => 2, 'clientes_has_brindes_habilitados_id' => $id])->first();

            $resultDevolucao = $queryDevolucao->select([
                'sum' => $queryDevolucao->func()->sum('quantidade')
            ])->where(['tipo_operacao' => 3, 'clientes_has_brindes_habilitados_id' => $id])->first();


            $entrada = is_null($resultEntrada['sum']) ? 0 : $resultEntrada['sum'];
            $saidaBrinde = is_null($resultSaidaBrinde['sum']) ? 0 : $resultSaidaBrinde['sum'];
            $saidaVenda = is_null($resultSaidaVenda['sum']) ? 0 : $resultSaidaVenda['sum'];
            $devolucao = is_null($resultDevolucao['sum']) ? 0 : $resultDevolucao['sum'];

            $estoque = [($entrada + $devolucao) - ($saidaBrinde + $saidaVenda)];

            // DebugUtil::print($id);
            $brinde['estoque'] = $estoque;

            return $brinde;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registros: {0} em: {1}", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Obtêm brinde habilitado
     *
     * @param array $where_conditions Condições de pesquisa
     *
     * @return App\Model\Entity\ClientesHasBrindesHabilitado
     **/
    public function getBrindeHabilitadoByBrindeId($id)
    {
        try {
            $brinde = $this->_getClientesHasBrindesHabilitadosTable()
                ->find('all')
                ->where(
                    array("ClientesHasBrindesHabilitados.id" => $id)
                )->contain(
                    array(
                        'Brindes',
                        'BrindeHabilitadoPrecoAtual'
                    )
                )
                ->first();

            return $brinde;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registros: {0} em: {1}", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * ClientesHasBrindesHabilitadosTable::getBrindeHabilitadoByBrindesIdClientesId
     *
     * Obtem Brinde Habilitado de um cliente pelo Brindes Id e Clientes Id
     *
     * @param integer $brindesId Id de brindes
     * @param integer $clientesId Id de clientes
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 01/07/2018
     *
     * @return App\Model\Entity\ClientesHasBrindesHabilitado
     */
    public function getBrindeHabilitadoByBrindesIdClientesId(int $brindesId, int $clientesId)
    {
        try {
            $brinde = $this->_getClientesHasBrindesHabilitadosTable()->find('all')->where(
                array(
                    "ClientesHasBrindesHabilitados.brindes_id" => $brindesId,
                    "ClientesHasBrindesHabilitados.clientes_id" => $clientesId
                )
            )
                ->contain(
                    array(
                        "Brindes",
                        "BrindeHabilitadoPrecoAtual",
                        "TiposBrindesClientes"
                    )
                )->first();

            return $brinde;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao obter brindes de unidade: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write("error", $trace);

            $error = array('status' => false, 'message' => $stringError, "errors" => array());
            return $error;
        }
    }

    /**
     * Obtêm todos os brindes de um cliente conforme tipo
     *
     * @param int  $clientes_id Id de CLiente
     * @param int $tiposBrindesClientesIds Ids de Tipo
     *
     * @return App\Model\Entity\ClientesHasBrindesHabilitado
     */
    public function getBrindesPorClienteId(
        int $clientesId,
        array $tiposBrindesClientesIds = array(),
        array $whereConditionsBrindes = array(),
        float $precoMin = null,
        float $precoMax = null,
        array $orderConditionsBrindes = array(),
        array $paginationConditionsBrindes = array(),
        array $filterTiposBrindesClientesColumns = array()
    ) {
        try {

            // Verifica se ordenação ordena algum campo de preço do brinde

            $brindesPrecosOrdenacao = array();
            $prefix = "brinde_habilitado_preco_atual_";

            $orderConditionsBrindesNew = array();
            foreach ($orderConditionsBrindes as $key => $value) {
                $pos = stripos($key, $prefix);

                if ($pos > -1) {
                    $key = substr($key, strlen($prefix));

                    $brindesPrecosOrdenacao[] = array($key => $value);
                } else {
                    $orderConditionsBrindesNew[$key] = $value;
                }
            }

            $orderConditionsBrindes = $orderConditionsBrindesNew;

            $brindesPrecoOrdenacao = null;
            if (sizeof($brindesPrecosOrdenacao) >= 1) {
                $brindesPrecoOrdenacao = $brindesPrecosOrdenacao[0];
            }

            // Primeiro, faz a consulta dos dos ids de unidades de cada rede
            $redesHasClientesTable = TableRegistry::get("RedesHasClientes");
            $redesHasCliente = $redesHasClientesTable->getRedesHasClientesByClientesId($clientesId);

            $clientesIds = $redesHasClientesTable->getClientesIdsFromRedesHasClientes($redesHasCliente["rede"]["id"]);

            // Então, faz a consulta dos brindes
            $whereConditionsBrindes[] = array(
                "clientes_id in " => $clientesIds,
                "habilitado" => 1
            );

            $brindesTable = TableRegistry::get("Brindes");
            $brindes = $brindesTable->findBrindes($whereConditionsBrindes, false);

            // DebugUtil::print($brindes->toArray());

            if (sizeof($orderConditionsBrindes) > 0) {
                $brindes = $brindes->order($orderConditionsBrindes);
            }

            // DebugUtil::print($brindes->toArray());

            $count = $brindes->count();

            if (sizeof($paginationConditionsBrindes) > 0) {
                $brindes = $brindes
                    ->limit($paginationConditionsBrindes["limit"])
                    ->page($paginationConditionsBrindes["page"]);
            }

            // $brindesIdsQuery = $brindes->select(['id', 'nome_img'])->toArray();
            $brindesIdsQuery = $brindes->select(['id'])->toArray();

            // DebugUtil::print($brindesIdsQuery);

            // Retorna mensagem de que não retornou dados se for page 1. Se for page 2, apenas não exibe.
            if (sizeof($brindesIdsQuery) == 0) {

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
                if ($paginationConditionsBrindes["page"] == 1) {
                    $retorno = array(
                        "brindes" => array(
                            "count" => 0,
                            "page_count" => 0,
                            "data" => array()
                        ),
                        "mensagem" => array(
                            "status" => false,
                            "message" => Configure::read("messageQueryNoDataToReturn"),
                            "errors" => array()
                        )
                    );
                } else {
                    $retorno = array(
                        "page_count" => 0,
                        "mensagem" => array(
                            "status" => false,
                            "message" => Configure::read("messageQueryPaginationEnd"),
                            "errors" => array()
                        ),
                        "brindes" => array(
                            "count" => 0,
                            "page_count" => 0,
                            "data" => array()
                        )

                    );
                }
                return $retorno;
            }

            $brindesIds = array();

            foreach ($brindesIdsQuery as $key => $brinde) {
                $brindesIds[] = $brinde["id"];
            }

            // DebugUtil::print($brindesIds);

            $clientesBrindesHabilitadosWhereConditions = array();

            $clientesBrindesHabilitadosWhereConditions[] = array('ClientesHasBrindesHabilitados.tipo_codigo_barras IS NOT NULL');
            $clientesBrindesHabilitadosWhereConditions[] = array('ClientesHasBrindesHabilitados.tipos_brindes_clientes_id IS NOT NULL');
            $clientesBrindesHabilitadosWhereConditions[] = array('ClientesHasBrindesHabilitados.habilitado' => 1);
            $clientesBrindesHabilitadosWhereConditions[] = array('ClientesHasBrindesHabilitados.clientes_id' => $clientesId);

            if (isset($tiposBrindesClientesIds) && sizeof($tiposBrindesClientesIds) > 0) {
                $clientesBrindesHabilitadosWhereConditions[] = array("ClientesHasBrindesHabilitados.tipos_brindes_clientes_id in " => $tiposBrindesClientesIds);
            }

            // DebugUtil::printArray($tiposBrindesClientesIds);
            // DebugUtil::printArray($clientesBrindesHabilitadosWhereConditions);
            $containArray = array(
                "Brindes",
                "Clientes"
            );

            if (sizeof($filterTiposBrindesClientesColumns)) {
                $containArray["TiposBrindesClientes"] = array("fields" => $filterTiposBrindesClientesColumns);
            } else {
                $containArray[] = "TiposBrindesClientes";
            }

            $clientesBrindesHabilitados = array();

            $tiposBrindesClientesTable = TableRegistry::get("TiposBrindesClientes");

            foreach ($brindesIds as $key => $brindeId) {

                $whereConditions = $clientesBrindesHabilitadosWhereConditions;

                $whereConditions[] = array("ClientesHasBrindesHabilitados.brindes_id" => $brindeId);

                $clientesBrindesHabilitado = $this
                    ->find('all')
                    ->where($whereConditions)
                    ->contain($containArray)
                    ->first();

                    // die();

                // DebugUtil::print($clientesBrindesHabilitado);
                // DebugUtil::print($clientesBrindesHabilitado, true, false);

                $brinde_habilitado_preco_table = TableRegistry::get('ClientesHasBrindesHabilitadosPreco');

                $brinde = $brindesTable->getBrindesById($brindeId);

                if (!empty($clientesBrindesHabilitado) && ($clientesBrindesHabilitado["tipos_brindes_clientes_id"] != 0)) {

                    $clientesBrindesHabilitado["brinde"] = $brinde;

                    $clientesBrindesHabilitado['brinde_habilitado_preco_atual'] = $brinde_habilitado_preco_table
                        ->find('all')
                        ->where(
                            array(
                                'status_autorizacao' => (int)Configure::read('giftApprovalStatus')['Allowed'],
                                'clientes_has_brindes_habilitados_id' => $clientesBrindesHabilitado["id"]
                            )

                        )
                        ->order(['id' => 'DESC'])
                        ->first();

                    $clientesBrindesHabilitados[] = $clientesBrindesHabilitado;
                } else {
                    $count -= 1;
                }
            }

            $clientesBrindesHabilitadosReturn = array();

            // DebugUtil::print($clientesBrindesHabilitados);

            if ($precoMin == 0 && $precoMax == 0) {
                $clientesBrindesHabilitadosReturn = $clientesBrindesHabilitados;
            } else {
                // Faz pesquisa por preço
                foreach ($clientesBrindesHabilitados as $brindeHabilitado) {

                    $podeAdicionar = false;

                    if ($precoMin > 0 && $precoMax > 0) {
                        if ($brindeHabilitado["brinde_habilitado_preco_atual"]["preco"] >= $precoMin
                            && $brindeHabilitado["brinde_habilitado_preco_atual"]["preco"] <= $precoMax) {
                            $podeAdicionar = true;
                        } else $count -= 1;
                    } else if ($precoMin > 0) {
                        if ($brindeHabilitado["brinde_habilitado_preco_atual"]["preco"] >= $precoMin) {
                            $podeAdicionar = true;
                        } else $count -= 1;
                    } else if ($precoMax > 0) {
                        if ($brindeHabilitado["brinde_habilitado_preco_atual"]["preco"] <= $precoMax) {
                            $podeAdicionar = true;
                        } else $count -= 1;
                    } else {
                        $count -= 1;
                    }

                    if ($podeAdicionar) {
                        $clientesBrindesHabilitadosReturn[] = $brindeHabilitado;
                    }
                }
            }

            // Se especificar ordenacao

            if ($brindesPrecoOrdenacao) {

                usort($clientesBrindesHabilitadosReturn, function ($a, $b) use ($brindesPrecoOrdenacao) {
                    $key = key($brindesPrecoOrdenacao);

                    if (strtoupper($brindesPrecoOrdenacao[$key]) == "ASC") {
                        return $a["brinde_habilitado_preco_atual"][$key] > $b["brinde_habilitado_preco_atual"][$key];
                    } else {
                        return $a["brinde_habilitado_preco_atual"][$key] < $b["brinde_habilitado_preco_atual"][$key];
                    }
                });
            }

            if (sizeof($clientesBrindesHabilitadosReturn) > 0) {
                $clientesBrindesHabilitados = $clientesBrindesHabilitadosReturn;
            }

            $retorno = array(
                "brindes" => array(
                    // "count" => $precoMin > 0 || $precoMax > 0 ? sizeof($clientesBrindesHabilitadosReturn) : $count,
                    "count" => $count,
                    "page_count" => sizeof($clientesBrindesHabilitados),
                    "data" => $clientesBrindesHabilitados
                ),
                "mensagem" => array(
                    "status" => sizeof($clientesBrindesHabilitados) > 0,
                    "message" => sizeof($clientesBrindesHabilitados) > 0 ? Configure::read("messageLoadDataWithSuccess") : Configure::read("messageQueryNoDataToReturn"),
                    "errors" => array()
                ),
            );

            return $retorno;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao obter brindes de unidade: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write("error", $trace);

            $error = array('status' => false, 'message' => $stringError, "errors" => array());
            return $error;
        }
    }

    /**
     * ClientesHasBrindesHabilitadosTable::getBrindesHabilitadosIds
     *
     * Obtem todos os brindes habilitados para cliente usando o Clientes Id
     *
     * @param array $brindesIds Array de brindesIds
     * @param array $clientesIds Array de clientesIds
     * @param string $tipoCodigoBarras Tipo de código de barras
     * @param array $tiposBrindesClientesIds Array de tiposBrindesClientesIds
     * @param boolean $habilitado
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 26/07/2018
     *
     * @return entity\ClientesHasBrindesHabilitados $entity
     */
    public function getBrindesHabilitadosIds(
        array $brindesIds = array(),
        array $clientesIds = array(),
        string $tipoCodigoBarras = "",
        array $tiposBrindesClientesIds = array(),
        bool $habilitado = null
    ) {

        try {

            if (sizeof($brindesIds) > 0) {
                $whereConditions[] = array("brindes_id in " => $brindesIds);
            }
            if (sizeof($clientesIds) > 0) {
                $whereConditions[] = array("clientes_id in " => $clientesIds);
            }
            if (!empty($tipoCodigoBarras)) {
                $whereConditions[] = array("tipo_codigo_barras" => $tipoCodigoBarras);
            }
            if (sizeof($tiposBrindesClientesIds) > 0) {
                $whereConditions[] = array("tipos_brindes_clientes_id" => $tiposBrindesClientesIds);
            }
            if (!empty($ihabilitado)) {
                $whereConditions[] = array("habilitado" => $habilitado);
            }

            $brindesHabilitadosQuery = $this->_getClientesHasBrindesHabilitadosTable()
                ->find("all")
                ->where($whereConditions)
                ->select(["id"]);

            $brindesHabilitadosIds = array();

            foreach ($brindesHabilitadosQuery as $brindeHabilitado) {
                $brindesHabilitadosIds[] = $brindeHabilitado["id"];
            }

            return $brindesHabilitadosIds;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registros: {0} em: {1}", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Obtem todos os brindes habilitados para cliente usando o Clientes Id
     *
     * @param array $clientes_ids     Ids de cliente
     * @param array $where_conditions Condições Extras
     *
     * @return entity\ClientesHasBrindesHabilitados $entity
     */
    public function getBrindesHabilitadosByClienteId(array $clientes_ids, array $where_conditions = [])
    {
        try {
            $conditions = [];

            array_push($conditions, ['Brindes.habilitado' => true]);

            foreach ($where_conditions as $key => $value) {
                array_push($conditions, $value);
            }

            $brindes_cliente = $this->_getClientesHasBrindesHabilitadosTable()->find('all')
                ->where($where_conditions)
                ->contain(
                    [
                        'Brindes' =>
                            [
                            'strategy' => 'join',
                            'queryBuilder' => function ($q) use ($clientes_ids) {
                                return $q->where(
                                    [
                                        'ClientesHasBrindesHabilitados.clientes_id IN ' => $clientes_ids,
                                    ]
                                );
                            }
                        ],
                        'Clientes',
                        'BrindeHabilitadoPrecoAtual'
                    ]
                )
                ->order(['ClientesHasBrindesHabilitados.id' => 'asc']);

            return $brindes_cliente;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registros: {0} em: {1}", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Obtem todos os brindes habilitados (ou não) para cliente usando o Clientes Id
     *
     * @param array $clientes_ids     Ids de cliente
     * @param array $where_conditions Condições Extras
     *
     * @return entity\ClientesHasBrindesHabilitados $entity
     */
    public function getTodosBrindesByClienteId(array $clientes_ids, array $where_conditions = [])
    {
        try {
            $conditions = [];

            // obtem todas as unidades que estão vinculada à aquela unidade

            $redeHasClienteTable = TableRegistry::get("RedesHasClientes");

            // Cliente que quero aplicar a configuração
            $clienteAplicarConfiguracaoId = $clientes_ids[0];

            $clientesIdsQuery = $redeHasClienteTable->getAllRedesHasClientesIdsByClientesId($clienteAplicarConfiguracaoId);
            $clientesIds = array();

            foreach ($clientesIdsQuery as $key => $value) {
                $clientesIds[] = $value["clientes_id"];
            }

            /**
             * Obtem os brindes daquela rede
             */
            $brindeTable = TableRegistry::get("Brindes");
            $brindesRede = $brindeTable->find('all')
                ->where(array(
                    "habilitado" => 1,
                    "clientes_id in " => $clientesIds
                ))->toArray();

            // Obtem os ids dos brindes da rede

            $brindesRedeIds = array();

            foreach ($brindesRede as $key => $brinde) {
                $brindesRedeIds[] = $brinde["id"];
            }

            /**
             * Brindes não configurados será iterado e aquele que constar na lista será removido.
             * em contrapartida, aquele removido será adicionado em Brindes Configurados Ids
             */
            $brindesConfiguradosIds = array();
            $brindesNaoConfiguradosIds = $brindesRedeIds;

            // echo 'oi';

            // DebugUtil::print($brindesRede);
            // DebugUtil::print($brindesRedeIds);
            // Obtem todos os brindes que estão configurados para aquela unidade.

            $brindesAtribuidos = $this->find("all")
                ->where(
                    array(
                        "brindes_id in " => $brindesRedeIds,
                        "clientes_id" => $clienteAplicarConfiguracaoId
                    )
                )->toArray();



            foreach ($brindesAtribuidos as $key => $brindeAtribuido) {
                if (in_array($brindeAtribuido["brindes_id"], $brindesNaoConfiguradosIds)) {
                    $brindesConfiguradosIds[] = $brindeAtribuido["brindes_id"];
                }
            }

            $brindesNaoAtribuidos = array();

            $brindesNaoAtribuidosWhereConditions = array(
                "clientes_id in " => $clientesIds,
                "clientes_id != " => $clienteAplicarConfiguracaoId
            );

            if (sizeof($brindesConfiguradosIds) > 0) {
                $brindesNaoAtribuidosWhereConditions[] = array(
                    "id not in " => $brindesConfiguradosIds
                );
            }

            $brindesNaoAtribuidos = $brindeTable->find('all')
                ->where(
                    array($brindesNaoAtribuidosWhereConditions)
                )->toArray();

            $brindesNaoVinculados = array();

            $brindesVinculados = array();

            foreach ($brindesRede as $key => $brinde) {
                foreach ($brindesConfiguradosIds as $key => $brindeConfiguradoId) {
                    if ($brinde["id"] == $brindeConfiguradoId) {
                        $clienteBrindeHabilitado = $this->_getClientesHasBrindesHabilitadosTable()->find('all')
                            ->where(
                                array(
                                    "brindes_id" => $brinde["id"],
                                    "clientes_id" => $clienteAplicarConfiguracaoId
                                )
                            )->first();

                        $item = $brinde;
                        $item["brindeVinculado"] = $clienteBrindeHabilitado;
                        $item["atribuido"] = 1;
                        $brindesVinculados[] = $item;
                    }
                }
            }

            foreach ($brindesNaoAtribuidos as $key => $brinde) {
                foreach ($brindesNaoConfiguradosIds as $key => $brindeNaoConfiguradoId) {
                    if ($brinde["id"] == $brindeNaoConfiguradoId) {
                        $item = $brinde;
                        $item["brindeVinculado"] = null;
                        $item["atribuido"] = 0;
                        $brindesNaoVinculados[] = $item;
                    }
                }
            }

            // echo __LINE__;
            // DebugUtil::printArray($brindesVinculados, true);
            // DebugUtil::printArray($brindesVinculados, true);
            return array_merge($brindesVinculados, $brindesNaoVinculados);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registros: {0} em: {1}", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Obtem ids de brindes habilitados conforme condições
     *
     * @param array $whereConditions Condições
     *
     * @return entity\ClientesHasBrindesHabilitados $entity
     */
    public function getBrindesHabilitadosIdsFromConditions(array $whereConditions = [])
    {
        try {
            $conditions = [];

            $clientesBrindesHabilitadosQuery = $this->_getClientesHasBrindesHabilitadosTable()
                ->find('all')
                ->where($whereConditions)
                ->select(['id'])
                ->toArray();

            $clientesBrindesHabilitadosIds = array();
            foreach ($clientesBrindesHabilitadosQuery as $item) {
                $clientesBrindesHabilitadosIds[] = $item["id"];
            }

            return $clientesBrindesHabilitadosIds;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registros: {0} em: {1}", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    #endregion

    /* ------------------------ Update ------------------------ */

    /**
     * Define todos os preços de brindes habilitados de um cliente para a matriz
     *
     * @param int $clientes_id Id de Cliente
     * @param int $matriz_id   Id da Matriz
     *
     * @return boolean
     */
    public function setClientesHasBrindesHabilitadosToMainCliente(int $clientes_id, int $matriz_id)
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
     * Apaga todos os preços para brindes habilitados de um cliente
     *
     * @param array $clientes_ids Ids de clientes
     *
     * @return boolean
     */
    public function deleteAllClientesHasBrindesHabilitadosByClientesIds(array $clientes_ids)
    {

        try {
            return $this->_getClientesHasBrindesHabilitadosTable()
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
