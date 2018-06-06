<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\GotasTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\GotasTable Test Case
 */
class GotasTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\GotasTable
     */
    public $Gotas;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.gotas',
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
        $config = TableRegistry::exists('Gotas') ? [] : ['className' => GotasTable::class];
        $this->Gotas = TableRegistry::get('Gotas', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Gotas);

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
