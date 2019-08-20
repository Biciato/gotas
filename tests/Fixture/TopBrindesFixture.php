<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * TopBrindesFixture
 *
 */
class TopBrindesFixture extends TestFixture
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
        'clientes_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => 'Id de Clientes da Rede', 'precision' => null, 'autoIncrement' => null],
        'brindes_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => 'Id do Brinde', 'precision' => null, 'autoIncrement' => null],
        'posicao' => ['type' => 'smallinteger', 'length' => 1, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => 'Posição do Brinde', 'precision' => null],
        'tipo' => ['type' => 'string', 'length' => null, 'null' => true, 'default' => 'Nacional', 'collate' => 'latin1_general_ci', 'comment' => 'Tipo de Top Brindes', 'precision' => null, 'fixed' => null],
        'habilitado' => ['type' => 'boolean', 'length' => null, 'null' => true, 'default' => '1', 'comment' => 'Habilitado/Desabilitado', 'precision' => null],
        'data' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => 'CURRENT_TIMESTAMP', 'comment' => 'Data/Hora Cadastro', 'precision' => null],
        'audit_user_insert_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => 'Usuário que cadastrou', 'precision' => null, 'autoIncrement' => null],
        'audit_insert' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'audit_update' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        '_indexes' => [
            'FK_top_brindes_clientes' => ['type' => 'index', 'columns' => ['clientes_id'], 'length' => []],
            'FK_top_brindes_brindes' => ['type' => 'index', 'columns' => ['brindes_id'], 'length' => []],
            'FK_top_brindes_audit_user_insert' => ['type' => 'index', 'columns' => ['audit_user_insert_id'], 'length' => []],
            'top_brindes_1_idx' => ['type' => 'index', 'columns' => ['redes_id', 'clientes_id', 'brindes_id', 'posicao', 'habilitado', 'data', 'audit_user_insert_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'FK_top_brindes_audit_user_insert' => ['type' => 'foreign', 'columns' => ['audit_user_insert_id'], 'references' => ['usuarios', 'id'], 'update' => 'restrict', 'delete' => 'restrict', 'length' => []],
            'FK_top_brindes_brindes' => ['type' => 'foreign', 'columns' => ['brindes_id'], 'references' => ['brindes', 'id'], 'update' => 'restrict', 'delete' => 'restrict', 'length' => []],
            'FK_top_brindes_clientes' => ['type' => 'foreign', 'columns' => ['clientes_id'], 'references' => ['clientes', 'id'], 'update' => 'restrict', 'delete' => 'restrict', 'length' => []],
            'FK_top_brindes_redes' => ['type' => 'foreign', 'columns' => ['redes_id'], 'references' => ['redes', 'id'], 'update' => 'restrict', 'delete' => 'restrict', 'length' => []],
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
                'brindes_id' => 1,
                'posicao' => 1,
                'tipo' => 'Lorem ipsum dolor sit amet',
                'habilitado' => 1,
                'data' => '2019-08-01 12:35:12',
                'audit_user_insert_id' => 1,
                'audit_insert' => '2019-08-01 12:35:12',
                'audit_update' => '2019-08-01 12:35:12'
            ],
        ];
        parent::init();
    }
}
