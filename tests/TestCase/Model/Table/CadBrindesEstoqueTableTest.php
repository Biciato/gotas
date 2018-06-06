<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CadBrindesEstoqueTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CadBrindesEstoqueTable Test Case
 */
class CadBrindesEstoqueTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\CadBrindesEstoqueTable
     */
    public $CadBrindesEstoque;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.cad_brindes_estoque',
        'app.cad_brindes',
        'app.cad_clientes',
        'app.cad_usuarios'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('CadBrindesEstoque') ? [] : ['className' => CadBrindesEstoqueTable::class];
        $this->CadBrindesEstoque = TableRegistry::get('CadBrindesEstoque', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CadBrindesEstoque);

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
