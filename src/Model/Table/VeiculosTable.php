<?php
namespace App\Model\Table;

use ArrayObject;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Core\Configure;

/**
 * Veiculos Model
 *
 * @method \App\Model\Entity\Veiculo get($primaryKey, $options = [])
 * @method \App\Model\Entity\Veiculo newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Veiculo[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Veiculo|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Veiculo patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Veiculo[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Veiculo findOrCreate($search, callable $callback = null, $options = [])
 */
class VeiculosTable extends GenericTable
{

    /**
     * -------------------------------------------------------------
     * Fields
     * -------------------------------------------------------------
     */
    protected $veiculosTable = null;

    /**
     * -------------------------------------------------------------
     * Properties
     * -------------------------------------------------------------
     */


    /**
     * Method get of user has vehicles table property
     *
     * @return (Cake\ORM\Table) Table object
     */
    private function _getVeiculosTable()
    {
        if (is_null($this->veiculosTable)) {
            $this->_setVeiculosTable();
        }
        return $this->veiculosTable;
    }

    /**
     * Method set of user has vehicles table property
     *
     * @return void
     */
    private function _setVeiculosTable()
    {
        $this->veiculosTable = TableRegistry::get('Veiculos');
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

        $this->setTable('veiculos');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->hasMany('UsuariosHasVeiculos')
            ->setForeignKey('veiculos_id');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $requireMessage = "É necessário informar %s para realizar o cadastro!";
        $emptyMessage = "O campo %s deve ser preenchido!";

        $validator
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence("placa", "create", sprintf($requireMessage, "PLACA"))
            ->notEmpty('placa', sprintf($emptyMessage, "PLACA"));

        $validator
            ->requirePresence("modelo", ["create", "update"], sprintf($requireMessage, "MODELO"))
            ->notEmpty('modelo', sprintf($emptyMessage, "MODELO"));

        $validator
            ->requirePresence("fabricante", ["create", "update"], sprintf($requireMessage, "FABRICANTE"))
            ->notEmpty('fabricante', sprintf($emptyMessage, "FABRICANTE"));

        $validator
            ->requirePresence("ano", ["create", "update"], sprintf($requireMessage, "ANO"))
            ->integer('ano')
            ->notEmpty('ano', sprintf($emptyMessage, "ANO"));

        $validator
            ->dateTime('audit_insert')
            ->allowEmpty('audit_insert');

        $validator
            ->dateTime('audit_update')
            ->allowEmpty('audit_update');

        return $validator;
    }

    /* ------------------------ Create ------------------------ */

    /**
     * Cria um novo veículo
     *
     * @param string $placa      Placa do veículo
     * @param string $modelo     Modelo de fabricação
     * @param string $fabricante Fabricante
     * @param int    $ano        Ano do veículo
     *
     * @return boolean Registro gravado
     */
    public function createVeiculo(
        string $placa,
        string $modelo,
        string $fabricante,
        int $ano
    ) {
        try {
            $veiculo = $this->_getVeiculosTable()->newEntity();

            $veiculo->placa = $placa;
            $veiculo->modelo = $modelo;
            $veiculo->fabricante = $fabricante;
            $veiculo->ano = $ano;

            return $this->_getVeiculosTable()->save($veiculo);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao criar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
        }
    }

    /* ------------------------ Read ------------------------ */

    /**
     * Obtem veículos conforme condições
     *
     * @param array $conditions Array de Condições
     *
     * @return \App\Model\Entity\Veiculos[]
     */
    public function findVeiculos(array $conditions)
    {
        try {
            if (sizeof($conditions) > 0) {
                return $this->_getVeiculosTable()
                    ->find('all')
                    ->where($conditions);
            } else {
                return $this->_getVeiculosTable()
                    ->find('all');
            }
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registros: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);
        }
    }

    /**
     * Find Vehicle By Plate
     *
     * @param (string) $placa Placa do veículo
     *
     * @return (entity\veiculos) $vehicle
     **/
    public function getVeiculoById($id)
    {
        try {
            return $this->_getVeiculosTable()
                ->find('all')
                ->where(['id' => $id])
                ->first();
        } catch (\Exception $e) {
        }
    }

    /**
     * Find Vehicle By Plate
     *
     * @param (string) $placa Placa do veículo
     *
     * @return (entity\veiculos) $vehicle
     **/
    public function getVeiculoByPlaca($placa)
    {
        try {
            return $this->_getVeiculosTable()
                ->find('all')
                ->where(['placa' => $placa])
                ->first();
        } catch (\Exception $e) {
        }
    }

    /**
     * Obtêm todos os usuários vinculados à um veículo
     *
     * @param string $placa            Placa
     * @param array  $where_conditions Condições Extras
     *
     * @return array $veiculos Lista de Veículos com usuários
     **/
    public function getFuncionariosClienteByVeiculo(string $placa, array $where_conditions = [])
    {
        try {

            $conditions = [];

            foreach ($where_conditions as $key => $condition) {
                array_push($conditions, $condition);
            }

            array_push($conditions, ['placa like ' => '%' . $placa . '%']);

            $users = $this->_getVeiculosTable()
                ->find('all')
                ->where($conditions)
                ->contain(
                    [
                        'UsuariosHasVeiculos',
                        'UsuariosHasVeiculos.Usuarios',
                        'UsuariosHasVeiculos.Usuarios.ClientesHasUsuarios',

                    ]
                );

            return $users->toArray();
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
     * Obtêm todos os usuários vinculados à um veículo
     *
     * @param string $placa            Placa
     * @param array  $where_conditions Condições Extras
     *
     * @return array $veiculos Lista de Veículos com usuários
     **/
    public function getUsuariosByVeiculo(string $placa, array $where_conditions = [])
    {
        try {

            $conditions = [];

            foreach ($where_conditions as $key => $condition) {
                array_push($conditions, $condition);
            }

            array_push($conditions, ['placa like ' => '%' . $placa . '%']);

            $users = $this->_getVeiculosTable()
                ->find('all')
                ->where($conditions)
                ->contain(['UsuariosHasVeiculos', 'UsuariosHasVeiculos.Usuarios']);

            return $users->toArray();
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
     * VeiculosTable::getUsuariosClienteByVeiculo
     *
     * Filtra Usuários pelo Veículo
     *
     * @param string  $placa
     * @param integer $redesId
     * @param array   $clientesIds
     * @param boolean $filtrarComFuncionarios
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date   10/06/2018
     *
     * @return \App\Model\Entity\Veiculo[] $veiculos
     */
    public function getUsuariosClienteByVeiculo(string $placa, int $redesId = null, array $clientesIds = array(), bool $filtrarComFuncionarios = false)
    {
        $veiculo = null;
        $usuarios = array();
        $data = array("veiculo", "usuarios");

        $veiculo = $this->_getVeiculosTable()->find("all")
            ->where(["placa" => $placa])->first();


        if (empty($veiculo)) {
            // Se não encontrar veiculo, retorna vazio
            $data = array("veiculo" => null, "usuarios" => array());
        } else {

            /**
             * Se informa a rede, pesquisar o id de todas as unidades.
             * Exemplo de quando vai acontecer:
             * Resgate de brinde;
             *
             * Se não informar, pesquisar geral.
             * Exemplo:
             * Atribuição de gotas
             *
             */

            /**
             * Pode filtrar pela rede ou pelas unidades da rede
             */
            if (!empty($redesId)) {

                $redeHasClienteTable = TableRegistry::get("RedesHasClientes");

                $redeHasClientesQuery = $redeHasClienteTable->getAllRedesHasClientesIdsByRedesId($redesId);

                $clientesIds = array();

                foreach ($redeHasClientesQuery->toArray() as $key => $value) {
                    $clientesIds[] = $value["clientes_id"];
                }
            }

            // Pega todos os usuários que estão vinculados à aquele veículo

            $usuariosHasVeiculosTable = TableRegistry::get("UsuariosHasVeiculos");

            $usuariosHasVeiculos = $usuariosHasVeiculosTable->findUsuariosHasVeiculos(["veiculos_id" => $veiculo["id"]])->select(["usuarios_id"])->toArray();

            $usuariosVeiculosEncontradosIds = array();

            foreach ($usuariosHasVeiculos as $key => $usuarioHasVeiculo) {
                $usuariosVeiculosEncontradosIds[] = $usuarioHasVeiculo["usuarios_id"];
            }

            $whereConditions = array();

            if ($filtrarComFuncionarios) {
                // Colocar tipo de perfil de Administrador Geral até Funcionário
                $whereConditions[] = array(
                    "ClientesHasUsuarios.tipo_perfil >= " => Configure::read("profileTypes")["AdminNetworkProfileType"],
                    "ClientesHasUsuarios.tipo_perfil <= " => Configure::read("profileTypes")["WorkerProfileType"]
                );
            } else {
                // Somente usuários
                $whereConditions[] = array(
                    "ClientesHasUsuarios.tipo_perfil" => Configure::read("profileTypes")["UserProfileType"]
                );
            }

            if (sizeof($clientesIds) > 0) {
                $whereConditions[] = array("clientes_id in " => $clientesIds);
            }

            // Pesquisa de Clientes que tem os usuários em questão

            $clientesHasUsuariosTable = TableRegistry::get("ClientesHasUsuarios");

            $usuariosClientesEncontradosIds = array();

            $usuariosClientesWhereConditions = $whereConditions;
            $usuariosClientesWhereConditions[] = array("ClientesHasUsuarios.usuarios_id in " => $usuariosVeiculosEncontradosIds);

            $usuariosClientesEncontradosArray = $clientesHasUsuariosTable->findClienteHasUsuario(
                $usuariosClientesWhereConditions
            )->select(["usuarios_id"])->toArray();

            foreach ($usuariosClientesEncontradosArray as $key => $value) {
                $usuariosClientesEncontradosIds[] = $value["usuarios_id"];
            }

            $usuarios = array();
            if (sizeof($usuariosClientesEncontradosIds) > 0) {

                $whereConditionsUsuariosRetorno = ["usuarios.id in " => $usuariosClientesEncontradosIds];

                // Obtem os usuários que atendem aos critérios

                $usuariosTable = TableRegistry::get("Usuarios");
                $usuarios = $usuariosTable->findAllUsuarios($whereConditionsUsuariosRetorno)->toArray();
            }
        }

        $data = array("veiculo" => $veiculo, "usuarios" => $usuarios);

        return $data;
    }

    /* ------------------------ Update ------------------------ */
    /* ------------------------ Delete ------------------------ */
}
