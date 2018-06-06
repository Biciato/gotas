<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * CadClientesHasCadBrindesHabilitadosFixture
 *
 */
class CadClientesHasCadBrindesHabilitadosFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'cad_clientes_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'cad_brindes_habilitados_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        '_indexes' => [
            'fk_cad_clientes_has_cad_brindes_habilitados_cad_brindes_hab_idx' => ['type' => 'index', 'columns' => ['cad_brindes_habilitados_id'], 'length' => []],
            'fk_cad_clientes_has_cad_brindes_habilitados_cad_clientes1_idx' => ['type' => 'index', 'columns' => ['cad_clientes_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'fk_cad_clientes_has_cad_brindes_habilitados_cad_brindes_habil1' => ['type' => 'foreign', 'columns' => ['cad_brindes_habilitados_id'], 'references' => ['cad_brindes_habilitados', 'id'], 'update' => 'cascade', 'delete' => 'cascade', 'length' => []],
            'fk_cad_clientes_has_cad_brindes_habilitados_cad_clientes1' => ['type' => 'foreign', 'columns' => ['cad_clientes_id'], 'references' => ['cad_clientes', 'id'], 'update' => 'cascade', 'delete' => 'cascade', 'length' => []],
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
            'cad_clientes_id' => 1,
            'cad_brindes_habilitados_id' => 1
        ],
    ];
}
