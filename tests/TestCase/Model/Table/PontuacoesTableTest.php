<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PontuacoesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PontuacoesTable Test Case
 */
class PontuacoesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\PontuacoesTable
     */
    public $Pontuacoes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.pontuacoes',
        'app.usuarios',
        'app.brindes_habilitados',
        'app.brindes',
        'app.clientes',
        'app.matrizs',
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
        $config = TableRegistry::exists('Pontuacoes') ? [] : ['className' => PontuacoesTable::class];
        $this->Pontuacoes = TableRegistry::get('Pontuacoes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Pontuacoes);

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
