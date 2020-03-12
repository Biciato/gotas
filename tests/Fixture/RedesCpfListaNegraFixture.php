<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * RedesCpfListaNegraFixture
 *
 */
class RedesCpfListaNegraFixture extends TestFixture
{

    /**
     * Table name
     *
     * @var string
     */
    public $table = 'redes_cpf_lista_negra';

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'redes_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => 'Id da Rede', 'precision' => null, 'autoIncrement' => null],
        'cpf' => ['type' => 'string', 'length' => 20, 'null' => false, 'default' => null, 'collate' => 'latin1_general_ci', 'comment' => 'CPF bloqueado', 'precision' => null, 'fixed' => null],
        'habilitado' => ['type' => 'boolean', 'length' => null, 'null' => true, 'default' => '1', 'comment' => 'Habilitado/Desabilitado', 'precision' => null],
        'data' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => 'CURRENT_TIMESTAMP', 'comment' => 'Data/Hora Cadastro', 'precision' => null],
        'audit_user_insert_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => 'Usuário que cadastrou', 'precision' => null, 'autoIncrement' => null],
        'audit_insert' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'audit_update' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        '_indexes' => [
            'FK_redes_cpf_cadastrados_audit_user_insert' => ['type' => 'index', 'columns' => ['audit_user_insert_id'], 'length' => []],
            'redes_cpf_cadastrados_idx' => ['type' => 'index', 'columns' => ['redes_id', 'cpf', 'habilitado', 'data', 'audit_user_insert_id', 'audit_insert', 'audit_update'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'FK_redes_cpf_cadastrados_audit_user_insert' => ['type' => 'foreign', 'columns' => ['audit_user_insert_id'], 'references' => ['usuarios', 'id'], 'update' => 'restrict', 'delete' => 'restrict', 'length' => []],
            'FK_redes_cpf_cadastrados_redes' => ['type' => 'foreign', 'columns' => ['redes_id'], 'references' => ['redes', 'id'], 'update' => 'restrict', 'delete' => 'restrict', 'length' => []],
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
                'cpf' => 'Lorem ipsum dolor ',
                'habilitado' => 1,
                'data' => '2020-03-11 17:32:20',
                'audit_user_insert_id' => 1,
                'audit_insert' => '2020-03-11 17:32:20',
                'audit_update' => '2020-03-11 17:32:20'
            ],
        ];
        parent::init();
    }
}
