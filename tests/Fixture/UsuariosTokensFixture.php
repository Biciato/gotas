<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * UsuariosTokensFixture
 *
 */
class UsuariosTokensFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'usuarios_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => 'Id do Usuário', 'precision' => null, 'autoIncrement' => null],
        'tipo' => ['type' => 'string', 'length' => null, 'null' => true, 'default' => 'WEB', 'collate' => 'latin1_general_ci', 'comment' => 'Tipo de Sessão', 'precision' => null, 'fixed' => null],
        'token' => ['type' => 'string', 'length' => 200, 'null' => true, 'default' => null, 'collate' => 'latin1_general_ci', 'comment' => 'Token API do usuário', 'precision' => null, 'fixed' => null],
        'audit_insert' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'audit_update' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        '_indexes' => [
            'usuarios_tokens_1_idx' => ['type' => 'index', 'columns' => ['usuarios_id'], 'length' => []],
            'usuarios_tokens_2_idx' => ['type' => 'index', 'columns' => ['tipo'], 'length' => []],
            'usuarios_tokens_3_idx' => ['type' => 'index', 'columns' => ['token'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'fk_usuarios_tokens_usuarios_id' => ['type' => 'foreign', 'columns' => ['usuarios_id'], 'references' => ['usuarios', 'id'], 'update' => 'cascade', 'delete' => 'cascade', 'length' => []],
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
                'usuarios_id' => 1,
                'tipo' => 'Lorem ipsum dolor sit amet',
                'token' => 'Lorem ipsum dolor sit amet',
                'audit_insert' => '2019-05-30 00:37:34',
                'audit_update' => '2019-05-30 00:37:34'
            ],
        ];
        parent::init();
    }
}
