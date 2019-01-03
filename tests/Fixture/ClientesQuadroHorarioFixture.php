<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ClientesHasQuadroHorarioFixture
 *
 */
class ClientesHasQuadroHorarioFixture extends TestFixture
{

    /**
     * Table name
     *
     * @var string
     */
    public $table = 'clientes_has_quadro_horario';

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'clientes_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'horario' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'audit_insert' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'audit_update' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        '_indexes' => [
            'clientes_has_quadro_horario_idx' => ['type' => 'index', 'columns' => ['id'], 'length' => []],
            'clientes_has_quadro_horario_clientes_id_idx' => ['type' => 'index', 'columns' => ['clientes_id'], 'length' => []],
            'clientes_has_quadro_horario_horario_idx' => ['type' => 'index', 'columns' => ['horario'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'fk_clientes_has_quadro_horario_clientes_id' => ['type' => 'foreign', 'columns' => ['clientes_id'], 'references' => ['clientes', 'id'], 'update' => 'noAction', 'delete' => 'noAction', 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'latin1_general_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd

    /**
     * Init method
     *
     * @return void
     */
    public function init()
    {
        $this->records = [
            [
                'id' => 1,
                'clientes_id' => 1,
                'horario' => '2018-12-27 00:25:37',
                'audit_insert' => '2018-12-27 00:25:37',
                'audit_update' => '2018-12-27 00:25:37'
            ],
        ];
        parent::init();
    }
}
