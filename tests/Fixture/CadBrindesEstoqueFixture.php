<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * CadBrindesEstoqueFixture
 *
 */
class CadBrindesEstoqueFixture extends TestFixture
{

    /**
     * Table name
     *
     * @var string
     */
    public $table = 'cad_brindes_estoque';

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'cad_brindes_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'cad_usuarios_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'quantidade' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'tipo_operacao' => ['type' => 'integer', 'length' => 6, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'data' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => 'CURRENT_TIMESTAMP', 'comment' => '', 'precision' => null],
        'audit_insert' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'audit_update' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        '_indexes' => [
            'cad_brindes_estoque_id_idx' => ['type' => 'index', 'columns' => ['id'], 'length' => []],
            'fk_cad_brindes_estoque_cad_brindes1_idx' => ['type' => 'index', 'columns' => ['cad_brindes_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'fk_cad_estoque_brindes_cad_brindes1' => ['type' => 'foreign', 'columns' => ['cad_brindes_id'], 'references' => ['cad_brindes', 'id'], 'update' => 'cascade', 'delete' => 'cascade', 'length' => []],
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
            'cad_brindes_id' => 1,
            'cad_usuarios_id' => 1,
            'quantidade' => 1,
            'tipo_operacao' => 1,
            'data' => '2017-07-06 14:05:41',
            'audit_insert' => '2017-07-06 14:05:41',
            'audit_update' => '2017-07-06 14:05:41'
        ],
    ];
}
