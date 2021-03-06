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
use Cake\Database\Expression\QueryExpression;
use Exception;

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
                'joinType' => Query::JOIN_TYPE_LEFT
            )
        );

        $this->hasMany(
            'RedesHasClientesAdministradores',
            [
                'foreignKey' => 'redes_has_clientes_id',
                'joinType' => Query::JOIN_TYPE_INNER
            ]
        );

        $this->belongsTo(
            'Clientes',
            [
                "className" => "Clientes",
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
     * @param int $redesId Id da Rede
     *
     * @return \App\Model\Entity\RedesHasCliente $redes_has_cliente
     */
    public function findMatrizOfRedesByRedesId(int $redesId)
    {
        try {
            return $this->find('all')
                ->where(
                    array(
                        'RedesHasClientes.redes_id' => $redesId,
                        'Clientes.matriz' => true
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
     * RedesHasClientes::findRedesHasClientes
     *
     * Localiza dados de relacionamento conforme parametros
     *
     * @param integer $redesId
     * @param array $clientesIds
     * @param string $nomeFantasia
     * @param string $razaoSocial
     * @param string $cnpj
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-04-28
     *
     * @return \App\Model\Entity\RedesHasClientes[] Redes Has Clientes
     */
    public function findRedesHasClientes(int $redesId, array $clientesIds = array(), string $nomeFantasia = null, string $razaoSocial = null, string $cnpj = null)
    {
        try {
            $whereCondition = array();

            $whereCondition[] = array('redes_id' => $redesId);

            if (!empty($nomeFantasia)) {
                $whereCondition[] = array("Clientes.nome_fantasia like '%{$nomeFantasia}%'");
            }

            if (!empty($razaoSocial)) {
                $whereCondition[] = array("Clientes.razao_social like '%{$razaoSocial}%'");
            }

            if (!empty($cnpj)) {
                $whereCondition[] = array("Clientes.cnpj like '%{$cnpj}%'");
            }

            if (count($clientesIds) > 0) {
                $whereCondition[] = array("RedesHasClientes.clientes_id IN " => $clientesIds);
            }

            $redesHasClientes = $this->find('all')
                ->where($whereCondition)
                ->contain(['Redes', 'Clientes'])
                ->order([
                    // "Clientes.matriz" => "DESC",
                    "Clientes.nome_fantasia" => "ASC"
                ]);

            return $redesHasClientes;
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao obter registro: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);

            return ['success' => false, 'message' => $stringError];
        }
    }

    /**
     * Obtem os clientes ids através de um id de Redes
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
            $trace = $e->getTraceAsString();

            $stringError = __("Erro ao obter ids de Clientes de Rede: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);

            return $stringError;
        }
    }



    /**
     * Obtem todos os clientes e a rede pelo id da rede
     *
     * @param int $redesId Id de Redes
     *
     * @return \App\Model\Entity\RedesHasClientes $redes_has_clientes[] Array
     */
    public function getRedesHasClientesById(int $id)
    {
        try {
            // return $this->find('all')
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

            $result = $this->find('all')
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
            $redesId = $this->find('all')
                ->where(['clientes_id' => $clientes_id])
                ->first()
                ->redes_id;

            // pega todos os ids de unidades que pertencem à rede

            $unidadesIds = $this->find('all')
                ->where(['redes_id' => $redesId])
                ->select(['clientes_id']);

            $unidadesIds = $this->retrieveColumnsQueryAsArray($unidadesIds, ['clientes_id']);

            return $unidadesIds;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $object = null;

            // @todo gustavosg: ajustar log
            $stringError = __("Erro ao obter registro: {0}, em {1}", $e->getMessage(), $object['file']);

            Log::write('error', $stringError);

            return ['success' => false, 'message' => $stringError];
        }
    }

    /**
     * Obtêm redes e estabelecimentos (clientes)
     *
     * Obtêm redes e todos os estabelecimentos (clientes) que estão associados à um ou mais usuários
     *
     * @param int $usuariosId Id de usuário
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-12-15
     *
     * @return Cake\Orm\Query
     */
    public function getAllRedesHasClientesAssociatedToUsuariosId(int $usuariosId)
    {
        try {

            /**
             * 1ª Parte:
             * obtem todas as redes associadas a um usuário
             */

            $where = function (QueryExpression $exp) use ($usuariosId) {

                $exp->eq("ClientesHasUsuarios.usuarios_id", $usuariosId)
                    ->eq("Redes.ativado", 1);

                return $exp;
            };

            $selectRedesId = ["redesId" => "Redes.id"];
            $concatRedes = [
                "Redes",
                "Clientes"
            ];

            $joinRedes = array(
                "ClientesHasUsuarios" => array(
                    "type" => "left",
                    "alias" => "ClientesHasUsuarios",
                    "table" => "clientes_has_usuarios",
                    "conditions" => "RedesHasClientes.clientes_id = ClientesHasUsuarios.clientes_id"
                )
            );

            $groupRedes = [
                "Redes.id"
            ];

            $redes = $this->find("all")
                ->where($where)
                ->contain($concatRedes)
                ->join($joinRedes)
                ->select($selectRedesId)
                ->group($groupRedes)
                ->toArray();

            $redesIds = array_map(function ($o) {
                return $o->redesId;
            }, $redes);


            /**
             * 2ª Parte:
             * obtem todos os ids de estabelecimentos das redes localizadas
             */


            $where = function (QueryExpression $exp) use ($redesIds) {
                return $exp->in("RedesHasClientes.redes_id", $redesIds)
                    ->eq("Clientes.ativado", true);
            };

            $containClientes = ["Clientes"];
            $selectClientesIds = ["clientesId" => "Clientes.id"];
            $groupClientes = ["Clientes.id"];

            $clientes = $this->find("all")
                ->where($where)
                ->contain($containClientes)
                ->select($selectClientesIds)
                ->group($groupClientes)
                ->toArray();

            $clientesIds = array_map(function ($c) {
                return $c->clientesId;
            }, $clientes);

            return $clientesIds;
        } catch (\Throwable $th) {
            $message = sprintf("[%s] %s", MSG_LOAD_EXCEPTION, $th->getMessage());
            Log::write("error", $message);
            throw new Exception($message, MSG_LOAD_EXCEPTION_CODE);
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
            return $this->find('all')
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
            return $this->find('all')
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
    public function getRedesHasClientesByRedesId(int $redesId = 0, array $clientesIds = [])
    {
        try {
            $whereCondition = array('RedesHasClientes.redes_id' => $redesId);

            if (isset($clientesIds) && sizeof($clientesIds) > 0) {
                $whereCondition[] = array('RedesHasClientes.clientes_id in ' => $clientesIds);
            }

            return $this->find('all')
                ->where($whereCondition)
                ->contain(['Redes', 'Clientes'])
                ->order([
                    // "Clientes.matriz" => "DESC",
                    "Clientes.nome_fantasia" => "ASC"
                ]);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao obter registro: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write('error', $e->getTraceAsString());
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
                $this->deleteAll(['clientes_id in' => $clientes_ids]);
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
