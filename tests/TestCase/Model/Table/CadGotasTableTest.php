<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CadGotasTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CadGotasTable Test Case
 */
class CadGotasTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\CadGotasTable
     */
    public $CadGotas;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.cad_gotas',
        'app.cad_clientes',
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
        $config = TableRegistry::exists('CadGotas') ? [] : ['className' => CadGotasTable::class];
        $this->CadGotas = TableRegistry::get('CadGotas', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CadGotas);

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
