<?php
namespace App\Test\TestCase\Form;

use App\Form\UsuariosForm;
use Cake\TestSuite\TestCase;

/**
 * App\Form\UsuariosForm Test Case
 */
class UsuariosFormTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Form\UsuariosForm
     */
    public $Usuarios;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Usuarios = new UsuariosForm();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Usuarios);

        parent::tearDown();
    }

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testInitialization()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
