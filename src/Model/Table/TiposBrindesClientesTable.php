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
 * TiposBrindesClientes Model
 *
 * @property \App\Model\Table\TipoBrindesTable|\Cake\ORM\Association\BelongsTo $TipoBrindes
 * @property \App\Model\Table\ClientesTable|\Cake\ORM\Association\BelongsTo $Clientes
 *
 * @method \App\Model\Entity\TipoBrindesCliente get($primaryKey, $options = [])
 * @method \App\Model\Entity\TipoBrindesCliente newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\TipoBrindesCliente[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\TipoBrindesCliente|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\TipoBrindesCliente patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\TipoBrindesCliente[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\TipoBrindesCliente findOrCreate($search, callable $callback = null, $options = [])
 */
class TiposBrindesClientesTable extends GenericTable
{
    /**
     * -------------------------------------------------------------
     * Fields
     * -------------------------------------------------------------
     */
    protected $tipoBrindesClientesTable = null;

    /**
     * -------------------------------------------------------------
     * Properties
     * -------------------------------------------------------------
     */

    /**
     * Método para obter tabela de Tipos Brindes Clientes
     *
     * @return Cake\ORM\Table Table object
     */
    private function _getTiposBrindesClientesTable()
    {
        if (is_null($this->tipoBrindesClientesTable)) {
            $this->_setTiposBrindesClientesTable();
        }
        return $this->tipoBrindesClientesTable;
    }

    /**
     * Method set of brinde table property
     *
     * @return void
     */
    private function _setTiposBrindesClientesTable()
    {
        $this->tipoBrindesClientesTable = TableRegistry::get('TiposBrindesClientes');
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

        $this->setTable('tipos_brindes_clientes');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('TiposBrindesRedes', [
            'foreignKey' => 'tipos_brindes_redes_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany("ClientesHasBrindesHabilitados", [
            "foreignKey" => "tipos_brindes_clientes_id",
            "joinType" => "INNER"
        ]);
        $this->belongsTo('Clientes', [
            'foreignKey' => 'clientes_id',
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
            ->requirePresence('tipo_principal_codigo_brinde', 'create')
            ->notEmpty('tipo_principal_codigo_brinde');

        $validator
            ->requirePresence('tipo_secundario_codigo_brinde', 'create')
            ->notEmpty('tipo_secundario_codigo_brinde');

        $validator
            ->boolean('habilitado')
            ->requirePresence('habilitado', 'create')
            ->notEmpty('habilitado');

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
        $rules->add($rules->existsIn(['tipos_brindes_redes_id'], 'TiposBrindesRedes'));
        $rules->add($rules->existsIn(['clientes_id'], 'Clientes'));

        return $rules;
    }

    /**
     * ---------------------------------------------------------------
     * Métodos CRUD
     * ---------------------------------------------------------------
     */

     /* -------------------------- Read ----------------------------- */

    /**
     * TiposBrindesClientesTable::findTiposBrindesClientes
     *
     * Procura tipo de brindes de um cliente conforme condições
     *
     * @param array $whereConditions Array de condições
     * @param int   $limit           Limite da consulta
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 03/06/2018
     *
     * @return \App\Model\Entity\TipoBrindesCliente[] $dados
     */
    public function findTiposBrindesClientes(array $whereConditions = array(), int $limit = 999)
    {
        try {
            $result = $this
                ->find('all')
                ->where(
                    $whereConditions
                );

            if ($limit == 1) {
                $result = $result->first();
            } else {
                $result = $result->limit($limit);
            }

            return $result;
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao salvar tipo de brindes ao cliente: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write('error', $trace);
        }
    }

    /**
     * TipoBrindesTable::findTiposBrindesClienteByClientesIds
     *
     * Obtem todos os tipos de brindes de cliente através dos ids de clientes
     *
     * @param array $clientesIds Ids de clientes
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 28/06/2018
     *
     * @return \App\Model\Entity\TiposBrinde $tipoBrindes Objeto gravado
     */
    public function findTiposBrindesClienteByClientesIds(array $clientesIds = array())
    {
        try {

            $tipoBrindesClientes = $this->_getTiposBrindesClientesTable()
                ->find('all')
                ->where(
                    array(
                        "habilitado" => 1,
                        "tipo_principal_codigo_brinde IS NOT NULL",
                        "tipo_secundario_codigo_brinde IS NOT NULL",
                        "clientes_id IN " => $clientesIds
                    )
                )
                ->select(array(
                    "id",
                    "tipos_brindes_redes_id"
                ))
                ->toArray();

            $tipoBrindesIds = array();

            foreach ($tipoBrindesClientes as $tiposBrindesCliente) {
                $tipoBrindesIds[] = $tiposBrindesCliente["tipos_brindes_redes_id"];
            }

            return $tipoBrindesIds;
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao obter tipo de brindes do cliente: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write('error', $trace);
        }
    }

    /**
     * TipoBrindesTable::findTiposBrindesClienteByClientesIdTiposBrindesRedesId
     *
     * Obtem todos os tipos de brindes de cliente através dos ids de clientes
     *
     * @param array $clientesIds Ids de clientes
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 28/06/2018
     *
     * @return \App\Model\Entity\TiposBrinde $tipoBrindes Objeto gravado
     */
    public function findTiposBrindesClienteByClientesIdTiposBrindesRedesId(int $clientesId, int $tipoBrindesId = null)
    {
        try {

            $whereConditions = array(
                "habilitado" => 1,
                // Ele pode não ter código
                // "tipo_principal_codigo_brinde IS NOT NULL",
                // "tipo_secundario_codigo_brinde IS NOT NULL",
                "clientes_id IN " => [$clientesId]
            );

            if (!empty($tipoBrindesId) && ($tipoBrindesId > 0)) {
                $whereConditions[] = array("tipos_brindes_redes_id" => $tipoBrindesId);
            }

            $tipoBrindesClientes = $this->_getTiposBrindesClientesTable()
                ->find('all')
                ->where($whereConditions)
                ->select(array(
                    "id",
                    "tipos_brindes_redes_id"
                ))
                ->toArray();

            $tipoBrindesClientesIds = array();

            foreach ($tipoBrindesClientes as $tiposBrindesCliente) {
                $tipoBrindesClientesIds[] = $tiposBrindesCliente["id"];
            }

            return $tipoBrindesClientesIds;
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao obter tipo de brindes do cliente: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write('error', $trace);
        }
    }

    /**
     * TiposBrindesClientesTable::getTiposBrindesClientesById
     *
     * Obtem um item pelo Id
     *
     * @param integer $id Id do Registro
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 03/06/2018
     *
     * @return \App\Model\Entity\TipoBrindesCliente $dado
     */
    public function getTiposBrindesClientesById(int $id)
    {
        try {
            return $this->_getTiposBrindesClientesTable()
                ->find('all')
                ->where(
                    [
                        "TiposBrindesClientes.id" => $id
                    ]
                )->contain(["TiposBrindesRedes.Rede"])
                ->first();
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao salvar tipo de brindes ao cliente: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
        }
    }

    /**
     * TiposBrindesClientesTable::getTiposBrindesClientesByClientesId
     *
     * Obtem os tipo de brindes de um cliente através do ClientesId
     *
     * @param integer $clientesId Id de Cliente
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 03/06/2018
     *
     * @return \App\Model\Entity\TipoBrindesCliente[] $dados
     */
    public function getTiposBrindesClientesByClientesId(int $clientesId)
    {
        try {
            return $this->_getTiposBrindesClientesTable()->find('all')
                ->where([
                    "clientes_id" => $clientesId
                ])->contain(["TiposBrindesRedes", "ClientesHasBrindesHabilitados"]);
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao salvar tipo de brindes ao cliente: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
        }
    }

    /**
     * TiposBrindesClientesTable::getTiposBrindesClientesByTiposBrindesRedes
     *
     * Obtem o tipo de brindes de um cliente através do ClientesId e tipo Brinde Id
     *
     * @param integer $tipoBrindesId Id do tipo de Brinde
     * @param integer $clientesId      Id de Cliente
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 12/06/2018
     *
     * @return \App\Model\Entity\TipoBrindesCliente[] $dados
     */
    public function getTiposBrindesClientesByTiposBrindesRedes(int $tipoBrindesId, int $clientesId)
    {
        try {
            return $this->_getTiposBrindesClientesTable()->find('all')
                ->where(
                    array(
                        "tipos_brindes_redes_id" => $tipoBrindesId,
                        "clientes_id" => $clientesId
                    )
                )->first();
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao obter tipo de brindes ao cliente: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
        }
    }

    /**
     * TiposBrindesClientesTable::getTiposBrindesClientesDisponiveis
     *
     * Obtem TiposBrindes Disponíveis
     *
     * @param integer $clientesId Id de Cliente
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 03/06/2018
     *
     * @return \App\Model\Entity\array[] $list
     */
    public function getTiposBrindesClientesDisponiveis(int $clientesId)
    {
        try {
            $tipoBrindesIds = array();
            $tipoBrindesJaUsadosQuery = $this->findTiposBrindesClientes(["clientes_id in " => [$clientesId]]);

            foreach ($tipoBrindesJaUsadosQuery->toArray() as $key => $tipoBrindesClienteJaUsado) {
                $tipoBrindesIds[] = $tipoBrindesClienteJaUsado["tipos_brindes_redes_id"];
            }

            $tipoBrindes = $this->TiposBrindesRedes->find('list');

            if (sizeof($tipoBrindesIds) > 0) {
                $tipoBrindes = $tipoBrindes->where(
                    [
                        "id not in" => $tipoBrindesIds,
                        "habilitado" => 1
                    ]
                );
            }

            return $tipoBrindes;
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao obter tipo de brindes do cliente disponíveis: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
        }

    }

    /**
     * TiposBrindesClientesTable::getTiposBrindesClientesDisponiveis
     *
     * Obtem TiposBrindes Vinculados a um cliente
     *
     * @param integer $clientesId Id de Cliente
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 08/06/2018
     *
     * @return \App\Model\Entity\array[] $list
     */
    public function getTiposBrindesClientesVinculados(array $clientesIds)
    {
        try {
            $tipoBrindesIds = array();
            $tipoBrindesJaUsadosQuery = $this->findTiposBrindesClientes(["clientes_id in " => $clientesIds]);

            foreach ($tipoBrindesJaUsadosQuery->toArray() as $key => $tiposBrindesCliente) {
                $tipoBrindesIds[] = $tiposBrindesCliente["tipos_brindes_redes_id"];
            }

            $tipoBrindes = $this->TiposBrindesRedes->find('list');

            if (sizeof($tipoBrindesIds) > 0) {
                $tipoBrindes = $tipoBrindes->where(
                    [
                        "id in" => $tipoBrindesIds,
                        "habilitado" => 1
                    ]
                );
            }

            return $tipoBrindes;
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao obter tipo de brindes do cliente disponíveis: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write('error', $trace);
        }
    }
    /**
     * TiposBrindesClientesTable::getTiposBrindesClientesIdsFromConditions
     *
     * Obtem Ids de Tipos Brindes Clientes de condição informada
     *
     * @param array $tipoBrindesClientesConditions Condições
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 06/07/2018
     *
     * @return \App\Model\Entity\TiposBrindesClientes[] $list
     */
    public function getTiposBrindesClientesIdsFromConditions(array $tipoBrindesClientesConditions)
    {
        try {
            $tipoBrindesClientes = $this->_getTiposBrindesClientesTable()
                ->find('all')
                ->where($tipoBrindesClientesConditions)
                ->toArray();

            $tipoBrindesClientesIds = array();

            foreach ($tipoBrindesClientes as $item) {
                $tipoBrindesClientesIds[] = $item["id"];
            }

            return $tipoBrindesClientesIds;
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao obter tipo de brindes do cliente disponíveis: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write('error', $trace);
        }
    }

    /* -------------------------- Create/Update ----------------------------- */

    /**
     * TipoBrindesTable::saveTiposBrindeCliente()
     *
     * Salva/Atualiza um Tipos de Brinde
     *
     * @param integer $tiposBrindesRedesId
     * @param integer $clientesId
     * @param string $tipoPrincipalCodigoBrinde
     * @param string $tipoSecundarioCodigoBrinde
     * @param boolean $habilitado
     * @param integer $id
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 31/05/2018
     *
     * @return \App\Model\Entity\TiposBrindesClientes $tipoBrindes Objeto gravado
     */

    public function saveTiposBrindeCliente(
        int $tiposBrindesRedesId,
        int $clientesId,
        string $tipoPrincipalCodigoBrinde,
        string $tipoSecundarioCodigoBrinde,
        bool $habilitado,
        int $id = null
    ) {
        try {
            $itemSave = null;

            if (!empty($id) && $id > 0) {
                $itemSave = $this->getTiposBrindesClientesById($id);
            } else {
                $itemSave = $this->_getTiposBrindesClientesTable()->newEntity();
            }

            $itemSave["tipos_brindes_redes_id"] = $tiposBrindesRedesId;
            $itemSave["clientes_id"] = $clientesId;
            $itemSave["tipo_principal_codigo_brinde"] = $tipoPrincipalCodigoBrinde;
            $itemSave["tipo_secundario_codigo_brinde"] = $tipoSecundarioCodigoBrinde;
            $itemSave["habilitado"] = $habilitado;

            return $this->_getTiposBrindesClientesTable()->save($itemSave);

        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao salvar tipo de brindes ao cliente: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
        }
    }

    /**
     * TiposBrindesClientesTable::updateHabilitadoTiposBrindesCliente
     *
     * Undocumented function
     *
     * @param integer $id
     * @param boolean $habilitado
     * @return \App\Model\Entity\TipoBrindesCliente Registro atualizado
     */
    public function updateHabilitadoTiposBrindesCliente(int $id, bool $habilitado)
    {
        try {

            if (empty($id) || $id == 0) {
                throw new \Exception("Id não informado!");
            }
            $itemSave = $this->_getTiposBrindesClientesTable()->get($id);

            $itemSave["habilitado"] = $habilitado;

            return $this->_getTiposBrindesClientesTable()->save($itemSave);
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao atualizar estado de tipo de brindes do cliente: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
        }
    }

    /* -------------------------- Delete ----------------------------- */

    /**
     * TiposBrindesClientesTable::deleteTiposBrindesClientesById
     *
     * Remove um registro pelo Id
     *
     * @param integer $tipoBrindesClientesId Id do registro
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 08/06/2018
     *
     * @return boolean
     */
    public function deleteTiposBrindesClientesById(int $tipoBrindesClientesId)
    {
        try {
            $tipoBrindesCliente = $this->_getTiposBrindesClientesTable()->get($tipoBrindesClientesId);

            return $this->_getTiposBrindesClientesTable->delete($tipoBrindesCliente);
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao remover tipo de brindes do cliente: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write('error', $trace);
        }
    }
}
