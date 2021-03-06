<?php

namespace App\Model\Table;

use \DateTime;
use Exception;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use App\Custom\RTI\DebugUtil;
use App\Custom\RTI\ResponseUtil;
use App\Model\Entity\Pontuacao;
use Cake\Database\Expression\QueryExpression;
use Throwable;

/**
 * Pontuacoes Model
 *
 * @property \App\Model\Table\UsuariosTable|\Cake\ORM\Association\BelongsTo $Usuarios
 * @property \App\Model\Table\BrindesHabilitadosTable|\Cake\ORM\Association\BelongsTo $BrindesHabilitados
 * @property \App\Model\Table\GotasTable|\Cake\ORM\Association\BelongsTo $Gotas
 *
 * @method \App\Model\Entity\Pontuacao get($primaryKey, $options = [])
 * @method \App\Model\Entity\Pontuacao newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Pontuacao[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Pontuacao|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Pontuacao patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Pontuacao[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Pontuacao findOrCreate($search, callable $callback = null, $options = [])
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
            array(
                'foreignKey' => 'clientes_id',
                'joinType' => 'INNER'
            )
        );

        $this->belongsTo(
            'Usuarios',
            array(
                'foreignKey' => 'usuarios_id',
                'joinType' => Query::JOIN_TYPE_LEFT
                // 'joinType' => Query::JOIN_TYPE_INNER
            )
        );

        $this->belongsTo(
            "Brindes",
            [
                "className" => "Brindes",
                "foreignKey" => "brindes_id",
                'joinType' => Query::JOIN_TYPE_LEFT
                // 'joinType' => Query::JOIN_TYPE_INNER
            ]
        );

        $this->belongsTo(
            "Funcionarios",
            [
                "className" => "usuarios",
                "foreignKey" => "funcionarios_id",
                "joinType" => Query::JOIN_TYPE_INNER
            ]
        );

        $this->belongsTo(
            'Gotas',
            [
                'foreignKey' => 'gotas_id',
                'joinType' => Query::JOIN_TYPE_LEFT
                // 'joinType' => Query::JOIN_TYPE_INNER
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
                    'PontuacoesAprovadas.cancelado' => 0
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
            ->decimal('quantidade_multiplicador')
            ->requirePresence('quantidade_multiplicador', 'create')
            ->allowEmpty('quantidade_multiplicador');

        $validator
            ->decimal('quantidade_gotas')
            ->requirePresence('quantidade_gotas', 'create')
            ->allowEmpty('quantidade_gotas');

        $validator
            ->decimal('valor_moeda_venda')
            ->allowEmpty('valor_moeda_venda');

        $validator
            ->dateTime('data')
            ->requirePresence('data', 'create')
            ->notEmpty('data');

        /**
         * Define se a pontuação está expirada
         * 0 - não (default)
         * 1 - sim
         */
        $validator
            ->integer('expirado');

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
        $rules->add($rules->existsIn(['brindes_id'], 'Brindes'));
        $rules->add($rules->existsIn(['gotas_id'], 'Gotas'));

        return $rules;
    }

    /**
     * -------------------------------------------------------------
     * Methods
     * -------------------------------------------------------------
     */

    #region Create

    /**
     * Adiciona pontuação de brinde para usuário
     *
     * @param int   $clientesId            Id de cliente
     * @param int   $usuariosId            Id de usuário
     * @param int   $brindesHabilitadosId  Id do brinde habilitado
     * @param float $quantidadePontos      Quantidade de pontos em gotas
     * @param float $quantidadePontos      Valor Pago em reais
     * @param int   $funcionariosId        Id de funcionário (opcional)
     *
     * @return boolean Resultado de inserção
     */
    public function addPontuacoesBrindesForUsuario(
        int $clientesId,
        int $usuariosId,
        int $brindesHabilitadosId,
        float $quantidadePontosGotas,
        float $quantidadePontosReais,
        int $funcionariosId = null
    ) {
        // @todo ver onde este método é usado e ajustar a chamada
        try {
            $pontuacao = $this->newEntity();

            $pontuacao["quantidade_gotas"] = $quantidadePontosGotas;
            $pontuacao["valor_moeda_venda"] = $quantidadePontosReais;
            $pontuacao["clientes_id"] = $clientesId;
            $pontuacao["usuarios_id"] = $usuariosId;
            $pontuacao["brindes_id"] = $brindesHabilitadosId;
            $pontuacao["utilizado"] = Configure::read('dropletsUsageStatus')['FullyUsed'];
            $pontuacao["data"] = date('Y-m-d H:i:s');
            $pontuacao["funcionarios_id"] = $funcionariosId;

            return $this->save($pontuacao);
        } catch (\Exception $e) {
            $trace = $e->getTraceAsString();
            $stringError = __("Erro ao editar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);
            Log::write("error", $trace);
        }
    }

    /**
     * Guarda registro de Pontuacao
     * Se $auto_save = false, retorna objeto para gravar posteriormente em batch
     *
     * @param int    $clientesId               Id de cliente
     * @param int    $usuariosId               Id de usuário
     * @param int    $funcionariosId           Id de funcionário (usuário)
     * @param int    $gotasId                  Id da gota
     * @param float  $quantidadeMultiplicador  Quantidade de multiplicador
     * @param float  $quantidadeGotas          Quantidade de gotas
     * @param int    $pontuacoesComprovanteId Id do comprovante da pontuação
     * @param string $data                      Data de processamento
     *
     * @return object $pontuacao
     */
    public function addPontuacaoCupom(
        int $clientesId,
        int $usuariosId,
        int $funcionariosId,
        int $gotasId,
        float $quantidadeMultiplicador,
        float $quantidadeGotas,
        int $pontuacoesComprovanteId,
        string $data
    ) {
        try {
            $pontuacao = $this->newEntity();

            $pontuacao["clientes_id"] = $clientesId;
            $pontuacao["usuarios_id"] = $usuariosId;
            $pontuacao["funcionarios_id"] = $funcionariosId;
            $pontuacao["quantidade_multiplicador"] = $quantidadeMultiplicador;
            $pontuacao["quantidade_gotas"] = $quantidadeGotas;
            $pontuacao["gotas_id"] = $gotasId;
            $pontuacao["pontuacoes_comprovante_id"] = $pontuacoesComprovanteId;
            $pontuacao["data"] = $data;
            $pontuacao["expirado"] = false;

            return $this->save($pontuacao);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao gravar registro: " . $e->getMessage());

            Log::write('error', $stringError);
            Log::write("error", $trace);
        }
    }

    /**
     * src\Model\Table\PontuacoesTable.php::saveUpdate
     *
     * Insere/Atualiza registros de Gotas de Bonificação SEFAZ
     *
     * @param Pontuacao $pontuacao Entitade
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-10-08
     *
     * @return Pontuacao $pontuacao Registrada
     */
    public function saveUpdate(Pontuacao $pontuacao)
    {
        try {
            return $this->save($pontuacao);
        } catch (Exception $e) {
            $message = sprintf("[%s] %s", MESSAGE_SAVED_ERROR, $e->getMessage());
            Log::write("error", $message);
            throw new Exception($message);
        }
    }

    #endregion

    #region Read

    /**
     * Obtem Produto que mais vendeu
     *
     * Realiza pesquisa no banco de dados e obtem lista de produtos mais vendidos conforme parâmetros
     *
     * @param integer $redesId Id da Rede
     * @param integer $clientesId Id do Estabelecimento
     * @param DateTime $minDate Data de Início
     * @param DateTime $maxDate Data de Fim
     * @param int $limit Limite de registros
     * @return \Cake\ORM\Query|\App\model\Entity\Pontuacao[] $pontuacoes [sum_gotas|gota] Array contendo coluna de soma e gota
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.1.6
     * @date 2020-03-08
     */
    public function getBestSellerGotas(int $redesId = null, int $clientesId = null, DateTime $minDate = null, DateTime $maxDate = null, int $limit = 0)
    {
        try {
            $where = function (QueryExpression $exp) use ($redesId, $clientesId, $minDate, $maxDate) {
                $exp->eq("Redes.id", $redesId);

                if (!empty($clientesId)) {
                    $exp->eq("Clientes.id", $clientesId);
                }

                $exp->isNotNull("Pontuacoes.gotas_id");
                /**
                 * Somente gotas/produtos cadastradas manualmente, para remover a possibilidade
                 * de exibir pontuações de gratificação
                 */
                $exp->eq("Gotas.tipo_cadastro", 0);
                $exp->eq("PontuacoesComprovantes.cancelado", false);

                // campos de data são obrigatórios
                $exp->gte("Pontuacoes.data", $minDate);
                $exp->lte("Pontuacoes.data", $maxDate);

                return $exp;
            };

            $join = [
                "Clientes.RedesHasClientes.Redes",
                "Gotas",
                "PontuacoesComprovantes",
                "Usuarios"
            ];

            $sumGotas = $this->find()->func()->sum("Pontuacoes.quantidade_gotas");

            $select = [
                "sum" => $sumGotas,
                "nome" => "Gotas.nome_parametro",
                "usuario" => "Usuarios.nome"
            ];

            $orderBy = ["sum" => "DESC"];

            return $this->find("all")
                ->where($where)
                ->contain($join)
                ->select($select)
                ->group(["Pontuacoes.gotas_id"])
                ->order($orderBy)
                ->limit($limit);
        } catch (Throwable $th) {
            $message = sprintf("[%s] %s", MSG_LOAD_EXCEPTION, $th->getMessage());
            Log::write("error", $message);
            throw new Exception($message, $th->getCode());
        }
    }

    /**
     * Obtem Funcionários que mais abasteceu Clientes
     *
     * Realiza pesquisa no banco de dados e obtem lista de Funcionários que mais abasteceu Clientes
     *
     * @param integer $redesId Id da Rede
     * @param integer $clientesId Id do Estabelecimento
     * @param DateTime $minDate Data de Início
     * @param DateTime $maxDate Data de Fim
     * @param int $limit Limite de registros
     * @return \Cake\ORM\Query|\App\model\Entity\Pontuacao[] $items [sum_gotas|id_funcionario|nome_funcionario] Array contendo coluna de quantidade de vezes atendida e funcionário
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.1.6
     * @date 2020-03-08
     */
    public function getEmployeeMostSoldGotas(int $redesId = null, int $clientesId = null, DateTime $minDate = null, DateTime $maxDate = null, int $limit = 0)
    {
        try {
            $where = function (QueryExpression $exp) use ($redesId, $clientesId, $minDate, $maxDate) {

                $exp->eq("Redes.id", $redesId);

                if (!empty($clientesId)) {
                    $exp->eq("Clientes.id", $clientesId);
                }

                $exp->isNotNull("Pontuacoes.gotas_id");
                /**
                 * Somente gotas/produtos cadastradas manualmente, para remover a possibilidade
                 * de exibir pontuações de gratificação
                 */
                $exp->eq("Gotas.tipo_cadastro", 0);
                $exp->eq("PontuacoesComprovantes.cancelado", 0);

                // campos de data são obrigatórios
                $exp->gte("Pontuacoes.data", $minDate);
                $exp->lte("Pontuacoes.data", $maxDate);

                return $exp;
            };

            $join = [
                "Clientes.RedesHasClientes.Redes",
                "Gotas",
                "PontuacoesComprovantes",
                "Funcionarios"
            ];

            $sumGotas = $this->find()->func()->count("Pontuacoes.quantidade_gotas");

            $select = [
                "count" => $sumGotas,
                "funcionarios_id" => "Funcionarios.id",
                "funcionarios_nome" => "Funcionarios.nome"
            ];

            $order = ["count" => "DESC"];

            return $this->find("all")
                ->where($where)
                ->contain($join)
                ->select($select)
                ->group(["funcionarios_id"])
                ->order($order)
                ->limit($limit);
        } catch (Throwable $th) {
            $message = sprintf("[%s] %s", MSG_LOAD_EXCEPTION, $th->getMessage());
            Log::write("error", $message);
            throw new Exception($message, $th->getCode());
        }
    }

    /**
     * Obtem Soma de Pontos Adquiridos
     *
     * Realiza pesquisa no banco de dados e obtem suma de pontos adquiridos pelos usuários na rede/estabelecimento
     *
     * @param integer $redesId Id da Rede
     * @param integer $clientesId Id do Estabelecimento
     * @param DateTime $minDate Data de Início
     * @param DateTime $maxDate Data de Fim
     * @return mixed[] $items [estabelecimento|sum_gotas|] Array contendo informações de Estabelecimento e soma
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.1.8
     * @date 2020-03-10
     */
    public function getSumPointsNetwork(int $redesId = null, int $clientesId = null, DateTime $minDate, DateTime $maxDate)
    {
        try {
            $where = function (QueryExpression $exp) use ($redesId, $clientesId, $minDate, $maxDate) {
                $exp->eq("Redes.id", $redesId);

                if (!empty($clientesId)) {
                    $exp->eq("Clientes.id", $clientesId);
                }

                $exp->isNotNull("Pontuacoes.gotas_id");
                /**
                 * Somente gotas/produtos cadastradas manualmente, para remover a possibilidade
                 * de exibir pontuações de gratificação
                 */
                $exp->eq("Gotas.tipo_cadastro", 0);
                $exp->eq("PontuacoesComprovantes.cancelado", 0);

                // campos de data são obrigatórios
                $exp->gte("Pontuacoes.data", $minDate);
                $exp->lte("Pontuacoes.data", $maxDate);

                return $exp;
            };

            $join = [
                "Clientes.RedesHasClientes.Redes",
                "Gotas",
                "PontuacoesComprovantes",
                "Usuarios"
            ];

            $sumGotas = $this->find()->func()->sum("Pontuacoes.quantidade_gotas");

            $select = [
                "sum" => $sumGotas,
                "clientes_id" => "Clientes.id",
                "nome" => "Clientes.nome_fantasia"
            ];

            return $this->find("all")
                ->where($where)
                ->contain($join)
                ->select($select)
                ->group(["Pontuacoes.clientes_id"])
                ->order(["SUM" => "DESC"]);
        } catch (Throwable $th) {
            $message = sprintf("[%s] %s", MSG_LOAD_EXCEPTION, $th->getMessage());
            Log::write("error", $message);
            throw new Exception($message, $th->getCode());
        }
    }

    /**
     * Obtem Usuários que mais adquiriu Produtos
     *
     * Realiza pesquisa no banco de dados e obtem lista de Usuários que mais adquiriu Produtos
     *
     * @param integer $redesId Id da Rede
     * @param integer $clientesId Id do Estabelecimento
     * @param DateTime $minDate Data de Início
     * @param DateTime $maxDate Data de Fim
     * @param int $limit Limite de registros
     * @return \Cake\ORM\Query|\App\model\Entity\Pontuacao[] [sum_gotas|usuarios_id|nome] Array contendo informações de pontuação e de usuário
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 1.1.6
     * @date 2020-03-09
     */
    public function getUserHighestPointsIn(int $redesId = null, int $clientesId = null, DateTime $minDate = null, DateTime $maxDate = null, int $limit = 0)
    {
        try {
            $where = function (QueryExpression $exp) use ($redesId, $clientesId, $minDate, $maxDate) {

                $exp->eq("Redes.id", $redesId);

                if (!empty($clientesId)) {
                    $exp->eq("Clientes.id", $clientesId);
                }

                $exp->isNotNull("Pontuacoes.gotas_id");
                /**
                 * Somente gotas/produtos cadastradas manualmente, para remover a possibilidade
                 * de exibir pontuações de gratificação
                 */
                $exp->eq("Gotas.tipo_cadastro", 0);
                $exp->eq("PontuacoesComprovantes.cancelado", 0);

                // campos de data são obrigatórios
                $exp->gte("Pontuacoes.data", $minDate);
                $exp->lte("Pontuacoes.data", $maxDate);

                return $exp;
            };

            $join = [
                "Clientes.RedesHasClientes.Redes",
                "Gotas",
                "PontuacoesComprovantes",
                "Usuarios"
            ];

            $sumGotas = $this->find()->func()->sum("Pontuacoes.quantidade_gotas");

            $select = [
                "sum" => $sumGotas,
                "usuarios_id" => "Usuarios.id",
                "nome" => "Usuarios.nome"
            ];

            $order = ["sum" => "DESC"];

            return $this->find("all")
                ->where($where)
                ->contain($join)
                ->select($select)
                ->group(["Pontuacoes.usuarios_id"])
                ->order($order)
                ->limit($limit);
        } catch (Throwable $th) {
            $message = sprintf("[%s] %s", MSG_LOAD_EXCEPTION, $th->getMessage());
            Log::write("error", $message);
            throw new Exception($message, $th->getCode());
        }
    }

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
            $pontuacao = $this->find('all')
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
            return $this->find('all')
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
     * @param bool  $cancelado Tipos de registros inválidos (false = válidos / true = inválidos)
     *
     * @return array $pontuacoes
     **/
    public function getPontuacoesOfUsuario(int $usuarios_id, array $array_clientes_id, bool $cancelado)
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
            $pontuacoes_invalidas = $this->PontuacoesComprovantes->find('all')
                ->where(
                    [
                        'usuarios_id' => $usuarios_id,
                        'clientes_id  in ' => $array_clientes_id,
                        'cancelado' => true
                    ]
                )
                ->select('id');

            $pontuacoes_invalidas_array = [];

            if (count($pontuacoes_invalidas->toArray()) > 0) {
                foreach ($pontuacoes_invalidas->toArray() as $key => $value) {
                    $pontuacoes_invalidas_array[] = $value['id'];
                }
            }

            if ($cancelado) {
                if (count($pontuacoes_invalidas_array) > 0) {
                    $pontuacoes
                        = $this->find('all')
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
                        = $this->find('all')
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

                if (count($pontuacoes_invalidas_array) > 0) {
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

                if (count($pontuacoes_invalidas_array) > 0) {
                    array_push($conditions, [$or_condition]);
                }

                $pontuacoes
                    = $this->find('all')
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
     * @param int   $ultimoIdProcessado Ultimo Id Processado
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 2018/05/14
     *
     * @return array @pontuacoes Lista de pontuações
     */
    public function getPontuacoesPendentesForUsuario(int $usuarios_id, array $clientes_id = [], int $how_many = 10, int $ultimoIdProcessado = null)
    {
        try {
            $conditions = [];

            $pontuacoesComprovantesInvalidated
                = $this->PontuacoesComprovantes
                ->find('all')
                ->select(['id'])
                ->where(
                    [
                        'usuarios_id' => $usuarios_id,
                        'clientes_id IN ' => $clientes_id,
                        'cancelado' => true
                    ]
                )->toArray();


            $pontuacoesComprovantesInvalidatedIds = [];
            foreach ($pontuacoesComprovantesInvalidated as $key => $value) {
                array_push($pontuacoesComprovantesInvalidatedIds, $value['id']);
            }

            if (count($pontuacoesComprovantesInvalidatedIds) > 0) {
                array_push(
                    $conditions,
                    [
                        'OR' =>
                        [
                            'pontuacoes_comprovante_id NOT IN ' => $pontuacoesComprovantesInvalidatedIds,
                            'pontuacoes_comprovante_id IS NULL'
                        ]
                    ]
                );
            }

            array_push($conditions, ['clientes_id IN ' => $clientes_id]);
            array_push($conditions, ['usuarios_id' => $usuarios_id]);
            array_push($conditions, ['utilizado != ' => Configure::read('dropletsUsageStatus')['FullyUsed']]);

            if (!is_null($ultimoIdProcessado)) {
                array_push($conditions, ['id >= ' => (int) $ultimoIdProcessado]);
            }

            $select = [
                'id',
                // "quantidade_gotas" => 'sum(quantidade_gotas)',
                "quantidade_gotas" => 'quantidade_gotas',
                'utilizado'
            ];
            if ($how_many === 1) {
                $select = [
                    'id',
                    "quantidade_gotas" => 'sum(quantidade_gotas)',
                    // "quantidade_gotas" => 'quantidade_gotas',
                    'utilizado'
                ];
            }

            $result = $this
                ->find('all')
                ->select($select)
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
            $value = $this->find('all')
                ->where(
                    [
                        'pontuacoes_comprovante_id' => $pontuacoes_comprovante_id
                    ]
                )
                ->select(['soma_quantidade' => $this
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
     * @return array $pontuacoes ("total_gotas_adquiridas", "total_gotas_utilizadas", "total_gotas_expiradas", "saldo")
     **/
    public function getSumPontuacoesOfUsuario(int $usuariosId, int $redesId = null, array $clientesIds = array())
    {
        try {
            /**
             *  Se passar a Rede, obtem os Ids de clientes e desconsidera o parâmetro de $clientesIds
             */

            // Obtem os ids de clientes da rede selecionada
            if (!empty($redesId) && $redesId > 0) {
                $redeHasClienteTable = $this->Clientes->RedesHasClientes;

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
            if (count($clientesIds) > 0) {
                // Pontuações obtidas pelo usuário

                // Primeiro, pega todos os comprovantes que não foram invalidados

                $pontuacoesComprovantesTable = $this->PontuacoesComprovantes;
                $pontuacoesComprovantesValidosQuery = $pontuacoesComprovantesTable->find('all')
                    ->where(
                        [
                            "usuarios_id" => $usuariosId,
                            "clientes_id in " => $clientesIds,
                            "cancelado" => 0,
                        ]
                    )->select(['id']);

                // Para cada comprovante, pega a soma das pontuacoes através de seus IDS

                $comprovantesIds = array();

                foreach ($pontuacoesComprovantesValidosQuery as $key => $comprovante) {
                    $comprovantesIds[] = $comprovante->id;
                }

                // faz o tratamento se tem algum id de pontuacao
                if (count($comprovantesIds) > 0) {
                    $queryTotalGotasAdquiridas = $this->find();
                    $queryTotalGotasAdquiridas = $this->find("all")->where(
                        [
                            "pontuacoes_comprovante_id in " => $comprovantesIds
                        ]
                    )->select(
                        [
                            'sum' => $queryTotalGotasAdquiridas->func()->sum('quantidade_gotas')
                        ]
                    )->first();

                    $sumTotalGotasAdquiridas = $queryTotalGotasAdquiridas->sum;

                    $totalGotasAdquiridas = !empty($sumTotalGotasAdquiridas) ? $sumTotalGotasAdquiridas : 0;
                }
                $queryTotalGotasUtilizadas = $this->find();
                $queryTotalGotasUtilizadas = $this->find("all")
                    ->where(
                        [
                            "clientes_id in " => $clientesIds,
                            "usuarios_id" => $usuariosId,
                            "brindes_id IS NOT NULL"
                        ]
                    )
                    ->select(

                        [
                            'sum' => $queryTotalGotasUtilizadas->func()->sum("quantidade_gotas")
                        ]
                    )->first();

                $sumTotalGotasUtilizadas = $queryTotalGotasUtilizadas->sum;

                $totalGotasUtilizadas = !empty($sumTotalGotasUtilizadas) ? $sumTotalGotasUtilizadas : 0;

                $queryTotalGotasExpiradas = $this->find();
                $queryTotalGotasExpiradas = $this->find("all")
                    ->where(
                        [
                            "clientes_id in " => $clientesIds,
                            "usuarios_id" => $usuariosId,
                            "expirado" => 1
                        ]
                    )->select(
                        [
                            'sum' => $queryTotalGotasExpiradas->func()->sum("quantidade_gotas")
                        ]
                    )->first();

                $sumTotalGotasExpiradas = $queryTotalGotasExpiradas->sum;

                $totalGotasExpiradas = !empty($sumTotalGotasExpiradas) ? $sumTotalGotasExpiradas : 0;

                $mensagem = array(
                    "status" => true,
                    "message" => Configure::read("messageLoadDataWithSuccess"),
                    "errors" => array()
                );
            } else {
                // Se não tiver pontuações, retorna o erro
                $mensagem = array(
                    "status" => false,
                    "message" => Configure::read("messageLoadDataWithError"),
                    "errors" => array(Configure::read("messageUsuarioNoPointsInNetwork"))
                );
            }

            // @todo retirar lógica de dentro desta consulta e só retornar o array de resumo_gotas
            $retorno = array(
                "mensagem" => $mensagem,
                "resumo_gotas" =>
                array(
                    'total_gotas_adquiridas' => floor($totalGotasAdquiridas),
                    'total_gotas_utilizadas' => floor($totalGotasUtilizadas),
                    'total_gotas_expiradas' => floor($totalGotasExpiradas),
                    'saldo' => $totalGotasAdquiridas == 0 ? $totalGotasAdquiridas : floor($totalGotasAdquiridas - $totalGotasUtilizadas)
                )
            );
            return $retorno;
        } catch (\Exception $e) {
            $trace = $e->getTraceAsString();
            $stringError = __("Erro ao buscar registro: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write('error', $trace);

            return $stringError;
        }
    }

    /**
     * Obtêm soma de dinheiro utilizado em brindes do usuário
     *
     * @param int   $usuariosId  Id de usuário
     * @param int   $redesId     Id da Rede (Opcional)
     *
     * @author      Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date        23/09/2018
     *
     * @return array $pontuacoes ("total_gotas_adquiridas", "total_gotas_utilizadas", "total_gotas_expiradas", "saldo")
     **/
    public function getSumPontuacoesReaisByUsuarioId(int $usuariosId, int $redesId = null)
    {
        try {
            $clientesIds = array();

            // Obtem os ids de clientes da rede selecionada
            if (!empty($redesId) && $redesId > 0) {
                $redeHasClienteTable = TableRegistry::get("RedesHasClientes");

                $redeHasClientesQuery = $redeHasClienteTable->getAllRedesHasClientesIdsByRedesId($redesId);


                $clientesIds = array();

                foreach ($redeHasClientesQuery->toArray() as $key => $value) {
                    $clientesIds[] = $value["clientes_id"];
                }
            }

            $mensagem = array();

            // A pesquisa só será feita se tiver clientes. Se não tiver, cliente não possui pontuações.

            if (count($clientesIds) == 0) {
                $resultado = array("totalMoedaCompraBrindes" => 0);

                return $resultado;
            } else {

                // Array de Condições

                $whereConditions = array(
                    "clientes_id in " => $clientesIds,
                    "usuarios_id" => $usuariosId,
                    "valor_moeda_venda IS NOT NULL"
                );
                // Total de Brindes adquiridos pelo usuário na rede inteira:

                $retorno = $this->find()
                    ->where($whereConditions)
                    ->select(
                        array(
                            "totalMoedaCompraBrindes" => $this->find()->func()->sum("valor_moeda_venda")
                        )
                    )->first();

                $totalMoedaCompraBrindes = !empty($retorno) ? $retorno["totalMoedaCompraBrindes"] : 0;
                return $totalMoedaCompraBrindes;
            }
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registro: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);

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

            $query = $this->find();

            $total = $this
                ->find('all')
                ->select(['soma' => $query->func()->sum('quantidade_gotas')])
                ->where(
                    [
                        'Pontuacoes.usuarios_id' => $usuarios_id,
                        'Pontuacoes.clientes_id IN ' => $clientes_id,
                        'Pontuacoes.brindes_id is null',
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

            $query = $this->find();

            $total = $this
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

            $pontuacoesComprovantesInvalidated
                = $this->PontuacoesComprovantes
                ->find('all')
                ->select(['id'])
                ->where(
                    array(
                        'usuarios_id' => $usuarios_id,
                        'clientes_id IN ' => $clientes_id,
                        'cancelado' => true
                    )
                )->toArray();


            $pontuacoesComprovantesInvalidatedIds = [];
            foreach ($pontuacoesComprovantesInvalidated as $key => $value) {
                array_push($pontuacoesComprovantesInvalidatedIds, $value['id']);
            }

            $conditions = [];

            array_push($conditions, ['usuarios_id' => $usuarios_id]);
            array_push($conditions, ['clientes_id IN' => $clientes_id]);

            $partialConditions = $conditions;

            array_push($partialConditions, ['utilizado' => Configure::read('dropletsUsageStatus')['ParcialUsed']]);

            if (count($pontuacoesComprovantesInvalidatedIds) > 0) {
                array_push(
                    $partialConditions,
                    array(
                        'OR' =>
                        array(
                            'pontuacoes_comprovante_id NOT IN ' => $pontuacoesComprovantesInvalidatedIds,
                            'pontuacoes_comprovante_id IS NULL'
                        )
                    )
                );
            }
            // verifica se o usuário tem alguma pontuacao parcialmente usada

            $lastPontuacaoPartiallyUsed = $this->find('all')
                ->where($partialConditions)->first();

            $difference = null;

            // se encontrou a última pontuacao parcialmente usada
            if ($lastPontuacaoPartiallyUsed) {
                // pega a soma até a pontuacao parcialmente usada

                $lastPontuacaoConditions = $conditions;

                array_push(
                    $lastPontuacaoConditions,
                    [
                        'id <= ' => $lastPontuacaoPartiallyUsed->id
                    ]
                );

                array_push(
                    $lastPontuacaoConditions,
                    [
                        'brindes_id IS NULL'
                    ]
                );

                if (count($pontuacoesComprovantesInvalidatedIds) > 0) {
                    array_push(
                        $lastPontuacaoConditions,
                        [
                            'OR' =>
                            [
                                'pontuacoes_comprovante_id NOT IN ' => $pontuacoesComprovantesInvalidatedIds,
                                'pontuacoes_comprovante_id IS NULL'
                            ]
                        ]
                    );
                }

                $sumPontuacoesPartiallyUsed = $this->find('all')
                    ->select(
                        [
                            'sum'
                            => $this
                                ->find('all')
                                ->func()
                                ->sum('quantidade_gotas')
                        ]
                    )
                    ->where($lastPontuacaoConditions)
                    ->first();


                // pega a soma de pontuações já usadas na 'emissão' de brindes

                $fullyPontuacoesWithBrindeUsedConditions = $conditions;

                array_push(
                    $fullyPontuacoesWithBrindeUsedConditions,
                    [
                        'brindes_id IS NOT NULL'
                    ]
                );

                if (count($pontuacoesComprovantesInvalidatedIds) > 0) {
                    array_push(
                        $fullyPontuacoesWithBrindeUsedConditions,
                        [
                            'OR' =>
                            [
                                'pontuacoes_comprovante_id NOT IN ' => $pontuacoesComprovantesInvalidatedIds,
                                'pontuacoes_comprovante_id IS NULL'
                            ]
                        ]
                    );
                }

                $sumPontuacoesWithBrindeFullyUsed
                    = $this
                    ->find('all')
                    ->select(
                        [
                            'sum'
                            => $this
                                ->find('all')
                                ->func()
                                ->sum('quantidade_gotas')
                        ]
                    )
                    ->where($fullyPontuacoesWithBrindeUsedConditions)
                    ->first();

                $difference = $sumPontuacoesWithBrindeFullyUsed->sum - $sumPontuacoesPartiallyUsed->sum;
            } else {
                // não encontrou o último pendente. então devo pegar o último que foi
                // usado totalmente (código 2) mas que não tem brinde vinculado

                $conditionsPontuacoesFullyUsed = $conditions;

                array_push(
                    $conditionsPontuacoesFullyUsed,
                    [
                        'brindes_id IS NULL'
                    ]
                );
                array_push(
                    $conditionsPontuacoesFullyUsed,
                    [
                        'utilizado' => Configure::read('dropletsUsageStatus')['FullyUsed']
                    ]
                );

                if (count($pontuacoesComprovantesInvalidatedIds) > 0) {
                    array_push(
                        $conditionsPontuacoesFullyUsed,
                        [
                            'OR' =>
                            [
                                'pontuacoes_comprovante_id NOT IN ' => $pontuacoesComprovantesInvalidatedIds,
                                'pontuacoes_comprovante_id IS NULL'
                            ]
                        ]
                    );
                }

                $sumPontuacoesFullyUsed
                    = $this
                    ->find('all')
                    ->select(
                        [
                            'sum'
                            => $this
                                ->find('all')
                                ->func()
                                ->sum('quantidade_gotas')
                        ]
                    )
                    ->where($conditionsPontuacoesFullyUsed)
                    ->first();

                $sumPontuacoesFullyUsed['sum'] = is_null($sumPontuacoesFullyUsed['sum']) ? 0 : $sumPontuacoesFullyUsed['sum'];

                // pega a soma de pontuações já usadas na 'emissão' de brindes

                $fullyPontuacoesWithBrindeUsedConditions = $conditions;

                array_push(
                    $fullyPontuacoesWithBrindeUsedConditions,
                    [
                        'brindes_id IS NOT NULL'
                    ]
                );

                if (count($pontuacoesComprovantesInvalidatedIds) > 0) {
                    array_push(
                        $fullyPontuacoesWithBrindeUsedConditions,
                        [
                            'OR' =>
                            [
                                'pontuacoes_comprovante_id NOT IN ' => $pontuacoesComprovantesInvalidatedIds,
                                'pontuacoes_comprovante_id IS NULL'
                            ]
                        ]
                    );
                }

                $sumPontuacoesWithBrindeFullyUsed
                    = $this
                    ->find('all')
                    ->select(
                        [
                            'sum'
                            => $this
                                ->find('all')
                                ->func()
                                ->sum('quantidade_gotas')
                        ]
                    )
                    ->where($fullyPontuacoesWithBrindeUsedConditions)
                    ->first();

                $sumPontuacoesWithBrindeFullyUsed['sum']
                    = is_null($sumPontuacoesWithBrindeFullyUsed['sum'])
                    ? 0
                    : $sumPontuacoesWithBrindeFullyUsed['sum'];

                $difference = $sumPontuacoesWithBrindeFullyUsed->sum - $sumPontuacoesFullyUsed->sum;
            }

            return $difference;
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao obter pontuações: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
        }
    }

    /**
     * Obtem soma de pontuações de um estabelecimento/rede
     *
     * Obtem soma de gotas, valor_moeda_venda e valor_gota_sefaz de um estabelecimento/rede
     *
     * PontuacoesTable::getSumPontuacoesIncoming
     *
     * @param integer $redesId Id da Rede
     * @param array $clientesIds Lista de Clientes
     * @param DateTime $minDate Data Mínima
     * @param DateTime $maxDate Data Máxima
     *
     * @return \App\Model\Entity\Pontuacao Soma de Valores de pontuações
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-11-28
     */
    public function getSumPontuacoesIncoming(int $redesId = 0, array $clientesIds = [], \DateTime $minDate = null, DateTime $maxDate = null)
    {
        try {
            $queryConditions = function (QueryExpression $exp) use ($redesId, $clientesIds, $minDate, $maxDate) {
                if (!empty($redesId)) {
                    $exp->eq("Redes.id", $redesId);
                }

                if (count($clientesIds) > 0) {
                    $exp->in("Clientes.id", $clientesIds);
                }

                if (!empty($minDate)) {
                    $exp->gte("DATE_FORMAT(Pontuacoes.data, '%Y-%m-%d %H:%i:%s')", $minDate->format("Y-m-d 00:00:00"));
                }

                if (!empty($maxDate)) {
                    $exp->lte("DATE_FORMAT(Pontuacoes.data, '%Y-%m-%d %H:%i:%s')", $maxDate->format("Y-m-d 23:59:59"));
                }

                return $exp->isNotNull("Pontuacoes.gotas_id");
            };

            $selectList = [
                "soma_gotas" => "IF (Pontuacoes.quantidade_gotas > 0, ROUND(SUM(Pontuacoes.quantidade_gotas), 2), 0)",
                "soma_reais" => "IF (Pontuacoes.valor_moeda_venda > 0, ROUND(SUM(Pontuacoes.valor_moeda_venda), 2), 0)",
                "soma_gota_sefaz" => "IF (Pontuacoes.valor_gota_sefaz > 0, ROUND(SUM(Pontuacoes.valor_gota_sefaz), 2), 0)"
            ];

            return $this
                ->find('all')
                ->where($queryConditions)
                ->select($selectList)
                ->contain(['Clientes.RedesHasClientes.Redes'])
                ->first();
        } catch (Throwable $th) {
            $message = sprintf("[%s] %s", MSG_LOAD_EXCEPTION, $th->getMessage());
            Log::write("error", $message);
            throw new Exception($message);
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

            return $this
                ->find('all')
                ->where($whereConditions)
                // TODO: olhar pq não está agrupando
                // ->group(['usuarios_id'])
                ->contain(['Usuarios']);
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao obter pontuações: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write('error', $trace);
        }
    }

    /**
     * Obtem dados de relatório
     *
     * Obtêm dados de pontuações que estão diretamente relacionados à Gotas de um Estabelecimento
     *
     * PontuacoesTable.php::getPontuacoesGotasMovimentationForClientes
     *
     * @param int $clientesId Clientes (Postos)
     * @param int $gotasId Id de Gota
     * @param int $funcionariosId Id de Funcionário
     * @param DateTime $dataInicio Data Inicio
     * @param DateTime $dataFim Data fim
     * @param string $tipoRelatorio Tipo Relatório Analítico / Sintético
     *
     * @return \App\Model\Entity\Pontuaco[] Array de pontuacoes
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-09-21
     */
    public function getPontuacoesGotasMovimentationForClientes(int $clientesId = null, int $gotasId = null, int $funcionariosId = null, DateTime $dataInicio = null, DateTime $dataFim = null, string $tipoRelatorio = REPORT_TYPE_SYNTHETIC)
    {
        try {
            $whereConditions = [];
            // Irá trazer de um posto ou todos os postos que o usuário tem acesso (conforme tipo_perfil)
            $whereConditions[] = ["Pontuacoes.clientes_id" => $clientesId];

            if (!empty($gotasId)) {
                $whereConditions[] = ["Pontuacoes.gotas_id" => $gotasId];
            } else {
                $notNull = function (QueryExpression $exp) {
                    return $exp->isNotNull("Pontuacoes.gotas_id");
                };
                $whereConditions[] = $notNull;
            }

            if (!empty($funcionariosId)) {
                $whereConditions[] = ["Pontuacoes.funcionarios_id" => $funcionariosId];
            }

            if (!empty($dataInicio)) {
                $whereConditions[] = ["Pontuacoes.data >= " => $dataInicio];
            }

            if (!empty($dataFim)) {
                $whereConditions[] = ["Pontuacoes.data <= " => $dataFim];
            }

            $order = [];
            $groupConditions = [];

            $selectList = [
                "quantidade_litros" => "ROUND(SUM(Pontuacoes.quantidade_multiplicador), 2)",
                "quantidade_gotas" => "SUM(Pontuacoes.quantidade_gotas)",
                "quantidade_reais" => "ROUND(SUM(Pontuacoes.valor_gota_sefaz), 2)"
            ];

            $join = [];

            if ($tipoRelatorio == REPORT_TYPE_ANALYTICAL) {
                $selectList = [
                    "quantidade_litros" => "ROUND(SUM(Pontuacoes.quantidade_multiplicador), 2)",
                    "quantidade_gotas" => "SUM(Pontuacoes.quantidade_gotas)",
                    "quantidade_reais" => "ROUND(SUM(Pontuacoes.valor_gota_sefaz), 2)",
                    "Gotas.nome_parametro",
                    "Funcionarios.id",
                    "Funcionarios.nome",
                    "Funcionarios.email",
                    "ano" => "YEAR(Pontuacoes.data)",
                    "mes" => "MONTH(Pontuacoes.data)",
                ];

                $join = [
                    "Gotas",
                    "Usuarios",
                    "Brindes",
                    "Funcionarios"
                ];

                $groupConditions = [
                    "Pontuacoes.funcionarios_id",
                    "Gotas.id",
                    "ano",
                    "mes"
                ];

                $order = [
                    "ano" => "ASC",
                    "mes" => "ASC",
                    "Funcionarios.nome" => "ASC"
                ];
            }

            $pontuacoes = $this
                ->find("all")
                ->where($whereConditions)
                ->contain($join)
                ->group($groupConditions)
                ->select($selectList)
                ->order($order);

            if ($tipoRelatorio == REPORT_TYPE_SYNTHETIC) {
                return $pontuacoes->first();
            } else {
                return $pontuacoes;
            }
        } catch (Throwable $th) {
            $message = sprintf("[%s] %s", MSG_LOAD_EXCEPTION, $th->getMessage());
            $code = MSG_LOAD_EXCEPTION_CODE;
            Log::write("error", sprintf("%s - %s", $code, $message));
            throw new Exception($message, $code);
        }
    }

    /**
     * Obtem dados de pontuações
     *
     * Obtem dados de pontuações de entrada e saída para relatório
     *
     * PontuacoesTable.php::getPontuacoesInOutForClientes
     *
     * @param int $clientesId Clientes (Postos)
     * @param integer $gotasId Id de Gota
     * @param integer $funcionariosId Id de Funcionário
     * @param DateTime $dataInicio Data Inicio
     * @param DateTime $dataFim Data fim
     * @param string $tipoMovimentacao Entrada / Saída
     * @param string $tipoRelatorio Tipo Relatório Analítico / Sintético
     *
     * @return \App\Model\Entity\Pontuaco[] Array de pontuacoes
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-09-10
     */
    public function getPontuacoesInForClientes(int $clientesId, int $gotasId = null, int $funcionariosId = null, DateTime $dataInicio = null, DateTime $dataFim = null, string $tipoMovimentacao = TYPE_OPERATION_IN, string $tipoRelatorio = REPORT_TYPE_SYNTHETIC)
    {
        try {
            $whereConditions = [];
            $groupConditions = [
                "periodo",
                "Pontuacoes.clientes_id",
                "Clientes.id"
            ];

            $selectList = [
                "periodo" => "DATE_FORMAT(Pontuacoes.data, '%Y-%m')",
                "qte_gotas" => "SUM(Pontuacoes.quantidade_gotas)",
                "Funcionarios.id",
                "Funcionarios.nome",
                "Clientes.id",
                "Clientes.nome_fantasia",
                "Clientes.municipio",
                "Clientes.estado"
            ];

            if ($tipoRelatorio == REPORT_TYPE_ANALYTICAL) {
                $selectList["periodo"] = "DATE_FORMAT(Pontuacoes.data, '%Y-%m-%d')";
            }

            $join = [
                "Gotas",
                "Usuarios",
                "Clientes",
                "Funcionarios",
                "PontuacoesComprovantes"
            ];

            // Irá trazer de um posto ou todos os postos que o usuário tem acesso (conforme tipo_perfil)
            $whereConditions[] = ["Pontuacoes.clientes_id" => $clientesId];

            if (!empty($gotasId)) {
                $whereConditions[] = ["Pontuacoes.gotas_id" => $gotasId];
            }

            if (!empty($funcionariosId)) {
                $whereConditions[] = ["Funcionarios.id" => $funcionariosId];
            }

            if (!empty($dataInicio)) {
                $whereConditions[] = ["Pontuacoes.data >= " => $dataInicio];
            }

            if (!empty($dataFim)) {
                $whereConditions[] = ["Pontuacoes.data <= " => $dataFim];
            }

            if ($tipoMovimentacao == TYPE_OPERATION_IN) {
                $whereConditions[] = "Pontuacoes.brindes_id IS NULL";
            } else {
                $whereConditions[] = "Pontuacoes.brindes_id IS NOT NULL";
            }

            // Elimina os cupons cancelados

            $whereConditions[] = ["PontuacoesComprovantes.cancelado" => 0];

            if ($tipoRelatorio == REPORT_TYPE_ANALYTICAL) {
                $groupConditions[] = "Usuarios.id";
                $groupConditions[] = "Gotas.id";
                $selectList[] = "Usuarios.id";
                $selectList[] = "Usuarios.nome";
                $selectList[] = "Gotas.id";
                $selectList[] = "Gotas.nome_parametro";
            }

            return $this
                ->find("all")
                ->where($whereConditions)
                ->contain($join)
                ->group($groupConditions)
                ->order(["periodo" => "ASC"])
                ->select($selectList);
        } catch (Throwable $th) {
            $message = sprintf("[%s] %s", MSG_LOAD_EXCEPTION, $th->getMessage());
            $code = MSG_LOAD_EXCEPTION_CODE;
            Log::write("error", sprintf("%s - %s", $code, $message));
            throw new Exception($message, $code);
        }
    }

    /**
     * PontuacoesTable::getExtratoPontuacoesOfUsuario
     *
     * Obtem extrato de pontos do usuário
     *
     * @param integer $usuariosId Id de Usuário
     * @param integer $redesId Id da rede
     * @param array $clientesIds Id das Unidades de Atendimento
     * @param string $brindesNome Nome do Brinde
     * @param string $gotasNomeParametro Nome da Gota de pesquisa
     * @param bool $tipoOperacao Tipo de Operacao (1 = entrada / 0 = saída)
     * @param string $dataInicio Data Início (Formato YYYY-MM-DD)
     * @param string $dataFim Data Fim (Formato YYYY-MM-DD)
     * @param array $orderConditions Condições de Ordenação
     * @param array $paginationConditions Condições de paginação
     *
     * @author Gustavo Souza Gonçalves <gustavosg@rvtecnologia.com.br>
     * @date 23/07/2018
     *
     * @return array $resultado
     */
    public function getExtratoPontuacoesOfUsuario(
        int $usuariosId,
        int $redesId = null,
        array $clientesIds = array(),
        int $tipoOperacao = null,
        string $brindesNome = "",
        string $gotasNomeParametro = "",
        string $dataInicio = null,
        string $dataFim = null,
        array $orderConditions = array(),
        array $paginationConditions = array()
    ) {
        try {

            $pontuacoesComprovantesTable = TableRegistry::get("PontuacoesComprovantes");
            $brindesTable = $this->Brindes;
            $gotasTable = $this->Gotas;

            $whereConditions = array(
                "Pontuacoes.usuarios_id" => $usuariosId
            );

            $clientesBrindesHabilitadosIds = array();

            // Se for por rede, pega o id de todas as redes da rede
            if ($redesId > 0 && count($clientesIds) == 0) {
                $redesHasClientesTable = TableRegistry::get("RedesHasClientes");

                $clientesIds = $redesHasClientesTable->getClientesIdsFromRedesHasClientes($redesId);
            }

            // Senão, verifica se $clientesIds está com valor, se tiver, adiciona na pesquisa
            if (count($clientesIds) > 0) {
                $whereConditions[] = array("Pontuacoes.clientes_id in " => $clientesIds);
            };

            /**
             * Se nome do brinde foi utilizado para pesquisa, verifica quais são os brindes
             */
            if (!empty($brindesNome) && strlen($brindesNome) > 0) {

                // Busca todos os brindes

                $brindesIds = $brindesTable->getBrindesIds(null, array(), null, $brindesNome);

                // Se não achar o brinde, não tem problema, pois o nome do parâmetro é o mesmo para gotas
                // if (count($brindesIds) > 0) {
                //     // @todo ajustar
                //     $clientesBrindesHabilitadosIds = $brindesTable->getBrindesHabilitadosIds($brindesIds, $clientesIds);
                //     $whereConditions[] = array(
                //         "OR" =>
                //         array(
                //             "brindes_id in " => $clientesBrindesHabilitadosIds,
                //             "brindes_id IS NULL"
                //         )
                //     );
                // } else {
                //     $whereConditions[] = array("brindes_id IS NULL");
                // }
            }

            // DebugUtil::print($whereConditions);

            if (!empty($dataInicio)) {
                $dataInicio = date_format(DateTime::createFromFormat("d/m/Y", $dataInicio), "Y-m-d");
            }

            if (!empty($dataFim)) {
                $dataFim = date_format(DateTime::createFromFormat("d/m/Y", $dataFim), "Y-m-d");
            }

            // Se não especificar data, filtra todo mundo
            if ($dataInicio && $dataFim) {
                $whereConditions[] = ["Pontuacoes.data >= " => $dataInicio];
                $whereConditions[] = ["Pontuacoes.data <= " => $dataFim];
            } else if ($dataInicio) {
                $whereConditions[] = ["Pontuacoes.data >= " => $dataInicio];
            } else if ($dataFim) {
                $whereConditions[] = ["Pontuacoes.data <= " => $dataFim];
            }

            $whereConditions[] = ["OR" => ["PontuacoesComprovantes.cancelado" => 0, "Pontuacoes.brindes_id IS NOT NULL"]];

            $pontuacoesQuery = $this->find("all")
                ->where($whereConditions)
                ->contain(["PontuacoesComprovantes"])
                ->order($orderConditions);

            // DebugUtil::printArray($pontuacoesQuery->toArray());

            $todasPontuacoes = $pontuacoesQuery->toArray();
            $pontuacoesAtual = $pontuacoesQuery->toArray();

            // DebugUtil::printArray($todasPontuacoes);

            $retorno = ResponseUtil::prepareReturnDataPagination($todasPontuacoes, $pontuacoesAtual, "pontuacoes", $paginationConditions);

            if ($retorno["mensagem"]["status"] == 0) {
                return $retorno;
            }

            if ($tipoOperacao < 2) {
                $isCompra = $tipoOperacao == 1;
                $isBrinde = $tipoOperacao == 0;
            } else {
                $isCompra = true;
                $isBrinde = true;
            }

            // return ResponseUtil::successAPI('', $todasPontuacoes);

            $pontuacoesRetorno = array();
            foreach ($todasPontuacoes as $key => $pontuacao) {

                if (!is_null($pontuacao["pontuacoes_comprovante_id"]) && $isCompra) {

                    $comprovante = $pontuacoesComprovantesTable->find("all")
                        ->where(
                            array(
                                "id" => $pontuacao["pontuacoes_comprovante_id"],
                                "cancelado" => 0
                            )
                        )->first();

                    $gota = null;
                    if (!empty($gotasNomeParametro) && strlen($gotasNomeParametro) > 0) {
                        $gota = $gotasTable->getGotasByIdNome($pontuacao["gotas_id"], $gotasNomeParametro);
                    } elseif (!empty($pontuacao["gotas_id"])) {
                        $gota = $gotasTable->getGotasById($pontuacao["gotas_id"]);
                    }
                    if (!empty($gota)) {
                        $pontuacao["gotas"] = $gota;
                        $pontuacao["pontuacoes_comprovante"] = $comprovante;
                        $pontuacao["tipo_operacao"] = TYPE_OPERATION_IN;
                        $pontuacoesRetorno[] = $pontuacao;
                    } else {
                        $pontuacao["gotas"] = null;
                        $pontuacoesRetorno[] = $pontuacao;
                    }
                } else if (!empty($pontuacao["brindes_id"]) && $isBrinde) {

                    $brinde = $brindesTable->find("all")
                        ->where(
                            array(
                                "id" => $pontuacao["brindes_id"],
                                "habilitado" => 1
                            )
                        )->first();

                    // return ResponseUtil::successAPI('', $brinde);

                    // $clienteBrindeHabilitado["brinde"] = $brinde;
                    $pontuacao["gotas"] = null;
                    $pontuacao["tipo_operacao"] = TYPE_OPERATION_OUT;

                    $brinde["preco_atual"] = $this->Brindes->PrecoAtual->getUltimoPrecoBrinde($brinde["id"], STATUS_AUTHORIZATION_PRICE_AUTHORIZED);
                    // $pontuacao["brindes_id"] = $brinde;
                    $pontuacao["brinde"] = $brinde;
                    $pontuacoesRetorno[] = $pontuacao;
                }
            }

            /**
             * Agora é feito a paginação.
             * O Motivo é pq como temos pontos obtidos e gastos, os obtidos é que carregam
             * A informação se foi inválido ou não, mas no retorno nós temos N registros
             * diferentes.
             */

            // DebugUtil::printArray($paginationConditions);

            $pagina = 1;
            $limite = count($pontuacoesRetorno);

            $totalPage = count($pontuacoesRetorno);
            $currentPage = count($pontuacoesRetorno);
            if (count($paginationConditions) > 0) {
                $pagina = $paginationConditions["page"];
                $limite = $paginationConditions["limit"];
                $limiteInicial = (($pagina * $limite) - $limite);

                $totalPage = count($pontuacoesRetorno);

                $pontuacoesRetorno = array_slice($pontuacoesRetorno, $limiteInicial, $limite);
                $currentPage = count($pontuacoesRetorno);
            }

            $resultado = array(
                "mensagem" => array(
                    "status" => 1,
                    "message" => Configure::read("messageLoadDataWithSuccess"),
                    "errors" => array()
                ),
                "pontuacoes" => array(
                    "count" => $totalPage,
                    "page_count" => $currentPage,
                    "data" => $pontuacoesRetorno
                )
            );

            return $resultado;
        } catch (Throwable $th) {
            $message = sprintf("[%s] %s", MSG_LOAD_EXCEPTION, $th->getMessage());
            Log::write("error", $message);
            throw new Exception($message);
        }
    }

    public function getMediaGotasAdquiridasUnidadesRedes(int $redesId = 0, array $clientesIds = array())
    {
        # code...
    }

    #endregion

    #region Update

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
            $pontuacoes = $this->query();

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
                $pontuacao = $this->find('all')
                    ->where(['Pontuacoes.id' => $value['id']])
                    ->first();

                if (is_null($pontuacao)) {
                    throw new \Exception(__("Registro de id {0} não encontrado em pontuações!", $value->id));
                }
                $pontuacao->utilizado = $value['utilizado'];

                $this->save($pontuacao);
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
     * @param float $quantidadeGotas Quantidade de gotas acumuladas
     * @param float $quantidadeMultiplicador Quantidade do Multiplicador (Litros de gasolina)
     *
     * @return \App\Model\Entity\Pontuaco $pontuacao Entidade atualizada
     */
    public function updateQuantidadeGotasByPontuacaoId(int $pontuacao_id, float $quantidadeGotas, float $quantidadeMultiplicador)
    {
        try {
            $pontuacoes = $this->query();

            return $pontuacoes->update()
                ->set(
                    array(
                        'quantidade_gotas' => $quantidadeGotas,
                        'quantidade_multiplicador' => $quantidadeMultiplicador
                    )
                )
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

    /**
     * PontuacoesTable::updatePontuacoesPendentesExpiracao
     *
     * Atualiza todas as pontuacoes que não estão expiradas, conforme número de meses da rede estabelecida
     *
     * @param integer $clientesId Id da unidade que irá varrer todos os registros
     * @param integer $tempoExpiracaoGotasUsuarios Tempo de expiração da Rede
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-02-06
     *
     * @return int Número de registros afetados
     */
    public function updatePontuacoesPendentesExpiracao(int $clientesId, int $tempoExpiracaoGotasUsuarios = 6)
    {
        try {
            $camposSet = array(
                "expirado" => 1
            );
            $camposWhere = array(
                "expirado" => 0,
                "gotas_id IS NOT NULL",
                "utilizado <> " => (int) Configure::read("dropletsUsageStatus")["FullyUsed"],
                "TIMESTAMPDIFF(MONTH, data, NOW()) > " => $tempoExpiracaoGotasUsuarios,
                "clientes_id" => $clientesId
            );

            return $this->updateAll($camposSet, $camposWhere);
        } catch (\Exception $e) {
            $stringError = __("Erro ao atualizar registros: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write('error', $e->getTraceAsString());
        }
    }

    #endregion

    #region Delete

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
    #endregion

    public function getPontuacoesClienteFinal($redesId, $dataInicio, $dataFim, $clientesId, $usuarioId)
    {
        try {
            $conds =
                [
                    'Redes.id' => $redesId,
                    'Pontuacoes.data >=' => $dataInicio,
                    'Pontuacoes.data <=' => $dataFim,
                    'Pontuacoes.gotas_id IS NOT NULL',
                    'Usuarios.id' => $usuarioId
                ];

            if (!empty($clientesId)) {
                $conds['Clientes.id'] = $clientesId;
            }

            $joins =
                [
                    'PontuacoesComprovantes',
                    'Clientes' =>
                    [
                        'RedesHasClientes' =>
                        [
                            'Redes'
                        ],
                    ],
                    'Funcionarios',
                    'Usuarios',
                    'Gotas'
                ];
            $order = ['Pontuacoes.data ASC'];
            return $this->find('all')->where($conds)->contain($joins)->order($order)->toArray();
        } catch (\Throwable $th) {
            $message = sprintf("[%s] %s", MSG_LOAD_EXCEPTION, $th->getMessage());
            Log::write("error", $message);
            throw new Exception($message, $th->getCode());
        }
    }
}
