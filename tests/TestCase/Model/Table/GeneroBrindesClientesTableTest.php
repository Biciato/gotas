<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TiposBrindesClientesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TiposBrindesClientesTable Test Case
 */
class TiposBrindesClientesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\TiposBrindesClientesTable
     */
    public $TiposBrindesClientes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.genero_brindes_clientes',
        'app.genero_brindes',
        'app.clientes',
        'app.redes_has_clientes',
        'app.redes',
        'app.redes_has_clientes_administradores',
        'app.usuarios',
        'app.clientes_has_usuarios',
        'app.cliente',
        'app.redes_has_cliente',
        'app.gotas',
        'app.clientes_has_brindes_habilitados',
        'app.brindes',
        'app.brindes_nao_habilitados',
        'app.brinde_habilitado_preco_atual',
        'app.brindes_habilitados_ultimos_precos',
        'app.brindes_estoque_atual',
        'app.pontuacoes',
        'app.brindes_habilitados',
        'app.pontuacoes_comprovantes',
        'app.funcionarios',
        'app.usuarios_has_veiculos',
        'app.veiculos',
        'app.transportadoras_has_usuarios',
        'app.transportadoras',
        'app.soma_pontuacoes',
        'app.pontuacoes_aprovadas',
        'app.descritivo_pontuacoes',
        'app.usuario'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('TiposBrindesClientes') ? [] : ['className' => TiposBrindesClientesTable::class];
        $this->TiposBrindesClientes = TableRegistry::get('TiposBrindesClientes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->TiposBrindesClientes);

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
