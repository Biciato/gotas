<?php

/**
 * @var \App\View\AppView $this
 */

/**
 * adicionar_genero_brindes_cliente.ctp
 *
 *
 * View para genero_brindes_clientes/generos_brindes_cliente
 *
 * Variáveis:
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\GeneroBrindesCliente
 *
 * @category View
 * @package App\Template\GeneroBrindesClientes
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @date      03/06/2018
 * @copyright 2018 Gustavo Souza Gonçalves
 * @license   http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version   1.0
 * @link      http://pear.php.net/package/PackageName
 * @since     File available since Release 1.0.0
 *
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$title = __("Adicionar Gênero de Brinde");

// Barra de navegação
$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);
$this->Breadcrumbs->add('Redes', ['controller' => 'Redes', 'action' => 'index']);
$this->Breadcrumbs->add('Detalhes da Rede', ['controller' => 'Redes', 'action' => 'ver_detalhes', $cliente->rede_has_cliente->redes_id]);
$this->Breadcrumbs->add('Detalhes da Unidade', ['controller' => 'clientes', 'action' => 'ver_detalhes', $cliente->id]);
$this->Breadcrumbs->add('Gênero de Brindes Habilitados', ['controller' => 'genero_brindes_clientes', "action" => "generos_brindes_cliente", $cliente->id]);
$this->Breadcrumbs->add($title, [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

// Menu Esquerdo

echo $this->element("../GeneroBrindesClientes/left_menu");


?>
<div class="generoBrindesClientes form col-lg-9 col-md-8 columns content">
    <?= $this->Form->create($generoBrindesCliente) ?>
    <fieldset>
        <legend><?= __($title) ?></legend>
        <?= $this->element("../GeneroBrindesClientes/form_genero_brindes_cliente") ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
