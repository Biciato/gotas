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

/**
 * Transportadoras Model
 *
 * @method \App\Model\Entity\Transportadora get($primaryKey, $options = [])
 * @method \App\Model\Entity\Transportadora newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Transportadora[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Transportadora|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Transportadora patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Transportadora[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Transportadora findOrCreate($search, callable $callback = null, $options = [])
 */
class TransportadorasTable extends GenericTable
{
    /**
     * -------------------------------------------------------------
     * Fields
     * -------------------------------------------------------------
     */

    protected $TransportadorasTable = null;

    /**
     * -------------------------------------------------------------
     * Properties
     * -------------------------------------------------------------
     */

    /**
     * Method get of transportadora table property
     * @return (Cake\ORM\Table) Table object
     */
    private function _getTransportadorasTable()
    {
        if (is_null($this->TransportadorasTable)) {
            $this->_setTransportadorasTable();
        }
        return $this->TransportadorasTable;
    }

    /**
     * Method set of transportadora table property
     * @return void
     */
    private function _setTransportadorasTable()
    {
        $this->TransportadorasTable = TableRegistry::get('Transportadoras');
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

        $this->setTable('transportadoras');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo("TransportadorasHasUsuarios", array(
            "foreignKey" => "id",
            "joinType" => "left"
        ));
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
            ->allowEmpty('nome_fantasia');

        $validator
            ->requirePresence('razao_social', "create", sprintf($requireMessage, "RAZAO_SOCIAL"))
            ->notEmpty('razao_social', sprintf($emptyMessage, "RAZAO_SOCIAL"));

        $validator
            ->requirePresence('cnpj', "create", sprintf($requireMessage, "CNPJ"))
            ->notEmpty('cnpj', sprintf($emptyMessage, "CNPJ"));

        $validator
            ->allowEmpty('cep');

        $validator
            ->allowEmpty('endereco');

        $validator
            ->integer('endereco_numero')
            ->allowEmpty('endereco_numero');

        $validator
            ->allowEmpty('endereco_complemento');

        $validator
            ->allowEmpty('bairro');

        $validator
            ->allowEmpty('municipio');

        $validator
            ->allowEmpty('estado');

        $validator
            ->allowEmpty('pais');

        $validator
            ->allowEmpty('tel_fixo');

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
     * Executes before validation when insert or edit happens
     *
     * @return (entity) $data object
     * @author Gustavo Souza Gonçalves
     **/
    public function beforeMarshal(Event $event, ArrayObject $data)
    {
        $data['id'] = isset($data['id']) ? $data['id'] : null;
        $data['nome_fantasia'] = isset($data['nome_fantasia']) ? $data['nome_fantasia'] : null;
        $data['razao_social'] = isset($data['razao_social']) ? $data['razao_social'] : null;
        $data['cnpj'] = isset($data['cnpj']) ? $this->cleanNumber($data['cnpj']) : null;
        $data['cep'] = isset($data['cep']) ? $this->cleanNumber($data['cep']) : null;
        $data['endereco'] = isset($data['endereco']) ? $data['endereco'] : null;
        $data['endereco_numero'] = isset($data['endereco_numero']) ? $data['endereco_numero'] : null;
        $data['endereco_complemento'] = isset($data['endereco_complemento']) ? $data['endereco_complemento'] : null;
        $data['bairro'] = isset($data['bairro']) ? $data['bairro'] : null;
        $data['municipio'] = isset($data['municipio']) ? $data['municipio'] : null;
        $data['estado'] = isset($data['estado']) ? $data['estado'] : null;
        $data['pais'] = isset($data['pais']) ? $data['pais'] : null;
        $data['tel_fixo'] = isset($data['tel_fixo']) ? $this->cleanNumber($data['tel_fixo']) : null;
        $data['tel_celular'] = isset($data['tel_celular']) ? $this->cleanNumber($data['tel_celular']) : null;

        return $data;
    }

    /**
     * -------------------------------------------------------------
     * Methods
     * -------------------------------------------------------------
     */

    /**
     * Cria uma nova transportadora
     *
     * @param array $placa      Placa do veículo
     *
     * @return boolean Registro gravado
     */
    public function createUpdateTransportadora(
        array $transportadora
    ) {
        try {

            if (strlen($transportadora['id']) > 0) {
                $transportadoraSave = $this
                    ->_getTransportadorasTable()
                    ->find('all')
                    ->where(['id' => $transportadora['id']])
                    ->first();

                if (empty($transportadoraSave)) {
                    $transportadoraSave = $this->_getTransportadorasTable()->newEntity();
                }
            } else {
                $transportadoraSave = $this->_getTransportadorasTable()->newEntity();
            }

            $transportadoraSave->nome_fantasia = isset($transportadora['nome_fantasia']) ? $transportadora['nome_fantasia'] : null;
            $transportadoraSave->razao_social = isset($transportadora['razao_social']) ? $transportadora['razao_social'] : null;
            $transportadoraSave->cnpj = isset($transportadora['cnpj']) ? $this->cleanNumber($transportadora['cnpj']) : null;
            $transportadoraSave->cep = isset($transportadora['cep']) ? $this->cleanNumber($transportadora['cep']) : null;
            $transportadoraSave->endereco = isset($transportadora['endereco']) ? $transportadora['endereco'] : null;
            $transportadoraSave->endereco_numero = isset($transportadora['endereco_numero']) ? $transportadora['endereco_numero'] : null;
            $transportadoraSave->endereco_complemento = isset($transportadora['endereco_complemento']) ? $transportadora['endereco_complemento'] : null;
            $transportadoraSave->bairro = isset($transportadora['bairro']) ? $transportadora['bairro'] : null;
            $transportadoraSave->municipio = isset($transportadora['municipio']) ? $transportadora['municipio'] : null;
            $transportadoraSave->estado = isset($transportadora['estado']) ? $transportadora['estado'] : null;
            $transportadoraSave->pais = isset($transportadora['pais']) ? $transportadora['pais'] : null;
            $transportadoraSave->tel_fixo = isset($transportadora['tel_fixo']) ? $this->cleanNumber($transportadora['tel_fixo']) : null;
            $transportadoraSave->tel_celular = isset($transportadora['tel_celular']) ? $this->cleanNumber($transportadora['tel_celular']) : null;

            return $this->_getTransportadorasTable()->save($transportadoraSave);
        } catch (\Exception $e) {
            $trace = $e->getTrace();

            $stringError = __("Erro ao gravar registro: {0}. [Função: {1} / Arquivo: {2} / Linha: {3}]  ", $e->getMessage(), __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
            Log::write('error', $trace);
        }
    }

    /**
     * Busca transportadora por CNPJ
     *
     * @param (string) $cnpj CNPJ de transportadora
     *
     * @return (entity\transportadora) $transportadora;
     **/
    public function findTransportadoraByCNPJ($cnpj)
    {
        try {

            $cnpj = $this->cleanNumber($cnpj);

            return $this->_getTransportadorasTable()->find('all')->where(['cnpj' => $cnpj])->first();

        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registros: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);
        }
    }

    /**
     * Obtem todas as transportadoras conforme condição
     *
     * @param array $conditions Condições de pesquisa
     *
     * @return \App\Model\Entity\Transportadoras[]
     */
    public function findTransportadoras(array $conditions)
    {
        try {
            if (sizeof($conditions) > 0) {
                return $this->_getTransportadorasTable()
                    ->find('all')
                    ->where($conditions);
            } else {
                return $this->_getTransportadorasTable()
                    ->find('all');
            }
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao buscar registros: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);
        }
    }

    /**
     * Transportadoras::getTransportadoraById
     *
     * Retorna registro de transportadora pelo Id
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @date 2018/05/03
     *
     * @param Int64 $id
     * @return \App\Model\Entity\Transportadora $transportadora|null
     */
    public function getTransportadoraById(int $id)
    {
        try {
            return $this->_getTransportadorasTable()->find('all')
                ->where(["id" => $id])->first();
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $messageString = __("Não foi possível obter transportadora!");
            $stringError = __("Erro ao buscar registro: {0} - {1} em: {2}. [Função: {3} / Arquivo: {4} / Linha: {5}]  ", $messageString, $e->getMessage(), $trace[1], __FUNCTION__, __FILE__, __LINE__);

            Log::write('error', $stringError);
        }
    }

    /**
     * TransportadorasTable::getTransportadorasUsuario
     *
     * Obtem dados de transportadora de Usuário
     *
     * @param integer $id  Id
     * @param string $cnpj Cnpj
     * @param string $nomeFantasia Nome Fantasia
     * @param string $razaoSocial Razao Social
     * @param integer $usuariosId  Usuarios Id
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @since 20/09/2018
     *
     * @return \App\Model\Entity\Transportadoras[] $data
     */
    public function getTransportadorasUsuario(int $id = null, string $cnpj = null, string $nomeFantasia = null, string $razaoSocial = null, int $usuariosId = null)
    {
        $whereConditions = array();

        if (!empty($id)) {
            $whereConditions[] = array("Transportadoras.id" => $id);
        }

        if (!empty($cnpj)) {
            $whereConditions[] = array("cnpj" => $cnpj);
        }

        if (!empty($nomeFantasia)) {
            $whereConditions[] = array("nomeFantasia like '%{$nomeFantasia}%");
        }

        if (!empty($razaoSocial)) {
            $whereConditions[] = array("razaoSocial like '%{$razaoSocial}%'");
        }

        if (!empty($usuariosId)) {
            $whereConditions[] = array("TransportadorasHasUsuarios.usuarios_id" => $usuariosId);
        }

        $selectFields = array(
            "id" => "Transportadoras.id",
            "cnpj" => "Transportadoras.cnpj",
            "nomeFantasia" => "Transportadoras.nome_fantasia",
            "razaoSocial" => "Transportadoras.razao_social",
            "municipio" => "Transportadoras.municipio",
            "estado" => "Transportadoras.estado",
            "telFixo" => "Transportadoras.tel_fixo",
            "telCelular" => "Transportadoras.tel_celular",
            "dataInsercao" => "Transportadoras.audit_insert",
            "usuariosId" => "TransportadorasHasUsuarios.usuarios_id"
        );

        return $this->find("all")
            ->where($whereConditions)
            ->select($selectFields)
            ->contain("TransportadorasHasUsuarios")
            ->toArray();

    }

}
