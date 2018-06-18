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

/**
 * GeneroBrindesClientes Model
 *
 * @property \App\Model\Table\GeneroBrindesTable|\Cake\ORM\Association\BelongsTo $GeneroBrindes
 * @property \App\Model\Table\ClientesTable|\Cake\ORM\Association\BelongsTo $Clientes
 *
 * @method \App\Model\Entity\GeneroBrindesCliente get($primaryKey, $options = [])
 * @method \App\Model\Entity\GeneroBrindesCliente newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\GeneroBrindesCliente[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\GeneroBrindesCliente|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\GeneroBrindesCliente patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\GeneroBrindesCliente[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\GeneroBrindesCliente findOrCreate($search, callable $callback = null, $options = [])
 */
class GeneroBrindesClientesTable extends GenericTable
{
    /**
     * -------------------------------------------------------------
     * Fields
     * -------------------------------------------------------------
     */
    protected $generoBrindesClientesTable = null;

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
    private function _getGeneroBrindesClientesTable()
    {
        if (is_null($this->generoBrindesClientesTable)) {
            $this->_setGeneroBrindesClientesTable();
        }
        return $this->generoBrindesClientesTable;
    }

    /**
     * Method set of brinde table property
     *
     * @return void
     */
    private function _setGeneroBrindesClientesTable()
    {
        $this->generoBrindesClientesTable = TableRegistry::get('GeneroBrindesClientes');
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

        $this->setTable('genero_brindes_clientes');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('GeneroBrindes', [
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
            ->integer('tipo_principal_codigo_brinde')
            ->requirePresence('tipo_principal_codigo_brinde', 'create')
            ->notEmpty('tipo_principal_codigo_brinde');

        $validator
            ->integer('tipo_secundario_codigo_brinde')
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
        $rules->add($rules->existsIn(['genero_brindes_id'], 'GeneroBrindes'));
        $rules->add($rules->existsIn(['clientes_id'], 'Clientes'));

        return $rules;
    }

    /**
     * ---------------------------------------------------------------
     * Métodos CRUD
     * ---------------------------------------------------------------
     */

    /* -------------------------- Create/Update ----------------------------- */

    /**
     * GeneroBrindesTable::saveGeneroBrindes()
     *
     * Salva/Atualiza um Gênero de Brinde
     *
     * @param array $generoBrindes
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 31/05/2018
     *
     * @return \App\Model\Entity\GeneroBrinde $generoBrindes Objeto gravado
     */
    public function saveGeneroBrindeCliente(array $generoBrindeCliente = array())
    {
        try {
            $itemSave = null;

            if (!empty($generoBrindeCliente["id"]) && $generoBrindeCliente["id"] > 0) {
                $itemSave = $this->getGeneroBrindesClientesById($generoBrindeCliente["id"]);
            } else {
                $itemSave = $this->_getGeneroBrindesClientesTable()->newEntity();
            }

            $itemSave["genero_brindes_id"] = $generoBrindeCliente["genero_brindes_id"];
            $itemSave["clientes_id"] = $generoBrindeCliente["clientes_id"];
            $itemSave["tipo_principal_codigo_brinde"] = (int)$generoBrindeCliente["tipo_principal_codigo_brinde"];
            $itemSave["tipo_secundario_codigo_brinde"] = $generoBrindeCliente["tipo_secundario_codigo_brinde"];
            $itemSave["habilitado"] = $generoBrindeCliente["habilitado"];

            return $this->_getGeneroBrindesClientesTable()->save($itemSave);

        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao salvar gênero de brindes ao cliente: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
        }
    }

    /**
     * GeneroBrindesClientesTable::updateHabilitadoGeneroBrindeCliente
     *
     * Undocumented function
     *
     * @param integer $id
     * @param boolean $habilitado
     * @return \App\Model\Entity\GeneroBrindesCliente Registro atualizado
     */
    public function updateHabilitadoGeneroBrindeCliente(int $id, bool $habilitado)
    {
        try {

            if (empty($id) || $id == 0) {
                throw new \Exception("Id não informado!");
            }
            $itemSave = $this->_getGeneroBrindesClientesTable()->get($id);

            $itemSave["habilitado"] = $habilitado;

            return $this->_getGeneroBrindesClientesTable()->save($itemSave);
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao atualizar estado de gênero de brindes do cliente: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
        }
    }

    /* -------------------------- Create/Update ----------------------------- */

    /**
     * GeneroBrindesClientesTable::findGeneroBrindesClientes
     *
     * Procura gênero de brindes de um cliente conforme condições
     *
     * @param array $whereConditions Array de condições
     * @param int   $limit           Limite da consulta
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 03/06/2018
     *
     * @return \App\Model\Entity\GeneroBrindesCliente[] $dados
     */
    public function findGeneroBrindesClientes(array $whereConditions = array(), int $limit = 999)
    {
        try {
            $result = $this->_getGeneroBrindesClientesTable()
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
        }
    }

    /**
     * GeneroBrindesClientesTable::getGeneroBrindesClientesById
     *
     * Obtem um item pelo Id
     *
     * @param integer $id Id do Registro
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 03/06/2018
     *
     * @return \App\Model\Entity\GeneroBrindesCliente $dado
     */
    public function getGeneroBrindesClientesById(int $id)
    {
        try {
            return $this->_getGeneroBrindesClientesTable()
                ->find('all')
                ->where(
                    [
                        "GeneroBrindesClientes.id" => $id
                    ]
                )->contain(["GeneroBrindes"])
                ->first();
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao salvar gênero de brindes ao cliente: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
        }
    }

    /**
     * GeneroBrindesClientesTable::getGeneroBrindesClientesByClientesId
     *
     * Obtem os gênero de brindes de um cliente através do ClientesId
     *
     * @param integer $clientesId Id de Cliente
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 03/06/2018
     *
     * @return \App\Model\Entity\GeneroBrindesCliente[] $dados
     */
    public function getGeneroBrindesClientesByClientesId(int $clientesId)
    {
        try {
            return $this->_getGeneroBrindesClientesTable()->find('all')
                ->where([
                    "clientes_id" => $clientesId
                ])->contain(["GeneroBrindes", "ClientesHasBrindesHabilitados"]);
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao salvar gênero de brindes ao cliente: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
        }
    }

    /**
     * GeneroBrindesClientesTable::getGeneroBrindesClientesByGeneroCliente
     *
     * Obtem o gênero de brindes de um cliente através do ClientesId e Gênero Brinde Id
     *
     * @param integer $generoBrindesId Id do Gênero de Brinde
     * @param integer $clientesId      Id de Cliente
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 12/06/2018
     *
     * @return \App\Model\Entity\GeneroBrindesCliente[] $dados
     */
    public function getGeneroBrindesClientesByGeneroCliente(int $generoBrindesId, int $clientesId)
    {
        try {
            return $this->_getGeneroBrindesClientesTable()->find('all')
                ->where(
                    array(
                        "genero_brindes_id" => $generoBrindesId,
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
     * GeneroBrindesClientesTable::getGenerosBrindesClientesDisponiveis
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
            $generoBrindesIds = array();
            $generoBrindesJaUsadosQuery = $this->_getGeneroBrindesClientesTable()->findGeneroBrindesClientes(["clientes_id in " => [$clientesId]]);

            foreach ($generoBrindesJaUsadosQuery->toArray() as $key => $generoBrinde) {
                $generoBrindesIds[] = $generoBrinde["genero_brindes_id"];
            }

            $generoBrindes = $this->GeneroBrindes->find('list');

            if (sizeof($generoBrindesIds) > 0) {
                $generoBrindes = $generoBrindes->where(
                    [
                        "id not in" => $generoBrindesIds,
                        "habilitado" => 1
                    ]
                );
            }

            return $generoBrindes;
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao obter gênero de brindes do cliente disponíveis: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
        }

    }

    /**
     * GeneroBrindesClientesTable::getGenerosBrindesClientesDisponiveis
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
    public function getGenerosBrindesClientesVinculados(int $clientesId)
    {
        try {
            $generoBrindesIds = array();
            $generoBrindesJaUsadosQuery = $this->_getGeneroBrindesClientesTable()->findGeneroBrindesClientes(["clientes_id in " => [$clientesId]]);

            foreach ($generoBrindesJaUsadosQuery->toArray() as $key => $generoBrinde) {
                $generoBrindesIds[] = $generoBrinde["genero_brindes_id"];
            }

            $generoBrindes = $this->GeneroBrindes->find('list');

            if (sizeof($generoBrindesIds) > 0) {
                $generoBrindes = $generoBrindes->where(
                    [
                        "id in" => $generoBrindesIds,
                        "habilitado" => 1
                    ]
                );
            }

            return $generoBrindes;
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao obter gênero de brindes do cliente disponíveis: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
        }

    }

    /* -------------------------- Delete ----------------------------- */

    /**
     * GeneroBrindesClientesTable::deleteGeneroBrindesClientesById
     *
     * Remove um registro pelo Id
     *
     * @param integer $generoBrindesClientesId Id do registro
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 08/06/2018
     *
     * @return boolean
     */
    public function deleteGeneroBrindesClientesById(int $generoBrindesClientesId)
    {
        try {
            $generoBrindesCliente = $this->_getGeneroBrindesClientesTable()->get($generoBrindesClientesId);

            return $this->_getGeneroBrindesClientesTable->delete($generoBrindesCliente);
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao remover gênero de brindes do cliente: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
        }
    }
}
