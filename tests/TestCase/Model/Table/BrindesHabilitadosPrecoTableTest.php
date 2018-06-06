<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\BrindesHabilitadosPrecoTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\BrindesHabilitadosPrecoTable Test Case
 */
class BrindesHabilitadosPrecoTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\BrindesHabilitadosPrecoTable
     */
    public $BrindesHabilitadosPreco;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.brindes_habilitados_preco',
        'app.brindes_habilitados',
        'app.brindes',
        'app.clientes'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('BrindesHabilitadosPreco') ? [] : ['className' => BrindesHabilitadosPrecoTable::class];
        $this->BrindesHabilitadosPreco = TableRegistry::get('BrindesHabilitadosPreco', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->BrindesHabilitadosPreco);

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
