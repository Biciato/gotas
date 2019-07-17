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

/**
 * UsuariosHasBrindes Model
 *
 * @property \App\Model\Table\UsuariosTable|\Cake\ORM\Association\BelongsTo $Usuarios
 * @property \App\Model\Table\BrindesTable|\Cake\ORM\Association\BelongsTo $Brindes
 *
 * @method \App\Model\Entity\UsuariosHasBrinde get($primaryKey, $options = [])
 * @method \App\Model\Entity\UsuariosHasBrinde newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\UsuariosHasBrinde[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\UsuariosHasBrinde|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\UsuariosHasBrinde patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\UsuariosHasBrinde[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\UsuariosHasBrinde findOrCreate($search, callable $callback = null, $options = [])
 */
class UsuariosHasBrindesTable extends GenericTable
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

        $this->setTable('usuarios_has_brindes');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Usuarios', [
            'foreignKey' => 'usuarios_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Brindes', [
            'foreignKey' => 'brindes_id',
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
            ->numeric('preco_gotas')
            ->allowEmpty("preco_gotas");

            $validator
            ->numeric('preco_reais')
            ->allowEmpty("preco_reais");

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
        $rules->add($rules->existsIn(['usuarios_id'], 'Usuarios'));
        $rules->add($rules->existsIn(['brindes_id'], 'Brindes'));

        return $rules;
    }

    /**
     * -------------------------------------------------------------
     * Methods
     * -------------------------------------------------------------
     */

    #region Create

    /**
     * UsuariosHasBrindesTable::addUsuarioHasBrindes
     *
     * Adiciona um novo brinde ao usuário
     *
     * @param int $usuariosId
     * @param int $redesId Id da rede
     * @param int $clientesId Id da Unidade da Rede
     * @param int $brindesId
     * @param float $quantidade
     * @param float $precoGotas
     * @param float $precoReais
     * @param string $tipoPagamento Tipo de Pagamento ("Gotas", "Dinheiro")
     * @param int $cuponsId
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2017-10-10
     *
     * @return \App\Model\Entity\UsuariosHasBrinde $usuarioHasBrinde
     **/
    public function addUsuarioHasBrindes(int $redesId, int $clientesId, int $usuariosId, int $brindesId, float $quantidade, float $precoGotas, float $precoReais, string $tipoPagamento, int $cuponsId = null)
    {
        try {
            $brindeUsuario = $this->newEntity();

            $brindeUsuario["redes_id"] = $redesId;
            $brindeUsuario["clientes_id"] = $clientesId;
            $brindeUsuario["usuarios_id"] = $usuariosId;
            $brindeUsuario["brindes_id"] = $brindesId;
            $brindeUsuario["quantidade"] = (int)$quantidade;
            $brindeUsuario["preco_gotas"] = !empty($precoGotas) && $precoGotas > 0 ? $precoGotas * $quantidade : 0;
            $brindeUsuario["preco_reais"] = !empty($precoReais) && $precoReais > 0 ? $precoReais * $quantidade : 0;
            $brindeUsuario["tipo_pagamento"] = $tipoPagamento;
            $brindeUsuario["data"] = date('Y-m-d H:i:s');
            $brindeUsuario["cupons_id"] = $cuponsId;

            return $this->save($brindeUsuario);
        } catch (\Exception $e) {
            $trace = $e->getTraceAsString();
            $stringError = __("Erro ao salvar brinde de usuário: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);

            throw new \Exception($stringError);
        }
    }

    #endregion

    #region Read

    /**
     * Verifica o número de resgates de brindes do usuário na rede
     *
     * @param integer $redesId
     * @param integer $usuariosId
     * @return void
     */
    public function checkNumberRescuesUsuarioRede(int $redesId, int $usuariosId)
    {
        try {
            $whereCondicoes = array(
                "DATE_FORMAT(data, '%Y-%m-%d') = CURDATE()",
                "redes_id" => $redesId,
                "usuarios_id" => $usuariosId
            );
            return $this
                ->find("all")
                ->where($whereCondicoes)
                ->count(array("id"));
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao obter registro: " . $e->getMessage());

            Log::write('error', $stringError);

            throw new \Exception($stringError);
        }
    }

    /**
     * Obtem detalhes de brinde de usuário pelo Id
     *
     * @param integer $usuarios_has_brindes_id Id do brinde de usuário
     * @return \App\Model\Entity\UsuariosHasBrindes
     */
    public function getUsuariosHasBrindesById(int $usuarios_has_brindes_id)
    {
        try {
            return $this
                ->find('all')
                ->contain(['Brindes'])
                ->where(['UsuariosHasBrindes.id' => $usuarios_has_brindes_id])
                ->first();
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao atualizar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Obtem detalhes de brinde de usuário pelo Id
     *
     * @param integer $usuarios_has_brindes_id Id do brinde de usuário
     * @return \App\Model\Entity\UsuariosHasBrindes
     */
    public function getUsuariosHasBrindesByCuponsId(int $cuponsId)
    {
        try {
            return $this
                ->find('all')
                ->contain(array('Brindes'))
                ->where(array('UsuariosHasBrindes.cupons_id' => $cuponsId))
                ->select(
                    array(
                        "UsuariosHasBrindes.id",
                        "UsuariosHasBrindes.quantidade",
                        "Brindes.id",
                        "Brindes.nome"
                    )
                )
                ->toArray()
                ;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao atualizar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Obtem todos os brindes de usuários conforme condições
     *
     * @param array $where_conditions Condições de pesquisa
     * @param array $order_conditions Condições de ordem
     *
     * @return \App\Model\Entity\UsuariosHasBrindes[]
     */
    public function getAllUsuariosHasBrindes(array $where_conditions = [], array $order_conditions = [])
    {
        try {
            return $this
                ->find('all')
                ->contain(
                    [
                        'Brindes'
                    ]
                )
                ->where($where_conditions)
                ->order($order_conditions);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao atualizar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    #region Update

    #region Delete

    /**
     * Apaga todas as gotas de um cliente
     *
     * @param array $clientes_ids Ids de clientes
     *
     * @return boolean
     */
    public function deleteAllUsuariosHasBrindesByClientesIds(array $clientesIds)
    {
        try {

            // pegar id de brindes que estão vinculados em um cliente

            $brindesClientesIds = $this->Brindes->find('all')
                ->where(['clientes_id in' => $clientesIds])
                ->select(['id']);

            $clientes_has_brindes_habilitados_ids = [];

            foreach ($brindesClientesIds as $key => $value) {
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

    /**
     * Apaga todas as gotas de um usuário
     *
     * @param int $usuariosId Id de usuario
     *
     * @return boolean
     */
    public function deleteAllUsuariosHasBrindesByUsuariosId(int $usuariosId)
    {
        try {
            return $this->deleteAll(['usuarios_id' => $usuariosId]);
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

    /**
     * Undocumented function
     *
     * @param integer $cuponsId
     * @return void
     */
    public function deleteBrindeByCupomId(int $cuponsId)
    {
        try {
            return $this->deleteAll(array("cupons_id" => $cuponsId));
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registro: {0}", $e->getMessage());

            Log::write('error', $stringError);
        }
    }
}
