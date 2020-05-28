<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * CadTransportadorasHasCadUsuariosFixture
 *
 */
class CadTransportadorasHasCadUsuariosFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'cad_transportadoras_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'cad_usuarios_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        '_indexes' => [
            'fk_cad_transportadoras_has_cad_usuarios_cad_usuarios1_idx' => ['type' => 'index', 'columns' => ['cad_usuarios_id'], 'length' => []],
            'fk_cad_transportadoras_has_cad_usuarios_cad_transportadoras_idx' => ['type' => 'index', 'columns' => ['cad_transportadoras_id'], 'length' => []],
            'cad_transportadoras_has_cad_usuarios_id_idx' => ['type' => 'index', 'columns' => ['id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'fk_cad_transportadoras_has_cad_usuarios_cad_transportadoras1' => ['type' => 'foreign', 'columns' => ['cad_transportadoras_id'], 'references' => ['cad_transportadoras', 'id'], 'update' => 'noAction', 'delete' => 'noAction', 'length' => []],
            'fk_cad_transportadoras_has_cad_usuarios_cad_usuarios1' => ['type' => 'foreign', 'columns' => ['cad_usuarios_id'], 'references' => ['cad_usuarios', 'id'], 'update' => 'noAction', 'delete' => 'noAction', 'length' => []],
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
            'cad_transportadoras_id' => 1,
            'cad_usuarios_id' => 1
        ],
    ];
}
