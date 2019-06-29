<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * CuponsTransacoesFixture
 *
 */
class CuponsTransacoesFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'redes_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => 'Id da Rede', 'precision' => null, 'autoIncrement' => null],
        'clientes_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => 'Id de Unidades da Rede (clientes)', 'precision' => null, 'autoIncrement' => null],
        'cupons_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => 'Id do Cupom', 'precision' => null, 'autoIncrement' => null],
        'brindes_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => 'Id do Brinde', 'precision' => null, 'autoIncrement' => null],
        'clientes_has_quadro_horario_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => 'Id do Turno (clientes_has_quadro_horarios)', 'precision' => null, 'autoIncrement' => null],
        'funcionarios_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => 'Id do Funcionário (usuarios)', 'precision' => null, 'autoIncrement' => null],
        'tipo_operacao' => ['type' => 'string', 'length' => null, 'null' => false, 'default' => null, 'collate' => 'latin1_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'DATA' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => 'CURRENT_TIMESTAMP', 'comment' => 'Data/Hora da Operação', 'precision' => null],
        'audit_insert' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'audit_update' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        '_indexes' => [
            'FK_cupons_transacoes_clientes' => ['type' => 'index', 'columns' => ['clientes_id'], 'length' => []],
            'FK_cupons_transacoes_cupons' => ['type' => 'index', 'columns' => ['cupons_id'], 'length' => []],
            'FK_cupons_transacoes_brindes' => ['type' => 'index', 'columns' => ['brindes_id'], 'length' => []],
            'FK_cupons_transacoes_clientes_has_quadro_horario' => ['type' => 'index', 'columns' => ['clientes_has_quadro_horario_id'], 'length' => []],
            'FK_cupons_transacoes_funcionarios' => ['type' => 'index', 'columns' => ['funcionarios_id'], 'length' => []],
            'cupons_transacoes_1_idx' => ['type' => 'index', 'columns' => ['redes_id', 'clientes_id', 'cupons_id', 'brindes_id', 'clientes_has_quadro_horario_id', 'funcionarios_id', 'tipo_operacao', 'DATA'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'FK_cupons_transacoes_brindes' => ['type' => 'foreign', 'columns' => ['brindes_id'], 'references' => ['brindes', 'id'], 'update' => 'restrict', 'delete' => 'restrict', 'length' => []],
            'FK_cupons_transacoes_clientes' => ['type' => 'foreign', 'columns' => ['clientes_id'], 'references' => ['clientes', 'id'], 'update' => 'restrict', 'delete' => 'restrict', 'length' => []],
            'FK_cupons_transacoes_clientes_has_quadro_horario' => ['type' => 'foreign', 'columns' => ['clientes_has_quadro_horario_id'], 'references' => ['clientes_has_quadro_horario', 'id'], 'update' => 'restrict', 'delete' => 'restrict', 'length' => []],
            'FK_cupons_transacoes_cupons' => ['type' => 'foreign', 'columns' => ['cupons_id'], 'references' => ['cupons', 'id'], 'update' => 'restrict', 'delete' => 'restrict', 'length' => []],
            'FK_cupons_transacoes_funcionarios' => ['type' => 'foreign', 'columns' => ['funcionarios_id'], 'references' => ['usuarios', 'id'], 'update' => 'restrict', 'delete' => 'restrict', 'length' => []],
            'FK_cupons_transacoes_redes' => ['type' => 'foreign', 'columns' => ['redes_id'], 'references' => ['redes', 'id'], 'update' => 'restrict', 'delete' => 'restrict', 'length' => []],
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
                'redes_id' => 1,
                'clientes_id' => 1,
                'cupons_id' => 1,
                'brindes_id' => 1,
                'clientes_has_quadro_horario_id' => 1,
                'funcionarios_id' => 1,
                'tipo_operacao' => 'Lorem ipsum dolor sit amet',
                'DATA' => '2019-06-28 16:13:21',
                'audit_insert' => '2019-06-28 16:13:21',
                'audit_update' => '2019-06-28 16:13:21'
            ],
        ];
        parent::init();
    }
}
