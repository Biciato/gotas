<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Log\Log;

/**
 * ClientesHasQuadroHorario Model
 *
 * @property \App\Model\Table\ClientesTable|\Cake\ORM\Association\BelongsTo $Clientes
 *
 * @method \App\Model\Entity\ClientesHasQuadroHorario get($primaryKey, $options = [])
 * @method \App\Model\Entity\ClientesHasQuadroHorario newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ClientesHasQuadroHorario[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ClientesHasQuadroHorario|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ClientesHasQuadroHorario patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ClientesHasQuadroHorario[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ClientesHasQuadroHorario findOrCreate($search, callable $callback = null, $options = [])
 */
class ClientesHasQuadroHorarioTable extends Table
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

        $this->setTable('clientes_has_quadro_horario');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Redes', [
            'foreignKey' => 'redes_id',
            'joinType' => 'INNER'
        ]);

        $this->belongsTo(
            'Clientes',
            [
                'foreignKey' => 'clientes_id',
                'joinType' => Query::JOIN_TYPE_LEFT
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
            ->time('horario')
            ->requirePresence('horario', 'create')
            ->notEmpty('horario');

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

    #region Read

    /**
     * ClientesHasQuadroHorario::getHorariosCliente
     *
     * Obtem as Horas de trabalho do Ponto de Atendimento
     *
     * @param integer $redesId Id da Rede
     * @param integer $clientesId Id do Cliente
     * @param integer $hora Hora
     * @param integer $minutos Minuto
     * @param boolean $ativado Registro ativado / Desativado
     * @param integer $limite Limite
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2018-12-31
     *
     * @return \App\Model\ClientesHasQuadroHorario[] Quadros de Horário
     */
    public function getHorariosCliente(int $redesId = null, int $clientesId = null, int $hora = null, int $minutos = null, bool $ativado = null, int $limite = 999)
    {
        try {
            $where = array();

            if (!empty($redesId)) {
                $where["redes_id"] = $redesId;
            }

            if (!empty($clientesId)) {
                $where["clientes_id"] = $clientesId;
            }

            if (isset($hora)) {
                $where["TIME_FORMAT(horario, %H)"] = $hora;
            }

            if (isset($minutos)) {
                $where["TIME_FORMAT(horario, %i)"] = $minutos;
            }

            if (isset($ativado)) {
                $where["ativado"] = $ativado;
            } else {
                $where["ativado"] = true;
            }

            $data = $this->find("all")->where($where);

            if ($limite == 1) {
                $data = $data->first();
            } else {
                $data = $data->limit($limite);
            }

            return $data;
        } catch (\Exception $error) {
            $trace = $error->getTrace();
            $stringError = __(
                "Erro ao buscar registro: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ",
                $error->getMessage(),
                __FUNCTION__,
                __FILE__,
                __LINE__
            );

            Log::write('error', $stringError);
            Log::write('error', $trace);
        }
    }

    #endregion

    #region Save

    /**
     * Adiciona Quadro de Horários
     *
     * @param integer $redesId Id da Rede
     * @param integer $clientesId Id do Cliente
     * @param array $horarios Array de Horários
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2018-12-31
     *
     * @return \App\Model\ClientesHasQuadroHorario entidade
     */
    public function addHorariosCliente(int $redesId, int $clientesId, array $horarios)
    {
        $arrayHorarios = array();

        foreach ($horarios as $horario) {

            $item = array(
                "redes_id" => $redesId,
                "clientes_id" => $clientesId,
                "horario" => implode(":", $horario)
            );

            $arrayHorarios[] = $item;
        };

        $horariosSave = $this->newEntities($arrayHorarios);

        $result = $this->saveMany($horariosSave);
        return $result;
    }

    /**
     * ClientesHasQuadroHorarioTable::disableHorariosCliente
     *
     * Atualiza todos os registros de Horários do Posto para desativados
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-06-21
     *
     * @param integer $clientesId Id do Posto Cliente
     *
     * @return bool Status de Update
     */
    public function disableHorariosCliente(int $clientesId)
    {
        $fields = array("ativado" => false);

        $conditions = array(
            "clientes_id" => $clientesId,
            "ativado" => true
        );

        return $this->updateAll($fields, $conditions);
    }

    #endregion

    #region Delete

    /**
     * ClientesHasQuadroHorarioTable::deleteHorariosCliente
     *
     * Remove todos os quadros de horarios de cliente
     *
     * @param integer $clientesId Id do cliente
     *
     * @return bool Status de remoção
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2018-01-01
     */
    public function deleteHorariosCliente(int $clientesId)
    {
        return $this->deleteAll(array("clientes_id" => $clientesId));
    }

    #endregion
}
