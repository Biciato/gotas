<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TopBrindesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TopBrindesTable Test Case
 */
class TopBrindesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\TopBrindesTable
     */
    public $TopBrindes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.top_brindes',
        'app.redes',
        'app.clientes',
        'app.brindes',
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
        $config = TableRegistry::exists('TopBrindes') ? [] : ['className' => TopBrindesTable::class];
        $this->TopBrindes = TableRegistry::get('TopBrindes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->TopBrindes);

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
