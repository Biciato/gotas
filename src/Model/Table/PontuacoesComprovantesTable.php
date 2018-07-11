<?php
namespace App\Model\Table;

use ArrayObject;
use App\View\Helper;
use App\Controller\AppController;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Aura\Intl\Exception;
use App\Custom\RTI\DebugUtil;

/**
 * PontuacoesComprovantes Model
 *
 * @property \App\Model\Table\ClientesTable|\Cake\ORM\Association\BelongsTo $Clientes
 * @property \App\Model\Table\UsuariosTable|\Cake\ORM\Association\BelongsTo $Usuarios
 * @property \App\Model\Table\PontuacoesTable|\Cake\ORM\Association\HasMany $Pontuacoes
 *
 * @method \App\Model\Entity\PontuacoesComprovante get($primaryKey, $options = [])
 * @method \App\Model\Entity\PontuacoesComprovante newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\PontuacoesComprovante[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\PontuacoesComprovante|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\PontuacoesComprovante patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\PontuacoesComprovante[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\PontuacoesComprovante findOrCreate($search, callable $callback = null, $options = [])
 */
class PontuacoesComprovantesTable extends GenericTable
{

    /**
     * -------------------------------------------------------------
     * Fields
     * -------------------------------------------------------------
     */
    protected $pontuacoesComprovantesTable = null;

    protected $pontuacoesComprovantesQuery = null;


    /**
     * -------------------------------------------------------------
     * Properties
     * -------------------------------------------------------------
     */

    /**
     * Method get of pontuacoesComprovantes table property
     *
     * @return Cake\ORM\Table Table object
     */
    private function _getPontuacoesComprovantesTable()
    {
        if (is_null($this->pontuacoesComprovantesTable)) {
            $this->_setPontuacoesComprovantesTable();
        }
        return $this->pontuacoesComprovantesTable;
    }

    /**
     * Method set of pontuacoesComprovantes table property
     *
     * @return void
     */
    private function _setPontuacoesComprovantesTable()
    {
        $this->pontuacoesComprovantesTable = TableRegistry::get('PontuacoesComprovantes');
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

        $this->setTable('pontuacoes_comprovantes');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Clientes', [
            'foreignKey' => 'clientes_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Usuarios', [
            'foreignKey' => 'usuarios_id',
            'joinType' => 'INNER'
        ]);

        $this->belongsTo('Funcionarios', [
            'className' => 'Usuarios',
            'joinType' => 'INNER',
            'foreignKey' => 'funcionarios_id'
        ]);
        $this->hasMany('Pontuacoes', [
            'joinType' => 'INNER',
            'foreignKey' => 'pontuacoes_comprovante_id'
        ]);

        $this->hasMany(
            'SomaPontuacoes',
            [
                'className' => 'Pontuacoes',
                'joinType' => 'INNER',
                'foreignKey' => 'pontuacoes_comprovante_id',
            ]
        );

        $this->hasMany(
            'DescritivoPontuacoes',
            [
                'className' => 'Pontuacoes',
                'joinType' => 'INNER',
                'foreignKey' => 'pontuacoes_comprovante_id'
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
            ->notEmpty('clientes_id');

        $validator
            ->notEmpty('usuarios_id');

        $validator
            ->notEmpty('funcionarios_id');

        $validator
            ->allowEmpty('conteudo');

        $validator
            ->requirePresence('nome_img', 'create')
            ->allowEmpty('nome_img');

        $validator
            ->allowEmpty('chave_nfe');

        $validator
            ->notEmpty('estado_nfe');

        $validator
            ->integer('requer_auditoria')
            ->notEmpty('requer_auditoria');

        $validator
            ->integer('auditado')
            ->notEmpty('auditado');

        $validator
            ->dateTime('data')
            ->notEmpty('data');

        $validator
            ->integer('registro_invalido')
            ->notEmpty('registro_invalido');

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
        $rules->add($rules->existsIn(['clientes_id'], 'Clientes'));
        $rules->add($rules->existsIn(['usuarios_id'], 'Usuarios'));

        return $rules;
    }

    /**
     * -------------------------------------------------------------
     * Methods
     * -------------------------------------------------------------
     */

    /* ------------------------ Create ------------------------ */

    /**
     * Guarda registro de Pontuacao Comprovante
     *
     * @param int $clientes_id
     * @param int $usuarios_id
     * @param int $funcionarios_id
     * @param string $conteudo
     * @param string $chave_nfe
     * @param string $estado_nfe
     * @param date $data
     * @param bool $requer_auditoria
     * @param bool $auditado
     * @return object $pontuacoes_comprovantes
     */
    public function addPontuacaoComprovanteCupom(
        int $clientes_id,
        int $usuarios_id,
        int $funcionarios_id,
        string $conteudo,
        string $chave_nfe,
        string $estado_nfe,
        $data,
        bool $requer_auditoria,
        bool $auditado
    ) {

        try {
            $pontuacoes_comprovantes = $this->_getPontuacoesComprovantesTable()->newEntity();
            $pontuacoes_comprovantes['clientes_id'] = $clientes_id;
            $pontuacoes_comprovantes['usuarios_id'] = $usuarios_id;
            $pontuacoes_comprovantes['funcionarios_id'] = $funcionarios_id;
            $pontuacoes_comprovantes['conteudo'] = $conteudo;
            $pontuacoes_comprovantes['chave_nfe'] = $chave_nfe;
            $pontuacoes_comprovantes['estado_nfe'] = $estado_nfe;
            $pontuacoes_comprovantes['data'] = $data;
            $pontuacoes_comprovantes['requer_auditoria'] = $requer_auditoria;
            $pontuacoes_comprovantes['auditado'] = $auditado;

            return $this->_getPontuacoesComprovantesTable()->save($pontuacoes_comprovantes);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao criar registro: {0} em {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /* ------------------------ Read ------------------------ */

    /**
     * Procura por cupom previamente inserido
     *
     * @param string $chave_nfe   Chave da NFE
     * @param string $estado_nfe  Estado da NFE
     * @param int    $clientes_id Id de Cliente
     *
     * @return object Registro de cupom | null
     */
    public function findCouponByKey(string $chave_nfe, string $estado_nfe, int $clientes_id = null)
    {
        try {
            $conditions = ['chave_nfe' => $chave_nfe, 'estado_nfe' => $estado_nfe];

            if (strtolower($estado_nfe) == 'mg') {
                array_push($conditions, ['clientes_id' => $clientes_id]);
            }

            return $this->_getPontuacoesComprovantesTable()->find('all')
                ->where($conditions)->first();
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao obter registro: {0} em {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Gera novo nome de recibo para gravar no servidor
     *
     * @return string nome para gravar
     */
    public function generateNewImageCoupon()
    {
        try {
            $proceed = false;
            $record = null;

            $keyname = bin2hex(openssl_random_pseudo_bytes(32)) . '.jpg';

            while ($proceed == false) {
                $record = $this->_getPontuacoesComprovantesTable()->find('all')->where(['nome_img' => $keyname])->first();

                // sai do loop se nome disponível, gera novo nome se já existe
                if (is_null($record)) {
                    $proceed = true;
                } else {
                    $keyname = bin2hex(openssl_random_pseudo_bytes(32)) . '.jpg';
                }
            }

            $keyname = substr($keyname, 0, sizeof($keyname) - 5);

            return $keyname;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao obter registro: {0} em {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Obtem todas as pontuacoes conforme parâmetros
     *
     * @param array $where_conditions Condições de pesquisa
     * @param array $order_conditions Condições de ordem
     *
     * @return void
     */
    public function getPontuacoesComprovantes(array $where_conditions = [], array $order_conditions = [])
    {
        try {
            return $this->_getPontuacoesComprovantesTable()->find('all')
                ->where($where_conditions)
                ->order($order_conditions)
                ->contain(
                    array(
                        'Pontuacoes.Gotas',
                        'Clientes',
                        'SomaPontuacoes',
                        'Usuarios',
                        'Funcionarios'
                    )
                );
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringExplode = implode(";", $trace);

            $stringError = __("Erro ao realizar pesquisa de PontuacoesComprovantes: {0}. [Função: {1} / Arquivo: {2} / Linha: {3} / Errors: ]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__, $stringExplode);

            Log::write('error', $stringError);
            Log::write('error', $trace);

            return $stringError;
        }
    }

    /**
     * PontuacoesComprovantesTable::getPontuacoesComprovantesUsuario
     *
     * Consulta genérica com filtro, ordenação e paginação
     *
     * @param Type $var
     * @return void
     */
    public function getPontuacoesComprovantesUsuario(
        int $usuariosId,
        int $redesId = null,
        array $clientesIds = array(),
        string $chaveNFE = null,
        string $estadoNFE = null,
        string $dataInicio = null,
        string $dataFim = null,
        array $orderConditions = array(),
        $paginationConditions = array()
    ) {

        try {

            // Verifica se foi informado Rede ou clientes ids
            $redesHasClientesTable = TableRegistry::get("RedesHasClientes");

            // Se informar a Rede, irá pesquisar todas as unidades de uma rede.
            if ((!empty($redesId)) && ($redesId > 0)) {
                $clientesIds = $redesHasClientesTable->getClientesIdsFromRedesHasClientes($redesId);
            }

            // Condições básicas de pesquisa
            $whereConditions = array(
                "usuarios_id" => $usuariosId,
            // Só irá retornar os dados válidos
                "registro_invalido" => 0
            );

            // Se informou numeração da Chave da NFE
            if ((!empty($chaveNFE)) && (strlen($chaveNFE) > 0)) {
                $whereConditions[] = array("chave_nfe like '%{$chaveNFE}%'");
            }

            // Se informou estado
            if ((!empty($estadoNFE)) && (strlen($estadoNFE) > 0)) {
                $whereConditions[] = array("estado_nfe" => $estadoNFE);
            }

            // Adiciona os Ids de clientes se pesquisa for por rede ou por unidades
            if (sizeof($clientesIds) > 0) {
                $whereConditions[] = array(
                    "clientes_id in " => $clientesIds
                );
            }

            // condições para datas
            if (!is_null($dataInicio) && !is_null($dataFim)) {
                $whereConditions[] = array("data BETWEEN '{$dataInicio}' AND '{$dataFim}'");
            } else if (!is_null($dataInicio)) {
                $whereConditions[] = array("data >=" => $dataInicio);
            } else if (!is_null($dataFim)) {
                $whereConditions[] = array("data <=" => $dataFim);
            } else {
                // Data não está setada, procura pelos últimos 30 dias
                $dataFim = date("Y-m-d");
                $dataInicio = date('Y-m-d', strtotime("-30 days"));

                $whereConditions[] = array("data BETWEEN '{$dataInicio}' AND '{$dataFim}'");
            }

            $pontuacoesComprovantesQuery = $this->_getPontuacoesComprovantesTable()->find('all')
                ->where($whereConditions)
                ->contain(
                    array(
                        'Pontuacoes.Gotas',
                        'Clientes',
                        'Funcionarios'
                    )
                );

            $pontuacoesComprovantesTodas = $pontuacoesComprovantesQuery->toArray();
            $pontuacoesComprovantesAtual = $pontuacoesComprovantesQuery->toArray();

            $retorno = $this->prepareReturnDataPagination($pontuacoesComprovantesTodas, $pontuacoesComprovantesAtual, "pontuacoes_comprovantes", $paginationConditions);

            if ($retorno["mensagem"]["status"] == 0) {
                return $retorno;
            }

            $novaOrderConditions = array();
            foreach ($orderConditions as $key => $order) {
                $novaOrderConditions["PontuacoesComprovantes." . $key] = $order;
            }

            $orderConditions = $novaOrderConditions;

            if (sizeof($orderConditions) > 0) {
                $pontuacoesComprovantesQuery = $pontuacoesComprovantesQuery->order($orderConditions);
            }

            if (sizeof($paginationConditions) > 0) {
                $pontuacoesComprovantesQuery = $pontuacoesComprovantesQuery->limit($paginationConditions["limit"])
                    ->page($paginationConditions["page"]);
            }

            $pontuacoesComprovantesAtual = $pontuacoesComprovantesQuery->toArray();

            $retorno = $this->prepareReturnDataPagination($pontuacoesComprovantesTodas, $pontuacoesComprovantesAtual, "pontuacoes_comprovantes", $paginationConditions);

            /**
             * A pesquisa de pontuação deverá retornar as seguintes condições:
             * 1 - Se não foi filtrado por redes ou clientesIds, o total de pontos não poderá ser calculado;
             * 2 - Se informado o id de rede, ou informado uma ou mais unidades de rede, deve retornar um array conforme a seguinte estrutura:
             * 2.1 - $pontos("total_pontos_rede", "total_pontos_pagina_atual")
             */

            $totalPontosRede = 0;
            $totalPontosPaginaAtual = 0;
            $pontuacoesTodas = array();
            $pontuacoesPaginaAtual = array();

            if (sizeof($pontuacoesComprovantesTodas) > 0) {
                foreach ($pontuacoesComprovantesTodas as $key => $pontuacoesComprovantes) {
                    foreach ($pontuacoesComprovantes["pontuacoes"] as $key => $pontuacaoComprovante) {
                        $pontuacoesTodas[] = $pontuacaoComprovante;
                    }
                }
            }

            if (sizeof($pontuacoesComprovantesAtual) > 0) {
                foreach ($pontuacoesComprovantesAtual as $key => $pontuacoesComprovantes) {
                    foreach ($pontuacoesComprovantes["pontuacoes"] as $key => $pontuacaoComprovante) {
                        $pontuacoesPaginaAtual[] = $pontuacaoComprovante;
                    }
                }
            }
            if (($redesId > 0) || sizeof($clientesIds) > 0) {
                foreach ($pontuacoesTodas as $pontuacao) {
                    $totalPontosRede += $pontuacao["quantidade_gotas"];
                }
                foreach ($pontuacoesPaginaAtual as $pontuacao) {
                    $totalPontosPaginaAtual += $pontuacao["quantidade_gotas"];
                }
            }

            $retorno["pontuacoes_comprovantes"]["data"]["soma_pontuacoes"] = array(
                "total_pontos_rede" => $totalPontosRede,
                "total_pontos_pagina_atual" => $totalPontosPaginaAtual,
            );

            return $retorno;
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao buscar dados de pontuações dos comprovantes do usuário: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write('error', $trace);
        }
    }

    /**
     * Obtem todas as pontuacoes conforme parâmetros
     *
     * @param array $where_conditions Condições de pesquisa
     * @param array $order_conditions Condições de ordem
     *
     * @return void
     */
    public function getAllCouponsForUsuarioInClientes(int $usuarios_id, array $clientes_ids, array $order_conditions = [])
    {
        try {

            $where_conditions[] = ['usuarios_id' => $usuarios_id];
            $where_conditions[] = ['clientes_id in ' => $clientes_ids];

            return $this->_getPontuacoesComprovantesTable()->find('all')
                ->where($where_conditions)
                ->order($order_conditions)
                ->contain([
                    'Pontuacoes',
                    'Clientes',
                    'Funcionarios',
                    'SomaPontuacoes'
                ]);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao obter registro: {0}", $e->getMessage());

            Log::write('error', $stringError);
            Log::write('error', $trace);

            return $stringError;
        }
    }

    /**
     * Retorna lista de ids de todas as unidades onde o usuário final possui pontuação
     *
     * @param array $where_conditions Condições de pesquisa
     *
     * @return array[] Lista de Ids
     */
    public function getAllClientesIdFromCoupons(array $where_conditions)
    {
        try {
            return $this->_getPontuacoesComprovantesTable()->find('all')
                ->where($where_conditions)
                ->distinct(['clientes_id'])
                ->select(['clientes_id']);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao obter registro: {0} em {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Obtêm todos os cupons inseridos manualmente por um funcionário
     * dentro do período especificado
     *
     * @param int    $funcionarios_id   Id do funcionário
     * @param string $start_date        Data de início
     * @param string $end_date          Data de fim
     * @param bool   $requer_auditoria  Registro requer auditoria
     * @param bool   $auditado          Registro já auditado
     *
     * @return array Lista de Ids
     */
    public function getAllCouponsIdByWorkerId(int $funcionarios_id, string $start_date = null, string $end_date = null, bool $requer_auditoria = null, bool $auditado = null)
    {
        try {
            $conditions = [];

            array_push($conditions, ['funcionarios_id' => $funcionarios_id]);

            if (!is_null($start_date) && !is_null($end_date)) {
                array_push(
                    $conditions,
                    [
                        'data between "' . $start_date . '" and "' . $end_date . '"'
                    ]
                );
            } elseif (!is_null($start_date)) {
                array_push($conditions, ['date >= ' => $start_date]);
            } else {
                array_push($conditions, ['date <= ' => $end_date]);
            }

            if (!is_null($requer_auditoria)) {
                array_push($conditions, ['requer_auditoria' => $requer_auditoria]);
            }

            if (!is_null($auditado)) {
                array_push($conditions, ['auditado' => $auditado]);
            }

            $result = $this->_getPontuacoesComprovantesTable()->find('all')->where($conditions)->select('id');

            return $result;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao obter registro: {0} em {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Obtêm comprovante coupon por Id
     *
     * @param int $id Id do Comprovante
     *
     * @return object $cupom
     */
    public function getCouponById(int $id)
    {
        try {
            $data = $this->_getPontuacoesComprovantesTable()
                ->find('all')
                ->where(
                    [
                        'PontuacoesComprovantes.id' => $id
                    ]
                )

                ->contain(
                    [
                        'Clientes',
                        'Usuarios',
                        'Funcionarios',
                        'Pontuacoes.Gotas',
                        'SomaPontuacoes' => [

                            'queryBuilder' => function ($q) {
                                $query = $q->find('all');

                                $query = $query->select(['soma_quantidade' => $query->func()->sum('quantidade_gotas'), 'pontuacoes_comprovante_id']);

                                return $query;
                            },
                        ]
                    ]
                )->first();


            if (isset($data['soma_pontuacoes'][0])) {
                $data['soma_pontuacoes'] = $data['soma_pontuacoes'][0]['soma_quantidade'];
            }
            // debug($data);

            return $data;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao obter registro: {0} em {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Obtem cupons por id de cliente
     *
     * @param array $clientes_ids        Ids dos clientes
     * @param array $options             Opções extras
     *
     * @return array $result lista de cupons
     */
    public function getCouponsByClienteId(array $clientes_ids, array $options = null)
    {
        try {
            $result = $this->_getPontuacoesComprovantesTable()->find('all')->where(
                ['PontuacoesComprovantes.clientes_id in' => $clientes_ids]
            )->contain(
                [
                    'Clientes',
                    'Usuarios',
                    'Funcionarios'

                ]
            );

            if (!is_null($options)) {
                $result = $result->where($options);
            }

            return $result;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao obter registro: {0} em {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Obtêm detalhes de cupom por Id
     *
     * @param int $id Id da pontuação
     *
     * @return object cupom com detalhes
     */
    public function getDetalhesCupomByCouponId(int $id)
    {
        try {
            $value = $this->_getPontuacoesComprovantesTable()->find('all')
                ->where(
                    [
                        'PontuacoesComprovantes.id' => $id
                    ]
                )
                ->contain(
                    [
                        'Clientes',
                        'Usuarios',
                        'Funcionarios',
                        'Pontuacoes.Gotas',

                        'SomaPontuacoes' => [
                            'queryBuilder' => function ($q) {
                                $query = $q->find('all');

                                $query = $query->select(['soma_quantidade' => $query->func()->sum('quantidade_gotas'), 'pontuacoes_comprovante_id']);

                                return $query;
                            }
                        ]
                    ]
                )->first();

            $value['soma_pontuacoes'] = $value['soma_pontuacoes'][0]['soma_quantidade'];


            return $value;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao obter registro: {0} em {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /* ------------------------ Update ------------------------ */

    /**
     * Configura comprovante de pontuação como aprovado
     *
     * @param int $id Id da pontuação comprovante
     *
     * @return void
     */
    public function setPontuacaoComprovanteApprovedById(int $id)
    {
        try {
            $pontuacao_comprovante = $this->_getPontuacoesComprovantesTable()->get($id);

            $pontuacao_comprovante->auditado = true;

            return $this->_getPontuacoesComprovantesTable()->save($pontuacao_comprovante);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao obter registro: {0} em {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Aloca todas as pontuacoes para uma matriz
     *
     * @param int $clientes_id Id de Cliente
     * @param int $matriz_id   Id da Matriz
     *
     * @return \App\Model\Entity\Pontuacoes $array[]
     *  lista de pontuacoes pendentes
     */
    public function setPontuacoesComprovantesToMainCliente(int $clientes_id, int $matriz_id)
    {
        try {
            return $this->updateAll(
                [
                    'clientes_id' => $matriz_id
                ],
                [
                    'clientes_id' => $clientes_id
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

            $stringError = __("Erro ao obter registro: {0}, em {1}", $e->getMessage(), $object['file']);

            Log::write('error', $stringError);

            return ['success' => false, 'message' => $stringError];
        }
    }

    /**
     * Configura comprovante de pontuação conforme status informado
     *
     * @param int  $id     Id da pontuação
     * @param bool $status Estado da validade da pontuação
     *
     * @return object $comprovante_pontuacao atualizado
     */
    public function setPontuacaoComprovanteValidStatusById(int $id, bool $status)
    {
        try {
            $pontuacao_comprovante = $this->_getPontuacoesComprovantesTable()->get($id);

            $pontuacao_comprovante->registro_invalido = $status;

            return $this->_getPontuacoesComprovantesTable()->save($pontuacao_comprovante);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao obter registro: {0} em {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Atualiza o usuário definido para uma pontuação
     *
     * @param int $pontuacao_id Id da Pontuacao
     * @param int $usuarios_id  Id de Usuário
     *
     * @return void
     */
    public function setUsuarioForPontuacaoComprovanteById(int $pontuacao_id, int $usuarios_id)
    {
        try {
            $pontuacao_comprovante
                = $this->_getPontuacoesComprovantesTable()->get($pontuacao_id);

            $pontuacao_comprovante->usuarios_id = $usuarios_id;

            return $this->_getPontuacoesComprovantesTable()->save($pontuacao_comprovante);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao atualizar registro: {0} em {1} ", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Atualiza todos os comprovantes de pontuacoes conforme argumentos
     *
     * @param array $fields     Campos contendo atualização
     * @param array $conditions Condições
     *
     * @return bool
     */
    public function updateAllPontuacoesComprovantes(array $fields, array $conditions)
    {
        try {
            return $this->updateAll($fields, $conditions);
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

    /* ------------------------ Delete ------------------------ */

    /**
     * Remove todos os comprovantes de pontuacoes por Id de Cliente
     *
     * @param array $clientes_ids Ids de Clientes
     *
     * @return \App\Model\Entity\PontuacoesComprovantes $array[]
     *  lista de pontuacoes pendentes
     */
    public function deleteAllPontuacoesComprovantesByClientesIds(array $clientes_ids)
    {
        try {
            return $this->deleteAll(['clientes_id in ' => $clientes_ids]);

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
     * Remove todos os comprovantes de pontuacoes por Id de Cliente
     *
     * @param int $usuarios_id Ids de Usuarios
     *
     * @return \App\Model\Entity\PontuacoesComprovantes $array[]
     *  lista de pontuacoes pendentes
     */
    public function deleteAllPontuacoesComprovantesByUsuariosId(int $usuarios_id)
    {
        try {
            return $this->deleteAll(['usuarios_id ' => $usuarios_id]);

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
}
