<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * PontuacoesFixture
 *
 */
class PontuacoesFixture extends TestFixture
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
        'brindes_habilitados_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'gotas_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'quantidade' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'data' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => 'CURRENT_TIMESTAMP', 'comment' => '', 'precision' => null],
        'audit_insert' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'audit_update' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        '_indexes' => [
            'fk_pontuacoes_usuarios1_idx' => ['type' => 'index', 'columns' => ['usuarios_id'], 'length' => []],
            'fk_pontuacoes_gotas1_idx' => ['type' => 'index', 'columns' => ['gotas_id'], 'length' => []],
            'fk_pontuacoes_brindes_habilitados1_idx' => ['type' => 'index', 'columns' => ['brindes_habilitados_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'fk_pontuacoes_brindes_habilitados1' => ['type' => 'foreign', 'columns' => ['brindes_habilitados_id'], 'references' => ['brindes_habilitados', 'id'], 'update' => 'cascade', 'delete' => 'cascade', 'length' => []],
            'fk_pontuacoes_gotas1' => ['type' => 'foreign', 'columns' => ['gotas_id'], 'references' => ['gotas', 'id'], 'update' => 'cascade', 'delete' => 'cascade', 'length' => []],
            'fk_pontuacoes_usuarios1' => ['type' => 'foreign', 'columns' => ['usuarios_id'], 'references' => ['usuarios', 'id'], 'update' => 'cascade', 'delete' => 'cascade', 'length' => []],
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
            'brindes_habilitados_id' => 1,
            'gotas_id' => 1,
            'quantidade' => 1,
            'data' => '2017-07-07 03:43:24',
            'audit_insert' => '2017-07-07 03:43:24',
            'audit_update' => '2017-07-07 03:43:24'
        ],
    ];
}
