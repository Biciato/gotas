<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CadTransportadorasTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CadTransportadorasTable Test Case
 */
class CadTransportadorasTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\CadTransportadorasTable
     */
    public $CadTransportadoras;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.cad_transportadoras'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('CadTransportadoras') ? [] : ['className' => CadTransportadorasTable::class];
        $this->CadTransportadoras = TableRegistry::get('CadTransportadoras', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CadTransportadoras);

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
