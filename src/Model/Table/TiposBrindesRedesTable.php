<?php
namespace App\Model\Table;

use ArrayObject;
use Exception;
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
 * TiposBrindesRedes Model
 *
 * @property \App\Model\Table\ClientesTable|\Cake\ORM\Association\BelongsToMany $Clientes
 *
 * @method \App\Model\Entity\TiposBrindesRede get($primaryKey, $options = [])
 * @method \App\Model\Entity\TiposBrindesRede newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\TiposBrindesRede[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\TiposBrindesRede|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\TiposBrindesRede patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\TiposBrindesRede[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\TiposBrindesRede findOrCreate($search, callable $callback = null, $options = [])
 */
class TiposBrindesRedesTable extends GenericTable
{

    /**
     * -------------------------------------------------------------
     * Fields
     * -------------------------------------------------------------
     */
    protected $tiposBrindesRedesTable = null;

    /**
     * -------------------------------------------------------------
     * Properties
     * -------------------------------------------------------------
     */

    /**
     * Método para obter tabela de Tipos Brindes Redes
     *
     * @return Cake\ORM\Table Table object
     */
    private function _getTiposBrindesRedesTable()
    {
        if (is_null($this->tiposBrindesRedesTable)) {
            $this->_setTiposBrindesRedesTable();
        }
        return $this->tiposBrindesRedesTable;
    }

    /**
     * Method set of brinde table property
     *
     * @return void
     */
    private function _setTiposBrindesRedesTable()
    {
        $this->tiposBrindesRedesTable = TableRegistry::get('TiposBrindesRedes');
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

        $this->setTable('tipos_brindes_redes');
        $this->setDisplayField('nome_necessidades_especiais');
        $this->setPrimaryKey('id');

        $this->belongsTo("Rede", array(
            "className" => "Redes",
            "foreignKey" => "redes_id",
            "joinTable" => "Redes"
        ));

        $this->hasOne(
            "TipoBrindesCliente",
            array(
                "className" => "TiposBrindesClientes",
                "foreignKey" => "tipos_brindes_redes_id",
                "joinType" => "LEFT"
            )
        );
        $this->hasMany(
            "TiposBrindesClientes",
            array(
                "className" => "TiposBrindesClientes",
                "foreignKey" => "tipos_brindes_redes_id",
                "joinType" => "LEFT"
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
            ->requirePresence('nome', 'create')
            ->notEmpty('nome');

        $validator
            ->integer('equipamento_rti')
            ->requirePresence('equipamento_rti', 'create')
            ->notEmpty('equipamento_rti', 'Por favor informe o tipo de Prestação de Serviços')
            ->add(
                'equipamento_rti',
                'inList',
                [
                    'rule' => ['inList', ['0', '1']],
                    'message' => 'Por favor informe o tipo de Prestação de Serviços',
                    "allowEmpty" => false
                ]
            );

        $validator
            ->boolean('brinde_necessidades_especiais')
            ->requirePresence('brinde_necessidades_especiais', 'create')
            ->notEmpty('brinde_necessidades_especiais ');

        $validator
            ->boolean('habilitado')
            ->requirePresence('habilitado', 'create')
            ->notEmpty('habilitado');

        $validator
            ->boolean('atribuir_automatico')
            ->requirePresence('atribuir_automatico', 'create')
            ->notEmpty('atribuir_automatico');

        $validator
            ->requirePresence('tipo_principal_codigo_brinde_default', 'create')
            ->allowEmpty('tipo_principal_codigo_brinde_default');

        $validator
            ->requirePresence('tipo_secundario_codigo_brinde_default', 'create')
            ->allowEmpty('tipo_secundario_codigo_brinde_default');

        $validator
            ->dateTime('audit_insert')
            ->allowEmpty('audit_insert');

        $validator
            ->dateTime('audit_update')
            ->allowEmpty('audit_update');

        return $validator;
    }

    /**
     * ---------------------------------------------------------------
     * Métodos CRUD
     * ---------------------------------------------------------------
     */

    /* -------------------------- Create/Update ----------------------------- */

    /**
     * TiposBrindesRedesTable::saveTiposBrindesRedes()
     *
     * Salva um tipo de Brinde. Atualiza se informar o Id
     *
     * @param array $tipoBrindesRedes
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 31/05/2018
     *
     * @return \App\Model\Entity\TiposBrindesRede $tipoBrindesRedes Objeto gravado
     */
    public function saveTiposBrindesRedes(
        int $redesId,
        string $nome,
        bool $equipamentoRti,
        bool $brindeNecessidadesEspeciais,
        bool $habilitado,
        bool $atribuirAutomatico,
        string $tipoPrincipalCodigoBrindeDefault = null,
        string $tipoSecundarioCodigoBrindeDefault = null,
        int $id = null
    ) {
        try {
            $tipoBrindesRedesSave = null;

            // Atualiza se o id está setado. Novo se id = null
            if (!empty($id) && isset($id) && $id > 0) {
                $tipoBrindesRedesSave = $this->_getTiposBrindesRedesTable()->find('all')
                    ->where(array("id" => $id))->first();
            } else {
                $tipoBrindesRedesSave = $this->_getTiposBrindesRedesTable()->newEntity();
            }

            $tipoBrindesRedesSave->nome = $nome;
            $tipoBrindesRedesSave->redes_id = $redesId;
            $tipoBrindesRedesSave->equipamento_rti = $equipamentoRti;
            $tipoBrindesRedesSave->brinde_necessidades_especiais = $brindeNecessidadesEspeciais;
            $tipoBrindesRedesSave->habilitado = $habilitado;
            $tipoBrindesRedesSave->atribuir_automatico = $atribuirAutomatico;
            $tipoBrindesRedesSave->tipo_principal_codigo_brinde_default = $tipoPrincipalCodigoBrindeDefault;
            $tipoBrindesRedesSave->tipo_secundario_codigo_brinde_default = $tipoSecundarioCodigoBrindeDefault;

            return $this->save($tipoBrindesRedesSave);
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao salvar tipo de brindes: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
        }
    }

    public function updateStateTiposBrindesRedesById(int $id, int $estado)
    {
        try {
            $tipoBrindeRede = $this->getTiposBrindesRedeById($id);
            $tipoBrindeRede["habilitado"] = $estado;

            return $this->save($tipoBrindeRede);
        } catch (\Exception $e) {

            throw new \Exception($e->getMessage());
        }
    }

    /* -------------------------- Read  ----------------------------- */

    /**
     * TiposBrindesRedesTable::findTiposBrindesRedes()
     *
     * Obtem tipo de Brindes conforme condições passadas
     *
     * @param array $whereConditions Condições de Where
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 31/05/2018
     *
     * @return \App\Model\Entity\TiposBrindesRede[] $tipoBrindesRedes Objetos da consulta
     */
    public function findTiposBrindesRedes(array $whereConditions = array(), int $limit = null)
    {
        try {
            $tipoBrindesRedes = $this->_getTiposBrindesRedesTable()->find("all")
                ->where($whereConditions)
                ->contain("TiposBrindesClientes");

            if (!empty($limit) && isset($limit)) {
                if ($limit == 1) {
                    $tipoBrindesRedes = $tipoBrindesRedes->first();
                } else {
                    $tipoBrindesRedes = $tipoBrindesRedes->limit($limit);
                }
            } else {
                $tipoBrindesRedes = $tipoBrindesRedes->limit(999);
            }
            return $tipoBrindesRedes;
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao obter tipo de brindes: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write('error', $trace);
        }
    }
    /**
     * TiposBrindesRedesTable::findTiposBrindesRedesByIds()
     *
     * Obtem tipo de Brindes conforme condições passadas
     *
     * @param array $clientesIds Ids de clientes
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 28/06/2018
     *
     * @return \App\Model\Entity\TiposBrindesRede[] $tipoBrindesRedes Objetos da consulta
     */
    public function findTiposBrindesRedesByIds(array $ids = array(), array $orderConditions = array(), array $paginationConditions = array())
    {
        try {
            $whereConditions = array(
                "id in " => $ids,
                "habilitado" => 1
            );

            $tipoBrindesRedesQuery = $this->_getTiposBrindesRedesTable()->find("all")
                ->where($whereConditions)
                ->select(
                    array(
                        "id",
                        "nome",
                        "equipamento_rti",
                        "brinde_necessidades_especiais",
                        "habilitado",
                    )
                );

            $tipoBrindesRedesTodos = $tipoBrindesRedesQuery->toArray();
            $tipoBrindesRedesAtual = $tipoBrindesRedesQuery->toArray();

            $retorno = $this->prepareReturnDataPagination($tipoBrindesRedesTodos, $tipoBrindesRedesAtual, "tipos_brindes", $paginationConditions);

            if ($retorno["mensagem"]["status"] == 0) {
                return $retorno;
            }

            if (sizeof($orderConditions) > 0) {
                $tipoBrindesRedesQuery = $tipoBrindesRedesQuery->order($orderConditions);
            }

            if (sizeof($paginationConditions) > 0) {
                $tipoBrindesRedesQuery = $tipoBrindesRedesQuery->limit($paginationConditions["limit"])
                    ->page($paginationConditions["page"]);
            }

            $tipoBrindesRedesAtual = $tipoBrindesRedesQuery->toArray();

            $retorno = $this->prepareReturnDataPagination($tipoBrindesRedesTodos, $tipoBrindesRedesAtual, "tipos_brindes", $paginationConditions);

            return $retorno;
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao obter tipo de brindes: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);

            $error = ['success' => false, 'message' => $stringError];
            return $error;
        }
    }

    /**
     * TiposBrindesRedesTable::findTiposBrindesRedesAtribuirAutomaticamente
     *
     * Procura todos os tipos de Brindes que estão habilitados e que podem
     * atribuir automaticamente para novas unidades de Redes
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date   26/06/2018
     *
     * @return \App\Model\Entity\TiposBrindesRede[] $array de tipos
     */
    public function findTiposBrindesRedesAtribuirAutomaticamente(int $redesId)
    {
        try {
            $tipoBrindesRedes = $this->_getTiposBrindesRedesTable()
                ->find('all')
                ->where(
                    array(
                        "redes_id" => $redesId,
                        "habilitado" => 1,
                        "atribuir_automatico" => 1,
                        "tipo_principal_codigo_brinde_default IS NOT NULL",
                        "tipo_secundario_codigo_brinde_default IS NOT NULL"
                    )
                )->toArray();

            return $tipoBrindesRedes;
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao obter tipo de brindes: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write('error', $trace);
        }
    }

    /**
     * TiposBrindesRedesTable::getTiposBrindesRedeById
     *
     * Obtem o tipo de brinde da rede pelo id
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since   26/06/2018
     *
     * @return \App\Model\Entity\TiposBrindesRede $tiposBrindesRede
     */
    public function getTiposBrindesRedeById(int $id)
    {
        try {
            $tipoBrindesRedes = $this
                ->find()
                ->where(
                    array(
                        "TiposBrindesRedes.id" => $id
                    )
                )
                ->contain("Rede")
                ->first();

            return $tipoBrindesRedes;
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao obter tipo de brindes: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write('error', $trace);
        }
    }


    #region Delete 

    /**
     * TiposBrindesRedesTable::deleteAllTiposBrindesRedesByRedesId
     * 
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2018-11-25
     * 
     * Remove todos os tipos brindes redes por um redes id
     *
     * @param integer $redesId Id da rede 
     * @return boolean registro removido
     */
    public function deleteAllTiposBrindesRedesByRedesId(int $redesId)
    {
        try {
            return $this->deleteAll(array("redes_id" => $redesId));
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = sprintf("Erro ao remover registros: %s. [Função: %s / Arquivo: %s / Linha: %s]. ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
            Log::write("error", $stringError);
            Log::write("error", $trace);
        }
    }

    #endregion
}
