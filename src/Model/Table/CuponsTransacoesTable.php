<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Exception;
use App\Model\Entity\CuponsTransacoes;
use Cake\Log\Log;
use DateTime;
use App\Custom\RTI\DebugUtil;
use Cake\Database\Expression\QueryExpression;

/**
 * CuponsTransacoes Model
 *
 * @property \App\Model\Table\RedesTable|\Cake\ORM\Association\BelongsTo $Redes
 * @property \App\Model\Table\ClientesTable|\Cake\ORM\Association\BelongsTo $Clientes
 * @property \App\Model\Table\CuponsTable|\Cake\ORM\Association\BelongsTo $Cupons
 * @property \App\Model\Table\BrindesTable|\Cake\ORM\Association\BelongsTo $Brindes
 * @property \App\Model\Table\ClientesHasQuadroHorarioTable|\Cake\ORM\Association\BelongsTo $ClientesHasQuadroHorario
 * @property \App\Model\Table\UsuariosTable|\Cake\ORM\Association\BelongsTo $Usuarios
 *
 * @method \App\Model\Entity\CuponsTransacoes get($primaryKey, $options = [])
 * @method \App\Model\Entity\CuponsTransacoes newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\CuponsTransacoes[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CuponsTransacoes|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CuponsTransacoes patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CuponsTransacoes[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\CuponsTransacoes findOrCreate($search, callable $callback = null, $options = [])
 */
class CuponsTransacoesTable extends GenericTable
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

        $this->setTable("cupons_transacoes");
        $this->setDisplayField("id");
        $this->setPrimaryKey("id");

        $this->belongsTo(
            "Redes",
            array(
                "className" => "redes",
                "foreignKey" => "redes_id",
                "joinType" => Query::JOIN_TYPE_LEFT
            )
        );
        $this->belongsTo(
            "Clientes",
            array(
                "className" => "clientes",
                "foreignKey" => "clientes_id",
                "joinType" => Query::JOIN_TYPE_LEFT
            )
        );
        $this->belongsTo(
            "Cupons",
            array(
                "className" => "cupons",
                "foreignKey" => "cupons_id",
                "joinType" => Query::JOIN_TYPE_LEFT
            )
        );
        $this->belongsTo(
            "Brindes",
            array(
                "className" => "brindes",
                "foreignKey" => "brindes_id",
                "joinType" => Query::JOIN_TYPE_LEFT
            )
        );
        $this->belongsTo(
            "ClientesHasQuadroHorarios",
            array(
                "className" => "clientes_has_quadro_horario",
                "foreignKey" => "clientes_has_quadro_horario_id",
                "joinType" => Query::JOIN_TYPE_LEFT
            )
        );
        $this->belongsTo(
            "Funcionarios",
            array(
                "className" => "usuarios",
                "foreignKey" => "funcionarios_id",
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
            ->scalar('tipo_operacao')
            ->requirePresence('tipo_operacao', 'create')
            ->notEmpty('tipo_operacao');

        $validator
            ->dateTime('DATA')
            ->requirePresence('DATA', 'create')
            ->notEmpty('DATA');

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
        $rules->add($rules->existsIn(['redes_id'], 'Rede'));
        $rules->add($rules->existsIn(['clientes_id'], 'Cliente'));
        $rules->add($rules->existsIn(['cupons_id'], 'Cupom'));
        $rules->add($rules->existsIn(['brindes_id'], 'Brinde'));
        $rules->add($rules->existsIn(['clientes_has_quadro_horario_id'], 'ClienteHasQuadroHorario'));
        $rules->add($rules->existsIn(['funcionarios_id'], 'Funcionario'));

        return $rules;
    }

    #region Read

    /**
     * CuponsTransacoesTable::getSumTransacoesByTypeOperation
     *
     * Retorna soma de transações por tipo de transação
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-07-03
     *
     * @param integer $redesId Redes Id
     * @param integer $clientesId Clientes Id
     * @param integer $cuponsId Cupons Id
     * @param integer $brindesId Brindes Id
     * @param integer $clienteHasQuadroHorario Cliente Has Quadro Horario
     * @param integer $funcionariosId Funcionarios Id
     * @param string $tipoOperacao Tipo Operacao
     * @param DateTime $minDate Data Inicio
     * @param DateTime $maxDate Data Fim
     *
     * @return int Soma
     */
    public function getSumTransacoesByTypeOperation(int $redesId = null, int $clientesId = null, int $cuponsId = null, int $brindesId = null, int $clienteHasQuadroHorario = null, int $funcionariosId = null, string $tipoOperacao = null, DateTime $minDate = null, DateTime $maxDate = null)
    {
        try {
            $where = array();

            if (!empty($redesId)) {
                $where[] = array("CuponsTransacoes.redes_id" => $redesId);
            }
            if (!empty($clientesId)) {
                $where[] = array("CuponsTransacoes.clientes_id" => $clientesId);
            }
            if (!empty($cuponsId)) {
                $where[] = array("CuponsTransacoes.cupons_id" => $cuponsId);
            }
            if (!empty($brindesId)) {
                $where[] = array("CuponsTransacoes.brindes_id" => $brindesId);
            }
            if (!empty($clienteHasQuadroHorario)) {
                $where[] = array("CuponsTransacoes.clientes_has_quadro_horario_id" => $clienteHasQuadroHorario);
            }
            if (!empty($funcionariosId)) {
                $where[] = array("CuponsTransacoes.funcionarios_id" => $funcionariosId);
            }
            if (!empty($tipoOperacao)) {
                $where[] = array("CuponsTransacoes.tipo_operacao" => $tipoOperacao);
            }
            if (!empty($minDate)) {
                $where[] = array("CuponsTransacoes.data >= " => $minDate->format("Y-m-d H:i:s"));
            }
            if (!empty($maxDate)) {
                $where[] = array("CuponsTransacoes.data <= " => $maxDate->format("Y-m-d H:i:s"));
            }

            $query = $this->find();
            $soma = $query->select(array("count" => $query->func()->count("CuponsTransacoes.id")))
                ->where($where)->first();

            return $soma["count"];
        } catch (\Exception $ex) {
            $message = $ex->getMessage();
            $trace = $ex->getTraceAsString();

            Log::write("error", sprintf("[%s] %s", MESSAGE_LOAD_DATA_WITH_ERROR, $message));
            Log::write("debug", sprintf("[%s] Error: %s/ Trace: %s", MESSAGE_LOAD_DATA_WITH_ERROR, $message, $trace));

            throw new Exception($message);
        }
    }

    /**
     * Obtem dados de transacoes
     *
     * Obtem dados de transacoes para relatório
     *
     * CuponsTransacoesTable.php::getTransactionsForReport
     *
     * @param integer $redesId Redes Id
     * @param array $clientesIds Clientes Ids
     * @param integer $brindesId Brindes Id
     * @param DateTime $minDate Min Date
     * @param DateTime $maxDate Max Date
     * @param string $tipoRelatorio Tipo Relatorio
     *
     * @return \App\Model\Entity\CuponsTransacoes[] Array de transacoes
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-12-02
     */
    public function getTransactionsForReport(int $redesId = null, array $clientesIds = [], int $brindesId = null, DateTime $minDate = null, DateTime $maxDate = null, string $tipoRelatorio = REPORT_TYPE_SYNTHETIC)
    {

        try {
            $where = function (QueryExpression $exp) use ($redesId, $clientesIds, $brindesId, $minDate, $maxDate) {
                if (!empty($redesId)) {
                    $exp->eq("Redes.id", $redesId);
                }

                if (!empty($clientesIds) && count($clientesIds) > 0) {
                    $exp->in("Clientes.id", $clientesIds);
                }

                if (!empty($brindesId)) {
                    $exp->eq("Brindes.id", $brindesId);
                }

                if (!empty($minDate)) {
                    $exp->gte("DATE_FORMAT(CuponsTransacoes.data, '%Y-%m-%d %H:%i:%s')", $minDate->format("Y-m-d 00:00:00"));
                }

                if (!empty($maxDate)) {
                    $exp->lte("DATE_FORMAT(CuponsTransacoes.data, '%Y-%m-%d %H:%i:%s')", $maxDate->format("Y-m-d 23:59:59"));
                }

                $exp->eq("CuponsTransacoes.tipo_operacao", TYPE_OPERATION_USE);

                return $exp;
            };

            $selectList = [
                "periodo" => "DATE_FORMAT(CuponsTransacoes.data, '%Y-%m')",
                "qte_gotas" => "ROUND(SUM(Cupons.valor_pago_gotas), 0)",
                "qte_reais" => "ROUND(SUM(Cupons.valor_pago_reais), 0)",
                "CuponsTransacoes.tipo_operacao",
                "brinde" => "Brindes.nome",
                "funcionario" => "Funcionarios.nome",
                "usuario" => "Usuarios.nome",
                "estabelecimento" => "Clientes.nome_fantasia",
                "rede" => "Redes.nome_rede",
                "qte" => "COUNT(Cupons.quantidade)"
            ];


            $group = ["Funcionarios.id"];
            $join = ["Redes",  "Clientes",  "Brindes",  "Cupons.Usuarios",  "Funcionarios"];

            if ($tipoRelatorio === REPORT_TYPE_ANALYTICAL) {
                $selectList["periodo"] = "DATE_FORMAT(CuponsTransacoes.data, '%Y-%m-%d')";
                $group = [
                    "periodo",
                    "Clientes.id",
                    "Brindes.id",
                    "Funcionarios.id",
                    "Usuarios.id"
                ];
            }

            $query = $this->find("all")
                ->where($where)
                ->contain($join)
                ->group($group)
                ->select($selectList);

            return $query;
        } catch (\Throwable $th) {
            $message = sprintf("[%s] %s", MSG_LOAD_EXCEPTION, $th->getMessage());
            $code = MSG_LOAD_EXCEPTION_CODE;
            Log::write("error", sprintf("%s - %s", $code, $message));
            throw new Exception($message, $code);
        }
    }

    #endregion

    #region Save

    /**
     * CuponsTransacoesTable::saveUpdate
     *
     * Salva / Atualiza uma transação de cupom
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-06-28
     *
     * @param CuponsTransacoes $data dados da Transação
     *
     * @return CuponsTransacoes objeto inserido
     */
    public function saveUpdate(CuponsTransacoes $cupomTransacao)
    {
        try {
            return $this->save($cupomTransacao);
        } catch (Exception $e) {
            $message = sprintf("[%s] %s", MESSAGE_SAVED_ERROR, $e->getMessage());
            Log::write("error", $message);
            throw new Exception($message);
        }
    }
    #endregion

    #region Delete

    /**
     * Remove registros de transação pelo id de rede
     *
     * @param integer $redesId Id de Rede
     *
     * @return bool
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-09-28
     */
    public function deleteAllByRedesId(int $redesId)
    {
        try {
            return $this->deleteAll(["redes_id" => $redesId]);
        } catch (\Throwable $th) {
            $code = MSG_DELETE_EXCEPTION_CODE;
            $message = sprintf("[%s] %s: %s", MSG_DELETE_EXCEPTION, MSG_DELETE_EXCEPTION_CODE, $th->getMessage());

            $trace = $th->getTraceAsString();

            Log::write("error", $message);
            Log::write("debug", sprintf("[%s] Error: %s/ Trace: %s", MSG_DELETE_EXCEPTION, $message, $trace));

            throw new Exception($message, $code);
        }
    }

    #endregion
}
