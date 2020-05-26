<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\BrindesHasBrindesHabilitadosTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\BrindesHasBrindesHabilitadosTable Test Case
 */
class BrindesHasBrindesHabilitadosTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\BrindesHasBrindesHabilitadosTable
     */
    public $BrindesHasBrindesHabilitados;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.brindes_has_brindes_habilitados',
        'app.brindes',
        'app.clientes',
        'app.brindes_habilitados'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('BrindesHasBrindesHabilitados') ? [] : ['className' => BrindesHasBrindesHabilitadosTable::class];
        $this->BrindesHasBrindesHabilitados = TableRegistry::get('BrindesHasBrindesHabilitados', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->BrindesHasBrindesHabilitados);

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
