<?php
namespace App\Test\TestCase\Controller;

use App\Controller\PontuacoesPendentesController;
use Cake\TestSuite\IntegrationTestCase;

/**
 * App\Controller\PontuacoesPendentesController Test Case
 */
class PontuacoesPendentesControllerTest extends IntegrationTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.pontuacoes_pendentes',
        'app.clientes',
        'app.cliente_matriz',
        'app.clientes_has_usuarios',
        'app.usuarios',
        'app.usuarios_has_veiculos',
        'app.veiculos',
        'app.transportadoras_has_veiculos',
        'app.pontuacoes',
        'app.brindes_habilitados',
        'app.gotas',
        'app.clientes_has_brindes_habilitados',
        'app.brindes',
        'app.brindes_nao_habilitados',
        'app.brinde_habilitado_preco_atual',
        'app.brindes_habilitados_ultimos_precos',
        'app.brindes_estoque_atual',
        'app.funcionarios'
    ];

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test view method
     *
     * @return void
     */
    public function testView()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test add method
     *
     * @return void
     */
    public function testAdd()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test edit method
     *
     * @return void
     */
    public function testEdit()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
