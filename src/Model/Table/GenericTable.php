<?php
namespace App\Model\Table;

use ArrayObject;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Routing\Router;
use Cake\Core\Configure;
use App\Custom\RTI\DebugUtil;


/**
 * Generic Model
 *
 */
class GenericTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);
    }

    /**
     * Remove todos os caracteres não numéricos para guardar sem formatação no BD
     * @param String CNPJ
     */
    public function cleanNumber($value)
    {
        return preg_replace('/[^0-9]/', "", $value);
    }

    /**
     * Obtem colunas de uma query como um array
     *
     * @param  Query $query               Query para pesquisar
     * @param  array $columns_to_retrieve Colunas para pesquisar
     * @return array $array               Valores
     */
    protected function retrieveColumnsQueryAsArray($query, $columns_to_retrieve)
    {
        $array = $query;
        if (is_a($query, 'Cake\ORM\Query')) {
            $array = $query->toArray();
        }

        $array_return = [];

        foreach ($array as $key => $value) {

            $array_item = [];
            foreach ($columns_to_retrieve as $key => $column) {
                $array_item[$column] = $value[$column];
            }

            $array_return[] = $array_item;
        }

        return $array_return;
    }

    /**
     * Carrega todas as Models necessárias informadas em um array
     * @param (array) $models
     * @author Gustavo Souza Gonçalves
     * @return void
     */
    public function loadNecessaryModels($models = null)
    {
        foreach ($models as $key => $model) {
            $this->loadModel($model);
        }
    }
}
