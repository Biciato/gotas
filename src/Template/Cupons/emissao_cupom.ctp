<?php

/**
  * @author   Gustavo Souza Gonçalves
  * @file     src/Template/Cupons/emissao_cupom.ctp
  * @date     13/06/2018
  */

?>

<?= $this->element('../Cupons/left_menu', ['mode' => 'escolher_brinde', 'controller' => 'cupons', 'action' => 'print_gift']) ?>

<div class="col-lg-9 col-md-10 columns">
	<legend><?= __("Emissão de Cupom")?></legend>

	<?= $this->Form->create(); ?>
	<?= $this->element('../Usuarios/filtro_usuarios_ajax')?>

	<?= $this->Form->text('clientes_id', ['id' => 'clientes_id', 'value' => $cliente->id, 'style' => 'display: none;']); ?>
	<?= $this->element('../Brindes/brindes_filtro_ajax')?>

	<?= $this->Form->button(__('{0} Imprimir', $this->Html->tag('i', '', ['class' => 'fa fa-print'])), ['escape' => false]) ?>
	<?= $this->Form->end(); ?>

</div>



