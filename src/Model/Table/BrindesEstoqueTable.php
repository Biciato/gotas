<?php
namespace App\Model\Table;

use Cake\Log\Log;
use Cake\ORM\RulesChecker;
use Cake\Validation\Validator;


/**
 * ClientesHasBrindesEstoque Model
 *
 * @property \App\Model\Table\BrindesTable|\Cake\ORM\Association\BelongsTo $Brindes
 * @property \App\Model\Table\UsuariosTable|\Cake\ORM\Association\BelongsTo $Usuarios
 *
 * @method \App\Model\Entity\BrindesEstoque get($primaryKey, $options = [])
 * @method \App\Model\Entity\BrindesEstoque newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\BrindesEstoque[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\BrindesEstoque|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\BrindesEstoque patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\BrindesEstoque[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\BrindesEstoque findOrCreate($search, callable $callback = null, $options = [])
 *
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 2018-01-22
 *
 */
class BrindesEstoqueTable extends GenericTable
{

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

        $this->setTable('brindes_estoque');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo(
            'Brindes',
            [
                'foreignKey' => 'brindes_id',
                'joinType' => 'INNER'
            ]
        );
        $this->belongsTo(
            'Usuarios',
            [
                'foreignKey' => 'usuarios_id'
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
            ->notEmpty("brindes_id", "Campo BRINDES_ID não informado!")
            ->integer("brindes_id");

        $validator
            ->allowEmpty("usuarios_id")
            ->integer("usuarios_id");

        $validator
            ->integer('quantidade')
            ->requirePresence('quantidade', 'create')
            ->notEmpty('quantidade');

        $validator
            ->integer('tipo_operacao')
            ->requirePresence('tipo_operacao', 'create')
            ->notEmpty('tipo_operacao');

        $validator
            ->dateTime('data')
            ->requirePresence('data', 'create')
            ->notEmpty('data');

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
        // $rules->add($rules->existsIn(['brindes_id'], 'Brindes'));
        // $rules->add($rules->existsIn(['usuarios_id'], 'Usuarios'));

        return $rules;
    }

    /**
     * -------------------------------------------------------------
     * Methods
     * -------------------------------------------------------------
     */

    #region Create

    /**
     * BrindesEstoqueTable::addBrindeEstoque
     *
     * Adiciona estoque para Clientes Has Brindes Estoque
     *
     * @param int $brindesId Id de Brinde
     * @param int $usuariosId Id de Usuário
     * @param int $quantidade Quantidade
     * @param string $tipoOperacao Operação ('Criação','Adicionado ao Estoque','Saída Brinde','Saída Venda','Retornado')
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-04-26
     *
     * @return \App\Model\Entity\BrindesEstoque $estoque
     **/
    public function addBrindeEstoque($brindesId, $usuariosId, $quantidade, $tipoOperacao)
    {
        try {
            $estoque = $this->newEntity();

            $estoque["brindes_id"] = $brindesId;
            $estoque["usuarios_id"] = $usuariosId;
            $estoque["quantidade"] = $quantidade;
            $estoque["tipo_operacao"] = $tipoOperacao;
            $estoque["data"] = date("Y-m-d H:i:s");

            return $this->save($estoque);
        } catch (\Exception $e) {
            $trace = $e->getTraceAsString();
            $stringError = __("Erro ao salvar registro: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
            Log::write('error', $stringError);
            Log::write('error', $trace);
        }
    }

    #region Read

    /**
     * BrindesEstoqueTable::getEstoqueForBrinde
     *
     * Obtem Estoque do Brinde
     *
     * @param int $brindesId Id de Brinde
     * @param int $usuariosId Id do Usuário da transação
     * @param int $quantidadeMin Quantidade Minima
     * @param int $quantidadeMax Quantidade Maxima
     * @param string $dataMin Data Mínima
     * @param string $dataMax Data Máxima
     * @param integer $qteRegistros Quantidade de Registros à serem pesquisados
     * @param integer $orderBy Ordenação
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-04-22
     *
     * @return App\Model\Entity\BrindesEstoque $brindesEstoques
     *
     */
    public function getEstoqueForBrinde(int $brindesId, int $usuariosId = null, int $quantidadeMin = null, int $quantidadeMax = null, string $tipoOperacao = null, string $dataMin = null,  string $dataMax = null, int $qteRegistros = 10, string $orderBy = null)
    {
        try {

            $conditions = [];

            $conditions[] = array("BrindesEstoque.brindes_id" => $brindesId);

            if (!empty($usuariosId)) {
                $conditions[] = array("BrindesEstoque.usuarios_id" => $usuariosId);
            }

            if (!empty($quantidadeMin)) {
                $conditions[] = array("BrindesEstoque.quantidade_min >= " => $quantidadeMin);
            }

            if (!empty($quantidadeMax)) {
                $conditions[] = array("BrindesEstoque.quantidade_max <=" => $quantidadeMax);
            }

            if (!empty($tipoOperacao)) {
                $conditions[] = array("BrindesEstoque.tipo_operacao" => $tipoOperacao);
            }

            if (!empty($dataMin)) {
                $conditions[] = array("BrindesEstoque.data >= " => $dataMin);
            }

            if (!empty($dataMax)) {
                $conditions[] = array("BrindesEstoque.data <= " => $dataMax);
            }

            $estoque = $this->find('all')
                ->contain(['Usuarios'])
                ->where($conditions);

            $apenasUm = false;

            if (!empty($qteRegistros)) {
                if ($qteRegistros == 1) {
                    $estoque = $estoque->first();
                } else {
                    $estoque = $estoque->limit($qteRegistros);
                }
            }

            if (!empty($orderBy)) {
                $estoque->order(array("BrindesEstoque.id" => $orderBy));
            }

            return $estoque;
        } catch (\Exception $e) {
            $trace = $e->getTraceAsString();

            $stringError = __("Erro ao obter estoque de brindes: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write('error', $trace);
        }
    }

    /**
     * BrindesEstoqueTable::getActualStockForBrindesEstoque
     *
     * Obtem estoques do brinde
     *
     * @param integer $id Id do brinde
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-04-27
     *
     * @return array(entrada, saida_brinde, saida_venda, devolucao, estoque_atual)
     */
    public function getActualStockForBrindesEstoque(int $id)
    {
        try {
            // Cálculo de estoque do item
            $queryEntrada = $this->find();
            $querySaidaBrinde = $this->find();
            $querySaidaVenda = $this->find();
            $queryDevolucao = $this->find();

            $resultEntrada = $queryEntrada->select([
                'sum' => $queryEntrada->func()->sum('quantidade')
            ])->where(['tipo_operacao' => TYPE_OPERATION_ADD_STOCK, 'brindes_id' => $id])->first();

            $resultSaidaBrinde = $querySaidaBrinde->select([
                'sum' => $querySaidaBrinde->func()->sum('quantidade')
            ])->where(['tipo_operacao' => TYPE_OPERATION_SELL_BRINDE, 'brindes_id' => $id])->first();

            $resultSaidaVenda = $querySaidaVenda->select([
                'sum' => $querySaidaVenda->func()->sum('quantidade')
            ])->where(['tipo_operacao' => TYPE_OPERATION_SELL_CURRENCY, 'brindes_id' => $id])->first();

            $resultDevolucao = $queryDevolucao->select([
                'sum' => $queryDevolucao->func()->sum('quantidade')
            ])->where(['tipo_operacao' => TYPE_OPERATION_RETURN, 'brindes_id' => $id])->first();


            $entrada = is_null($resultEntrada['sum']) ? 0 : $resultEntrada['sum'];
            $saidaBrinde = is_null($resultSaidaBrinde['sum']) ? 0 : $resultSaidaBrinde['sum'];
            $saidaVenda = is_null($resultSaidaVenda['sum']) ? 0 : $resultSaidaVenda['sum'];
            $devolucao = is_null($resultDevolucao['sum']) ? 0 : $resultDevolucao['sum'];

            $estoque = ($entrada + $devolucao) - ($saidaBrinde + $saidaVenda);

            $brinde["entrada"] = $entrada;
            $brinde["saida_brinde"] = $saidaBrinde;
            $brinde["saida_venda"] = $saidaVenda;
            $brinde["devolucao"] = $devolucao;
            $brinde["estoque_atual"] = $estoque;
            return $brinde;
        } catch (\Throwable $th) {
            $trace = $th->getTraceAsString();

            $stringError = __("Erro ao obter estoque de brindes: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $th->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write('error', $trace);
        }
    }
    /**
     * Get total sum of brindes stock
     *
     * @param int $clientes_has_brindes_habilitados_id

     * @return int sum of brinde stock
     *
     * @todo Deverá ser reescrevido o retorno, ou utilizar um novo método
     * @deprecated 1.0
     **/
    public function getEstoqueAtualForBrindeId($clientes_has_brindes_habilitados_id)
    {
        try {
            $query = $this->find();

            $queryResult = $query->select(['sum' => $query->func()->sum('quantidade')])
                ->where(['clientes_has_brindes_habilitados_id' => $clientes_has_brindes_habilitados_id])->first();

            return $queryResult['sum'];
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registros: {0} em: {1}", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Verifica se tem estoque para o brinde
     *
     * @param int $clientes_has_brindes_habilitados_id Id do Brinde Habilitado
     * @param int $checkout_ammount                    Quantidade de saída
     *
     * @return array
     */
    public function checkBrindeHasEstoqueByBrindesHabilitadosId(int $clientes_has_brindes_habilitados_id, int $checkout_ammount)
    {
        $left = $this->getEstoqueAtualForBrindeId($clientes_has_brindes_habilitados_id);

        return ['enough' => $left > $checkout_ammount, 'left' => $left];
    }

    #endregion

    #region Update

    #endregion

    #region Delete

    /**
     * Apaga todas as gotas de clientes
     *
     * @param array $clientes_ids Ids de clientes
     *
     * @return boolean
     */
    public function deleteAllClientesHasBrindesEstoqueByClientesIds(array $clientes_ids)
    {
        try {

            $clientes_has_brindes_habilitados_id = $this->ClientesHasBrindesHabilitados->find('all')
                ->where(['clientes_id in' => $clientes_ids])->select(['id']);

            $clientes_has_brindes_habilitados_ids = [];

            foreach ($clientes_has_brindes_habilitados_id as $key => $value) {
                array_push($clientes_has_brindes_habilitados_ids, $value['id']);
            }

            if (sizeof($clientes_has_brindes_habilitados_ids) > 0) {
                return $this
                    ->deleteAll(['clientes_has_brindes_habilitados_id in' => $clientes_has_brindes_habilitados_ids]);
            } else {
                return true;
            }
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
