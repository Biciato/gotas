<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * CadUsuariosHasCadVeiculosFixture
 *
 */
class CadUsuariosHasCadVeiculosFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'cad_usuarios_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'cad_veiculos_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        '_indexes' => [
            'fk_cad_usuarios_has_cad_veiculos_cad_veiculos1_idx' => ['type' => 'index', 'columns' => ['cad_veiculos_id'], 'length' => []],
            'fk_cad_usuarios_has_cad_veiculos_cad_usuarios1_idx' => ['type' => 'index', 'columns' => ['cad_usuarios_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'fk_cad_usuarios_has_cad_veiculos_cad_usuarios1' => ['type' => 'foreign', 'columns' => ['cad_usuarios_id'], 'references' => ['cad_usuarios', 'id'], 'update' => 'cascade', 'delete' => 'cascade', 'length' => []],
            'fk_cad_usuarios_has_cad_veiculos_cad_veiculos1' => ['type' => 'foreign', 'columns' => ['cad_veiculos_id'], 'references' => ['cad_veiculos', 'id'], 'update' => 'cascade', 'delete' => 'cascade', 'length' => []],
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
            'cad_usuarios_id' => 1,
            'cad_veiculos_id' => 1
        ],
    ];
}
