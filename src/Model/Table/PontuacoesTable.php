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
 * Pontuacoes Model
 *
 * @property \App\Model\Table\UsuariosTable|\Cake\ORM\Association\BelongsTo $Usuarios
 * @property \App\Model\Table\BrindesHabilitadosTable|\Cake\ORM\Association\BelongsTo $BrindesHabilitados
 * @property \App\Model\Table\GotasTable|\Cake\ORM\Association\BelongsTo $Gotas
 *
 * @method \App\Model\Entity\Pontuaco get($primaryKey, $options = [])
 * @method \App\Model\Entity\Pontuaco newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Pontuaco[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Pontuaco|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Pontuaco patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Pontuaco[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Pontuaco findOrCreate($search, callable $callback = null, $options = [])
 */
class PontuacoesTable extends GenericTable
{
    /**
     * -------------------------------------------------------------
     * Fields
     * -------------------------------------------------------------
     */
    protected $pontuacoesTable = null;

    /**
     * -------------------------------------------------------------
     * Properties
     * -------------------------------------------------------------
     */

    /**
     * Method get of pontuacoes table property
     *
     * @return Cake\ORM\Table Table object
     */
    private function _getPontuacoesTable()
    {
        if (is_null($this->pontuacoesTable)) {
            $this->_setPontuacoesTable();
        }
        return $this->pontuacoesTable;
    }

    /**
     * Method set of pontuacoes table property
     *
     * @return void
     */
    private function _setPontuacoesTable()
    {
        $this->pontuacoesTable = TableRegistry::get('Pontuacoes');
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

        $this->setTable('pontuacoes');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo(
            'Clientes',
            [
                'foreignKey' => 'clientes_id',
                'joinType' => 'INNER'
            ]
        );

        $this->belongsTo(
            'Usuarios',
            [
                'foreignKey' => 'usuarios_id',
                'joinType' => 'INNER'
            ]
        );

        $this->belongsTo(
            'BrindesHabilitados',
            [
                'foreignKey' => 'brindes_habilitados_id',
                'joinType' => 'INNER'
            ]
        );

        $this->belongsTo(
            'Gotas',
            [
                'foreignKey' => 'gotas_id',
                'joinType' => 'INNER'
            ]
        );

        $this->belongsTo(
            'ClientesHasBrindesHabilitados',
            [
                'foreignKey' => 'clientes_has_brindes_habilitados_id'
            ]
        );

        $this->belongsTo(
            'PontuacoesComprovantes',
            [
                'foreignKey' => 'pontuacoes_comprovante_id',
            ]
        );

        $this->belongsTo(
            'PontuacoesAprovadas',
            [
                'className' => 'PontuacoesComprovantes',
                'foreignKey' => 'pontuacoes_comprovante_id',
                'joinType' => 'INNER',
                'conditions' => [
                    'PontuacoesAprovadas.registro_invalido' => 0
                ]
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
            ->decimal('quantidade')
            ->requirePresence('quantidade', 'create')
            ->notEmpty('quantidade');

        $validator
            ->dateTime('data')
            ->requirePresence('data', 'create')
            ->notEmpty('data');

        /**
         * 0 = não usado
         * 1 = parcialmente usado
         * 2 = totalmente usado
         */
        $validator
            ->integer('utilizado');

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
        $rules->add($rules->existsIn(['usuarios_id'], 'Usuarios'));
        $rules->add($rules->existsIn(['clientes_id'], 'Clientes'));
        $rules->add($rules->existsIn(['clientes_has_brindes_habilitados_id'], 'ClientesHasBrindesHabilitados'));
        $rules->add($rules->existsIn(['gotas_id'], 'Gotas'));

        return $rules;
    }

    /**
     * -------------------------------------------------------------
     * Methods
     * -------------------------------------------------------------
     */

    /* ------------------------ Create ------------------------ */

    /**
     * Adiciona pontuação de brinde para usuário
     *
     * @param int   $clientes_id             Id de cliente
     * @param int   $usuarios_id             Id de usuário
     * @param int   $brindes_habilitados_id  Id do brinde habilitado
     * @param float $total_pontuacao_debitar Quantidade de pontos
     * @param int   $funcionariosId          Id de funcionário (opcional)
     *
     * @return boolean Resultado de inserção
     */
    public function addPontuacoesBrindesForUsuario(
        int $clientes_id,
        int $usuarios_id,
        int $brindes_habilitados_id,
        float $total_pontuacao_debitar,
        int $funcionariosId = null
    ) {
        try {
            $pontuacao = $this->_getPontuacoesTable()->newEntity();

            $pontuacao->clientes_id = $clientes_id;
            $pontuacao->usuarios_id = $usuarios_id;
            $pontuacao->clientes_has_brindes_habilitados_id = $brindes_habilitados_id;
            $pontuacao->quantidade_gotas = $total_pontuacao_debitar;
            $pontuacao->utilizado = Configure::read('dropletsUsageStatus')['FullyUsed'];
            $pontuacao->data = date('Y-m-d H:i:s');
            $pontuacao->funcionarios_id = $funcionariosId;

            return $this->_getPontuacoesTable()->save($pontuacao);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao editar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);
            Log::write("error", $trace);
        }
    }

    /**
     * Guarda registro de Pontuacao
     * Se $auto_save = false, retorna objeto para gravar posteriormente em batch
     *
     * @param int    $clientes_id               Id de cliente
     * @param int    $usuarios_id               Id de usuário
     * @param int    $funcionarios_id           Id de funcionário (usuário)
     * @param int    $gotas_id                  Id da gota
     * @param float  $quantidade_multiplicador  Quantidade de multiplicador
     * @param float  $quantidade_gotas          Quantidade de gotas
     * @param int    $pontuacoes_comprovante_id Id do comprovante da pontuação
     * @param string $data                      Data de processamento
     *
     * @return object $pontuacao
     */
    public function addPontuacaoCupom(int $clientes_id, int $usuarios_id, int $funcionarios_id, int $gotas_id, float $quantidade_multiplicador, float $quantidade_gotas, int $pontuacoes_comprovante_id, string $data)
    {
        try {
            $pontuacao = $this->_getPontuacoesTable()->newEntity();

            $pontuacao->clientes_id = $clientes_id;
            $pontuacao->usuarios_id = $usuarios_id;
            $pontuacao->funcionarios_id = $funcionarios_id;
            $pontuacao->quantidade_multiplicador = $quantidade_multiplicador;
            $pontuacao->quantidade_gotas = $quantidade_gotas;
            $pontuacao->gotas_id = $gotas_id;
            $pontuacao->pontuacoes_comprovante_id = $pontuacoes_comprovante_id;
            $pontuacao->data = $data;
            $pontuacao->expirado = false;

            return $this->_getPontuacoesTable()->save($pontuacao);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao editar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);
            Log::write("error", $trace);
        }
    }

    /* ------------------------ Read ------------------------ */

    /**
     * Obtêm pontuação por Id
     *
     * @param int $pontuacoes_id Id de pontuação
     *
     * @return App\Model\Entity\Pontuaco $pontuacao Pontuação
     */
    public function getPontuacaoById(int $pontuacoes_id)
    {
        try {
            $pontuacao = $this->_getPontuacoesTable()->find('all')
                ->where(['Pontuacoes.id' => $pontuacoes_id])
                ->contain('PontuacoesComprovantes')
                ->first();

            return $pontuacao;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);
            Log::write("error", $trace);
        }
    }

    /**
     * Obtem todas as pontuações por um cliente Id
     *
     * @param int $clientes_id Id de Cliente
     *
     * @return \App\Model\Entity\Pontuacoes $pontuacoes[] lista de pontuacoes
     */
    public function getPontuacoesByClienteId(int $clientes_id)
    {
        try {
            return $this->_getPontuacoesTable()->find('all')
                ->where(['clientes_id' => $clientes_id])
                ->contain(['PontuacoesComprovantes']);

        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Obtêm pontuações de usuário
     *
     * @param int   $usuarios_id       Id de usu´rio
     * @param array $array_clientes_id Array de Clientes Id
     * @param bool  $registro_invalido Tipos de registros inválidos (false = válidos / true = inválidos)
     *
     * @return array $pontuacoes
     **/
    public function getPontuacoesOfUsuario(int $usuarios_id, array $array_clientes_id, bool $registro_invalido)
    {
        try {
            /*
             * primeiro pega todas as pontuações que não tem id de comprovante
             * pois são pontuações já 'aprovadas' pelo qr code ou venda
             */

            /**
             * No caso de registro inválido, só deve trazer a quantidade de pontos invalidados
             * Um registro inválido significa um registro 'invalidado' por um administrador
             * como, por exemplo, quando um cupom é negado pelo administrador quando inserido
             * manualmente por um funcionário (não bate com o cupom fiscal da SEFAZ)
             */

            // pega os ids de pontuações inválidas
            $pontuacoes_invalidas = $this->_getPontuacoesTable()->PontuacoesComprovantes->find('all')
                ->where(
                    [
                        'usuarios_id' => $usuarios_id,
                        'clientes_id  in ' => $array_clientes_id,
                        'registro_invalido' => true
                    ]
                )
                ->select('id');

            $pontuacoes_invalidas_array = [];

            if (sizeof($pontuacoes_invalidas->toArray()) > 0) {
                foreach ($pontuacoes_invalidas->toArray() as $key => $value) {
                    $pontuacoes_invalidas_array[] = $value['id'];
                }
            }

            if ($registro_invalido) {
                if (sizeof($pontuacoes_invalidas_array) > 0) {
                    $pontuacoes
                        = $this->_getPontuacoesTable()->find('all')
                        ->where(
                            [
                                'Pontuacoes.usuarios_id' => $usuarios_id,
                                'Pontuacoes.clientes_id in ' => $array_clientes_id,
                                'Pontuacoes.pontuacoes_comprovante_id in'
                                    => $pontuacoes_invalidas_array
                            ]
                        );
                } else {
                    $pontuacoes
                        = $this->_getPontuacoesTable()->find('all')
                        ->where(
                            [
                                'Pontuacoes.usuarios_id' => $usuarios_id,
                                'Pontuacoes.clientes_id in ' => $array_clientes_id

                            ]
                        );
                }
            } else {
                // no caso de registro válidos, deve trazer a quantidade
                // de pontos validados mais a quantidade de pontos debitados

                if (sizeof($pontuacoes_invalidas_array) > 0) {
                    $or_condition = [
                        'OR' =>
                            [
                            'Pontuacoes.pontuacoes_comprovante_id not in'
                                => $pontuacoes_invalidas_array,
                            'Pontuacoes.pontuacoes_comprovante_id is null'

                        ]
                    ];
                }

                $conditions
                    = [
                    'Pontuacoes.usuarios_id' => $usuarios_id,
                    'Pontuacoes.clientes_id in ' => $array_clientes_id,
                ];

                if (sizeof($pontuacoes_invalidas_array) > 0) {
                    array_push($conditions, [$or_condition]);
                }

                $pontuacoes
                    = $this->_getPontuacoesTable()->find('all')
                    ->where($conditions)
                    ->contain(['Gotas']);
            }
            return $pontuacoes->toArray();
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);
            Log::write("error", $trace);
        }
    }

    /**
     * PontuacoesTable::getPontuacoesPendentesForUsuario
     *
     * Obtêm pontuações pendentes de uso para usuário
     *
     * @param int   $usuarios_id          Id de usuário
     * @param array $clientes_id          Array Id de cliente
     * @param int   $how_many             Quantidade de registros à retornar
     * @param int   $ultimo_id_processado Ultimo Id Processado
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 2018/05/14
     *
     * @return array @pontuacoes Lista de pontuações
     */
    public function getPontuacoesPendentesForUsuario(int $usuarios_id, array $clientes_id = [], int $how_many = 10, int $ultimo_id_processado = null)
    {
        try {
            $conditions = [];

            $pontuacoes_comprovantes_invalidated
                = $this->_getPontuacoesTable()->PontuacoesComprovantes
                ->find('all')
                ->select(['id'])
                ->where(
                    [
                        'usuarios_id' => $usuarios_id,
                        'clientes_id IN ' => $clientes_id,
                        'registro_invalido' => true
                    ]
                )->toArray();


            $pontuacoes_comprovantes_invalidated_ids = [];
            foreach ($pontuacoes_comprovantes_invalidated as $key => $value) {
                array_push($pontuacoes_comprovantes_invalidated_ids, $value['id']);
            }

            if (sizeof($pontuacoes_comprovantes_invalidated_ids) > 0) {
                array_push(
                    $conditions,
                    [
                        'OR' =>
                            [
                            'pontuacoes_comprovante_id NOT IN ' => $pontuacoes_comprovantes_invalidated_ids,
                            'pontuacoes_comprovante_id IS NULL'
                        ]
                    ]
                );
            }

            array_push($conditions, ['clientes_id IN ' => $clientes_id]);
            array_push($conditions, ['usuarios_id' => $usuarios_id]);
            array_push($conditions, ['utilizado != ' => Configure::read('dropletsUsageStatus')['FullyUsed']]);

            if (!is_null($ultimo_id_processado)) {
                array_push($conditions, ['id >= ' => (int)$ultimo_id_processado]);
            }

            $result = $this->_getPontuacoesTable()
                ->find('all')
                ->select(['id', 'quantidade_gotas', 'utilizado'])
                ->where($conditions)
                ->order(['id' => 'asc'])
                ->limit($how_many);

            if ($how_many == 1) {
                $result = $result->first();
            }

            return $result;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);
            Log::write("error", $trace);
        }
    }

    /**
     * Obtem soma de Pontos por comprovante
     *
     * @param int $pontuacoes_comprovante_id Id de comprovante
     *
     * @return float $soma_quantidade
     */
    public function getSumPontuacoesByComprovanteId(int $pontuacoes_comprovante_id)
    {
        try {
            $value = $this->_getPontuacoesTable()->find('all')
                ->where(
                    [
                        'pontuacoes_comprovante_id' => $pontuacoes_comprovante_id
                    ]
                )
                ->select(['soma_quantidade' => $this->_getPontuacoesTable()
                    ->find()
                    ->func()
                    ->sum('quantidade_gotas'), 'pontuacoes_comprovante_id'])
                ->first();

            return $value['soma_quantidade'];
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);
            Log::write("error", $trace);
        }
    }

    /**
     * Obtêm soma de pontuações de usuário
     *
     * @param int   $usuariosId  Id de usuário
     * @param int   $redesId     Id da Rede (Opcional)
     * @param array $clientesIds Array de Id de clientes (Opcional)
     *
     * @author      Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date        15/06/2018
     *
     * @return array $pontuacoes ("", "", "", "saldo")
     **/
    public function getSumPontuacoesOfUsuario(int $usuariosId, int $redesId = null, array $clientesIds = array())
    {
        try {
            /**
             *  Se passar a Rede, obtem os Ids de clientes e desconsidera o parâmetro de $clientesIds
             */

            // Obtem os ids de clientes da rede selecionada
            if (!empty($redesId) && $redesId > 0) {
                $redeHasClienteTable = TableRegistry::get("RedesHasClientes");

                $redeHasClientesQuery = $redeHasClienteTable->getAllRedesHasClientesIdsByRedesId($redesId);

                $clientesIds = array();

                foreach ($redeHasClientesQuery->toArray() as $key => $value) {
                    $clientesIds[] = $value["clientes_id"];
                }
            }

            $totalGotasAdquiridas = 0;
            $totalGotasUtilizadas = 0;
            $totalGotasExpiradas = 0;

            $mensagem = array();

            // A pesquisa só será feita se tiver clientes. Se não tiver, cliente não possui pontuações.
            if (sizeof($clientesIds) > 0) {
                // Pontuações obtidas pelo usuário

                // Primeiro, pega todos os comprovantes que não foram invalidados

                $pontuacoesComprovantesTable = TableRegistry::get("PontuacoesComprovantes");
                $pontuacoesComprovantesValidosQuery = $pontuacoesComprovantesTable->find('all')
                    ->where(
                        [
                            "usuarios_id" => $usuariosId,
                            "clientes_id in " => $clientesIds,
                            "registro_invalido" => 0,
                        ]
                    )->select(['id']);

                // Para cada comprovante, pega a soma das pontuacoes através de seus IDS

                $comprovantesIds = array();

                foreach ($pontuacoesComprovantesValidosQuery as $key => $comprovante) {
                    $comprovantesIds[] = $comprovante->id;
                }

                // faz o tratamento se tem algum id de pontuacao
                if (sizeof($comprovantesIds) > 0) {
                    $querytotalGotasAdquiridas = $this->_getPontuacoesTable()->find()->where(
                        [
                            "pontuacoes_comprovante_id in " => $comprovantesIds
                        ]
                    );

                    $querytotalGotasAdquiridas = $querytotalGotasAdquiridas->select(
                        [
                            'sum' => $querytotalGotasAdquiridas->func()->sum('quantidade_gotas')
                        ]
                    );

                    $totalGotasAdquiridas = !is_null($querytotalGotasAdquiridas->first()['sum']) ? $querytotalGotasAdquiridas->first()['sum'] : 0;

                }
                $queryTotalGotasUtilizadas = $this->_getPontuacoesTable()->find()->where(
                    [
                        "clientes_id in " => $clientesIds,
                        "usuarios_id" => $usuariosId,
                        "clientes_has_brindes_habilitados_id IS NOT NULL"
                    ]
                );

                $queryTotalGotasUtilizadas = $queryTotalGotasUtilizadas
                    ->select(
                        [
                            'sum' => $queryTotalGotasUtilizadas->func()->sum("quantidade_gotas")
                        ]
                    );

                $totalGotasUtilizadas = !is_null($queryTotalGotasUtilizadas->first()['sum']) ? $queryTotalGotasUtilizadas->first()['sum'] : 0;

                $queryTotalGotasExpiradas = $this->_getPontuacoesTable()
                    ->find()->where(
                        [
                            "clientes_id in " => $clientesIds,
                            "usuarios_id" => $usuariosId,
                            "expirado" => 1
                        ]
                    );

                $queryTotalGotasExpiradas = $queryTotalGotasExpiradas
                    ->select(
                        [
                            'sum' => $queryTotalGotasExpiradas->func()->sum("quantidade_gotas")
                        ]
                    );

                $totalGotasExpiradas = !is_null($queryTotalGotasExpiradas->first()['sum']) ? $queryTotalGotasExpiradas->first()['sum'] : 0;

                $mensagem = array(
                    "status" => 1,
                    "message" => Configure::read("messageLoadDataWithSuccess"),
                    "errors" => array()
                );

            } else {
                // Se não tiver pontuações, retorna o erro
                $mensagem = array(
                    "status" => 0,
                    "message" => Configure::read("messageLoadDataWithError"),
                    "errors" => array(Configure::read("messageUserNoPointsInNetwork"))
                );
            }

            $retorno = array(
                "mensagem" => $mensagem,
                "resumo_gotas" =>
                    array(
                    'total_gotas_adquiridas' => $totalGotasAdquiridas,
                    'total_gotas_utilizadas' => $totalGotasUtilizadas,
                    'total_gotas_expiradas' => $totalGotasExpiradas,
                    'saldo' => $totalGotasAdquiridas == 0 ? $totalGotasAdquiridas : $totalGotasAdquiridas - $totalGotasUtilizadas
                )
            );
            return $retorno;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registro: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write('error', $trace);

            return $stringError;
        }
    }

    /**
     * Obtêm soma de todos os pontos que estão 'pendentes' de uso
     *
     * @param int   $usuarios_id Id de Usuários
     * @param array $clientes_id Lista de id de clientes
     *
     * @return float $sum total de gotas
     */
    public function getSumPontuacoesObtainedByUsuario(int $usuarios_id, array $clientes_id)
    {
        try {
            // pegar os ids de comprovantes que foram invalidados pelo admin

            $query = $this->_getPontuacoesTable()->find();

            $total = $this->_getPontuacoesTable()
                ->find('all')
                ->select(['soma' => $query->func()->sum('quantidade_gotas')])
                ->where(
                    [
                        'Pontuacoes.usuarios_id' => $usuarios_id,
                        'Pontuacoes.clientes_id IN ' => $clientes_id,
                        'Pontuacoes.clientes_has_brindes_habilitados_id is null',
                        'Pontuacoes.expirado' => 0

                    ]
                )->contain(['PontuacoesAprovadas'])
                ->first();

            return $total['soma'];

        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao obter pontuações: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
        }
    }

    /**
     * Obtêm soma de todos os pontos utilizados
     *
     * @param int   $usuarios_id Id de Usuários
     * @param array $clientes_id Lista de id de clientes
     *
     * @return float $sum total de gotas
     */
    public function getSumPontuacoesUsedByUsuario(int $usuarios_id, array $clientes_id)
    {
        try {
            // pegar os ids de comprovantes que foram invalidados pelo admin

            $query = $this->_getPontuacoesTable()->find();

            $total = $this->_getPontuacoesTable()
                ->find('all')
                ->select(['soma' => $query->func()->sum('quantidade_gotas')])
                ->where(
                    [
                        'usuarios_id' => $usuarios_id,
                        'clientes_id IN ' => $clientes_id,
                        'pontuacoes_comprovante_id is null'

                    ]
                )->first();

            return $total['soma'];

        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao obter pontuações: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
        }
    }

    /**
     * Obtêm soma de todos os pontos que estão 'pendentes' de uso
     *
     * @param int   $usuarios_id Id de Usuários
     * @param array $clientes_id Lista de id de clientes
     *
     * @return float $sum total de gotas
     */
    public function getSumPontuacoesPendingToUsageByUsuario(int $usuarios_id, array $clientes_id)
    {
        try {
            // pegar os ids de comprovantes que foram invalidados pelo admin

            $pontuacoes_comprovantes_invalidated
                = $this->_getPontuacoesTable()->PontuacoesComprovantes
                ->find('all')
                ->select(['id'])
                ->where(
                    [
                        'usuarios_id' => $usuarios_id,
                        'clientes_id IN ' => $clientes_id,
                        'registro_invalido' => true
                    ]
                )->toArray();


            $pontuacoes_comprovantes_invalidated_ids = [];
            foreach ($pontuacoes_comprovantes_invalidated as $key => $value) {
                array_push($pontuacoes_comprovantes_invalidated_ids, $value['id']);
            }

            $conditions = [];

            array_push($conditions, ['usuarios_id' => $usuarios_id]);
            array_push($conditions, ['clientes_id IN' => $clientes_id]);

            $partial_conditions = $conditions;

            array_push($partial_conditions, ['utilizado' => Configure::read('dropletsUsageStatus')['ParcialUsed']]);

            if (sizeof($pontuacoes_comprovantes_invalidated_ids) > 0) {
                array_push(
                    $partial_conditions,
                    [
                        'OR' =>
                            [
                            'pontuacoes_comprovante_id NOT IN ' => $pontuacoes_comprovantes_invalidated_ids,
                            'pontuacoes_comprovante_id IS NULL'
                        ]

                    ]
                );
            }
            // verifica se o usuário tem alguma pontuacao parcialmente usada

            $last_pontuacao_partially_used = $this->_getPontuacoesTable()->find('all')
                ->where($partial_conditions)->first();

            $difference = null;

            // se encontrou a última pontuacao parcialmente usada
            if ($last_pontuacao_partially_used) {
                // pega a soma até a pontuacao parcialmente usada

                $last_pontuacao_conditions = $conditions;

                array_push(
                    $last_pontuacao_conditions,
                    [
                        'id <= ' => $last_pontuacao_partially_used->id
                    ]
                );

                array_push(
                    $last_pontuacao_conditions,
                    [
                        'clientes_has_brindes_habilitados_id IS NULL'
                    ]
                );

                if (sizeof($pontuacoes_comprovantes_invalidated_ids) > 0) {
                    array_push(
                        $last_pontuacao_conditions,
                        [
                            'OR' =>
                                [
                                'pontuacoes_comprovante_id NOT IN ' => $pontuacoes_comprovantes_invalidated_ids,
                                'pontuacoes_comprovante_id IS NULL'
                            ]
                        ]
                    );
                }

                $sum_pontuacoes_partially_used = $this->_getPontuacoesTable()->find('all')
                    ->select(
                        [
                            'sum'
                                => $this->_getPontuacoesTable()
                                ->find('all')
                                ->func()
                                ->sum('quantidade_gotas')
                        ]
                    )
                    ->where($last_pontuacao_conditions)
                    ->first();


                // pega a soma de pontuações já usadas na 'emissão' de brindes

                $fully_pontuacoes_with_brinde_used_conditions = $conditions;

                array_push(
                    $fully_pontuacoes_with_brinde_used_conditions,
                    [
                        'clientes_has_brindes_habilitados_id IS NOT NULL'
                    ]
                );

                if (sizeof($pontuacoes_comprovantes_invalidated_ids) > 0) {
                    array_push(
                        $fully_pontuacoes_with_brinde_used_conditions,
                        [
                            'OR' =>
                                [
                                'pontuacoes_comprovante_id NOT IN ' => $pontuacoes_comprovantes_invalidated_ids,
                                'pontuacoes_comprovante_id IS NULL'
                            ]
                        ]
                    );
                }

                $sum_pontuacoes_with_brinde_fully_used
                    = $this->_getPontuacoesTable()
                    ->find('all')
                    ->select(
                        [
                            'sum'
                                => $this->_getPontuacoesTable()
                                ->find('all')
                                ->func()
                                ->sum('quantidade_gotas')
                        ]
                    )
                    ->where($fully_pontuacoes_with_brinde_used_conditions)
                    ->first();

                $difference = $sum_pontuacoes_with_brinde_fully_used->sum - $sum_pontuacoes_partially_used->sum;
            } else {
                // não encontrou o último pendente. então devo pegar o último que foi
                // usado totalmente (código 2) mas que não tem brinde vinculado

                $conditions_pontuacoes_fully_used = $conditions;

                array_push(
                    $conditions_pontuacoes_fully_used,
                    [
                        'clientes_has_brindes_habilitados_id IS NULL'
                    ]
                );
                array_push(
                    $conditions_pontuacoes_fully_used,
                    [
                        'utilizado' => Configure::read('dropletsUsageStatus')['FullyUsed']
                    ]
                );

                if (sizeof($pontuacoes_comprovantes_invalidated_ids) > 0) {
                    array_push(
                        $conditions_pontuacoes_fully_used,
                        [
                            'OR' =>
                                [
                                'pontuacoes_comprovante_id NOT IN ' => $pontuacoes_comprovantes_invalidated_ids,
                                'pontuacoes_comprovante_id IS NULL'
                            ]
                        ]
                    );
                }

                $sum_pontuacoes_fully_used
                    = $this->_getPontuacoesTable()
                    ->find('all')
                    ->select(
                        [
                            'sum'
                                => $this->_getPontuacoesTable()
                                ->find('all')
                                ->func()
                                ->sum('quantidade_gotas')
                        ]
                    )
                    ->where($conditions_pontuacoes_fully_used)
                    ->first();

                $sum_pontuacoes_fully_used['sum'] = is_null($sum_pontuacoes_fully_used['sum']) ? 0 : $sum_pontuacoes_fully_used['sum'];

                // pega a soma de pontuações já usadas na 'emissão' de brindes

                $fully_pontuacoes_with_brinde_used_conditions = $conditions;

                array_push(
                    $fully_pontuacoes_with_brinde_used_conditions,
                    [
                        'clientes_has_brindes_habilitados_id IS NOT NULL'
                    ]
                );

                if (sizeof($pontuacoes_comprovantes_invalidated_ids) > 0) {
                    array_push(
                        $fully_pontuacoes_with_brinde_used_conditions,
                        [
                            'OR' =>
                                [
                                'pontuacoes_comprovante_id NOT IN ' => $pontuacoes_comprovantes_invalidated_ids,
                                'pontuacoes_comprovante_id IS NULL'
                            ]
                        ]
                    );
                }

                $sum_pontuacoes_with_brinde_fully_used
                    = $this->_getPontuacoesTable()
                    ->find('all')
                    ->select(
                        [
                            'sum'
                                => $this->_getPontuacoesTable()
                                ->find('all')
                                ->func()
                                ->sum('quantidade_gotas')
                        ]
                    )
                    ->where($fully_pontuacoes_with_brinde_used_conditions)
                    ->first();

                $sum_pontuacoes_with_brinde_fully_used['sum']
                    = is_null($sum_pontuacoes_with_brinde_fully_used['sum'])
                    ? 0
                    : $sum_pontuacoes_with_brinde_fully_used['sum'];

                $difference = $sum_pontuacoes_with_brinde_fully_used->sum - $sum_pontuacoes_fully_used->sum;
            }

            return $difference;
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao obter pontuações: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
        }
    }

    /**
     * Undocumented function
     *
     * @param array $gotasIds
     * @return void
     */
    public function getUsuariosIdsOfPontuacoesByGotas(array $gotasIds)
    {
        try {
            $whereConditions = array();

            $whereConditions = ['gotas_id in ' => $gotasIds];

            return $this->_getPontuacoesTable()
                ->find('all')
                ->distinct(['usuarios_id'])
                ->where($whereConditions)
                ->contain(['Usuarios']);
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao obter pontuações: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
        }
    }

    /* ------------------------ Update ------------------------ */

    /**
     * Atualiza todos os comprovantes de pontuações conforme objeto de condições
     *
     * @param int   $pontuacoes_comprovante_id Id de Pontuacoes Comprovantes
     * @param array $array_conditions          Array de Condições
     *
     * @return void
     */
    public function updateAllPontuacoesByComprovantesId(int $pontuacoes_comprovante_id, array $array_conditions)
    {
        try {
            $pontuacoes = $this->_getPontuacoesTable()->query();

            return $pontuacoes->update()
                ->set([$array_conditions])
                ->where(['pontuacoes_comprovante_id' => $pontuacoes_comprovante_id])
                ->execute();
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao atualizar pontuações: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
        }
    }

    /**
     * Atualiza pontuações pendentes
     *
     * @param array $array_update Array de informações à atualizar
     *
     * @return bool registro atualizado
     */
    public function updatePendingPontuacoesForUsuario(array $array_update)
    {
        try {
            foreach ($array_update as $key => $value) {
                $pontuacao = $this->_getPontuacoesTable()->find('all')
                    ->where(['Pontuacoes.id' => $value['id']])
                    ->first();

                if (is_null($pontuacao)) {
                    throw new \Exception(__("Registro de id {0} não encontrado em pontuações!", $value->id));
                }
                $pontuacao->utilizado = $value['utilizado'];

                $this->_getPontuacoesTable()->save($pontuacao);
            }

            return true;
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao atualizar pontuações: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
        }
    }

    /**
     * Remove todas as pontuacoes por Id de Cliente
     *
     * @param int $clientes_id Id de Cliente
     * @param int $matriz_id   Id da Matriz
     *
     * @return \App\Model\Entity\Pontuacoes $array[]
     *  lista de pontuacoes pendentes
     */
    public function setPontuacoesToMainCliente(int $clientes_id, int $matriz_id)
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
     * Atualiza a quantidade de litros abastecidos de uma Pontuação
     *
     * @param int   $pontuacao_id     Id da pontuação
     * @param float $quantidade_gotas Quantidade de gotas acumuladas
     *
     * @return \App\Model\Entity\Pontuaco $pontuacao Entidade atualizada
     */
    public function updateQuantidadeGotasByPontuacaoId(int $pontuacao_id, float $quantidade_gotas)
    {
        try {
            $pontuacoes = $this->_getPontuacoesTable()->query();

            return $pontuacoes->update()
                ->set(['quantidade_gotas' => $quantidade_gotas])
                ->where(['id' => $pontuacao_id])
                ->execute();
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao editar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);
            Log::write("error", $trace);
        }
    }

    /**
     * Atualiza todas as pontuacoes conforme argumentos
     *
     * @param array $fields     Campos contendo atualização
     * @param array $conditions Condições
     *
     * @return bool
     */
    public function updateAllPontuacoes(array $fields, array $conditions)
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
     * Remove todas as pontuacoes por Id de Cliente
     *
     * @param array $clientes_ids Ids de Clientes
     *
     * @return boolean
     */
    public function deleteAllPontuacoes()
    {
        try {
            return $this->deleteAll(['id >= ' => 0]);
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao obter registro: {0}, em {1}", $e->getMessage(), $object['file']);

            Log::write('error', $stringError);
            Log::write('error', $trace);

            return ['success' => false, 'message' => $stringError];
        }
    }

    /**
     * Remove todas as pontuacoes por Id de Cliente
     *
     * @param array $clientes_ids Ids de Clientes
     *
     * @return boolean
     */
    public function deleteAllPontuacoesByClientesIds(array $clientes_ids)
    {
        try {
            return $this->deleteAll(['clientes_id in' => $clientes_ids]);
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
     * Remove todas as pontuacoes por Id de usuário
     *
     * @param int $usuarios_id Id de Usuário
     *
     * @return boolean
     */
    public function deleteAllPontuacoesByUsuariosId(int $usuarios_id)
    {
        try {
            return $this->deleteAll(['usuarios_id' => $usuarios_id]);
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
