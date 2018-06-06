<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ClientesHasBrindesHabilitadosTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ClientesHasBrindesHabilitadosTable Test Case
 */
class ClientesHasBrindesHabilitadosTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\ClientesHasBrindesHabilitadosTable
     */
    public $ClientesHasBrindesHabilitados;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.clientes_has_brindes_habilitados',
        'app.clientes',
        'app.matrizs',
        'app.brindes_habilitados',
        'app.brindes'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('ClientesHasBrindesHabilitados') ? [] : ['className' => ClientesHasBrindesHabilitadosTable::class];
        $this->ClientesHasBrindesHabilitados = TableRegistry::get('ClientesHasBrindesHabilitados', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ClientesHasBrindesHabilitados);

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
