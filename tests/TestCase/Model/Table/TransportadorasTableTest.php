<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TransportadorasTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TransportadorasTable Test Case
 */
class TransportadorasTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\TransportadorasTable
     */
    public $Transportadoras;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.transportadoras'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Transportadoras') ? [] : ['className' => TransportadorasTable::class];
        $this->Transportadoras = TableRegistry::get('Transportadoras', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Transportadoras);

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
}
