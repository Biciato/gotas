<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\GeneroBrindesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\GeneroBrindesTable Test Case
 */
class GeneroBrindesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\GeneroBrindesTable
     */
    public $GeneroBrindes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.genero_brindes',
        'app.clientes',
        'app.redes_has_clientes',
        'app.redes',
        'app.redes_has_clientes_administradores',
        'app.usuarios',
        'app.clientes_has_usuarios',
        'app.cliente',
        'app.rede_has_cliente',
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
        'app.usuario',
        'app.genero_brindes_clientes'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('GeneroBrindes') ? [] : ['className' => GeneroBrindesTable::class];
        $this->GeneroBrindes = TableRegistry::get('GeneroBrindes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->GeneroBrindes);

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
