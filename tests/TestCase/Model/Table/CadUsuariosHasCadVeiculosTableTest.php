<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CadUsuariosHasCadVeiculosTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CadUsuariosHasCadVeiculosTable Test Case
 */
class CadUsuariosHasCadVeiculosTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\CadUsuariosHasCadVeiculosTable
     */
    public $CadUsuariosHasCadVeiculos;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.cad_usuarios_has_cad_veiculos',
        'app.cad_usuarios',
        'app.cad_veiculos'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('CadUsuariosHasCadVeiculos') ? [] : ['className' => CadUsuariosHasCadVeiculosTable::class];
        $this->CadUsuariosHasCadVeiculos = TableRegistry::get('CadUsuariosHasCadVeiculos', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CadUsuariosHasCadVeiculos);

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
