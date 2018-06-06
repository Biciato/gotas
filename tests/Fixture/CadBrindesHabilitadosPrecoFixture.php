<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * CadBrindesHabilitadosPrecoFixture
 *
 */
class CadBrindesHabilitadosPrecoFixture extends TestFixture
{

    /**
     * Table name
     *
     * @var string
     */
    public $table = 'cad_brindes_habilitados_preco';

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'cad_brindes_habilitados_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'preco' => ['type' => 'float', 'length' => null, 'precision' => null, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => ''],
        'data_preco' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'audit_insert' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'audit_update' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        '_indexes' => [
            'cad_brindes_habilitados_preco_id_idx' => ['type' => 'index', 'columns' => ['id'], 'length' => []],
            'fk_cad_brindes_habilitados_preco_cad_brindes_habilitados1_idx' => ['type' => 'index', 'columns' => ['cad_brindes_habilitados_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'fk_cad_brindes_habilitados_preco_cad_brindes_habilitados1' => ['type' => 'foreign', 'columns' => ['cad_brindes_habilitados_id'], 'references' => ['cad_brindes_habilitados', 'id'], 'update' => 'cascade', 'delete' => 'cascade', 'length' => []],
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
            'cad_brindes_habilitados_id' => 1,
            'preco' => 1,
            'data_preco' => '2017-07-06 14:05:42',
            'audit_insert' => '2017-07-06 14:05:42',
            'audit_update' => '2017-07-06 14:05:42'
        ],
    ];
}
