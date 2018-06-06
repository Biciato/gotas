<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CadTransportadorasHasCadUsuariosTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CadTransportadorasHasCadUsuariosTable Test Case
 */
class CadTransportadorasHasCadUsuariosTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\CadTransportadorasHasCadUsuariosTable
     */
    public $CadTransportadorasHasCadUsuarios;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.cad_transportadoras_has_cad_usuarios',
        'app.cad_transportadoras',
        'app.cad_usuarios'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('CadTransportadorasHasCadUsuarios') ? [] : ['className' => CadTransportadorasHasCadUsuariosTable::class];
        $this->CadTransportadorasHasCadUsuarios = TableRegistry::get('CadTransportadorasHasCadUsuarios', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CadTransportadorasHasCadUsuarios);

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
