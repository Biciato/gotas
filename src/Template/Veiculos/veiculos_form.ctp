<?php
/**
 * @description Lista os veículos de um usuário
 * @author 	    Gustavo Souza Gonçalves
 * @file 	    Template\Veiculos\meus_veiculos.php
 * @date 	    25/07/2017
 *
 */

use Cake\Core\Configure; 
?>
<?php

if (!isset($veiculoPath))
{
 	$veiculoPath = '';
}
?>
<div class="veiculos col-lg-12">
	<legend>Dados de veículo</legend>
	
	<?= $this->Form->hidden($veiculoPath.'id', ['id' => 'id'])?>
	<span class="col-lg-12">Informe a placa do veículo, se já existir, iremos trazer os dados previamente cadastrados </span>
	
	<span class="text-danger validation-message col-lg-12" id="placa_validation"></span>
	<div class="col-lg-3">
		<?= $this->Form->control($veiculoPath.'placa', ['id' => 'placa', 'label' => 'Placa']) ?>
	</div>
	<div class="col-lg-3">
		<?= $this->Form->control($veiculoPath.'modelo', ['id' => 'modelo', 'label' => 'Modelo']) ?>
	</div>
	<div class="col-lg-3">
		
		<?= $this->Form->control($veiculoPath.'fabricante', ['id' => 'fabricante', 'label' => 'Fabricante']) ?>
	</div>
	<div class="col-lg-3">
		<?= $this->Form->input($veiculoPath.'ano', ['id' => 'ano', 'label' => 'Ano']) ?>
	</div>
</div>

<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/veiculos/general') ?>
<?php else: ?> 
    <?= $this->Html->script('scripts/veiculos/general.min') ?>
<?php endif; ?>



<?= $this->fetch('script') ?>