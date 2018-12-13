<?php

use Cake\Core\Configure;

$title = isset($title) ? $title : __('Adicionar Veículo');
?>

<?= $this->Form->create($veiculo) ?>
    <fieldset>
        <legend><?= $title ?></legend>
            <?= $this->Form->control('placa', ['id' => 'placa']); ?>

            <?= $this->Html->tag('div', '', ['class' => 'text-danger validation-message']) ?>
            <?= $this->Form->control('modelo', ['id' => 'modelo', 'class' => 'frozen-input-data']); ?>
            <?= $this->Form->control('fabricante', ['id' => 'fabricante', 'class' => 'frozen-input-data']); ?>
            <?= $this->Form->control('ano', ['id' => 'ano', 'class' => 'frozen-input-data']); ?>
    </fieldset>

    <div class="col-lg-12 text-right">
        <button type="submit" 
            class="btn btn-primary save-button botao-confirmar">
            <i class="fa fa-save"></i>
            Salvar
        </button>
        
        <a href="/" 
            class="btn btn-danger botao-cancelar">
            <i class="fa fa-window-close"></i>
            Cancelar
        </a>
    </div>
    <?= $this->Form->end() ?>

<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/veiculos/form_cadastrar_veiculo') ?>
<?php else : ?>
    <?= $this->Html->script('scripts/veiculos/form_cadastrar_veiculo.min') ?>
<?php endif; ?>

<?= $this->fetch('script') ?>
