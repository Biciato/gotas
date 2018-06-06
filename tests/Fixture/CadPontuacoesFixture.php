<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * CadPontuacoesFixture
 *
 */
class CadPontuacoesFixture extends TestFixture
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
        'cad_brindes_habilitados_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'cad_gotas_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'quantidade' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'data' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => 'CURRENT_TIMESTAMP', 'comment' => '', 'precision' => null],
        'audit_insert' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'audit_update' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        '_indexes' => [
            'cad_pontuacoes_id_idx' => ['type' => 'index', 'columns' => ['id'], 'length' => []],
            'fk_cad_pontuacoes_cad_usuarios1_idx' => ['type' => 'index', 'columns' => ['cad_usuarios_id'], 'length' => []],
            'fk_cad_pontuacoes_cad_gotas1_idx' => ['type' => 'index', 'columns' => ['cad_gotas_id'], 'length' => []],
            'fk_cad_pontuacoes_cad_brindes_habilitados1_idx' => ['type' => 'index', 'columns' => ['cad_brindes_habilitados_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'fk_cad_pontuacoes_cad_brindes_habilitados1' => ['type' => 'foreign', 'columns' => ['cad_brindes_habilitados_id'], 'references' => ['cad_brindes_habilitados', 'id'], 'update' => 'cascade', 'delete' => 'cascade', 'length' => []],
            'fk_cad_pontuacoes_cad_gotas1' => ['type' => 'foreign', 'columns' => ['cad_gotas_id'], 'references' => ['cad_gotas', 'id'], 'update' => 'cascade', 'delete' => 'cascade', 'length' => []],
            'fk_cad_pontuacoes_cad_usuarios1' => ['type' => 'foreign', 'columns' => ['cad_usuarios_id'], 'references' => ['cad_usuarios', 'id'], 'update' => 'cascade', 'delete' => 'cascade', 'length' => []],
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
            'cad_brindes_habilitados_id' => 1,
            'cad_gotas_id' => 1,
            'quantidade' => 1,
            'data' => '2017-07-06 14:05:45',
            'audit_insert' => '2017-07-06 14:05:45',
            'audit_update' => '2017-07-06 14:05:45'
        ],
    ];
}
