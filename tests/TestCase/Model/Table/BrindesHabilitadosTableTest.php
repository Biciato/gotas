<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\BrindesHabilitadosTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\BrindesHabilitadosTable Test Case
 */
class BrindesHabilitadosTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\BrindesHabilitadosTable
     */
    public $BrindesHabilitados;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
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
        $config = TableRegistry::exists('BrindesHabilitados') ? [] : ['className' => BrindesHabilitadosTable::class];
        $this->BrindesHabilitados = TableRegistry::get('BrindesHabilitados', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->BrindesHabilitados);

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
