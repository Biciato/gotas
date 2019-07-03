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

        $this->setTable('cupons_transacoes');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo(
            'Rede',
            array(
                "className" => "redes",
                'foreignKey' => 'redes_id',
                'joinType' => 'INNER'
            )
        );
        $this->belongsTo(
            'Cliente',
            array(
                "className" => "clientes",
                'foreignKey' => 'clientes_id',
                'joinType' => 'INNER'
            )
        );
        $this->belongsTo(
            'Cupom',
            array(
                "className" => "cupons",
                'foreignKey' => 'cupons_id',
                'joinType' => 'INNER'
            )
        );
        $this->belongsTo(
            'Brinde',
            array(
                "className" => "brindes",
                'foreignKey' => 'brindes_id',
                'joinType' => 'INNER'
            )
        );
        $this->belongsTo(
            'ClienteHasQuadroHorario',
            array(
                "className" => "clientes_has_quadro_horario",
                "foreignKey" => 'clientes_has_quadro_horario_id',
                "joinType" => 'INNER'
            )
        );
        $this->belongsTo(
            'Funcionario',
            array(
                "className" => "usuarios",
                'foreignKey' => 'funcionarios_id',
                'joinType' => 'INNER'
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
     * @param DateTime $dataInicio Data Inicio
     * @param DateTime $dataFim Data Fim
     * 
     * @return int Soma
     */
    public function getSumTransacoesByTypeOperation(int $redesId = null, int $clientesId, int $cuponsId = null, int $brindesId = null, int $clienteHasQuadroHorario = null, int $funcionariosId = null, string $tipoOperacao = null, DateTime $dataInicio = null, DateTime $dataFim = null)
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
            if (!empty($dataInicio)) {
                $where[] = array("CuponsTransacoes.data >= " => $dataInicio->format("Y-m-d H:i:s"));
            }
            if (!empty($dataFim)) {
                $where[] = array("CuponsTransacoes.data <= " => $dataFim->format("Y-m-d H:i:s"));
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
            Log::write("debug", sprintf("Error: %s/ Trace: %s", $message, $e->getTraceAsString()));
            throw new Exception($message);
        }
    }
    #endregion

    #region Delete

    #endregion
}
