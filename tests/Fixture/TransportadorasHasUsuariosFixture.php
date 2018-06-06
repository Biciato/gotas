<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * TransportadorasHasUsuariosFixture
 *
 */
class TransportadorasHasUsuariosFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'transportadoras_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'usuarios_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        '_indexes' => [
            'fk_transportadoras_has_usuarios_usuarios1_idx' => ['type' => 'index', 'columns' => ['usuarios_id'], 'length' => []],
            'fk_transportadoras_has_usuarios_transportadoras_idx' => ['type' => 'index', 'columns' => ['transportadoras_id'], 'length' => []],
            'transportadoras_has_usuarios_id_idx' => ['type' => 'index', 'columns' => ['id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'fk_transportadoras_has_usuarios_transportadoras1' => ['type' => 'foreign', 'columns' => ['transportadoras_id'], 'references' => ['transportadoras', 'id'], 'update' => 'noAction', 'delete' => 'noAction', 'length' => []],
            'fk_transportadoras_has_usuarios_usuarios1' => ['type' => 'foreign', 'columns' => ['usuarios_id'], 'references' => ['usuarios', 'id'], 'update' => 'noAction', 'delete' => 'noAction', 'length' => []],
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
            'transportadoras_id' => 1,
            'usuarios_id' => 1
        ],
    ];
}
