<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * CadUsuariosFixture
 *
 */
class CadUsuariosFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'tipo_perfil' => ['type' => 'integer', 'length' => 6, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'nome' => ['type' => 'string', 'length' => 60, 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'cpf' => ['type' => 'string', 'length' => 11, 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'sexo' => ['type' => 'integer', 'length' => 4, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'data_nasc' => ['type' => 'date', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'email' => ['type' => 'string', 'length' => 60, 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'senha' => ['type' => 'string', 'fixed' => true, 'length' => 76, 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null],
        'telefone' => ['type' => 'string', 'length' => 12, 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'tipo_logradouro' => ['type' => 'integer', 'length' => 6, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'endereco' => ['type' => 'string', 'length' => 45, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'endereco_numero' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'endereco_complemento' => ['type' => 'string', 'length' => 20, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'bairro' => ['type' => 'string', 'length' => 45, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'municipio' => ['type' => 'string', 'length' => 45, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'estado' => ['type' => 'string', 'length' => 2, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'cep' => ['type' => 'string', 'length' => 8, 'null' => true, 'default' => null, 'collate' => 'latin1_swedish_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'audit_insert' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'audit_update' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        '_indexes' => [
            'cad_usuarios_id_idx' => ['type' => 'index', 'columns' => ['id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
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
            'tipo_perfil' => 1,
            'nome' => 'Lorem ipsum dolor sit amet',
            'cpf' => 'Lorem ips',
            'sexo' => 1,
            'data_nasc' => '2017-07-06',
            'email' => 'Lorem ipsum dolor sit amet',
            'senha' => 'Lorem ipsum dolor sit amet',
            'telefone' => 'Lorem ipsu',
            'tipo_logradouro' => 1,
            'endereco' => 'Lorem ipsum dolor sit amet',
            'endereco_numero' => 1,
            'endereco_complemento' => 'Lorem ipsum dolor ',
            'bairro' => 'Lorem ipsum dolor sit amet',
            'municipio' => 'Lorem ipsum dolor sit amet',
            'estado' => '',
            'cep' => 'Lorem ',
            'audit_insert' => '2017-07-06 14:05:46',
            'audit_update' => '2017-07-06 14:05:46'
        ],
    ];
}
