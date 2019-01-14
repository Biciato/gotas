<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * GotasHasHistoricoValoresFixture
 *
 */
class GotasHasHistoricoValoresFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'clientes_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'gotas_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => 'Id da Gota do Cliente', 'precision' => null, 'autoIncrement' => null],
        'preco' => ['type' => 'decimal', 'length' => 4, 'precision' => 2, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => 'Preço da Gota (Gasolina, Diesel...)'],
        'audit_insert' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'audit_update' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        '_indexes' => [
            'gotas_has_historico_valores1_idx' => ['type' => 'index', 'columns' => ['clientes_id'], 'length' => []],
            'gotas_has_historico_valores2_idx' => ['type' => 'index', 'columns' => ['gotas_id'], 'length' => []],
            'gotas_has_historico_valores3_idx' => ['type' => 'index', 'columns' => ['preco'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'fk_gotas_clientes_id' => ['type' => 'foreign', 'columns' => ['clientes_id'], 'references' => ['clientes', 'id'], 'update' => 'cascade', 'delete' => 'cascade', 'length' => []],
            'fk_gotas_gotas_id' => ['type' => 'foreign', 'columns' => ['gotas_id'], 'references' => ['gotas', 'id'], 'update' => 'cascade', 'delete' => 'cascade', 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_unicode_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd

    /**
     * Init method
     *
     * @return void
     */
    public function init()
    {
        $this->records = [
            [
                'id' => 1,
                'clientes_id' => 1,
                'gotas_id' => 1,
                'preco' => 1.5,
                'audit_insert' => '2019-01-13 22:43:11',
                'audit_update' => '2019-01-13 22:43:11'
            ],
        ];
        parent::init();
    }
}
