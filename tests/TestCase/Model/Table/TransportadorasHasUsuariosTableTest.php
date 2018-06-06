<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TransportadorasHasUsuariosTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TransportadorasHasUsuariosTable Test Case
 */
class TransportadorasHasUsuariosTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\TransportadorasHasUsuariosTable
     */
    public $TransportadorasHasUsuarios;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.transportadoras_has_usuarios',
        'app.transportadoras',
        'app.usuarios'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('TransportadorasHasUsuarios') ? [] : ['className' => TransportadorasHasUsuariosTable::class];
        $this->TransportadorasHasUsuarios = TableRegistry::get('TransportadorasHasUsuarios', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->TransportadorasHasUsuarios);

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
