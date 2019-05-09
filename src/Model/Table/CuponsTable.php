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
use App\Custom\RTI\ResponseUtil;

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

        $this->belongsTo('Brindes', [
            'foreignKey' => 'brindes_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Clientes', [
            'foreignKey' => 'clientes_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Funcionarios', [
            "className" => "Usuarios",
            'foreignKey' => 'usuarios_id',
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
            ->integer('codigo_principal')
            ->notEmpty('codigo_principal');

        $validator
            ->integer('codigo_secundario')
            ->notEmpty('codigo_secundario');

        $validator
            ->decimal('valor_pago_gotas')
            ->allowEmpty('valor_pago_gotas');

        $validator
            ->decimal('valor_pago_reais')
            ->allowEmpty('valor_pago_reais');

        $validator
            ->notEmpty("tipo_venda");
        // ->scalar("tipo_venda", "create")
        // ->add(
        //     "tipo_venda",
        //     "inList",
        //     array(
        //         "rule" => array("inList", array(TYPE_SELL_FREE_TEXT, TYPE_SELL_DISCOUNT_TEXT, TYPE_SELL_CURRENCY_OR_POINTS_TEXT)),
        //         "message" => "É necessário selecionar um Tipo de Venda",
        //         "allowEmpty" => false
        //     )
        // )->notEmpty("tipo_venda");

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
            ->boolean("estornado");

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
        $rules->add($rules->existsIn(['brindes_id'], 'Brindes'));
        $rules->add($rules->existsIn(['clientes_id'], 'Clientes'));
        $rules->add($rules->existsIn(['usuarios_id'], 'Usuarios'));

        return $rules;
    }

    /**
     * -------------------------------------------------------------
     * Methods
     * -------------------------------------------------------------
     */

    #region Create

    /**
     * Adiciona cupom de Smart Shower
     *
     * @param int $brindesId Id de Brinde Habilitado
     * @param int $clientesId Id de Cliente
     * @param int $funcionariosId Id de Funcionário
     * @param int $usuariosId Id de Usuário
     * @param float $valorPagoGotas Valor Pago em Gotas
     * @param float $valorPagoReais Valor Pago em Reais
     * @param int $quantidade Quantidade Solicitada
     * @param int $tipoVenda Tipo de Venda ('Isento', 'Com Desconto', 'Gotas ou Reais')
     *
     * @return \App\Model\Entity\Cupom
     */
    public function addCupomForUsuario(int $brindesId, int $clientesId, int $funcionariosId, int $usuariosId, float $valorPagoGotas = null, float $valorPagoReais = null, int $quantidade, string $tipoVenda = TYPE_SELL_CURRENCY_OR_POINTS_TEXT)
    {
        // @todo Gustavo Verificar onde chama este método, ajustar:
        // 1 - tipo Venda
        // 2 - valorPago => (reais e gotas)

        try {
            $cupom = $this->_getCuponsTable()->newEntity();

            $year = date('Y');
            $month = date('m');
            $day = date('d');
            $hour = date('H');
            $minute = date('i');
            $second = date('s');
            // $data = $year . '-' . $month . '-' . $day . ' ' . $hour . ':' . $minute . ':' . $second;
            $data = date("Y-m-d H:i:s");

            // Obtem Brinde habilitado no cliente

            $brinde = $this->Brindes->getBrindeById($brindesId);

            // DebugUtil::print($brindeHabilitado);

            /**
             *  TODO: Deve ser feito a lógica de geração do cupom caso o brinde não seja lido por um equipamento rti
             * Isto é, se for leitura por leitor comum, e não equipamento rti,
             * o código pode ser usado conforme lógica antiga de brinde
             */

            $codigoPrimario = !empty($brinde["codigo_primario"]) ? $brinde["codigo_primario"] : "0";
            // Brindes["tempo_uso_brinde"]
            $codigoSecundario = !empty($brinde["tempo_uso_brinde"]) ? $brinde["tempo_uso_brinde"] : "00";

            if (is_numeric($codigoSecundario) && $codigoPrimario <= 4) {
                // Validação se é banho ou brinde comum. Se for banho, adiciona + 10
                $codigoSecundario = $codigoSecundario + 10;
            } else {
                // Se não é banho, apenas verifica se o tamanho é 1. se for, coloca um 0 na frente
                $codigoSecundario = strlen($codigoSecundario) == 1 ? '0' . $codigoSecundario : $codigoSecundario;
            }

            // Obtem cliente

            $cliente = $this->Clientes->getClienteById($clientesId);

            // Pega todas as senhas emitidas no dia para saber qual é a próxima
            $qteSenhas = $this->find('all')
                ->order(
                    array("senha" => "desc")
                )
                ->where(
                    array(
                        "clientes_id" => $clientesId,
                        "DATE(data)" => date('Y-m-d')
                    )
                )->first()['senha'];


            // Processo de Gravação
            $cupom["brindes_id"] = $brindesId;
            $cupom["clientes_id"] = $clientesId;
            $cupom["funcionarios_id"] = $funcionariosId;
            $cupom["usuarios_id"] = $usuariosId;
            $cupom["codigo_principal"] = $codigoPrimario;
            $cupom["codigo_secundario"] = $codigoSecundario;
            $cupom["valor_pago_gotas"] = $valorPagoGotas;
            $cupom["valor_pago_reais"] = $valorPagoReais;
            $cupom["senha"] = $qteSenhas + 1;
            $cupom["data"] = $data;
            $cupom["quantidade"] = $quantidade;
            $cupom["tipo_venda"] = $tipoVenda;

            /**
             * Se é um Equipamento RTI, já considera resgatado pois equipamentos RTI são impressos na hora
             * Senão, false.
             */
            // $cupom->resgatado = $codigoPrimario <= 4;
            $cupom["resgatado"] = $brinde["equipamento_rti"] == TYPE_EQUIPMENT_RTI;

            // Usado é automatico após 24 horas se for Equipamento RTI
            // Se não for, é definido como usado, quando é feito a baixa.
            // Por isso, o default deverá ser 0
            $cupom["usado"] = 0;

            // Antes do save, calcular cupom emitido

            $identificador_cliente = $cliente["codigo_equipamento_rti"];

            if (strlen($identificador_cliente) == 1) {
                $identificador_cliente = '0' . $identificador_cliente;
            }

            $anoCupom = substr($year, 2, 2) + 10;
            $mesCupom = $month + 10;
            $diaCupom = $day + 10;

            $senha = $qteSenhas == null ? 1 : $qteSenhas + 1;

            if (strlen($senha) == 1) {
                $senha = '00' . $senha;
            } elseif (strlen($senha) == 2) {
                $senha = '0' . $senha;
            }

            $cupom->cupom_emitido = __(
                "{0}{1}{2}{3}{4}{5}{6}",
                $identificador_cliente,
                $anoCupom,
                $mesCupom,
                $diaCupom,
                $codigoPrimario,
                $codigoSecundario,
                $senha
            );

            $cupom = $this->save($cupom);
            $cupom = $this->find()->where(array("id" => $cupom["id"]))->first();
            unset($cupom["codigo_primario"]);
            unset($cupom["codigo_secundario"]);
            $cupom["valor_pago_gotas"] = Number::precision($cupom["valor_pago_gotas"], 2);
            $cupom["valor_pago_reais"] = Number::precision($cupom["valor_pago_reais"], 2);
            return $cupom;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao editar registro: " . $e->getMessage());

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
            $cupomEmitido = bin2hex(openssl_random_pseudo_bytes(7));

            // verifica se ja teve um cupom com essa sequencia. se sim, gera outro cupom

            while ($this->getCupomByCupomEmitido($cupomEmitido)) {
                $cupomEmitido = bin2hex(openssl_random_pseudo_bytes(7));
            }

            $cupom = $this->newEntity();

            $cupom->brindes_id = $brinde_habilitado->id;
            $cupom->clientes_id = $brinde_habilitado->clientes_id;
            $cupom->usuarios_id = $usuarios_id;
            $cupom->valor_pago_gotas = $brinde_habilitado->preco_atual->preco;
            $cupom->valor_pago_reais = $brinde_habilitado->preco_atual->valor_moeda_venda;
            $cupom->cupom_emitido = $cupomEmitido;
            $cupom->resgatado = false;
            $cupom->data = date("Y-m-d H:i:s");
            $cupom->quantidade = $quantidade;

            return $this->save($cupom);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao editar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    #endregion

    #region Read

    /**
     * Retorna cupons pelo valor de cupom emitido
     * (campo que identifica cada cupom no código de leitura)
     *
     * @param  string $cupomEmitido String de cupom emitido
     * @return object $cupom
     */
    public function getCupomByCupomEmitido(string $cupomEmitido, bool $estornado = null)
    {
        try {

            $whereConditions = array();
            $whereConditions["Cupons.cupom_emitido"] = $cupomEmitido;

            if (isset($estornado)) {
                $whereConditions["Cupons.estornado"] = $estornado;
            }

            return $this->_getCuponsTable()->find('all')
                ->where(
                    $whereConditions
                )->contain(
                    array(
                        'Brindes',
                        'Clientes',
                        'Usuarios'
                    )
                )->first();
            // )->sql();
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
     * @param  string $cupomEmitido String de cupom emitido
     * @param array $clientesIds Ids de Clientes à pesquisar (opcional)
     *
     * @return object $cupom
     */
    public function getCuponsByCupomEmitido(string $cupomEmitido, array $clientesIds = array())
    {
        try {

            $whereConditions = array();

            $whereConditions[] = array('Cupons.cupom_emitido' => $cupomEmitido);

            if (sizeof($clientesIds) > 0) {
                $whereConditions[] = array("Cupons.clientes_id IN " => $clientesIds);
            }

            return $this->_getCuponsTable()->find('all')
                ->where($whereConditions)
                ->contain(
                    array(
                        "Clientes.RedeHasCliente",
                        "Brindes",
                        "Usuarios"
                    )
                );
        } catch (\Exception $e) {
            $trace = $e->getTraceAsString();
            $stringError = __("Erro ao consultar cupom: {0}. [Função: {1} / Arquivo: {2} / Linha: {3} / Detalhes: {4}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__, $trace);

            Log::write('error', $stringError);

            throw new \Exception($stringError);
        }
    }

    /**
     * Obtem todos os cupons
     *
     * @param boolean $resgatado
     * @param boolean $usado
     * @param boolean $equipamentoRTI
     * @param boolean $redeAtiva
     * @param integer $diasAnteriores
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-01-30
     *
     * @return array App\Model\Entity\Cupon
     */
    public function getCuponsResgatadosUsados(bool $resgatado = true, bool $usado = false, bool $equipamentoRTI = null, bool $redeAtiva = true, int $diasAnteriores = 1)
    {
        $whereConditions =   array();

        $whereConditions[] = array("Cupons.resgatado" => $resgatado);
        $whereConditions[] = array("Cupons.usado" => $usado);

        if (isset($equipamentoRTI) && is_bool($equipamentoRTI)) {
            $whereConditions[] = array("TipoBrindeRede.equipamento_rti" => $equipamentoRTI);
        }

        $whereConditions[] = array("Rede.ativado" => $redeAtiva);
        $whereConditions[] = array("Cupons.data <= DATE_SUB(DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:01'), INTERVAL '{$diasAnteriores}' DAY)");

        $cupons = $this->find("all")
            ->contain(
                array(
                    "Brindes",
                    "Clientes.RedesHasClientes.Rede",
                    "Usuarios"
                )
            )
            ->where(
                $whereConditions
            )
            ->select(array(
                "Cupons.id",
                "Cupons.brindes_id",
                "Cupons.clientes_id",
                "Cupons.funcionarios_id",
                "Cupons.usuarios_id",
                "Cupons.codigo_principal",
                "Cupons.codigo_secundario",
                "Cupons.valor_pago_gotas",
                "Cupons.valor_pago_reais",
                "Cupons.tipo_venda",
                "Cupons.senha",
                "Cupons.cupom_emitido",
                "Cupons.data",
                "Cupons.resgatado",
                "Cupons.usado",
                "Cupons.quantidade",
                "Cupons.audit_insert",
                "Cupons.audit_update",
            ));

        $retorno = array();

        foreach ($cupons as $key => $value) {
            $retorno[] = array(
                "id" => $value["id"],
                "brindes_id" => $value["brindes_id"],
                "clientes_id" => $value["clientes_id"],
                "funcionarios_id" => $value["funcionarios_id"],
                "usuarios_id" => $value["usuarios_id"],
                "codigo_principal" => $value["codigo_principal"],
                "codigo_secundario" => $value["codigo_secundario"],
                "valor_pago_gotas" => $value["valor_pago_gotas"],
                "valor_pago_reais" => $value["valor_pago_reais"],
                "tipo_venda" => $value["tipo_venda"],
                "senha" => $value["senha"],
                "cupom_emitido" => $value["cupom_emitido"],
                "data" => $value["data"],
                "resgatado" => $value["resgatado"],
                "usado" => $value["usado"],
                "quantidade" => $value["quantidade"],
                "audit_insert" => $value["audit_insert"],
                "audit_update" => $value["audit_update"],
            );
        }

        return $retorno;
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
    public function getCupons(array $whereConditions, array $orderConditions = array(), array $paginationConditions = array())
    {
        // DebugUtil::print($tiposBrindesClienteConditions);

        $selectArray = array(
            "Cupons.id",
            "Cupons.brindes_id",
            "Cupons.clientes_id",
            "Cupons.funcionarios_id",
            "Cupons.usuarios_id",
            "Cupons.valor_pago_gotas",
            "Cupons.valor_pago_reais",
            "Cupons.tipo_venda",
            "Cupons.senha",
            "Cupons.cupom_emitido",
            "Cupons.data",
            "Cupons.data_validade",
            "Cupons.resgatado",
            "Cupons.usado",
            "Cupons.quantidade",
            "Cupons.audit_insert",
            "Cupons.audit_update",
            "Clientes.nome_fantasia",
            "Clientes.razao_social",
            "Clientes.endereco",
            "Clientes.endereco_numero",
            "Clientes.endereco_complemento",
            "Clientes.bairro",
            "Clientes.municipio",
            "Clientes.estado",
            "Clientes.pais",
            "Clientes.propaganda_img",
            // "Clientes.propaganda_img_completo",
            "Clientes.cep",
            "Clientes.tel_fixo",
            "Clientes.tel_fax",
            "Clientes.tel_celular",
            "Brindes.id",
            "Brindes.clientes_id",
            "Brindes.codigo_primario",
            "Brindes.nome",
            "Brindes.tempo_uso_brinde",
            "Brindes.tipo_venda",
            "Brindes.tipo_codigo_barras",
            "Brindes.ilimitado",
            "Brindes.habilitado",
            "Brindes.preco_padrao",
            "Brindes.valor_moeda_venda_padrao",
            "Brindes.nome_img",
            "Brindes.audit_insert",
            "Brindes.audit_update"
        );

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
        $brindesIds = array();

        if (sizeof($brindesIds) > 0) {
            $whereConditions[] = array("brindes_id in " => $brindesIds);
        }

        $cupons = $this->find('all')
            ->contain(
                array(
                    "Brindes",
                    "Clientes"
                )
            )
            ->where($whereConditions)
            ->select($selectArray);

        $dataTodosCupons = $cupons->toArray();

        $count = $cupons->count();

        $retorno = ResponseUtil::prepareReturnDataPagination($dataTodosCupons, $cupons->toArray(), "cupons", $paginationConditions);

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

        $retorno = ResponseUtil::prepareReturnDataPagination($dataTodosCupons, $cupons->toArray(), "cupons", $paginationConditions);

        return $retorno;
    }

    /**
     * Reprint a ticket
     *
     * @param int $id
     * @param int $brindesId
     * @param int $clientes_id
     * @param int $usuarios_id
     * @param string $data
     * @return object $cupons[]
     */
    public function getCupomToReprint(int $id, int $brindesId, int $clientes_id, int $usuarios_id, string $data)
    {
        try {
            $cupons = $this->_getCuponsTable()->find('all')
                ->where(
                    [
                        'id' => $id,
                        'brindes_id' => $brindesId,
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

            if (!empty($brindeSelecionado)) {
                $whereConditions[] = array("Brindes.id" => $brindeSelecionado);
            }

            $whereConditions[] = array("Usuarios.nome LIKE '%{$nomeUsuarios}%'");
            $whereConditions[] = array("Cupons.valor_pago_gotas BETWEEN '{$valorMinimo}' AND '{$valorMaximo}'");
            $whereConditions[] = array("Cupons.valor_pago_reais BETWEEN '{$valorMinimo}' AND '{$valorMaximo}'");
            $whereConditions[] = array("Cupons.data BETWEEN '{$dataInicio}' AND '{$dataFim}'");

            $cupons = $this->_getCuponsTable()->find('all')
                ->where(
                    $whereConditions
                )
                ->contain(
                    array('Brindes', 'Clientes', 'Usuarios')
                );

            return $cupons;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    #endregion

    #region Update

    /**
     * CuponsTable::setCupomEstornado
     *
     * Define cupom como estornado, impossibilitanto novo estorno
     *
     * @param integer $id Id do Cupom
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2019-02-09
     *
     * @return \App\Model\Entity\Cupon $cupom
     */
    public function setCupomEstornado(int $id)
    {
        try {
            $cupom = $this->get($id);

            if (empty($cupom)) {
                throw new Exception(sprintf("Cupom de id %s não encontrado!", $id));
            }

            $cupom["estornado"] = true;
            $cupom = $this->save($cupom);

            return $cupom;
        } catch (\Exception $e) {
            $trace = $e->getTraceAsString();
            $stringError = __("Erro ao gravar cupom: {0}. [Função: {1} / Arquivo: {2} / Linha: {3} / Detalhes: {4}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__, $trace);

            Log::write('error', $stringError);
            Log::write('error', $trace);

            throw new \Exception($stringError);
        }
    }

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
        return $this->updateAll(array('resgatado' => 1), array('id' => $id));
    }

    /**
     * Define o(s) cupom(s) como resgatado e usado
     *
     * @param array $ids Ids de Cupom
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 2018-01-30
     *
     * @return int Número de registros afetados
     */
    public function setCuponsResgatadosUsados(array $ids)
    {
        return $this->updateAll(
            array(
                'resgatado' => 1,
                'usado' => 1
            ),
            array(
                'id IN' => $ids
            )
        );
    }

    #endregion

    #region Delete

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

    #endregion
}
