<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CadBrindesHabilitadosPrecoTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CadBrindesHabilitadosPrecoTable Test Case
 */
class CadBrindesHabilitadosPrecoTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\CadBrindesHabilitadosPrecoTable
     */
    public $CadBrindesHabilitadosPreco;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.cad_brindes_habilitados_preco',
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
        $config = TableRegistry::exists('CadBrindesHabilitadosPreco') ? [] : ['className' => CadBrindesHabilitadosPrecoTable::class];
        $this->CadBrindesHabilitadosPreco = TableRegistry::get('CadBrindesHabilitadosPreco', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CadBrindesHabilitadosPreco);

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
