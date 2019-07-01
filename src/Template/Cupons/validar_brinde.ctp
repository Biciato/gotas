<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Cupons/validarBrinde.ctp
 * @date     18/08/2017
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

// Navegação
$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);
$this->Breadcrumbs->add('Resgate de Cupons', [], ['class' => 'active']);
echo $this->Breadcrumbs->render(['class' => 'breadcrumb']);


?>

<?= $this->element('../Pages/left_menu', ['item_selected' => 'validarBrinde']) ?>

<?= $this->element('../Cupons/validar_brinde_form') ?>

<?= $this->element('../Cupons/validar_brinde_canhoto_confirmacao') ?>

<?= $this->element('../Cupons/validar_brinde_canhoto_impressao') ?>

<div class="hidden">
	<?= $this->element('../Cupons/impressao_cupom_layout') ?>
</div>
