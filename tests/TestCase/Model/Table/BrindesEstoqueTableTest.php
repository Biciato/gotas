<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\BrindesEstoqueTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\BrindesEstoqueTable Test Case
 */
class BrindesEstoqueTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\BrindesEstoqueTable
     */
    public $BrindesEstoque;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.brindes_estoque',
        'app.brindes',
        'app.clientes',
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
        $config = TableRegistry::exists('BrindesEstoque') ? [] : ['className' => BrindesEstoqueTable::class];
        $this->BrindesEstoque = TableRegistry::get('BrindesEstoque', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->BrindesEstoque);

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
