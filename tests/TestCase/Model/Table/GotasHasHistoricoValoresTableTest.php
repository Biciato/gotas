<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\GotasHasHistoricoValoresTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\GotasHasHistoricoValoresTable Test Case
 */
class GotasHasHistoricoValoresTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\GotasHasHistoricoValoresTable
     */
    public $GotasHasHistoricoValores;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.gotas_has_historico_valores',
        'app.clientes',
        'app.gotas'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('GotasHasHistoricoValores') ? [] : ['className' => GotasHasHistoricoValoresTable::class];
        $this->GotasHasHistoricoValores = TableRegistry::get('GotasHasHistoricoValores', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->GotasHasHistoricoValores);

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
