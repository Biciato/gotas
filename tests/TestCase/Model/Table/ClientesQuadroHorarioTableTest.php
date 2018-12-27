<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ClientesQuadroHorarioTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ClientesQuadroHorarioTable Test Case
 */
class ClientesQuadroHorarioTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\ClientesQuadroHorarioTable
     */
    public $ClientesQuadroHorario;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.clientes_quadro_horario',
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
        $config = TableRegistry::exists('ClientesQuadroHorario') ? [] : ['className' => ClientesQuadroHorarioTable::class];
        $this->ClientesQuadroHorario = TableRegistry::get('ClientesQuadroHorario', $config);
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
