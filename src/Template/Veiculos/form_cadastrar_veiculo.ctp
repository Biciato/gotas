<?php 

use Cake\Core\Configure;
?>

<?= $this->Form->create($veiculo) ?>
    <fieldset>
        <legend><?= __('Adicionar Veículo') ?></legend>
            <?= $this->Form->control('placa', ['id' => 'placa']); ?>

            <?= $this->Html->tag('div', '', ['class' => 'text-danger validation-message'])?>
            <?= $this->Form->control('modelo', ['id' => 'modelo', 'class' => 'frozen-input-data']); ?>
            <?= $this->Form->control('fabricante', ['id' => 'fabricante', 'class' => 'frozen-input-data']); ?>
            <?= $this->Form->control('ano', ['id' => 'ano', 'class' => 'frozen-input-data']); ?>
    </fieldset>
    <?= $this->Form->button(__('{0} Salvar',
        $this->Html->tag('i', '', ['class' => 'fa fa-save'])),
        [
            'class' => 'btn btn-primary',
            'escape' => false
        ]
    
    ) ?>
    <?= $this->Form->end() ?>

<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/veiculos/form_cadastrar_veiculo') ?>
<?php else: ?> 
    <?= $this->Html->script('scripts/veiculos/form_cadastrar_veiculo.min') ?>
<?php endif; ?>

<?= $this->fetch('script') ?>