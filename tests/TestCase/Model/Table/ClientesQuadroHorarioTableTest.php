<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ClientesHasQuadroHorarioTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ClientesHasQuadroHorarioTable Test Case
 */
class ClientesHasQuadroHorarioTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\ClientesHasQuadroHorarioTable
     */
    public $ClientesHasQuadroHorario;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.clientes_has_quadro_horario',
        'app.clientes'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('ClientesHasQuadroHorario') ? [] : ['className' => ClientesHasQuadroHorarioTable::class];
        $this->ClientesHasQuadroHorario = TableRegistry::get('ClientesHasQuadroHorario', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ClientesQuadroHorario);

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
