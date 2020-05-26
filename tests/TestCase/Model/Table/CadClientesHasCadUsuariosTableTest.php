<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CadClientesHasCadUsuariosTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CadClientesHasCadUsuariosTable Test Case
 */
class CadClientesHasCadUsuariosTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\CadClientesHasCadUsuariosTable
     */
    public $CadClientesHasCadUsuarios;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.cad_clientes_has_cad_usuarios',
        'app.cad_clientes',
        'app.matrizs',
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
        $config = TableRegistry::exists('CadClientesHasCadUsuarios') ? [] : ['className' => CadClientesHasCadUsuariosTable::class];
        $this->CadClientesHasCadUsuarios = TableRegistry::get('CadClientesHasCadUsuarios', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CadClientesHasCadUsuarios);

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
