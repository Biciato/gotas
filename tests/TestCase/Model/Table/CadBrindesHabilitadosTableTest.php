<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CadBrindesHabilitadosTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CadBrindesHabilitadosTable Test Case
 */
class CadBrindesHabilitadosTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\CadBrindesHabilitadosTable
     */
    public $CadBrindesHabilitados;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.cad_brindes_habilitados',
        'app.cad_brindes',
        'app.cad_clientes'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('CadBrindesHabilitados') ? [] : ['className' => CadBrindesHabilitadosTable::class];
        $this->CadBrindesHabilitados = TableRegistry::get('CadBrindesHabilitados', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CadBrindesHabilitados);

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
