<?php
/**
  * @var \App\View\AppView $this
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $veiculo->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $veiculo->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Veiculos'), ['action' => 'index']) ?></li>
    </ul>
</nav>
<div class="veiculos form large-9 medium-8 columns content">
    <?= $this->Form->create($veiculo) ?>
    <fieldset>
        <legend><?= __('Edit Veiculo') ?></legend>
        <?php
            echo $this->Form->control('placa');
            echo $this->Form->control('modelo');
            echo $this->Form->control('fabricante');
            echo $this->Form->control('ano');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Salvar')) ?>
    <?= $this->Form->end() ?>
</div>
