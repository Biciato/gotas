<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CadPontuacoesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CadPontuacoesTable Test Case
 */
class CadPontuacoesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\CadPontuacoesTable
     */
    public $CadPontuacoes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.cad_pontuacoes',
        'app.cad_usuarios',
        'app.cad_brindes_habilitados',
        'app.cad_brindes',
        'app.cad_clientes',
        'app.matrizs',
        'app.cad_gotas'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('CadPontuacoes') ? [] : ['className' => CadPontuacoesTable::class];
        $this->CadPontuacoes = TableRegistry::get('CadPontuacoes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CadPontuacoes);

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
