<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\BrindesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\BrindesTable Test Case
 */
class BrindesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\BrindesTable
     */
    public $Brindes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.brindes',
        'app.clientes',
        'app.matrizs'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Brindes') ? [] : ['className' => BrindesTable::class];
        $this->Brindes = TableRegistry::get('Brindes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Brindes);

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
