<?php

/**
 * @var \App\View\AppView $this
 */

/**
 * adicionar_tipos_brindes_cliente.ctp
 *
 *
 * View para tipos_brindes_clientes/tipos_brindes_cliente
 *
 * Variáveis:
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TiposBrindesCliente
 *
 * @category  View
 * @package   App\Template\TiposBrindesClientes
 * @author    Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since     03/06/2018
 * @copyright 2018 Gustavo Souza Gonçalves
 * @license   http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version   1.0
 * @link      http://pear.php.net/package/PackageName
 * @since     File available since Release 1.0.0
 *
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$title = __("Adicionar Tipo de Brinde");

// Barra de navegação
$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);
$this->Breadcrumbs->add('Redes', ['controller' => 'Redes', 'action' => 'index']);
$this->Breadcrumbs->add('Detalhes da Rede', ['controller' => 'Redes', 'action' => 'ver_detalhes', $cliente->rede_has_cliente->redes_id]);
$this->Breadcrumbs->add('Detalhes da Unidade', ['controller' => 'clientes', 'action' => 'ver_detalhes', $cliente->id]);
$this->Breadcrumbs->add('Tipos de Brindes Habilitados', ['controller' => 'tipos_brindes_clientes', "action" => "tipos_brindes_cliente", $cliente->id]);
$this->Breadcrumbs->add($title, [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

// Menu Esquerdo

echo $this->element("../TiposBrindesClientes/left_menu");


?>
<div class="tiposBrindesClientes form col-lg-9 col-md-8 columns content">
    <?= $this->Form->create($tiposBrindesCliente) ?>
    <fieldset>
        <legend><?= __($title) ?></legend>
        <?= $this->element("../TiposBrindesClientes/form_tipos_brindes_cliente", array("selectTiposBrindesEnabled" => true)) ?>
    </fieldset>
    <?= $this->Form->end() ?>
</div>
