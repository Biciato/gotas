<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\RedesCpfListaNegraTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\RedesCpfListaNegraTable Test Case
 */
class RedesCpfListaNegraTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\RedesCpfListaNegraTable
     */
    public $RedesCpfListaNegra;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.redes_cpf_lista_negra',
        'app.redes',
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
        $config = TableRegistry::exists('RedesCpfListaNegra') ? [] : ['className' => RedesCpfListaNegraTable::class];
        $this->RedesCpfListaNegra = TableRegistry::get('RedesCpfListaNegra', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->RedesCpfListaNegra);

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
