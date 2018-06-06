<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * GeneroBrindesClientesFixture
 *
 */
class GeneroBrindesClientesFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'genero_brindes_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'clientes_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'tipo_principal_codigo_brinde' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'tipo_secundario_codigo_brinde' => ['type' => 'integer', 'length' => 2, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'habilitado' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => '1', 'comment' => '', 'precision' => null],
        'audit_insert' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'audit_update' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        '_indexes' => [
            'genero_brindes_clientes_id_idx' => ['type' => 'index', 'columns' => ['id'], 'length' => []],
            'FK_genero_brindes_clientes_clientes_id' => ['type' => 'index', 'columns' => ['clientes_id'], 'length' => []],
            'tbl_genero_brindes_clientes_1' => ['type' => 'index', 'columns' => ['genero_brindes_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'FK_genero_brindes_clientes_clientes_id' => ['type' => 'foreign', 'columns' => ['clientes_id'], 'references' => ['clientes', 'id'], 'update' => 'noAction', 'delete' => 'cascade', 'length' => []],
            'FK_genero_brindes_clientes_genero_brindes_id' => ['type' => 'foreign', 'columns' => ['genero_brindes_id'], 'references' => ['genero_brindes', 'id'], 'update' => 'noAction', 'delete' => 'cascade', 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'latin1_general_ci'
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
            'genero_brindes_id' => 1,
            'clientes_id' => 1,
            'tipo_principal_codigo_brinde' => 1,
            'tipo_secundario_codigo_brinde' => 1,
            'habilitado' => 1,
            'audit_insert' => '2018-06-02 17:28:34',
            'audit_update' => '2018-06-02 17:28:34'
        ],
    ];
}
