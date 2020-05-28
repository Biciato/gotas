<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * UsuariosHasVeiculosFixture
 *
 */
class UsuariosHasVeiculosFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'usuarios_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'veiculos_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        '_indexes' => [
            'fk_usuarios_has_veiculos_veiculos1_idx' => ['type' => 'index', 'columns' => ['veiculos_id'], 'length' => []],
            'fk_usuarios_has_veiculos_usuarios1_idx' => ['type' => 'index', 'columns' => ['usuarios_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'fk_usuarios_has_veiculos_usuarios1' => ['type' => 'foreign', 'columns' => ['usuarios_id'], 'references' => ['usuarios', 'id'], 'update' => 'cascade', 'delete' => 'cascade', 'length' => []],
            'fk_usuarios_has_veiculos_veiculos1' => ['type' => 'foreign', 'columns' => ['veiculos_id'], 'references' => ['veiculos', 'id'], 'update' => 'cascade', 'delete' => 'cascade', 'length' => []],
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
            'usuarios_id' => 1,
            'veiculos_id' => 1
        ],
    ];
}
