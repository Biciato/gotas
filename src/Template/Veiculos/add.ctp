<?php
/**
  * @var \App\View\AppView $this
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Veiculos'), ['action' => 'index']) ?></li>
    </ul>
</nav>
<div class="veiculos form large-9 medium-8 columns content">
    <?= $this->Form->create($veiculo) ?>
    <fieldset>
        <legend><?= __('Add Veiculo') ?></legend>
        <?php
            echo $this->Form->control('placa');
            echo $this->Form->control('modelo');
            echo $this->Form->control('fabricante');
            echo $this->Form->control('ano');
        ?>
    </fieldset>
    <?= $this->Form->button(
            __('{0} Salvar', $this->Html->tag('i', '', ['class' => 'fa fa-save'])),
            [
                'type' => 'button', 
                'escape' => false,
            ]
        ) ?>
    <?= $this->Form->end() ?>
</div>
