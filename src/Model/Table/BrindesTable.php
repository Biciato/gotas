<?php

namespace App\Model\Table;

use Cake\Log\Log;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\Validation\Validator;
use \Exception;
use App\Model\Entity\Brinde;

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
     * Properties
     * -------------------------------------------------------------
     */

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

        $this->belongsTo(
            "CategoriaBrinde",
            [
                "className" => "CategoriasBrindes",
                "foreignKey" => "categorias_brindes_id",
                "joinType" => Query::JOIN_TYPE_LEFT
            ]
        );

        // relacionamento de brindes com posto
        $this->belongsToMany(
            "Clientes",
            array(
                "className" => "Clientes",
                "foreignKey" => "clientes_id",
                "joinType" => Query::JOIN_TYPE_INNER
            )
        );

        $this->belongsTo(
            'Clientes',
            array(
                "className" => "Clientes",
                'foreignKey' => 'clientes_id',
                'joinType' => 'INNER'
            )
        );

        $this->hasOne(
            "PrecoAtual",
            array(
                "className" => "BrindesPrecos",
                "foreignKey" => "brindes_id",
                "joinType" => Query::JOIN_TYPE_LEFT,
                "strategy" => "select",
                // "strategy" => "join",
                "conditions" => array(
                    "PrecoAtual.status_autorizacao" => STATUS_AUTHORIZATION_PRICE_AUTHORIZED,
                ),
                "order" => array(
                    "data_preco" => "DESC"
                )

                // "limit" => 1,


            )
        );

        $this->hasMany(
            "BrindesEstoque",
            array(
                "className" => "BrindesEstoque",
                "foreignKey" => "brindes_id",
                "joinType" => Query::JOIN_TYPE_LEFT
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
            ->requirePresence('nome', 'create')
            ->notEmpty('nome');

        $validator
            ->integer('codigo_primario')
            ->allowEmpty("codigo_primario");

        $validator
            ->scalar("tipo_equipamento", "create")
            ->add(
                "tipo_equipamento",
                "inList",
                array(
                    "rule" => array("inList", array(TYPE_EQUIPMENT_PRODUCT_SERVICES, TYPE_EQUIPMENT_RTI)),
                    "message" => "É necessário selecionar um Tipo de Equipamento",
                    "allowEmpty" => false
                )
            )->notEmpty("tipo_equipamento");

        $validator
            ->integer('ilimitado')
            ->requirePresence('ilimitado', 'create')
            ->notEmpty('ilimitado');

        $validator
            ->integer("habilitado");

        $validator
            ->scalar("tipo_venda", "create")
            ->add(
                "tipo_venda",
                "inList",
                array(
                    "rule" => array("inList", array(TYPE_SELL_FREE_TEXT, TYPE_SELL_DISCOUNT_TEXT, TYPE_SELL_CURRENCY_OR_POINTS_TEXT)),
                    "message" => "É necessário selecionar um Tipo de Venda",
                    "allowEmpty" => false
                )
            )->notEmpty("tipo_venda");

        $validator
            ->decimal('preco_padrao')
            ->requirePresence('preco_padrao', 'create')
            ->notEmpty('preco_padrao');

        $validator
            ->decimal('valor_moeda_venda_padrao')
            ->requirePresence('valor_moeda_venda_padrao', 'create')
            ->allowEmpty('valor_moeda_venda_padrao');

        $validator
            ->integer("apagado");

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

    #region Create

    #endregion

    #region Read

    /**
     * Procura brindes conforme filtros
     *
     * @param integer $redesId Id da Rede
     * @param integer $clientesId Id do posto/loja
     * @param integer $categoriasBrindesId Id de Categorias Brindes
     * @param string $nome Nome do Brinde
     * @param integer $tempoUsoBrindeMin Tempo Uso Brinde Minimo
     * @param integer $tempoUsoBrindeMax Tempo Uso Brinde Maximo
     * @param integer $ilimitado Ilimitado
     * @param string $tipoEquipamento Tipo Equipamento
     * @param array $tipoVendas Tipo de Venda
     * @param string $tipoCodigoBarras Tipo Codigo Barras
     * @param float $precoPadraoMin Preco Padrao Minimo
     * @param float $precoPadraoMax Preco Padrao Maximo
     * @param float $valorMoedaVendaPadraoMin Valor Moeda Venda Padrao Minimo
     * @param float $valorMoedaVendaPadraoMax Valor Moeda Venda Padrao Maximo
     * @param int $apagado Registro Apagado (< 0 pesquisa por todos)
     * @param array $orderBy Array de Ordenação
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2017-07-01

     * @return \App\Model\Entity\Brindes[] $brindes
     */

    public function findBrindes(int $redesId = null, int $clientesId = null, int $categoriasBrindesId = null, string $nome = null, int $codigoPrimario = null, int $tempoUsoBrindeMin = null, int $tempoUsoBrindeMax = null, int $ilimitado = null, string $tipoEquipamento = null, array $tiposVendas = array(), string $tipoCodigoBarras = null, float $precoPadraoMin = null, float $precoPadraoMax = null, float $valorMoedaVendaPadraoMin = null, float $valorMoedaVendaPadraoMax = null, int $apagado = null, array $orderBy = array())
    {
        try {

            $where = array();

            if (!empty($redesId)) {
                $where[] = array("RedesHasClientes.redes_id" => $redesId);
            }

            if (!empty($clientesId)) {
                $where[] = ["OR" => [
                    "Brindes.brinde_rede" => 1,
                    "Brindes.clientes_id" => $clientesId
                ]];
            }

            if (!empty($categoriasBrindesId)) {
                $where[] = ["Brindes.categorias_brindes_id" => $categoriasBrindesId];
            }

            if (!empty($codigoPrimario)) {
                $where[] = array("Brindes.codigo_primario" => $codigoPrimario);
            }

            if (!empty($nome)) {
                // $where[] = array("nome LIKE '%{$nome}%'");
                $where[] = array("Brindes.nome = '{$nome}'");
            }

            if (!empty($tempoUsoBrindeMin) && !empty($tempoUsoBrindeMax)) {
                $where[] = array("Brindes.tempo_uso_brinde BETWEEN '{$tempoUsoBrindeMin}' AND '{$tempoUsoBrindeMax}' ");
            } else if (!empty($tempoUsoBrindeMin)) {
                $where[] = array("Brindes.tempo_uso_brinde >= " => $tempoUsoBrindeMin);
            } else if (!empty($tempoUsoBrindeMax)) {
                $where[] = array("Brindes.tempo_uso_brinde <= " => $tempoUsoBrindeMax);
            }

            if (isset($ilimitado)) {
                $where[] = array("Brindes.ilimitado" => $ilimitado);
            }

            if (!empty($tipoEquipamento)) {
                $where[] = array("Brindes.tipo_equipamento" => $tipoEquipamento);
            }

            if (count($tiposVendas) > 0) {
                $tiposVendas = implode("','", $tiposVendas);
                $where[] = array("Brindes.tipo_venda IN ('{$tiposVendas}')");
            }

            if (!empty($tipoCodigoBarras)) {
                $where[] = array("Brindes.tipo_codigo_barras" => $tipoCodigoBarras);
            }

            if (!empty($precoPadraoMin) && !empty($precoPadraoMax)) {
                $where[] = array("Brindes.preco_padrao BETWEEN '{$precoPadraoMin}' AND '{$precoPadraoMax}' ");
            } else if (!empty($precoPadraoMin)) {
                $where[] = array("Brindes.preco_padrao >= " => $precoPadraoMin);
            } else if (!empty($precoPadraoMax)) {
                $where[] = array("Brindes.preco_padrao <= " => $precoPadraoMax);
            }

            if (!empty($valorMoedaVendaPadraoMin) && !empty($valorMoedaVendaPadraoMax)) {
                $where[] = array("Brindes.valor_moeda_venda_padrao BETWEEN '{$valorMoedaVendaPadraoMin}' AND '{$valorMoedaVendaPadraoMax}' ");
            } else if (!empty($valorMoedaVendaPadraoMin)) {
                $where[] = array("Brindes.valor_moeda_venda_padrao >= " => $valorMoedaVendaPadraoMin);
            } else if (!empty($valorMoedaVendaPadraoMax)) {
                $where[] = array("Brindes.valor_moeda_venda_padrao <= " => $valorMoedaVendaPadraoMax);
            }

            // Só mostra os registros não apagados
            if (!isset($apagado)) {
                $where[] = array("Brindes.apagado" => 0);
            } elseif ($apagado < 0) {
                $where[] = array("Brindes.apagado IN " => array(0, 1));
            } else {
                $where[] = array("Brindes.apagado" => $apagado);
            }

            $whereConditions = $where;
            $contains = array("PrecoAtual", "CategoriaBrinde", "Clientes.RedesHasClientes.Redes");

            $brindes = $this->find('all')
                ->contain($contains)
                ->where($whereConditions);

            if (count($orderBy) > 0) {
                $brindes = $brindes->order($orderBy);
            }

            return $brindes;
        } catch (\Exception $e) {
            $trace = $e->getTraceAsString();
            $message = sprintf("[%s] %s", MESSAGE_LOAD_DATA_WITH_ERROR, $e->getMessage());
            $messageTrace = sprintf("[%s] %s / %s", MESSAGE_LOAD_DATA_WITH_ERROR, $e->getMessage(), $trace);

            Log::write('error', $message);
            Log::write('debug', $messageTrace);

            throw new Exception($message);
        }
    }


    public function getBrindesPostoNotIn(int $clientesId, array $brindesIdNotIn = [])
    {
        try {
            $where = [];

            $where[] = ["Brindes.clientes_id" => $clientesId];
            $where[] = ["Brindes.habilitado" => 1];

            if (count($brindesIdNotIn) > 0) {
                $where[] = ["Brindes.id NOT IN " => $brindesIdNotIn];
            }

            return $this->find("all")
                ->where($where)
                ->contain(["CategoriaBrinde", "PrecoAtual"]);
        } catch (\Throwable $th) {
            $message = sprintf("[%s] %s", MESSAGE_LOAD_DATA_WITH_ERROR, $e->getMessage());
            Log::write('error', $message);

            throw new Exception($message);
        }
    }

    /**
     * BrindesTable::getList
     *
     * Obtem a lista de brindes
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-07-01
     *
     * @param integer $redesId Redes Id
     * @param integer $clientesId Clientes Id
     * @param integer $apagado Apagado
     *
     * @return array[int, string] Lista de Brindes
     */
    public function getList(int $redesId = null, int $clientesId = null, int $apagado = null)
    {
        try {
            $where = array();

            $join = [];

            if (!empty($redesId)) {
                // $where["Brindes.redes_id"] = $redesId;
                $join = ["Clientes.RedesHasClientes.Redes"];
                $where["Redes.id"] = $redesId;
            }

            if (!empty($clientesId)) {
                $where["Brindes.clientes_id"] = $clientesId;
            }

            if (count($where) == 0) {
                throw new Exception("Informe um dos seguintes argumentos para obter a lista de brindes: [REDES_ID, CLIENTES_ID]!");
            }

            $where["Brindes.habilitado"] = 1;

            // Só mostra os registros não apagados
            if (!isset($apagado)) {
                $where[] = array("Brindes.apagado" => 0);
            } elseif ($apagado < 0) {
                $where[] = array("Brindes.apagado IN " => array(0, 1));
            } else {
                $where[] = array("Brindes.apagado" => $apagado);
            }

            $brindes = $this->find("all")
                ->where($where)
                ->select([
                    "Brindes.id",
                    "Brindes.nome"
                ]);

            if (!empty($join) > 0) {
                $brindes = $brindes->contain($join);
            }

            return $brindes;
        } catch (Exception $e) {
            $message = sprintf("[%s] %s", MESSAGE_LOAD_DATA_WITH_ERROR, $e->getMessage());

            throw new Exception($message);
        }
    }

    public function getBrindesSell(int $clientesId, array $tipoVenda, string $tipoOperacao)
    // public function getBrindesSell(int $clientesId, string $tipoVenda, string $tipoOperacao)
    {
        try {
            $brindesList = $this->find(
                'all',
                array(
                    "conditions" => array(
                        "OR" => [
                            "Brindes.clientes_id" => $clientesId,
                            "Brindes.brinde_rede" => 1
                        ],
                        "Brindes.tipo_venda IN " => $tipoVenda,
                        // "Brindes.tipo_venda" => $tipoVenda,
                        "Brindes.apagado" => 0,
                        "Brindes.habilitado" => 1
                    ),
                    "contain" => array("PrecoAtual")
                )
            );

            $brindes = array();

            if (in_array($tipoVenda, [TYPE_SELL_CURRENCY_OR_POINTS_TEXT])) {
                // Se for Gotas ou Reais
                foreach ($brindesList as $brinde) {
                    if ($tipoOperacao == TYPE_PAYMENT_POINTS) {
                        if (!empty($brinde["preco_atual"]["preco"])) {
                            $brindes[] = $brinde;
                        }
                    } else {
                        if (!empty($brinde["preco_atual"]["valor_moeda_venda"])) {
                            $brindes[] = $brinde;
                        }
                    }
                }
            } else {
                // Se Desconto ou Isento
                $brindes = $brindesList->toArray();
            }

            return $brindes;
        } catch (\Throwable $th) {
            $trace = $th->getTraceAsString();

            $stringError = __("Erro ao obter registros: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $th->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write('error', $trace);
        }
    }

    /**
     * BrindesTable::getBrindesIds
     *
     * Obtem Id de brindes conforme condições
     *
     * @param integer $id
     * @param integer $clientesIds
     * @param string $nome
     * @param integer $tempoUsoBrinde
     * @param boolean $ilimitado
     * @param boolean $habilitado
     * @param float $precoPadrao
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 26/07/2018
     *
     * @return array $resultado
     *
     * @deprecated 1.0
     */
    public function getBrindesIds(
        int $id = null,
        array $clientesIds = array(),
        string $nome = "",
        int $tempoUsoBrinde = null,
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
            if (!empty($nome)) {
                $whereConditions[] = array("nome like '%{$nome}%'");
            }
            if (!empty($tempoUsoBrinde)) {
                $whereConditions[] = array("tempo_uso_brinde" => $tempoUsoBrinde);
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

            $brindesQuery = $this->find("all")
                ->where($whereConditions)
                ->select(["id"]);

            $brindesIds = array();

            foreach ($brindesQuery as $brinde) {
                $brindesIds[] = $brinde["id"];
            }

            return $brindesIds;
        } catch (\Exception $e) {
            $trace = $e->getTraceAsString();

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
     * @return \App\Model\Entity\Brinde $brinde
     **/
    public function getBrindeById($brindesId)
    {
        try {

            $brinde = $this->find("all")
                ->where(array("Brindes.id" => $brindesId))
                ->contain(array("PrecoAtual"))
                ->first();

            if (empty($brinde)) {
                return null;
            }

            $estoque = $this->BrindesEstoque->getActualStockForBrindesEstoque($brindesId);
            $brinde["estoque"] = $estoque;
            return $brinde;
        } catch (\Exception $e) {
            $trace = $e->getTraceAsString();

            $stringError = __("Erro ao obter ids de brindes: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write('error', $trace);
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
     *
     * @deprecated 1.0 Não é mais usado
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

            $brindes = $this->find('all')->where($matriz_conditions);

            $brindes_cliente = $this->ClientesHasBrindesHabilitados->find('all')->where($clientes_conditions)->contain(['Brindes']);

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

    #endregion

    #region Update

    /**
     * Undocumented function
     *
     * @param Brinde $brinde

     * @return int|bool
     */
    public function saveUpdate(Brinde $brinde)
    {
        try {
            return $this->save($brinde);
        } catch (\Exception $e) {
            $trace = $e->getTraceAsString();
            $stringError = __("Erro ao gravar brinde: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write('error', $trace);

            throw new Exception($stringError);
        }
    }
    /**
     * BrindesTable::updateEnabledStatusBrinde
     *
     * Altera estado de habilitado do brinde
     *
     * @param integer $id Id do Brinde
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-04-26
     *
     * @return \App\Model\Entity\Brinde Brinde
     */
    public function updateEnabledStatusBrinde(int $id)
    {
        try {
            $brinde = $this->get($id);

            $changeEnabled = !$brinde["habilitado"];
            $brinde["habilitado"] = $changeEnabled;

            return $this->save($brinde);
        } catch (\Exception $e) {

            $trace = $e->getTraceAsString();
            $stringError = __("Erro ao gravar brinde: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write('error', $trace);
        }
    }

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

    #endregion

    #region Delete

    /**
     * 'Deleta' um Brinde
     *
     * @param integer $id Id do Brinde
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-04-28
     *
     *
     * @return true/false
     */
    public function deleteBrinde(int $id)
    {
        try {
            return $this
                ->updateAll(array("apagado" => 1), array("id" => $id));
        } catch (\Exception $e) {

            $trace = $e->getTraceAsString();
            $stringError = __("Erro ao deletar brinde: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write('error', $trace);
        }
    }

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
            return $this
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

    #endregion
}
