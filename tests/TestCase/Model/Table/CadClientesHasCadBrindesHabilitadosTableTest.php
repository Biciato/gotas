<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CadClientesHasCadBrindesHabilitadosTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CadClientesHasCadBrindesHabilitadosTable Test Case
 */
class CadClientesHasCadBrindesHabilitadosTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\CadClientesHasCadBrindesHabilitadosTable
     */
    public $CadClientesHasCadBrindesHabilitados;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.cad_clientes_has_cad_brindes_habilitados',
        'app.cad_clientes',
        'app.matrizs',
        'app.cad_brindes_habilitados',
        'app.cad_brindes'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('CadClientesHasCadBrindesHabilitados') ? [] : ['className' => CadClientesHasCadBrindesHabilitadosTable::class];
        $this->CadClientesHasCadBrindesHabilitados = TableRegistry::get('CadClientesHasCadBrindesHabilitados', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CadClientesHasCadBrindesHabilitados);

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
