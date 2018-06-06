<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * BrindesEstoqueFixture
 *
 */
class BrindesEstoqueFixture extends TestFixture
{

    /**
     * Table name
     *
     * @var string
     */
    public $table = 'brindes_estoque';

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'brindes_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'usuarios_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'quantidade' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'tipo_operacao' => ['type' => 'integer', 'length' => 6, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'data' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => 'CURRENT_TIMESTAMP', 'comment' => '', 'precision' => null],
        'audit_insert' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'audit_update' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        '_indexes' => [
            'brindes_estoque_id_idx' => ['type' => 'index', 'columns' => ['id'], 'length' => []],
            'fk_brindes_estoque_brindes1_idx' => ['type' => 'index', 'columns' => ['brindes_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'fk_estoque_brindes_brindes1' => ['type' => 'foreign', 'columns' => ['brindes_id'], 'references' => ['brindes', 'id'], 'update' => 'cascade', 'delete' => 'cascade', 'length' => []],
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
            'brindes_id' => 1,
            'usuarios_id' => 1,
            'quantidade' => 1,
            'tipo_operacao' => 1,
            'data' => '2017-07-07 03:43:19',
            'audit_insert' => '2017-07-07 03:43:19',
            'audit_update' => '2017-07-07 03:43:19'
        ],
    ];
}
