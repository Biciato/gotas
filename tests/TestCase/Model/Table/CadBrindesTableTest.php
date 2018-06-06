<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CadBrindesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CadBrindesTable Test Case
 */
class CadBrindesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\CadBrindesTable
     */
    public $CadBrindes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
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
        $config = TableRegistry::exists('CadBrindes') ? [] : ['className' => CadBrindesTable::class];
        $this->CadBrindes = TableRegistry::get('CadBrindes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CadBrindes);

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
