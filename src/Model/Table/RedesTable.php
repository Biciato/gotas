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

/**
 * Redes Model
 *
 * @method \App\Model\Entity\Rede get($primaryKey, $options = [])
 * @method \App\Model\Entity\Rede newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Rede[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Rede|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Rede patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Rede[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Rede findOrCreate($search, callable $callback = null, $options = [])
 */
class RedesTable extends GenericTable
{

    /**
     * -------------------------------------------------------------
     * Fields
     * -------------------------------------------------------------
     */
    protected $redes_table = null;

    /**
     * -------------------------------------------------------------
     * Properties
     * -------------------------------------------------------------
     */

    /**
     * Method get of Redes table property
     *
     * @return Cake\ORM\Table Table object
     */
    private function _getRedesTable()
    {
        if (is_null($this->redes_table)) {
            $this->_setRedesTable();
        }
        return $this->redes_table;
    }

    /**
     * Method set of Redes table property
     *
     * @return void
     */
    private function _setRedesTable()
    {
        $this->redes_table = TableRegistry::get('Redes');
    }

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     *
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('redes');
        $this->setDisplayField('nome_rede');
        $this->setPrimaryKey('id');

        $this->hasMany(
            'RedesHasClientes',
            [
                'className' => 'RedesHasClientes',
                'foreignKey' => 'redes_id',
                'join' => 'INNER'

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
            ->requirePresence('nome_rede', 'create')
            ->notEmpty('nome_rede');

        $validator
            ->boolean('ativado')
            ->allowEmpty('ativado');

        $validator
            ->decimal("custo_referencia_gotas")
            ->notEmpty("custo_referencia_gotas");

        $validator
            ->integer("media_assiduidade_clientes")
            ->notEmpty("media_assiduidade_clientes");

        $validator
            ->integer("quantidade_pontuacoes_usuarios_dia")
            ->notEmpty("quantidade_pontuacoes_usuarios_dia");

        $validator
            ->integer("quantidade_consumo_usuarios_dia")
            ->notEmpty("quantidade_consumo_usuarios_dia");

        $validator
            ->dateTime('audit_insert')
            ->allowEmpty('audit_insert');

        $validator
            ->dateTime('audit_update')
            ->allowEmpty('audit_update');

        return $validator;
    }

    /**
     * -------------------------------------------------------------
     * Methods
     * -------------------------------------------------------------
     */

    #region Create

    /**
     * Adiciona uma rede
     *
     * @param \App\Model\Entity\Rede $rede Objeto Rede
     *
     * @return bool
     */
    public function addRede(\App\Model\Entity\Rede $rede)
    {
        try {
            return $this->save($rede);
        } catch (\Exception $e) {
            // @todo gustavosg Corrigir log
            $trace = $e->getTrace();
            $object = null;

            foreach ($trace as $key => $item_trace) {
                if ($item_trace['class'] == 'Cake\Database\Query') {
                    $object = $item_trace;
                    break;
                }
            }

            $stringError = __("Erro ao adicionar registro: {0}, em {1}", $e->getMessage(), $object['file']);

            Log::write('error', $stringError);

            $error = ['success' => false, 'message' => $stringError];
            return $error;
        }
    }

    #endregion

    #region Read

    /**
     * RedesTable::findRedesByName
     *
     * Procura Redes por nome
     *
     * @param string $nomeRede Nome da rede
     * @param integer $qteRegistros Qte de Registros
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2018-08-12
     *
     * @return Cake\ORM\Query Registros contendo redes
     */
    public function findRedesByName(string $nomeRede, int $qteRegistros = null)
    {
        try {
            $options = array(
                "conditions" => array(
                    "nome_rede like '%$nomeRede%'"
                )

            );
            $redes = $this->find("all", $options);

            if ($qteRegistros > 0) {
                $redes = $redes->limit($qteRegistros);
            }

            return $redes;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao realizar pesquisa: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write('error', $trace);

        }
    }

    public function getClientesFromRedes(int $id = 0, string $nomeRede = "", bool $ativado = true, int $tempoExpiracaoGotasUsuarios = 6, int $quantidadePontuacoesUsuariosDia = 3, int $quantidadeConsumoUsuariosDia = 10, float $custoReferenciaGotas = 0.05, int $mediaAssiduidadeClientes = 2, array $selectFields = array())
    {

        $whereCondicoes = array();

        if (!empty($id)) {
            $whereCondicoes["id"] = $id;
        }

        if (!empty($nomeRede)){
            $whereCondicoes["nome_rede"] = $nomeRede;
        }

        // todo @gustavosg WIP


        $clientes = $this->find("all")
            ->where(array());
    }

    /**
     * RedesTable::getRedesList
     * Retorna uma lista de Redes para Select
     *
     * @param array $whereConditions Lista de condições
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 2018/05/13
     *
     * @return \App\Model\Entity\Redes ['id', 'nome']
     */
    public function getRedesList(int $id = null, string $nomeRede = null, int $ativado = null, bool $permiteConsumoGotasFuncionarios = null, int $tempoExpiracaoGotasUsuarios = null, int $quantidadePontuacoesUsuariosDia = null, int $mediaAssiduidadeClientes = null)
    {
        try {

            $whereConditions = array();

            if (strlen($id) > 0 && $id > 0) {
                $whereConditions[] = array("Redes.id" => $id);
            }

            if (!empty($nomeRede)) {
                $whereConditions[] = array("Redes.nome_rede like '%{$nomeRede}%'");
            }

            if (strlen($ativado) > 0) {
                $whereConditions[] = array("Redes.ativado" => $ativado);
            }

            if (strlen($tempoExpiracaoGotasUsuarios) > 0) {
                $whereConditions[] = array("Redes.tempo_expiracao_gotas_usuarios" => $tempoExpiracaoGotasUsuarios);
            }
            if (strlen($quantidadePontuacoesUsuariosDia) > 0) {
                $whereConditions[] = array("Redes.quantidade_pontuacoes_usuarios_dia" => $quantidadePontuacoesUsuariosDia);
            }

            if (strlen($mediaAssiduidadeClientes) > 0) {
                $whereConditions[] = array("Redes.media_assiduidade_clientes" => $mediaAssiduidadeClientes);
            }

            return $this->_getRedesTable()->find('list')
                ->where($whereConditions)
                ->select(['id', 'nome_rede'])
                ->order(array("nome_rede" => "asc"));
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

            $error = ['success' => false, 'message' => $stringError];
            return $error;
        }
    }

    /**
     * Obtem todas as redes
     *
     * @param string $queryType        Tipo de Query
     * @param array  $whereConditions Condições extras
     *
     * @return \App\Entity\Model\Redes $redes[] Lista de Redes
     */
    public function getAllRedes(string $queryType = null, array $whereConditions = [], bool $withAssociations = true)
    {
        try {

            $conditions = [];

            foreach ($whereConditions as $key => $value) {
                array_push($conditions, [$key => $value]);
            }

            $query = isset($queryType) ? $queryType : 'all';

            $redes = $this->_getRedesTable()->find($query)
                ->where($conditions);

            if ($withAssociations) {
                $redes = $redes->contain(
                    [
                        'RedesHasClientes',
                        'RedesHasClientes.RedesHasClientesAdministradores',
                        'RedesHasClientes.Clientes'
                    ]
                );
            }

            return $redes;
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

            $error = ['success' => false, 'message' => $stringError];
            return $error;
        }
    }

    /**
     * Obtem todas as redes Conforme condições
     *
     * @param array $whereConditions      Condições de where
     * @param array $associations         Lista de Associações
     * @param array $orderConditions      Condições de Ordenação
     * @param array $paginationConditions Condições de paginação
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @data   2018/05/13
     *
     * @return array("count", "data") \App\Entity\Model\Redes $redes[] Lista de Redes
     */
    public function getRedes(array $whereConditions = [], array $selectFields = array(), array $associations = [], array $orderConditions = [], array $paginationConditions = [])
    {
        try {

            $conditions = [];

            foreach ($whereConditions as $key => $value) {
                // array_push($conditions, $value);
                array_push($conditions, [$key => $value]);
            }

            $redesQuery = $this->_getRedesTable()->find('all')
                ->where($conditions);

            if (sizeof($selectFields) > 0) {
                $redesQuery = $redesQuery->select($selectFields);
            }

            $redesTodas = $redesQuery->toArray();
            $redesAtual = $redesQuery->toArray();

            $retorno = $this->prepareReturnDataPagination($redesTodas, $redesAtual, "redes", $paginationConditions);

            if ($retorno["mensagem"]["status"] == 0) {
                return $retorno;
            }

            if (sizeof($orderConditions) > 0) {
                $redesQuery = $redesQuery->order($orderConditions);
            }

            if (sizeof($paginationConditions) > 0) {
                $redesQuery = $redesQuery->limit($paginationConditions["limit"])
                    ->page($paginationConditions["page"]);
            }

            $redesAtual = $redesQuery->toArray();

            return $this->prepareReturnDataPagination($redesTodas, $redesAtual, "redes", $paginationConditions);
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

            $error = ['success' => false, 'message' => $stringError];
            return $error;
        }
    }

    /**
     * Obtêm rede por id
     *
     * @param int $id Id da Rede
     *
     * @return \App\Model\Entity\Rede
     */
    public function getRedeById(int $id)
    {
        try {
            return $this->find('all')
                ->where(array('id' => $id))
                ->contain(
                    array(
                        'RedesHasClientes',
                        'RedesHasClientes.RedesHasClientesAdministradores',
                        'RedesHasClientes.Clientes.ClientesHasBrindesHabilitados.Brindes'
                    )
                )
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

            $error = ['success' => false, 'message' => $stringError];

            throw new \Exception($stringError);

            return $error;
        }
    }

    #endregion

    #region Update

    /**
     * Troca estado de unidade
     *
     * @param int  $id      Id de RedesHasClientes
     * @param bool $ativado Estado de ativação
     *
     * @return \App\Model\Entity\Clientes $rede
     */
    public function changeStateEnabledRede(int $id, bool $ativado)
    {
        try {

            $rede = $this->_getRedesTable()->find('all')
                ->where(['id' => $id])
                ->contain(['RedesHasClientes'])
                ->first();

            $clientes_ids = [];

            foreach ($rede->redes_has_clientes as $key => $value) {
                array_push($clientes_ids, $value->clientes_id);
            }

            // troca o estado dos registros pertencentes à uma rede
            $clientes_table = TableRegistry::get('Clientes');

            if (sizeof($clientes_ids) > 0) {
                $clientes_table->updateAll(
                    [
                        'ativado' => $ativado
                    ],
                    [
                        'id IN ' => $clientes_ids
                    ]
                );
            }

            $rede->ativado = $ativado;

            return $this->_getRedesTable()->save($rede);

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
     * Atualiza dados de rede
     *
     * @param \App\Model\Entity\Redes $rede Objeto de rede
     *
     * @return \App\Model\Entity\Redes Objeto de redes atualizado
     */
    public function updateRede(\App\Model\Entity\Rede $rede)
    {
        try {

            return $this->_getRedesTable()->save($rede);

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

    #endregion

    #region Delete

    /**
     * Remove uma rede
     *
     * @param int $id Id da Rede
     *
     * @return boolean
     */
    public function deleteRedesById(int $id)
    {
        try {

            return
                $this->_getRedesTable()->deleteAll(['id' => $id]);

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
