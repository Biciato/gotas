<?php

namespace App\Model\Table;

use ArrayObject;
use App\View\Helper;
use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Database\Exception as CakeDatabaseException;
use Cake\Log\Log;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use App\Custom\RTI\DebugUtil;
use App\Custom\RTI\ResponseUtil;
use Cake\Database\Expression\QueryExpression;
use \DateTime;
use \Exception;
use \Throwable;

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
            array(
                'className' => 'RedesHasClientes',
                'foreignKey' => 'redes_id',
                'join' => 'LEFT'
            )
        );

        $this->hasMany(
            "CategoriasBrindes",
            [
                "className" => "CategoriasBrindes",
                "foreignKey" => "redes_id",
                "join" => "LEFT"
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
            ->integer("qte_mesmo_brinde_resgate_dia")
            ->notEmpty("qte_mesmo_brinde_resgate_dia");

        $validator
            ->integer("qte_gotas_minima_bonificacao")
            ->allowEmpty("qte_gotas_minima_bonificacao");

        $validator
            ->integer("qte_gotas_bonificacao")
            ->allowEmpty("qte_gotas_bonificacao");

        $validator
            ->boolean("pontuacao_extra_produto_generico");

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

    #region Save

    /**
     * Adiciona uma rede
     *
     * @param \App\Model\Entity\Rede $rede Objeto Rede
     *
     * @deprecated 1.2.3 Utilizar method saveUpdate()
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

    /**
     * Salva um registro
     *
     * @param RedesCpfListaNegra $record Entidade
     * @return RedesCpfListaNegra $record Entidade salva com Id
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.1.8
     */
    public function saveUpdate(\App\Model\Entity\Rede $record)
    {
        try {
            return $this->save($record);
        } catch (Throwable $th) {
            $message = sprintf("[%s] %s", MSG_SAVED_EXCEPTION, $th->getMessage());
            Log::write("error", $message);
            throw new Exception($message, MSG_SAVED_EXCEPTION_CODE);
        }
    }

    #endregion

    #region Read

    /**
     * RedesTable::getRedeByImage
     *
     * Verifica se já tem imagem cadastrada para a rede com a string informada
     *
     * @author Gustavo Souza Gonçalves  <gustavosouzagoncalves@outlook.com>
     * @since 2019-07-20
     *
     * @param string $propagandaImg Imagem de Propaganda
     *
     * @return int Id
     */
    public function getRedeByImage(string $propagandaImg)
    {
        try {
            return $this->find("all")
                ->where(array("propaganda_img" => $propagandaImg))
                ->select("Redes.id")
                ->first();
        } catch (Exception $e) {
            Log::write("error", sprintf("[%s] %s", MSG_LOAD_EXCEPTION, $e->getMessage()));

            throw new Exception($e->getMessage());
        }
    }

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
            $stringError = __("Erro ao realizar pesquisa: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
        }
    }

    /**
     * Obtem Lista de Clientes pela rede
     *
     * @param integer $id Id da Rede
     * @param string $nomeRede Nome da Rede
     * @param boolean $ativado Rede Ativada / Desativada
     * @param integer $tempoExpiracaoGotasUsuarios
     * @param integer $quantidadePontuacoesUsuariosDia
     * @param integer $quantidadeConsumoUsuariosDia
     * @param float $custoReferenciaGotas
     * @param integer $mediaAssiduidadeClientes
     * @param array $selectFields
     * @return void
     */
    public function getClientesFromRedes(int $id = 0, string $nomeRede = "", bool $ativado = true, int $tempoExpiracaoGotasUsuarios = 6, int $quantidadePontuacoesUsuariosDia = 3, int $quantidadeConsumoUsuariosDia = 10, float $custoReferenciaGotas = 0.05, int $mediaAssiduidadeClientes = 2, array $selectFields = array())
    {

        $whereCondicoes = array();

        if (!empty($id)) {
            $whereCondicoes["id"] = $id;
        }

        if (!empty($nomeRede)) {
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

            return $this->find('list')
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
    public function getAllRedes(string $queryType = null, array $whereConditions = null, bool $withAssociations = true)
    {
        try {

            $conditions = [];

            if (isset($whereConditions)) {
                foreach ($whereConditions as $key => $value) {
                    array_push($conditions, [$key => $value]);
                }
            }

            $query = isset($queryType) ? $queryType : 'all';

            $redes = $this->find($query)
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

            $redes = $redes->order([
                "Redes.nome_rede" => "ASC"
            ]);

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
     * Realiza pesquisa de Redes
     *
     * @param integer $id
     * @param string $nomeRede
     * @param boolean $ativado
     * @param integer $tempoExpiracaoGotasUsuariosMin
     * @param integer $tempoExpiracaoGotasUsuariosMax
     * @param integer $quantidadePontuacoesUsuariosDiaMin
     * @param integer $quantidadePontuacoesUsuariosDiaMax
     * @param integer $quantidadeConsumoUsuariosDiaMin
     * @param integer $quantidadeConsumoUsuariosDiaMax
     * @param integer $qteMesmoBrindeResgateDiaMin
     * @param integer $qteMesmoBrindeResgateDiaMax
     * @param integer $qteGotasMinimaBonificacaoMin
     * @param integer $qteGotasMinimaBonificacaoMax
     * @param integer $qteGotasBonificacaoMin
     * @param integer $qteGotasBonificacaoMax
     * @param float $custoReferenciaGotasMin
     * @param float $custoReferenciaGotasMax
     * @param integer $mediaAssiduidadeClientesMin
     * @param integer $mediaAssiduidadeClientesMax
     * @param boolean $appPersonalizado
     * @param boolean $msgDistanciaCompraBrinde
     * @param boolean $pontuacaoExtraProdutoGenerico
     * @param DateTime $dateCreatedMin
     * @param DateTime $dateCreatedMax
     * @return \Cake\Orm\Query|\App\Model\Entity\Rede[]
     */
    public function getRedes(
        int $id = null,
        string $nomeRede = null,
        bool $ativado = null,
        int $tempoExpiracaoGotasUsuariosMin = null,
        int $tempoExpiracaoGotasUsuariosMax = null,
        int $quantidadePontuacoesUsuariosDiaMin = null,
        int $quantidadePontuacoesUsuariosDiaMax = null,
        int $quantidadeConsumoUsuariosDiaMin = null,
        int $quantidadeConsumoUsuariosDiaMax = null,
        int $qteMesmoBrindeResgateDiaMin = null,
        int $qteMesmoBrindeResgateDiaMax = null,
        int $qteGotasMinimaBonificacaoMin = null,
        int $qteGotasMinimaBonificacaoMax = null,
        int $qteGotasBonificacaoMin = null,
        int $qteGotasBonificacaoMax = null,
        float $custoReferenciaGotasMin = null,
        float $custoReferenciaGotasMax = null,
        int $mediaAssiduidadeClientesMin = null,
        int $mediaAssiduidadeClientesMax = null,
        bool $appPersonalizado = null,
        bool $msgDistanciaCompraBrinde = null,
        bool $pontuacaoExtraProdutoGenerico = null,
        DateTime $dateCreatedMin = null,
        DateTime $dateCreatedMax = null,
        bool $bringAssociatedData = false
    ) {
        try {
            $where = function (QueryExpression $exp) use (
                $id,
                $nomeRede,
                $ativado,
                $tempoExpiracaoGotasUsuariosMin,
                $tempoExpiracaoGotasUsuariosMax,
                $quantidadePontuacoesUsuariosDiaMin,
                $quantidadePontuacoesUsuariosDiaMax,
                $quantidadeConsumoUsuariosDiaMin,
                $quantidadeConsumoUsuariosDiaMax,
                $qteMesmoBrindeResgateDiaMin,
                $qteMesmoBrindeResgateDiaMax,
                $qteGotasMinimaBonificacaoMin,
                $qteGotasMinimaBonificacaoMax,
                $qteGotasBonificacaoMin,
                $qteGotasBonificacaoMax,
                $custoReferenciaGotasMin,
                $custoReferenciaGotasMax,
                $mediaAssiduidadeClientesMin,
                $mediaAssiduidadeClientesMax,
                $appPersonalizado,
                $msgDistanciaCompraBrinde,
                $pontuacaoExtraProdutoGenerico,
                $dateCreatedMin,
                $dateCreatedMax
            ) {
                if (!empty($id)) {
                    $exp->eq("Redes.id", $id);
                } else {
                    if (!empty($nomeRede)) {
                        $exp->like("Redes.nome_rede", sprintf("%%%s%%", $nomeRede));
                    }

                    if (isset($ativado)) {
                        $exp->eq("Redes.ativado", $ativado);
                    } else {
                        $exp->isNotNull("Redes.ativado");
                    }

                    if (!empty($tempoExpiracaoGotasUsuariosMin)) {
                        $exp->gte("Redes.tempo_expiracao_gotas_usuario", $tempoExpiracaoGotasUsuariosMin);
                    }

                    if (!empty($tempoExpiracaoGotasUsuariosMax)) {
                        $exp->lte("Redes.tempo_expiracao_gotas_usuario", $tempoExpiracaoGotasUsuariosMax);
                    }

                    if (!empty($quantidadePontuacoesUsuariosDiaMin)) {
                        $exp->gte("Redes.quantidade_pontuacoes_usuarios_dia", $quantidadePontuacoesUsuariosDiaMin);
                    }

                    if (!empty($quantidadePontuacoesUsuariosDiaMax)) {
                        $exp->lte("Redes.quantidade_pontuacoes_usuarios_dia", $quantidadePontuacoesUsuariosDiaMax);
                    }

                    if (!empty($quantidadeConsumoUsuariosDiaMin)) {
                        $exp->gte("Redes.quantidade_consumo_usuarios_dia", $quantidadeConsumoUsuariosDiaMin);
                    }

                    if (!empty($quantidadeConsumoUsuariosDiaMax)) {
                        $exp->lte("Redes.quantidade_consumo_usuarios_dia", $quantidadeConsumoUsuariosDiaMax);
                    }

                    if (!empty($qteMesmoBrindeResgateDiaMin)) {
                        $exp->gte("Redes.qte_mesmo_brinde_resgate_dia", $qteMesmoBrindeResgateDiaMin);
                    }

                    if (!empty($qteMesmoBrindeResgateDiaMax)) {
                        $exp->lte("Redes.qte_mesmo_brinde_resgate_dia", $qteMesmoBrindeResgateDiaMax);
                    }

                    if (!empty($qteGotasMinimaBonificacaoMin)) {
                        $exp->lte("Redes.qte_gotas_minima_bonificacao", $qteGotasMinimaBonificacaoMin);
                    }

                    if (!empty($qteGotasMinimaBonificacaoMax)) {
                        $exp->gte("Redes.qte_gotas_minima_bonificacao", $qteGotasMinimaBonificacaoMax);
                    }

                    if (!empty($qteGotasBonificacaoMin)) {
                        $exp->lte("Redes.qte_gotas_bonificacao", $qteGotasBonificacaoMin);
                    }

                    if (!empty($qteGotasBonificacaoMax)) {
                        $exp->gte("Redes.qte_gotas_bonificacao", $qteGotasBonificacaoMax);
                    }

                    if (!empty($custoReferenciaGotasMin)) {
                        $exp->lte("Redes.custo_referencia_gotas", $custoReferenciaGotasMin);
                    }

                    if (!empty($custoReferenciaGotasMax)) {
                        $exp->gte("Redes.custo_referencia_gotas", $custoReferenciaGotasMax);
                    }

                    if (!empty($mediaAssiduidadeClientesMin)) {
                        $exp->lte("Redes.media_assiduidade_clientes", $mediaAssiduidadeClientesMin);
                    }

                    if (!empty($mediaAssiduidadeClientesMax)) {
                        $exp->gte("Redes.media_assiduidade_clientes", $mediaAssiduidadeClientesMax);
                    }

                    if (is_bool($appPersonalizado)) {
                        $exp->eq("Redes.app_personalizado", $appPersonalizado);
                    }

                    if (isset($msgDistanciaCompraBrinde)) {
                        $exp->eq("Redes.msg_distancia_compra_brinde", $msgDistanciaCompraBrinde);
                    }

                    if (isset($pontuacaoExtraProdutoGenerico)) {
                        $exp->eq("Redes.pontuacao_extra_produto_generico", $pontuacaoExtraProdutoGenerico);
                    }

                    /**
                     * @TODO Nota: deve ser criado um novo campo na tabela Redes.data_criacao,
                     * pois o audit_insert é apenas para auditoria
                     * No momento, ele não é usado nas consultas
                     */

                    if (!empty($dateCreatedMin)) {
                        $exp->gte("Redes.audit_insert", $dateCreatedMin);
                    }

                    if (!empty($dateCreatedMax)) {
                        $exp->lte("Redes.audit_insert", $dateCreatedMax);
                    }
                }

                return $exp;
            };

            $query = $this->find("all")
                ->where($where);

            if ($bringAssociatedData) {
                $query = $query->contain(["RedesHasClientes.Clientes"]);
            }

            Log::write("info", $query->sql());
            return $query;
        } catch (Throwable $th) {
            $codeError = $th->getCode();
            $message = sprintf("[{%s} %s] %s", $codeError, MSG_LOAD_EXCEPTION, $th->getMessage());
            Log::write("error", $message);
            throw new CakeDatabaseException($message, $codeError);
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
    public function getRedesForMobileAPI(array $whereConditions = [], array $selectFields = array(), array $associations = [], array $orderConditions = [], array $paginationConditions = [])
    {
        try {

            $conditions = [];

            foreach ($whereConditions as $key => $value) {
                // array_push($conditions, $value);
                array_push($conditions, [$key => $value]);
            }

            $redesQuery = $this->find('all')
                ->where($conditions);

            if (sizeof($selectFields) > 0) {
                $redesQuery = $redesQuery->select($selectFields);
            }

            $redesTodas = $redesQuery->toArray();
            $redesAtual = $redesQuery->toArray();

            $retorno = ResponseUtil::prepareReturnDataPagination($redesTodas, $redesAtual, "redes", $paginationConditions);

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

            return ResponseUtil::prepareReturnDataPagination($redesTodas, $redesAtual, "redes", $paginationConditions);
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
     * RedesTable::getRedesHabilitadas()
     *
     * Obtem informações de Redes Habilitadas, e suas respectivas unidades
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-02-06
     *
     * @return App\Model\Entity\Redes[]
     */
    public function getRedesHabilitadas()
    {
        try {
            $whereArray = array("Redes.ativado" => 1);
            $joinArray = array(
                "RedesHasClientes" => array(
                    "type" => "left",
                    "table" => "redes_has_clientes",
                    "conditions" => "Redes.id = RedesHasClientes.redes_id"
                ),
                "Clientes" => array(
                    "type" => "left",
                    "alias" => "clientes",
                    "table" => "clientes",
                    "conditions" => "RedesHasClientes.clientes_id = clientes.id"
                )
            );

            $selectArray = array(
                "id",
                "nome_rede",
                "tempo_expiracao_gotas_usuarios",
                "Clientes.id",
                "Clientes.nome_fantasia"
            );

            $redes = $this
                ->find("all")
                ->where($whereArray)
                ->join($joinArray)
                ->select($selectArray)
                ->all();

            return $redes;
        } catch (\Exception $e) {
            $stringError = __("Erro ao obter registros: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write('error', $e->getTraceAsString());
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
                        'RedesHasClientes.Clientes.Brindes'
                    )
                )
                ->first();
        } catch (\Exception $e) {
            $trace = $e->getTraceAsString();
            $stringError = __("Erro ao obter dados: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write('error', $trace);
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
    public function changeStateNetwork(int $id)
    {
        try {
            $rede = $this->find('all')
                ->where(['id' => $id])
                ->contain(['RedesHasClientes'])
                ->first();

            $clientesIds = [];
            $ativado = !$rede->ativado;

            foreach ($rede->redes_has_clientes as $key => $value) {
                $clientesIds[] = $value["clientes_id"];
            }

            // troca o estado dos registros pertencentes à uma rede
            $clientes_table = TableRegistry::get('Clientes');

            if (sizeof($clientesIds) > 0) {
                $clientes_table->updateAll(
                    array('ativado' => $ativado),
                    array('id IN ' => $clientesIds)
                );
            }

            $rede->ativado = $ativado;

            return $this->save($rede);
        } catch (Throwable $th) {
            $message = sprintf("[%s] %s", MSG_LOAD_EXCEPTION, $th->getMessage());
            Log::write("error", $message);
            throw new Exception($message, $th->getCode());
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
            return $this->deleteAll(['id' => $id]);
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
