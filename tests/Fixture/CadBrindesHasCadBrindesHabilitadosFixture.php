<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * CadBrindesHasCadBrindesHabilitadosFixture
 *
 */
class CadBrindesHasCadBrindesHabilitadosFixture extends TestFixture
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
        'cad_brindes_habilitados_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        '_indexes' => [
            'fk_cad_brindes_has_cad_brindes_habilitados_cad_brindes_habi_idx' => ['type' => 'index', 'columns' => ['cad_brindes_habilitados_id'], 'length' => []],
            'fk_cad_brindes_has_cad_brindes_habilitados_cad_brindes1_idx' => ['type' => 'index', 'columns' => ['cad_brindes_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'fk_cad_brindes_has_cad_brindes_habilitados_cad_brindes1' => ['type' => 'foreign', 'columns' => ['cad_brindes_id'], 'references' => ['cad_brindes', 'id'], 'update' => 'cascade', 'delete' => 'cascade', 'length' => []],
            'fk_cad_brindes_has_cad_brindes_habilitados_cad_brindes_habili1' => ['type' => 'foreign', 'columns' => ['cad_brindes_habilitados_id'], 'references' => ['cad_brindes_habilitados', 'id'], 'update' => 'cascade', 'delete' => 'cascade', 'length' => []],
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
            'cad_brindes_habilitados_id' => 1
        ],
    ];
}
