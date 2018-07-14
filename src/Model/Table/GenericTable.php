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

    /**
     * Prepara array de retorno em caso de consulta via paginação
     *
     * @param array $totalData Array de Dados
     * @param string $stringLabelReturn Nome do índice de retorno
     * @param array $pagination Array de Paginação
     *
     * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
     * @data 08/07/2018
     *
     * @return array $dados
     */
    public function prepareReturnDataPagination(array $totalData, array $currentData = array(), string $stringLabelReturn = "data", array $pagination = null)
    {
        $retorno = array();
        $count = sizeof($totalData);

        // DebugUtil::printArray($totalData);
        // DebugUtil::printArray($currentData);

        // Retorna mensagem de que não retornou dados se for page 1. Se for page 2, apenas não exibe.
        if (sizeof($totalData) == 0) {
            $retorno = array(
                "mensagem" => array(
                    "status" => 0,
                    "message" => Configure::read("messageQueryNoDataToReturn"),
                    "errors" => array()
                ),
                $stringLabelReturn => array(
                    "count" => 0,
                    "page_count" => 0,
                    "data" => array()
                ),
            );

            if (sizeof($pagination) > 0) {
                if ($pagination["page"] == 1) {
                    $retorno = array(
                        $stringLabelReturn => array(
                            "count" => 0,
                            "page_count" => 0,
                            "data" => array()
                        ),
                        "mensagem" => array(
                            "status" => 0,
                            "message" => Configure::read("messageQueryNoDataToReturn"),
                            "errors" => array()
                        )
                    );
                } else {
                    $retorno = array(
                        "mensagem" => array(
                            "status" => 0,
                            "message" => Configure::read("messageQueryNoDataToReturn"),
                            "errors" => array()
                        ),
                        $stringLabelReturn => array(
                            "count" => 0,
                            "page_count" => 0,
                            "data" => array()
                        )

                    );
                }
            }
        } else {
            // se tem dados, mas a página atual não tem, é fim de paginação também
            // DebugUtil::printArray($currentData);

            if (sizeof($currentData) == 0) {
                $retorno = array(
                    "mensagem" => array(
                        "status" => 0,
                        "message" => Configure::read("messageQueryPaginationEnd"),
                        "errors" => array()
                    ),
                    $stringLabelReturn => array(
                        "count" => 0,
                        "page_count" => 0,
                        "data" => array()
                    )

                );
            } else {
                // DebugUtil::printArray($totalData);
                $retorno = array(
                    $stringLabelReturn => array(
                        "count" => sizeof($totalData),
                        "page_count" => sizeof($currentData),
                        "data" => $currentData
                    ),
                    "mensagem" => array(
                        "status" => sizeof($totalData) > 0 ? 1 : 0,
                        "message" => sizeof($totalData) > 0 ? Configure::read("messageLoadDataWithSuccess") : Configure::read("messageQueryNoDataToReturn"),
                        "errors" => array()
                    ),
                );
            }
        }

        // DebugUtil::printArray($retorno);
        return $retorno;
    }
}
