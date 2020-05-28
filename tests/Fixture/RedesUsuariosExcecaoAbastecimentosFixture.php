<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * RedesUsuariosExcecaoAbastecimentosFixture
 *
 */
class RedesUsuariosExcecaoAbastecimentosFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'redes_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => 'Rede que autorizou a exceÃ§Ã£o da regra', 'precision' => null, 'autoIncrement' => null],
        'adm_rede_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => 'Administrador que autorizou a exceÃ§Ã£o da regra', 'precision' => null, 'autoIncrement' => null],
        'usuarios_id' => ['type' => 'biginteger', 'length' => 20, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => 'UsuÃ¡rio utilizador da regra', 'precision' => null, 'autoIncrement' => null],
        'quantidade_dia' => ['type' => 'smallinteger', 'length' => 2, 'unsigned' => false, 'null' => true, 'default' => '10', 'comment' => 'Quantidade de vezes permitida por dia', 'precision' => null],
        'validade' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => 'Validade da ExceÃ§Ã£o para UsuÃ¡rio', 'precision' => null],
        'habilitado' => ['type' => 'boolean', 'length' => null, 'null' => true, 'default' => '1', 'comment' => 'Status', 'precision' => null],
        'audit_insert' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'audit_update' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        '_indexes' => [
            'redes_usuarios_excecao_abastecimentos_1_idx' => ['type' => 'index', 'columns' => ['redes_id'], 'length' => []],
            'redes_usuarios_excecao_abastecimentos_2_idx' => ['type' => 'index', 'columns' => ['adm_rede_id'], 'length' => []],
            'redes_usuarios_excecao_abastecimentos_3_idx' => ['type' => 'index', 'columns' => ['usuarios_id'], 'length' => []],
            'redes_usuarios_excecao_abastecimentos_4_idx' => ['type' => 'index', 'columns' => ['redes_id', 'adm_rede_id'], 'length' => []],
            'redes_usuarios_excecao_abastecimentos_5_idx' => ['type' => 'index', 'columns' => ['redes_id', 'usuarios_id'], 'length' => []],
            'redes_usuarios_excecao_abastecimentos_6_idx' => ['type' => 'index', 'columns' => ['redes_id', 'adm_rede_id', 'usuarios_id'], 'length' => []],
            'redes_usuarios_excecao_abastecimentos_7_idx' => ['type' => 'index', 'columns' => ['quantidade_dia'], 'length' => []],
            'redes_usuarios_excecao_abastecimentos_8_idx' => ['type' => 'index', 'columns' => ['validade'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'fk_redes_usuarios_excecao_abastecimentos_1' => ['type' => 'foreign', 'columns' => ['redes_id'], 'references' => ['redes', 'id'], 'update' => 'noAction', 'delete' => 'cascade', 'length' => []],
            'fk_redes_usuarios_excecao_abastecimentos_2' => ['type' => 'foreign', 'columns' => ['adm_rede_id'], 'references' => ['usuarios', 'id'], 'update' => 'noAction', 'delete' => 'restrict', 'length' => []],
            'fk_redes_usuarios_excecao_abastecimentos_3' => ['type' => 'foreign', 'columns' => ['usuarios_id'], 'references' => ['usuarios', 'id'], 'update' => 'noAction', 'delete' => 'restrict', 'length' => []],
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
                'adm_rede_id' => 1,
                'usuarios_id' => 1,
                'quantidade_dia' => 1,
                'validade' => '2019-06-16 22:37:53',
                'habilitado' => 1,
                'audit_insert' => '2019-06-16 22:37:53',
                'audit_update' => '2019-06-16 22:37:53'
            ],
        ];
        parent::init();
    }
}
