<?php

namespace App\Model\Table;

use ArrayObject;
use App\View\Helper;
use App\Controller\AppController;
use App\Model\Entity\Gota;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Exception;

/**
 * Gotas Model
 *
 * @property \App\Model\Table\GotasTable|\Cake\ORM\Association\BelongsTo $Gotas
 *
 * @method \App\Model\Entity\Gota get($primaryKey, $options = [])
 * @method \App\Model\Entity\Gota newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Gota[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Gota|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Gota patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Gota[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Gota findOrCreate($search, callable $callback = null, $options = [])
 */
class GotasTable extends GenericTable
{

    /**
     * -----------------------------------------------------
     * Fields
     * -----------------------------------------------------
     */
    protected $gotasTable = null;

    /**
     * -----------------------------------------------------
     * Properties
     * -----------------------------------------------------
     */

    /**
     * Method get of client table property
     * @return (Cake\ORM\Table) Table object
     */
    private function _getGotasTable()
    {
        if (is_null($this->gotasTable)) {
            $this->_setGotasTable();
        }
        return $this->gotasTable;
    }

    /**
     * Method set of client table property
     * @return void
     */
    private function _setGotasTable()
    {
        $this->gotasTable = TableRegistry::get('Gotas');
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

        $this->setTable('gotas');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo(
            'Clientes',
            [
                'foreignKey' => 'clientes_id',
                'joinType' => 'INNER'
            ]
        );

        $this->hasOne(
            "GotasHasHistoricoValor",
            array(
                "className" => "GotasHasHistoricoValores",
                "foreignKey" => "gotas_id",
                "joinType" => "LEFT",
                "limit" => 1,
                "order" => array(
                    "audit_insert" => "desc"
                ),
            )
        );
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     *
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('nome_parametro')
            ->notEmpty('nome_parametro');

        $validator
            ->decimal('multiplicador_gota')
            ->requirePresence('multiplicador_gota', 'create')
            ->notEmpty('multiplicador_gota');

        $validator
            ->integer('habilitado');

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
        // $rules->add($rules->existsIn(['gotas_id'], 'Gotas'));

        return $rules;
    }

    #region Create

    /**
     * Cria uma nova gota
     *
     * @param int    $clientesId        Id de Cliente
     * @param string $nome_parametro     Nome da gota
     * @param float  $multiplicador_gota Multiplicador da gota
     *
     * @return boolean Registro gravado
     */
    public function createGota(int $clientesId, string $nome_parametro, float $multiplicador_gota)
    {
        try {
            $gota = $this->newEntity();

            $gota->clientes_id = $clientesId;
            $gota->nome_parametro = $nome_parametro;
            $gota->multiplicador_gota = $multiplicador_gota;

            return $this->save($gota);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao gravar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);
        }
    }

    /**
     * Insere/Atualiza
     *
     * Insere/Atualiza registro do banco de dados
     *
     * src\Model\Table\GotaTable.php::saveUpdate
     *
     * @param Gota $gota Objeto
     * @return \App\Model\Entity\Gota $gota Objeto
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-10-17
     */
    public function saveUpdate(Gota $gota)
    {
        try {
            return $this->save($gota);
        } catch (Exception $e) {
            $message = sprintf("[%s] %s: %s", MESSAGE_SAVED_ERROR, $e->getCode(), $e->getMessage());
            Log::write("error", $message);
            throw new Exception($message, $e->getCode());
        }
    }

    /**
     * GotasTable::saveUpdateBonificacaoExtraSefaz
     *
     * Insere/Atualiza registros de Gotas de Bonificação SEFAZ
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-07-21
     *
     * @param array $clientesIds Ids de Clientes
     * @param integer $qteGotasBonificacao Qte Bonificação
     *
     * @return \App\Model\Entity\Gota $gota
     */
    public function saveUpdateBonificacaoExtraSefaz(array $clientesIds, int $qteGotasBonificacao)
    {
        try {
            foreach ($clientesIds as $clienteId) {
                $gotaBonificacaoSistema = $this->find("all")
                    ->where(array(
                        "clientes_id" => $clienteId,
                        "nome_parametro" => GOTAS_BONUS_SEFAZ
                    ))
                    ->first();

                if (empty($gotaBonificacaoSistema)) {
                    $gotaBonificacaoSistema = $this->newEntity();
                    $gotaBonificacaoSistema->clientes_id = $clienteId;
                    $gotaBonificacaoSistema->nome_parametro = GOTAS_BONUS_SEFAZ;
                    $gotaBonificacaoSistema->habilitado = 1;
                    $gotaBonificacaoSistema->tipo_cadastro = GOTAS_REGISTER_TYPE_AUTOMATIC;
                }

                $gotaBonificacaoSistema->multiplicador_gota = $qteGotasBonificacao;
                $this->save($gotaBonificacaoSistema);
            }
        } catch (\Exception $e) {
            $message = sprintf("[%s] %s", MESSAGE_SAVED_EXCEPTION, $e->getMessage());
            Log::error($message);

            throw new Exception($message);
        }
    }

    /**
     * GotasTable::saveUpdateGotasAdjustment
     *
     * Insere/Atualiza registros de Gotas de Bonificação SEFAZ
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-11-08
     *
     * @param array $clientesIds Ids de Clientes
     *
     * @return \App\Model\Entity\Gota $gota
     */
    public function saveUpdateGotasAdjustment(array $clientesIds)
    {
        try {
            foreach ($clientesIds as $clienteId) {
                $gotaAjustePontos = $this->find("all")
                    ->where(array(
                        "clientes_id" => $clienteId,
                        "nome_parametro" => GOTAS_ADJUSTMENT_POINTS
                    ))
                    ->first();

                if (empty($gotaAjustePontos)) {
                    $gotaAjustePontos = $this->newEntity();
                    $gotaAjustePontos->clientes_id = $clienteId;
                    $gotaAjustePontos->nome_parametro = GOTAS_ADJUSTMENT_POINTS;
                    $gotaAjustePontos->habilitado = 1;
                    $gotaAjustePontos->tipo_cadastro = GOTAS_REGISTER_TYPE_AUTOMATIC;
                }

                $gotaAjustePontos->multiplicador_gota = 1;
                $this->save($gotaAjustePontos);
            }
        } catch (\Exception $e) {
            $message = sprintf("[%s] %s", MESSAGE_SAVED_EXCEPTION, $e->getMessage());
            Log::error($message);

            throw new Exception($message);
        }
    }

    #region Read

    /**
     * Encontra todas as 'gotas' de clientes
     *
     * @param array $clientesIds     Id de Cliente
     * @param array $whereConditions Condições de pesquisa
     *
     * @return (entity\Gotas)[] $gotas
     **/
    public function findGotasByClientesId(array $clientesIds = [], array $whereConditions = [])
    {
        try {
            $conditionsSql = array();

            $conditionsSql[] = [
                'clientes_id IN ' => $clientesIds,
                // Exibir somente cadastro manual
                "tipo_cadastro" => 0
            ];

            foreach ($whereConditions as $key => $value) {
                $conditionsSql[] = $value;
            }

            return $this->find('all')
                ->where($conditionsSql)
                ->contain(['Clientes']);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Encontra todas as 'gotas' de um cliente
     *
     * @param int $clientesId Id de Cliente
     *
     * @return (entity\Gotas)[] $gotas
     **/
    public function findGotasEnabledByClientesId(int $clientesId)
    {
        try {
            return $this->find('all')
                ->where(
                    [
                        'Gotas.clientes_id' => $clientesId,
                        'Gotas.habilitado' => true,
                    ]
                )
                ->contain(array("GotasHasHistoricoValor"));
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registro: " . $e->getMessage());

            Log::write('error', $stringError);

            // $this->Flash->error($stringError);
        }
    }

    /**
     * Obtêm todas as gotas para a rede especificada pelo Id
     *
     * @param array $clientesIds     Ids de Cliente
     * @param array $where_conditions Condições de pesquisa
     *
     * @return array $clientes Lista de clientes com gotas
     */
    public function getAllGotasWithClientes(array $clientesIds, array $where_conditions = array())
    {
        try {
            // obtem todos os registros

            $conditions = [];

            array_push(
                $conditions,
                [
                    'id in ' => $clientesIds
                ]
            );

            foreach ($where_conditions as $key => $condition) {
                array_push($conditions, $condition);
            }

            $query = $this->_getGotasTable()->Clientes->find('all')
                ->where($conditions)
                ->contain(['Gotas']);

            return $query;
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

            $error = [false, $stringError];
            return $error;
        }
    }

    /**
     * GotasTable::getGotaBonificacaoSefaz
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-07-21
     *
     * Obtem Gota de Bonificação Sefaz do Posto
     *
     * @param integer $clientesId
     *
     * @return \App\Model\Entity\Gota
     */
    public function getGotaBonificacaoSefaz(int $clientesId)
    {
        try {
            return $this->find("all")->where(
                [
                    "clientes_id" => $clientesId,
                    "nome_parametro" => GOTAS_BONUS_SEFAZ,
                    "tipo_cadastro" => GOTAS_REGISTER_TYPE_AUTOMATIC,
                    "habilitado" => 1
                ]
            )->select(
                [
                    "id",
                    "multiplicador_gota"
                ]
            )->first();
        } catch (Exception $ex) {
            $message = sprintf("[%s] %s", MSG_LOAD_EXCEPTION, $ex->getMessage());
            Log::write("error", $message);
            throw new Exception($message);
        }
    }

    /**
     * Obtem gotas do Banco
     *
     * Obtêm registro de gotas do banco conforme parâmetros passados
     *
     * src/Model/Table/GotasTable.php::getGotas
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-10-11
     *
     * @param integer $clientesId Id do Cliente
     * @param string $nomeParametro Nome do Parâmetro
     * @param float $multiplicadorGotaMinimo Valor Multiplicador Mínimo
     * @param float $multiplicadorGotaMaximo Valor Multiplicador Máximo
     * @param integer $habilitado Habilitado
     * @param integer $tipoCadastro Tipo de Cadastro (0 - Manual / 1 - Automático, sendo Manual inserido pelo usuário)
     *
     * @return \App\Model\Entity\Gota[] Gotas
     */
    public function getGotas(int $clientesId = null, string $nomeParametro = null, float $multiplicadorGotaMinimo = null, float $multiplicadorGotaMaximo = null, int $habilitado = null, int $tipoCadastro = 0)
    {
        try {
            $where = [];

            if (!empty($clientesId)) {
                $where[] = ["Gotas.clientes_id" => $clientesId];
            }

            if ($tipoCadastro == 1) {
                $where[] = ["Gotas.nome_parametro" => $nomeParametro];
            } else {
                if (!empty($nomeParametro)) {
                    $where[] = ["Gotas.nome_parametro like" => "'%$nomeParametro%'"];
                }
            }

            if (isset($multiplicadorGotaMinimo)) {
                $where[] = ["Gotas.multiplicador_gota >= " => $multiplicadorGotaMinimo];
            }

            if (isset($multiplicadorGotaMaximo)) {
                $where[] = ["Gotas.multiplicador_gota <= " => $multiplicadorGotaMaximo];
            }

            if (isset($habilitado)) {
                $where[] = ["Gotas.habilitado" => $habilitado];
            }

            if (isset($tipoCadastro)) {
                $where[] = ["Gotas.tipo_cadastro" => $tipoCadastro];
            }

            $orderBy = [
                "Gotas.nome_parametro" => "ASC"
            ];

            return $this->find("all")
                ->where($where)
                ->order($orderBy);
        } catch (\Throwable $th) {
            $message = sprintf("[%s] %s", MESSAGE_LOAD_EXCEPTION, $th->getMessage());
            Log::write("error", $message);
            throw new Exception($message, MESSAGE_LOAD_EXCEPTION_CODE);
        }
    }

    /**
     * Obtêm gota pelo ID
     *
     * @param int $id Id da Gota
     *
     * @return object $gota Gota
     */
    public function getGotasById(int $id)
    {
        try {
            return $this->find('all')
                ->where(
                    array(
                        'id' => $id
                    )
                )->first();
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

            $error = [false, $stringError];
            return $error;
        }
    }

    /**
     * GotasTable::getGotasByIdNome
     *
     * Obtêm gota pelo ID e nome
     *
     * @param int $id Id da Gota
     * @param string $nomeParametro Nome do Parametro de gota
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 22/08/2018
     *
     * @return object $gota Gota
     */
    public function getGotasByIdNome(int $id, string $nomeParametro = null)
    {
        try {
            return $this->_getGotasTable()->find('all')
                ->where(
                    array(
                        'id' => $id,
                        "nome_parametro like '%$nomeParametro%'"
                    )
                )->first();
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

            $error = [false, $stringError];
            return $error;
        }
    }

    /**
     * Obtem gota por id e clientes Id
     *
     * @param int $id          Id da gota
     * @param int $clientesId Id de clientes
     *
     * @return (entity\Gotas) $gota
     */
    public function getGotaClienteById(int $id, int $clientesId)
    {
        try {
            return $this->_getGotasTable()->find('all')
                ->where(
                    [
                        'id' => $id,
                        'clientes_id' => $clientesId
                    ]
                )->first();
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

            $error = [false, $stringError];
            return $error;
        }
    }

    /**
     * Obtem gota de cliente pelo nome
     *
     * @param int    $clientesId    Id de cliente
     * @param string $nome_parametro Nome do parâmetro
     *
     * @return (entity\Gotas) $gota
     */
    public function getGotaClienteByName(int $clientesId, string $nome_parametro)
    {
        try {
            return $this->_getGotasTable()->find('all')
                ->where(
                    [
                        'clientes_id' => $clientesId,
                        'nome_parametro' => $nome_parametro
                    ]
                )->first();
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

            $error = [false, $stringError];
            return $error;
        }
    }

    #region Update

    /**
     * Define todas as gotas de um cliente para a matriz
     *
     * @param int $clientesId Id de Cliente
     * @param int $matriz_id   Id da Matriz
     *
     * @return boolean
     */
    public function setGotasToMainCliente(int $clientesId, int $matriz_id)
    {
        try {
            return $this->updateAll(
                [
                    'clientes_id' => $matriz_id,
                    'habilitado' => false,
                ],
                [
                    'clientes_id' => $clientesId
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

    /**
     * Habilita/Desabilita uma gota
     *
     * @param int  $id     Id da Gota
     * @param bool $status Estado de habilitado
     *
     * @return bool registro atualizado
     */
    public function updateStatusGota(int $id, bool $status)
    {
        try {
            $gotas = $this->_getGotasTable()->query();

            $success[0] = $gotas->update()
                ->set(['habilitado' => $status])
                ->where(['id' => $id])
                ->execute();

            return $success;
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

            $error = [false, $stringError];
            return $error;
        }
    }

    #region Delete

    /**
     * Apaga todas as gotas de um cliente
     *
     * @param array $clientesIds Ids de clientes
     *
     * @return boolean
     */
    public function deleteAllGotasByClientesIds(array $clientesIds)
    {
        try {
            return $this->_getGotasTable()
                ->deleteAll(
                    [
                        'clientes_id in' => $clientesIds
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
}
