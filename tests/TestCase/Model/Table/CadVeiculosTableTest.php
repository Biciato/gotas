<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CadVeiculosTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CadVeiculosTable Test Case
 */
class CadVeiculosTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\CadVeiculosTable
     */
    public $CadVeiculos;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.cad_veiculos'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('CadVeiculos') ? [] : ['className' => CadVeiculosTable::class];
        $this->CadVeiculos = TableRegistry::get('CadVeiculos', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CadVeiculos);

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
