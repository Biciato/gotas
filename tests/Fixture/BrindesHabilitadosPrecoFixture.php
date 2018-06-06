<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * BrindesHabilitadosPrecoFixture
 *
 */
class BrindesHabilitadosPrecoFixture extends TestFixture
{

    /**
     * Table name
     *
     * @var string
     */
    public $table = 'brindes_habilitados_preco';

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'brindes_habilitados_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'preco' => ['type' => 'float', 'length' => null, 'precision' => null, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => ''],
        'data_preco' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'audit_insert' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'audit_update' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        '_indexes' => [
            'brindes_habilitados_preco_id_idx' => ['type' => 'index', 'columns' => ['id'], 'length' => []],
            'fk_brindes_habilitados_preco_brindes_habilitados1_idx' => ['type' => 'index', 'columns' => ['brindes_habilitados_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'fk_brindes_habilitados_preco_brindes_habilitados1' => ['type' => 'foreign', 'columns' => ['brindes_habilitados_id'], 'references' => ['brindes_habilitados', 'id'], 'update' => 'cascade', 'delete' => 'cascade', 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'latin1_swedish_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => 1,
            'brindes_habilitados_id' => 1,
            'preco' => 1,
            'data_preco' => '2017-07-07 03:43:20',
            'audit_insert' => '2017-07-07 03:43:20',
            'audit_update' => '2017-07-07 03:43:20'
        ],
    ];
}
