<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\RedesUsuariosExcecaoAbastecimentosTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\RedesUsuariosExcecaoAbastecimentosTable Test Case
 */
class RedesUsuariosExcecaoAbastecimentosTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\RedesUsuariosExcecaoAbastecimentosTable
     */
    public $RedesUsuariosExcecaoAbastecimentos;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.redes_usuarios_excecao_abastecimentos',
        'app.redes',
        'app.usuarios'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('RedesUsuariosExcecaoAbastecimentos') ? [] : ['className' => RedesUsuariosExcecaoAbastecimentosTable::class];
        $this->RedesUsuariosExcecaoAbastecimentos = TableRegistry::get('RedesUsuariosExcecaoAbastecimentos', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->RedesUsuariosExcecaoAbastecimentos);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
