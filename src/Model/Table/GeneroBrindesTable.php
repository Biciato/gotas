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
 * GeneroBrindes Model
 *
 * @property \App\Model\Table\ClientesTable|\Cake\ORM\Association\BelongsToMany $Clientes
 *
 * @method \App\Model\Entity\GeneroBrinde get($primaryKey, $options = [])
 * @method \App\Model\Entity\GeneroBrinde newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\GeneroBrinde[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\GeneroBrinde|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\GeneroBrinde patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\GeneroBrinde[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\GeneroBrinde findOrCreate($search, callable $callback = null, $options = [])
 */
class GeneroBrindesTable extends GenericTable
{

    /**
     * -------------------------------------------------------------
     * Fields
     * -------------------------------------------------------------
     */
    protected $generoBrindeTable = null;

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
    private function _getGeneroBrindeTable()
    {
        if (is_null($this->generoBrindeTable)) {
            $this->_setGeneroBrindeTable();
        }
        return $this->generoBrindeTable;
    }

    /**
     * Method set of brinde table property
     *
     * @return void
     */
    private function _setGeneroBrindeTable()
    {
        $this->generoBrindeTable = TableRegistry::get('GeneroBrindes');
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

        $this->setTable('genero_brindes');
        $this->setDisplayField('nome_necessidades_especiais');
        $this->setPrimaryKey('id');

        $this->belongsToMany('Clientes', [
            'foreignKey' => 'genero_brindes_id',
            'targetForeignKey' => 'clientes_id',
            'joinTable' => 'genero_brindes_clientes'
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
            ->requirePresence('nome', 'create')
            ->notEmpty('nome');

        $validator
            ->boolean('equipamento_rti')
            ->requirePresence('equipamento_rti', 'create')
            ->notEmpty('equipamento_rti');

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
            ->integer('tipo_principal_codigo_brinde_default')
            ->requirePresence('tipo_principal_codigo_brinde_default', 'create')
            ->allowEmpty('tipo_principal_codigo_brinde_default');

        $validator
            ->integer('tipo_secundario_codigo_brinde_default')
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
     * GeneroBrindesTable::saveGeneroBrindes()
     *
     * Salva um Gênero de Brinde. Atualiza se informar o Id
     *
     * @param array $generoBrindes
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 31/05/2018
     *
     * @return \App\Model\Entity\GeneroBrinde $generoBrindes Objeto gravado
     */
    public function saveGeneroBrindes(array $generoBrindes = array())
    {
        try {
            $generoBrindesSave = null;

            // Atualiza se o id está setado. Novo se id = null
            if (!empty($generoBrindes["id"]) && isset($generoBrindes["id"]) && $generoBrindes["id"] > 0) {
                $generoBrindesSave = $this->_getGeneroBrindeTable()->find('all')
                    ->where(["id" => $generoBrindes["id"]])->first();
            } else {
                $generoBrindesSave = $this->_getGeneroBrindeTable()->newEntity();
            }

            $generoBrindesSave->nome = $generoBrindes["nome"];
            $generoBrindesSave->equipamento_rti = $generoBrindes["equipamento_rti"];
            $generoBrindesSave->brinde_necessidades_especiais = $generoBrindes["brinde_necessidades_especiais"];
            $generoBrindesSave->habilitado = $generoBrindes["habilitado"];
            $generoBrindesSave->atribuir_automatico = $generoBrindes["atribuir_automatico"];
            $generoBrindesSave->tipo_principal_codigo_brinde_default = !empty($generoBrindes["tipo_principal_codigo_brinde_default"]) ? $generoBrindes["tipo_principal_codigo_brinde_default"] : null;
            $generoBrindesSave->tipo_secundario_codigo_brinde_default = !empty($generoBrindes["tipo_secundario_codigo_brinde_default"]) ? $generoBrindes["tipo_secundario_codigo_brinde_default"] : null;

            return $this->_getGeneroBrindeTable()->save($generoBrindesSave);
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao salvar gênero de brindes: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
        }
    }

    /* -------------------------- Read  ----------------------------- */

    /**
     * GeneroBrindesTable::findGeneroBrindes()
     *
     * Obtem Gênero de Brindes conforme condições passadas
     *
     * @param array $whereConditions Condições de Where
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 31/05/2018
     *
     * @return \App\Model\Entity\GeneroBrinde[] $generoBrindes Objetos da consulta
     */
    public function findGeneroBrindes(array $whereConditions = array(), int $limit = null)
    {
        try {
            $generoBrindes = $this->_getGeneroBrindeTable()->find("all")
                ->where($whereConditions);

            if (!empty($limit) && isset($limit)) {
                if ($limit == 1) {
                    $generoBrindes = $generoBrindes->first();
                } else {
                    $generoBrindes = $generoBrindes->limit($limit);
                }
            } else {
                $generoBrindes = $generoBrindes->limit(999);
            }
            return $generoBrindes;
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao obter gênero de brindes: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
        }
    }
    /**
     * GeneroBrindesTable::findGeneroBrindesClientes()
     *
     * Obtem Gênero de Brindes conforme condições passadas
     *
     * @param array $clientesIds Ids de clientes
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 28/06/2018
     *
     * @return \App\Model\Entity\GeneroBrinde[] $generoBrindes Objetos da consulta
     */
    public function findGeneroBrindesByIds(array $ids = array(), array $orderConditions = array(), array $paginationConditions = array())
    {
        try {
            $whereConditions = array(
                "id in " => $ids,
                "habilitado" => 1
            );

            $generoBrindesQuery = $this->_getGeneroBrindeTable()->find("all")
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

            $generoBrindesTodos = $generoBrindesQuery->toArray();
            $generoBrindesAtual = $generoBrindesQuery->toArray();

            $retorno = $this->prepareReturnDataPagination($generoBrindesTodos, $generoBrindesAtual, "genero_brindes", $paginationConditions);

            if ($retorno["mensagem"]["status"] == 0) {
                return $retorno;
            }

            if (sizeof($orderConditions) > 0) {
                $generoBrindesQuery = $generoBrindesQuery->order($orderConditions);
            }

            if (sizeof($paginationConditions) > 0) {
                $generoBrindesQuery = $generoBrindesQuery->limit($paginationConditions["limit"])
                    ->page($paginationConditions["page"]);
            }

            $generoBrindesAtual = $generoBrindesQuery->toArray();

            $retorno = $this->prepareReturnDataPagination($generoBrindesTodos, $generoBrindesAtual, "genero_brindes", $paginationConditions);

            return $retorno;
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao obter gênero de brindes: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);

            $error = ['success' => false, 'message' => $stringError];
            return $error;
        }
    }

    /**
     * GeneroBrindesTable::findGeneroBrindesAtribuirAutomaticamente
     *
     * Procura todos os Gêneros de Brindes que estão habilitados e que podem
     * atribuir automaticamente para novas unidades de Redes
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date   26/06/2018
     *
     * @return \App\Model\Entity\GeneroBrinde[] $array de gêneros
     */
    public function findGeneroBrindesAtribuirAutomaticamente()
    {
        try {
            $generoBrindes = $this->_getGeneroBrindeTable()
                ->find('all')
                ->where(
                    array(
                        "habilitado" => 1,
                        "atribuir_automatico" => 1,
                        "tipo_principal_codigo_brinde_default IS NOT NULL",
                        "tipo_secundario_codigo_brinde_default IS NOT NULL"
                    )
                )->toArray();

            return $generoBrindes;
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao obter gênero de brindes: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
        }
    }


}
