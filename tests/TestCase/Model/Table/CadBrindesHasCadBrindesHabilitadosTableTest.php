<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CadBrindesHasCadBrindesHabilitadosTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CadBrindesHasCadBrindesHabilitadosTable Test Case
 */
class CadBrindesHasCadBrindesHabilitadosTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\CadBrindesHasCadBrindesHabilitadosTable
     */
    public $CadBrindesHasCadBrindesHabilitados;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.cad_brindes_has_cad_brindes_habilitados',
        'app.cad_brindes',
        'app.cad_clientes',
        'app.cad_brindes_habilitados'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('CadBrindesHasCadBrindesHabilitados') ? [] : ['className' => CadBrindesHasCadBrindesHabilitadosTable::class];
        $this->CadBrindesHasCadBrindesHabilitados = TableRegistry::get('CadBrindesHasCadBrindesHabilitados', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CadBrindesHasCadBrindesHabilitados);

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
