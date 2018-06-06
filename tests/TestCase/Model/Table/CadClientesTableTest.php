<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CadClientesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CadClientesTable Test Case
 */
class CadClientesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\CadClientesTable
     */
    public $CadClientes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.cad_clientes',
        'app.matrizs'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('CadClientes') ? [] : ['className' => CadClientesTable::class];
        $this->CadClientes = TableRegistry::get('CadClientes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CadClientes);

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
