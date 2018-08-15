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
 * TipoBrindesClientes Model
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
class TipoBrindesClientesTable extends GenericTable
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
     * Método para obter tabela de Genero Brindes
     *
     * @return Cake\ORM\Table Table object
     */
    private function _getTipoBrindesClientesTable()
    {
        if (is_null($this->tipoBrindesClientesTable)) {
            $this->_setTipoBrindesClientesTable();
        }
        return $this->tipoBrindesClientesTable;
    }

    /**
     * Method set of brinde table property
     *
     * @return void
     */
    private function _setTipoBrindesClientesTable()
    {
        $this->tipoBrindesClientesTable = TableRegistry::get('TipoBrindesClientes');
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

        $this->belongsTo('TipoBrindes', [
            'foreignKey' => 'genero_brindes_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany("ClientesHasBrindesHabilitados", [
            "foreignKey" => "genero_brindes_clientes_id",
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
        $rules->add($rules->existsIn(['genero_brindes_id'], 'TipoBrindes'));
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
     * TipoBrindesClientesTable::findTipoBrindesClientes
     *
     * Procura gênero de brindes de um cliente conforme condições
     *
     * @param array $whereConditions Array de condições
     * @param int   $limit           Limite da consulta
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 03/06/2018
     *
     * @return \App\Model\Entity\TipoBrindesCliente[] $dados
     */
    public function findTipoBrindesClientes(array $whereConditions = array(), int $limit = 999)
    {
        try {
            $result = $this->_getTipoBrindesClientesTable()
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

            $stringError = __("Erro ao salvar gênero de brindes ao cliente: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write('error', $trace);
        }
    }

    /**
     * TipoBrindesTable::findTipoBrindesClienteByClientesIds
     *
     * Obtem todos os gêneros de brindes de cliente através dos ids de clientes
     *
     * @param array $clientesIds Ids de clientes
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 28/06/2018
     *
     * @return \App\Model\Entity\GeneroBrinde $tipoBrindes Objeto gravado
     */
    public function findTipoBrindesClienteByClientesIds(array $clientesIds = array())
    {
        try {

            $tipoBrindesClientes = $this->_getTipoBrindesClientesTable()
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
                    "genero_brindes_id"
                ))
                ->toArray();

            $tipoBrindesIds = array();

            foreach ($tipoBrindesClientes as $generoBrindeCliente) {
                $tipoBrindesIds[] = $generoBrindeCliente["genero_brindes_id"];
            }

            return $tipoBrindesIds;
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao obter gênero de brindes do cliente: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write('error', $trace);
        }
    }

    /**
     * TipoBrindesTable::findTipoBrindesClienteByClientesIdGeneroBrindeId
     *
     * Obtem todos os gêneros de brindes de cliente através dos ids de clientes
     *
     * @param array $clientesIds Ids de clientes
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 28/06/2018
     *
     * @return \App\Model\Entity\GeneroBrinde $tipoBrindes Objeto gravado
     */
    public function findTipoBrindesClienteByClientesIdGeneroBrindeId(int $clientesId, int $tipoBrindesId = null)
    {
        try {

            $whereConditions = array(
                "habilitado" => 1,
                "tipo_principal_codigo_brinde IS NOT NULL",
                "tipo_secundario_codigo_brinde IS NOT NULL",
                "clientes_id IN " => [$clientesId]
            );

            if (!empty($tipoBrindesId) && ($tipoBrindesId > 0)) {
                $whereConditions[] = array("genero_brindes_id" => $tipoBrindesId);
            }

            $tipoBrindesClientes = $this->_getTipoBrindesClientesTable()
                ->find('all')
                ->where($whereConditions)
                ->select(array(
                    "id",
                    "genero_brindes_id"
                ))
                ->toArray();

            $tipoBrindesClientesIds = array();

            foreach ($tipoBrindesClientes as $generoBrindeCliente) {
                $tipoBrindesClientesIds[] = $generoBrindeCliente["id"];
            }

            return $tipoBrindesClientesIds;
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao obter gênero de brindes do cliente: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write('error', $trace);
        }
    }

    /**
     * TipoBrindesClientesTable::getTipoBrindesClientesById
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
    public function getTipoBrindesClientesById(int $id)
    {
        try {
            return $this->_getTipoBrindesClientesTable()
                ->find('all')
                ->where(
                    [
                        "TipoBrindesClientes.id" => $id
                    ]
                )->contain(["TipoBrindes"])
                ->first();
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao salvar gênero de brindes ao cliente: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
        }
    }

    /**
     * TipoBrindesClientesTable::getTipoBrindesClientesByClientesId
     *
     * Obtem os gênero de brindes de um cliente através do ClientesId
     *
     * @param integer $clientesId Id de Cliente
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 03/06/2018
     *
     * @return \App\Model\Entity\TipoBrindesCliente[] $dados
     */
    public function getTipoBrindesClientesByClientesId(int $clientesId)
    {
        try {
            return $this->_getTipoBrindesClientesTable()->find('all')
                ->where([
                    "clientes_id" => $clientesId
                ])->contain(["TipoBrindes", "ClientesHasBrindesHabilitados"]);
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao salvar gênero de brindes ao cliente: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
        }
    }

    /**
     * TipoBrindesClientesTable::getTipoBrindesClientesByGeneroCliente
     *
     * Obtem o gênero de brindes de um cliente através do ClientesId e Gênero Brinde Id
     *
     * @param integer $tipoBrindesId Id do Gênero de Brinde
     * @param integer $clientesId      Id de Cliente
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 12/06/2018
     *
     * @return \App\Model\Entity\TipoBrindesCliente[] $dados
     */
    public function getTipoBrindesClientesByGeneroCliente(int $tipoBrindesId, int $clientesId)
    {
        try {
            return $this->_getTipoBrindesClientesTable()->find('all')
                ->where(
                    array(
                        "genero_brindes_id" => $tipoBrindesId,
                        "clientes_id" => $clientesId
                    )
                )->first();
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao obter gênero de brindes ao cliente: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
        }
    }

    /**
     * TipoBrindesClientesTable::getGenerosBrindesClientesDisponiveis
     *
     * Obtem GêneroBrindes Disponíveis
     *
     * @param integer $clientesId Id de Cliente
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 03/06/2018
     *
     * @return \App\Model\Entity\array[] $list
     */
    public function getGenerosBrindesClientesDisponiveis(int $clientesId)
    {
        try {
            $tipoBrindesIds = array();
            $tipoBrindesJaUsadosQuery = $this->_getTipoBrindesClientesTable()->findTipoBrindesClientes(["clientes_id in " => [$clientesId]]);

            foreach ($tipoBrindesJaUsadosQuery->toArray() as $key => $generoBrindeCliente) {
                $tipoBrindesIds[] = $generoBrindeCliente["genero_brindes_id"];
            }

            $tipoBrindes = $this->TipoBrindes->find('list');

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

            $stringError = __("Erro ao obter gênero de brindes do cliente disponíveis: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
        }

    }

    /**
     * TipoBrindesClientesTable::getGenerosBrindesClientesDisponiveis
     *
     * Obtem GêneroBrindes Vinculados a um cliente
     *
     * @param integer $clientesId Id de Cliente
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 08/06/2018
     *
     * @return \App\Model\Entity\array[] $list
     */
    public function getGenerosBrindesClientesVinculados(array $clientesIds)
    {
        try {
            $tipoBrindesIds = array();
            $tipoBrindesJaUsadosQuery = $this->_getTipoBrindesClientesTable()->findTipoBrindesClientes(["clientes_id in " => $clientesIds]);

            foreach ($tipoBrindesJaUsadosQuery->toArray() as $key => $generoBrindeCliente) {
                $tipoBrindesIds[] = $generoBrindeCliente["genero_brindes_id"];
            }

            $tipoBrindes = $this->TipoBrindes->find('list');

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

            $stringError = __("Erro ao obter gênero de brindes do cliente disponíveis: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write('error', $trace);
        }
    }
    /**
     * TipoBrindesClientesTable::getGenerosBrindesClientesIdsFromConditions
     *
     * Obtem Ids de Gênero Brindes Clientes de condição informada
     *
     * @param array $tipoBrindesClientesConditions Condições
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 06/07/2018
     *
     * @return \App\Model\Entity\TipoBrindesClientes[] $list
     */
    public function getGenerosBrindesClientesIdsFromConditions(array $tipoBrindesClientesConditions)
    {
        try {
            $tipoBrindesClientes = $this->_getTipoBrindesClientesTable()
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

            $stringError = __("Erro ao obter gênero de brindes do cliente disponíveis: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write('error', $trace);
        }
    }

    /* -------------------------- Create/Update ----------------------------- */

    /**
     * TipoBrindesTable::saveTipoBrindes()
     *
     * Salva/Atualiza um Gênero de Brinde
     *
     * @param array $tipoBrindes
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 31/05/2018
     *
     * @return \App\Model\Entity\GeneroBrinde $tipoBrindes Objeto gravado
     */
    public function saveGeneroBrindeCliente(array $generoBrindeCliente = array())
    {
        try {
            $itemSave = null;

            if (!empty($generoBrindeCliente["id"]) && $generoBrindeCliente["id"] > 0) {
                $itemSave = $this->getTipoBrindesClientesById($generoBrindeCliente["id"]);
            } else {
                $itemSave = $this->_getTipoBrindesClientesTable()->newEntity();
            }

            $itemSave["genero_brindes_id"] = $generoBrindeCliente["genero_brindes_id"];
            $itemSave["clientes_id"] = $generoBrindeCliente["clientes_id"];
            $itemSave["tipo_principal_codigo_brinde"] = (int)$generoBrindeCliente["tipo_principal_codigo_brinde"];
            $itemSave["tipo_secundario_codigo_brinde"] = $generoBrindeCliente["tipo_secundario_codigo_brinde"];
            $itemSave["habilitado"] = $generoBrindeCliente["habilitado"];

            return $this->_getTipoBrindesClientesTable()->save($itemSave);

        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao salvar gênero de brindes ao cliente: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
        }
    }

    /**
     * TipoBrindesClientesTable::updateHabilitadoGeneroBrindeCliente
     *
     * Undocumented function
     *
     * @param integer $id
     * @param boolean $habilitado
     * @return \App\Model\Entity\TipoBrindesCliente Registro atualizado
     */
    public function updateHabilitadoGeneroBrindeCliente(int $id, bool $habilitado)
    {
        try {

            if (empty($id) || $id == 0) {
                throw new \Exception("Id não informado!");
            }
            $itemSave = $this->_getTipoBrindesClientesTable()->get($id);

            $itemSave["habilitado"] = $habilitado;

            return $this->_getTipoBrindesClientesTable()->save($itemSave);
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao atualizar estado de gênero de brindes do cliente: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
        }
    }



    /* -------------------------- Delete ----------------------------- */

    /**
     * TipoBrindesClientesTable::deleteTipoBrindesClientesById
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
    public function deleteTipoBrindesClientesById(int $tipoBrindesClientesId)
    {
        try {
            $tipoBrindesCliente = $this->_getTipoBrindesClientesTable()->get($tipoBrindesClientesId);

            return $this->_getTipoBrindesClientesTable->delete($tipoBrindesCliente);
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao remover gênero de brindes do cliente: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write('error', $trace);
        }
    }
}
