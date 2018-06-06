<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * CadBrindesHabilitadosFixture
 *
 */
class CadBrindesHabilitadosFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'cad_brindes_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'cad_clientes_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'habilitado' => ['type' => 'integer', 'length' => 4, 'unsigned' => false, 'null' => false, 'default' => '1', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'audit_insert' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'audit_update' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        '_indexes' => [
            'cad_brindes_habilitados_id_idx' => ['type' => 'index', 'columns' => ['id'], 'length' => []],
            'cad_brindes_habilitados_cad_brindes_id_idx' => ['type' => 'index', 'columns' => ['cad_brindes_id', 'id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
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
            'cad_clientes_id' => 1,
            'habilitado' => 1,
            'audit_insert' => '2017-07-06 14:05:42',
            'audit_update' => '2017-07-06 14:05:42'
        ],
    ];
}
