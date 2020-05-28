<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * BrindesHasBrindesHabilitadosFixture
 *
 */
class BrindesHasBrindesHabilitadosFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'brindes_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'brindes_habilitados_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        '_indexes' => [
            'fk_brindes_has_brindes_habilitados_brindes_habi_idx' => ['type' => 'index', 'columns' => ['brindes_habilitados_id'], 'length' => []],
            'fk_brindes_has_brindes_habilitados_brindes1_idx' => ['type' => 'index', 'columns' => ['brindes_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'fk_brindes_has_brindes_habilitados_brindes1' => ['type' => 'foreign', 'columns' => ['brindes_id'], 'references' => ['brindes', 'id'], 'update' => 'cascade', 'delete' => 'cascade', 'length' => []],
            'fk_brindes_has_brindes_habilitados_brindes_habili1' => ['type' => 'foreign', 'columns' => ['brindes_habilitados_id'], 'references' => ['brindes_habilitados', 'id'], 'update' => 'cascade', 'delete' => 'cascade', 'length' => []],
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
            'brindes_habilitados_id' => 1
        ],
    ];
}
