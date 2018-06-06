<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Cupons/escolher_brinde.ctp
 * @date     18/08/2017
 */

use Cake\Core\Configure;

use Cake\Routing\Router;
?>

<?= $this->element('../Pages/left_menu', ['item_selected' => 'resgate_cupons']) ?>

<?= $this->element('../Cupons/resgate_cupom_form') ?>

<?= $this->element('../Cupons/resgate_cupom_canhoto_confirmacao') ?>

<?= $this->element('../Cupons/resgate_cupom_canhoto_impressao') ?>

<div class="hidden">
	<?= $this->element('../Cupons/impressao_cupom_layout') ?>
</div>
