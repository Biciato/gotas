<?php
namespace App\Model\Table;

use ArrayObject;
use Cake\Event\Event;
use Cake\Log\Log;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use App\Custom\RTI\DebugUtil;
use Cake\Core\Configure;
use Cake\I18n\Number;

/**
 * Cupons Model
 *
 * @property \App\Model\Table\BrindesTable|\Cake\ORM\Association\BelongsTo $Brindes
 * @property \App\Model\Table\ClientesTable|\Cake\ORM\Association\BelongsTo $Clientes
 * @property \App\Model\Table\UsuariosTable|\Cake\ORM\Association\BelongsTo $Usuarios
 *
 * @method \App\Model\Entity\Cupom get($primaryKey, $options = [])
 * @method \App\Model\Entity\Cupom newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Cupom[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Cupom|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Cupom patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Cupom[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Cupom findOrCreate($search, callable $callback = null, $options = [])
 */
class CuponsTable extends GenericTable
{
    /**
     * -------------------------------------------------------------
     * Fields
     * -------------------------------------------------------------
     */
    protected $cuponsTable = null;

    protected $cuponsQuery = null;


    /**
     * -------------------------------------------------------------
     * Properties
     * -------------------------------------------------------------
     */

    /**
     * Method get of Cupons table property
     *
     * @return Cake\ORM\Table Table object
     */
    private function _getCuponsTable()
    {
        if (is_null($this->cuponsTable)) {
            $this->_setCuponsTable();
        }
        return $this->cuponsTable;
    }

    /**
     * Method set of Cupons table property
     *
     * @return void
     */
    private function _setCuponsTable()
    {
        $this->cuponsTable = TableRegistry::get('Cupons');
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

        $this->setTable('cupons');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('ClientesHasBrindesHabilitados', [
            'foreignKey' => 'clientes_has_brindes_habilitados_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Clientes', [
            'foreignKey' => 'clientes_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Usuarios', [
            'foreignKey' => 'usuarios_id',
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
            ->integer('tipo_banho')
            ->allowEmpty('tipo_banho');

        $validator
            ->integer('tipo_principal_codigo_brinde')
            ->notEmpty('tipo_principal_codigo_brinde');

        $validator
            ->integer('tipo_secundario_codigo_brinde')
            ->notEmpty('tipo_secundario_codigo_brinde');

        $validator
            ->decimal('valor_pago')
            ->allowEmpty('valor_pago');

        $validator
            ->integer('tipo_venda')
            ->notEmpty('tipo_venda');

        $validator
            ->integer('senha')
            ->allowEmpty('senha');

        $validator
            ->requirePresence('cupom_emitido', 'create')
            ->notEmpty('cupom_emitido');

        $validator
            ->dateTime('data')
            ->requirePresence('data', 'create')
            ->notEmpty('data');

        $validator
            ->boolean('resgatado')
            ->notEmpty('resgatado');

        $validator
            ->boolean('usado')
            ->notEmpty('usado');

        $validator
            ->boolean('quantidade')
            ->allowEmpty('quantidade');

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
        $rules->add($rules->existsIn(['clientes_has_brindes_habilitados_id'], 'ClientesHasBrindesHabilitados'));
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
     * Adiciona cupom de Smart Shower
     *
     * @param int   $clientes_has_brindes_habilitados_id Id de Brinde Habilitado
     * @param int   $clientes_id                         Id de Cliente
     * @param int   $usuarios_id                         Id de Usuário
     * @param int   $tipo_banho                          Tipo do Banho
     * @param int   $tempo_banho                         Tempo de Banho
     * @param float $valorPago                          Valor Pago
     * @param int   $quantidade                          Quantidade Solicitada
     * @param int   $tipoVenda                           Tipo de Venda (0 = Gotas, 1 = Dinheiro)
     *
     * @return \App\Model\Entity\Cupom
     */
    public function addCupomForUsuario(int $clientes_has_brindes_habilitados_id, int $clientes_id, int $usuarios_id, float $valorPago, int $quantidade, int $tipoVenda = 0)
    {
        try {
            $cupom = $this->_getCuponsTable()->newEntity();

            $year = date('Y');
            $month = date('m');
            $day = date('d');
            $hour = date('H');
            $minute = date('i');
            $second = date('s');
            $data = $year . '-' . $month . '-' . $day . ' ' . $hour . ':' . $minute . ':' . $second;

            // Obtem Brinde habilitado no cliente

            $clienteHasBrindeHabilitadoTable = TableRegistry::get("ClientesHasBrindesHabilitados");

            $brindeHabilitado = $clienteHasBrindeHabilitadoTable->getBrindeHabilitadoById($clientes_has_brindes_habilitados_id);

            /**
             * Para saber se o brinde é banho ou não (e como será 'calculado' os códigos primários e secundários),
             * preciso pegar qual é o Tipo Brindes Cliente associado ào registro e ver o que realmente está configurado.
             * Se for menor ou igual a 4, é banho. Aí, o tempo de banho tem que ser adicionado 10.
             * Se não for, é a configuração passada.
             */

            // Obtem Tipo Brinde Cliente configurado

            $tiposBrindesClientesTable = TableRegistry::get("TiposBrindesClientes");

            $tiposBrindesCliente = $tiposBrindesClientesTable->getTiposBrindesClientesById($brindeHabilitado["tipos_brindes_clientes_id"]);

            /**
             *  TODO: Deve ser feito a lógica de geração do cupom caso o brinde não seja lido por um equipamento rti
             * Isto é, se for leitura por leitor comum, e não equipamento rti,
             * o código pode ser usado conforme lógica antiga de brinde
             */

            $tipoPrincipalCodigoBrinde = $tiposBrindesCliente["tipo_principal_codigo_brinde"];
            $tipoSecundarioCodigoBrinde = $tiposBrindesCliente["tipo_secundario_codigo_brinde"];

            if ($tipoPrincipalCodigoBrinde <= 4) {
                // Validação se é banho ou brinde comum. Se for banho, adiciona + 10
                $tipoSecundarioCodigoBrinde = $tipoSecundarioCodigoBrinde + 10;
            } else {
                // Se não é banho, apenas verifica se o tamanho é 1. se for, coloca um 0 na frente
                $tipoSecundarioCodigoBrinde = strlen($tipoSecundarioCodigoBrinde) == 1 ? '0' . $tipoSecundarioCodigoBrinde : $tipoSecundarioCodigoBrinde;
            }

            $tipoSecundarioCodigoBrinde = $tipoPrincipalCodigoBrinde <= 4 ? $brindeHabilitado["brinde"]["tempo_rti_shower"] + 10 : $tiposBrindesCliente["tipo_secundario_codigo_brinde"];

            /**
             * Se o brinde não for banho, pode acontecer do código secundário ter tamanho 1.
             * É necessário aumentar o tamanho concatenando um 0 na frente;
             */
            if (strlen($tipoSecundarioCodigoBrinde) == 1) {
                $tipoSecundarioCodigoBrinde = __("{0}{1}", "0", $tipoSecundarioCodigoBrinde);
            }

            // Obtem cliente

            $cliente = $this->_getCuponsTable()->Clientes->getClienteById($clientes_id);

            // Pega todas as senhas emitidas no dia para saber qual é a próxima
            $qteSenhas = $this->_getCuponsTable()->find('all')
                ->order(['senha' => 'desc'])
                ->where(['clientes_id' => $clientes_id, 'data like' => '%' . date('Y-m-d') . '%'])->first()['senha'];

            // Processo de Gravação
            $cupom->clientes_has_brindes_habilitados_id = $clientes_has_brindes_habilitados_id;
            $cupom->clientes_id = $clientes_id;
            $cupom->usuarios_id = $usuarios_id;
            $cupom->tipo_principal_codigo_brinde = $tipoPrincipalCodigoBrinde;
            $cupom->tipo_secundario_codigo_brinde = $tipoSecundarioCodigoBrinde;
            $cupom->valor_pago = $valorPago;
            $cupom->senha = $qteSenhas + 1;
            $cupom->data = $data;
            $cupom->quantidade = $quantidade;
            $cupom->tipo_venda = $tipoVenda;

            /**
             * Se Smart Shower, já considera resgatado
             * pois o smart shower é impresso na hora.
             * Senão, false.
             */
            $cupom->resgatado = $tipoPrincipalCodigoBrinde <= 4;

            // Usado é automatico após 24 horas se for brinde de banho.
            // Se não for, é atribuido quando faz o resgate
            $cupom->usado = $tipoPrincipalCodigoBrinde <= 4;

            // Antes do save, calcular cupom emitido

            $identificador_cliente = $cliente->codigo_rti_shower;

            if (strlen($identificador_cliente) == 1) {
                $identificador_cliente = '0' . $identificador_cliente;
            }

            $ano_cupom = substr($year, 2, 2) + 10;
            $mes_cupom = $month + 10;
            $dia_cupom = $day + 10;

            $senha = $qteSenhas == null ? 1 : $qteSenhas + 1;

            if (strlen($senha) == 1) {
                $senha = '00' . $senha;
            } elseif (strlen($senha) == 2) {
                $senha = '0' . $senha;
            }

            $cupom->cupom_emitido = __(
                "{0}{1}{2}{3}{4}{5}{6}",
                $identificador_cliente,
                $ano_cupom,
                $mes_cupom,
                $dia_cupom,
                $tipoPrincipalCodigoBrinde,
                $tipoSecundarioCodigoBrinde,
                $senha
            );

            $cupom = $this->_getCuponsTable()->save($cupom);
            $cupom = $this->find()->where(array("id" => $cupom["id"]))->first();
            $cupom["valor_pago"] = Number::precision($cupom["valor_pago"], 2);
            return $cupom;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao editar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Adiciona brindes para o usuário
     *
     * @param App\Entity\Table\Clientes_Has_Brindes_Habilitado $brinde_habilitado
     * @param App\Entity\Table\Usuario                         $usuario
     * @param int                                              $quantidade
     * @return void
     *
     * @deprecated 1.0 Versão não será mais utilizada, pois perdeu o sentido desta função com a regra de Tipos no sistema
     */
    public function addCuponsBrindesForUsuario($brinde_habilitado, $usuarios_id, $quantidade)
    {
        try {
            $cupom_emitido = bin2hex(openssl_random_pseudo_bytes(7));

            $usuarios_table = TableRegistry::get('Usuarios');

            $usuario = $usuarios_table->getUsuarioById($usuarios_id);

            // verifica se ja teve um cupom com essa sequencia. se sim, gera outro cupom

            while ($this->_getCuponsTable()->getCupomByCupomEmitido($cupom_emitido)) {
                $cupom_emitido = bin2hex(openssl_random_pseudo_bytes(7));
            }

            $cupom = $this->_getCuponsTable()->newEntity();

            $cupom->clientes_has_brindes_habilitados_id = $brinde_habilitado->id;
            $cupom->clientes_id = $brinde_habilitado->clientes_id;
            $cupom->usuarios_id = $usuario->id;
            $cupom->valor_pago = $brinde_habilitado->brinde_habilitado_preco_atual->preco * $quantidade;
            $cupom->cupom_emitido = $cupom_emitido;
            $cupom->resgatado = false;
            $cupom->data = date("Y-m-d H:i:s");
            $cupom->quantidade = $quantidade;

            return $this->_getCuponsTable()->save($cupom);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao editar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /* ------------------------ Read ------------------------ */

    /**
     * Retorna cupons pelo valor de cupom emitido
     * (campo que identifica cada cupom no código de leitura)
     *
     * @param  string $cupom_emitido String de cupom emitido
     * @return object $cupom
     */
    public function getCupomByCupomEmitido(string $cupom_emitido)
    {
        try {
            return $this->_getCuponsTable()->find('all')
                ->where(['Cupons.cupom_emitido' => $cupom_emitido])->contain(['ClientesHasBrindesHabilitados', 'Clientes', 'ClientesHasBrindesHabilitados.Brindes', 'Usuarios'])->first();
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao editar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Retorna cupons pelo valor de cupom emitido
     * (campo que identifica cada cupom no código de leitura)
     *
     * @param  string $cupom_emitido String de cupom emitido
     * @param array $clientesIds Ids de Clientes à pesquisar (opcional)
     *
     * @return object $cupom
     */
    public function getCuponsByCupomEmitido(string $cupom_emitido, array $clientesIds = array())
    {
        try {

            $whereConditions = array();

            $whereConditions[] = array('Cupons.cupom_emitido' => $cupom_emitido);

            if (sizeof($clientesIds) > 0) {
                $whereConditions[] = array("Cupons.clientes_id IN " => $clientesIds);
            }

            return $this->_getCuponsTable()->find('all')
                ->where($whereConditions)
                ->contain(
                    [
                        "ClientesHasBrindesHabilitados",
                        "Clientes.RedeHasCliente",
                        "ClientesHasBrindesHabilitados.Brindes",
                        "Usuarios",
                    ]
                );
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao editar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * CuponsTable::getCupons
     * Pesquisa de Cupons conforme parâmetros informados
     *
     * @param array $whereConditions      Condições de pesquisa
     * @param array $orderConditions      Condições de ordenação
     * @param array $paginationConditions Condições de Paginação
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 2018/05/13
     *
     * @return array('count', 'data') \App\Model\Entity\Cupom[] Lista de Cupons
     */
    public function getCupons(array $whereConditions, array $tiposBrindesClienteConditions = array(), array $orderConditions = array(), array $paginationConditions = array())
    {
        try {

            /**
             * Nesta pesquisa, se o usuário informar Condições de Tipo Brindes Clientes,
             * a pesquisa será particularmente pelo tipo principal de código de brinde.
             * Mas foi deixado como array, pois esta pesquisa pode ampliar no futuro
             *
             * A intenção desta pesquisa, é apenas capturar os ids de
             * Clientes Has Brindes Habilitados
             * que serão filtrados
             */

            $tiposBrindesClientesIds = array();
            $clientesHasBrindesHabilitadosIds = array();
            if (sizeof($tiposBrindesClienteConditions) > 0) {

                $tiposBrindesClientesTable = TableRegistry::get("TiposBrindesClientes");

                $tiposBrindesClientesIds = $tiposBrindesClientesTable->getTiposBrindesClientesIdsFromConditions($tiposBrindesClienteConditions);

                $clientesHasBrindesHabilitadosConditions = array(
                    "tipos_brindes_clientes_id in " => $tiposBrindesClientesIds
                );

                $clientesHasBrindesHabilitadosTable = TableRegistry::get("ClientesHasBrindesHabilitados");
                $clientesHasBrindesHabilitadosIds = $clientesHasBrindesHabilitadosTable->getBrindesHabilitadosIdsFromConditions($clientesHasBrindesHabilitadosConditions);
            }

            if (sizeof($clientesHasBrindesHabilitadosIds) > 0) {
                $whereConditions[] = array("clientes_has_brindes_habilitados_id in " => $clientesHasBrindesHabilitadosIds);
            }

            $cupons = $this->_getCuponsTable()->find('all')
                ->contain(["ClientesHasBrindesHabilitados.Brindes"])
                ->where($whereConditions);

            $dataTodosCupons = $cupons->toArray();

            $count = $cupons->count();

            $retorno = $this->prepareReturnDataPagination($dataTodosCupons, $cupons->toArray(), "cupons", $paginationConditions);

            if ($retorno["mensagem"]["status"] == 0) {
                return $retorno;
            }

            if (sizeof($orderConditions) > 0) {
                $cupons = $cupons->order($orderConditions);
            }

            if (sizeof($paginationConditions) > 0) {
                $cupons = $cupons->limit($paginationConditions["limit"])
                    ->page($paginationConditions["page"]);
            }

            $retorno = $this->prepareReturnDataPagination($dataTodosCupons, $cupons->toArray(), "cupons", $paginationConditions);

            return $retorno;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar cupons: " . $e->getMessage());

            Log::write('error', $stringError);
            Log::write('error', $trace);

            return $stringError;
        }
    }

    /**
     * Reprint a ticket
     *
     * @param int $id
     * @param int $clientes_has_brindes_habilitados_id
     * @param int $clientes_id
     * @param int $usuarios_id
     * @param string $data
     * @return object $cupons[]
     */
    public function getCupomToReprint(int $id, int $clientes_has_brindes_habilitados_id, int $clientes_id, int $usuarios_id, string $data)
    {
        try {
            $cupons = $this->_getCuponsTable()->find('all')
                ->where(
                    [
                        'id' => $id,
                        'clientes_has_brindes_habilitados_id' => $clientes_has_brindes_habilitados_id,
                        'clientes_id' => $clientes_id,
                        'usuarios_id' => $usuarios_id,
                        'data' => $data
                    ]
                )->first();

            return $cupons;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao editar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Get tickets by cliente id
     *
     * @param array $clientes_ids Ids de clientes
     *
     * @return void
     */
    public function getCuponsById(int $id)
    {
        try {
            $cupons = $this->_getCuponsTable()->find('all')
                ->where(
                    [
                        'Cupons.id' => $id
                    ]
                )
                ->contain(
                    [
                        'ClientesHasBrindesHabilitados',
                        'Clientes',
                        'Usuarios', 'ClientesHasBrindesHabilitados.Brindes'
                    ]
                )->first();

            return $cupons;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Get tickets by cliente id
     *
     * @param array $clientes_ids Ids de clientes
     *
     * @return void
     */
    public function getExtratoCuponsClientes(array $clientesIds = [], int $brindeSelecionado = null, string $nomeUsuarios = null, float $valorMinimo = null, float $valorMaximo = null, string $dataInicio = null, string $dataFim = null)
    {
        try {

            $whereConditions = array();

            if (sizeof($clientesIds) > 0) {
                $whereConditions[] = array("Cupons.clientes_id IN" => $clientesIds);
            }

            if (!empty($brindeSelecionado)){
                $whereConditions[] = array("Brindes.id" => $brindeSelecionado);
            }

            $whereConditions[] = array("Usuarios.nome LIKE '%{$nomeUsuarios}%'");
            $whereConditions[] = array("Cupons.valor_pago BETWEEN '{$valorMinimo}' AND '{$valorMaximo}'");
            $whereConditions[] = array("Cupons.data BETWEEN '{$dataInicio}' AND '{$dataFim}'");

            $cupons = $this->_getCuponsTable()->find('all')
                ->where(
                    $whereConditions
                )
                ->contain(
                    ['ClientesHasBrindesHabilitados', 'Clientes', 'Usuarios', 'ClientesHasBrindesHabilitados.Brindes']
                    // ['ClientesHasBrindesHabilitados', 'Clientes', 'Usuarios', 'Brindes']
                );

            return $cupons;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /* ------------------------ Update ------------------------ */

    /**
     * Define todas as gotas de um cliente para a matriz
     *
     * @param int $clientes_id Id de Cliente
     * @param int $matriz_id   Id da Matriz
     *
     * @return boolean
     */
    public function setCuponsToMainCliente(int $clientes_id, int $matriz_id)
    {
        try {
            return $this->updateAll(
                [
                    'clientes_id' => $matriz_id,
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

            $stringError = __("Erro ao buscar registro: {0}, em {1}", $e->getMessage(), $object['file']);

            Log::write('error', $stringError);

            $error = ['result' => false, 'message' => $stringError];
            return $error;
        }
    }

    /**
     * Define o cupom como resgatado
     *
     * @param integer $id
     * @return void
     */
    public function setCupomResgatado(int $id)
    {
        try {
            return $this->updateAll(
                [
                    'resgatado' => 1,
                    'usado' => 1
                ],
                [
                    'id' => $id
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

            $stringError = __("Erro ao buscar registro: {0}, em {1}", $e->getMessage(), $object['file']);

            Log::write('error', $stringError);

            $error = ['result' => false, 'message' => $stringError];
            return $error;
        }
    }

    /**
     * Define o cupom como resgatado e usado
     *
     * @param integer $id
     * @return void
     */
    public function setCupomResgatadoUsado(int $id)
    {
        try {
            return $this->updateAll(
                [
                    'resgatado' => 1,
                    'usado' => 1
                ],
                [
                    'id' => $id
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

            $stringError = __("Erro ao buscar registro: {0}, em {1}", $e->getMessage(), $object['file']);

            Log::write('error', $stringError);

            $error = ['result' => false, 'message' => $stringError];
            return $error;
        }
    }

    /* ------------------------ Delete ------------------------ */

    /**
     * Deleta todos os cupons por um Clientes Id
     *
     * @param array $clientes_ids ids de Clientes
     *
     * @return void
     */
    public function deleteAllCuponsByClientesIds(array $clientes_ids)
    {
        try {
            return $this->_getCuponsTable()
                ->deleteAll(
                    [
                        'clientes_id in' => $clientes_ids
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

            $stringError = __("Erro ao remover registro: {0}, em {1}", $e->getMessage(), $object['file']);

            Log::write('error', $stringError);

            $error = ['result' => false, 'message' => $stringError];
            return $error;
        }
    }

    /**
     * Deleta todos os cupons por um Usuário Id
     *
     * @param int $usuarios_id Id de Usuário
     *
     * @return void
     */
    public function deleteAllCuponsByUsuariosId(int $usuarios_id)
    {
        try {
            return $this->_getCuponsTable()
                ->deleteAll(
                    [
                        'usuarios_id' => $usuarios_id
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

            $stringError = __("Erro ao remover registro: {0}, em {1}", $e->getMessage(), $object['file']);

            Log::write('error', $stringError);

            $error = ['result' => false, 'message' => $stringError];
            return $error;
        }
    }
}
