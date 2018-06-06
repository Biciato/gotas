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
                $transportadora_save = $this
                    ->_getTransportadorasTable()
                    ->find('all')
                    ->where(['id' => $transportadora['id']])
                    ->first();
            } else {
                $transportadora_save = $this->_getTransportadorasTable()->newEntity();
            }

            $transportadora_save->nome_fantasia = $transportadora['nome_fantasia'];
            $transportadora_save->razao_social = $transportadora['razao_social'];
            $transportadora_save->cnpj = $this->cleanNumber($transportadora['cnpj']);
            $transportadora_save->cep = $this->cleanNumber($transportadora['cep']);
            $transportadora_save->endereco = $transportadora['endereco'];
            $transportadora_save->endereco_numero = $transportadora['endereco_numero'];
            $transportadora_save->endereco_complemento = $transportadora['endereco_complemento'];
            $transportadora_save->bairro = $transportadora['bairro'];
            $transportadora_save->municipio = $transportadora['municipio'];
            $transportadora_save->estado = $transportadora['estado'];
            $transportadora_save->pais = $transportadora['pais'];
            $transportadora_save->tel_fixo = $this->cleanNumber($transportadora['tel_fixo']);
            $transportadora_save->tel_celular = $this->cleanNumber($transportadora['tel_celular']);

            return $this->_getTransportadorasTable()->save($transportadora_save);
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            $stringError = __("Erro ao criar registro: " . $e->getMessage() . ", em: " . $trace[1]);

            Log::write('error', $stringError);

            $this->Flash->error($stringError);
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
}
