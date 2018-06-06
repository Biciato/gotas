<?php

/**
 * TODO: fazer header
 * 
 */

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

/**
 * Clientes Model
 *
 * @property \App\Model\Table\MatrizsTable|\Cake\ORM\Association\BelongsTo $Matrizs
 *
 * @method \App\Model\Entity\Cliente get($primaryKey, $options = [])
 * @method \App\Model\Entity\Cliente newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Cliente[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Cliente|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Cliente patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Cliente[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Cliente findOrCreate($search, callable $callback = null, $options = [])
 */
class ClientesTable extends GenericTable
{
    /**
     * -----------------------------------------------------
     * Fields
     * -----------------------------------------------------
     */
    protected $clienteTable = null;

    /**
     * -----------------------------------------------------
     * Properties
     * -----------------------------------------------------
     */

    /**
     * Method get of client table property
     * @return Cake\ORM\Table Table object
     */
    private function _getClientesTable()
    {
        if (is_null($this->clienteTable)) {
            $this->_setClienteTable();
        }
        return $this->clienteTable;
    }

    /**
     * Method set of client table property
     * @return void
     */
    private function _setClienteTable()
    {
        $this->clienteTable = TableRegistry::get('Clientes');
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

        $this->setTable('clientes');
        $this->setDisplayField('razao_social');
        $this->setPrimaryKey('id');

        $this->belongsto(
            'RedesHasClientes',
            [
                'className' => 'RedesHasClientes',
                'foreignKey' => 'clientes_id',
                'join' => 'left'
            ]
        );

        $this->hasOne(
            'RedeHasCliente',
            [
                'className' => 'RedesHasClientes',
                'foreignKey' => 'clientes_id',
                'join' => 'INNER'
            ]
        );

        $this->hasMany(
            'ClientesHasUsuarios',
            [
                'className' => 'ClientesHasUsuarios',
                'foreignKey' => 'clientes_id',
                'join' => 'INNER'
            ]
        );

        $this->hasMany(
            'Gotas',
            [
                'className' => 'Gotas',
                'foreignKey' => 'clientes_id',
                'join' => 'INNER'
            ]
        );

        $this->hasMany(
            'ClientesHasBrindesHabilitados',
            [
                'className' => 'ClientesHasBrindesHabilitados',
                'foreignKey' => 'clientes_id',
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
            ->notEmpty('matriz');

        $validator
            ->integer('tipo_unidade')
            ->requirePresence('tipo_unidade', 'create')
            ->notEmpty('tipo_unidade');

        $validator
            ->allowEmpty('codigo_rti_shower');

        $validator
            ->allowEmpty('nome_fantasia');

        $validator
            ->requirePresence('razao_social', 'create')
            ->notEmpty('razao_social');

        $validator
            ->notEmpty('cnpj');

        $validator
            ->allowEmpty('endereco');

        $validator
            ->allowEmpty('endereco_numero');

        $validator
            ->allowEmpty('endereco_complemento');

        $validator
            ->allowEmpty('bairro');

        $validator
            ->allowEmpty('municipio');

        $validator
            ->requirePresence('estado')
            ->notEmpty('estado', 'Necessário informar um estado para realizar a importação de dados da SEFAZ');

        $validator
            ->requirePresence('pais')
            ->notEmpty('pais');

        $validator
            ->allowEmpty('cep', 'Se o usuário não souber o CEP do local, utilize o CEP da cidade.');

        $validator
            ->allowEmpty('latitude');

        $validator
            ->allowEmpty('longitude');

        $validator
            ->allowEmpty('tel_fixo');

        $validator
            ->allowEmpty('tel_fax');

        $validator
            ->allowEmpty('tel_celular');

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

        return $rules;
    }

    public function beforeMarshal(Event $event, ArrayObject $data)
    {
        $data = $this->formatClient($data);

        return $data;
    }

    /* ------------------------ Create -------------------------- */


    /**
     * Adiciona nova unidade para uma rede 
     * 
     * @param int                      $redes_id Id da rede
     * @param App\Model\Entity\Cliente $cliente  Entidade Cliente
     * 
     * @return bool         $cliente   Registro salvo
     */
    public function addClient(int $redes_id, \App\Model\Entity\Cliente $cliente)
    {
        try {

            $redes_has_clientes = $this->_getClientesTable()
                ->RedeHasCliente->find('all')
                ->where(
                    [
                        'redes_id' => $redes_id
                    ]
                )->toArray();

            $cliente->matriz = sizeof($redes_has_clientes) == 0;

            $cliente = $this->_getClientesTable()->save($cliente);

            // salvou o cliente
            if ($cliente) {
                $redes_has_cliente = $this->_getClientesTable()->RedeHasCliente->newEntity();

                $redes_has_cliente->redes_id = $redes_id;
                $redes_has_cliente->clientes_id = $cliente->id;

                // verifica se tem alguma empresa cadastrada. 
                // Se não tiver, esta fica sendo a matriz 



                $result = $this->_getClientesTable()->RedeHasCliente->save($redes_has_cliente);
            }

            return $result;
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            Log::write('error', "Erro ao inserir novo registro: " . $e->getMessage() . ", em: " . $trace[1]);

            return false;
        }
    }

    /* ------------------------ Read -------------------------- */

    /**
     * Undocumented function
     *
     * @param int $clientes_id Id de Clientes
     * 
     * @return Cliente $cliente Registro da matriz
     */
    public function findClienteMatrizFromClientesId(int $clientes_id)
    {
        try {
            $matriz = null;

            // TODO: ajustar
            while (true) {
                $cliente = $this->_getClientesTable()->find('all')
                    ->where(['id' => $clientes_id])
                    ->first();

                $clientes_id = $cliente->matriz_id;

                if (!isset($clientes_id)) {
                    $matriz = $cliente;
                    break;
                }
            }

            return
                [
                'result' => true,
                'data' => $matriz
            ];
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return [
                'result' => false,
                'data' => $stringError
            ];
        }
    }

    /**
     * Obtem todos os clientes
     *
     * @param array $where_conditions condições
     * 
     * @return void
     */
    public function getAllClientes(array $where_conditions = array())
    {
        try {
            $conditions = [];

            foreach ($where_conditions as $key => $condition) {
                array_push($conditions, $condition);
            }

            return $this->_getClientesTable()->find('all')
                ->where($conditions)
                ->order(
                    [
                        'id' => 'asc'
                    ]
                )
                ->contain(['ClientesHasUsuarios']);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Obtem todos os clientes
     * Utilizado para Serviços REST
     *
     * @param array $whereConditions Condições de where
     * @param array $orderConditions Condições de ordenação
     * @param array $paginationConditions Condições de Paginação
     * 
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 2018/05/13
     * 
     * @return array ("count", "data")
     */
    public function getClientes(array $whereConditions = array(), array $orderConditions = array(), array $paginationConditions = array())
    {
        try {
            $clientes = $this->_getClientesTable()->find('all')
                ->where($whereConditions);

            // $count = 0;
            $count = $clientes->count();

            if (sizeof($orderConditions) > 0) {
                $clientes = $clientes->order($orderConditions);
            }

            if (sizeof($paginationConditions) > 0) {
                $clientes = $clientes->limit($paginationConditions["limit"])
                    ->page($paginationConditions["page"]);
            }

            return ["count" => $count, "data" => $clientes];

        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringExplode = implode(";", $trace);

            $stringError = __("Erro ao realizar pesquisa de clientes: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4} / Errors: ]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__, $stringExplode);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Pega todos os ids de uma rede
     *
     * @return void
     * @author
     **/
    public function getAllIdsCliente($clientes_id)
    {
        try {
            $cliente = $this->_getClientesTable()->get($clientes_id);

            $matriz = null;

            // cliente não é matriz?
            if (!is_null($cliente->matriz_id)) {
                // pega quem é matriz
                $matriz = $this->_getClientesTable()->find('all')
                    ->where(
                        [
                            'matriz_id' => $cliente->matriz_id
                        ]
                    )->first();
            } else {
                $matriz = $cliente;
            }

            // Após ter a matriz, retorna lista de ids dos clientes para passar em pontuações

            $array_ids = $this->_getClientesTable()->find('all')
                ->where(['matriz_id' => $matriz->id])
                ->select(['id'])->toArray();

            if (!is_null($cliente->matriz_id)) {
                $array_ids[] = ['id' => $cliente->matriz_id];
            } else {
                $array_ids[] = ['id' => $cliente->id];
            }

            return $array_ids;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao obter registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Obtem cliente pelo id
     *
     * @return entity $cliente
     * @author
     **/
    public function getClienteById($clientes_id)
    {
        try {
            return $this->_getClientesTable()
                ->find('all')
                ->where(
                    [
                        'Clientes.id' => $clientes_id
                    ]
                )->contain(['RedeHasCliente', 'RedeHasCliente.Redes'])
                ->first();
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            return ['success' => 'false', 'message' => $stringError];
        }
    }

    /**
     * Clientes::getClienteByCNPJ
     * 
     * Obtem cliente a partir de seu CNPJ
     *
     * @param string $cnpj CNPJ
     * 
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date   2018/05/07
     * 
     * @return \App\Model\Entity\Cliente $cliente
     * 
     **/
    public function getClienteByCNPJ(string $cnpj)
    {
        try {
            return $this->_getClientesTable()
                ->find('all')
                ->where(
                    [
                        'cnpj' => $cnpj
                    ]
                )->first();
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao realizar pesquisa de clientes: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Clientes::getClientesCNPJByEstado
     * 
     * Obtem todos os CNPJ cadastrado pelo estado
     *
     * @param string $estado Estado 
     * 
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date   2018/05/07
     * 
     * @return array[] CNPJ's
     * 
     **/
    public function getClientesCNPJByEstado(string $estado)
    {
        try {
            return $this->_getClientesTable()
                ->find('all')
                ->where(
                    [
                        'estado' => $estado
                    ]
                )->select(["cnpj"]);
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao realizar pesquisa de CNPJ's de clientes: {0} em: {1}. [Função: {2} / Arquivo: {3} / Linha: {4}]  ", $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);

            return $stringError;
        }
    }

    /**
     * Obtem ids de matriz e filiais pelo Cliente ID
     *
     * @param int $clientes_id Id do Cliente
     *
     * @return array Array de Ids
     **/
    public function getIdsMatrizFiliaisByClienteId($clientes_id)
    {
        try {
            // $array_ids = $this->_getClientesTable()->getAllIdsCliente($clientes_id);
            $array_ids = $this->_getClientesTable()->RedesHasClientes->getAllRedesHasClientesIdsByClientesId($clientes_id);

            $array = [];

            // preciso somente dos ids
            foreach ($array_ids as $key => $value) {
                array_push($array, $value['clientes_id']);
            }

            $array_ids = $array;

            return $array_ids;
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao atualizar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Retorna Entidade Cliente associado à usuário
     *
     * @return Entity\Cliente $cliente
     * @param Cake\Auth\Storage $user_logged
     * @author Gustavo Souza Gonçalves
     **/
    public function getClienteMatrizLinkedToUsuario($user_logged = null)
    {
        $matriz = $this->_getClientesTable()->find('all')
            ->join(
                [
                    'ClientesHasUsuarios' =>
                        [
                        'table' => 'clientes_has_usuarios',
                        'alias' => 'chu',
                        'type' => 'inner',
                        'conditions' =>
                            [
                            'chu.clientes_id = Clientes.id',
                            'chu.usuarios_id' => $user_logged['id']
                        ]

                    ]
                ]
            )->select([
                'id',
                'tipo_unidade',
                'codigo_rti_shower',
                'nome_fantasia',
                'razao_social',
                'cnpj',
                'tel_fixo',
                'tel_fax',
                'tel_celular',
                'endereco',
                'endereco_numero',
                'endereco_complemento',
                'bairro',
                'municipio',
                'estado',
                'cep',
                'audit_insert',
                'audit_update',
                'chu.id',
                'chu.clientes_id',
                'chu.usuarios_id'
            ]);

        if (sizeof($matriz->toArray()) > 0) {
            return $matriz->first();
        } else {
            return null;
        }
    }

    /**
     * Retorna filiais associadas com matriz
     *
     * @param int $clientes_id Id de cliente
     *
     * @return void
     **/
    public function getClienteFiliais(int $clientes_id, array $where_conditions = array())
    {
        try {
            $conditions = [];

            foreach ($where_conditions as $key => $condition) {
                array_push($conditions, $condition);
            }
            array_push($conditions, ['Clientes.matriz_id' => $clientes_id]);

            return $this->_getClientesTable()
                ->find('all')
                ->where([$conditions])
                ->contain(['ClientesHasUsuarios']);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /**
     * Retorna filiais associadas com matriz
     *
     * @param int $cliente_id Id de cliente
     *
     * @return void
     **/
    public function getClienteFiliaisIds(int $cliente_id = null)
    {

        try {
            $ids = $this->_getClientesTable()->find('all')
                ->where(['matriz_id' => $cliente_id])
                ->select(['id']);

            $returnIds = [];
            foreach ($ids as $key => $id) {
                array_push($returnIds, $id->id);
            }

            return $returnIds;
        } catch (\Exception $e) {
        }
    }

    /**
     * Obtêm registro de cliente matriz ligada ao administrador
     * Se usuário não for da matriz, retorna null
     *
     * @return object|null $cliente
     * @author
     **/
    public function getClienteMatrizLinkedToAdmin($user_logged)
    {
        $cliente = $this->_getClientesTable()->find('all')
            ->matching(
                'ClientesHasUsuarios',
                function ($q) use ($user_logged) {
                    return $q->where(['ClientesHasUsuarios.matriz_id' => $user_logged['matriz_id']]);
                }
            )
            ->first();

        return $cliente;
    }

    /* ------------------------ Update ------------------------ */

    /**
     * Troca estado de unidade
     *
     * @param int  $id      Id de RedesHasClientes
     * @param bool $ativado Estado de ativação
     * 
     * @return \App\Model\Entity\Clientes $cliente
     */
    public function changeStateEnabledCliente(int $id, bool $ativado)
    {
        try {

            $cliente = $this->_getClientesTable()->find('all')
                ->where(['id' => $id])
                ->first();

            $cliente->ativado = $ativado;

            return $this->_getClientesTable()->save($cliente);

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
     * Update entity in BD
     * 
     * @param entity $cliente
     * @return void
     */
    public function updateClient($cliente)
    {
        try {
            $clienteTable = TableRegistry::get('Clientes');
            $clienteToUpdate = $this->formatClient($cliente);

            return $clienteTable->save($clienteToUpdate);

        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao inserir novo registro: {0} em: {1}", $e->getMessage(), $trace[1]);

            Log::write('error', $stringError);

            return ['success' => false, 'message' => $stringError];
        }
    }

    /* ------------------------ Delete ------------------------ */

    /**
     * Remove todos os clientes
     *
     * @param array $clientes_ids Ids de clientes
     * 
     * @return void
     */
    public function deleteClientesByIds(array $clientes_ids)
    {
        try {

            return
                $this->_getClientesTable()->deleteAll(['id in' => $clientes_ids]);

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

    /* ------------------------ Others ------------------------ */

    /**
     * Prepara cliente para atualização no BD
     * @param entity $cliente
     * @return Entity\Cliente
     */
    public function formatClient($cliente)
    {
        $cliente['tipo_unidade'] = $cliente['tipo_unidade'];
        $cliente['nome_fantasia'] = $cliente['nome_fantasia'];
        $cliente['razao_social'] = $cliente['razao_social'];
        $cliente['endereco'] = $cliente['endereco'];
        $cliente['endereco_numero'] = $cliente['endereco_numero'];
        $cliente['endereco_complemento'] = $cliente['endereco_complemento'];
        $cliente['bairro'] = $cliente['bairro'];
        $cliente['municipio'] = $cliente['municipio'];
        $cliente['estado'] = $cliente['estado'];
        $cliente['cnpj'] = $this->cleanNumber($cliente['cnpj']);
        $cliente['tel_fixo'] = $this->cleanNumber($cliente['tel_fixo']);
        $cliente['tel_celular'] = $this->cleanNumber($cliente['tel_celular']);
        $cliente['tel_fax'] = $this->cleanNumber($cliente['tel_fax']);
        $cliente['cep'] = $this->cleanNumber($cliente['cep']);

        if (isset($cliente['matriz_id'])) {
            $cliente['matriz_id'] = $cliente['matriz_id'];
        }

        return $cliente;
    }

    /**
     * Undocumented function
     *
     * @param [type] $cliente
     * @param [type] $matrizId
     *
     * @return void
     */
    public function setMatrizId($cliente, $matrizId)
    {
        $cliente->matriz_id = $matrizId;

        return $cliente;
    }

    /**
     * Remove todos os caracteres não numéricos para guardar sem formatação no BD
     * @param String CNPJ
     */
    public function cleanNumber($value)
    {
        return preg_replace('/[^0-9]/', "", $value);
    }
}
