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

<div class="veiculos">
	<legend>Dados de veículo</legend>

	<?= $this->Form->hidden($veiculoPath.'id', ['id' => 'id'])?>
    <div class="form-group row">
        <span class="col-lg-12">Informe a placa do veículo, se já existir, iremos trazer os dados previamente cadastrados </span>

        <span class="text-danger validation-message col-lg-12" id="placa_validation"></span>
        <div class="col-lg-3">
            <label for="placa">Placa</label>
            <input type="text"
                name="<?= sprintf("%s%s", $veiculoPath, "placa")?>"
                id="<?= sprintf("%s%s", $veiculoPath, "placa")?>"
                class="form-control placa"
                placeholder="Placa..."
                value="<?= $veiculo['placa']?>"/>
        </div>
        <div class="col-lg-3">
            <label for="modelo">Modelo</label>
            <input type="text"
                name="<?= sprintf("%s%s", $veiculoPath, "modelo")?>"
                id="<?= sprintf("%s%s", $veiculoPath, "modelo")?>"
                class="form-control modelo"
                placeholder="Modelo..."
                value="<?= $veiculo['modelo']?>"/>
        </div>
        <div class="col-lg-3">
            <label for="fabricante">Fabricante</label>
            <input type="text"
                name="<?= sprintf("%s%s", $veiculoPath, "fabricante")?>"
                id="<?= sprintf("%s%s", $veiculoPath, "fabricante")?>"
                class="form-control fabricante"
                placeholder="Fabricante..."
                value="<?= $veiculo['fabricante']?>"/>

        </div>
        <div class="col-lg-3">
            <label for="ano">Ano</label>
            <input type="text"
                name="<?= sprintf("%s%s", $veiculoPath, "ano")?>"
                id="<?= sprintf("%s%s", $veiculoPath, "ano")?>"
                class="form-control ano"
                placeholder="Ano..."
                value="<?= $veiculo['ano']?>"/>
        </div>
    </div>
</div>

<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/veiculos/general') ?>
<?php else: ?>
    <?= $this->Html->script('scripts/veiculos/general.min') ?>
<?php endif; ?>



<?= $this->fetch('script') ?>
